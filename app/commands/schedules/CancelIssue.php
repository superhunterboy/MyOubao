<?php

/**
 * 撤销此定奖期内的所有正常注单
 */
class CancelIssue extends BaseTask {

    protected $Issue;
    protected $pageSize = 100;
    protected $errorFiles = ['system','bet','fund','account','lottery','issue','seriesway'];
    protected $hasWatingTraceDetail;
    protected $betQueueIsEmpty;

//    protected $prefix = 'can';

    protected function doCommand(){
        $oLottery = ManLottery::find($this->data[ 'lottery_id' ]);
        if (!$oLottery->exists){
            $this->log = 'Missing Lottery';
            return self::TASK_SUCCESS;
        }
        $oIssue = ManIssue::getIssueObject($this->data[ 'lottery_id' ],$this->data[ 'issue' ]);
        if (!$oIssue->exists){
            $this->log = 'Missing Issue';
            return self::TASK_SUCCESS;
        }

        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_CANCELED){
            $this->log = 'Wrong WINNING NUMBER Status';
            return self::TASK_RESTORE;
        }

        // 获得处于等待状态的追号预约数，检查投注线程数是否为空。此处为判断计奖任务的完成状态做准备
        $this->hasWatingTraceDetail = TraceDetail::getUnGenerateDetailCount($oIssue->lottery_id,$oIssue->issue);
        $this->betQueueIsEmpty = BetThread::isEmpty($oIssue->lottery_id,$oIssue->issue);

        $i               = 0;
        $aFailedProjects      = $aNeedCreatePrjTraces = [];
        $DB              = DB::connection();
        $oMessage        = new Message($this->errorFiles);

        isset($this->data['begin_time']) or $this->data['begin_time'] = null;
        do{
            $oProjects = ManProject::getUnCalculatedProjects($this->data[ 'lottery_id' ],$this->data[ 'issue' ],null,$this->data['begin_time'], $this->pageSize * $i++,$this->pageSize);
            foreach ($oProjects as $oProject){
                if ($oProject->status != ManProject::STATUS_NORMAL){
                    continue;
                }
                $oAccount = Account::lock($oProject->account_id,$iLocker);
                if (empty($oAccount)){
                    $aFailedProjects[ $oProject->id ] = $oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED);
                    continue;
//                    return $Redirect->with('error',$oMessage->getResponseMsg(Account::ERRNO_LOCK_FAILED));
                }
                $oProject->setAccount($oAccount);
                $DB->beginTransaction();
                if (($iReturn = $oProject->drop(Project::DROP_BY_SYSTEM)) != ManProject::ERRNO_DROP_SUCCESS){
                    $DB->rollback();
                    $aFailedProjects[ $oProject->id ] = $oMessage->getResponseMsg($iReturn);
                }
                else{
                    $DB->commit();
                    $oProject->addTurnoverStatTask(false);    // 建立销售量更新任务
                    // 判断是否是追号单，如果是，则记录下trace_id,以便发起生成追号单任务
                    if ($oProject->trace_id){
                        $aNeedCreatePrjTraces[] = $oProject->trace_id;
                    }
                }
                Account::unLock($oProject->account_id,$iLocker,false);
            }
        } while ($oProjects->count());
        // 获取用户自行撤销的单子的追号ID
        if ($aTraceIdsOfDropedPrjs = & ManProject::getTraceIdArrayOfDroped($oIssue->lottery_id,$oIssue->issue)){
            $aNeedCreatePrjTraces = array_merge($aNeedCreatePrjTraces, $aTraceIdsOfDropedPrjs);
        }
        
        if ($aNeedCreatePrjTraces){
            $this->setTraceTask(null,$aNeedCreatePrjTraces);
        }
        $this->log = ($bSucc     = empty($aFailedProjects)) ? 'Successful' : 'Failed Projects:' . var_export($aFailedProjects,true);
        $bSucc = $bSucc && !$this->hasWatingTraceDetail && $this->betQueueIsEmpty;
        return $bSucc ? self::TASK_SUCCESS : self::TASK_RESTORE;
    }

    /**
     * 进行基础数据检查
     * @return type
     */
    protected function checkData(){
        return $this->data[ 'lottery_id' ] > 0 && $this->data[ 'issue' ];
    }

}
