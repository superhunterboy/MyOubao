<?php

/**
 * 计算总代分红
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CalculateDailySalary extends BaseCommand {

    protected $name = 'daily-salary:calculate';
    protected $description = 'calculate daily salary for top agent';

    public function doCommand(& $sMsg = null) {
        $this->writeLog('start command');
        $sDate = date('Y-m-d', strtotime('-1 day'));
//        $sBeginDate = $sEndDate = '2015-06-23';
        $this->writeLog('calculate date=' . $sDate);
        $aTeamProfits = TeamProfit::getAllTopAgentProfitByDate($sDate);
        foreach ($aTeamProfits as $oTeamProfit) {
            $this->writeLog('username=' . $oTeamProfit->username . ', turnover=' . $oTeamProfit->turnover . ', profit=' . $oTeamProfit->profit);
            if ($oTeamProfit->turnover < 10000) {
                continue;
            }
            $fBonus = $this->getBonus($oTeamProfit->turnover, $oTeamProfit->profit, $oTeamProfit->username);
            $this->writeLog('bonus=' . $fBonus);

            if ($fBonus == 0) {
                continue;
            }
            $oUser = User::find($oTeamProfit->user_id);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                $this->writeLog($oTeamProfit->username.' Account lock failed');
                continue;
            }
            $aExtraData = [
                'note' => '总代日工资',
            ];
            DB::connection()->beginTransaction();
            $bSucc = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_SEND_BONUS, $fBonus, $aExtraData) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
            if ($bSucc) {
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
            Account::unLock($oUser->account_id, $iLocker, false);
        }
    }

    public function getBonus($fTurnover, $fProfit, $sUsename) {
        $fTurnover = $fTurnover / 10000;
        $iTurnover = floor($fTurnover);
        $iTurnover = $iTurnover * 10000;
        $sSpeciaTopAgents = SysConfig::readValue('special_topAgents');
        if(!empty($sSpeciaTopAgents))
        {
        	$aSpeciaTopAgents = explode(',', $sSpeciaTopAgents);
	        if (in_array($sUsename, $aSpeciaTopAgents)) {
	            return $iTurnover * 0.012;
	        }
        }
        
        if ($fProfit > 0) {
            return $iTurnover * 0.008;
        }
        $fProfit = abs($fProfit);
        $fRate = 0.008;
        if ($fProfit > 0 && $fProfit < 15000) {
            $fRate = 0.01;
        } else if ($fProfit >= 15000 && $fProfit < 30000) {
            $fRate = 0.012;
        } else if ($fProfit >= 30000) {
            $fRate = 0.015;
        }
        return $iTurnover * $fRate;
    }

    private function getExpression($fTurnover) {
        $sExp = '$profit<500&& $fBonus=0;';
        if ($fTurnover >= 10000000) {
            $sExp .='if($profit>=35000)  { $fBonus=35000;return;}';
        }
        if ($fTurnover >= 5000000) {
            $sExp .='if($profit>=20000 ) { $fBonus=20000;return;}';
        }
        if ($fTurnover >= 2000000) {
            $sExp .='if($profit>=12000 ) { $fBonus=12000;return;}';
        }
        if ($fTurnover >= 1000000) {
            $sExp .='if($profit>=6800 ) { $fBonus=6800;return;}';
        }
        if ($fTurnover >= 700000) {
            $sExp .='if($profit>=4800 ) { $fBonus=4800;return;}';
        }
        if ($fTurnover >= 500000) {
            $sExp .='if($profit>=3500 ) { $fBonus=3500;return;}';
        }
        if ($fTurnover >= 300000) {
            $sExp .='if($profit>=2500 ) { $fBonus=2500;return;}';
        }
        if ($fTurnover >= 200000) {
            $sExp .='if($profit>=1800 ) { $fBonus=1800;return;}';
        }
        if ($fTurnover >= 100000) {
            $sExp .='if($profit>=1000 ) { $fBonus=1000;return;}';
        }
        if ($fTurnover >= 50000) {
            $sExp .='if($profit>=500) {$fBonus=500;return;}';
        }
        return $sExp;
    }

    /**
     *  创建用户奖品对象
     */
    private function createUserPrize($fBonus, $oTeamProfit) {
        $oUserPrize = new ActivityUserPrize();
        $oUserPrize->prize_id = ActivityPrize::PRIZE_TOP_AGENT_DAILY_SALARY;
        $oActivityPrize = ActivityPrize::find($oUserPrize->prize_id);
        $oUserPrize->activity_id = $oActivityPrize->activity_id;
        $aExtraData = [
            'rebate_amount' => $fBonus,
            'username' => $oTeamProfit->username,
            'team_turnover' => $oTeamProfit->total_turnover,
            'team_profit' => $oTeamProfit->total_profit,
        ];
        $oUserPrize->data = json_encode($aExtraData);
        $oUserPrize->count = 1;
        $oUserPrize->user_id = $oTeamProfit->user_id;
        $oUserPrize->source = ActivityUserPrize::SOURCE_COMMAND;
        $oUserPrize->status = ActivityUserPrize::STATUS_NO_SEND;
        return $oUserPrize->save();
    }

    /**
     * 创建活动报表对象
     */
    private function createReportDailySalary($fBonus, $oTeamProfit, $sDate) {
        $oReportDailySalary = new ActivityReportDailySalaryTopAgent;
        $oReportDailySalary->user_id = $oTeamProfit->user_id;
        $oReportDailySalary->username = $oTeamProfit->username;
        $oReportDailySalary->is_tester = $oTeamProfit->is_tester ? 1 : 0;
        $oReportDailySalary->rebate_amount = $fBonus;
        $oReportDailySalary->rebate_date = $sDate;
        $oReportDailySalary->team_turnover = $oTeamProfit->total_turnover;
        $oReportDailySalary->team_profit = $oTeamProfit->total_profit;
        return $oReportDailySalary->save();
    }

}
