<?php

/**
 * Query From PlatForm
 *
 * @author dev
 */
class CheckDeposit extends BaseTask {
    protected $delayOnRelease = 15;

    function doCommand() {
//        pr($this->data);
//        exit;
        extract($this->data, EXTR_PREFIX_ALL, 'dep');
//        pr($dep_id);
//        exit;
        $oDeposit = Deposit::find($dep_id);
//        pr($oDeposit->toArray());
//        exit;
        if (empty($oDeposit)) {
            $this->log = ' Deposit Missing, Exiting';
            return self::TASK_SUCCESS;
        }
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_RECEIVED) {
            $this->log = ' Deposit Status Is Not Received, Exiting';
            return self::TASK_SUCCESS;
        }
        $oPayment     = PaymentPlatform::getObject($oDeposit->platform_identifier);
//        $oPayment = PaymentPlatform::find($oDeposit->platform_id);
        $oPaymentAccount = PaymentAccount::getAccountByNo($oPayment->id, $oDeposit->merchant_code);
//        pr($oPayment->toArray());
//        exit;
        $iQueryResult = $oPayment->queryFromPlatform($oPaymentAccount, $oDeposit->order_no, null, $aResponses);
//        pr($iQueryResult);
//        exit;
//        $iQueryResult = BasePlatform::PAY_NO_ORDER;
//        $iQueryResult = intval($iQueryResult);
//        pr($aResponses);
//        pr($iQueryResult);
//        exit;
        switch ($iQueryResult) {
            case BasePlatform::PAY_QUERY_FAILED: // no order in platform
            case BasePlatform::show :
                $iApplyTime = strtotime($oDeposit->created_at);
                if (time() - $iApplyTime < 1800) {
                    $this->log = ' Query Failed, Restore';
                    return self::TASK_KEEP;
                } else {
                    $this->log = ' Order Failed In 30 Minutes, No longer Query Again, Exiting';
                    return self::TASK_SUCCESS;
                }
                break;
            case BasePlatform::PAY_NO_ORDER:
                $iApplyTime = strtotime($oDeposit->created_at);
                if (time() - $iApplyTime < 900) {
                    $this->log = ' Order Not Exists In Platform, Waiting, Restore';
                    return self::TASK_KEEP;
                } else {
                    if ($oDeposit->setClosed()) {
                        $this->log = ' Order Not Exists In Platform, Deposit Closed, Exiting';
                        return self::TASK_SUCCESS;
                    } else {
                        $this->log = ' Order Not Exists In Platform, But Can Not Close The Deposit, Retore';
                        return self::TASK_KEEP;
                    }
                }
                break;
            case BasePlatform::PAY_UNPAY:
                $iApplyTime = strtotime($oDeposit->created_at);
                if (time() - $iApplyTime < Deposit::DEPOSIT_THIRD_AVAILABLE_TIME) {
                    $this->log = ' Unpay, Waiting, Restore';
                    return self::TASK_KEEP;
                } else {
                    if ($oDeposit->setClosed()) {
                        $this->log = ' Unpay In ' . Deposit::DEPOSIT_THIRD_AVAILABLE_TIME . ' Seconds, Deposit Closed, Exiting';
                        return self::TASK_SUCCESS;
                    } else {
                        $this->log = ' Unpay In ' . Deposit::DEPOSIT_THIRD_AVAILABLE_TIME . ' Seconds, But Can Not Close The Deposit, Retore';
                        return self::TASK_RESTORE;
                    }
                }
                break;
            case BasePlatform::PAY_AMOUNT_ERROR:
                $this->log = ' Payed, But The Amount Error, Restore';
                return self::TASK_RESTORE;
                break;
            case BasePlatform::PAY_SUCCESS:
                $this->log = ' Pay Success';
                $data = $oPayment->platform->getServiceInfoFromQueryResult($aResponses);
//                if ($oDeposit->platform_id == 3){
//                    $a = explode(',', $aResponses['queryDetails']);
//                    $data = [
//                                'service_order_no' => $a[3],
//                                'service_time' => date('Y-m-d H:i:s', strtotime($a[1])),
//                                'service_order_status' => $a[4],
//                                'pay_time' => date('Y-m-d H:i:s', strtotime($a[2])),
//                    ];
//                }
//                else{
//                    $data = $aResponses;
//                }
                if (!$oDeposit->setWaitingLoad($data)) {
                    $this->log = ' Payed, But Set Deposit to Waiting Load Error, Restore';
                    return self::TASK_RESTORE;
                }

                // 加币
                if (Deposit::addDepositTask($oDeposit->id)) {
                    $oPaymentAccount->updateStat($oDeposit);
                    $this->log = ' Payed, Task Added';
                    return self::TASK_SUCCESS;
//                    return $this->goBackToIndex('success', __('_deposit.waiting-load-seted'));
                }
                break;
        }
    }

}
