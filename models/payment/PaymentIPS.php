<?php

/**
 * 环讯平台
 *
 */
class PaymentIPS extends BasePlatform {

    public $successMsg = 'ipscheckok';
    public $signColumn = 'signature';
    public $accountColumn = 'mercode';
    public $orderNoColumn = 'billno';
    public $paymentOrderNoColumn = 'ipsbillno';
    public $successColumn = 'succ';
    public $successValue = 'Y';
    public $amountColumn = 'amount';
    public $bankNoColumn = 'bankbillno';
    public $serviceOrderTimeColumn = '';
    public $unSignColumns = [ 'signature'];

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        return md5('billno' . $aInputData['Billno'] . 'currencytype' . $aInputData['Currency_Type'] . 'amount' . $aInputData['Amount'] .
                'date' . $aInputData['Date'] . 'orderencodetype' . $aInputData['OrderEncodeType'] . $oPaymentAccount->safe_key);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData) {
        return md5('billno' . $aInputData['billno'] . 'currencytype' . $aInputData['Currency_type'] . 'amount' . $aInputData['amount'] .
                'date' . $aInputData['date'] . 'succ' . $aInputData['succ'] . 'ipsbillno' . $aInputData['ipsbillno'] . 'retencodetype' . $aInputData['retencodetype'] . $oPaymentAccount->safe_key);
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            'Mer_Code' => $oPaymentAccount->account,
            'ServerUrl' => $oPaymentPlatform->notify_url,
//            'Merchanturl'        => $oPaymentPlatform->return_url, // 可选，同步回调地址
            'Billno' => $oDeposit->order_no,
            'Date' => str_replace('-', '', substr($oDeposit->created_at, 0, 10)),
            'Currency_Type' => 'RMB',
            'Gateway_Type' => '01',
            'Amount' => $oDeposit->amount,
            'OrderEncodeType' => 5,
            'RetEncodeType' => 17,
            'Rettype' => 1,
//                'client_ip'         => get_client_ip(), // 可选
        ];
        $data['SignMD5'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);
//        $data['sign_type'] = 'MD5';

        return $data;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $data = $aInputData = [
            'service_type' => 'single_trade_query',
            'merchant_code' => $oPaymentAccount->customer_id,
            'interface_version' => 'V3.0',
            'order_no' => $sOrderNo,
        ];
        empty($sServiceOrderNo) or $data['trade_no'] = $sServiceOrderNo;
//        pr($oPaymentPlatform->toArray());
//        exit;
        $data['sign'] = $this->compileSign($oPaymentAccount, $data);
        $data['sign_type'] = 'MD5';
//        pr($data);
        return $data;
    }

    public function compileQueryUrl($data) {
        $aQueryStr = [];
        $aNeed = [
            'service_type',
            'merchant_code',
            'interface_version',
            'sign_type',
            'sign',
            'order_no'
        ];
//        $aQueryStr[] = $key . '=' . $value;
        foreach ($aNeed as $key) {
            $aQueryStr[] = $key . '=' . $data[$key];
        }
        return $oPaymentPlatform->query_url . '?' . implode('&', $aQueryStr);
    }

    /**
     * 此方法不可用,因平台不支持
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
        return false;
        $data = $this->compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oPaymentPlatform->getQueryUrl());
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将数据传给变量
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //取消身份验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch); //接收返回信息
        file_put_contents('/tmp/zf_' . $sOrderNo, $response);
        if (curl_errno($ch)) {//出错则显示错误信息
            print curl_error($ch);
        }

        curl_close($ch); //关闭curl链接
//        var_dump($response);
        if ($response === '') {     // query failed
            return self::PAY_QUERY_FAILED;
        }
        $resParser = xml_parser_create();
        if (!xml_parse_into_struct($resParser, $response, $values, $index)) {   // parse error
            return self::PAY_QUERY_PARSE_ERROR;
        }
//            pr($values);
//            pr($index);
        $aResonses = [];
        foreach ($values as $aInfo) {
            if ($aInfo['type'] != 'complete') {
                continue;
            }
            $aResonses[strtolower($aInfo['tag'])] = $aInfo['value'];
        }
//        pr($aResonses);
//        exit;
        if ($aResonses['is_success'] == 'F') {      // NO ORDER
            return self::PAY_NO_ORDER;
        }
        $sDinpaySign = $aResonses['sign'];
        $sDinpaySignType = $aResonses['sign_type'];
        unset($aResonses['sign'], $aResonses['sign_type']);
//        pr($aResonses);
        $aNeed = [
            'merchant_code', 'order_no', 'order_time', 'order_amount', 'trade_no', 'trade_time', 'trade_status'
        ];
        $sign = $this->compileSign($oPaymentPlatform, $aResonses, $aNeed);
//        pr($sign);
//        pr($sDinpaySign);
//        exit;
//            $sign         = md5($sQueryString . '&key=' . $sMerchantKey);
//        pr($sign);
        if ($sign != $sDinpaySign) {
            return self::PAY_SIGN_ERROR;
        }

        switch ($aResonses['trade_status']) {
            case 'UNPAY':
                return self::PAY_UNPAY;
            case 'SUCCESS':
//                if ($aResonses['order_amount'] != $oDeposit->amount) {
//                    return self::PAY_AMOUNT_ERROR;
//                }
                return self::PAY_SUCCESS;
        }
//        return $aResonses['is_success'] == 'T' && $aResonses['trade_status'] == 'SUCCESS';
//        exit;
    }

    public static function & compileCallBackData($data, $ip) {
        $aData = [
            'order_no' => $data['billno'],
            'service_order_no' => $data['ipsbillno'],
            'merchant_code' => $data['mercode'],
            'amount' => $data['amount'],
            'ip' => $ip,
            'status' => DepositCallback::STATUS_CALLED,
            'post_data' => var_export($data, true),
            'callback_time' => time(),
            'callback_at' => date('Y-m-d H:i:s'),
            'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null,
            'http_user_agent' => array_get($_SERVER, 'HTTP_USER_AGENT'),
        ];
        return $aData;
    }

//    public static function addCallBackHistory(& $data, $ip){
//        $aData = self::compileCallBackData($data, $ip);
//        pr($aData);
//        exit;
//        $oDepositCallback = new DepositCallback($aData);
//        if ($oDepositCallback->save()){
//            return $oDepositCallback;
//        }
//        return false;
//    }
}
