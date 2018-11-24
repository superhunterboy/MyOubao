<?php

class AlarmController extends Controller {

    public function checkWithdrawNewFlag() {
//        if ($iCount = Withdrawal::checkNewFlag()){
//            $sSessionKey = '_alarm_withdrawal_time';
//            $iLastTime = Session::get($sSessionKey);
//            if (time() - $iLastTime > 15){
//                Session::put($sSessionKey, time());
//            }
//        }
        die(json_encode(Withdrawal::checkNewFlag()));
    }

    public function checkWithdrawNewFlagForFinance() {
        die(json_encode(Withdrawal::checkNewFlagForFinance()));
    }

    public function checkDepositNewFlag() {
        die(json_encode(Deposit::checkNewFlag()));
    }

}
