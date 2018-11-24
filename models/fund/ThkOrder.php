<?php
class ThkOrder extends BaseModel
{

    protected $table = 'thk_orders';

    protected $fillable = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'tonghuika_order_num',
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
        'tonghuika_order_num',
        'detail',
        'request_time',
        'response_time',
        'mc_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $rules = [

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


    public $ValidStatuses=array(
        self::WITHDRAWAL_ORDER_STATUS_NEW 	   => '待提交',
        self::WITHDRAWAL_ORDER_STATUS_SUBMITED   => '已提交',
        self::WITHDRAWAL_ORDER_STATUS_BACKED	   => '已返回',
        self::WITHDRAWAL_ORDER_STATUS_SUCCESS    => '成功',
        self::WITHDRAWAL_ORDER_STATUS_PART	   => '部分处理',
        self::WITHDRAWAL_ORDER_STATUS_FAIL  	   => '失败'
    );

    /**
     * CLi向MC发起审核用户订单创建
     * @param array $aWithdraw 提现记录列表数组
     * @return status list
     */
    public function _doWithdrawOrderCliProcess($aWithdraw = array())
    {
        $aOrderInfo = $this->getThkOrderInfo($aWithdraw['serial_number']);
        file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' check tonghuika order response data : '.json_encode($aOrderInfo)."\n\r", FILE_APPEND);
        if($aOrderInfo['is_success'] === 'TRUE'){
            file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' serial_number : '.$aWithdraw['serial_number']." already exists\n\r", FILE_APPEND);
            $oWithdrawal = Withdrawal::find($aWithdraw['id']);
            $oWithdrawal->status = Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING;
            $oWithdrawal->save();
            return BaseTask::TASK_SUCCESS;
        }

        if(empty($aWithdraw))
        {
            return BaseTask::TASK_SUCCESS;
        }

        if(empty($aWithdraw))
        {
            $this->iStatus = self::WITHDRAWAL_ORDER_STATUS_NEW;
            $aWithdraw = $this->getThkOrderList();
        }

        header('content-type:text/html; charset=utf-8');
        $url = Sysconfig::readValue('REMIT_URL');

        $post_data = array() ;
        $post_data['amount'] = trim($aWithdraw['amount']);
        $oUserBankCard = UserBankCard::where('user_id', $aWithdraw['user_id'])->where('bank_id', $aWithdraw['bank_id'])->where('account', $aWithdraw['account'])->first();
        $post_data['bank_account'] = $oUserBankCard->account_name;
        $post_data['bank_card_no'] = $aWithdraw['account'];
        $oBank = Bank::where('id', $aWithdraw['bank_id'])->first();
        $post_data['bank_code'] = $oBank->bank_code;
        $post_data['input_charset'] = "UTF-8";
        $post_data['merchant_code'] = Sysconfig::readValue('MER_NO');
        $post_data['merchant_order'] = $aWithdraw['serial_number'];
        file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);
        $key = Sysconfig::readValue('MER_KEY');

        $o = "";
        $sign = "";
        ksort($post_data);
        foreach ($post_data as $k => $v) {
            if (!empty($v)) {
                $o.= "$k=".$v."&";
            }
        }

        $post_data = substr ($o , 0 ,-1);
        $sign = md5($post_data."&key=".$key);
        $post_data = $post_data."&sign=".$sign;


        $w = $aWithdraw;

        $aWithdrawInfo = Withdrawal::find($w['id']);

        $check_status = array(2,9,10); //未处理和异常返回和处理中

        if(empty($aWithdrawInfo) && !in_array($aWithdrawInfo['status'], $check_status))
        {
            return BaseTask::TASK_SUCCESS;
            die;
        }

        //平台订单号
        $company_order_num					= $w['serial_number'];

        $orderData['withdrawal_id'] 		= $w['id'];
        $orderData['company_order_num'] 	= $company_order_num;
        $orderData['detail'] 				= $w['id'];
//        $orderData['tonghuika_order_num'] 	= $aWithdrawInfo['tonghuika_order_num'];
        $orderData['request_time'] 			= date("Y-m-d H:i:s");
        $orderData['status'] 				= self::WITHDRAWAL_ORDER_STATUS_NEW;
        $orderData['created'] 				= date("Y-m-d H:i:s");
        $orderData['amount'] 				= $w['amount'];
        $orderData['card_num'] 				= $w['account'];
        $orderData['card_name'] 			= $w['account_name'];

        $oUser = UserUser::find($w['user_id']);
        $orderData['company_user'] 			= $oUser->user_flag;

        //thk 订单ID
        $iWithdrawId = $w['id'];

        //插入初始化的订单表
        $oThkOrder = new ThkOrder();
        $oThkOrder->fill($orderData);
        $oThkOrder->save();

        //针对订单失败的情况
        if( in_array($aWithdrawInfo['status'], array(10)) )
        {
            file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' send request url : '.$url.' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);
            $aCheckResult = $this->doPostRequest($url, $post_data, null);
            file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' response data : '.json_encode($aCheckResult)."\n\r", FILE_APPEND);
            if($aCheckResult === "success")
            {
                //更新订单表内容
                $aWithdrawOrderData = array(
                    'response_time' 	=> date('Y-m-d H:i:s'),
                    'company_order_num'  => $company_order_num,
                    'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
                );
                Withdrawal::find($iWithdrawId)->update($aWithdrawOrderData);
//                $aJobData = [
//                    'merchant_order' => $company_order_num,
//                    'user_id' => $w['user_id']
//                ];
//                $date = Carbon::now()->addMinutes(1);

//                Queue::later($date, 'CheckTongHuiKaWithdrawalStatus', $aJobData, 'withdraw');
//                BaseTask::addTask('CheckTongHuiKaWithdrawalStatus', $aJobData, 'withdraw');
            }
            return BaseTask::TASK_RESTORE;
            die;
        }
        file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' send request url : '.$url.' post_data : '.json_encode($post_data)."\n\r", FILE_APPEND);
        $aCheckResult = self::doPostRequest($url, $post_data, null);
        file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s').' response data : '.json_encode($aCheckResult)."\n\r", FILE_APPEND);

        if($aCheckResult === "success"){
            $aWithdrawOrderData = array(
                'response_time' 	=> date('Y-m-d H:i:s'),
                'company_order_num'  => $company_order_num,
                'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
            );

            $aWithdrawData = array(
                'mc_request_time'       => date('Y-m-d H:i:s'),
                'status' => Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
            );
            $oThkOrder->update($aWithdrawOrderData);
            Withdrawal::find($iWithdrawId)->update($aWithdrawData);
//            $aJobData = [
//                'merchant_order' => $company_order_num,
//                'user_id' => $w['user_id']
//            ];
//            $date = Carbon::now()->addMinutes(1);

//            Queue::later($date, 'CheckTongHuiKaWithdrawalStatus', $aJobData, 'withdraw');
//            BaseTask::addTask('CheckTongHuiKaWithdrawalStatus', $aJobData, 'withdraw');
            return BaseTask::TASK_SUCCESS;
        }else{
            $status = self::WITHDRAWAL_ORDER_RETURN_ERROR;
            Withdrawal::find($iWithdrawId)->update(['status'=>$status,'error_msg'=>$aCheckResult,'mc_request_time'=>date('Y-m-d H:i:s')]);
            return BaseTask::TASK_SUCCESS;
        }
        return BaseTask::TASK_SUCCESS;
        die;
    }

    public static function doPostRequest($url, $data, $optional_headers = null) {
        $params = array('http' => array('method' => 'POST', 'content' => $data));
        if ($optional_headers !== null) {
            $params['http']['header'] = $optional_headers;
        }
        $ctx = stream_context_create($params);
        ini_set('max_execution_time','120');
        $fp = @fopen($url, 'rb', false, $ctx);
        $php_errormsg = '';
        if (!$fp) {
//            throw new Exception("Problem with $url, $php_errormsg");
            file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s')." Problem with $url, $php_errormsg, params : ".json_encode($params)."\n\r", FILE_APPEND);exit;
        }
        $response = @stream_get_contents($fp);
        if ($response === false) {
//            throw new Exception("Problem reading data from $url, $php_errormsg");
            file_put_contents('/tmp/tonghuika', date('Y-m-d H:i:s')." Problem reading data from $url, $php_errormsg, params : ".json_encode($params)."\n\r", FILE_APPEND);exit;
        }
        return $response;
    }

    public function getThkOrderList( $iWithdrawalId = NULL)
    {
        if(empty($iWithdrawalId))
        {
            Withdrawal::where("withdrawal_id",'=',$iWithdrawalId);
        }

        $iCurrentStatus = $this->iStatus;
        self::where('status','=',$iCurrentStatus);

        return ThkOrder::get();
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

    public function getThkOrderInfo($sSerialNumber){
        $url = SysConfig::readValue('REMIT_URL') . '/query';
        $post_data = array();
        $post_data['input_charset'] = "UTF-8";
        $post_data['merchant_code'] = Sysconfig::readValue('MER_NO');
        $key = Sysconfig::readValue('MER_KEY');
        $post_data['merchant_order'] = $sSerialNumber;

        $o = "";
        $sign = "";
        ksort($post_data);
        foreach ($post_data as $k => $v) {
            if (!empty($v)) {
                $o .= "$k=" . $v . "&";
            }
        }

        $post_data = substr($o, 0, -1);
        $sign = md5($post_data . "&key=" . $key);
        $post_data = $post_data . "&sign=" . $sign;

        $sResponseXml = ThkOrder::doPostRequest($url, $post_data, null);

        $aResponse = json_decode(json_encode(simplexml_load_string($sResponseXml)), TRUE);

        $aOrderInfo = $aResponse['response'];

        return $aOrderInfo;
    }

}
