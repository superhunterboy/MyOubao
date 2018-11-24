<?php

class DepositApiController extends Controller {

    /**
     * 充值响应验证规则
     * @var array
     */
    private $approveApiRules = [
        'pay_time' => 'required|date',
        'bank_id' => ['required', 'integer'],
        'amount' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'company_order_num' => 'required|between:1,64',
        'mownecum_order_num' => 'required|between:1,64',
        'pay_card_num' => 'regex:/^[0-9*]{16,32}$/',
        'pay_card_name' => '',
        'channel' => '',
        'area' => '',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'transaction_charge' => 'required|regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'key' => 'required|between:32,32',
        'deposit_mode' => 'required|in:1,2,3',
    ];


    public function index() {
        // 接受请求
        $aRequest = Input::all();

        // $aRequest = [  // test data
        //     "pay_time" => "20150313153101",
        //     "bank_id" => "1",
        //     "amount" => "1200.00",
        //     "mownecum_order_num" => "BM2015031302006799507",
        //     "company_order_num" => "278176495502902a8582d",
        //     "pay_card_num" => "6212262010008236868",
        //     "pay_card_name" => "张超飞",
        //     "channel" => "个人网银",
        //     "area" => "河南洛阳分行业务处理中心（虚拟）",
        //     "fee" => "0.50",
        //     "transaction_charge" => "0.00",
        //     "key" => "3c71fa5662dd650903d9d95961c39d43",
        //     "deposit_mode" => "1",
        // ];
        // 数据检查
       if(!$this->verifyApproveData($aRequest)) {
           return $this->makeResponse('VERIFY ERROR');
       }
        // 记录推送数据
       $oBank = Bank::findBankByMcBankId($aRequest['bank_id']);
       $aInitData = [
           'pay_time' => date('Y-m-d H:i:s', strtotime(array_get($aRequest, 'pay_time'))),
           'bank_id' =>$oBank->id,
           'mc_bank_id' => array_get($aRequest, 'bank_id'),
           'amount' => array_get($aRequest, 'amount'),
           'mownecum_order_num' => array_get($aRequest, 'mownecum_order_num'),
            'company_order_num' => array_get($aRequest, 'company_order_num'),
            'pay_card_num' => array_get($aRequest, 'pay_card_num'),
            'pay_card_name' => array_get($aRequest, 'pay_card_name'),
            'channel' => array_get($aRequest, 'channel'),
            'area' => array_get($aRequest, 'area'),
            'fee' => array_get($aRequest, 'fee'),
            'transaction_charge' => array_get($aRequest, 'transaction_charge'),
            'deposit_mode' => array_get($aRequest, 'deposit_mode'),
           'key' => array_get($aRequest, 'key'),
       ];
       // 事务前保存所有推送信息
        $oDepositCallback = DepositCallback::createCallback($aInitData);
        if(!$oDepositCallback) {
            return $this->makeResponse('SERVICE INTERNAL ERROR');
        }
        $oDeposit = Deposit::findDepositByCompanyOrderNum( $aInitData['company_order_num'] );
        // 订单是否存在
        if(!$oDeposit) {
            $oDepositCallback->setResponseFailed('DEPOSIT NOT FOUND');
            return $this->makeResponse('', $oDepositCallback);
        }
        // 订单号是否匹配
        if($oDeposit->mownecum_order_num != $oDepositCallback->mownecum_order_num) {
            $oDepositCallback->setResponseFailed('ORDER NUMBER NOT MATCH');
            return $this->makeResponse('', $oDepositCallback);
        }
        // 订单是否在待处理状态
        if($oDeposit->status != Deposit::DEPOSIT_STATUS_RECEIVED) {
            $oDepositCallback->setResponseFailed('REPEAT RECORD ERROR');
            return $this->makeResponse('', $oDepositCallback);
        }
        // 手续费计算（根据用户发起银行为依据）
        $oUserApplyBank = Bank::find($oDeposit->bank_id);
        $fBankFee = 0;
        // 仅银行卡转账且该银行手续费开关打开时，才进行手续费返还操作
        if($oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_BANK_CARD && $oUserApplyBank->fee_switch == BANK::BANK_FEE_SWITCH_ON) { // 手续费返还开关
            $fBankFee = $oDepositCallback->fee; // 开启手续费开关时默认使用接口传回的手续费金额，如果为０则再通过公式计算
            if($fBankFee <= 0 && $oDepositCallback->amount >= $oUserApplyBank->fee_valve) {
                // 当接口手续费不存在且充值金额大于返还阀值，进行公式计算手续费
                $fBankFee = $oUserApplyBank->calculateBankFee($oDepositCallback->amount);
            }
        }
        $oUser = User::find($oDeposit->user_id);
        $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
        Account::lock($oUser->account_id, $iLocker);
        // 开启事务
        DB::connection()->beginTransaction();
        try{
            // 更新订单状态
            $bSuccessful = $oDeposit->setSuccess([
                'real_amount' => $oDepositCallback->amount,
                'fee' => $fBankFee,
                'pay_time' => $oDepositCallback->pay_time,
            ]);
            if(!$bSuccessful) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
            // 增加游戏币
            $dDepositDate = date('Y-m-d');
            $aExtraData = [
                'note' => $oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_THIRD_PART
                    ? Deposit::$aDepositMode[Deposit::DEPOSIT_MODE_THIRD_PART] : $oBank->name,
            ];
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT, $oDepositCallback->amount, $aExtraData);
            // 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL){
                throw new Exception('ADD COIN ERROR');
            }
            
            
            if($fBankFee > 0) {
                // 返还手续费
                $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT_FEE_BACK, $fBankFee);
                // 添加状态是否成功
                if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL){
                    throw new Exception('BANK CHARGE ERROR');
                }
            }
        } catch (Exception $e) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $oDeposit->setAddFail();
            $oDepositCallback->setResponseFailed($e->getMessage());
            return $this->makeResponse('', $oDepositCallback);
        }
        DB::connection()->commit();
        Account::unlock($oUser->account_id, $iLocker);
        if($oDeposit->bank_id == 47){ //暂时写死 47表示银联支付
            //扣手续费
            $this->addAccountFee($oDeposit, $oDepositCallback);
        }
//        Event::fire('bomao.auth.register', $oUser->id);
//        Queue::push('SignTaskQueue', ['task_id' => 7, 'user_id' => $oUser->id, 'activity_id' => 2, 'amount'=>$oDepositCallback->amount], 'activity');
//        Deposit::addProfitTask($dDepositDate, $oUser->id, $oDepositCallback->amount);
        $oDepositCallback->setResponseSuccessful();
        return $this->makeResponse('', $oDepositCallback);
    }


    /**
     * 验证充值确认推送数据
     * @param array $aRequest 得到的推送信息
     * @return boolean
     */
    private function verifyApproveData( array $aRequest ) {
        $validator = Validator::make($aRequest, $this->approveApiRules);
        if (!$validator->passes()) {
//            pr($validator->getMessageBag()->toArray());
            return false;
        }
        if(Deposit::getApiKey($aRequest, Deposit::DEPOSIT_API_APPROVE) != array_get($aRequest, 'key')) { // key error
//            echo 'key error';
            return false;
        }
        return true;
    }

     /**
     * 扣除充值手续费
     */
    private function addAccountFee($oDeposit, &$oSdpayDepositCallback) {
        $fBankFee = Bank::calculateSdpayBankFee($oSdpayDepositCallback->amount);
        if ($fBankFee <= 0) {
            //如果手续非为0  成功
            return true;
        }
        $oUser = User::find($oDeposit->user_id);
        $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
        Account::lock($oUser->account_id, $iLocker);
        DB::connection()->beginTransaction();
        try {
// 更新订单手续费状态
            $bSuccessful = $oDeposit->setDeductFeeSuccess([
                'fee' => $fBankFee,
            ]);

            if (!$bSuccessful) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
// 扣除手续费
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_WITHDRAW_FEE, $fBankFee);
// 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('BANK CHARGE ERROR');
            }
        } catch (Exception $e) {
            DB::connection()->rollback();
            Account::unlock($oUser->account_id, $iLocker);
            $oDeposit->setDeductFeeFail();
            $oSdpayDepositCallback->setResponseFailed($e->getMessage());
            return ['status' => '0', 'msg' => $e->getMessage()];
        }
        DB::connection()->commit();
        Account::unlock($oUser->account_id, $iLocker);



        return ['status' => '1', 'msg' => 'success'];
    }


    /**
     * 响应请求
     * @param type $sMessage 指定要反馈的信息
     * @param DepositCallback $oDepositCallback 提现推送对象
     * @return JsonResponse
     */
    private function makeResponse($sMessage = '', DepositCallback $oDepositCallback = null) {
        $aResponse = [];
        if(!$oDepositCallback) {
            $aResponse = [
                'company_order_num' => '',
                'mownecum_order_num' => '',
                'status' => DepositCallback::RESPONSE_STATUS_FAIL,
                'error_msg' => $sMessage,
                ];
        } else {
            is_null($oDepositCallback->error_msg) && $oDepositCallback->error_msg = '';
            $aResponse = [
                'company_order_num' => $oDepositCallback->company_order_num,
                'mownecum_order_num' => $oDepositCallback->mownecum_order_num,
                'status' => $oDepositCallback->status,
                'error_msg' => !empty($sMessage) ? $sMessage : $oDepositCallback->error_msg,
                ];
        }
        $oResponse = Response::json($aResponse);
        $this->writeLog(['url' => __CLASS__, 'date' => date('Y-m-d H:i:s'), 'request' => trimArray(Input::all()), 'response' => $oResponse->getContent()]);
        return $oResponse;
    }


    /**
     * 写充值日志
     * @param string|array $msg
     */
    protected function writeLog($msg){
        !is_array($msg) or $msg = var_export($msg,true);
        @file_put_contents('/tmp/deposit-bomao-'.date('Y-m-d'),$msg . "\n",FILE_APPEND);
    }
}