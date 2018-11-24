<?php

/**
 * 更新月盈亏表
 *
 * @author white
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateMonthProfitCommand extends BaseCommand {
    /**
     * command name.
     *
     * @var string
     */
    protected $name = 'stat:update-month-profit';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'Update the month profit data';
    
    public function doCommand(& $sMsg = null) {
        $iYear = $this->argument('year');
        $iMonth = $this->argument('month');
        $sEndDate = $iMonth == date('m') ? $sEndDate = date('Y-m-d',time() - 3600 * 24) : null;
        $sMonth = $iYear . '-' . str_pad($iMonth, 2, '0', STR_PAD_LEFT);
//        $iBeginTime or die("Invalid Begin Date\n");
//        $iEndTime or die("Invalid End Date\n");
//        $iEndTime >= $iBeginTime or die("End Date Less Than Begin Date!\n");
        
//        for($iTime = $iBeginTime; $iTime <= $iEndTime;$iTime += 3600 * 24){
//            $sDate = date('Y-m-d', $iTime);
////            continue;
////            pr($sDate);
//            pr($oProfit->toArray());
        $aData = $this->getMonthSumData($sMonth, $sEndDate);
        if ($aData){
            $sBeginDate = Carbon::create($iYear,$iMonth,1)->__toString();
            $sEndDate = Carbon::create($iYear,$iMonth,1)->endOfMonth()->__toString();
            $oMonthProfit = MonthProfit::getMonthProfitObject($iYear, $iMonth);
            $oMonthProfit->fill($aData);
            $oMonthProfit->calculateProfit();
            $oMonthProfit->signed_count = UserLogin::getLoginUserCount($sBeginDate,$sEndDate);
            $oMonthProfit->bought_count = ManProject::getBoughtUserCount($sBeginDate,$sEndDate);
            $oMonthProfit->save();
        }
//            $oProfit->update($aData);
//        }
    }

    protected function getMonthSumData($sMonth, $sEndDate = null){
        $sSql = "select sum(deposit) deposit, sum(withdrawal) withdrawal, sum(turnover) turnover, sum(prize) prize, sum(commission) commission, sum(bonus) bonus,sum(daily_salary) daily_salary, sum(dividend) dividend,    sum(profit) profit, sum(prj_count) prj_count, "
                . "sum(tester_deposit) tester_deposit, sum(tester_withdrawal) tester_withdrawal, sum(tester_turnover) tester_turnover, sum(tester_prize) tester_prize, sum(tester_commission) tester_commission, sum(tester_bonus) tester_bonus, sum(tester_daily_salary) tester_daily_salary, sum(tester_dividend) tester_dividend, sum(tester_profit) tester_profit,  sum(tester_prj_count) tester_prj_count, "
                . "sum(registered_count) registered_count, sum(registered_top_agent_count) registered_top_agent_count "
                . " from profits where date like '$sMonth%'";
        empty($sEndDate) or $sSql .= " and date <= '$sEndDate'";
        $aResults = DB::select($sSql);
        $aData = objectToArray($aResults[0]);
        return array_sum($aData) ? $aData : false;
    }
    
    protected function queryTotalAmount($sDate, $aTransactionTypes, $bOnlyTester = false){
        $sTransactionTypes = implode(',',$aTransactionTypes);
        $sSql = "select sum(amount) total_amount from transactions where type_id in ($sTransactionTypes) and created_at between '$sDate' and '$sDate 23:59:59'";
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
        $aResults = DB::select($sSql);
        return $aResults[0]->total_amount ? $aResults[0]->total_amount : 0;
    }

    protected function queryPrjCount($sDate, $bOnlyTester = false){
        $sSql = "select count(*) prj_count from projects where status <> " . Project::STATUS_DROPED . " and bought_at between '$sDate' and '$sDate 23:59:59'";
        !$bOnlyTester or $sSql .= ' and is_tester = 1';
        $aResults = DB::select($sSql);
        return $aResults[0]->prj_count ? $aResults[0]->prj_count : 0;
    }
    
    protected function getArguments() {
        return array(
//            array('lottery_id', InputArgument::REQUIRED, null),
            array('year', InputArgument::OPTIONAL, null, date('Y')),
            array('month', InputArgument::OPTIONAL, null, date('m', time() - 3600 * 24)),
        );
    }

}
