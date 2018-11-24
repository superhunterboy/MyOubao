<?php

# 用户银行卡管理

class BankCardController extends UserBaseController {

    protected $resourceView = 'centerUser.bankCard';
    protected $modelName = 'UserUserBankCard';

    const CARD_NUM_LIMIT = 4;
    const CARD_BINDING_STATUS_NAME = 'CardBindingStatus';
    const FIRST_BINDING_NAME = 'IsFirstBinding';

    // protected $views = ['validate' => 'validate', 'create' => 'create', 'edit' => 'edit', 'confirm' => 'confirm', 'result' => 'result', 'destroy' => 'destroy'];

    protected function beforeRender() {
        parent::beforeRender();
        // $oUserBankCard = new UserUserBankCard;

        $iUserId = Session::get('user_id');
        $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
        $this->setVars(compact('iBindedCardsNum'));
        // pr($datas[0]->toArray());exit;
        // pr($this->action);exit;
        switch ($this->action) {
            case 'index':
                $iLimitCardsNum = self::CARD_NUM_LIMIT;
                $aStatus = UserBankCard::$validStatuses;
                $bLocked = UserUserBankCard::getUserCardsLockStatus();
                // pr($this->viewVars['datas']->toArray());exit;
                $this->setVars(compact('iLimitCardsNum', 'aStatus', 'bLocked'));
                break;
            case 'create':
            case 'edit':
                // $oBank = new Bank;
                // $this->setVars('aBanks', $oBank->getValueListArray('name', ['status' => ['=', Bank::BANK_STATUS_AVAILABLE]], [], true));
                $aBanks = [];
                $oBanks = Bank::getSupportCardBank();
                foreach ($oBanks as $key => $value) {
                    if($value->identifier == 'TENPAY') continue;
                    $aBanks[$value->id] = $value->name;
                }
                $aSelectorData = $this->generateSelectorData();
                $this->setVars(compact('aSelectorData', 'aBanks'));
                break;
            case 'validate':
            case 'cardLock':
                $aBindedCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'bank']);
                $bLocked = UserUserBankCard::getUserCardsLockStatus();
                // pr($aBindedCards->toArray());exit;
                $this->setVars(compact('aBindedCards', 'bLocked'));
                break;
            case 'bindCard':
                break;
            default:
                # code...
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
     * [generateHiddenAccount 生成只显示末尾4位的银行卡帐号信息]
     * @param  [String] $account [银行卡帐号]
     * @return [String]          [只显示末尾4位的银行卡帐号信息,且每4位空格隔开]
     */
    // private function generateHiddenAccount($account)
    // {
    //     $str = str_repeat('*', (strlen($account) - 4));
    //     $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($account, -4);
    //     return $account_hidden;
    // }

    public function index() {
        // $this->setVars('datas', $datas);
        $iUserId = Session::get('user_id');
        // $datas = UserBankCard::getUserCardsInfo();
        // pr($datas->toArray());exit;
        if (!$iUserCardNum = UserUserBankCard::getUserCardsNum($iUserId)) {
            $this->saveUrlToSession();
            // pr($iUserCardNum);exit;
            // return View::make('centerUser.withdrawal.noCard');// Redirect::route('user-withdrawals.withdrawal-card');
            $this->action = 'noCard';
            return $this->render();
        }
        $this->params['user_id'] = $iUserId;
        $this->params['status'] = UserBankCard::STATUS_DELETED;
        Session::forget(self::CARD_BINDING_STATUS_NAME);
        return parent::index();
    }

    /**
     * [customDestroy 自定义删除 ]
     * @param  [Int] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    public function customDestroy($iCardId) {
        // pr(Request::method());exit;

        $bLocked = UserUserBankCard::getUserCardsLockStatus();
        if ($bLocked) {
            return $this->goBack('error', __('_basic.bankcards-locked'));
        }
        if (Request::method() == 'POST') {
            return $this->postDestroy($iCardId);
        } else {
            $oUserBankCard = UserUserBankCard::find($iCardId);
            $sAccountHidden = $oUserBankCard->account_hidden;
            $this->setVars(compact('sAccountHidden', 'iCardId'));
            $this->action = 'destroy';
            return $this->render();
        }
    }

    /**
     * [postDestroy description]
     * @param  [Int] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function postDestroy($iCardId) {
        $bValidated = $this->postValidate($iCardId);
        if (!$bValidated) {
            return $this->goBack('error', __('_basic.validate-card-fail'));
        } else {
            return parent::destroy($iCardId);
            // return Redirect::route('bank-cards.index');
        }
    }

    /**
     * [cardLock 锁定银行卡]
     * @param  [Integer] $iStatus [当前的锁定状态]
     * @return [Response]          [description]
     */
    public function cardLock($iStatus) {
        $iUserId = Session::get('user_id');
        $aUserBankCards = UserUserBankCard::getUserCardsInfo($iUserId);
        if (!count($aUserBankCards)) {
            return $this->goBack('error', __('_userbankcard.bind-card-first'));
        }
        $sFundPassword = UserUser::find($iUserId)->fund_password;
        if ($sFundPassword == null) {
            return Redirect::route('users.safe-reset-fund-password');
        }
        if (Request::method() == 'POST') {
            return $this->postCardLock($iStatus);
        } else {
            $this->action = 'cardLock';
            $this->setVars(compact('iStatus'));
            return $this->render();
        }
    }

    /**
     * [postCardLock 响应更改银行卡锁定状态的POST请求]
     * @param  [Integer] $iStatus [当前的锁定状态]
     * @return [Response]          [description]
     */
    private function postCardLock($iStatus) {
        // 不允许用户自己解锁银行卡
        if ($iStatus == UserBankCard::UNLOCKED) {
            return $this->goBack('error', __('_userbankcard.unlock-card-forbidden'));
        }
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        $sFundPassword = trim(Input::get('fund_password'));
        $bValidated = $oUser->checkFundPassword($sFundPassword);
        if (!$bValidated) {
            return $this->goBack('error', __('_basic.validate-fund-password-fail'));
        }
        // TODO，锁定非删除的所有卡还是只锁启用状态的卡
        $aCards = UserUserBankCard::getUserCardsInfo($iUserId);
        if (!$aCards) {
            return $this->goBack('error', __('_basic.no-bankcards'));
        }
        // pr($iStatus);exit;
        $bSucc = true;
        DB::connection()->beginTransaction();
        foreach ($aCards as $key => $oCard) {
            $bSucc = UserUserBankCard::setLockStatus($oCard->id, $iStatus, $iUserId);
            if (!$bSucc)
                break;
        }
        $sPre = $iStatus ? 'locked' : 'unlock';
        // pr((int)$bSucc);exit;
        if ($bSucc) {
            DB::connection()->commit();
            if ($sPre == 'locked') {
//                Event::fire('bomao.auth.lockBankCard', $oUser->id);
                Queue::push('EventTaskQueue', ['event'=>'bomao.auth.lockBankCard', 'user_id'=>$oUser->id, 'data' => []], 'activity');
                
            }
            return $this->goBackToIndex('success', __('_basic.' . $sPre . '-bankcards-success', $this->langVars));
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', __('_basic.' . $sPre . '-bankcards-fail'));
        }
    }

    /**
     * [modifyCard 新增/编辑银行卡绑定]
     * @param  [Integer] $iStep [步骤]
     * @param  [Integer] $id    [编辑操作时, 被编辑的银行卡绑定记录id]
     * @return [Response]        [description]
     */
    public function modifyCard($iStep, $id = null) {
        // pr(Request::method());
        // pr($iStep . '------' . Session::get(self::CARD_BINDING_STATUS_NAME). '----------');
        // $oUserBankCard = new UserUserBankCard;
        $bLocked = UserUserBankCard::getUserCardsLockStatus();
        if ($bLocked) {
            return $this->goBack('error', __('_basic.bankcards-locked'));
        }
        if (!Session::has(self::CARD_BINDING_STATUS_NAME)) {
            $iUserId = Session::get('user_id');
            $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
            // pr($iBindedCardsNum);exit;
            if ($iBindedCardsNum > 0 && $iBindedCardsNum < self::CARD_NUM_LIMIT) {
                Session::put(self::CARD_BINDING_STATUS_NAME, 0);
            } else {
                Session::put(self::CARD_BINDING_STATUS_NAME, 1);
            }
        }
        // pr($iStep . '------------' . Session::get(self::CARD_BINDING_STATUS_NAME) . '----------');
        if ($iStep > Session::get(self::CARD_BINDING_STATUS_NAME)) {
            $iStep = Session::get(self::CARD_BINDING_STATUS_NAME);
            return Redirect::route('bank-cards.' . ($id ? 'modify-card' : 'bind-card'), $id ? [$iStep, $id] : $iStep);
        }
        // $iStep = Session::get(self::CARD_BINDING_STATUS_NAME);
        // pr($iStep . '------------' . Session::get(self::CARD_BINDING_STATUS_NAME));exit;
        switch ($iStep) {
            case 0:
                return $this->validate($id);
                break;
            case 1:
                return $this->generateCardInfo($id);
                break;
            case 2:
                return $this->confirm($id);
                break;
            case 3:
            case 4:
            case 5:
                return $this->result();
                break;
            default:
                return $this->validate();
                break;
        }
    }

    /**
     * [bindCard 银行卡绑定, 如果未设置资金密码，则跳转到资金密码设置页面]
     * @param  [Integer] $iStep [步骤]
     * @return [Response]       [description]
     */
    public function bindCard($iStep) {
        // $2y$10$G7VeZnQfLpbr9h6cCzTxdeWpW.do/Gz1l7o592ktLIe7QWEloR6Ny
        $iUserId = Session::get('user_id');
        $sFundPassword = UserUser::find($iUserId)->fund_password;
        // pr($iUserId);
        // pr((int)($sFundPassword == null));exit;
        // $bFundPasswordSetted = (int)$sFundPassword;
        // pr((boolean)$bFundPasswordSetted);exit;
      
        if ($sFundPassword == null) {
            $this->saveUrlToSession();
            return Redirect::route('users.safe-reset-fund-password');
        } else {
            if (!Session::get(self::FIRST_BINDING_NAME)) {
                $bIsFirst = UserUserBankCard::getUserCardsNum($iUserId) ? 0 : 1;
                // pr('new_' . $bIsFirst);exit;
                Session::put(self::FIRST_BINDING_NAME, $bIsFirst);
            } else {
                $bIsFirst = Session::get(self::FIRST_BINDING_NAME);
                // pr('session_' . self::FIRST_BINDING_NAME . $bIsFirst);exit;
            }
            // pr($iStep);exit;
            $this->setVars(compact('bIsFirst'));
            return $this->modifyCard($iStep);
        }
    }

    /**
     * [validate 验证银行卡信息]
     * @param  [Integer] $iCardId [待验证的银行卡id]
     * @return [Response]         [description]
     */
    private function validate($iCardId = null) {
         $iUserId = Session::get('user_id');
         $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId($iUserId);
        if(empty($oRes)){
            return Redirect::route('security-questions.index');  
        }
        if (!SecurityUserAnswer::checkSecurity()){
            return SecurityUserAnswer::Redirect();
        }
        // pr(Request::method());
        if (Request::method() == 'POST') {
            // pr($iCardId);exit;
            $bValidated = $this->postValidate($iCardId);
            if (!$bValidated) {
                // pr((int)$bValidated);exit;
                // pr(Session::get(self::CARD_BINDING_STATUS_NAME) . '-------');exit;
                // Session::put(self::CARD_BINDING_STATUS_NAME, 0);
                // pr(Input::all());exit;
                return $this->goBack('error', __('_basic.validate-card-fail'));
            } else {
                // pr(Session::get(self::CARD_BINDING_STATUS_NAME));exit;
                Session::put(self::CARD_BINDING_STATUS_NAME, 1);
                SecurityUserAnswer::destroyCallbackSession();
                return Redirect::route('bank-cards.' . ($iCardId ? 'modify-card' : 'bind-card'), $iCardId ? [1, $iCardId] : 1);
            }
        } else {
            if (!SecurityUserAnswer::checkReferer()){
                return SecurityUserAnswer::Redirect();
            }
            $data = UserUserBankCard::find($iCardId);
            // $isEdit = $iCardId ? 1 : 0;
            $this->setVars(compact('data', 'iCardId'));
            $this->action = 'validate';
            Session::put(self::CARD_BINDING_STATUS_NAME, 0);
            return $this->render();
        }
    }

    /**
     * [generateCardInfo 生成银行卡绑定信息]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function generateCardInfo($iCardId) {
        // pr(Request::method());exit;
        // if (Request::method() == 'POST') {
        //     $this->view = $this->resourceView . '.' . $this->views[2];
        //     // $this->setVars()
        //     return $this->render();
        // } else {
        // if (Session::get(self::CARD_BINDING_STATUS_NAME) == 1) {
        $this->action = $iCardId ? 'edit' : 'create';
        if ($iCardId) {
            $data = UserUserBankCard::find($iCardId);
            $this->setVars(compact('data', 'iCardId'));
        }
        // $this->view = $this->resourceView . '.' . $this->views[2];
        Session::put(self::CARD_BINDING_STATUS_NAME, 2);
        return $this->render();
        // }
    }

    /**
     * [confirm 确认银行卡信息]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function confirm($iCardId = null) {
        // pr(Session::get(self::CARD_BINDING_STATUS_NAME));exit;
        if (Session::get(self::CARD_BINDING_STATUS_NAME) == 2) {
            // pr($iCardId);
            // pr(Session::get(self::CARD_BINDING_STATUS_NAME));exit;
            Session::put(self::CARD_BINDING_STATUS_NAME, 3);
            $aFormData = trimArray(Input::all());
            // pr($aFormData);exit;
            $this->action = 'confirm';
            // $this->view = $this->resourceView . '.' . $this->views[2];
            $this->setVars(compact('aFormData', 'iCardId'));
            return $this->render();
        } else if (Session::get(self::CARD_BINDING_STATUS_NAME) == 3 && in_array(Request::method(), ['PUT', 'POST'])) {
            // pr($iCardId);
            // pr(Session::get(self::CARD_BINDING_STATUS_NAME));exit;
            DB::connection()->beginTransaction();
            if ($iCardId) {
                $this->model = $this->model->find($iCardId);
                $this->_fillModelDataFromInput();
                $bSucc = $this->model->save();
            } else {
                $bSucc = $this->saveData();
            }
            // pr($this->model->toArray());exit;
            if ($bSucc) {
                // pr((int)$bSucc);exit;
                DB::connection()->commit();
                Session::put(self::CARD_BINDING_STATUS_NAME, 4);
                // $this->action = 'result';
                // $aFormData = trimArray(Input::all());
                // $this->setVars(compact('aFormData'));
                // return $this->render();
                // pr(route('bank-cards.' . ($iCardId ? 'modify-card' : 'bind-card'), $iCardId ? [3, $iCardId] : 3));exit;
                return Redirect::route('bank-cards.' . ($iCardId ? 'modify-card' : 'bind-card'), $iCardId ? [3, $iCardId] : 3);
                // return Redirect::route('bank-cards.result');
            } else {
                // pr($this->model->toArray());
                // pr('---------');
                // pr($this->model->validationErrors);exit;
                DB::connection()->rollback();
                Session::put(self::CARD_BINDING_STATUS_NAME, 2);
                $this->langVars['reason'] = & $this->model->getValidationErrorString();
                // pr($this->langVars['reason']);exit;
                $aFormData = $this->params;
                // pr($aFormData);exit;
                $sRedirectName = $iCardId ? 'bank-cards.modify-card' : 'bank-cards.bind-card';
                // return $this->goBack('error', __('_basic.create-fail', $this->langVars));
                return Redirect::route($sRedirectName, $iCardId ? [1, $iCardId] : 1)
                                ->with('error', __('_userbankcard.bind-card-fail', $this->langVars));
            }
        }
    }

    /**
     * [result 响应绑定银行卡结果页面]
     * @return [Response] [description]
     */
    private function result() {
        // pr(Request::method());exit;
        $this->action = 'result';
        $bSucceed = (int) (Session::get(self::CARD_BINDING_STATUS_NAME) == 4);
        $this->setVars(compact('bSucceed'));
        Session::forget(self::CARD_BINDING_STATUS_NAME);
        Session::forget(self::FIRST_BINDING_NAME);
        return $this->render();
    }

    // protected function renderCustomView()
    // {
    //     $this->beforeRender();
    //     $this->view = $this->resourceView . '.' . $this->action; // $this->views[$this->action];
    //     // pr($this->view);
    //     return View::make($this->view)->with($this->viewVars);
    // }

    /**
     * [postValidate 提交验证 ]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Boolean] [验证是否成功]
     */
    private function postValidate($iCardId) {
        // $oUserBankCard = new UserUserBankCard;
        // $aConditions = $this->generateValidateFrom();
        // $bValidated = $oUserBankCard->doWhere($aConditions)->exists();
        $iId = trim(Input::get('id'));
        $sAccountName = trim(Input::get('account_name'));
        $sAccount = trim(Input::get('account'));
        $sAccount = str_replace(' ', '', $sAccount);
        $oCardInfo = UserUserBankCard::find($iId);
        $bValidated = !!$oCardInfo;
        // pr(Input::all());
        // pr($oCardInfo->toArray());
        // exit;
        if ($bValidated) {
            $bValidated = ($oCardInfo->account_name == $sAccountName && $oCardInfo->account == $sAccount);
            if ($bValidated) {
                $iUserId = Session::get('user_id');
                $sFundPassword = trim(Input::get('fund_password'));
                $oUser = UserUser::find($iUserId);
                // pr($oUser->toArray());exit;
                $bValidated = $oUser->checkFundPassword($sFundPassword);
            }
        }
        return $bValidated;
    }

}
