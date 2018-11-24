<?php

use SoapBox\Formatter\Formatter;

class SdOrder extends BaseModel
{

    protected $table = 'sd_orders';

    protected $fillable = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'sd_order_num',
        'detail',
        'request_time',
        'response_time',
        'sd_status',
        'status',
        'refund_type',
        'amount',
    ];

    public static $columnForList = [
        'id',
        'withdrawal_id',
        'company_order_num',
        'sd_order_num',
        'detail',
        'request_time',
        'response_time',
        'sd_status',
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
        self::WITHDRAWAL_ORDER_STATUS_FAIL  	   => '失败',
    );

    /**
     * 返回MC订单记录列表
     * @param $iWithdrawalId
     * @return Array
     */
    public function getSdOrderList( $iWithdrawalId = NULL)
    {
        if(empty($iWithdrawalId))
        {
            Withdrawal::where("withdrawal_id",'=',$iWithdrawalId);
        }

        $iCurrentStatus = $this->iStatus;
        self::where('status','=',$iCurrentStatus);

        return SdOrder::get();
    }

    /**
     * CLi向MC发起审核用户订单创建
     * @param array $aWithdraw 提现记录列表数组
     * @return status list
     */
    public function _doWithdrawOrderCliProcess($aWithdraw = array())
    {
        if(empty($aWithdraw)) return BaseTask::TASK_SUCCESS;

        //实例化一些类和一些数据
        $w = $aWithdraw;
        $this->saveLog('\r\n---begin--request-a-withdraw'.var_export($w,true));

        $aWithdrawInfo = Withdrawal::find($w['id']);

        //未处理和异常返回和处理中
        $check_status = [
            Withdrawal::WITHDRAWAL_STATUS_VERIFIED,
            Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING,
            Withdrawal::WITHDRAWAL_STATUS_MC_ERROR_RETURN,
        ];
        if(empty($aWithdrawInfo) && !in_array($aWithdrawInfo['status'], $check_status))
        {
            return BaseTask::TASK_SUCCESS;
            die;
        }

        $oSdOrder = $this->getSdOrderObject($aWithdraw);
        $xmlString = $this->compileRequestData($aWithdraw);
        $des = $this->encryptData($xmlString);
        $aApiRes = $this->request($des);

        $result = $this->validReposeData($aApiRes, $w['id'], $oSdOrder);

        $this->saveLog('\r\n---end--request-a-withdraw');
    }

    /**
     * 增加订单
     * @param $attributes
     * @return SdOrder
     */
    public function getSdOrderObject($attributes){
        $w = $attributes;

        $oSdOrder = self::where('company_order_num', '=', $w['serial_number'])->first();

        if(! is_object($oSdOrder))
        {
            $oUser = UserUser::find($w['user_id']);
            $orderData['company_user'] 			= $oUser->user_flag;

            $orderData['withdrawal_id'] 		= $w['id'];
            $orderData['company_order_num'] 	= $w['serial_number'];
            $orderData['detail'] 				= $w['id'];
            $orderData['request_time'] 			= date("Y-m-d H:i:s");
            $orderData['status'] 				= self::WITHDRAWAL_ORDER_STATUS_NEW;
            $orderData['created'] 				= date("Y-m-d H:i:s");
            $orderData['amount'] 				= $w['amount'];

            //插入初始化的订单表
            $oSdOrder = new SdOrder();
            $oSdOrder->fill($orderData);
            $oSdOrder->save();
        }

        return $oSdOrder;
    }

    /**
     * 编译请求数据
     * @param array $aWithdraw
     * @return string
     */
    public function compileRequestData(array $aWithdraw)
    {
        /*        $TransferInformation = [
                    'Id' => 0,
                    'IntoAccount' => $aWithdraw['account'],
                    'IntoName' => $aWithdraw['account_name'],
                    'IntoBank1' => $aWithdraw['branch'],
                    'IntoBank2' => $aWithdraw['branch_address'],//转入分行
                    'IntoAmount' => $aWithdraw['amount'],
                    'TransferNote' => '',
                    'RecordsState' => 0,
                    'SerialNumber' => $aWithdraw['serial_number'],
                    'BusinessmanId' => 0,
                    'SendORNOT' => 0,
                    'beforeMoney' => 0,
                    'afterMoney' => 0,
                    'BankCardAlias' => '',
                    'AccountSerialNumber' => '',
                    'GroupId' => 0,
                    'TransferredBank' => '',
                    'IntoProvince' => '',
                    'IntoCity' => '',
                    'BusinessmanName' => '',
                    'BankCode' => '',
                ];*/
        //Formatter::make($TransferInformation, Formatter::ARR);

        $oBank = Bank::find($aWithdraw['bank_id']);
        $IntoBank1 = $oBank->identifier_sdpay ? $oBank->identifier_sdpay : $oBank->identifier;

        $xml = "<TransferInformation>"
            ."<Id>0</Id>"
            ."<IntoAccount>".$aWithdraw['account']."</IntoAccount>"
            ."<IntoName>".$aWithdraw['account_name']."</IntoName>"
            ."<IntoBank1>".$IntoBank1."</IntoBank1>"
            ."<IntoBank2>".$aWithdraw['branch']."</IntoBank2>"
            ."<IntoAmount>".$aWithdraw['amount']."</IntoAmount>"."<TransferNote></TransferNote>"
            ."<RecordsState>0</RecordsState>"
            ."<SerialNumber>".$aWithdraw['serial_number']."</SerialNumber>"
            ."<BusinessmanId>0</BusinessmanId>"
            ."<SendORNOT>0</SendORNOT>"
            ."<beforeMoney>0</beforeMoney>"
            ."<afterMoney>0</afterMoney>"
            ."<BankCardAlias></BankCardAlias>"
            ."<AccountSerialNumber>0</AccountSerialNumber>"
            ."<GroupId>0</GroupId>"
            ."<TransferredBank></TransferredBank>"
            ."<IntoProvince></IntoProvince>"
            ."<IntoCity></IntoCity>"
            ."<BusinessmanName></BusinessmanName>"
            ."<BankCode></BankCode>"
            ."</TransferInformation>";

        $this->saveLog($xml);

        return $xml;
    }

    /**
     * 编译响应数据
     * @param array $aWithdraw
     * @return string
     */
    public function compileReposeData($xml)
    {
        $formatter = Formatter::make($xml, Formatter::XML);
        return $formatter->toArray();
    }

    public function encryptData($xml){
        //todo
        $key1 ="iS0ZQizlV88=";
        $key2 ="ZzQkyWUGnos=";

        $mencrypt = new SDPay($key1,$key2);
        return $mencrypt->encryptData($xml);
    }

    public function decryptData($resultStr){
        //todo
        $key1 ="iS0ZQizlV88=";
        $key2 ="ZzQkyWUGnos=";

        $decrypt = new SDPay($key1,$key2);
        return $decrypt->decryptData($resultStr);
    }


    public function request($des){
        //todo
        $merchantid = 'bm061015';
        $url = 'https://payout.sdapayapi.com/8001/Customer.asmx?WSDL';

        try {
            $soap = new SoapClient($url);
            //$params = array($merchantid => 'LoginAccount', $des => 'GetFundInfo');
            $result = $soap->GetFund($merchantid,$des);

            return $result;

        } catch (SoapFault $exception) {

            echo $exception;
        }
    }

    public function validReposeData($apiRes, $iWithdrawId, $oSdOrder){

        $this->saveLog('back from sd');
        $this->saveLog('result:'.$apiRes);

        $aWithdrawOrderData['response_time'] = $aWithdrawData['mc_request_time'] = date('Y-m-d H:i:s');
        $aWithdrawOrderData['sd_status']  = $apiRes;

        $status = '';
        if($apiRes != "")
        {
            $apiRes = intval($apiRes);
            //-------------------------测试数据-----------------------------
            //--------------------------测试数据结束-------------------------
            //如果返回结果大于0则表示提交成功，返回值为这条记录的serverId。如果返回值小于0代表提交失败
            if( $apiRes > 0 )
            {
                $aWithdrawOrderData['sd_status']  = 0;
                $aWithdrawData['status'] = Withdrawal::WITHDRAWAL_STATUS_MC_PROCESSING;
                $aWithdrawOrderData['sd_order_num'] = $aWithdrawData['mownecum_order_num'] = $apiRes;
                $orderStatus = self::WITHDRAWAL_ORDER_STATUS_PART;
            }else{
                $errorCode = [-11 => 'amount is too less', -12 => 'info is part', -13 => 'bank is null', -14 => 'bank Invalid', -15 => 'order repeat'];

                if(in_array($apiRes, array_keys($errorCode))){
                    $this->saveLog($errorCode[$apiRes]);
                    $orderStatus = self::WITHDRAWAL_ORDER_RETURN_ERROR;
                    if($apiRes == -15) return BaseTask::TASK_SUCCESS;
                }else{
                    $this->saveLog(' result '.$apiRes.' is dimness');
                    $orderStatus = self::WITHDRAWAL_ORDER_STATUS_FAIL;
                    $aWithdrawData['status'] = Withdrawal::WITHDRAWAL_STATUS_MC_ERROR_RETURN;
                }
            }
        }
        else {
            $orderStatus = self::WITHDRAWAL_ORDER_STATUS_FAIL;
            $this->saveLog('no-data back from sd');
        }

        empty($aWithdrawData['status']) or Withdrawal::find($iWithdrawId)->update($aWithdrawData);

        $aWithdrawOrderData['status'] = $orderStatus;
        $oSdOrder->update($aWithdrawOrderData);

        return empty($apiRes) ? BaseTask::TASK_RESTORE : BaseTask::TASK_SUCCESS;

        die;
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


    /**
     * 保持日志
     * @param $messages
     */
    private function saveLog($messages){
        $messages = is_array($messages) ? var_export($messages, true) : $messages;
        $messages = "\r\n---".'['.date("H:i:s").']'. $messages;
        file_put_contents("/tmp/sd_request_backdata_".date("Y-m-d"), $messages,FILE_APPEND);
    }
}
