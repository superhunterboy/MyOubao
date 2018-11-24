<?php

class YoufuController extends Controller {
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
        $data = array(
	'mer_no' => Input::get('mer_no'),
	'order_no' => Input::get('order_no'),
	'order_amount' => Input::get('order_amount'),
	'trade_no' => Input::get('trade_no'),
	'trade_time' => Input::get('trade_time'),
	'trade_status' => Input::get('trade_status'),
	'trade_params' => Input::get('trade_params')
        );
        file_put_contents("/tmp/youfuReturnParams", json_encode($data),FILE_APPEND);
        ksort($data);
        $sign = Input::get('sign');
        $url = '';
        foreach($data as $pkey => $pval){
                $url .= $pkey.'='.$pval.'&';
        }
        $hmacstr = $url . 'KEY=' . SysConfig::readValue('YOUFU_MER_KEY');
        $_sign= md5($hmacstr);
        
        if ($_sign == $sign){
            if ($data['trade_status'] == "success"){
                 $tradeResult = "支付成功！";
                 
                 $oDeposit = Deposit::where('company_order_num', $data['trade_no'])->first();
                if(!$oDeposit){
                   $tradeResult = '订单不存在';
                   return ;
                }
                if($data['mer_no'] != Sysconfig::readValue('YOUFU_MER_NO') || $oDeposit->amount != $data['order_amount']){
                    $tradeResult = '不合法数据';
                     return ;
                }
                if(Input::get('order_amount') != $oDeposit->amount){
                     $tradeResult = '不合法数据';
                     return ;
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
                            'pay_time' =>date('Y-m-d H:i:s', $data['trade_time']),
                        ]);
                        // 更新订单状态
                        $bSuccessful = $oDeposit->setSuccess([
                            'real_amount' => $oDeposit->amount,
                            'fee' => 0,
                            'pay_time' => date('Y-m-d H:i:s', $data['trade_time']),
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
                        DB::connection()->rollback();
                        Account::unlock($oUser->account_id, $iLocker);
                        $oDeposit->setAddFail();
                    }
                    DB::connection()->commit();
                    Account::unlock($oUser->account_id, $iLocker);
                 }else{
                      $tradeResult = '订单已处理';
                 }
            }else{
                 $tradeResult = "支付失败";
            }
            
        }else{
           $tradeResult = "不合法数据"; 
        }
//        return Redirect::route('user-recharges.index')->with('error', $tradeResult);
          file_put_contents('/tmp/youfu_deposit', 'response data:'.json_encode(Input::all())."\n\r", FILE_APPEND);
    }

}