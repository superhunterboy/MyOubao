<?php

class DoWithdraw extends BaseTask {

    private $accountLocker;
    private $account_id;
    protected $errorFiles = ['system', 'fund', 'account'];

//    private $dbThreadId;


    protected function doCommand() {
//        pr($this->data);
//        exit;
//        extract($this->data, EXTR_PREFIX_ALL, 'ctp');
        //        pr($data);
        $oWithdrawal = Withdrawal::find($this->data['id']);
//        pr($oDeposit->getAttributes());
        if (empty($oWithdrawal)) {
            $this->log = "Withdraw {$this->data['id']} Not Exists";
            return self::TASK_SUCCESS;
        }
        if (!in_array($oWithdrawal->status, [Withdrawal::WITHDRAWAL_STATUS_SUCCESS, Withdrawal::WITHDRAWAL_STATUS_REMITT_VERIFIED])) {
            $this->log = "Withdraw {$oWithdrawal->id} Status Error: {$oWithdrawal->status}";
            return self::TASK_SUCCESS;
        }
        if ($oWithdrawal->amount == 0) {
            $this->log = "Withdraw {$oDeposit->id} Amount : 0.00";
            return self::TASK_SUCCESS;
        }
        $oMessage = new Message($this->errorFiles);

        DB::connection()->beginTransaction();

        try {
            // 更新订单状态
//            pr('update success' . "\n");
            if (!$bSuccessful = $oWithdrawal->setToDeductSuccess()) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
//            pr('lock account' . "\n");
            $oUser = User::find($oWithdrawal->user_id);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                throw new Exception($oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
            }
            $aExtraData = [
                'note' => $oWithdrawal->note . '<br>withdrawal: ' . $oWithdrawal->id,
            ];

            $iReturnUnfreeze = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $oWithdrawal->amount);
            if ($iReturnUnfreeze != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('UNFREEZE AMOUNT ERROR');
            }
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_WITHDRAW, $oWithdrawal->amount, $aExtraData);
            // 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('DEDUCT COIN ERROR');
            }
            $oWithdrawal->put_at = date('Y-m-d H:i:s');
            $oWithdrawal->save();
//            pr('add money success' . "\n");
        } catch (Exception $e) {
            DB::connection()->rollback();
            if (isset($iLocker)) {
                Account::unlock($oUser->account_id, $iLocker);
            }
//            $oDeposit->setAddFail();
            $this->log = $e->getMessage();
            return self::TASK_RESTORE;
        }

//        pr('commit' . "\n");
        DB::connection()->commit();
        Account::unlock($oUser->account_id, $iLocker);
//        Queue::push('EventTaskQueue', ['event' => 'dsgame.activity.deposit', 'user_id' => $oUser->id, 'data' => []], 'activity');
//        $dDepositDate = date('Y-m-d');
        Withdrawal::addProfitTask(date('Y-m-d'), $oUser->id, $oWithdrawal->amount);
//        $oDepositCallback->setResponseSuccessful();
        return self::TASK_SUCCESS;
    }

}
