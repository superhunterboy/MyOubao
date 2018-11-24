<?php

/**
 * 计奖
 * */
class CalculatePrize extends BaseTask {

    protected $Issue;
    protected $winningNumbers = [];
    protected $hasWatingTraceDetail;
    protected $betQueueIsEmpty;
    protected $delayOnRelease = 5;
    protected $wnNumberPath = null;
    protected $wnNumberFile = null;
    protected $wnNumberFile2 = null;
    
    protected function doCommand() {
        extract($this->data, EXTR_PREFIX_ALL, 'cal');
        $oLottery = Lottery::find($cal_lottery_id);

        if (empty($oLottery)) {
            $this->log = ' Lottery Missing, Exiting';
            return self::TASK_SUCCESS;
        }

        $oIssue = ManIssue::getIssueObject($cal_lottery_id, $cal_issue);
        if (!is_object($oIssue)) {
            $this->log = ' Issue Missing, Exiting';
            return self::TASK_SUCCESS;
        }

        $this->wnNumberPath = '/tmp/wnNumberOfWay/' . $cal_lottery_id . DS . date('Ym/d', $oIssue->end_time);
        if (!file_exists($this->wnNumberPath)) {
            @mkdir($this->wnNumberPath, 0777, true);
            @chmod($this->wnNumberPath, 0777);
        }
        $this->wnNumberFile = $this->wnNumberPath . DS . $oIssue->issue;
        $this->wnNumberFile2 = $this->wnNumberFile . '-name';
        $this->logBase .= ' Expire Time: ' . date('m-d H:i:s', $oIssue->end_time);
        $this->logBase .= ' Number: ' . $oIssue->wn_number;

        // 获得处于等待状态的追号预约数，检查投注线程数是否为空。此处为判断计奖任务的完成状态做准备
        $this->hasWatingTraceDetail = TraceDetail::getUnGenerateDetailCount($oIssue->lottery_id, $oIssue->issue);
        $this->betQueueIsEmpty = BetThread::isEmpty($oIssue->lottery_id, $oIssue->issue);

        // 如果号码处于被取消状态，则直接写为计奖已完成，以做好重新计奖的准备
        if ($oIssue->status == ManIssue::ISSUE_CODE_STATUS_CANCELED) {
            $this->log = 'Winning Number Canceled, Set To Finished';
            return $oIssue->setCalulated(true) ? self::TASK_SUCCESS : self::TASK_RESTORE;
        }
        // 如果号码状态不是已完成，则退出，并保持任务
        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_FINISHED) {
            $this->log = 'On sale, Exiting';
            return self::TASK_SUCCESS;
        }
//        pr($this->data[ 'issue' ]);
//        pr($oIssue->toArray());
        if ($oIssue->status_count == ManIssue::CALCULATE_FINISHED) {
            $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
            $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
            $this->log = 'Finished, Exiting';
            return self::TASK_SUCCESS;
        }
        // todo: 暂时取消锁，需要恢复
        if ($oIssue->lockCalculate()) {
            $this->Issue = $oIssue;
        } else {
            $this->log = 'Issue Lock Failed, Exiting';
//            pr($this->log);
            return self::TASK_KEEP;
        }
//exit;
        $aWnNumberOfMethods = $this->getWnNumberOfSeriesMethods($oLottery, $oIssue->wn_number, $aWnNumberOfMethodsByName);
//        pr($aWnNumberOfMethodsByName);
//        exit;
        $oIssue->status_prize != ManIssue::PRIZE_NONE or $this->setStartSendMoneyTask();
        $oIssue->status_trace_prj != ManIssue::TRACE_PRJ_NONE or $this->setStartTracePrjTask($oIssue);
//$this->setStartTracePrjTask($oIssue);
        if (!$iProjectCount = ManProject::getUnCalcutatedCount($oIssue->lottery_id, $oIssue->issue)) {
            if ($this->setCalculateFinished($oIssue, 0, $bFinished)) {
                if ($bFinished) {
                    $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
                    $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
                }
                return $bFinished ? self::TASK_SUCCESS : self::TASK_RESTORE;
            } else {
                return self::TASK_KEEP;
            }
        }

//        $oBasicWays = BasicWay::where('lottery_type','=',$oLottery->type)->get();
//        pr($oBasicWays->toArray());
//        exit;
        $bSucc = true;
        $aResult = [0, 0, 0];
        $oSeriesWays = SeriesWay::where('series_id', '=', $oLottery->series_id)->orderBy('id', 'asc')->get();

//        foreach ($oBasicWays as $oBasicWay){
//            $oSeriesWays = SeriesWay::where('series_id','=',$oLottery->series_id)->where('basic_way_id','=',$oBasicWay->id)->orderBy('id','asc')->get();
        foreach ($oSeriesWays as $oSeriesWay) {
            $sLogMsgOfWay = " Way: $oSeriesWay->id $oSeriesWay->name ";
//                pr($sLogMsgOfWay);
            $aWinningNumbers = & $oSeriesWay->getWinningNumber($aWnNumberOfMethods);
//                var_dump($aWinningNumbers);
//                var_dump($oSeriesWay->WinningNumber);
//                continue;
            $bSucc = $this->calculateProjectsOfWay($oSeriesWay, $oIssue, $aResultOfWay);
//                Log::info("$sLogMsgOfWay Projects: {$aResultOfWay[ 0 ]} Won: {$aResultOfWay[ 1 ]} Lost: {$aResultOfWay[ 2 ]} Message: {$aResultOfWay[ 3 ]}");
            $aResult[0] += $aResultOfWay[0];
            $aResult[1] += $aResultOfWay[1];
            $aResult[2] += $aResultOfWay[2];
            if (!$bSucc) {
                break 2;
            }
        }
//        }
//        pr(intval($bSucc));
//        exit;
        if ($bSucc) {
            if ($this->setCalculateFinished($oIssue, $aResult, $bFinished)) {
                if ($bFinished) {
                    $oIssue->status_prize == ManIssue::PRIZE_FINISHED or $this->setFinishedSendMoneyTask();
                    $oIssue->status_trace_prj == ManIssue::TRACE_PRJ_FINISHED or $this->setFinishedTracePrjTask();
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

    private function & getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber, & $aWnNumberOfMethodsByName) {
        $aWnNumberOfMethods = $this->_getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber);
        $aWnNumberOfMethodsByName = $this->_getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber, true);
//        pr($aWnNumberOfMethods1);
//        exit;
        file_put_contents($this->wnNumberFile, var_export($aWnNumberOfMethods, true));
        file_put_contents($this->wnNumberFile2, var_export($aWnNumberOfMethodsByName, true));
        return $aWnNumberOfMethods;
    }

    /**
     * 由中奖号码分析得出各投注方式的中奖号码数组
     * @param Lottery $oLottery
     * @param string $sFullWnNumber
     * @param bool $bNameKey
     * @return array &
     */
    private function & _getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber, $bNameKey = false) {
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
        $DB = DB::connection();
        if ($oSeriesWay->WinningNumber === false) {
            $sMsg = "Batch set unprizedProjcts ";
            $DB->beginTransaction();
            if (!$bSucc = ManProject::setLostOfWay($oIssue->wn_number, $oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id)) {
                $DB->rollback();
                $sMsg .= 'Failed';
            } else {
                $DB->commit();
                $this->setSendCommissionTaskOfWay($oSeriesWay, $oIssue);
                $this->setTraceTaskOfWay($oSeriesWay, $oIssue);
                $sMsg .= 'Success';
            }
            $aResult = [ $aTotalCount, 0, $aTotalCount, $sMsg];
            return $bSucc;
        }
//        pr($oProjects->toArray());
        $oProjects = ManProject::getUnCalculatedProjects($oIssue->lottery_id, $oIssue->issue, $oSeriesWay->id);
        $aPrizedOfBetNumbers = [];
        $aWonProjects = $aLostProjects = $aNeedStopTraces = $aNeedGenerateTrace = [];
//        $aUsers              = [];
        foreach ($oProjects as $oProject) {
            $this->addRedEnvelopes($oProject);
        }

        $bSucc = true;
        $DB->beginTransaction();

        foreach ($oProjects as $oProject) {
            if (!isset($oProject->display_bet_number)) {
                $oProject->display_bet_number = $oProject->getDisplayBetNumber();
                $bSucc = $oProject->save();

                if (!$bSucc) {
                    break;
                }
            }
        }

        if ($bSucc) {
            $DB->commit();
        } else {
            $DB->rollback();
            $sMsg = 'set display bet number failed';
            $aResult = array($aTotalCount, 0, 0, $sMsg);
            return false;
        }



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

    public function addRedEnvelopes($oProject) {
        $active_red_envelopes_status = SysConfig::readValue('active_red_envelopes_status');

        if ($active_red_envelopes_status) {
            $aActiveData = array('username' => $oProject->username, 'turnover' => $oProject->amount, 'user_id' => $oProject->user_id, 'lottery_id' => $oProject->lottery_id, 'way_id' => $oProject->way_id, 'issue' => $oProject->issue, 'project_id' => $oProject->id, 'title' => $oProject->title);
            $res = ActiveRedEnvelopeUser::ActiveUpdateRedEnvelope($aActiveData);
            $msg = array('date' => date('Y-m-d H:i:s'), 'request' => $aActiveData, 'result' => $res);
            !is_array($msg) || ($msg = var_export($msg, true));
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
        $this->setSendCommissionTaskOfProjectIds(array_merge($aWonProjects, $aLostProjects));
        $this->setTraceTask($aNeedStopTraces, $aNeedGenerateTrace);
    }

    /**
     * 新增派奖任务
     * @param bool $aWonProjects 中奖的注单ID
     */
    function setSendPrizeTask($aWonProjects) {
        if (empty($aWonProjects)) {
            return true;
        }
        return $this->pushJob('SendPrize', ['projects' => $aWonProjects], Config::get('schedule.send_prize'));
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
    function setSendCommissionTaskOfProjectIds($aProjectIds) {
//        pr('add commission task: ' . var_export($aProjectIds,true));
        return $this->pushJob('SendCommission', ['projects' => $aProjectIds], Config::get('schedule.send_commission'));
    }

    protected function setCalculateFinished($oIssue, $aPrjCount, &$bFinished) {
        $bFinished = $this->betQueueIsEmpty && ($this->hasWatingTraceDetail === 0);

        if ($bSucc = $oIssue->setCalulated($bFinished)) {
            is_array($aPrjCount) or $aPrjCount = [ $aPrjCount, 0, 0];
            $this->log = " : {$aPrjCount[0]} Calculated, Won: {$aPrjCount[1]}, Lost: {$aPrjCount[2]}";
//            $bFinished ? $this->setFinished($oJob,$sLogMsg) : $this->setRelease($oJob,$sLogMsg);
//            if (!$bFinished){
//                $this->setRelease($oJob,$sLogMsg);
//            }
//            else{
//                $this->setFinished($oJob,$sLogMsg);
//            }
        }
        if (!$bFinished) {
            if (!$this->betQueueIsEmpty) {
                $this->log .= ", Bet Queue Is Not Empty";
            }
            if ($this->hasWatingTraceDetail) {
                $this->log .= ", Has $this->hasWatingTraceDetail Traces Details No Projects";
            }
        }
        $this->log .= ', Finished, Exiting';
        return $bSucc;
    }

    protected function setStartSendMoneyTask() {
        return $this->pushJob('StartSendMoney', $this->data, Config::get('schedule.send_money'));
    }

    protected function setFinishedSendMoneyTask() {
        pr('money finished');
        return $this->pushJob('FinishSendMoney', $this->data, Config::get('schedule.send_money'));
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
        return $this->pushJob('FinishTracePrj', $this->data, Config::get('schedule.trace'));
    }

    protected function compileOtherTasks() {
        $this->otherTasks[] = [
            'command' => 'SetWithdrawable',
            'queue' => 'account',
            'data' => [
                'lottery_id' => $this->data['lottery_id'],
                'issue' => $this->data['issue']
            ]
        ];
    }

    /**
     * 自动解锁
     */
    public function __destruct() {
        parent::__destruct();
        if ($this->Issue && $this->Issue->status_count = ManIssue::CALCULATE_PROCESSING) {
            ManIssue::unlockCalculate($this->Issue->lottery_id, $this->Issue->issue, $this->Issue->locker, false);
        }
    }

}
