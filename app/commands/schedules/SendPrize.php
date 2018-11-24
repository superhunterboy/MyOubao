<?php

/**
 * 发放奖金
 *
 * @author white
 */
class SendPrize extends MoneyBaseTask {
    protected $validTypes = ['commission'];

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'send');
//        pr($this->data);
        is_array($send_projects) or $send_projects = explode(',',$send_projects);
//        if (!in_array($send_type,$this->validTypes)){
//            $this->log = "ERROR: Invalid Money Type: $send_type ,Exiting";
//            return self::TASK_SUCCESS;
//        }

//        $sMoneyType   = Str::plural($send_type);
//        $sFunction    = 'send' . ucfirst($sMoneyType);
//        $sGetFunction = 'getUnSent' . ucfirst($sMoneyType) . 'Projects';
        $DB          = DB::connection();
        $iTotalCount = count($send_projects);
        $iSuccCount  = $iFailCount  = 0;
        $aErrnos     = [];
        $oProjects    = ManProject::getUnSentPrizesProjects($send_projects,100);
        if (!$iTotalCount = $oProjects->count()){
            $this->log = 'No Projects, Exiting ';
            return self::TASK_SUCCESS;
        }

//        $iTotalCount  = count($send_projects);
        foreach ($oProjects as $oProject){
            if (($iReturn = $this->sendPrizes($oProject,$DB)) > 0){
                $iSuccCount++;
            }
            else{
                $iFailCount++;
                if ($iReturn !== false) {
                    $aErrnos[$oProject->id] = $iReturn;
                }
            }
        }
        $this->log = " $iSuccCount sent, $iFailCount Didn't Sent, ";
        if ($iTotalCount == $iSuccCount){
            $this->log .= 'Finished Exiting ';
            return self::TASK_SUCCESS;
        }
        else{
            $this->log .= $iTotalCount - $iSuccCount . ' Remaining, Exiting';
            if ($iFailCount){
                $errorFiles = ['system','schedule','fund','account','lottery','issue'];
                $oMessage   = new Message($errorFiles);
                $this->log .= " Failed Projects: ";
                $aErrorMsgs = [];
                foreach ($aErrnos as $iProjectId => $iErrno){
                    $sMsg         = $oMessage->getResponseMsg($iErrno);
                    $aErrorMsgs[] = "$iProjectId $sMsg";
                }
                $this->log .= implode("\n",$aErrorMsgs);
                $this->log .= ' Exiting';
                unset($aErrorMsgs);
            }
            return self::TASK_RESTORE;
        }
    }

    /**
     * 派发奖金
     * @param Project $oProject
     * @param DB $DB
     * @return int
     */
    private function sendPrizes($oProject,$DB){
        if ($oProject->status_prize == ManProject::PRIZE_STATUS_SENT){
            return 1;
        }
//        if (!$oProject->lock(TRUE)){
//            return ManProject::ERRNO_LOCK_FAILED;
//        }
        $oAccount = Account::lock($oProject->account_id,$iLocker);
        if (empty($oAccount)){
            return Account::ERRNO_LOCK_FAILED;
        }
        $oUser = User::find($oProject->user_id);
        $oProject->setUser($oUser);
        $oProject->setAccount($oAccount);
        $DB->beginTransaction();
        if (($iReturn = $oProject->sendPrizes()) === true){
            $iReturn = $oProject->setPrizeSentStatus();
        }
//        $bSucc  = ($iCount = $oProject->sendPrizes()) && $oProject->setPrizeSentStatus();
//        file_put_contents('/tmp/sendprice',var_export($oProject->toArray(),true));
        if ($iReturn === true){
            $DB->commit();
            $this->addProfitTask('prize',$oProject->prize_sent_at,$oProject->user_id,$oProject->prize,$oProject->lottery_id,$oProject->issue);
        }
        else{
            $DB->rollback();
        }
        Account::unLock($oProject->account_id,$iLocker,false);
        $iReturn === true or $oProject->unlock(true);
        return $iReturn;
    }

    /**
     * 向任务队列追加销售额统计任务
     * @param string $sType
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @param int $iLotteryId
     * @param string $sIssue
     * @return bool
     */
//    protected function addProfitTask($sType, $sDate, $iUserId, $fAmount, $iLotteryId, $sIssue = null) {
//        $aTaskData = [
//            'type'    => $sType,
//            'user_id' => $iUserId,
//            'amount'  => $fAmount,
//            'date'    => substr($sDate,0,10),
//            'lottery_id' => $iLotteryId,
//            'issue'   => $sIssue,
//        ];
//        return BaseTask::addTask('StatUpdateProfit', $aTaskData, 'stat');
//    }
}
