<?php

/**
 * 检查指定的用户资金账户，如果加锁者已停止运行，则强制解开，如果失败，则会恢复任务
 *
 * @author frank
 */
class ReleaseDeadAccountLock extends BaseTask {

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'acc');

        if (!$acc_id) return self::TASK_SUCCESS;

        isset($acc_locker) or $acc_locker = null;

        $iReturn = Account::releaseDeadLock($acc_id,$acc_locker);
        $this->log = " $iReturn: " . Account::$releaseDeadLockMessages[$iReturn];
        return ($iReturn == Account::RELEASE_DEAD_LOCK_FAILED) ? self::TASK_RESTORE : self::TASK_SUCCESS;
    }

}
