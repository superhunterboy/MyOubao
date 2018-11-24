<?php

/**
 * 手动充值队列任务
 *
 * */
class ManualDepositQueue extends BaseTask {

    protected $Issue;
    protected $winningNumbers = [];
    protected $hasWatingTraceDetail;
    protected $betQueueIsEmpty;
    protected $errorFiles = ['system','bet','fund','account','lottery','issue','seriesway'];

    protected function doCommand() {
        $oManualDeposit = ManualDeposit::find($this->data['manual_deposit_id']);
        if (!is_object($oManualDeposit)) {
            $this->log = 'manualDeposit missing, id=' . $this->data['manual_deposit_id'];
            return self::TASK_SUCCESS;
        }

        $oUser = User::find($oManualDeposit->user_id);
        DB::connection()->beginTransaction();
        $fAmount = $oManualDeposit->amount_add_coin;
        $sNote = $oManualDeposit->note;
        $iTransactionType = $oManualDeposit->transaction_type_id;
        $aValidTransactionTypes = ManualDeposit::$aValidTransactionTypes;

        if ($fAmount <= 0 || !in_array($iTransactionType, $aValidTransactionTypes)) {
            $this->log = __('_basic.deposit-error');
            return self::TASK_SUCCESS;
        }
        $fAmount = formatNumber($fAmount, 2);
        $oAccount = Account::lock($oUser->account_id, $iLocker);

        if (empty($oAccount)) {
            $oMessage = new Message($this->errorFiles);
            return $this->goBack('error', $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
        }
        $aExtraData = [
            'note' => $sNote,
            'administrator' => $oManualDeposit->administrator,
            'admin_user_id' => $oManualDeposit->admin_user_id,
        ];
        $bSucc = Transaction::addTransaction($oUser, $oAccount, $iTransactionType, $fAmount, $aExtraData) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
        !$bSucc or $bSucc = $oManualDeposit->changeStatus(ManualDeposit::STATUS_VERIFIED, ManualDeposit::STATUS_DEPOSIT_SUCCESS);
        if ($bSucc) {
            DB::connection()->commit();
//            $iTransactionType != TransactionType::TYPE_DEPOSIT_BY_ADMIN or Deposit::addProfitTask(date('Y-m-d'), $oUser->id, $fAmount);
//            $iTransactionType != TransactionType::TYPE_PROMOTIANAL_BONUS or Deposit::addProfitTask(date('Y-m-d'), $oUser->id, $fAmount,'dividend');
        } else {
            DB::connection()->rollback();
        }
        Account::unLock($oUser->account_id, $iLocker, false);
        if ($bSucc) {
            return self::TASK_SUCCESS;
        } else {
            return self::TASK_KEEP;
        }
    }

}
