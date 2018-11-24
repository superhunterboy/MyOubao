<?php

class IPSDepositNotifyController extends BaseDepositNotifyController {
    protected $platformIdentifier = 'ips';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit      = 182;
        $oPayment = PaymentPlatform::getObject('ips');

        $oDeposit          = Deposit::find($iTestDeposit);
        $oPaymentAccount = PaymentAccount::getAccountByNo($oPayment->id, $oDeposit->merchant_code);
        $data          = [
            'mercode'      => $oDeposit->merchant_code,
            'Currency_type' => 'RMB',
            'billno'           => $oDeposit->order_no, //商家订单号
            'date'              => substr($oDeposit->created_at,0,10), //商家订单时间
            'amount'       => $oDeposit->amount, //商家订单金额
            'succ'              => 'Y', //是否成功
            'ipsbillno'       => mt_rand(1000000, 9999999), //环讯交易定单号
            'msg'               => 'testing', //智付交易时间
            'bankbillno'        => mt_rand(1000000, 9999999), //银行交易流水号
            'retencodetype' => 17,
        ];
        $data['signature']  = $oPayment->compileSignReturn($oPaymentAccount, $data);
        return $data;
    }

}
