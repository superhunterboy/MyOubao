<?php

# 用户银行卡管理

class MobileBankCardController extends MobileBaseController {

    protected $modelName = 'UserUserBankCard';

    const CARD_NUM_LIMIT = 4;
    const CARD_BINDING_STATUS_NAME = 'CardBindingStatus';
    const FIRST_BINDING_NAME = 'IsFirstBinding';

    protected function beforeRender() {
        parent::beforeRender();

        $iUserId = Session::get('user_id');
        $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
        $this->setVars(compact('iBindedCardsNum'));
        switch ($this->action) {
            case 'index':
                $iLimitCardsNum = self::CARD_NUM_LIMIT;
                $aStatus = UserBankCard::$validStatuses;
                $bLocked = UserUserBankCard::getUserCardsLockStatus();
                $this->setVars(compact('iLimitCardsNum', 'aStatus', 'bLocked'));
                break;
            case 'create':
            case 'edit':
                $aBanks = [];
                $oBanks = Bank::getSupportCardBank();
                foreach ($oBanks as $key => $value) {
                    $aBanks[$value->id] = $value->name;
                }
                $this->setVars(compact('aBanks'));
                break;
            default:
                break;
        }
    }

    public function index() {
        $iUserId = Session::get('user_id');
        $oUser = User::find($iUserId);
        if ($oUser->fund_password) {
            $this->params['user_id'] = $iUserId;
            $this->params['status'] = UserBankCard::STATUS_DELETED;
            Session::forget(self::CARD_BINDING_STATUS_NAME);
            $data = parent::mobileIndex(UserBankCard::$mobileColumns);
            $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
            $data['binded_card_num'] = $iBindedCardsNum;
            $data['max_card_num'] = 4;
            $data['is_set_fund_password'] = 1;
            $this->halt(true, 'info', null, $a, $a, $data);
        } else {
            $data['is_set_fund_password'] = 0;
            $this->halt(false, 'no-fund-password', UserUser::ERRNO_MISSING_FUND_PASSWORD);
        }
    }

    /**
     * [customDestroy 自定义删除 ]
     * @param  [Int] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    public function customDestroy() {

        $bLocked = UserUserBankCard::getUserCardsLockStatus();
        if ($bLocked) {
            $this->halt(false, 'error', UserBankCard::ERRNO_CARD_LOCKED);
        }
        if ($this->params) {
            $this->postDestroy();
        } else {
            $this->halt(false, 'error', null);
        }
    }

    /**
     * [postDestroy description]
     * @param  [Int] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function postDestroy() {
        $bValidated = $this->postValidate();
        if (!$bValidated) {
            $this->halt(false, 'error', UserBankCard::ERRNO_VALID_FAILED);
        } else {
            parent::destroy($this->params['id']);
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
            $this->halt(false, 'error', UserBankCard::ERRNO_NO_CARD);
        }
        $sFundPassword = UserUser::find($iUserId)->fund_password;
        if ($sFundPassword == null) {
            $this->halt(false, 'error', UserUser::ERRNO_MISSING_FUND_PASSWORD);
        }
        if (Request::method() == 'POST') {
            $this->postCardLock($iStatus);
        } else {
            $aBindedCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'bank'])->toArray();
            $bLocked = UserUserBankCard::getUserCardsLockStatus();
            $this->setVars(compact('aBindedCards', 'bLocked'));
            $this->setVars(compact('iStatus'));
            $this->halt(true, 'info', null, $a, $a, $this->viewVars);
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
            $this->halt(false, 'error', UserBankCard::ERRNO_UNLOCK_CARD_FORBIDDEN);
        }
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        $sFundPassword = trim(Input::get('fund_password'));
        $bValidated = $oUser->checkFundPassword($sFundPassword);
        if (!$bValidated) {
            $this->halt(false, 'error', UserUser::ERRNO_WRONG_FUND_PASSWORD);
        }
        $aCards = UserUserBankCard::getUserCardsInfo($iUserId);
        if (!$aCards) {
            $this->halt(false, 'error', UserBankCard::ERRNO_NO_CARD);
        }
        $bSucc = true;
        DB::connection()->beginTransaction();
        foreach ($aCards as $key => $oCard) {
            $bSucc = UserUserBankCard::setLockStatus($oCard->id, $iStatus, $iUserId);
            if (!$bSucc)
                break;
        }
        if ($bSucc) {
            DB::connection()->commit();
            $this->halt(true, 'success', UserBankCard::ERRNO_LOCK_CARD_SUCCESS);
        } else {
            DB::connection()->rollback();
            $this->halt(false, 'error', UserBankCard::ERRNO_LOCK_CARD_FAILED);
        }
    }

    /**
     * [modifyCard 新增/编辑银行卡绑定]
     * @param  [Integer] $iStep [步骤]
     * @param  [Integer] $id    [编辑操作时, 被编辑的银行卡绑定记录id]
     * @return [Response]        [description]
     */
    public function modifyCard($iStep, $id = null) {
        $bLocked = UserUserBankCard::getUserCardsLockStatus();
        if ($bLocked) {
            $this->halt(false, 'error', UserBankCard::ERRNO_CARD_LOCKED);
        }
        if (!Session::has(self::CARD_BINDING_STATUS_NAME)) {
            $iUserId = Session::get('user_id');
            $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
            if ($iBindedCardsNum > 0 && $iBindedCardsNum < self::CARD_NUM_LIMIT) {
                Session::put(self::CARD_BINDING_STATUS_NAME, 0);
            } else {
                Session::put(self::CARD_BINDING_STATUS_NAME, 1);
            }
        }
        if ($iStep > Session::get(self::CARD_BINDING_STATUS_NAME)) {
            $iStep = Session::get(self::CARD_BINDING_STATUS_NAME);
            $data['redirect_to'] = 'mobile-bank-cards.' . ($id ? 'modify-card' : 'bind-card');
            $data['step'] = $iStep;
            $this->halt(false, 'error', UserBankCard::ERRNO_CARD_OPERATION_STEP_ERROR, $a, $a, $data);
        }
        switch ($iStep) {
            case 0:
                $this->validate($id);
                break;
            case 1:
                $this->generateCardInfo($id);
                break;
            case 2:
                $this->confirm($id);
                break;
            default:
                $this->validate();
                break;
        }
    }

    /**
     * [bindCard 银行卡绑定, 如果未设置资金密码，则跳转到资金密码设置页面]
     * @param  [Integer] $iStep [步骤]　0:验证卡信息，1:增加银行卡，2:确认卡信息
     * @return [Response]       [description]
     */
    public function bindCard($iStep) {
        $iUserId = Session::get('user_id');
        $sFundPassword = UserUser::find($iUserId)->fund_password;
        if ($sFundPassword == null) {
            $this->halt(false, 'no-fund-password', UserUser::ERRNO_MISSING_FUND_PASSWORD);
        }
        $iBindedCardsNum = UserUserBankCard::getUserCardsNum($iUserId);
        if ($iBindedCardsNum >= self::CARD_NUM_LIMIT) {
            $this->halt(false, 'error', UserBankCard::ERRNO_OUT_MAX_CARD_NUM);
        }
        if (!Session::get(self::FIRST_BINDING_NAME)) {
            $bIsFirst = UserUserBankCard::getUserCardsNum($iUserId) ? 0 : 1;
            Session::put(self::FIRST_BINDING_NAME, $bIsFirst);
        } else {
            $bIsFirst = Session::get(self::FIRST_BINDING_NAME);
        }
        //如果是第一次绑卡，不需要验证，直接跳转到增加银行卡操作
        if ($bIsFirst && $iStep == 0) {
            $iStep = 1;
        }
        $this->modifyCard($iStep);
    }

    /**
     * [validate 验证银行卡信息]
     * @param  [Integer] $iCardId [待验证的银行卡id]
     * @return [Response]         [description]
     */
    private function validate($iCardId = null) {
        if ($this->params) {
            $bValidated = $this->postValidate();
            if (!$bValidated) {
                $this->halt(false, 'error', UserBankCard::ERRNO_VALID_FAILED);
            } else {
                Session::put(self::CARD_BINDING_STATUS_NAME, 1);
                $this->halt(true, 'success', UserBankCard::ERRNO_VALID_SUCCESS);
            }
        } else {
            $iUserId = Session::get('user_id');
            $aBindedCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'bank'])->toArray();
            $bLocked = UserUserBankCard::getUserCardsLockStatus();
            $this->setVars(compact('aBindedCards', 'bLocked'));
            $this->setVars('step', 0);
            Session::put(self::CARD_BINDING_STATUS_NAME, 0);
            $this->halt(true, 'info', null, $a, $a, $this->viewVars);
        }
    }

    /**
     * [generateCardInfo 生成银行卡绑定信息]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function generateCardInfo($iCardId) {
        $aBanks = [];
        $oBanks = Bank::getSupportCardBank();
        foreach ($oBanks as $key => $value) {
            $aBanks[$value->id] = $value->name;
        }
        $this->setVars('banks', $aBanks);
        $this->action = $iCardId ? 'edit' : 'create';
        if ($iCardId) {
            $data = UserUserBankCard::find($iCardId);
            $this->setVars(compact('data', 'iCardId'));
        }
//        $this->setVars('redirect_to', $this->action);
        $this->setVars('provice_cities', $this->getProvinceAndCities());
        Session::put(self::CARD_BINDING_STATUS_NAME, 2);
        $this->halt(true, 'success', null, $a, $a, $this->viewVars);
        // }
    }

    /**
     * [confirm 确认银行卡信息]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Response]          [description]
     */
    private function confirm($iCardId = null) {
        if (Session::get(self::CARD_BINDING_STATUS_NAME) == 2 && $this->params) {
            DB::connection()->beginTransaction();
            if ($iCardId) {
                $this->model = $this->model->find($iCardId);
                $this->_fillModelDataFromInput();
                $bSucc = $this->model->save();
            } else {
                $bSucc = $this->saveData();
            }
            if ($bSucc) {
                DB::connection()->commit();
                Session::forget(self::CARD_BINDING_STATUS_NAME);
                Session::forget(self::FIRST_BINDING_NAME);
                $this->halt(true, 'success', $iCardId ? UserBankCard::ERRNO_MODIFY_CARD_SUCCESS : UserBankCard::ERRNO_BIND_CARD_SUCCESS);
            } else {
                DB::connection()->rollback();
                Session::put(self::CARD_BINDING_STATUS_NAME, 2);
                $data['step'] = 2;
                $this->halt(false, 'error', $iCardId ? UserBankCard::ERRNO_MODIFY_CARD_FAILED : UserBankCard::ERRNO_BIND_CARD_FAILED, $a, $a, $data, null, $this->model->getValidationErrorString());
            }
        }
    }

    /**
     * [postValidate 提交验证 ]
     * @param  [Integer] $iCardId [银行卡id]
     * @return [Boolean] [验证是否成功]
     */
    private function postValidate() {
        $iId = trim(array_get($this->params, 'id'));
        $sAccountName = trim(array_get($this->params, 'account_name'));
        $sAccount = trim(array_get($this->params, 'account'));
        $sAccount = str_replace(' ', '', $sAccount);
        $oCardInfo = UserUserBankCard::find($iId);
        $bValidated = !!$oCardInfo;
        if ($bValidated) {
            $bValidated = ($oCardInfo->account_name == $sAccountName && $oCardInfo->account == $sAccount);
            if ($bValidated) {
                $iUserId = Session::get('user_id');
                $sFundPassword = trim(array_get($this->params, 'fund_password'));
                $oUser = UserUser::find($iUserId);
                $bValidated = $oUser->checkFundPassword($sFundPassword);
            }
        }
        return $bValidated;
    }

    /**
     * 提供移动端获取所有省市的json数据
     */
    public function getProvinceAndCities() {
        $aWidget = Config::get('widget.widget.districts');
        $generator = new SelectTemplateGenerator;
        $result = $generator->generateJsonData($aWidget);
        return $result;
    }

}
