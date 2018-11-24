<?php

/**
 * 调整指定用户的不可提现金额
 *
 * @author white
 */
class AdjustProhibitAmount extends BaseTask {
    
    protected function doCommand() {
        extract($this->data);
        if (!$user_id || !$amount){
            $this->log = "ERROR: Invalid Data, Exiting";
            return self::TASK_SUCCESS;
        }
        
        $oUser = User::find($user_id);
        if (!$oUser->exists){
            $this->log = "ERROR: Missing User $user_id, Exiting";
            return self::TASK_SUCCESS;
        }
        $oAccount = Account::lock($oUser->account_id, $iLocker);
        if (empty($oAccount)) {
            $this->log = "ERROR: Lock Account Error, Restore";
            return self::TASK_RESTORE;
        }
        if (!$bSucc = $oAccount->setProhibitAmount($amount)) {
            $this->log = 'Errors: ' . $oAccount->getValidationErrorString();
        }
        else{
            $this->log = 'Seted';
        }
        Account::unLock($oAccount->id, $iLocker, false);
        return $bSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;
    }

}
