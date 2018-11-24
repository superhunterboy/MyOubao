<?php
class McOrder extends BaseModel
{

    protected $table = 'mc_orders';

    public $iStatus;

    public $iResponseStatus;

    public $iUserID;

    public $iCurrentWithdrawID;

    protected $fillable = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'mownecum_order_num',
        'detail',
        'request_time',
        'response_time',
        'mc_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $columnForList = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'mownecum_order_num',
        'detail',
        'request_time',
        'response_time',
        'mc_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $mc_return_list = [
        '0'     =>  'Handle failed',
        '1'     =>  'Success',
        '2'     =>  'Partly Success',
        '3'     =>  'Not handle',
        '4'     =>  'Processing',
        '5'     =>  'Invaild Order',
    ];

    public static $rules = [
//         'id'                    => 'required|numeric',
//         'withdrawal_id'         => 'required|numeric',
//         'mownecum_order_num'    => 'max:50',
//         'mc_status'             => 'numeric',
//         'status'                => 'numeric',
//         'refund_type'           => 'numeric',
//         'amount'                => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
    ];

    /**
     * 充值请求
     * @var int
     */
    const DEPOSIT_REQUEST = 1;

    /**
     * 充值响应
     * @var int
     */
    const DEPOSIT_RESPONSE = 2;

    /**
     * 充值确认
     * @var int
     */
    const DEPOSIT_APPROVE = 3;

    /**
     * 异常推送
     * @var int
     */
    const EXCEPTION_PUSH = 4;

    /**
     * 提现请求
     * @var int
     */
    const WITHDRAW_REQUEST = 5;

    /**
     * 提现状态确认
     * @var int
     */
    const WITHDRAW_APPROVE = 6;

    /**
     * 提现结果确认
     * @var int
     */
    const WITHDRAW_RESULT_APPROVE = 7;

    //提现状态
    const WITHDRAWAL_ORDER_STATUS_NEW		= 0;
    const WITHDRAWAL_ORDER_STATUS_SUBMITED  = 1;
    const WITHDRAWAL_ORDER_STATUS_BACKED	= 2;
    const WITHDRAWAL_ORDER_STATUS_SUCCESS	= 3;
    const WITHDRAWAL_ORDER_STATUS_PART		= 4;
    const WITHDRAWAL_ORDER_STATUS_FAIL		= 5;

    const WITHDRAWAL_ORDER_RETURN_ERROR     = 10;
    const WITHDRAWAL_ORDER_PROCESSING       = 9;



    //MC接口返回状态
    const MC_RESPONSE_STATUS_SUCCESS		= 1;
    const MC_RESPONSE_STATUS_FAIL			= 0;


    public $ValidStatuses=array(
        self::WITHDRAWAL_ORDER_STATUS_NEW 	   => '待提交',
        self::WITHDRAWAL_ORDER_STATUS_SUBMITED   => '已提交',
        self::WITHDRAWAL_ORDER_STATUS_BACKED	   => '已返回',
        self::WITHDRAWAL_ORDER_STATUS_SUCCESS    => '成功',
        self::WITHDRAWAL_ORDER_STATUS_PART	   => '部分处理',
        self::WITHDRAWAL_ORDER_STATUS_FAIL  	   => '失败'
    );

    public $ValidResponseStatuses=array(
        self::MC_RESPONSE_STATUS_SUCCESS	   => '成功',
        self::MC_RESPONSE_STATUS_FAIL	 	   => '失败'
    );

    /**
     * 返回MC订单记录列表
     * @param $iWithdrawalId
     * @return Array
     */
    public function getMcOrderList( $iWithdrawalId = NULL)
    {
        if(empty($iWithdrawalId))
        {
            Withdrawal::where("withdrawal_id",'=',$iWithdrawalId);
        }

        $iCurrentStatus = $this->iStatus;
        self::where('status','=',$iCurrentStatus);

        return McOrder::get();
    }

    /**
     * 写入MC订单信息
     */
    public function saveMcOrder()
    {
        $aData=array(
            'withdrawal_id'=>$iWithdrawalId,
            'request_time'=>1,
            'company_order_num 	'=>1,
            'amount'=>0,

        );
        $this->oDB->insert($this->table, $aData);
        return $this->oDB->ar();
    }

    public function changeRecordToMcOrder()
    {
        $aNeedDearlingRecord = $this->getMcOrderList();
    }

    /**
     * 根据this->id取得指定的订单信息
     * @return Array
     */
    private function _getOrder()
    {
       if(empty($this->iStatus))
       {
           self::where("withdrawal_id",'=',$iWithdrawalId);
           return self::get();
       }
       return array();
    }

    /**
     * 根据MC接口的返回，更新指定订单的的状态
     * 如果$aExtraData为非空数组，则同时更新这个数组内的字段
     * @$iToStatus int 		新状态值
     * @$iFromStatus int 	旧状态值
     * @$aExtraData array 	须同时更新的字段数组
     * @return bool
     */
    private function _updateReponseStatus($iToStatus, $iFromStatus, $aExtraData =array())
    {
        $aData=array(
            'mc_status'=>$iToStatus,
        );
        if(!empty($aExtraData) && is_array($aExtraData))
        {
            $aExtraData=array(
                'detail'=>'',
                'response_time'=>1,
                'status'=>0,
                'updateed'=>'now()'
            );
            $aData=array_merge($aData,$aExtraData);
        }
        $sWhere='id='.$this->id.' AND mc_status='.$iFromStatus;
        $this->oDB->update($this->table, $aData,$sWhere);
        return $this->oDB->affectedRows()===1;
    }

    /**
     * 根据MC接口的返回，更新withdrawals表中提现申请记录的状态
     * 如果$aExtraData为非空数组，则同时更新这个数组内的字段
     * @$iToStatus int 		新状态值
     * @$iFromStatus int 	旧状态值
     * @$aExtraData array 	须同时更新的字段数组
     * @return bool
     */
    private function _updateStatus($iToStatus, $iFromStatus, $aExtraData =array())
    {
        $aData=array(
            'status'=>$iToStatus,
        );
        if(!empty($aExtraData) && is_array($aExtraData))
        {
            $aExtraData=array(
                'verified_time'=>'',
                'transaction_charge'=>1,
                'transaction_amount'=>0,
                'updated'=>'now()'
            );
            $aData=array_merge($aData,$aExtraData);
        }
        $sWhere='id='.$this->id.' AND status='.$iFromStatus;
        $this->oDB->update('withdrawals', $aData,$sWhere);
        return $this->oDB->affectedRows()===1;
    }

    /**
     * 生成用户唯一标识
     * @param int $iUserId
     * @return string
     */
    protected function _getUserFlag($iUserId)
    {
        $iUserId = intval($this->iUserID) > 0 ? intval($this->iUserID) : 0;
        // $sRange = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $sRange = 'GqNbzewIF6kfx5mYaAnBEUvMuJyH8o9D7XcWt0hiQKOgRLdlSPpsC2jZ143rTV'; // 使用乱序字串
        if($iUserId == 0)
        {
            return $sRange[0];
        }
        $iLength = strlen($sRange);
        $sStr = ''; // 最终生成的字串
        while ($iUserId > 0)
        {
            $sStr = $sRange[$iUserId % $iLength]. $sStr;
            $iUserId = floor($iUserId / $iLength);
        }
        return $sStr;
    }

    /**
     * CLi向MC发起审核用户订单创建
     * @param array $aWithdraw 提现记录列表数组
     * @return status list
     */
    public function _doWithdrawOrderCliProcess($aWithdraw = array())
    {
        
        if(empty($aWithdraw))
        {
            // echo "cannot find withdraw !";
            return BaseTask::TASK_SUCCESS;
        }
        // echo "----------- DEBUG MODE------------ \n";

        if(empty($aWithdraw))
        {
            $this->iStatus = self::WITHDRAWAL_ORDER_STATUS_NEW;
            $aWithdraw = $this->getMcOrderList();
        }

        $forefather_ids = $aWithdraw['user_forefather_ids'];
        $VipList =  Config::get('user_vip.users');
        $fatherVipList = Config::get('user_vip.parent_id');
        $isVip=false;
        if(!empty($forefather_ids) && !empty($fatherVipList)){
            $forefather_ids = explode(',',$forefather_ids);
            if(in_array($aWithdraw['user_id'],$fatherVipList)){
                $isVip = true;
            }else if(!empty(array_intersect($forefather_ids,$fatherVipList))){
                $isVip = true;;
            }
        }
        if($isVip || (!empty($VipList) && in_array($aWithdraw['user_id'],$VipList))){
            $sWithdrawUrl       = SysConfig::readValue("mc_withdrawal_url_vip");
            $iCompanyId         = SysConfig::readValue("mc_company_id_vip");
        }else{
            $sWithdrawUrl       = SysConfig::readValue("mc_withdrawal_url");
            $iCompanyId         = SysConfig::readValue("mc_company_id");
        }

        $sMcKey             = SysConfig::readValue("mc_company_key");
        $min_amount         = SysConfig::readValue("withdraw_default_min_amount");
        $max_amount         = SysConfig::readValue("withdraw_default_max_amount");
        $sWithdrawCheckUrl  = "http://10.89.7.11/Mownecum_2_API_Live/QueryWithdrawal";//SysConfig::readValue('mc_withdrawal_url');  //mc_withdrawal_check



        $w = $aWithdraw;
       // file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '\r\n---begin--request-a-withdraw'.var_export($w,true),FILE_APPEND);
 
        //针对所有的提现记录列表循环进行操作
//         foreach ($aWithdraw as $w)
//         {
//             $oWithdraw	= new model_mownecum2_withdrawal($w['id']);
            $aWithdrawInfo = Withdrawal::find($w['id']);

            $check_status = array(2,9,10); //未处理和异常返回和处理中
            if(empty($aWithdrawInfo) && !in_array($aWithdrawInfo['status'], $check_status))
            {
                
                // echo "cannot find withdraw OR withdraw status not valid!";
                return BaseTask::TASK_SUCCESS;
                die;
            }

            //平台订单号
            $company_order_num					= $w['serial_number'];

            $orderData['withdrawal_id'] 		= $w['id'];
            $orderData['company_order_num'] 	= $company_order_num;
            $orderData['detail'] 				= $w['id'];
            $orderData['mownecum_order_num'] 	= $aWithdrawInfo['mownecum_order_num'];
            $orderData['request_time'] 			= date("Y-m-d H:i:s");
            $orderData['status'] 				= self::WITHDRAWAL_ORDER_STATUS_NEW;
            $orderData['created'] 				= date("Y-m-d H:i:s");
            $orderData['amount'] 				= $w['amount'];
            $orderData['card_num'] 				= $w['account'];
            $orderData['card_name'] 			= $w['account_name'];

            $oUser = UserUser::find($w['user_id']);
            $orderData['company_user'] 			= $oUser->user_flag;

            //MC 订单ID
            $iWithdrawId = $w['id'];
            // echo "-- 处理的订单ID $iWithdrawId : Status ". $aWithdrawInfo['status']. "--\n";


            //插入初始化的订单表
            $oMcOrder = new McOrder();
            $oMcOrder->fill($orderData);
            $oMcOrder->save();

            //针对订单失败的情况
            if( in_array($aWithdrawInfo['status'], array(10)) )
            {

                $orderCheckData['company_id']			= $iCompanyId;
                $orderCheckData['company_order_num']	= $company_order_num;
                $orderCheckData['mownecum_order_num']	= $aWithdrawInfo['mownecum_order_num'];
                $orderCheckData['key']					= $this->_getKey($orderData, self::WITHDRAW_APPROVE);


   // file_put_contents("/tmp/mc_exception_orders_".date("Y-m-d"), '\r\n postdata---------------['.date('H:i:s').']'. var_export($orderCheckData,true),FILE_APPEND);
                $oCurl = new MyCurl($sWithdrawCheckUrl);
                $oCurl->setPost($orderCheckData);
                $oCurl->createCurl();
                $oCurl->execute();
                $aCheckResult = $oCurl->__tostring();
     //           file_put_contents("/tmp/mc_exception_orders_".date("Y-m-d"),  '['.$oCurl->getHttpStatus().']\r\nbackdata--------------['.date("H:i:s").']'.var_export($aCheckResult,true),FILE_APPEND);

                
                if($aCheckResult != "")
                {
                    $re = json_decode($aCheckResult,true);
                    // echo "--  进入失败订单查询,返回结果如下:  ---\n";
                    // var_dump($re);
                    // echo "-------------------------------------\n";
                    if( isset($re['status']))
                    {

                        //更新订单表内容
                        $aWithdrawOrderData = array(
                            'response_time' 	=> date('Y-m-d H:i:s'),
                            'company_order_num'  => $aCheckResult['company_order_num'],
                            'mownecum_order_num' => $aCheckResult['mownecum_order_num'],
                            'status' => $aCheckResult['status'],
                        );
                        Withdrawal::find($iWithdrawId)->update($aWithdrawOrderData);
                        if(!in_array($re['status'], array(5,9)))
                        {
                            return BaseTask::TASK_SUCCESS;
                        }
                    }
                }
                return BaseTask::TASK_RESTORE;
                die;
            }


            //检测record记录表, 如果msterNum不存在则退出
            $postData=array();
            $o_bank = new Bank();
            $postData['bank_id']			= $o_bank->find($w['bank_id'])->getAttribute("mc_bank_id");
            $postData['company_id']			= $iCompanyId;
            $postData['amount']				= $w['amount'];
            $postData['card_num']			= $w['account'];
            $postData['card_name']			= $w['account_name'];
            $postData['company_user'] 		= $this->_getUserFlag($aWithdrawInfo['userid']);
            $postData['company_order_num']	= $company_order_num;
            $postData['key']				= $this->_getKey($postData, self::WITHDRAW_REQUEST);
            //Curl 请求
            $oCurl = new MyCurl($sWithdrawUrl);
            $oCurl->setPost($postData);
 
		//file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '--------------'.$sWithdrawUrl.'-------->postdata---------------- \r\n['.date("H:i:s").']',FILE_APPEND);

            //file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  var_export($postData,true),FILE_APPEND);
 
            $oCurl->createCurl();
            $oCurl->execute();
            $aApiRes = $oCurl->__tostring();

            $userid							= $w['user_id'];
            
            
	   // file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '---------------------->backdata \r\n ',FILE_APPEND);
                
                
            // echo "----------提现服务器返回数据-------------\n";
            // var_dump($sWithdrawUrl);
            // var_dump($postData);
            // var_dump($aApiRes);
            // echo "----------------------------------------\n";
            if($aApiRes != "")
            {

           // file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '['.date("H:i:s").']'.var_export($aApiRes,true),FILE_APPEND);
                //-------------------------测试数据-----------------------------
                $re = json_decode($aApiRes,true);
//                 $re = array(
//                     'mownecum_order_num'    =>  'MC_'.$company_order_num,
//                     'company_order_num'     =>  $company_order_num,
//                     'status'                =>  5
//                 );
                //--------------------------测试数据结束-------------------------

                if( isset($re['status']) && $re['status']==1 )
                {
                    // 3更新mc_witharawal_orders已插入记录
                    $aWithdrawOrderData = array(
                        'response_time' 	=> date('Y-m-d H:i:s'),
                        'company_order_num'  => $re['company_order_num'],
                        'mownecum_order_num' => $re['mownecum_order_num'],
                        'status' => 4,
                    );

                    $aWithdrawData = array(
                        'mownecum_order_num'    => $re['mownecum_order_num'],
                        'mc_request_time'       => date('Y-m-d H:i:s'),
                    );
                    $oMcOrder->update($aWithdrawOrderData);

                    // 3更新withdrawals已插入记录
//                     Withdrawal::find($iWithdrawId)->setToSuccess();
                    Withdrawal::find($iWithdrawId)->update($aWithdrawData);
                    return BaseTask::TASK_SUCCESS;
                }

                    $error_msg=isset($re['error_msg'])?$re['error_msg']:'';
                if(!isset($re['status']))
                {
                    $status = self::WITHDRAWAL_ORDER_RETURN_ERROR;
                    Withdrawal::find($iWithdrawId)->update(['status'=>$status,'error_msg'=>$error_msg,'mc_request_time'=>date('Y-m-d H:i:s')]);
                    // echo "0x001 : 异常没有返回status withdrawID: $iWithdrawId\n";
                    return BaseTask::TASK_SUCCESS;
                }
                if($re['status'] == 0)
                {
//                    Withdrawal::find($iWithdrawId)->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL,'mc_request_time'=>date('Y-m-d H:i:s')]);
                    Withdrawal::find($iWithdrawId)->update(['status'=>Withdrawal::WITHDRAWAL_STATUS_VERIFIED,'error_msg'=>$error_msg,'mc_request_time'=>date('Y-m-d H:i:s')]);
                    
                }
                $aWithdrawOrderData = array(
                    'response_time' 	=> date('Y-m-d H:i:s'),
                    'status' => $re['status'],
                );
                $oMcOrder->update($aWithdrawOrderData);
              //  file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '\r\n---end--request-a-withdraw',FILE_APPEND);

                return BaseTask::TASK_SUCCESS;
                die;
            }
            else {
	//	file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"), '['.date("H:i:s").']'.'no-data back from mc',FILE_APPEND);

                // echo "0x002 : MC创建订单没有返回 withdrawID: $iWithdrawId\n";
                $aWithdrawOrderData = array(
                    'response_time' 	=> date('Y-m-d H:i:s'),
                    'status' => self::WITHDRAWAL_ORDER_RETURN_ERROR,
                );
                $oMcOrder->update($aWithdrawOrderData);
               // Withdrawal::find($iWithdrawId)->update(['status'=>self::WITHDRAWAL_ORDER_RETURN_ERROR]);
          //      file_put_contents("/tmp/mc_request_backdata_".date("Y-m-d"),  '\r\n---end--request-a-withdraw',FILE_APPEND);
            
                return BaseTask::TASK_RESTORE;
                die;
            }
//      }

    }


    /**
     * 生成通讯密钥
     * @param array $aPostData 通信数据包
     * @param int   $iType 通信类型
     * @return string | FALSE
     */
    public static  function _getKey($aPostData, $iType) {
        if(!is_array($aPostData))
        {
            return FALSE;
        }
        switch ($iType) {
            case self::WITHDRAW_REQUEST: //提现申请
                $sDataStr = array_get($aPostData,'company_id') . array_get($aPostData,'bank_id') . array_get($aPostData,'company_order_num') . array_get($aPostData,'amount')
                . array_get($aPostData,'card_num') . array_get($aPostData,'card_name') . array_get($aPostData,'company_user') . array_get($aPostData,'issue_bank_name')
                . array_get($aPostData,'issue_bank_address') . array_get($aPostData,'memo');
                break;
            case self::WITHDRAW_APPROVE: //提现信息确认
                $sDataStr = array_get($aPostData,'company_order_num') . array_get($aPostData,'mownecum_order_num') . array_get($aPostData,'amount')
                . array_get($aPostData,'card_num') . array_get($aPostData,'card_name') . array_get($aPostData,'company_user');
                break;
            case self::WITHDRAW_RESULT_APPROVE: //提现结果确认
                $sDataStr = array_get($aPostData,'mownecum_order_num') . array_get($aPostData,'company_order_num') . array_get($aPostData,'status')
                . array_get($aPostData,'amount') . array_get($aPostData,'exact_transaction_charge');
                break;
            default :
                return FALSE;
        }
        $o_mc_order         = new Withdrawal();
        $o_current_withdrawal  =   $o_mc_order->getObjectByParams(['serial_number' => array_get($aPostData,'company_order_num')]);
        if($o_current_withdrawal){
            $user_id            = $o_current_withdrawal->getAttribute('user_id');
            $forefather_ids =$o_current_withdrawal->getAttribute('user_forefather_ids');

            $VipList =  Config::get('user_vip.users');
            $fatherVipList = Config::get('user_vip.parent_id');
            $isVip=false;
            if(!empty($forefather_ids) && !empty($fatherVipList)){
                $forefather_ids = explode(',',$forefather_ids);
                if(in_array($user_id,$fatherVipList)){
                    $isVip = true;
                }else if(!empty(array_intersect($forefather_ids,$fatherVipList))){
                    $isVip = true;;
                }
            }
            if(($isVip || !empty($VipList) && in_array($user_id,$VipList))){
                $sConfigStr = SysConfig::readValue("mc_company_key_vip");
            }else{
                $sConfigStr = SysConfig::readValue("mc_company_key");
            }
        }else{
            $sConfigStr = SysConfig::readValue("mc_company_key");
        }


        return md5(md5($sConfigStr) . $sDataStr);
    }

    public static  function _getKeyForVip($aPostData, $iType) {
        if(!is_array($aPostData))
        {
            return FALSE;
        }
        switch ($iType) {
            case self::WITHDRAW_REQUEST: //提现申请
                $sDataStr = array_get($aPostData,'company_id') . array_get($aPostData,'bank_id') . array_get($aPostData,'company_order_num') . array_get($aPostData,'amount')
                    . array_get($aPostData,'card_num') . array_get($aPostData,'card_name') . array_get($aPostData,'company_user') . array_get($aPostData,'issue_bank_name')
                    . array_get($aPostData,'issue_bank_address') . array_get($aPostData,'memo');
                break;
            case self::WITHDRAW_APPROVE: //提现信息确认
                $sDataStr = array_get($aPostData,'company_order_num') . array_get($aPostData,'mownecum_order_num') . array_get($aPostData,'amount')
                    . array_get($aPostData,'card_num') . array_get($aPostData,'card_name') . array_get($aPostData,'company_user');
                break;
            case self::WITHDRAW_RESULT_APPROVE: //提现结果确认
                $sDataStr = array_get($aPostData,'mownecum_order_num') . array_get($aPostData,'company_order_num') . array_get($aPostData,'status')
                    . array_get($aPostData,'amount') . array_get($aPostData,'exact_transaction_charge');
                break;
            default :
                return FALSE;
        }
        $sConfigStr = SysConfig::readValue("mc_company_key_vip");
        return md5(md5($sConfigStr) . $sDataStr);
    }

    
    public function deductUserFund($user_id,$mc_amount=0,$amount_freeze,$part_pay = FALSE)
    {
                //account ID
        $o_account    = Account::getAccountInfoByUserId($user_id);
        $account_id   = $o_account->id;
        $o_user = User::find($user_id);

        $return = false;
        if($mc_amount != 0 )
        {
            $iReturn_part           = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
            $iReturn                = Transaction::addTransaction($o_user,$o_account,TransactionType::TYPE_WITHDRAW,$mc_amount);
            $amount_need_frezze     = $amount_freeze;
            $iReturn_part   == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true :  $return = false;
            $iReturn        == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true : $return = false;
        }else{
            $iReturn1 = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
            $iReturn1 == Transaction::ERRNO_CREATE_SUCCESSFUL ? $return = true : $return = false;
        }

        return $return;
    }

}
