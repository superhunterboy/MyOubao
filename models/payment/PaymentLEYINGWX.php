<?php

/**
 * 乐赢平台
 */
class PaymentLEYINGWX extends PaymentLEYING {

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
            'version' => '1.0',
            'serialID' => uniqid(),
            'submitTime' => date('YmdHis', time()),
            'failureTime' => date('YmdHis', strtotime("+1 year")),
            'customerIP' => 'localhost[127.0.0.1]',
            'orderDetails' => $oDeposit->order_no . ',' . $oDeposit->amount * 100 . ',,' . '代金券' . ',' . 1,
            'totalAmount' => $oDeposit->amount * 100,
            'type' => '1000',
            'buyerMarked' => '',
            'payType' => key_exists($oPaymentAccount->pay_type, self::$aPayTypes) ? self::$aPayTypes[$oPaymentAccount->pay_type] : self::$aPayTypes[self::PAY_TYPE_ALL],
            'orgCode' => key_exists($oPaymentAccount->pay_type, self::$aOrgCodes) ? self::$aOrgCodes[$oPaymentAccount->pay_type] : self::$aOrgCodes[self::PAY_TYPE_ALL],
            'currencyCode' => 1,
            'directFlag' => $oPaymentAccount->pay_type == self::PAY_TYPE_WEIXIN ? 1 : 0,
            'borrowingMarked' => 0,
            'couponFlag' => 0,
            'platformID' => '',
            'returnUrl' => route('depositapi.lywx'), // 可选，同步回调地址
            'noticeUrl' => route('dnotify.lywx'),
            'partnerID' => $oPaymentAccount->account,
            'remark' => 'test', // todo: 生成remark
            'charset' => 1,
            'signType' => 2,
        ];
        $data['signMsg'] = $sSafeStr = $this->compileSign($oPaymentAccount, $data);

        return $data;
    }

}
