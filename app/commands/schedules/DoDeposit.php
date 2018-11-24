<?php

class DoDeposit extends BaseTask {

    private $accountLocker;
    private $account_id;
    protected $errorFiles = ['system', 'fund', 'account'];

//    private $dbThreadId;


    protected function doCommand() {
//        pr($this->data);
//        exit;
//        extract($this->data, EXTR_PREFIX_ALL, 'ctp');
        //        pr($data);
        $oDeposit = Deposit::find($this->data['id']);
//        pr($oDeposit->getAttributes());
        if (empty($oDeposit)) {
            $this->log = "Deposit {$this->data['id']} Not Exists";
            return self::TASK_SUCCESS;
        }
        if ($oDeposit->status != Deposit::DEPOSIT_STATUS_CHECK_SUCCESS) {
            $this->log = "Deposit {$oDeposit->id} Status Error: {$oDeposit->status}";
            return self::TASK_SUCCESS;
        }
        if ($oDeposit->amount == 0) {
            $this->log = "Deposit {$oDeposit->id} Amount : 0.00";
            return self::TASK_SUCCESS;
        }
        $oMessage = new Message($this->errorFiles);
        DB::connection()->beginTransaction();
        try {
            // 更新订单状态
//            pr('update success' . "\n");
            if (!$bSuccessful = $oDeposit->setSuccess()) { // 是否更新状态成功
                throw new Exception('UPDATE STATUS ERROR');
            }
//            pr('lock account' . "\n");
            $oUser = User::find($oDeposit->user_id);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                throw new Exception($oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
            }
            // 增加游戏币
//            pr('add money' . "\n");
            $aExtraData = [
                'note' => 'deposit: ' . $oDeposit->id,
            ];
            $iReturn = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_DEPOSIT, $oDeposit->amount, $aExtraData);
            // 添加状态是否成功
            if ($iReturn != Transaction::ERRNO_CREATE_SUCCESSFUL) {
                throw new Exception('ADD COIN ERROR');
            }
            $oDeposit->put_at = date('Y-m-d H:i:s');
            $oDeposit->save();
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
        $oDeposit->addCommissionTask();
        Deposit::addProfitTask(date('Y-m-d'), $oUser->id, $oDeposit->amount);
        return self::TASK_SUCCESS;
    }

}
