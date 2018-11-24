<?php

/**
 * 设置指定奖期的派奖和佣金派发状态为已完成
 *
 * @author frank
 */
class FinishSendMoney extends BaseTask {

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'cal');

        $oLottery = ManLottery::find($cal_lottery_id);
        if (empty($oLottery)){
            $this->log = ' Lottery Missing, Exiting';
            return self::TASK_SUCCESS;
        }
//        $sLogMsg = preg_replace('/(Lottery:) [\d]+/','$1 ' . $oLottery->name,$sLogMsg);
        $oIssue = ManIssue::getIssueObject($cal_lottery_id,$cal_issue);
        if (!is_object($oIssue)){
            $this->log = ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }
        $this->logBase .= ' Expire Time: ' . date('H:i:s',$oIssue->end_time);
        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_FINISHED){
            $this->log = 'On sale, Exiting';
            return self::TASK_RESTORE;
        }
        if ($oIssue->status_count == ManIssue::CALCULATE_NONE){
            $this->log = 'Issue Status Error, Exiting';
            return self::TASK_RESTORE;
        }

        $DB                  = DB::connection();
        $DB->beginTransaction();
        $bFinishedPrize      = !PrjPrizeSet::getCountOfIssue($this->data[ 'lottery_id' ],$this->data[ 'issue' ],PrjPrizeSet::STATUS_WAIT);
        $oIssue->setPrizeFinishStatus($bFinishedPrize);
        $this->log      = 'Prize ' . ($bFinishedPrize ? 'Finished' : 'Partial Finished') . ',';
        $bFinishedCommission = !Commission::getCountOfIssue($this->data[ 'lottery_id' ],$this->data[ 'issue' ],Commission::STATUS_WAIT);
        $oIssue->setCommissionFinishStatus($bFinishedCommission);
        $this->log .= ' Commission ' . ($bFinishedCommission ? 'Finished' : 'Partial Finished') . ',';
        $bSucc               = $bFinishedPrize && $bFinishedCommission;
        $bSucc ? $DB->commit() : $DB->rollback();
        return $bSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;
        
//        $bSuccPrize      = $oIssue->setPrizeProcessing();
//        pr($bSuccPrize);
//        $this->log       = 'Prize ' . ($bSuccPrize ? 'Finished' : 'Partial Finished') . ',';
//        $bSuccCommission = $oIssue->setCommissionProcessing();
//        pr($bSuccCommission);
//        $this->log .= ' Commission ' . ($bSuccCommission ? 'Finished' : 'Partial Finished') . ',';
//        pr($bSucc);
//        $bSucc ? $DB->commit() : $DB->rollback();
//        return $bSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;
    }

}
