<?php

/**
 * generate winning-number for self lotteries
 *
 * @author white
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateNumber extends BaseCommand {

    protected $sFileName = 'GenerateNumber';
    protected $name = 'number:generate';
    protected $description = 'generate winning-number for self lotteries';
    public $writeTxtLog = true;
    protected $tryTimes = 15;
    protected $betweenSeconds = 0.5;

    protected function fire() {
        $this->logFileParam[] = $this->params['lottery_id'] = $this->argument('lottery_id');
        $this->params['count'] = $this->argument('count');
        parent::fire();
    }

    public function doCommand(& $sMsg = null) {
        $iLotteryId = $this->params['lottery_id'];
        $iCount = $this->params['count'];
//        pr($iLotteryId);
//        pr($iCount);
        $oLottery = ManLottery::find($iLotteryId);
        if (!$oLottery->is_self) {
            $this->writeLog($oLottery->name . ' Is Not Self ');
            exit;
        }
//        pr($oLottery->toArray());
//        $oToolIssue = new ManIssue;
//        sleep(1);
        $this->writeLog('Generate Winning Number for ' . $oLottery->name);
        $i = 0;
        do {
            !$i or usleep($this->betweenSeconds * 1000000);
            $oIssues = ManIssue::getNonNumberIssues($iLotteryId, $iCount);
//            pr($oIssues->count());
        } while (!$oIssues->count() && ( ++$i < $this->tryTimes));
        if (!$oIssues->count()) {
            $this->writeLog('No Issue, Exiting');
            exit;
        }
        foreach ($oIssues as $oIssue) {
            $this->writeLog('Issue: ' . $oIssue->issue);
            $this->writeLog('End Time: ' . $oIssue->end_time2);
            if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_WAIT_CODE) {
                $this->writeLog('Status Error, Exiting');
                continue;
            }
            if (!in_array($oLottery->series_id, [18]) && ($oIssue->issue % 1 == 0 || ($oIssue->issue % 1 == 0 && $this->checkBlackListBet($oIssue->lottery_id, $oIssue->issue))) || $oIssue->lottery_id == 11) {
                $sCode = $this->_filterCode($oLottery, $oIssue);
            } else {
                $sCode = $oLottery->compileWinningNumber();
            }
            $this->writeLog('Number: ' . $sCode);
            if (!$oLottery->checkWinningNumber($sCode)) {
                $this->writeLog('InValud Number, Exiting');
                continue;
            }
            $bSucc = $oIssue->setWinningNumber($sCode) === true;
            $this->writeLog('Set Wn Number: ' . ($bSucc ? 'true' : 'false') . ', issue=' . $oIssue->issue . ', code=' . $sCode);
            if ($bSucc === true) {
                $oIssue->setCalculateTask();
                $oIssue->updateWnNumberCache();
            }
        }
    }

    private function _filterCode($oLottery, $oIssue) {
        $aCodes = [];
        $aProjects = ManProject::getUnCalculatedProjects($oLottery->id, $oIssue->issue, null, null, null, null, 0, 0);
        $fFinalPrize = 0;
        $sFinalCode = '';
        for ($i = 0; $i < 4; $i++) {
            Log::info('lottery id = ' . $oLottery->id . ', issue=' . $oIssue->issue);
            $sCode = $oLottery->compileWinningNumber();
            $aWnNumberOfMethods = $this->_getWnNumberOfSeriesMethods($oLottery, $sCode);
            $aSeriesWays = [];
            $DB = DB::connection();
            $fTotalPrize = 0;
            foreach ($aProjects as $oProject) {
                if (key_exists($oProject->way_id, $aSeriesWays)) {
                    $oSeriesWay = $aSeriesWays[$oProject->way_id];
                } else {
                    $oSeriesWay = SeriesWay::find($oProject->way_id);
                    foreach ($oSeriesWay->series_method_ids as $iSeriesMethodId) {
                        $oSeriesMethod = SeriesMethod::find($iSeriesMethodId);
                        $aWinningNumbers = & $oSeriesWay->getWinningNumber($aWnNumberOfMethods);
                    }
                    $aSeriesWays[$oSeriesWay->id] = $oSeriesWay;
                }
                if ($oSeriesWay->WinningNumber === false) {
                    continue;
                }
                if ($fPrize = $this->calculateProject($DB, $oSeriesWay, $sCode, $oProject, $aWonProjects, $aLostProjects)) {
                    $fTotalPrize+=$fPrize;
                }
            }
            $aCodes[$sCode] = $fTotalPrize;
            if ($i == 0) {
                $sFinalCode = $sCode;
                $fFinalPrize = $fTotalPrize;
            }
        }
        foreach ($aCodes as $sCode => $fTmpPrize) {
            if (floatval($fFinalPrize) >= floatval($fTmpPrize)) {
                $sFinalCode = $sCode;
                $fFinalPrize = $fTmpPrize;
            }
        }
//        foreach ($aProjects as $oProject) {
//            $this->writeLog($oProject->serial_number);
//        }
        $this->writeLog('all codes=' . var_export($aCodes, true));
        return $sFinalCode;
    }

    /**
     * 由中奖号码分析得出各投注方式的中奖号码数组
     * @param Lottery $oLottery
     * @param string $sFullWnNumber
     * @param bool $bNameKey
     * @return array &
     */
    private function & _getWnNumberOfSeriesMethods($oLottery, $sFullWnNumber, $bNameKey = false) {
        $oSeriesMethods = SeriesMethod::where('series_id', '=', $oLottery->series_id)->get();
        $aWnNumbers = [];
        foreach ($oSeriesMethods as $oSeriesMethod) {
            $aWnNumbers[$oSeriesMethod->id] = $oSeriesMethod->getWinningNumber($sFullWnNumber);
        }
        return $aWnNumbers;
    }

    /**
     * 对注单计奖
     * @param DB $DB
     * @param SeriesWay $oSeriesWay
     * @param Issue $oIssue
     * @param Project $oProject
     * @param array & $aWonProjects
     * @param array & $aLostProjects
     * @return array &
     */
    private function calculateProject($DB, $oSeriesWay, $sCode, $oProject, & $aWonProjects, & $aLostProjects) {
        $aPrizeSet = json_decode($oProject->prize_set, true);
        $sBetNumber = $oProject->bet_number;
        $sPosition = $oProject->position;
        $sKey = md5($sBetNumber . $sPosition);
        $fPrize = 0;
        try {
            $aPrized = $oSeriesWay->checkPrize($sBetNumber, $sPosition);
            if ($aPrized) {
                foreach ($aPrized as $iBasicMethodId => $aPrizeOfBasicMethod) {
                    list($iLevel, $iCount) = each($aPrizeOfBasicMethod);
                    $this->writeLog('prize set=' . var_export($aPrizeSet, true));
                    $this->writeLog('wn_number=' . var_export($oSeriesWay->WinningNumber, true));
                    $this->writeLog('project serial_number=' . $oProject->serial_number);
                    $this->writeLog('basic_method=' . $iBasicMethodId);
                    $this->writeLog('level=' . $iLevel);
                    $fPrizeOf = $aPrizeSet[$iBasicMethodId][$iLevel] * $iCount * $oProject->multiple * $oProject->coefficient;
                    $fPrize += $fPrizeOf;
                }
            }
        } catch (Exception $e) {
            Log::info('renxuan calculate failed');
            Log::info($oProject->getAttributes());
            Log::info('-------------------------------------------');
        }

        return $fPrize;
    }

    /**
     *  核查黑名单投注
     */
    protected function checkBlackListBet($iLotteryId, $sIssue) {
        $aUsers = Config::get('bet.blacklist');
        Log::info('********************************');
        Log::info('lottery=' . $iLotteryId . ', issue=' . $sIssue);
        $aProjects = ManProject::getValidProjectUserIds($iLotteryId, $sIssue);
        Log::info($aProjects);
        $aResult = array_intersect($aUsers, $aProjects);
        Log::info($aResult);
        Log::info('********************************');
        return count($aResult) > 0;
    }

    protected function getArguments() {
        return array(
            array('lottery_id', InputArgument::REQUIRED, null),
            array('count', InputArgument::OPTIONAL, null, 100),
        );
    }

}
