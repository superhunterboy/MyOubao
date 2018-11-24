<?php

# 用户银行卡管理

class UserUserBankCardController extends UserBaseController {

    protected $resourceView = 'centerUser.userBankCard';
    protected $modelName = 'UserUserBankCard';

    const CARD_BINDING_STATUS_NAME = 'CardBindingStatus';
    const FIRST_BINDING_NAME = 'IsFirstBinding';
    const BIND_CARD_STEP0 = 0;
    const BIND_CARD_STEP1 = 1;
    const BIND_CARD_STEP2 = 2;
    const BIND_CARD_STEP3 = 3;
    const BIND_CARD_STEP4 = 4;

    // protected $views = ['validate' => 'validate', 'create' => 'create', 'edit' => 'edit', 'confirm' => 'confirm', 'result' => 'result', 'destroy' => 'destroy'];

    protected function beforeRender() {
        parent::beforeRender();
        $iUserId = Session::get('user_id');
        $iBindedCardsNum = UserUserBankCard::getUserBankCardsCount($iUserId);
        $this->setVars(compact('iBindedCardsNum'));
        switch ($this->action) {
            case 'index':
                $iLimitCardsNum = UserUserBankCard::BIND_CARD_NUM_LIMIT;
                $aStatus = BankCard::$validStatuses;
                $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
                $this->setVars(compact('iLimitCardsNum', 'aStatus', 'bLocked'));
                break;
            case 'create':
            case 'edit':
                $aBanks = [];
                $aBanks = Bank::getAllBank();
                $aSelectorData = $this->generateSelectorData();
                $this->setVars(compact('aSelectorData', 'aBanks'));
                $this->setVars('sSideNav', 'bank-cards.index');
                break;
            case 'validate':
                $this->setVars('sSideNav', 'bank-cards.index');
            case 'cardLock':
                $aBindedCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'bank']);
                $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
                $this->setVars(compact('aBindedCards', 'bLocked'));
                break;
            default:
                break;
        }
    }

    /**
     * [generateSelectorData 生成下拉框渲染数据]
     * @return [Array] [数组]
     */
    private function generateSelectorData() {
        $data = isset($this->viewVars['data']) ? $this->viewVars['data'] : null;
        $aHiddenColumns = [
            ['name' => 'province', 'value' => $data ? $data->province : ''],
            ['name' => 'city', 'value' => $data ? $data->city : '']
        ];
        $aSelectColumn = [
            ['name' => 'province_id', 'emptyDesc' => '请选择省份'],
            ['name' => 'city_id', 'emptyDesc' => '请选择城市']
        ];

        $aSelectorData = [
            'aHiddenColumns' => $aHiddenColumns,
            'aSelectColumn' => $aSelectColumn,
            'sSelectedFirst' => $data ? $data->province_id : '',
            'sSelectedSecond' => $data ? $data->city_id : '',
            'sDataFile' => 'districts',
        ];
        return $aSelectorData;
    }

    /**
     * [generateHiddenAccount 生成只显示末尾4位的银行卡账号信息]
     * @param  [String] $account [银行卡账号]
     * @return [String]          [只显示末尾4位的银行卡账号信息,且每4位空格隔开]
     */
    // private function generateHiddenAccount($account)
    // {
    //     $str = str_repeat('*', (strlen($account) - 4));
    //     $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($account, -4);
    //     return $account_hidden;
    // }

    public function index() {
        $iUserId = Session::get('user_id');
//        if (!$iUserCardCount = UserUserBankCard::getUserBankCardsCount($iUserId)) {
//            $this->saveUrlToSession();
//            $this->action = 'noCard';
//            return $this->render();
//        }
        $this->params['user_id'] = $iUserId;
        $this->params['status'] = [UserUserBankCard::STATUS_IN_USE, UserUserBankCard::STATUS_LOCKED];
        Session::forget(self::CARD_BINDING_STATUS_NAME);
        return parent::index();
    }

    /**
     * [customDestroy 自定义删除 ]
     * @param  [Int] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    public function customDestroy($iCardId) {
        $iUserId = Session::get('user_id');
        $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
        if ($bLocked) {
            return $this->goBack('error', __('_userbankcard.locked-bank-card'));
        }
        if (Request::method() == 'POST') {
            return $this->postDestroy();
        } else {
            $oUserBankCard = UserUserBankCard::find($iCardId);
            $sAccountHidden = $oUserBankCard->account_hidden;
            $this->setVars(compact('sAccountHidden', 'iCardId'));
            $this->view = $this->resourceView . '.destroy';
            return $this->render();
        }
    }

    /**
     * 删除用户绑定的银行卡
     * @param  int $iCardId  用户绑定的银行卡id
     */
    private function postDestroy() {
        $bValidated = $this->postValidate();
        if (!$bValidated) {
            return $this->goBack('error', __('_basic.validate-card-fail'));
        } else {
            $oUserBankCard = UserUserBankCard::find(array_get($this->params, 'id'));
            if (!is_object($oUserBankCard)) {
                return $this->goBack('error', __('_userbankcard.mising-data'));
            }
            $bSucc = $oUserBankCard->setDeleteStatus();
            if ($bSucc) {
                $oUserBankCard->flushUserBankCardCount();
                return $this->goBackToIndex('success', __('_userbankcard.delete-success'));
            } else {
                return $this->goBack('error', __('_userbankcard.delete-fail'));
            }
        }
    }

    /**
     * 用户锁定银行卡
     * @param  [Integer] $iStatus [当前的锁定状态]
     * @return [Response]          [description]
     */
    public function cardLock() {
        $iUserId = Session::get('user_id');
        if (!UserUserBankCard::getUserBankCardsCount($iUserId)) {
            return $this->goBack('error', __('_userbankcard.bind-card-first'));
        }
        $oUser = UserUser::find($iUserId);
        if (is_null($oUser->fund_password)) {
            return Redirect::route('users.safe-reset-fund-password');
        }
        if (Request::method() == 'POST') {
            return $this->_postCardLock($oUser);
        } else {
            $this->action = 'cardLock';
            return $this->render();
        }
    }

    /**
     * 用户锁定银行卡
     * @param  int       $iStatus 当前银行卡状态
     * @param  object $oUser   当前用户对象
     */
    private function _postCardLock($oUser) {
        $sFundPassword = trim(Input::get('fund_password'));
        $bValidated = $oUser->checkFundPassword($sFundPassword);
        if (!$bValidated) {
            return $this->goBack('error', __('_basic.validate-fund-password-fail'));
        }
        $bSucc = UserUserBankCard::setLockStatus($oUser->id);
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBackToIndex('success', __('_userbankcard.locked-bankcards-success'));
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', __('_userbankcard.locked-bankcards-fail'));
        }
    }

    /**
     * 修改银行卡信息
     * @param  int $iStep 修改卡步骤
     * @param  int $id    用户绑定银行卡id
     */
    public function modifyCard($iStep, $id = null) {
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-data'));
        }
        // 绑卡前先设置资金密码
        if ($oUser->fund_password == null) {
            $this->saveUrlToSession();
            Session::put('curPage-UserUser', route('users.safe-reset-fund-password'));
            return Redirect::route('users.safe-reset-fund-password', 1);
        }
        // 绑卡时，银行卡不能被锁定
        $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
        if ($bLocked) {
            return $this->goBack('error', __('_userbankcard.locked-bank-card'));
        }
        $oUserBankCard = UserUserBankCard::find($id);
        if ($oUserBankCard->status != UserUserBankCard::STATUS_IN_USE) {
            return Redirect::route('bank-cards.index')->with('error', __('_userbankcard.missing-data'));
        }
        switch ($iStep) {
            case 0:
                return $this->validate($id);
            case 1:
                return $this->generateCardInfo($id);
            case 2:
                return $this->validateCardInfo($id);
            case 3:
                return $this->confirm($id);
            case 4:
                return $this->result();
        }
    }

    /**
     * 用户绑定银行卡
     * @param  int $iStep 绑定步骤
     */
    public function bindCard($iStep) {
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-data'));
        }
        // 绑卡前先设置资金密码
        if ($oUser->fund_password == null) {
             $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        }
        // 绑卡时，银行卡不能被锁定
        $bLocked = UserUserBankCard::getUserCardsLockStatus($iUserId);
        if ($bLocked) {
            return $this->goBack('error', __('_userbankcard.locked-bank-card'));
        }
        // 最大允许绑定卡数量
        $iUserBankCardCount = UserUserBankCard::getUserBankCardsCount($iUserId);
        $this->isFirstCard = ($iUserBankCardCount == 0);
        if ($iStep < self::BIND_CARD_STEP4 && $iUserBankCardCount >= UserBankCard::BIND_CARD_NUM_LIMIT) {
            return $this->goBack('error', __('_userbankcard.user-bank-cards-count-full'));
        }
        switch ($iStep) {
            case 0:
                return $this->validate();
            case 1:
                return $this->generateCardInfo();
            case 2:
                return $this->validateCardInfo();
            case 3:
                return $this->confirm();
            case 4:
                return $this->result();
        }
    }

    /**
     * [validate 验证银行卡信息]
     * @param  [Integer] $iCardId [待验证的银行卡id]
     * @return [Response]         [description]
     */
    private function validate($iCardId = null) {

        if (Request::method() == 'POST') {
            $bValidated = $this->postValidate();
            if (!$bValidated) {
                return Redirect::back()->withInput()->with('error', __('_basic.validate-card-fail'));
            } else {
                Session::put(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0, 1);
                return Redirect::route('bank-cards.' . ($iCardId ? 'modify-card' : 'bind-card'), $iCardId ? [self::BIND_CARD_STEP1, $iCardId] : self::BIND_CARD_STEP1);
            }
        } else {
            $data = UserUserBankCard::find($iCardId);
            $this->setVars(compact('data', 'iCardId'));
            $this->action = 'validate';
            Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0);
            return $this->render();
        }
    }

    /**
     * [generateCardInfo 生成银行卡绑定信息]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function generateCardInfo($iCardId = 0) {
        //如果不是第一次绑卡，则需要验证卡信息
        if (!$iCardId && !$this->isFirstCard && !Session::get(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0)) {
            return Redirect::route('bank-cards.bind-card', self::BIND_CARD_STEP0);
        }
        // 如果是修改卡信息，则需要验证旧卡信息
        if ($iCardId && !Session::get(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0)) {
            return Redirect::route('bank-cards.modify-card', [self::BIND_CARD_STEP0, $iCardId]);
        }
        $this->action = $iCardId ? 'edit' : 'create';
        if ($iCardId) {
            $data = UserUserBankCard::find($iCardId);
            $this->setVars(compact('data', 'iCardId'));
        }
        return $this->render();
    }

    /**
     * 验证银行卡信息有效性
     */
    private function validateCardInfo($iCardId = 0) {
        $this->setVars(compact('iCardId'));

        $oUserBankCard = UserUserBankCard::getUserBankCardAccountNameNoStatus(array_get($this->params, 'account_name'));

        /*if (is_object($oUserBankCard) && Session::get('user_id') != $oUserBankCard->user_id) {
            Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
            $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
            return Redirect::route($sRedirectName, $iCardId ? [1, $iCardId] : 1)
                            ->with('error', '该开户姓名已存在，请联系客服，谢谢！');
        }*/
        $sAccount = str_replace(' ', '', $this->params['account']);
        $oUserBankCard2 = UserUserBankCard::getUserBankCardAccountNoStatus($sAccount);
        if (is_object($oUserBankCard2) && Session::get('user_id') != $oUserBankCard2->user_id) {
            Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
            $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
            return Redirect::route($sRedirectName, $iCardId ? [1, $iCardId] : 1)
                            ->with('error', '该银行卡已经存在，请联系客服，谢谢！');
        }
        $iUserId = Session::get('user_id');
        $iBindedCardsNum = UserUserBankCard::getUserBankCardsCount($iUserId);
        if ($iBindedCardsNum >= 1 && !is_object($oUserBankCard)) {
            Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
            $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
            return Redirect::route($sRedirectName, $iCardId ? [1, $iCardId] : 1)
                            ->with('error', '请使用与已绑定卡相同的开户姓名，谢谢！');
        }
        $aRules = [
            'bank_id' => 'required|integer|min:1',
            'bank' => 'required|max:50',
            'province_id' => 'integer|min:1',
            'province' => 'between:0,20',
            'city_id' => 'integer|min:1',
            'city' => 'between:0,20',
            'branch' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
            'account_name' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
//            'account' => 'required|regex:/^[0-9]*$/|between:16,19|confirmed',
            'account_confirmation' => 'required|regex:/^[0-9]*$/|between:16,19',
        ];
        $this->params['account'] = str_replace(' ', '', array_get($this->params, 'account'));
        $this->params['account_confirmation'] = str_replace(' ', '', array_get($this->params, 'account_confirmation'));

        $validator = Validator::make($this->params, $aRules, UserUserBankCard::$customMessages);
        if (!$validator->passes()) {
            $this->langVars['reason'] = '';
            foreach ($validator->messages()->all() as $message) {
                $this->langVars['reason'] .='<li>' . $message . '</li>';
            }
            Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
            $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
            return Redirect::route($sRedirectName, $iCardId ? [1, $iCardId] : 1)
                            ->with('error', __('_userbankcard.bind-card-fail', $this->langVars));
        }
        $this->action = 'confirm';
        Session::put(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2, 1);
        return $this->render();
    }

    private function confirm($iCardId = null) {
        $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
        // 不是第一次绑卡，需要验证旧卡
        if (!$iCardId && !$this->isFirstCard && !Session::get(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0)) {
            return Redirect::route($sRedirectName, $iCardId ? [self::BIND_CARD_STEP0, $iCardId] : self::BIND_CARD_STEP0);
        }
        //修改卡信息，需要验证旧卡
        if ($iCardId && !Session::get(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0)) {
            return Redirect::route($sRedirectName, $iCardId ? [self::BIND_CARD_STEP0, $iCardId] : self::BIND_CARD_STEP0);
        }
        // 填写的银行卡信息需要正确
        if (!Session::get(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2)) {
            return Redirect::route($sRedirectName, $iCardId ? [self::BIND_CARD_STEP1, $iCardId] : self::BIND_CARD_STEP1);
        }
        $this->setVars('aFormData', $this->params);
        $this->setVars('iCardId', $iCardId);
        if (Request::method() == 'POST') {
            $this->params['user_id'] = Session::get('user_id');
            $this->params['account'] = str_replace(' ', '', $this->params['account']);
            $this->params['account_confirmation'] = str_replace(' ', '', $this->params['account_confirmation']);
            if ($iCardId) {
                return $this->_modifyConfirm($iCardId);
            } else {
                return $this->_bindConfirm();
            }
        }
    }

    /**
     * 绑定银行卡确认
     * @return type
     */
    private function _bindConfirm() {
        //判断该银行卡是否已经绑定
        $oUserBankCard = UserUserBankCard::getUserBankCardAccount($this->params['account']);
        if (is_object($oUserBankCard)) {
            return Redirect::route('bank-cards.bind-card', self::BIND_CARD_STEP1)->with('error', __('_userbankcard.user-bank-card-exist'));
        }
        DB::connection()->beginTransaction();
        //查询银行卡是否存在，如果不存在先保存银行卡信息
        $oBankCard = BankCard::getObjectByParams(['account' => $this->params['account'], 'account_name' => $this->params['account_name']]);
        if (!is_object($oBankCard)) {
            $oBankCard = new BankCard;
            $oBankCard->status = BankCard::STATUS_IN_USE;
            $oBankCard->fill($this->params);
            $bSucc = $oBankCard->save();
        } else {
            $bSucc = true;
        }
        // 绑定银行卡到指定用户
        !$bSucc or $this->model->bank_card_id = $oBankCard->id;
        !$bSucc or $bSucc = $this->saveData();
        $oUser = User::find(Session::get('user_id'));
        !$bSucc or $bSucc = $oUser->save();
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0);
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP1);
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
        if ($bSucc) {
            DB::connection()->commit();
            return Redirect::route('bank-cards.bind-card', self::BIND_CARD_STEP4);
        } else {
            DB::connection()->rollback();
            $this->langVars['reason'] = & $this->model->getValidationErrorString();
            return Redirect::route('bank-cards.bind-card', self::BIND_CARD_STEP1)
                            ->with('error', __('_userbankcard.bind-card-fail', $this->langVars));
        }
    }

    private function _modifyConfirm($iCardId) {
        DB::connection()->beginTransaction();
        //查询银行卡是否存在，如果不存在先保存银行卡信息
        $oBankCard = BankCard::getObjectByParams(['account' => $this->params['account']]);
        if (!is_object($oBankCard)) {
            $oBankCard = new BankCard;
            $oBankCard->fill($this->params);
            $bSucc = $oBankCard->save();
        } else {
            $bSucc = true;
        }
        // 绑定银行卡到指定用户
        $this->model = $this->model->find($iCardId);
        if ($this->model->account != $this->params['account']) {
            $oUserBankCard = UserUserBankCard::getObjectByParams(['account' => $this->params['account'], 'status' => UserUserBankCard::STATUS_IN_USE]);
            if (is_object($oUserBankCard)) {
                return Redirect::route('bank-cards.modify-card', [self::BIND_CARD_STEP1, $iCardId])->with('error', __('_userbankcard.user-bank-card-exist'));
            }
            // 删除原来的绑定卡
            $this->model->status = UserUserBankCard::STATUS_DELETED;
            $bSucc = $this->model->save();
            //创建新的绑定卡
            !$bSucc or $this->model = new UserUserBankCard;
            !$bSucc or $this->model->bank_card_id = $oBankCard->id;
        }
        $this->_fillModelDataFromInput();
        !$bSucc or $bSucc = $this->model->save();
        $iCardId = $this->model->id;
        if ($bSucc) {
            DB::connection()->commit();
            return Redirect::route('bank-cards.modify-card', [self::BIND_CARD_STEP4, $iCardId]);
        } else {
            DB::connection()->rollback();
            $this->langVars['reason'] = & $this->model->getValidationErrorString();
            return Redirect::route('bank-cards.modify-card', [self::BIND_CARD_STEP1, $iCardId])
                            ->with('error', __('_userbankcard.bind-card-fail', $this->langVars));
        }
    }

    /**
     * [result 响应绑定银行卡结果页面]
     * @return [Response] [description]
     */
    private function result() {
        // pr(Request::method());exit;
        $this->action = 'result';
        $bSucceed = true;
        $this->setVars(compact('bSucceed'));
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP0);
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP1);
        Session::forget(self::CARD_BINDING_STATUS_NAME . self::BIND_CARD_STEP2);
        return $this->render();
    }

    /**
     * [postValidate 提交验证 ]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Boolean] [验证是否成功]
     */
    private function postValidate() {
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-data'));
        }
        $bValidated = $oUser->checkFundPassword(array_get($this->params, 'fund_password'));
        !$bValidated or $oUserBankCard = UserUserBankCard::find(array_get($this->params, 'id'));
        $sAccountName = array_get($this->params, 'account_name');
        $sAccount = array_get($this->params, 'account');
        $sAccount = str_replace(' ', '', $sAccount);
        !$bValidated or $bValidated = ($oUserBankCard->account_name == $sAccountName && $oUserBankCard->account == $sAccount);
        return $bValidated;
    }

    protected function & makeSearchConditions() {
        $aConditions = [];
        // pr($this->functionality->id);
        // pr($this->searchConfig);
        // pr($this->paramSettings);
//         pr($this->searchItems);
        // pr($this->params);
        // exit;

        $iCount = count($this->params);
        foreach ($this->paramSettings as $sColumn => $aParam) {
            if (!isset($this->params[$sColumn])) {
                if ($aParam['limit_when_null'] && $iCount <= 1) {
                    $aFieldInfo[1] = null;
                } else {
                    continue;
                }
            }

            $mValue = isset($this->params[$sColumn]) ? $this->params[$sColumn] : null;
            if (!is_array($mValue) && !mb_strlen($mValue) && !$aParam['limit_when_null'])
                continue;
            if (is_array($mValue) && empty($mValue[0]) && empty($mValue[1])) {
                continue;
            }
            // 处理between and 的情况
            if (!isset($this->searchItems[$sColumn])) {
                if (is_array($mValue) && !empty($mValue[0]) && !empty($mValue[1])) {
                    $aConditions[$sColumn] = [ 'between', $mValue];
                } else if (is_array($mValue) && !empty($mValue[0])) {
                    $aConditions[$sColumn] = [ '>=', $mValue[0]];
                } else if (is_array($mValue) && !empty($mValue[1])) {
                    $aConditions[$sColumn] = [ '<=', $mValue[1]];
                } else {
                    $aConditions[$sColumn] = [ '=', $mValue];
                }
                continue;
            }
            $aPattSearch = array('!\$model!', '!\$\$field!', '!\$field!');
            $aItemConfig = & $this->searchItems[$sColumn];
            $mValue = is_array($mValue) ? implode(',', $mValue) : $mValue;
            $aPattReplace = array($aItemConfig['model'], $mValue, $aItemConfig['field']);
            $sMatchRule = preg_replace($aPattSearch, $aPattReplace, $aItemConfig['match_rule']);
            $aMatchRule = explode("\n", $sMatchRule);
            if (count($aMatchRule) > 1) {        // OR
                // todo : or
            } else {
                $aFieldInfo = array_map('trim', explode(' = ', $aMatchRule[0]));
                $aTmp = explode(' ', $aFieldInfo[0]);
                $iOperator = (count($aTmp) > 1) ? $aTmp[1] : '=';
                if (!mb_strlen($mValue) && $aParam['limit_when_null']) {
                    $aFieldInfo[1] = null;
                }
                list($tmp, $sField) = explode('.', $aTmp[0]);
                $sField{0} == '$' or $sColumn = $sField;
                if (isset($aConditions[$sColumn])) {
                    // TODO 原来的方式from/to的值和search_items表中的记录的顺序强关联, 考虑修改为自动从小到大排序的[from, to]数组
                    $arr = [$aConditions[$sColumn][1], $aFieldInfo[1]];
                    sort($arr);
                    $aConditions[$sColumn] = [ 'between', $arr];
                } else {
                    if ($aFieldInfo[1] == "NULL") {
                        $aConditions[$sColumn] = [ $iOperator, null];
                    } else {
                        $aConditions[$sColumn] = [ $iOperator, $aFieldInfo[1]];
                    }
                }
            }
        }
        return $aConditions;
    }

}
