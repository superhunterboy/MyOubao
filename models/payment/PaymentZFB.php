<?php

/**
 * 环讯平台
 *
 */
class PaymentZFB extends BasePlatform {

    public $successMsg = '支付宝充值成功';
    public $signColumn = 'sign';
    public $accountColumn = 'seller_id';
    public $orderNoColumn = 'out_trade_no';
    public $paymentOrderNoColumn = 'trade_no';
    public $successColumn = 'is_success';
    public $successValue = 'T';
    public $amountColumn = 'total_fee';
    public $bankNoColumn = '';
    public $serviceOrderTimeColumn = '';
    public $unSignColumns = [ 'sign'];

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        $paramStr = createLinkstring($aInputData);
        return md5($paramStr . $oPaymentAccount->safe_key);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($aInputData);
        //对待签名参数数组排序
        $para_sort = argSort($para_filter);
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);
        return md5($prestr . $oPaymentAccount->safe_key);
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            '_input_charset' => $oPaymentPlatform->charset,
            'notify_url' => $oPaymentPlatform->notify_url,
            'out_trade_no' => $oDeposit->order_no,
            'partner' => $oPaymentAccount->account,
            'payment_type' => '1',
            'return_url' => $oPaymentPlatform->return_url,
            'seller_email' => 'bohualin1@163.com',
            'service' => 'create_direct_pay_by_user',
            'subject' => $oDeposit->order_no,
            'total_fee' => $oDeposit->amount,
        ];
        $data['sign'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);
        $data['sign_type'] = 'MD5';

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
            'order_no' => $data['out_trade_no'],
            'service_order_no' => $data['trade_no'],
            'merchant_code' => $data['seller_id'],
            'amount' => $data['total_fee'],
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
