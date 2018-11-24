<?php

/**
 * 生成奖期
 *
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateIssue extends BaseCommand {
    protected $sFileName = 'GenerateIssue';
    protected $name = 'issue:generate';
    protected $description = 'generate issues';
    public $writeTxtLog    = true;
    private $checkDays = [ 50, 5 ];
    
    public function doCommand(& $sMsg = null){
        $iLotteryId = $this->argument('lottery_id');
        if ( empty($iLotteryId) ) {
            $sMsg = "LotteryId not Given!";
            return;
        }
        $oLottery = ManLottery::find($iLotteryId);
        if (empty($oLottery)) {
            $sMsg = "Lottery $iLotteryId not Exists!";
            return;
        }
        $this->writeLog("Start Generate Issue For Lottery: $iLotteryId, Lottery: $oLottery->name");
        $sBeginIssue = '';
        $bAccumulating = $oLottery->isAccumulating();
        $oExistLastIssue = ManIssue::getLastIssueObject($iLotteryId);
        $aIssueRules = IssueRule::getIssueRulesOfLottery($oLottery->id);
        if (!empty($oExistLastIssue)){
            $iLastEndTime = $oExistLastIssue->end_time;
            $sBeginDate = $oLottery->getNextDay($iLastEndTime);
            if ($bAccumulating) $sBeginIssue = $oExistLastIssue->issue + 1;
            $iNeedDays = $this->checkDays[$oLottery->high_frequency];
            // 奖期还有很多的时候就不生成
            if ($iLastEndTime - time() > $iNeedDays * 3600 * 24){
                $sMsg = "The Issues of $oLottery->name are enough, exiting";
                return;
            }
            $sLastIssue = $oExistLastIssue->issue;
        }
        else{
            if ($bAccumulating && empty($sBeginIssue)){
                $sMsg = "The Issue of $oLottery->name is Accumulating, need Begin Issue exiting";
                return;
            }
            if (empty($sBeginDate)){
                $sBeginDate = Carbon::now()->toDateString();
            }
            $iLastEndTime = null;
            $sLastIssue = null;
        }
        $oBeginDate = new Carbon($sBeginDate);
        $oEndDate = $oLottery->high_frequency ? $oBeginDate->endOfMonth() : $oBeginDate->endOfYear();
        $sEndDate = $oEndDate->toDateString();
        //电子游戏和PK10分分彩每次只生成5天的奖期数据
        if(in_array($iLotteryId, [26, 29, 32, 35, 39, 42, 45, 48, 51, 60, 62]))
        {
        	$sEndDate = date('Y-m-d',strtotime($sBeginDate."+4 day"));
        }
        //$sEndDate = $sBeginDate;

        $DB          = DB::connection();
        $DB->beginTransaction();
        $this->writeLog("Start Generate, From $sBeginDate to $sEndDate, LastIssue:$sLastIssue, BeginIssue:$sBeginIssue, Generating...");

        // pr('sBeginDate:'.$sBeginDate);
        // pr('sEndDate:'.$sEndDate);
        // pr('sLastIssue:'.$sLastIssue);
        // pr('sBeginIssue:'.$sBeginIssue);
        // exit();
        
        $manIssue = new ManIssue();
        $bsucc =  $manIssue->autoGenerateIssues($oLottery, $aIssueRules, $sBeginDate, $sEndDate, $sLastIssue, $sBeginIssue, $iCount);
        
        if (!$bsucc){
            $DB->rollback();
            $last_query = json_encode(end($DB->getQueryLog()));
            $sMsg = "Generate Failed, Last Query: $last_query";
        }
        else{
            $DB->commit();
            $sMsg = "Generated, Total $iCount Issues";
        }
        $this->writeLog($sMsg);
        return;
    }

    // 设置接收的参数
    protected function getArguments() {
        return array(
            // array('lottery_id', InputArgument::REQUIRED, null),
            array('lottery_id', InputArgument::OPTIONAL, null, 1),
        );
    }

}
