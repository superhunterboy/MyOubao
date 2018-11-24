<?php

/**
 * 通汇平台
 */
class PaymentTONGHUI extends BasePlatform {

    public $payRequestColumns = [
        'orderNo' => 'serialID',
        'account' => 'partnerID',
        'amount' => 'totalAmount',
    ];
    public $notifyColumns = [
        'orderNo' => 'orderID',
        'account' => 'partnerID',
        'amount' => 'payAmount',
    ];
    public $successMsg = '通汇充值成功';
    public $signColumn = 'sign';
    public $accountColumn = 'merchant_code';                // 通知接口中的变量名
//    public $returnAccountColumn = 'partnerID';
    public $orderNoColumn = 'order_no';
    public $paymentOrderNoColumn = 'trade_no';
    public $successColumn = 'trade_status';
    public $successValue = 'success';
    public $amountColumn = 'order_amount';
    public $bankNoColumn = '';
    public $serviceOrderTimeColumn = 'acquiringTime';
    public $unSignColumns = [ 'sign'];
    public $queryResponseFormat = 'querystring';
    public $queryResultColumn = 'resultCode';
    public $signNeedColumns = [
        'bank_code',
        'customer_ip',
        'input_charset',
        'merchant_code',
        'notify_url',
        'order_amount',
        'order_no',
        'order_time',
        'pay_type',
        'req_referer',
    ];
    public $signNeedColumnsForNotify = [
        'merchant_code',
        'notify_type',
        'order_amount',
        'order_no',
        'order_time',
        'trade_no',
        'trade_status',
        'trade_time',
    ];
    public $signNeedColumnsForQuery = [
        'version',
        'serialID',
        'mode',
        'type',
        'orderID',
        'beginTime',
        'endTime',
        'partnerID',
        'remark',
        'charset',
        'signType',
    ];

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        $aData = [];
        foreach ($this->signNeedColumns as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
        }
        $sMsg = implode('&', $aData);
//        pr($sMsg . '&pkey=' . $oPaymentAccount->safe_key);exit;
        return md5($sMsg . '&key=' . $oPaymentAccount->safe_key);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        foreach ($this->signNeedColumnsForNotify as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
        }
        $sMsg = implode('&', $aData);
        return md5($sMsg . '&key=' . $oPaymentAccount->safe_key);
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            'bank_code' => isMobile() ? 'WEIXIN_H5' : 'WEIXIN',
            'customer_ip' => '127.0.0.1',
            'input_charset' => 'UTF-8',
            'merchant_code' => $oPaymentAccount->account,
            'notify_url' => $oPaymentPlatform->notify_url,
            'order_amount' => $oDeposit->amount,
            'order_no' => $oDeposit->order_no,
            'order_time' => date('Y-m-d H:i:s'),
            'pay_type' => 1,
            'req_referer' => $oPaymentAccount->relay_load_url, // 可选，同步回调地址
        ];
        $data['sign'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);

        return $data;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $data = [
            'version' => '1',
            'serialID' => uniqid(),
            'mode' => 1,
            'type' => 1,
            'orderID' => $sOrderNo,
            'beginTime' => '',
            'endTime' => '',
            'partnerID' => $oPaymentAccount->account,
            'remark' => 'query', // todo: 生成remark
            'charset' => 1,
            'signType' => 2,
        ];
//        pr($oPaymentPlatform->toArray());
//        exit;
        $data['signMsg'] = $this->compileQuerySign($oPaymentAccount, $data);
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
    public function queryFromPlatform($oPaymentPlatform, $oPaymentAccount, $sOrderNo, $sServiceOrderNo = null, & $aResponses) {
//        return false;
        $data = $this->compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo);
//        pr($data);
        $response = $this->__doQuery($oPaymentPlatform, $oPaymentAccount, $data, $sOrderNo);
//        var_dump($response);
//        exit;
        if ($response === '') {     // query failed
            return self::PAY_QUERY_FAILED;
        }
        switch ($this->queryResponseFormat) {
            case 'xml':
                $resParser = xml_parser_create();
                if (!xml_parse_into_struct($resParser, $response, $values, $index)) {   // parse error
                    return self::PAY_QUERY_PARSE_ERROR;
                }
                //            pr($values);
                //            pr($index);
                $aResponses = [];
                foreach ($values as $aInfo) {
                    if ($aInfo['type'] != 'complete') {
                        continue;
                    }
                    $aResponses[strtolower($aInfo['tag'])] = $aInfo['value'];
                }
                break;
            case 'querystring':
                parse_str($response, $aResponses);
                break;
        }
//                ErrorCode=0401&serialID=16597060557e7c1d5f119557fcb20b14d94.25137105&mode=1&type=1&beginTime=&endTime=&partnerID=10056214294&remark=query&charset=1&signType=2&signMsg=db6026b6e2bd610714478815434d4695
//        pr($aResponses);
        if (!$this->checkQueryResponseSign($aResponses, $oPaymentAccount, $sSign)) {
            return self::PAY_SIGN_ERROR;
        }

        switch ($aResponses['resultCode']) {
            case '0009':
            case '0411':
                return self::PAY_NO_ORDER;
//                return self::PAY_UNPAY;
            case '0000':
                if ($aResponses['queryDetailsSize'] < 1) {
                    return self::PAY_QUERY_FAILED;
                }
                list($sQOrderNo, $sQOrderAmount, $sQPayAmount, $sQAcquiringTime, $sPayTime, $sQSOrderNo, $sQStateCode) = explode(',', $aResponses['queryDetails']);
                if ($sOrderNo != $sQOrderNo || $sQStateCode != 2) {
                    return self::PAY_UNPAY;     // todo: 此处须进一步区分情况
                }
                if ($sQOrderAmount != $sQPayAmount) {
                    return self::PAY_AMOUNT_ERROR;
                }
                return self::PAY_SUCCESS;
            default:
                return self::PAY_UNPAY;
        }
    }

    private function checkQueryResponseSign($aResponse, $oPaymentAccount, & $sSign) {
        $sPostedSign = $aResponse['signMsg'];
        unset($aResponse['signMsg']);
        foreach ($aResponse as $sColumn => $sValue) {
            $aData[$sColumn] = $sColumn . '=' . $sValue;
        }
        $sMsg = implode('&', $aData);
        Log::info($sMsg);
        $sSign = md5($sMsg . '&pkey=' . $oPaymentAccount->safe_key);
        Log::info($sSign);
//        $sSign       = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
//        pr($sSign);
//        exit;
        return $sSign == $sPostedSign;
    }

    public static function & compileCallBackData($data, $ip) {
        $aData = [
            'order_no' => $data['trade_no'],
            'service_order_no' => $data['order_no'],
            'merchant_code' => $data['merchant_code'],
            'amount' => $data['order_amount'],
            'ip' => $ip,
            'status' => DepositCallback::STATUS_CALLED,
            'post_data' => json_encode($data),
            'callback_time' => time(),
            'callback_at' => date('Y-m-d H:i:s'),
        ];
        return $aData;
    }

    public static function & getServiceInfoFromQueryResult(& $aResponses) {
        $a = explode(',', $aResponses['queryDetails']);
        $data = [
            'service_order_no' => $a[5],
            'service_time' => date('Y-m-d H:i:s', strtotime($a[3])),
            'service_order_status' => $a[6],
            'pay_time' => date('Y-m-d H:i:s', strtotime($a[4])),
        ];
        return $data;
    }

}
