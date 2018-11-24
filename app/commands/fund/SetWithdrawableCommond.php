<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 更新可提余额
 *
 */
class SetWithdrawableCommond extends BaseCommand {

    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'fund:set-withdraw-amount';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'set the withdraw amount';

    public function doCommand(& $sMsg = null) {

        $sBeginDate = $this->argument('begin_date');

        $aUserTurnovers = UserTurnover::getUserTurnOversByDate($sBeginDate . ' 00:00:00');
        if (empty($aUserTurnovers)) {
            return;
        }
        $iTotal = count($aUserTurnovers);
        $iSucc = $iFail = 0;

        $DB = DB::connection();

        foreach ($aUserTurnovers as $aTurnover) {
            $oIssue = ManIssue::getIssueObject($aTurnover['lottery_id'], $aTurnover['issue']);
            if(is_object($oIssue) && $oIssue->status_count != ManIssue::CALCULATE_FINISHED){
                continue;
            }
            $oAccount = Account::lock($aTurnover['account_id'], $iLocker);
            if (empty($oAccount)) {
                $iFail++;
                continue;
            }
            $DB->beginTransaction();
            
             if ($bSucc = $oAccount->setWithdrawable($aTurnover['turnover'] * 5)){
                $bSucc = UserTurnover::setToUsed($aTurnover['id']);
                $this->writeLog("username=$oAccount->username, lottery_id=" . $aTurnover['lottery_id'] . ", issue=" . $aTurnover['issue'] . ", turnover=" . $aTurnover['turnover']);
            } else {
                $this->writeLog('Errors: ' . $oAccount->getValidationErrorString());
            }
            if ($bSucc) {
                $this->writeLog('Seted');
                $iSucc++;
                $DB->commit();
            } else {
                $DB->rollback();
            }
            Account::unLock($oAccount->id, $iLocker, false);
//            $oAccount->s
        }
        $this->writeLog("Total: $iTotal Succ: $iSucc Fail: $iFail");
        $this->writeLog($iFail ? ' Restore' : ' Delete');
//        if (!$bSucc = UserProfit::updateProfitData($type, $date, $oUser, $amount)) {
//            $DB->rollback();
//            $this->log = "User Profit Update Failed";
//            return self::TASK_RESTORE;
//        }
        // 更新与奖期关联的销售额
    }

    protected function getArguments() {
        return array(
//            array('lottery_id', InputArgument::REQUIRED, null),
            array('begin_date', InputArgument::OPTIONAL, null, date('Y-m-d')),
        );
    }

}
