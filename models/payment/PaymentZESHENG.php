<?php

/**
 * 锐付平台
 */
class PaymentZESHENG extends PaymentZESHENGWX {

    public static $aPayTypes = [
        self::PAY_TYPE_NET_BANK => '',
        self::PAY_TYPE_WEIXIN => 'WECHAT',
        self::PAY_TYPE_ZFB => 'ALIPAY',
        self::PAY_TYPE_ALL => 'ALL',
    ];
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
    public $successMsg = '锐付充值成功';
    public $signColumn = 'sign';
    public $accountColumn = 'merchantCode';                // 通知接口中的变量名
//    public $returnAccountColumn = 'partnerID';
    public $orderNoColumn = 'outOrderId';
    public $paymentOrderNoColumn = 'instructCode';
    public $successColumn = 'transType';
    public $successValue = 'Y';
    public $amountColumn = 'totalAmount';
    public $bankNoColumn = '';
    public $serviceOrderTimeColumn = '';
    public $unSignColumns = [ 'signMD5'];
    public $queryResponseFormat = 'xml';
    public $queryResultColumn = 'resultCode';
    public $signNeedColumns = [
        "lastPayTime",
        "merchantCode",
        "orderCreateTime",
        "outOrderId",
        "totalAmount",
    ];
    public $signNeedColumnsForNotify = [
        "instructCode",
        "merchantCode",
        "outOrderId",
        "totalAmount",
        "transTime",
        "transType",
    ];
    public $signNeedColumnsForQuery = [
        'orderNo',
        'mtaTransIdFrm',
        'mtaTransIdTo',
        'txnStartDt',
        'txnEndDt',
    ];
    public $signNeedColumnsForQueryVerify = [
        'orderno',
        'mtatransid',
        'result',
        'respcode',
    ];

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        $aData = [];
        foreach ($this->signNeedColumns as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
        }
        $sMsg = implode('&', $aData);
//        pr($sMsg . '&KEY=' . $oPaymentAccount->safe_key);
        return strtoupper(md5($sMsg . '&KEY=' . $oPaymentAccount->safe_key));
    }

    public function compileSignReturn($oPaymentAccount, $aInputData) {
        $aData = [];
        foreach ($this->signNeedColumnsForNotify as $sColumn) {
            $aData[$sColumn] = $sColumn . '=' . (isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '');
        }
        $sMsg = implode('&', $aData);
//        pr($sMsg . $oPaymentAccount->safe_key);exit;
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        return strtoupper(md5($sMsg . '&KEY=' . $oPaymentAccount->safe_key));
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            'merchantCode' => $oPaymentAccount->account,
            'outOrderId' => $oDeposit->order_no,
            'totalAmount' => $oDeposit->amount * 100,
            'goodsName' => 'Vitrual' . intval(mt_rand(1, 99999)),
            'goodsExplain' => 'ext',
            'ext' => '',
            'orderCreateTime' => date('YmdHis'),
            'lastPayTime' => date("YmdHis", strtotime("+1 days") + 3600 * 8),
            'merUrl' => $oPaymentPlatform->return_url,
            'noticeUrl' => $oPaymentPlatform->notify_url,
            'bankCode' => $oBank->identifier,
            'bankCardType' => '01',
        ];
        $data['sign'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);

        return $data;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $data = [
            'partyId' => $oPaymentAccount->account,
            'accountId' => $oPaymentAccount->account . '001',
            'orderNo' => $sOrderNo,
            'mtaTransIdFrm' => '',
            'mtaTransIdTo' => '',
            'txnStartDt' => '',
            'txnEndDt' => '',
            'txnSts' => '1',
        ];
//        pr($oPaymentPlatform->toArray());
//        exit;
        $data['signMD5'] = $this->compileQuerySign($oPaymentAccount, $data);
//        pr($data);
        return $data;
    }

    public function compileQuerySign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        $aData = [];
        foreach ($this->signNeedColumnsForQuery as $sColumn) {
            $aData[$sColumn] = isset($aInputData[$sColumn]) ? $aInputData[$sColumn] : '';
        }
        $sMsg = implode('', $aData);
        return md5($sMsg . $oPaymentAccount->safe_key);
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
        $response = trimtab($response);
//         Log::info($response);
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
                    $aResponses[strtolower($aInfo['tag'])] = array_get($aInfo, 'value');
                }
                break;
            case 'querystring':
                parse_str($response, $aResponses);
                break;
        }
//                ErrorCode=0401&serialID=16597060557e7c1d5f119557fcb20b14d94.25137105&mode=1&type=1&beginTime=&endTime=&partnerID=10056214294&remark=query&charset=1&signType=2&signMsg=db6026b6e2bd610714478815434d4695
        if (!$this->checkQueryResponseSign($aResponses, $oPaymentAccount, $sSign)) {
            return self::PAY_SIGN_ERROR;
        }

        switch ($aResponses['result']) {
            case '1000':
            case '1010':
                return self::PAY_NO_ORDER;
//                return self::PAY_UNPAY;
            case '0000':
                if ($aResponses['respcode'] != 1) {
                    return self::PAY_QUERY_FAILED;
                }
                if ($sOrderNo != $aResponses['orderno']) {
                    return self::PAY_UNPAY;     // todo: 此处须进一步区分情况
                }
                return self::PAY_SUCCESS;
            default:
                return self::PAY_UNPAY;
        }
    }

    private function checkQueryResponseSign($aResponse, $oPaymentAccount, & $sSign) {
        $sPostedSign = $aResponse['signmd5'];
        foreach ($this->signNeedColumnsForQueryVerify as $sColumn) {
            $aData[$sColumn] = isset($aResponse[$sColumn]) ? $aResponse[$sColumn] : '';
        }
        $sMsg = implode('', $aData);
        $sSign = md5($sMsg . $oPaymentAccount->safe_key);
        return $sSign == $sPostedSign;
    }

    public static function & compileCallBackData($data, $ip) {
        $aData = [
            'order_no' => $data['outOrderId'],
            'service_order_no' => $data['instructCode'],
            'merchant_code' => $data['merchantCode'],
            'amount' => $data['totalAmount'],
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
        $data = [
            'service_time' => date('Y-m-d H:i:s', strtotime($aResponses['transtime'])),
            'service_order_status' => $aResponses['respcode'],
            'real_amount' => $aResponses['transamt'],
        ];
        return $data;
    }

    private function & __doQuery($oPaymentPlatform, $oPaymentAccount, & $data, $sOrderNo) {
        $ch = curl_init();
//        Log::info($oPaymentPlatform->getQueryUrl($oPaymentAccount));
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

}
