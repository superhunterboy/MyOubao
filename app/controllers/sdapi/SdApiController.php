<?php
class SdApiController extends Controller
{
    //0未处理，1正在处理，2成功，3失败，4其他
    public static $sd_withdrawal_result_status = [
        "0"     =>  Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
        "1"     =>  Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
        "2"     =>  Withdrawal::WITHDRAWAL_STATUS_SUCCESS,
        "3"     =>  Withdrawal::WITHDRAWAL_STATUS_FAIL,
        "4"     =>  Withdrawal::WITHDRAWAL_STATUS_MC_ERROR_RETURN,
    ];

    /**
     * 资源模型名称
     * @var string
     */
    protected $modelName = 'SdOrder';

    public function saveAllCallBacks($data)
    {
        $a_call_backs['call_url']               = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $a_call_backs['post_data']              = json_encode($data);
        $a_call_backs['mownecum_order_num']     = array_get($data, 'Id');
        $a_call_backs['company_order_num']      = array_get($data, 'SerialNumber');
        $a_call_backs['status']                 = array_get($data, 'RecordsState');
        $a_call_backs['amount']                 = array_get($data, 'IntoAmount');
        WithdrawalCallback::createCallback($a_call_backs);
    }

    /*
     * 提现订单查询
     */
    public function verifyWithdrawSearch($order)
    {
        //todo
    }


    /**
     * 提现确认接口
     */
    public function verifyWithdrawResult()
    {
//        $request = new Request();

        $inputAll = trimArray(Input::all());

        file_put_contents('/tmp/sd_api_verifyWithdrawResult'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($inputAll)."\r\n",FILE_APPEND);

        if(!isset($inputAll['HiddenField1']) || empty($inputAll['HiddenField1'])){
            $return['error_msg']    = "HiddenField1 is null!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }

        $SdOrder = new SdOrder();
        if(false == $data = $SdOrder->decryptData($inputAll['HiddenField1'])){
            $return['error_msg']    = "decryptData is fail!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }

        $rNum = WithdrawalCallback::where('post_data','=',$data)->count();
        if($rNum){
            $return['status'] =1;
            $return['error_msg']    = "don't send me repeated request in such a short time..";
            $this->jsonEcho($return);
        }
        $data = $SdOrder->compileReposeData($data);

        //-------------------------测试数据-----------------------------
        /*        $data = array(
                    "serverid"        => "LGV20150918014081825747",
                    "IntoAmount"      =>"123",
                    "RecordsState"    =>"2",
                    "SerialNumber"    =>"F918587454843402",
                );*/
        //--------------------------测试数据结束-------------------------

        $this->saveAllCallBacks($data);

        $sd_order_num         = array_get($data, 'Id');
        $sd_amount            = floatval(array_get($data, 'IntoAmount', 0));
        $sd_status            = array_get($data, 'RecordsState'); //0未处理，1正在处理，2成功，3失败，4其他
        $sd_serialNumber      = array_get($data, 'SerialNumber');

        $oSdOrder  =   SdOrder::getObjectByParams(['sd_order_num' => $sd_order_num]);
        $oWithdrawal  =   Withdrawal::getObjectByParams(['serial_number' => $sd_serialNumber]);

        if(!is_object($oSdOrder) || !is_object($oWithdrawal))
        {
            $return['error_msg']    = "sd_order_num or serial_number cannot find!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }

        $return = array(
            "error_msg"             =>  "",
            "sd_order_num"    =>  $sd_order_num,
            "company_order_num"     =>  $sd_serialNumber,
            "status"                =>  ""
        );

        $checkStatus = [
            Withdrawal::WITHDRAWAL_STATUS_NEW,
            Withdrawal::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM,
            Withdrawal::WITHDRAWAL_STATUS_REFUSE,
            Withdrawal::WITHDRAWAL_STATUS_SUCCESS,
            Withdrawal::WITHDRAWAL_STATUS_FAIL,
            Withdrawal::WITHDRAWAL_STATUS_DEDUCT_FAIL,
            Withdrawal::WITHDRAWAL_STATUS_PART,
            Withdrawal::WITHDRAWAL_STATUS_REFUND,
        ];

        if (in_array($oWithdrawal->status, $checkStatus))
        {
            $return['error_msg']    = "status check failed!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }

        if($sd_amount != $oWithdrawal->amount)
        {
            $return['error_msg']    = "SDPay return amount does not equal apply amount!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }

        if(in_array($sd_status, array_keys(self::$sd_withdrawal_result_status)))
        {
            $withdrawal_status = self::$sd_withdrawal_result_status[$sd_status];

            if($withdrawal_status != Withdrawal::WITHDRAWAL_STATUS_SUCCESS){
                $sd_amount = 0;
                $return['status'] = 0;
                $return['error_msg']    = "RecordsState can not deal";
            }else{
                $return['status'] = 1;
            }
            $log = $return;
            $aUpdate = ['status'=>$withdrawal_status,'mc_confirm_time'=>date('Y-m-d H:i:s'),'transaction_amount'=>$sd_amount];

            //只有在成功或者失败的情况下才解冻
            if(in_array($withdrawal_status, [Withdrawal::WITHDRAWAL_STATUS_SUCCESS, Withdrawal::WITHDRAWAL_STATUS_FAIL]) )
            {
                $sd_status == 1 ?  $part_pay = TRUE : $part_pay = FALSE;

                $user_id      = $oWithdrawal->getAttribute('user_id');
                $o_account    = Account::getAccountInfoByUserId($user_id);
                $account_id   = $o_account->id;

                Account::lock($account_id,$iLocker);

                DB::connection()->beginTransaction();

                if($oSdOrder->deductUserFund($user_id,$sd_amount,$oWithdrawal->amount,$part_pay) && $oWithdrawal->update($aUpdate)){
                    DB::connection()->commit();
                }else{
                    $log['error_msg'] = "Deduct User Fund failed";
                    DB::connection()->rollback();
                }
                Account::unLock($account_id,$iLocker,false);
            }
            else{
                $oWithdrawal->update($aUpdate);
            }

            file_put_contents('/tmp/sd_api_verifyWithdrawResult'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($log)."\r\n",FILE_APPEND);
            $this->jsonEcho($return);

        }else {
            $return['error_msg']    = "invaild status";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }
    }

    /**
     * 输出Json数据
     * @param array $msg
     */
    public function jsonEcho($msg)
    {
        file_put_contents('/tmp/sd_api_response'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($msg)."\r\n",FILE_APPEND);
        //header('Content-Type: application/json');
        $result = $msg['status'] ? 'Success' : 'Fail';
        echo '<html><body><span id="resultLable" >'.$result.'</span></body></html>';
//        echo json_encode($msg);
        exit();
    }
}
