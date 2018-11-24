<?php

# 提现

class MobileWithdrawalController extends MobileBaseController {

    protected $modelName = 'UserWithdrawal';

    const WITHDRAWAL_STATUS = 'WithdrawalStatus'; // withdrawal step flag

    public function index() {
        // $this->params['user_id'] = Session::get('user_id');
        $iLoginUserId = Session::get('user_id');
        // 如果是代理并且有username参数，则精准查找该代理下用户名为输入参数的子用户的提现列表
        // 否则，查询该代理的提现列表
        if (Session::get('is_agent') && isset($this->params['username']) && $this->params['username']) {
            $oUser = User::getUserByParams(['username' => $this->params['username'], 'forefather_ids' => $iLoginUserId], ['forefather_ids']);
            if ($oUser) {
                $this->params['user_id'] = $oUser->id;
            } else {
                $aReplace = ['username' => $this->params['username']];
                return $this->goBack('error', __('_basic.not-your-user', $aReplace));
            }
        } else {
            $this->params['user_id'] = $iLoginUserId;
        }
        return parent::index();
    }

    protected function beforeRender() {
        parent::beforeRender();
        $oUserBandCard = new UserUserBankCard;
        $iUserId = Session::get('user_id');
        $sUsername = Session::get('username');
        $oAccount = Account::getAccountInfoByUserId($iUserId);

        $iMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $iMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
        $this->setVars(compact('iMinWithdrawAmount', 'iMaxWithdrawAmount'));
        switch ($this->action) {
            case 'index':
//                $aSum = $this->getSumData(['amount', 'transaction_amount', 'transaction_charge'], true);
//                $aSum['money_change'] = (int)$aSum['amount_sum'] - (int)$aSum['transaction_charge_sum'];
                $this->setVars('reportName', 'withdrawApply');
                $aStatusDesc = UserWithdrawal::getTranslateValidStatus();
                $this->setVars(compact('aStatusDesc'));
            case 'confirm':
                $iCardId = trim(Input::get('id'));
                $oBankCard = $oUserBandCard->getUserCardInfoById($iCardId);
                $aInputData = trimArray(Input::all());
                $this->setVars(compact('oBankCard', 'oAccount', 'aInputData'));
                break;
            case 'withdraw':
                $aBankCards = $oUserBandCard->getUserCardsInfo($iUserId, ['id', 'account', 'account_name', 'bank']);
                $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily'); // UserWithdrawal::WITHDRAWAL_LIMIT_PER_DAY;
                $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);
                // pr($aBankCards);exit;
                // pr($aSeriesLotteries);exit;

                $this->setVars(compact('aBankCards', 'sUsername', 'oAccount', 'iWithdrawLimitNum', 'iWithdrawalNum'));
                break;
        }
    }

    // protected function renderCustomView()
    // {
    //     $this->beforeRender();
    //     $this->view = $this->resourceView . '.' . $this->action; // $this->views[$this->action];
    //     // pr($this->view);
    //     return View::make($this->view)->with($this->viewVars);
    // }

    /**
     * [withdraw 通过Sessin中保存的状态值判断当前进行到提现的哪一步]
     * @param  [Integer] $iStep [提现步骤]
     * @return [Response]       [description]
     */
    public function withdraw($iStep = 0) {
        $this->setVars('min_withdraw_amount', SysConfig::readValue('withdraw_default_min_amount'));
        $this->setVars('max_withdraw_amount', SysConfig::readValue('withdraw_default_max_amount'));
        $iUserId = Session::get('user_id');
        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            $this->halt(false, 'error', UserUser::ERRNO_MISSING_USERNAME);
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            $this->halt(false, 'error', UserUser::ERRNO_USER_FUND_BLOCKED);
        }
        // $oUserBandCard = new UserUserBankCard;
        if (!$iUserCardNum = UserUserBankCard::getUserCardsNum()) {
            $data['redirect_to'] = 'mobile-users.no-card';
            $this->halt(false, 'error', UserBankCard::ERRNO_NO_CARD, $a, $a, $data);
        }
        switch ($iStep) {
            case 0:
                $this->withdrawForm();
                break;
            case 1:
                return $this->confirm();
                break;
            default:
                break;
        }
    }

    /**
     * [withdraw 发起提现]
     * @return [Response] [description]
     */
    private function withdrawForm() {
        $iUserId = Session::get('user_id');
        $oAccount = Account::getAccountInfoByUserId($iUserId, ['user_id', 'username', 'available', 'withdrawable']);
        $aAccounts = is_object($oAccount) ? $oAccount->attributesToArray() : [];
        $aBankCards = UserUserBankCard::getUserCardsInfo($iUserId, ['id', 'account', 'account_name', 'bank', 'province', 'city'])->toArray();
        $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily'); // UserWithdrawal::WITHDRAWAL_LIMIT_PER_DAY;
        $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);
        $this->setVars('bank_cards', $aBankCards);
        $this->setVars('accounts', $aAccounts);
        $this->setVars('withdraw_limit_num', $iWithdrawLimitNum);
        $this->setVars('withdraw_num', $iWithdrawalNum);
        $this->halt(true, 'info', null, $a, $a, $this->viewVars);
    }

    /**
     * [confirm 确认提现]
     * @return [Response] [description]
     */
    private function confirm() {
        $sBankCardAccount = trim($this->params['account']);
        $oUserBandCard = UserUserBankCard::getUserBankCardAccount($sBankCardAccount);
        if (!is_object($oUserBandCard)) {
            $this->halt(false, 'error', UserBankCard::ERRNO_MISSING_BANK_CARD);
        }
        // 新增/修改卡后2个小时才可以提现
//        if (Carbon::now()->subHour(Withdrawal::WITHDRAWAL_TIME_LIMIT)->toDateTimeString() < $oUserBandCard->updated_at) {
//            $sErrMsg = '新增或修改银行卡绑定后未满' . Withdrawal::WITHDRAWAL_TIME_LIMIT . '个小时，无法提现，请稍后再试！';
//            $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED, $a, $a, $a, null, $sErrMsg);
//        }
        // pr(Session::get(self::WITHDRAWAL_STATUS));exit;
        $iUserId = Session::get('user_id');
        $sFundPassword = trim($this->params['fund_password']);
        $fAmount = floatval(trim($this->params['amount']));
        $oUser = User::find($iUserId);
        // pr($oUser->account_id);
        $oAccount = Account::lock($oUser->account_id, $iLocker);
        // pr($iLocker);
        // pr($oAccount->toArray());exit;
        if (empty($oAccount)) {
//                $this->writeLog('lock-fail');
            $this->halt(false, 'error', Account::ERRNO_LOCK_FAILED);
//                $this->halt(false,'netAbnormal',Account::ERRNO_LOCK_FAILED);
        }

        // pr(Input::all());
        // pr('----------');
        // pr($fAmount . '  ' . $oAccount->withdrawable);exit;
        // TODO 待过滤提现黑白名单
        // $aWithdrawalBlackList = [];
        // if (in_array($iUserId, $aWithdrawalBlackList)) {
        //     $this->langVars['reason'] = '无法提现 ';
        // }
        // TODO 提现金额最小值，应该等同于所选银行卡的最小提现金额
        $fMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $fMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
        if (!$bValidated = ( is_float($fAmount) && $fAmount >= $fMinWithdrawAmount && $fAmount <= $fMaxWithdrawAmount )) {
            Account::unlock($oUser->account_id, $iLocker);
            $sErrMsg = '提现金额范围' . $fMinWithdrawAmount . ' - ' . $fMaxWithdrawAmount . '元，并且只允许两位小数';
            $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED, $a, $a, $a, null, $sErrMsg);
        }
        //校验用户可提现余额是否符合要求,代理用户:取可用余额;玩家:取可用余额和可提现余额中的较小值
        if (Session::get('is_agent')) {
            if (!$bValidated = $fAmount <= $oAccount->available) {
                Account::unlock($oUser->account_id, $iLocker);
                $sErrMsg = '提现金额大于可提现余额';
                $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED, $a, $a, $a, null, $sErrMsg);
            }
        } else if (Session::get('is_player')) {
            if (!$bValidated = $fAmount <= $oAccount->true_withdrawable) {
                Account::unlock($oUser->account_id, $iLocker);
                $sErrMsg = '提现金额大于可提现余额';
                $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED, $a, $a, $a, null, $sErrMsg);
            }
        }
        if (!$bValidated = $oUser->checkFundPassword($sFundPassword)) {
            Account::unlock($oUser->account_id, $iLocker);
            $this->halt(false, 'error', UserUser::ERRNO_WRONG_FUND_PASSWORD);
        }
        $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily'); // UserWithdrawal::WITHDRAWAL_LIMIT_PER_DAY;
        $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);
        if ($iWithdrawLimitNum > 0 && $iWithdrawalNum >= $iWithdrawLimitNum) {
            $sErrMsg = '超出每天提现次数'; // _withdrawal.overstep-withdrawal-num-limit-per-day
            $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED, $a, $a, $a, null, $sErrMsg);
        }

        $oWidthdrawal = new UserWithdrawal;
        // pr($this->params);
        $oBank = UserBankCard::find($this->params['id']);
        if (!$oBank) {
            $this->halt(false, 'error', UserBankCard::ERRNO_MISSING_BANK_CARD);
        }
        $data = & $oWidthdrawal->compileData($this->params['id'], $this->params['amount']);
        // pr($data);
        $oWidthdrawal->fill($data);
        // pr($oWidthdrawal->toArray());exit;
        DB::connection()->beginTransaction();
        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_FREEZE_FOR_WITHDRAWAL, $fAmount);
        // pr($iReturn);exit;
        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
//                pr(Transaction->getValidationErrorString());
//                pr($iReturn);
//                pr($this->Message->getResponseMsg($iReturn));
//                exit;
            $this->langVars['reason'] = $this->Message->getResponseMsg($iReturn);
            $this->halt(false, 'error', $iReturn);
        }
        if ($bSucc = $oWidthdrawal->save()) {
            DB::connection()->commit();
            Account::unlock($oUser->account_id, $iLocker);
            Session::forget(self::WITHDRAWAL_STATUS);
//                return Redirect::route('user-withdrawals.index');
            $this->halt(true, 'success', UserWithdrawal::ERRNO_WITHDRAWAL_SUCCESS);
        } else {
            // $queries = DB::getQueryLog();
            // $last_query = end($queries);
            // pr($last_query);
            // pr($oWidthdrawal->toArray());
            // pr('---------');
            // pr($oWidthdrawal->validationErrors);exit;
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $this->langVars['reason'] = & $oWidthdrawal->getValidationErrorString();
            // pr($oWidthdrawal->validationErrors);
            // pr($this->langVars['reason']);
            // exit;
            $this->halt(false, 'error', UserWithdrawal::ERRNO_WITHDRAWAL_FAILED);
        }
    }

}
