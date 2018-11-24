<?php

class RFPAYDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'rfpay';
    protected $test = false;

    protected function checkSign(& $sSign) {
        $sPostedSign = $this->params[$this->Platform->signColumn];
        $this->clearNoSignValues();
//        pr($this->params);
        $this->PaymentAccount = PaymentAccount::getAccountByNoForRF($this->params[$this->Platform->accountColumn]);
//        pr($this->PaymentAccount->toArray());
//        pr($this->params);
        $sSign = $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
        return $sSign == $sPostedSign;
    }

    protected function & mkTestData() {
        $iTestDeposit = 219;
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
        $data = [
            'orderID' => $oDeposit->order_no, //商家订单号
            'resultCode' => '',
            'stateCode' => '2',
            'orderAmount' => $oDeposit->amount * 100,
            'payAmount' => $oDeposit->amount * 100,
            'acquiringTime' => $sAcquiringTime,
            'completeTime' => $oCarbon->addMinutes(5)->__toString(),
            'orderNo' => mt_rand(10000000, 99999999),
            'partnerID' => $oDeposit->merchant_code,
            'remark' => 'test',
            'charset' => 1,
            'signType' => 2,
        ];
        $data['signMsg'] = $oPayment->compileSignReturn($oPaymentAccount, $data);
//        pr($data);
//        exit;
        return $data;
    }

}
