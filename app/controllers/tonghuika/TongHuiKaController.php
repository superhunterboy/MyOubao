<?php

class TongHuiKaController extends Controller {
    /**
     * 在渲染前执行，为视图准备变量
     */
    protected function beforeRender() {
        parent::beforeRender();

        switch ($this->action) {
            case 'index':
                $this->setVars();
                break;
        }

    }

    public function depositReturn(){

        $merchantCode = Input::get(AppConstants::$MERCHANT_CODE);
        $notifyType = Input::get(AppConstants::$NOTIFY_TYPE);
        $orderNo = Input::get(AppConstants::$ORDER_NO);
        $orderAmount = Input::get(AppConstants::$ORDER_AMOUNT);
        $orderTime = Input::get(AppConstants::$ORDER_TIME);
        $returnParams = Input::get(AppConstants::$RETURN_PARAMS);
        $tradeNo = Input::get(AppConstants::$TRADE_NO);
        $tradeTime = Input::get(AppConstants::$TRADE_TIME);
        $tradeStatus = Input::get(AppConstants::$TRADE_STATUS);
        $sign = Input::get(AppConstants::$SIGN);

        $kvs = new KeyValues();
        $kvs->add(AppConstants::$MERCHANT_CODE, $merchantCode);
        $kvs->add(AppConstants::$NOTIFY_TYPE, $notifyType);
        $kvs->add(AppConstants::$ORDER_NO, $orderNo);
        $kvs->add(AppConstants::$ORDER_AMOUNT, $orderAmount);
        $kvs->add(AppConstants::$ORDER_TIME, $orderTime);
        $kvs->add(AppConstants::$RETURN_PARAMS, $returnParams);
        $kvs->add(AppConstants::$TRADE_NO, $tradeNo);
        $kvs->add(AppConstants::$TRADE_TIME, $tradeTime);
        $kvs->add(AppConstants::$TRADE_STATUS, $tradeStatus);
        $_sign = $kvs->sign();
        $tradeResult = '';
        if ($_sign == $sign)
        {
            if ($tradeStatus == "success")
            {
                $tradeResult = "支付成功！";

                $oDeposit = Deposit::where('company_order_num', $orderNo)->first();
                $oSysconfig = new SysConfig();
                if($merchantCode != $oSysconfig::readValue('MER_NO') || $oDeposit->amount != $orderAmount){
                    file_put_contents('/tmp/tonghuika_deposit', 'tradeResult : 不合法数据, response data:'.json_encode(Input::all())."\n\r", FILE_APPEND);
                    return 0;
                }

                if(!$oDeposit){
                    file_put_contents('/tmp/tonghuika_deposit', 'tradeResult : 订单不存在, response data:'.json_encode(Input::all())."\n\r", FILE_APPEND);
                    return 0;
                }

                if($oDeposit->status == Deposit::DEPOSIT_STATUS_NEW){
                    $oUser = User::find($oDeposit->user_id);
                    $oAccount = Account::getAccountInfoByUserId($oDeposit->user_id);
                    Account::lock($oUser->account_id, $iLocker);
                    DB::connection()->beginTransaction();
                    try{
                        $bSuccessful = $oDeposit->setReceived([
                            'real_amount' => $oDeposit->amount,
                            'fee' => 0,
                            'pay_time' => $tradeTime,
                        ]);
                        // 更新订单状态
                        $bSuccessful = $oDeposit->setSuccess([
                            'real_amount' => $oDeposit->amount,
                            'fee' => 0,
                            'pay_time' => $tradeTime,
                        ]);


                        if(!$bSuccessful) { // 是否更新状态成功
                            throw new Exception('UPDATE STATUS ERROR');
                        }
                        // 增加游戏币
                        $dDepositDate = date('Y-m-d');
                        $aExtraData = [
                            'note' => $oDeposit->deposit_mode == Deposit::DEPOSIT_MODE_THIRD_PART
                                ? Deposit::$aDepositMode[Deposit::DEPOSIT_MODE_THIRD_PART] : '微信',
                        ];
                        $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT, $oDeposit->amount, $aExtraData);

                        // 添加状态是否成功
                        if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL){
                            throw new Exception('ADD COIN ERROR');
                        }
                    } catch (Exception $e) {
                        $tradeResult = $e->getMessage();
                        DB::connection()->rollback();
                        Account::unlock($oUser->account_id, $iLocker);
                        $oDeposit->setAddFail();
                    }
                    DB::connection()->commit();
                    Account::unlock($oUser->account_id, $iLocker);
                }else{
                    $tradeResult = '订单已处理';
                }
            }
            else
            {
                $tradeResult = "支付失败";
            }
        }
        else
        {
            $tradeResult = "不合法数据";
        }
        file_put_contents('/tmp/tonghuika_deposit', 'tradeResult : '.$tradeResult.'response data:'.json_encode(Input::all())."\n\r", FILE_APPEND);
        return 0;

    }


}
