<?php

/**
 * 智付平台
 *
 * @author white
 */
class PaymentGUOFUBAOWAP extends BasePlatform {

    public $successMsg = 'SUCCESS';
    public $signColumn = 'signValue';
    public $accountColumn = 'virCardNoIn';
    public $orderNoColumn = 'merOrderNum';
    public $paymentOrderNoColumn = 'orderId';
    public $successColumn = 'respCode';
    public $successValue = '0000';
    public $amountColumn = 'tranAmt';
    public $bankNoColumn = 'bankCode';
    public $unSignColumns = [ 'signType', 'signValue'];
    public $serviceOrderTimeColumn = 'tranFinishTime';
    public $queryResultColumn = 'msgExt';

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {

        @$signStr = 'version=[' . $aInputData['version'] . ']' .
                'tranCode=[' . $aInputData['tranCode'] . ']' .
                'merchantID=[' . $aInputData['merchantID'] . ']' .
                'merOrderNum=[' . $aInputData['merOrderNum'] . ']' .
                'tranAmt=[' . $aInputData['tranAmt'] . ']' .
                'feeAmt=[' . $aInputData['feeAmt'] . ']' .
                'tranDateTime=[' . $aInputData['tranDateTime'] . ']' .
                'frontMerUrl=[' . $aInputData['frontMerUrl'] . ']' .
                'backgroundMerUrl=[' . $aInputData['backgroundMerUrl'] . ']' .
                'orderId=[' . $aInputData['orderId'] . ']' .
                'gopayOutOrderId=[' . $aInputData['gopayOutOrderId'] . ']' .
                'tranIP=[' . $aInputData['tranIP'] . ']' .
                'respCode=[' . $aInputData['respCode'] . ']' .
                'gopayServerTime=[' . $aInputData['gopayServerTime'] . ']' .
                'VerficationCode=[' . $oPaymentAccount->safe_key . ']';

        return md5($signStr);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        return $this->compileSign($oPaymentAccount, $aInputData, $aNeedKeys);
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = [
            "version" => '2.1',
            "charset" => 2,
            "language" => 1,
            "signType" => 1,
            "tranCode" => '8888',
            "merchantID" => $oPaymentAccount->merchant_id,
            "merOrderNum" => $oDeposit->order_no,
            "tranAmt" => $oDeposit->amount,
            "currencyType" => 156,
            "frontMerUrl" => $oPaymentPlatform->return_url,
            //"backgroundMerUrl" => $oPaymentPlatform->notify_url,
            "tranDateTime" => date('YmdHis', strtotime($oDeposit->created_at)),
            "virCardNoIn" => $oPaymentAccount->account,
            "tranIP" => get_client_ip(),
            "buyerName" => 'MWEB',
            "userType" => '1',
            'feeAmt' => '',
            'backgroundMerUrl' => '',
            'orderId' => '',
            'gopayOutOrderId' => '',
            'respCode' => '',
            'gopayServerTime' => '',
        ];

        $data['signValue'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);

        return $data;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $oDeposit = Deposit::getObjectByParams(['order_no' => $sOrderNo]);
        $data = $aInputData = [
            "tranCode" => '4020',
            "merchantID" => $oPaymentAccount->merchant_id,
            "merOrderNum" => $sOrderNo,
            "tranAmt" => '',
            "ticketAmt" => '',
            "tranDateTime" => date('YmdHis'),
            "currencyType" => '',
            "merURL" => '',
            "customerEMail" => '',
            "authID" => '',
            'orgOrderNum' => $sOrderNo,
            'orgtranDateTime' => date('YmdHis', strtotime($oDeposit->created_at)),
            "orgtranAmt" => '',
            "orgTxnType" => '',
            "orgTxnStat" => '',
            "msgExt" => '',
            "virCardNo" => '',
            "virCardNoIn" => '',
            "tranIP" => $oDeposit->ip,
            "isLocked" => '',
            "feeAmt" => '',
            "respCode" => '',
//            'merchant_code' => $oPaymentAccount->account,
        ];
        $data['signValue'] = $sSafeStr = $this->compileQuerySign($oPaymentAccount, $data);
        return $data;
    }

    public function compileQuerySign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        @$signStr = 'tranCode=[' . $aInputData['tranCode'] . ']' .
                'merchantID=[' . $aInputData['merchantID'] . ']' .
                'merOrderNum=[' . $aInputData['merOrderNum'] . ']' .
                'tranAmt=[' . $aInputData['tranAmt'] . ']' .
                'ticketAmt=[' . $aInputData['ticketAmt'] . ']' .
                'tranDateTime=[' . $aInputData['tranDateTime'] . ']' .
                'currencyType=[' . $aInputData['currencyType'] . ']' .
                'merURL=[' . $aInputData['merURL'] . ']' .
                'customerEMail=[' . $aInputData['customerEMail'] . ']' .
                'authID=[' . $aInputData['authID'] . ']' .
                'orgOrderNum=[' . $aInputData['orgOrderNum'] . ']' .
                'orgtranDateTime=[' . $aInputData['orgtranDateTime'] . ']' .
                'orgtranAmt=[' . $aInputData['orgtranAmt'] . ']' .
                'orgTxnType=[' . $aInputData['orgTxnType'] . ']' .
                'orgTxnStat=[' . $aInputData['orgTxnStat'] . ']' .
                'msgExt=[]' .
                'virCardNo=[' . $aInputData['virCardNo'] . ']' .
                'virCardNoIn=[]' .
                'tranIP=[' . $aInputData['tranIP'] . ']' .
                'isLocked=[' . $aInputData['isLocked'] . ']' .
                'feeAmt=[' . $aInputData['feeAmt'] . ']' .
                'respCode=[' . $aInputData['respCode'] . ']' .
                'VerficationCode=[' . $oPaymentAccount->safe_key . ']';
        return md5($signStr);
    }

    /**
     * Query from Payment Platform
     * @param PaymentPlatform $oPaymentPlatform
     * @param string $sOrderNo
     * @param string $sServiceOrderNo
     * @param array & $aResonses
     * @return integer | boolean
     *  1: Success
     *  -1: Query Failed
     *  -2: Parse Error
     *  -3: Sign Error
     *  -4: Unpay
     *  -5: Amount Error
     */
    public function queryFromPlatform($oPaymentPlatform, $oPaymentAccount, $sOrderNo, $sServiceOrderNo = null, & $aResonses) {
        $data = $this->compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo);
        $sData = $this->_makeRequestData($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oPaymentPlatform->getQueryUrl($oPaymentAccount));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将数据传给变量
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //取消身份验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $sData);
        $response = curl_exec($ch); //接收返回信息
        file_put_contents('/tmp/gfb_' . $sOrderNo, $response);
        if (curl_errno($ch)) {//出错则显示错误信息
            print curl_error($ch);
        }

        curl_close($ch); //关闭curl链接
//        var_dump($response);
        if ($response === '') {     // query failed
            return self::PAY_QUERY_FAILED;
        }

//        $resParser = xml_parser_create();
//        xml_parser_set_option($resParser, XML_OPTION_CASE_FOLDING, 1);
//        if (!xml_parse_into_struct($resParser, $response, $values, $index)) {   // parse error
//            return self::PAY_QUERY_PARSE_ERROR;
//        }
        $xml_parser = xml_parser_create();
       if(!xml_parse($xml_parser,$response,true)){
           xml_parser_free($xml_parser);
           return self::PAY_QUERY_FAILED;
       }else{
            $xmlResult = simplexml_load_string($response);
        }
        $aResonses = [];
        foreach ($xmlResult->children() as $childItem) {
            //输出xml节点名称和值    
            $aResonses[$childItem->getName()] = $childItem;
        }
        if ($aResonses['orgTxnStat'] != '20000') {      // NO ORDER
            return self::PAY_NO_ORDER;
        }
        $sDinpaySign = $aResonses['signValue'];
        $sign = $this->compileQuerySign($oPaymentAccount, $aResonses);

        if ($sign != $sDinpaySign) {
            return self::PAY_SIGN_ERROR;
        }

        if ($aResonses['respCode'] == '0000') {
            return self::PAY_SUCCESS;
        } else {
            return self::PAY_UNPAY;
        }
    }

    public static function & compileCallBackData($data, $ip) {

        $aData = [
            'order_no' => $data['merOrderNum'],
            'service_order_no' => $data['orderId'],
            'merchant_code' => $data['merchantID'],
            'amount' => $data['tranAmt'],
            'ip' => $ip,
            'status' => DepositCallback::STATUS_CALLED,
            'post_data' => var_export($data, true),
            'callback_time' => time(),
            'callback_at' => date('Y-m-d H:i:s'),
            'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'http_user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ];

        return $aData;
    }

    public static function & getServiceInfoFromQueryResult(& $aResponses) {
        $data = [
            'service_time' => date('Y-m-d H:i:s', strtotime($aResponses['tranDateTime'])),
            'service_order_status' => $aResponses['orgTxnStat'],
            'real_amount' => $aResponses['orgtranAmt'],
        ];
        return $data;
    }

    private function _makeRequestData($aData) {
        $aString = [];
        foreach ($aData as $sKey => $sVal) {
            $aString[] = $sKey . '=' . $aData[$sKey];
        }
        return implode('&', $aString);
    }

}
