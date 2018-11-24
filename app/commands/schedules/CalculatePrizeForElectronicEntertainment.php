<?php

/**
 * 计奖
 *
 * */
class CalculatePrizeForElectronicEntertainment extends BaseTask {

    protected $Issue;
    protected $winningNumbers = [];
    protected $hasWatingTraceDetail;
    protected $betQueueIsEmpty;

    protected function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'cal');
        $oLottery = Lottery::find($cal_lottery_id);
        if (empty($oLottery)) {
            $this->log = ' Lottery Missing, Exiting';
            return self::TASK_SUCCESS;
        }
//        $sLogMsg = preg_replace('/(Lottery:) [\d]+/','$1 ' . $oLottery->name,$sLogMsg);
        $oIssue = ManIssue::getIssueObject($cal_lottery_id, $cal_issue);

        //电子娱乐没有追号,追号状态直接置为已完成
        $oIssue->status_trace_prj = ManIssue::TRACE_PRJ_FINISHED;
        $oIssue->save();

        if (!is_object($oIssue)) {
            $this->log = ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }
        $this->logBase .= ' Expire Time: ' . date('m-d H:i:s', $oIssue->end_time);
        $this->logBase .= ' Number: ' . $oIssue->wn_number;

        // 获得处于等待状态的追号预约数，检查投注线程数是否为空。此处为判断计奖任务的完成状态做准备
        $this->hasWatingTraceDetail = TraceDetail::getUnGenerateDetailCount($oIssue->lottery_id, $oIssue->issue);
        $this->betQueueIsEmpty = BetThread::isEmpty($oIssue->lottery_id, $oIssue->issue);

        // 如果号码状态不是已完成，则退出，并保持任务
        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_FINISHED) {
            $this->log = 'On sale, Exiting';
            return self::TASK_KEEP;
        }

        //如果没有投注，则派奖完成
        if (!Project::getCount(['lottery_id' => $cal_lottery_id, 'issue' => $cal_issue])) {
            $this->setFinishedSendMoneyTask();
        }

        // 如果号码处于被取消状态，则直接写为计奖已完成，以做好重新计奖的准备
        if ($oIssue->status == ManIssue::ISSUE_CODE_STATUS_CANCELED) {
            $this->log = 'Winning Number Canceled, Set To Finished';
            return $oIssue->setCalulated(true) ? self::TASK_SUCCESS : self::TASK_RESTORE;
        }


        if ($oIssue->status_count == ManIssue::CALCULATE_FINISHED) {
            $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
//            $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
            $this->log = 'Finished, Exiting';
            return self::TASK_SUCCESS;
        }
        if ($oIssue->lockCalculate()) {
            $this->Issue = $oIssue;
        } else {
            $this->log = 'Issue Lock Failed, Exiting';
            ManIssue::unlockCalculate($oIssue->lottery_id, $oIssue->issue, $oIssue->locker, false);
//            return self::TASK_SUCCESS;
            return self::TASK_KEEP;
        }

        $aWnNumberOfMethods = $this->getWnNumberOfSeriesMethods($oLottery, $oIssue->wn_number);
        $aWnNumberOfMethods1 = $this->getWnNumberOfSeriesMethods($oLottery, $oIssue->wn_number, true);
        $oIssue->status_prize != ManIssue::PRIZE_NONE or $this->setStartSendMoneyTask();
//        $oIssue->status_trace_prj != ManIssue::TRACE_PRJ_NONE or $this->setStartTracePrjTask($oIssue);

        if (!$iProjectCount = ManProject::getUnCalcutatedCount($oIssue->lottery_id, $oIssue->issue)) {
            if ($this->setCalculateFinished($oIssue, 0, $bFinished)) {
                if ($bFinished) {
                    $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
//                    $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
                }
                return $bFinished ? self::TASK_SUCCESS : self::TASK_RESTORE;
            } else {
                return self::TASK_KEEP;
            }
        }

        $oBasicWays = BasicWay::where('lottery_type', '=', $oLottery->type)->get();

        $bSucc = true;
        $aResult = [0, 0, 0];
        foreach ($oBasicWays as $oBasicWay) {
            $oSeriesWays = SeriesWay::where('series_id', '=', $oLottery->series_id)->where('basic_way_id', '=', $oBasicWay->id)->orderBy('id', 'asc')->get();
            foreach ($oSeriesWays as $oSeriesWay) {
                $sLogMsgOfWay = " Way: $oSeriesWay->id $oSeriesWay->name ";
                $aWinningNumbers = & $oSeriesWay->getWinningNumber($aWnNumberOfMethods);
                $bSucc = $this->calculateProjectsOfWay($oSeriesWay, $oIssue, $aResultOfWay);
//                Log::info("$sLogMsgOfWay Projects: {$aResultOfWay[ 0 ]} Won: {$aResultOfWay[ 1 ]} Lost: {$aResultOfWay[ 2 ]} Message: {$aResultOfWay[ 3 ]}");
                $aResult[0] += $aResultOfWay[0];
                $aResult[1] += $aResultOfWay[1];
                $aResult[2] += $aResultOfWay[2];
                if (!$bSucc) {
                    break 2;
                }
            }
        }
        if ($bSucc) {
            if ($this->setCalculateFinished($oIssue, $aResult, $bFinished)) {
                if ($bFinished) {
                    $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
//                    $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
                }
                return $bFinished ? self::TASK_SUCCESS : self::TASK_RESTORE;
            } else {
                return self::TASK_KEEP;
            }
//            $this->setCalculateFinished($oIssue,$sLogMsg,$aResult);
//            return 2;
        }
//        $this->winningNumbers = & $aWnNumberOfWays;
//        unset($aWnNumberOfWays);
//        pr($this->winningNumbers);
    }

    /**
     * 由中奖号码分析得出各投注方式的中奖号码数组
     * @param Lottery $oLottery
     * @param string $sFullWnNumber
     * @param bool $bNameKey
     * @return array &
     */
    function & getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber, $bNameKey = false) {
//        pr($oLottery);
//        pr($oLottery->toArray());
//        exit;
        $oSeriesMethods = SeriesMethod::where('series_id', '=', $oLottery->series_id)->get();
        $aWnNumbers = [];
        $sKeyColumn = $bNameKey ? 'name' : 'id';
        foreach ($oSeriesMethods as $oSeriesMethod) {
            $aWnNumbers[$oSeriesMethod->$sKeyColumn] = $oSeriesMethod->getWinningNumber($sFullWnNumber);
        }
        return $aWnNumbers;
    }

    /**
     * 对指定SeriesWay的所有注单计奖
     *
     * @param Issue $oIssue
     * @return boolean
     */
    public function calculateProjectsOfWay($oSeriesWay, $oIssue, & $aResult) {
//        pr($this->name);
        if (!$aTotalCount = ManProject::getUnCalcutatedCount($oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id)) {
            $aResult = [0, 0, 0, "Dont have projects"];
            return true;
        }
        $oProjects = ManProject::getUnCalculatedProjects($oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id);

        foreach ($oProjects as $oProject) {
            $this->addRedEnvelopes($oProject);
        }
        $DB = DB::connection();
        
        //开奖后写入display_bet_number start
        $bSucc = true;
        $DB->beginTransaction();
        foreach ($oProjects as $oProject) {
            if (!isset($oProject->display_bet_number)){
                $oProject->display_bet_number = $oProject->getDisplayBetNumber();
                $bSucc = $oProject->save();
                if (!$bSucc){
                    break;
                }
            }
        }
        if ($bSucc){
            $DB->commit();
        } else {
            $DB->rollback();
            $sMsg = 'set display bet number failed';
            $aResult = [ $aTotalCount, 0, 0, $sMsg];
            return false;
        }
        //开奖后写入display_bet_number end
        
        if ($oSeriesWay->WinningNumber === false) {
            $sMsg = "Batch set unprizedProjcts ";
            $DB->beginTransaction();
            if (!$bSucc = ManProject::setLostOfWay($oIssue->wn_number, $oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id)) {
                $DB->rollback();
                $sMsg .= 'Failed';
            } else {
                $DB->commit();
//                $this->setSendCommissionTaskOfWay($oSeriesWay,$oIssue);
//                $this->setTraceTaskOfWay($oSeriesWay, $oIssue);

                $sMsg .= 'Success';
            }
            $aResult = [ $aTotalCount, 0, $aTotalCount, $sMsg];
            return $bSucc;
        }
//        pr($oProjects->toArray());
        $aPrizedOfBetNumbers = [];
        $aWonProjects = $aLostProjects = $aNeedStopTraces = $aNeedGenerateTrace = [];
//        $aUsers              = [];
        foreach ($oProjects as $oProject) {
            $this->calculateProject($DB, $oSeriesWay, $oIssue, $oProject, $aPrizedOfBetNumbers, $aWonProjects, $aLostProjects, $aNeedStopTraces, $aNeedGenerateTrace);
        }
//        empty($aWonProjects) or $this->setSendPrizeTask($aWonProjects);
        $bSucc = true;
        $sMessage = '';
        if ($aLostProjects) {
            $bSucc = ManProject::setLostOfIds($oIssue->wn_number, $aLostProjects);
            $sMessage .= 'Batch set Lost ' . ($bSucc ? 'Success' : 'Failed');
        }
//        $sMessage = $bSucc ? "Batch set unprizedProjcts Success" : "Batch set unprizedProjcts Failed";
        $this->setTask($aWonProjects, $aLostProjects, $aNeedStopTraces, $aNeedGenerateTrace);
        $aResult = [ $oProjects->count(), count($aWonProjects), count($aLostProjects), $sMessage];
//        $this->setSendCommissionTaskOfProjectIds(array_merge($aWonProjects,$aLostProjects));
//        $this->setSendCommissionTaskOfWay($oSeriesWay,$oIssue);
        return $bSucc;
    }

    /**
     * 对注单计奖
     * @param DB $DB
     * @param SeriesWay $oSeriesWay
     * @param Issue $oIssue
     * @param Project $oProject
     * @param array & $aPrizedOfBetNumbers
     * @param array & $aWonProjects
     * @param array & $aLostProjects
     * @param array & $aNeedStopTraces
     * @param array & $aNeedGenerateTrace
     * @return array &
     */
    private function & calculateProject($DB, $oSeriesWay, $oIssue, $oProject, & $aPrizedOfBetNumbers, & $aWonProjects, & $aLostProjects, & $aNeedStopTraces, & $aNeedGenerateTrace) {
        $sBetNumber = $oProject->bet_number;
        $sPosition = $oProject->position;
        $sKey = md5($sBetNumber . $sPosition);
        if (array_key_exists($sKey, $aPrizedOfBetNumbers)) {
            $aPrized = $aPrizedOfBetNumbers[$sKey];
        } else {
            $aPrized = $oSeriesWay->checkPrize($sBetNumber, $sPosition);
            !$aPrized or $aPrizedOfBetNumbers[md5($sBetNumber)] = $aPrized;
        }
//            pr($aPrized);
//            exit;
//            pr($oProject->toArray());



        if ($aPrized) {
            $oProject = ManProject::find($oProject->id);
            $DB->beginTransaction();
//            $aPrizeDetailOfPrj = [];
            if ($bSucc = $oProject->setWon($oIssue->wn_number, $aPrized, $aPrizeDetailOfPrj, $oTrace)) {
                $DB->commit();
//                $aPrizeDetails  = array_merge($aPrizeDetails,$aPrizeDetailOfPrj);
                $aWonProjects[] = $oProject->id;
                if ($oTrace) {
                    $oTrace->stop_on_won ? $aNeedStopTraces[] = $oTrace->id : $aNeedGenerateTrace[] = $oTrace->id;
                }
            } else {
                $DB->rollback();
            }
        } else {
            $aLostProjects [] = $oProject->id;
            empty($oProject->trace_id) or $aNeedGenerateTrace[] = $oProject->trace_id;
        }
        return $aPrizeDetailOfPrj;
    }

    /*     * *
     * 抢红包活动
     */

    public function addRedEnvelopes($oProject) {

//         //增加活动记录
//        
//            @file_put_contents('/tmp/redEnvelope.txt',"starting\n\n\n", FILE_APPEND);
        $active_red_envelopes_status = SysConfig::readValue('active_red_envelopes_status');
        if ($active_red_envelopes_status) {
            //活动销量统计
            $aActiveData = [
                'username' => $oProject->username,
                'turnover' => $oProject->amount,
                'user_id' => $oProject->user_id,
                'lottery_id' => $oProject->lottery_id,
                'way_id' => $oProject->way_id,
                'issue' => $oProject->issue,
                'project_id'=>$oProject->id,
                'title'=>$oProject->title,
            ];
            $res = ActiveRedEnvelopeUser::ActiveUpdateRedEnvelope($aActiveData);
            $msg = [ 'date' => date('Y-m-d H:i:s'), 'request' => $aActiveData, 'result' => $res];
            !is_array($msg) or $msg = var_export($msg, true);
            @file_put_contents('/tmp/redEnvelope.txt', $msg . "\n ending×××××××××××××××××××××××××× \n\n", FILE_APPEND);
        }
        return true;
    }

    /**
     * 向队列中增加派奖、派佣金、终止追号、生成追号单等任务
     * @param array $aWonProjects
     * @param array $aLostProjects
     * @param array $aNeedStopTraces
     * @param array $aNeedGenerateTrace
     * @return void
     */
    function setTask($aWonProjects, $aLostProjects, $aNeedStopTraces, $aNeedGenerateTrace) {
        $this->setSendPrizeTask($aWonProjects);
//        $this->setSendCommissionTaskOfProjectIds(array_merge($aWonProjects,$aLostProjects));
//        $this->setTraceTask($aNeedStopTraces, $aNeedGenerateTrace);
    }

    /**
     * 新增派奖任务
     * @param bool $aWonProjects 中奖的注单ID
     */
    function setSendPrizeTask($aWonProjects) {
        if (empty($aWonProjects)) {
            return true;
        }
        return $this->pushJob('SendMoneyForElectronicEntertainment', [ 'type' => 'prize', 'projects' => $aWonProjects], Config::get('schedule.send_money_electronic_entertainment'));
    }

    /**
     * 新增派佣金任务:将指定奖期、指定投注方式下的所有注单均加入任务
     * @param SeriesWay $oSeriesWay
     * @param Issue $oIssue
     * @return bool
     */
    function setSendCommissionTaskOfWay($oSeriesWay, $oIssue) {
        $aProjectIds = ManProject::getValidProjectIds($oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id);
        return $this->setSendCommissionTaskOfProjectIds($aProjectIds);
    }

    /**
     * 新增生成追号注单任务:将指定奖期、指定投注方式下的所有相关的追号任务均加入任务
     * @param SeriesWay $oSeriesWay
     * @param Issue $oIssue
     * @return bool
     */
    function setTraceTaskOfWay($oSeriesWay, $oIssue) {
        $aTraces = ManProject::getLostTraceIds($oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id);
        return $this->setTraceTask(null, $aTraces);
    }

    /**
     * 新增派佣金任务:将给定ID的所有注单均加入任务
     * @param array $aProjectIds
     * @return bool
     */
//    function setSendCommissionTaskOfProjectIds($aProjectIds){
//        is_array($aProjectIds) or $aProjectIds = explode(",",$aProjectIds);
//
////        return
////        pr('add commission task: ' . var_export($aProjectIds,true));
////        return $this->pushJob('SendMoney',[ 'type' => 'commission','projects' => $aProjectIds],Config::get('schedule.send_money'));
//    }

    /**
     * 设置奖期的计奖状态
     * @param Issue $oIssue
     * @param array $aPrjCount
     * @param bool & $bFinished
     * @return bool
     */
    protected function setCalculateFinished($oIssue, $aPrjCount, & $bFinished) {
//        $oBeforeIssue    = $oIssue->getFirstUnCalculatedIssueBeforeIssue();
//        $bAllDoBetFinished = BetThread::isEmpty($oIssue->lottery_id,$oIssue->issue);
//        pr($bAllDoBetFinished);
//        pr(TraceDetail::getUnGenerateDetailCount($oIssue->lottery_id,$oIssue->issue));
//        && !$oIssue->getFirstUnCalculatedIssueBeforeIssue();
        $bFinished = $this->betQueueIsEmpty && ($this->hasWatingTraceDetail === 0);     // 如果投注队列不为空或等待的预约数不为0，则当期计奖状态为“部分完成”
//        $bPartial          = !empty($oBeforeIssue) || !$bAllBetFinished;
        if ($bSucc = $oIssue->setCalulated($bFinished)) {
            is_array($aPrjCount) or $aPrjCount = [ $aPrjCount, 0, 0];
            $this->log = " : {$aPrjCount[0]} Calculated, Won: {$aPrjCount[1]}, Lost: {$aPrjCount[2]}, Finished, Exiting";
//            $bFinished ? $this->setFinished($oJob,$sLogMsg) : $this->setRelease($oJob,$sLogMsg);
//            if (!$bFinished){
//                $this->setRelease($oJob,$sLogMsg);
//            }
//            else{
//                $this->setFinished($oJob,$sLogMsg);
//            }
        }
        return $bSucc;
    }

    protected function setStartSendMoneyTask() {
        return $this->pushJob('StartSendMoneyForElectronicEntertainment', $this->data, Config::get('schedule.send_money_electronic_entertainment'));
    }

    protected function setFinishedSendMoneyTask() {
        return $this->pushJob('FinishSendMoneyForElectronicEntertainment', $this->data, Config::get('schedule.send_money_electronic_entertainment'));
    }

    protected function setStartTracePrjTask($oIssue) {
//        pr($oIssue->issue);
        $aTraceIds = ManProject::getTraceIdArrayOfDroped($oIssue->lottery_id, $oIssue->issue);
//        pr($aTraceIds);
        if ($aTraceIds = ManProject::getTraceIdArrayOfDroped($oIssue->lottery_id, $oIssue->issue)) {
            $this->setTraceTask(null, $aTraceIds);
        }
        return $this->pushJob('StartTracePrj', $this->data, Config::get('schedule.trace'));
    }

    protected function setFinishedTracePrjTask() {
        extract($this->data);
        file_put_contents('/tmp/debug',json_encode($this->data)."\n\r",FILE_APPEND);
        $oIssue = ManIssue::getIssueObject($lottery_id,$issue);
        $DB        = DB::connection();
        $DB->beginTransaction();
        $bSucc     = $oIssue->setTracePrjFinishStatus();
        $bSucc ? $DB->commit() : $DB->rollback();
        return $bSucc;
//        return $this->pushJob('FinishTracePrj', $this->data, Config::get('schedule.trace'));
    }

//    protected function compileOtherTasks() {
//        $this->otherTasks[] = [
//            'command' => 'SetWithdrawable',
//            'queue'   => 'account',
//            'data'    => [
//                'lottery_id' => $this->data['lottery_id'],
//                'issue'      => $this->data['issue']
//            ]
//        ];
//    }

    /**
     * 自动解锁
     */
    public function __destruct() {
        $this->log = 'unlock';
        parent::__destruct();
        if ($this->Issue && $this->Issue->status_count = ManIssue::CALCULATE_PROCESSING) {
            ManIssue::unlockCalculate($this->Issue->lottery_id, $this->Issue->issue, $this->Issue->locker, false);
        }
    }

}
