<?php

class ZHIFUBAODepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'zfb';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 183;
        $oDeposit = Deposit::find($iTestDeposit);
        $data = [
            'discount' => '0.00',
            'payment_type' => '1',
            'subject' => '30836082456692f9dc90de',
            'trade_no' => '2015121021001004390096423945',
            'buyer_email' => '13241321209',
            'gmt_create' => '2015-12-10 15:57:17',
            'notify_type' => 'trade_status_sync',
            'quantity' => '1',
            'out_trade_no' => '30836082456692f9dc90de',
            'seller_id' => '2088121460152961',
            'notify_time' => '2015-12-10 16:01:19',
            'trade_status' => 'TRADE_SUCCESS',
            'is_total_fee_adjust' => 'N',
            'total_fee' => '2.00',
            'gmt_payment' => '2015-12-10 15:57:32',
            'seller_email' => 'bohualin1@163.com',
            'price' => '2.00',
            'buyer_id' => '2088912648459390',
            'notify_id' => 'db4cca4d88cf425a3db91f74b72d3b6j0c',
            'use_coupon' => 'N',
            'sign_type' => 'MD5',
            'sign' => 'e6856535e8dc796dae017c5f7b6f1cc7',
        ];
        $data['sign'] = UserDeposit::compileSignZf($data, $oDeposit->merchant_key);
        $data['sign_type'] = 'MD5';
        return $data;
    }

}
