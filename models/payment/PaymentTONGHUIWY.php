<?php

/**
 * 通汇平台
 */
class PaymentTONGHUIWY extends PaymentTONGHUI {

    public $signNeedColumns = [
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

    public function & compileInputData($oPaymentPlatform, $oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        $data = $aInputData = [
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

}
