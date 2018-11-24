<?php

/**
 * 新生平台
 *
 */
class PaymentXINSHENG extends BasePlatform {

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
    public $successMsg = '200';
    public $signColumn = 'signMsg';
    public $accountColumn = 'partnerID';                // 通知接口中的变量名
//    public $returnAccountColumn = 'partnerID';
    public $orderNoColumn = 'orderID';
    public $paymentOrderNoColumn = 'orderNo';
    public $successColumn = 'stateCode';
    public $successValue = '2';
    public $amountColumn = 'payAmount';
    public $bankNoColumn = '';
    public $serviceOrderTimeColumn = 'acquiringTime';
    public $unSignColumns = [ 'signMsg'];
    public $queryResponseFormat = 'querystring';
    public $queryResultColumn = 'resultCode';
    public $signNeedColumns = [
        'version',
        'serialID',
        'submitTime',
        'failureTime',
        'customerIP',
        'orderDetails',
        'totalAmount',
        'type',
        'buyerMarked',
        'payType',
        'orgCode',
        'currencyCode',
        'directFlag',
        'borrowingMarked',
        'couponFlag',
        'platformID',
        'returnUrl',
        'noticeUrl',
        'partnerID',
        'remark',
        'charset',
        'signType',
    ];
    public $signNeedColumnsForNotify = [
        'orderID',
        'resultCode',
        'stateCode',
        'orderAmount',
        'payAmount',
        'acquiringTime',
        'completeTime',
        'orderNo',
        'partnerID',
        'remark',
        'charset',
        'signType',
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
        return md5($sMsg . '&pkey=' . $oPaymentAccount->safe_key);
    }

    public function compileQuerySign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        $aData = [];
        foreach ($this->signNeedColumnsForQuery as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
        }
        $sMsg = implode('&', $aData);
        return md5($sMsg . '&pkey=' . $oPaymentAccount->safe_key);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData) {
        foreach ($this->signNeedColumnsForNotify as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
//            !isset($aInputData[$sColumn]) or $aData[$sColumn] = $sColumn . '=' . $aInputData[$sColumn];
        }
        $sMsg = implode('&', $aData);
        return md5($sMsg . '&pkey=' . $oPaymentAccount->safe_key);
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            'version' => '2.6',
            'serialID' => uniqid(),
            'submitTime' => str_replace(['-', ' ', ':'], '', $oDeposit->created_at),
            'failureTime' => str_replace(['-', ' ', ':'], '', Carbon::now()->addHour()->toDatetimeString()),
            'orderDetails' => $oDeposit->order_no . ',' . $oDeposit->amount * 100 . ',,' . '代金券' . ',' . 1,
            'totalAmount' => $oDeposit->amount * 100,
            'type' => '1000',
            'payType' => 'ALL',
            'currencyCode' => 1,
            'directFlag' => 0,
            'borrowingMarked' => 0,
            'couponFlag' => 0,
            'returnUrl' => route('depositapi.xs'), // 可选，同步回调地址
            'noticeUrl' => route('dnotify.xs'),
            'partnerID' => $oPaymentAccount->account,
            'remark' => 'test', // todo: 生成remark
            'charset' => 1,
            'signType' => 2,
        ];
        $data['signMsg'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);
//        var_dump($data);
//        exit;
        return $data;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $data = [
            'version' => '2.6',
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

    private function & __doQuery($oPaymentPlatform, $oPaymentAccount, & $data, $sOrderNo) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $oPaymentPlatform->getQueryUrl($oPaymentAccount));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //将数据传给变量
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //取消身份验证
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch); //接收返回信息
//        file_put_contents('/tmp/xs_' . $sOrderNo, $response);
        if (curl_errno($ch)) {//出错则显示错误信息
            print curl_error($ch);
        }
        curl_close($ch); //关闭curl链接
        return $response;
    }

    /**
     * Query from Payment Platform
     * @param PaymentPlatform $oPaymentPlatform
     * @param string $sOrderNo
     * @param string $sServiceOrderNo
     * @param array & $aResponses
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
        Log::info($response);
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

        Log::info($aResponses);
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
        $sSign = md5($sMsg . '&pkey=' . $oPaymentAccount->safe_key);
//        $sSign       = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
//        pr($sSign);
//        exit;
        return $sSign == $sPostedSign;
    }

    public static function & compileCallBackData($data, $ip) {
        $aData = [
            'order_no' => $data['orderID'],
            'service_order_no' => $data['orderNo'],
            'merchant_code' => $data['partnerID'],
            'amount' => $data['payAmount']/100,
            'ip' => $ip,
            'status' => DepositCallback::STATUS_CALLED,
            'post_data' => json_encode($data),
            'callback_time' => time(),
            'callback_at' => date('Y-m-d H:i:s'),
        ];
        return $aData;
    }

    public function getPayAmount($data) {
        return formatNumber($data[$this->amountColumn] / 100, 2);
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
