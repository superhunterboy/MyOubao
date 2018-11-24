<?php

class ZESHENGQQDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'zeshengqq';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 92;
        $oDeposit = Deposit::find($iTestDeposit);
        if (strtolower($oDeposit->platform_identifier) != strtolower($this->platformIdentifier)) {
            pr($oDeposit->platform_identifier);
            $this->halt('wrong platform');
        }
        $oCarbon = new Carbon($oDeposit->created_at);
        $oCarbon->addSeconds(3);
        $oCarbon->setToStringFormat('YmdHis');
        $sAcquiringTime = $oCarbon->__toString();
        $oPayment = PaymentPlatform::getObject($this->platformIdentifier);
        $oPaymentAccount = PaymentAccount::getAccountByNo($oPayment->id, $oDeposit->merchant_code);
        $data = array(
            'ext' => '',
            'totalAmount' => '10000',
            'merchantCode' => '1000000882',
            'transTime' => '20170630162415',
            'transType' => '00202',
            'instructCode' => '2017063000002417630',
            'outOrderId' => $oDeposit->order_no,
        );
        $data['sign'] = $oPayment->compileSignReturn($oPaymentAccount, $data);
        return $data;
    }

    protected function checkSuccessFlag() {
        return true;
    }

}
