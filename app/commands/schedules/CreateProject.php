<?php

class CreateProject extends BaseTask {

    private $accountLocker;
    private $account_id;

    protected function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'ctp');
        $oTrace = ManTrace::find($ctp_trace_id);

        if (empty($oTrace)) {
            $this->log .= ' Missing Trace';
            return self::TASK_SUCCESS;
        }

        if ($oTrace->status != ManTrace::STATUS_RUNNING) {
            $this->log .= ' Trace Not Running';
            return self::TASK_SUCCESS;
        }

        $oUser = User::find($oTrace->user_id);
        $sIssue = $oTrace->getNextIssue();
        $oOnSaleIssue = ManIssue::getOnSaleIssue($oTrace->lottery_id);

        if ($oOnSaleIssue->issue < $sIssue) {
            $this->log .= ' Too Early';
            return self::TASK_SUCCESS;
        }

        $oAccount = Account::lock($oTrace->account_id, $iLocker);

        if (empty($oAccount)) {
            $this->log .= ' Lock Account Failed';
            return self::TASK_RESTORE;
        }

        $this->account_id = $oTrace->account_id;
        $this->accountLocker = $iLocker;
        BetThread::addThread($oTrace->lottery_id, $sIssue, $iLocker);
        $oTrace->setAccount($oAccount);
        $oTrace->setUser($oUser);
        $DB = DB::connection();
        $DB->beginTransaction();
        $mReturn = $oTrace->generateProjectOfIssue();
        $bSucc = is_object($mReturn) || ($mReturn == Trace::ERRNO_PRJ_GERENATE_FAILED_NO_DETAIL);

        if ($bSucc) {
            $DB->commit();

            if (is_object($mReturn)) {
                $mReturn->setCommited();
                $this->log .= ' Success Project ' . $mReturn->id;
            } else {
                $this->log .= ' No Detail';
            }
        } else {
            $DB->rollback();
            $this->log .= ' ERROR: ' . $mReturn;
        }

        Account::unlock($oTrace->account_id, $iLocker, false);
        BetThread::deleteThread($this->accountLocker);
        $this->accountLocker = NULL;
        return $bSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;
    }

    public function __destruct() {
        if ($this->accountLocker) {
            Account::unLock($this->account_id, $this->accountLocker, false);
            BetThread::deleteThread($this->accountLocker);
        }

        parent::__destruct();
    }

}

?>