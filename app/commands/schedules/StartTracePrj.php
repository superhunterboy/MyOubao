<?php

/**
 * 设置指定奖期的追号单生成状态为进行中
 *
 * @author frank
 */
class StartTracePrj extends BaseTask {

    protected function doCommand(){
        extract($this->data,EXTR_PREFIX_ALL,'cal');
//        $this->job->delete();
//        exit;
        $oLottery = Lottery::find($cal_lottery_id);
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

        $DB        = DB::connection();
        $DB->beginTransaction();
        $bSucc     = $oIssue->setTracePrjProcessing();
        $bSucc ? $DB->commit() : $DB->rollback();
        $this->log = 'Trace Prj Create ' . ($bSucc ? 'Started' : 'Start Failed') . ',';
//        return $bSucc ? self::TASK_SUCCESS : self::TASK_KEEP;
        return self::TASK_SUCCESS;
    }

}
