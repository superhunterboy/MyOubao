<?php
class McOrderController extends AdminBaseController
{
//    protected $customViewPath = 'admin.mcorder';
    
//    protected $customViews = [
//        'index', 'view'
//    ];
    
    /**
     * 资源模型名称
     * @var string
    */
    protected $modelName = 'McOrder';
    
    public function verifyWithdrawInfo()
    {
        if(isset($_POST['company_order_num']) && !empty($_POST['company_order_num']))
        {
            $data = trimArray(Input::all());
            //test data
//             $data = [
//                 'company_order_num'     => 'EA13668545115840',
//                 'mownecum_order_num'    => 'MC_EA13668545115840',
//                 'amount'                => '222.00',
//                 'card_num'              => '8888154522221414',
//                 'card_name'             => '王显',
//                 'company_user'          => '1234',
//                 'key'                   => 'bccde8cabd0215f4f785f99c3e723a1a'
                
//             ];
//             $data = json_decode($data['content'],true);
            
            $company_order_num      = array_get($data, 'company_order_num');
            $mownecum_order_num     = array_get($data, 'mownecum_order_num');
            $amount                 = array_get($data, 'amount');
            $card_num               = array_get($data, 'card_num');
            $card_name              = array_get($data, 'card_name');
            $company_user           = array_get($data, 'company_user');
            $key                    = array_get($data, 'key');
            
            $i_origin_key = McOrder::_getKey($data, McOrder::WITHDRAW_APPROVE);
            
            $msg = '';
            $o_mc_order = new McOrder();
            $status = $o_mc_order->getObjectByParams(['mownecum_order_num' => $mownecum_order_num])->getAttribute('status');
            
            //密钥不匹配
            if($key != $i_origin_key)
            {
                $status = 9;
                $msg    = "error key is not compare!";
                
            }
            
            if( empty($status) && $status !=0 )
            {
                $status = 5;
                $msg    = "cannot find this record";
            }

            $array = array(
                "error_msg"             =>  $msg,
                "mownecum_order_num"    =>  $mownecum_order_num,
                "company_order_num"     =>  $company_order_num,
                "status"                =>  $status
            );
            
            $this->jsonEcho($array);
            
        }else{
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
    
    /**
     * 提现确认接口
     */
    public function verifyWithdrawResult()
    {
        $request = new Request();
        if(isset($_POST['company_order_num']) && !empty($_POST['company_order_num']))
        {
            $data = trimArray(Input::all());
            //             $data = json_decode($data['content'],true);

            //test data
//             $data = [
//                 'company_order_num'     => 'EA13668545115840',
//                 'mownecum_order_num'    => 'MC_EA13668545115840',
//                 'amount'                => '222.00',
//                 'detail'                => 'wori',
//                 'exact_transaction_charge'=> '2',
//                 'status'                => '1',
//                 'key'                   => '0f9a871c7243c4ff6fef6807c1934758'
            
//             ];
        
            $company_order_num          = $data['company_order_num'];
            $mownecum_order_num         = $data['mownecum_order_num'];
            $mc_amount                  = $data['amount'];
            $detail                     = $data['detail'];
            $key                        = $data['key'];
            $mc_status                  = $data['status'];
            $exact_transaction_charge   = $data['exact_transaction_charge'];
        
            $i_origin_key = McOrder::_getKey($data, McOrder::WITHDRAW_RESULT_APPROVE);
//             echo $i_origin_key;die;
        
            $msg = '';
            $o_mc_order = new McOrder();
            $o_current_mcorder  =   $o_mc_order->getObjectByParams(['mownecum_order_num' => $mownecum_order_num]);
            $status             =   $o_current_mcorder->getAttribute('status');
            $withdrawID         =   $o_current_mcorder->getAttribute('withdrawal_id');
            $amount             =   $o_current_mcorder->getAttribute('amount');
            
            $o_current_withdraw = Withdrawal::find($withdrawID);
            $user_id            = $o_current_withdraw->getAttribute('user_id');

            $return = array(
                "error_msg"             =>  "",
                "mownecum_order_num"    =>  $mownecum_order_num,
                "company_order_num"     =>  $company_order_num,
                "status"                =>  ""
            );
            
            //密钥不匹配
            if($key != $i_origin_key)
            {
                $return['error_msg']    = "error key is not compare!";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
        
            //找不到记录
            if( empty($status) && $status != 0 )
            {
                $return['error_msg']    = "cannot find this record";
                $return['status']       = 0;
                $this->jsonEcho($return);
            }
            
            if(in_array($mc_status, array(1,2,0)))  // 处理成功，部分成功，未处理
            {
                $mc_status == 2 ? $part_pay = TRUE : $part_pay = FALSE;
                if($mc_status == 0)  $mc_amount=0;
                //事务提交用户加款
                DB::connection()->beginTransaction();
                $b_deduct_result    = $o_mc_order->deductUserFund($user_id,$mc_amount,$amount,$part_pay);
                DB::connection()->commit();
                if ($b_deduct_result)
                {
                  $return['status'] = 1;
                  Withdrawal::find($withdrawID)->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_SUCCESS]);
                  $this->jsonEcho($return);  
                }else {
                  $return['status'] = 0;
                  Withdrawal::find($withdrawID)->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_DEDUCT_FAIL]);
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
        header('Content-Type: application/json');
        echo json_encode($msg);
        exit();
    }
}