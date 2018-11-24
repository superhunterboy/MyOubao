<?php

class WFZFBDepositNotifyController extends BaseDepositNotifyController {

    protected $platformIdentifier = 'wfzfb';
    protected $test = false;

    protected function checkSign(& $sSign) {
        $sPostedSign = $this->params[$this->Platform->signColumn];
//        $this->clearNoSignValues();
//        pr($this->params);
        $this->PaymentAccount = PaymentAccount::getAccountByNo($this->Payment->id, $this->params[$this->Platform->accountColumn]);
//        pr($this->PaymentAccount->toArray());
//        pr($this->params);
        return $this->Payment->compileSignReturn($this->PaymentAccount, $this->params);
    }

}
