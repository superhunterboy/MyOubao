<?php

class WEFUDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'wefu';
    protected $test = false;

    protected function & mkTestData() {
        $data = array(
            'trade_no' => '1001753932',
            'sign_type' => 'RSA-S',
            'notify_type' => 'offline_notify',
            'merchant_code' => '108777003001',
            'order_no' => '147576797059e3f15078ebc',
            'trade_status' => 'SUCCESS',
            'sign' => 'KyxrZJMZB23HXKxX70d82qP9wzcfT72atItWJwGL/9/EwczikxwMhqDPxTGU7ua/Fxfhj3ckUmp6OYV0bKFgEy2cd1p/gxt7usS3fHci7cU2bJsHgMtjnEpZGojr8FL9QKmUnurz0lmaVjdelKNStqqaZgHaUwPBLA/kNoLhrFw=',
            'order_amount' => '1000',
            'interface_version' => 'V3.0',
            'bank_seq_no' => '1596636253',
            'order_time' => '2017-10-16 07:37:52',
            'notify_id' => '8e58a62e103341fb9f47a5e854c810df',
            'trade_time' => '2017-10-16 07:37:53',
        );
        return $data;
    }

    protected function checkSign(& $sSign) {
        $this->clearNoSignValues();
//        pr($this->params);
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);
//        pr($this->PaymentAccount->toArray());
//        pr($this->params);
        return $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
    }

}
