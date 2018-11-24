<?php

/**
 * 支付平台基类
 *
 * @author frank
 */
class BasePlatform {

    const PAY_SUCCESS = 1;
    const PAY_QUERY_FAILED = -1;
    const PAY_QUERY_PARSE_ERROR = -2;
    const PAY_SIGN_ERROR = -3;
    const PAY_NO_ORDER = -4;
    const PAY_UNPAY = -5;
    const PAY_AMOUNT_ERROR = -6;
    const PAY_TYPE_NET_BANK = 0;
    const PAY_TYPE_WEIXIN = 1;
    const PAY_TYPE_ZFB = 2;
    const PAY_TYPE_ALL = 3;

    public static $aPayTypes = [
        self::PAY_TYPE_NET_BANK => 'net-bank',
        self::PAY_TYPE_WEIXIN => 'weixin',
        self::PAY_TYPE_ZFB => 'zfb',
        self::PAY_TYPE_ALL => 'all',
    ];
    public $successMsg = '';
    public $signColumn = '';
    public $accountColumn = '';
    public $orderNoColumn = '';
    public $paymentOrderNoColumn = '';
    public $successColumn = '';
    public $successValue = '';
    public $amountColumn = '';
    public $bankNoColumn = '';
    public $bankTimeColumn = '';
    public $serviceOrderTimeColumn = '';
    public $queryResultColumn = '';
    public $unSignColumns = [];

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        
    }

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = [];
        return $data;
    }

    public function getLoadUrl() {
        return $this->platform->relay_url ? $this->platform->relay_url : $this->platform->load_url;
    }

    public function & compileQueryData($oPaymentAccount, $sOrderNo, $sServiceOrderNo) {
        $data = [];
        return $data;
    }

    public function compileQueryUrl($data) {
        
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
        
    }

    protected static function & compileCallBackData($data, $ip) {
        $aData = [];
        return $aData;
    }

    public function addCallBackHistory(& $data, $ip) {
        $aData = $this->compileCallBackData($data, $ip);
        $oDepositCallback = new DepositCallback($aData);
        if ($oDepositCallback->save()) {
            return $oDepositCallback;
        } else {
            pr($oDepositCallback->validationErrors->toArray());
        }
        return false;
    }

    public function getPayAmount($data) {
        return $data[$this->amountColumn];
    }

    public static function & getServiceInfoFromQueryResult(& $aResponses) {
        return $aResponses;
    }

}
