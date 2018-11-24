<?php

class ZHIHFDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'zhihf';
    protected $test = false;

    protected function & mkTestData() {
        $iTestDeposit = 6159;
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
            'trade_no' => 'Z1020665791',
            'sign_type' => 'RSA-S',
            'notify_type' => 'offline_notify',
            'merchant_code' => 'Z800002002054',
            'order_no' => '1405527978590ef3316d2d2',
            'trade_status' => 'SUCCESS',
            'sign' => 'UePsaCyAX39vBXJUSxV6jPLuIL3mHhEfCVY4LsiQIii2hkTXQMxQGy6GuhmhuODZDFNfkLfA5rPn5Vm0csX3ii3Hg/F2kTNEJOrc0N7WLSZ5q0ZUC25hXXSOcIm8M9tI0j2JrC1E54y3ns9ptyYIQA23GSpHzu52LlZcRlaYigg=',
            'order_amount' => '10',
            'interface_version' => 'V3.0',
            'bank_seq_no' => 'C1001468124',
            'order_time' => '2017-05-07 18:13:05',
            'notify_id' => '7fb29f1419e240ea9e66e6225cc311ff',
            'trade_time' => '2017-05-07 18:13:08',
            '_url' => '/dnotify/zhf',
        );
//        $data['signMsg'] = $oPayment->compileSignReturn($oPaymentAccount, $data);
//        pr($data);
//        exit;
        return $data;
    }

    protected function checkSign(& $sSign) {
        $this->clearNoSignValues();
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);
        return $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
    }

}
