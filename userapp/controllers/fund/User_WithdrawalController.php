<?php

# 提现

class User_WithdrawalController extends UserBaseController {

    protected $resourceView = 'centerUser.withdrawal';
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
        //过滤某些用户的某个时间点的以前记录
        if ($aExludeUsers = Config::get('transaction')['exclude_user_view']) {
            foreach ($aExludeUsers as $username => $datetime) {
                if ($username == Session::get('username')) {
                    $this->params['request_time_from'] = $this->params['created_at_from'] = $datetime;
                    break;
                }
            }
        }
        return parent::index();
    }

    protected function beforeRender() {
        parent::beforeRender();
        $oUserBandCard = new UserUserBankCard;
        $iUserId = Session::get('user_id');
        $sUsername = Session::get('username');
        $oAccount = Account::getAccountInfoByUserId($iUserId);
        $aIdentifiers = Bank::getAllBankIdentifier();

        $iMinWithdrawAmount = SysConfig::readValue('withdraw_default_min_amount');
        $iMaxWithdrawAmount = SysConfig::readValue('withdraw_default_max_amount');
        $this->setVars(compact('iMinWithdrawAmount', 'iMaxWithdrawAmount', 'aIdentifiers'));
        switch ($this->action) {
            case 'index':
//                $aSum = $this->getSumData(['amount', 'transaction_amount', 'transaction_charge'], true);
//                $aSum['money_change'] = (int)$aSum['amount_sum'] - (int)$aSum['transaction_charge_sum'];
                $this->setVars('reportName', 'index');
                $aStatusDesc = UserWithdrawal::getTranslateValidStatus();
                $this->setVars(compact('aStatusDesc'));
            case 'confirm':
                $iCardId = trim(Input::get('id'));
                $oBankCard = $oUserBandCard->getUserCardInfoById($iCardId);
                $aInputData = trimArray(Input::all());
                $this->setVars(compact('oBankCard', 'oAccount', 'aInputData'));
                break;
            case 'withdraw':
                $aBankCards = $oUserBandCard->getUserCardsInfo($iUserId, ['id', 'account', 'account_name', 'bank', 'bank_id', 'islock']);
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
        $iUserId = Session::get('user_id');

        $oUser = UserUser::find($iUserId);
        if (!is_object($oUser)) {
            return $this->goBack('error', __('_user.missing-user'));
        }
        if ($oUser->blocked == UserUser::BLOCK_FUND_OPERATE) {
            return $this->goBack('error', __('_user.withdraw-now-allowed'));
        }
        // $oUserBandCard = new UserUserBankCard;
        if (!$iUserCardNum = UserUserBankCard::getUserCardsNum()) {
            // pr($iUserCardNum);exit;
            // return View::make('centerUser.withdrawal.noCard');// Redirect::route('user-withdrawals.withdrawal-card');
            $this->view = 'centerUser.bankCard.noCard';
            return $this->render();
        }
        $oRes = SecurityUserAnswer::isSetSecurityQuestionByUserId($iUserId);
        if (empty($oRes)) {
            return Redirect::route('security-questions.index');
        }
        //当前是否允许发起提现
        if (SysConfig::readValue('prohibited_withdraw')) {
            return $this->goBack('error', __('_user.prohibited-withdraw'));
        }
        switch ($iStep) {
            case 0:
                return $this->withdrawForm();
                break;
            case 1:
                return $this->confirm();
                break;
            case 2:
                return $this->completed();
                break;
            default:
                # code...
                break;
        }
    }

    /**
     * [withdraw 发起提现]
     * @return [Response] [description]
     */
    private function withdrawForm() {
        // if (Request::method() == 'POST') {
        //     $this->action = 'confirm';
        //     $aInputData   = trimArray(Input::all());
        //     $this->setVars(compact('aInputData'));
        //     return $this->render();
        // } else {
        Session::put(self::WITHDRAWAL_STATUS, 0);
        return $this->render();
        // }
    }

    /**
     * [confirm 确认提现]
     * @return [Response] [description]
     */
    private function confirm() {
        /* ==============流水未达标禁止提款============ */
        $iUserId = Session::get('user_id');

        $fMinCost = UserVoucher::getMinCost($iUserId);
        $fUserCost = UserVoucher::getUserCost($iUserId);
        if ($fMinCost > $fUserCost) {
            $this->langVars['reason'] = '流水不足';
            return $this->goBack('error', __('_withdrawal.withdrawal-failed', $this->langVars));
        }
        /* ==============流水未达标禁止提款============ */

        // pr(Session::get(self::WITHDRAWAL_STATUS));
        $sRequestMethod = Request::method();
        // pr($sRequestMethod);exit;
        if ($sRequestMethod == 'GET' && Session::get(self::WITHDRAWAL_STATUS) == 0) {
            Session::put(self::WITHDRAWAL_STATUS, 1);
            $this->action = 'confirm';
            $iCardId = trim(Input::get('id'));
            // pr(Input::all());exit;
            if (!$iCardId || ($iCardId && !UserUserBankCard::find($iCardId)->exists())) {
                // $this->action = 'requireWithdrawal';
                // return Redirect::route('user-withdrawals.withdraw', 0)->with('error', '没有收款银行卡');
                Session::put(self::WITHDRAWAL_STATUS, 0);
                $this->langVars['reason'] = '没有收款银行卡';
                return $this->goBack('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
            return $this->render();
        } else if ($sRequestMethod == 'POST' && Session::get(self::WITHDRAWAL_STATUS) == 1) {
            $oUserBandCardModel = new UserUserBankCard;
            $iCardId = $this->params['id'];
            $oUserBandCard = $oUserBandCardModel->getUserCardInfoById($iCardId);
            if (empty($oUserBandCard) || $oUserBandCard->user_id != Session::get('user_id')) {
                $this->langVars['reason'] = '银行卡异常';
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
//            $sBankCardAccount = trim(Input::get('account'));
//            $oUserBandCard = UserUserBankCard::getUserBankCardAccount($sBankCardAccount);
            // 新增/修改卡后2个小时才可以提现
            if (Carbon::now()->subHour(Withdrawal::WITHDRAWAL_TIME_LIMIT)->toDateTimeString() < $oUserBandCard->updated_at) {
                $this->langVars['reason'] = '新增或修改银行卡绑定后未满' . Withdrawal::WITHDRAWAL_TIME_LIMIT . '个小时，无法提现，请稍后再试！';
                return Redirect::route('user-withdrawals.withdraw')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
            // pr(Session::get(self::WITHDRAWAL_STATUS));exit;
            $iUserId = Session::get('user_id');
            $sFundPassword = trim(Input::get('fund_password'));
            $fAmount = floatval(trim(Input::get('amount')));
            $oUser = User::find($iUserId);
            // pr($oUser->account_id);
            $this->Message = new Message($this->errorFiles);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            // pr($iLocker);
            // pr($oAccount->toArray());exit;
            if (empty($oAccount)) {
//                $this->writeLog('lock-fail');
                $this->langVars['reason'] = $this->Message->getResponseMsg(Account::ERRNO_LOCK_FAILED);
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
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
                $this->langVars['reason'] = '提现金额范围' . $fMinWithdrawAmount . ' - ' . $fMaxWithdrawAmount . '元，并且只允许两位小数';
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
            //校验用户可提现余额是否符合要求,代理用户:取可用余额;玩家:取可用余额和可提现余额中的较小值
            if (Session::get('is_agent')) {
                if (!$bValidated = $fAmount <= $oAccount->available) {
                    Account::unlock($oUser->account_id, $iLocker);
                    $this->langVars['reason'] = '提现金额大于可提现余额';
                    return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
//                return $this->goBackToIndex('error', __('_withdrawal.withdrawal-failed', $this->langVars));
                }
            } else if (Session::get('is_player')) {
                if (!$bValidated = $fAmount <= $oAccount->true_withdrawable) {
                    Account::unlock($oUser->account_id, $iLocker);
                    $this->langVars['reason'] = '提现金额大于可提现余额';
                    return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
//                return $this->goBackToIndex('error', __('_withdrawal.withdrawal-failed', $this->langVars));
                }
            }
            if (!$bValidated = $oUser->checkFundPassword($sFundPassword)) {
                Account::unlock($oUser->account_id, $iLocker);
                $this->langVars['reason'] = '资金密码错误';
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
//                return $this->goBackToIndex('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
            $iWithdrawLimitNum = SysConfig::readValue('withdraw_max_times_daily'); // UserWithdrawal::WITHDRAWAL_LIMIT_PER_DAY;
            $iWithdrawalNum = UserWithdrawal::getWithdrawalNumPerDay($iUserId);
            if ($iWithdrawLimitNum > 0 && $iWithdrawalNum >= $iWithdrawLimitNum) {
                $this->langVars['reason'] = '超出每天提现次数'; // _withdrawal.overstep-withdrawal-num-limit-per-day
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }

            $oWidthdrawal = new UserWithdrawal;
            // pr($this->params);
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
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
            if ($bSucc = $oWidthdrawal->save()) {
                DB::connection()->commit();
                Account::unlock($oUser->account_id, $iLocker);
                Session::forget(self::WITHDRAWAL_STATUS);
                return Redirect::route('user-withdrawals.withdraw', 2);
//                return Redirect::route('user-withdrawals.index')->with('success', __('_withdrawal.withdrawal-success'));
            } else {
                // $queries = DB::getQueryLog();
                // $last_query = end($queries);
                // pr($last_query);
                // pr($oWidthdrawal->toArray());
                // pr('---------');
                // pr($oWidthdrawal->validationErrors);exit;
                Session::put(self::WITHDRAWAL_STATUS, 1);
                DB::connection()->rollback();
                Account::unlock($oUser->account_id, $iLocker);
                $this->langVars['reason'] = & $oWidthdrawal->getValidationErrorString();
                // pr($oWidthdrawal->validationErrors);
                // pr($this->langVars['reason']);
                // exit;
                return Redirect::route('user-withdrawals.index')->with('error', __('_withdrawal.withdrawal-failed', $this->langVars));
            }
        }
    }

    private function completed() {
        $this->view = 'centerUser.withdrawal.completed';
        return $this->render();
    }

    public function trace($id) {
        $iUserId = Session::get('user_id');
        $oWithdrawal = UserWithdrawal::where('id', $id)->where('user_id', $iUserId)->first();
        $aStatus = UserWithdrawal::getTranslateValidStatus();
        $aStepLab = ['提款申请', '平台审核', '执行出款'];
        $sCsUrl = '若需进一步了解原因，请咨询 <a href="javascript:hj5107.openChat();">客服人员</a>。';

        $aStatusSettingList = [
            Withdrawal::WITHDRAWAL_STATUS_NEW => ['step' => 0, 'time_field' => 'request_time'],
            Withdrawal::WITHDRAWAL_STATUS_CLAIMED => ['step' => 1, 'time_field' => 'claim_at'],
            Withdrawal::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM => ['step' => 1, 'time_field' => 'updated_at'],
            Withdrawal::WITHDRAWAL_STATUS_VERIFIED => ['step' => 1, 'time_field' => 'verified_time'],
            Withdrawal::WITHDRAWAL_STATUS_REFUSE => ['step' => 1, 'time_field' => 'verified_time'],
            Withdrawal::WITHDRAWAL_STATUS_DEDUCT_FAIL => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_PART => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_REFUND => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_MC_ERROR_RETURN => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL => ['step' => 2, 'time_field' => 'mc_request_time'],
            Withdrawal::WITHDRAWAL_STATUS_SUCCESS => ['step' => 2, 'time_field' => 'updated_at'],
            Withdrawal::WITHDRAWAL_STATUS_FAIL => ['step' => 2, 'time_field' => 'updated_at'],
        ];

        $iCurrentStep = $aStatusSettingList[$oWithdrawal->status]['step'];
        $aSteps = [];
        foreach ($aStepLab as $iStep => $sVal) {
            $iStatus = null;
            $aSteps[$iStep] = [];
            if ($iStep < $iCurrentStep) {
                if ($iStep == 0) {
                    $iStatus = Withdrawal::WITHDRAWAL_STATUS_NEW;
                }
                if ($iStep == 1) {
                    $iStatus = Withdrawal::WITHDRAWAL_STATUS_VERIFIED;
                }
            } else if ($iStep == $iCurrentStep) {
                $iStatus = $oWithdrawal->status;
            } else if ($iStep == $iCurrentStep + 1 && !in_array($oWithdrawal->status, [Withdrawal::WITHDRAWAL_STATUS_REFUSE])) {
                $aSteps[$iStep] = [
                    'processing' => 1,
                ];
            }
            if (isset($iStatus)) {
                $dTime = $oWithdrawal->{$aStatusSettingList[$iStatus]['time_field']};
                if (!$dTime) {
                    $dTime = $oWithdrawal->updated_at;
                }
                $aSteps[$iStep] = [
                    'status' => $iStatus,
                    'time' => $dTime,
                ];
            }
            if ($iStatus == Withdrawal::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM) {
                //            $aStatusSettingList[$oWithdrawal->status]['msg'] = $oWithdrawal->remark.'<br />'.$sCsUrl;
                $aSteps[$iStep]['msg'] = $sCsUrl;
            }
            if ($iStatus == Withdrawal::WITHDRAWAL_STATUS_REFUSE) {
                $aSteps[$iStep]['msg'] = $oWithdrawal->error_msg . '<br />' . $sCsUrl;
            }
            if ($iStatus == Withdrawal::WITHDRAWAL_STATUS_FAIL) {
                $aSteps[$iStep]['msg'] = $sCsUrl;
            }
        }
        $this->setVars(compact('aStep', 'aStatus', 'aStepLab', 'aSteps', 'iCurrentStep', 'oWithdrawal'));
        return $this->render();
    }

}
