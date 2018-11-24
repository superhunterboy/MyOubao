<?php
/**
 * 异常推送API
 */
class ExceptionDepositApiController extends Controller {
    
    
    public function index() {
                // 接受请求
            $aRequest = trimArray(Input::all());

//        $aRequest = [  // test data
//            "exception_order_num" => "TLGV20141213021047725",
//            "company_id" => "9",
//            "exact_payment_bank" => "",
//            "pay_card_num" => "",
//            "pay_card_name" => "查霞",
//            "receiving_bank" => "3",
//            "receiving_account_name" => "王必伦",
//            "channel" => "",
//            "note" => "网银转账",
//            "area" => "查霞",
//            "exact_time" => "20141213200204",
//            "amount" => "891.00",
//            "fee" => "NA",
//            "transaction_charge" => "0.00",
//            "key" => "afed2b75c51007c8e2fd8eda86bebca6"
//        ];
            // 数据检查
            if(!$this->verifyApproveData($aRequest)) {
                return $this->makeResponse('VERIFY ERROR');
            }
            if(array_get($aRequest, 'fee') == 'NA') { // 针对Mownecum关于未获取到手续费，返回fee为NA作特殊处理
                $aRequest['fee'] = null;
        }
       // 检查异常订单是否已存在
       if(ExceptionDeposit::firstByAttributes(['exception_order_num' => array_get($aRequest, 'exception_order_num')])) {
           return $this->makeResponse('REPEAT RECORD ERROR');
       }
        // 记录推送数据
       $oExactPaymentBank = Bank::findBankByMcBankId( array_get($aRequest, 'exact_payment_bank') );
       $oReceivingBank = Bank::findBankByMcBankId( array_get($aRequest, 'receiving_bank') );
       $aInitData = [
            'exception_order_num' => array_get($aRequest, 'exception_order_num'),
            'company_id' => array_get($aRequest, 'company_id'),
            'exact_payment_bank' => $oExactPaymentBank ? $oExactPaymentBank->id : null,
            'pay_card_name' => array_get($aRequest, 'pay_card_name'),
            'pay_card_num' => array_get($aRequest, 'pay_card_num'),
            'receiving_bank' => $oReceivingBank ? $oReceivingBank->id : null,
            'receiving_account_name' => array_get($aRequest, 'receiving_account_name'),
            'channel' => array_get($aRequest, 'channel'),
            'note' => array_get($aRequest, 'note'),
            'area' => array_get($aRequest, 'area'),
            'exact_time' => date('Y-m-d H:i:s', strtotime(array_get($aRequest, 'exact_time'))),
            'amount' => array_get($aRequest, 'amount'),
            'fee' => array_get($aRequest, 'fee', null),
            'transaction_charge' => array_get($aRequest, 'transaction_charge'),

       ];
       // 保存推送信息
        $oExceptionDeposit = new ExceptionDeposit($aInitData);
        if (!$oExceptionDeposit->save()) {
//            pr($oExceptionDeposit->validationErrors->toArray());
//            exit;
            return $this->makeResponse('SERVICE INTERNAL ERROR');
        }
        return $this->makeResponse('', $oExceptionDeposit);
    }
    
    
    /**
     * 验证充值确认推送数据
     * @param array $aRequest 得到的推送信息
     * @return boolean
     */
    private function verifyApproveData( array $aRequest ) {
        $aRules = ExceptionDeposit::$rules;
        $aRules['fee'] = ['regex:/(^[0-9]+(.[0-9]{1,2})?$)|(^NA$)/']; // 针对Mownecum关于未获取到手续费，返回fee为NA作特殊处理
        $validator = Validator::make($aRequest, $aRules);
        if (!$validator->passes()) {
//            pr($validator->getMessageBag()->toArray());
            return false;
        }
        $vip_company_id = SysConfig::readValue('mc_company_id_vip');
        if($vip_company_id == $aRequest['company_id']) {
            $key = ExceptionDeposit::getApiKeyForVip($aRequest, ExceptionDeposit::EXCEPTION_API_RECEIVED);
        }else{
            $key = ExceptionDeposit::getApiKey($aRequest, ExceptionDeposit::EXCEPTION_API_RECEIVED);
        }

        if( $key!= array_get($aRequest, 'key')) { // key error
//            echo 'key error';
            return false;
        }
        return true;
    }
    
    
    /**
     * 响应请求
     * @param type $sMessage 指定要反馈的信息
     * @param ExceptionDeposit $oExceptionDeposit 提现推送对象
     * @return JsonResponse
     */
    private function makeResponse($sMessage = '', ExceptionDeposit $oExceptionDeposit = null) {
        $aResponse = [];
        if(!$oExceptionDeposit) {
            $aResponse = [
                'exception_order_num' => '',
                'status' => ExceptionDeposit::RESPONSE_STATUS_FAIL,
                'error_msg' => $sMessage,
            ];
            
        } else {
            $aResponse = [
                'exception_order_num' => $oExceptionDeposit->exception_order_num,
                'status' => ExceptionDeposit::RESPONSE_STATUS_SUCCESS,
                'error_msg' => $sMessage,
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
        @file_put_contents('/tmp/bomao_exception_deposit',$msg . "\n",FILE_APPEND);
    }
}