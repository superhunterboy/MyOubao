<?php
class McApiController extends Controller
{
//    protected $customViewPath = 'admin.mcorder';
    
//    protected $customViews = [
//        'index', 'view'
//    ];
    
    public static $mc_withdrawal_result_status = [
        "1"     =>  Withdrawal::WITHDRAWAL_STATUS_SUCCESS,
        "2"     =>  Withdrawal::WITHDRAWAL_STATUS_PART,
        "0"     =>  Withdrawal::WITHDRAWAL_STATUS_FAIL,
    ];
    
    /**
     * 资源模型名称
     * @var string
    */
    protected $modelName = 'McOrder';
    
    public function saveAllCallBacks($data)
    {
        $a_call_backs['call_url']               = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
        $a_call_backs['post_data']              = json_encode($data);
        $a_call_backs['mownecum_order_num']     = array_get($data, 'mownecum_order_num');
        $a_call_backs['company_order_num']      = array_get($data, 'company_order_num');
        $a_call_backs['status']                 = array_get($data, 'status');
        $a_call_backs['amount']                 = array_get($data, 'amount');
        WithdrawalCallback::createCallback($a_call_backs);
    }
    
    public function verifyExcetionInfo($data)
    {
        $company_order_num      = array_get($data, 'company_order_num');
        $mownecum_order_num     = array_get($data, 'mownecum_order_num');
        $amount                 = array_get($data, 'amount');
        $card_num               = array_get($data, 'card_num');
        $card_name              = array_get($data, 'card_name');
        $company_user           = array_get($data, 'company_user');
        $key                    = array_get($data, 'key');
        
        $i_origin_key = McOrder::_getKey($data, McOrder::WITHDRAW_APPROVE);
        
        $msg = '';
        $o_excetion_Order       = new ExceptionDeposit();
        $o_current_exception    = $o_excetion_Order->getObjectByParams(['company_order_num' => $company_order_num]);
        $status                 = $o_current_exception->getAttribute('status');
        //状态：0未处理（默认），1申请成功，2申请失败，3挂起（申请时无响应），4退款成功，5退款失败，6已加币，7已没收
        switch($status)
        {
            case ExceptionDeposit::EXCEPTION_STATUS_RECEIVED:
                $return_status = 4;
                break;
            case ExceptionDeposit::EXCEPTION_STATUS_SUCCESS:
                $return_status = 1;
                break;
            case ExceptionDeposit::EXCEPTION_STATUS_FAIL:
                $return_status = 0;
                break;
            default:
                $return_status = 5;
                break;
        }
        
        //密钥不匹配
        if($key != $i_origin_key)
        {
            $return_status = 9;
            $msg    = "error key is not compare!";
        }
        
        $array = array(
            "error_msg"             =>  $msg,
            "mownecum_order_num"    =>  $mownecum_order_num,
            "company_order_num"     =>  $company_order_num,
            "status"                =>  $return_status
        );
        
        $this->jsonEcho($array);
        
    }
    
    public function verifyWithdrawInfo()
    {
         $data = trimArray(Input::all());
         file_put_contents('/tmp/bomao_mc_api_verifyWithdrawInfo'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($data)."\r\n",FILE_APPEND);
           
        if(isset($data['company_order_num']) && !empty($data['company_order_num']))
        {
            $mownecum_order_num     = array_get($data, 'mownecum_order_num');
            $company_order_num      = array_get($data, 'company_order_num');
//            $rNum = WithdrawalCallback::where('post_data','=',json_encode($data))->whereNull('status')->count();
//            if($rNum){
//                  $return['status'] =1;
//                  $return['error_msg']    = "we have already received your post data before.";
//                  $return["mownecum_order_num"]    =  $mownecum_order_num;
//                  $return["company_order_num"]     =  $company_order_num;
//                  $this->jsonEcho($return);
//           }

            
//            $this->saveAllCallBacks($data);
            
            if(substr($company_order_num, 0, 4) == 'EXCE') $this->verifyExcetionInfo($data);
           
         
            $amount                 = array_get($data, 'amount');
            $card_num               = array_get($data, 'card_num');
            $card_name              = array_get($data, 'card_name');
            $company_user           = array_get($data, 'company_user');
            $key                    = array_get($data, 'key');
            
            $i_origin_key = McOrder::_getKey($data, McOrder::WITHDRAW_APPROVE);
            
            $msg = '';
            $o_MC_Order = new Withdrawal();
            $o_current_withdraw = $o_MC_Order->getObjectByParams(['mownecum_order_num' => $mownecum_order_num]);
            
            
            //处理异常情况
            if( empty($o_current_withdraw) )
            {
                $return_status = 5;
                $msg    = "cannot find this record";
                $this->jsonEcho(array(
                    "error_msg"             =>  $msg,
                    "mownecum_order_num"    =>  $mownecum_order_num,
                    "company_order_num"     =>  $company_order_num,
                    "status"                =>  $return_status
                ));
            }
            
            $status = $o_current_withdraw->getAttribute('status');
            
//            if($status == Withdrawal::WITHDRAWAL_STATUS_VERIFIED) $return_status = 4;
            
            switch($status)
            {
                case Withdrawal::WITHDRAWAL_STATUS_VERIFIED:
                    $return_status = 4;
                    break;
                case Withdrawal::WITHDRAWAL_STATUS_SUCCESS:
                    $return_status = 1;
                    break;
                case Withdrawal::WITHDRAWAL_STATUS_FAIL:
                    $return_status = 0;
                    break;
                default:
                    $return_status = 5;
                    break;
            }
            //密钥不匹配
            if($key != $i_origin_key)
            {
                $return_status  = 9;
                $msg            = "error key is not compare!";
            }
            if($amount != $o_current_withdraw->getAttribute('amount'))
            {
                $return_status  = 0;
                $msg            = "Amount is not compare!";
            }
            
            $array = array(
                "error_msg"             =>  $msg,
                "mownecum_order_num"    =>  $mownecum_order_num,
                "company_order_num"     =>  $company_order_num,
                "status"                =>  $return_status
            );
            file_put_contents('/tmp/bomao_mc_api_verifyWithdrawInfo'.date("Y-m-d"),'backdata['.date("H:i:s").']'.json_encode($array)."\r\n",FILE_APPEND); 
            $this->jsonEcho($array);
            
        }else{
            file_put_contents('/tmp/bomao_mc_api_verifyWithdrawInfo'.date("Y-m-d"),'backdata['.date("H:i:s")."]error POST TYPE!\r\n",FILE_APPEND); 
            
            die("error POST TYPE!");
        }
    }
    
    /*
     * 提现订单查询
     */
    public function verifyWithdrawSearch($order)
    {
        if( empty($order['company_id']) || empty($order['company_order_num']) || empty($order['mownecum_order_num']) || empty($order['key']) )
        {
            return false;
        }
        
//        $oSysconfig = new SysConfig();
        $s_withdraw_order_url = SysConfig::readValue('withdraw_check_order');
        
        $oCurl = new MyCurl($s_withdraw_order_url);
        $oCurl->setPost($order);
        $oCurl->createCurl();
        $oCurl->execute();
        $aCheckResult = $oCurl->__tostring();
        
        if(!empty($aCheckResult))
        {
            $a_result = json_decode($aCheckResult);
            
            if (!empty($a_result) && is_array($a_result))
            {
                return $a_result;
            }
        }
        return false;
    }
    
    public function verifyExceptionResult($data)
    {
        
        $company_order_num          = array_get($data, 'company_order_num');
        $mownecum_order_num         = array_get($data, 'mownecum_order_num');
        $mc_amount                  = array_get($data, 'amount');
        $detail                     = array_get($data, 'detail');
        $key                        = array_get($data, 'key');
        $mc_status                  = array_get($data, 'status');
        $exact_transaction_charge   = array_get($data, 'exact_transaction_charge');
        
        $i_origin_key           = McOrder::_getKey($data, McOrder::WITHDRAW_RESULT_APPROVE);
        $o_exception            = new ExceptionDeposit();
        $o_current_withdrawal   = $o_exception->getObjectByParams(['company_order_num' => $company_order_num]);
        
        $return = array(
            "error_msg"             =>  "",
            "mownecum_order_num"    =>  $mownecum_order_num,
            "company_order_num"     =>  $company_order_num,
            "status"                =>  1
        );
        
        //密钥不匹配
        if($key != $i_origin_key)
        {
            $return['error_msg']    = "error key is not compare!";
            $return['status']       = 0;
            $this->jsonEcho($return);
        }
        
        switch ($mc_status)
        {
            case 0:
                $o_current_withdrawal->setFail() ? $return['status'] = 1 : $return['status'] = 0;
                break;
            case 1:
                $o_current_withdrawal->setSuccess() ? $return['status'] = 1 : $return['status'] = 0;
                break;
            default:
                break;
        }
        
        $this->jsonEcho($return);
    }
    
    /**
     * 提现确认接口
     */
    public function verifyWithdrawResult()
    {
        
//         $_POST = array(
//             "mownecum_order_num"        => "BM2014112601004916413",
//             "company_order_num"         =>"EB26730055819423",
//             "status"                    =>"1",
//             "detail"                    =>"您尾号为4737的e时代卡向 尾号为9429 的工行账户汇入1.00元 ， 交易流水号为030220114830103065384807996。",
//             "key"                       =>"d4afda480c10b17f37458ff4c5476a2a",
//             "amount"                    =>"1.00",
//             "exact_transaction_charge"  =>"0.00",
//         );
        $request = new Request();
        $data = trimArray(Input::all());
        
         file_put_contents('/tmp/mc_api_verifyWithdrawResult'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($data)."\r\n",FILE_APPEND);
       
        if(isset($data['company_order_num']) && !empty($data['company_order_num']))
        {
            
            $rNum = WithdrawalCallback::where('post_data','=',json_encode($data))->count();
            if($rNum){
                  $return['status'] =1;
                  $return['error_msg']    = "don't send me repeated request in such a short time..";
                  $this->jsonEcho($return);
            }
            $this->saveAllCallBacks($data);
        
            $company_order_num          = array_get($data, 'company_order_num');
            if(substr($company_order_num, 0, 4) == 'EXCE') $this->verifyExceptionResult($data);
            $mownecum_order_num         = array_get($data, 'mownecum_order_num');
            $mc_amount                  = array_get($data, 'amount', 0);
            $mc_transaction_charge                  = array_get($data, 'exact_transaction_charge', 0);
            $detail                     = array_get($data, 'detail');
            $key                        = array_get($data, 'key');
            $mc_status                  = array_get($data, 'status');
            $exact_transaction_charge   = array_get($data, 'exact_transaction_charge');
        
            $i_origin_key = McOrder::_getKey($data, McOrder::WITHDRAW_RESULT_APPROVE);

            $msg = '';
            $o_mc_order         = new Withdrawal();
            $o_current_mcorder  = new McOrder();
            $o_current_withdrawal  =   $o_mc_order->getObjectByParams(['mownecum_order_num' => $mownecum_order_num]);
            if(empty($o_current_withdrawal))
            {
                $return['error_msg']    = "mownecum_order_num cannot find!";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
            $status             =   $o_current_withdrawal->getAttribute('status');
            $withdrawID         =   $o_current_withdrawal->getAttribute('id');
            $amount             =   $o_current_withdrawal->getAttribute('amount');

            $return = array(
                "error_msg"             =>  "",
                "mownecum_order_num"    =>  $mownecum_order_num,
                "company_order_num"     =>  $company_order_num,
                "status"                =>  ""
            );
            
            //0: 待审核; 1: 客服待定; 2: 审核通过, 待处理; 3.未通过审核(审核拒绝); 4.成功; 5.失败; 6.扣游戏币异常失败; 7.mc部分成功, 扣减部分游戏币; 8.已退款(专用于审核拒绝情况的后续状态); 9.MC处理中. 10:MC异常返回
            if (in_array($status, array(0,1,3,4,5,6,7,8)))
            {
                $return['error_msg']    = "status check failed!";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
            
            $user_id            = $o_current_withdrawal->getAttribute('user_id');

            
            //密钥不匹配
            if($key != $i_origin_key)
            {
                $return['error_msg']    = "error key is not compare!";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
            
            if($mc_amount > $amount)
            {
                $return_status  = 0;
                $msg            = "Mc return amount bigger than apply amount!";
            }
        
            //找不到记录
            if( empty($status) && $status != 0 )
            {
                $return['error_msg']    = "cannot find this record";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
            
//            $mc_update = array("status"=>$mc_status);
//            $o_current_mcorder->update($mc_update);
            
            if(in_array($mc_status, array(1,2,0)))  // 处理成功，部分成功，未处理
            {
                $withdrawal_status = self::$mc_withdrawal_result_status[$mc_status];
                if($mc_status == 0)  $mc_amount=0;
                $mc_status == 2 ?  $part_pay = TRUE : $part_pay = FALSE;
                //事务提交用户加款
                
                $o_account    = Account::getAccountInfoByUserId($user_id);
                $account_id   = $o_account->id;
                $o_user = User::find($user_id);
                
                //TODO : 加锁失败加入队列处理
                 Account::lock($account_id,$iLocker);
//                if (empty($oAccount)){
                    
//                    $return['error_msg']    = "Add Lock failed!";
//                    $return['status']       = 0;
//                    $this->jsonEcho($return);
//                }
                DB::connection()->beginTransaction();
                $b_deduct_result    = $o_current_mcorder->deductUserFund($user_id,$mc_amount,$amount,$part_pay);
                $b_deduct_result ? DB::connection()->commit() : DB::connection()->rollback();
                Account::unLock($account_id,$iLocker,false);

                
                if ($b_deduct_result)
                {
//                  Queue::push('EventTaskQueue', ['event'=>'bomao.activity.withDrawal', 'user_id'=>$user_id, 'data'=>[]], 'activity');
                  $return['status'] = 1;
                  Withdrawal::find($withdrawID)->update(['status'=>$withdrawal_status,'mc_confirm_time'=>date('Y-m-d H:i:s'),'transaction_amount'=>$mc_amount, 'transaction_charge'=>$mc_transaction_charge]);
                  Withdrawal::addProfitTask(date('Y-m-d'), $user_id, $mc_amount);
                  file_put_contents('/tmp/mc_api_verifyWithdrawResult'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($return)."\r\n",FILE_APPEND);
       
                  $this->jsonEcho($return);  
                }else {
                  $return['status'] = 0;
                  Withdrawal::find($withdrawID)->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_FAIL,'mc_confirm_time'=>date('Y-m-d H:i:s')]);
                  $return['error_msg']    = "Deduct User Fund failed";
                  $this->jsonEcho($return);
                }
            }else {
                $return['error_msg']    = "invaild status";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
        
        }else{
            die("error POST TYPE!");
        }
    }
    
    /**
     * 输出Json数据
     * @param array $msg
     */
    public function jsonEcho($msg)
    {
        file_put_contents('/tmp/bomao_mc_api_response'.date("Y-m-d"), '['.date("H:i:s").']'.json_encode($msg)."\r\n",FILE_APPEND);
        header('Content-Type: application/json');
        echo json_encode($msg);
        exit();
    }
}
