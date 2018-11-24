<?php

/**
 * 计算总代月分红
 */
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TopAgentMonthlyBonusCommand extends BaseCommand {

    protected $name = 'topAgent:monthyBonus';
    protected $description = 'calculate top agent monthly bonus';

    public function doCommand(& $sMsg = null) {
    	$sDate = date('d');
        if($sDate == '16')
    	{
    		$sStartDate = date('Y-m-01');
        	$sEndDate   = date('Y-m-15');
    	}
        else if($sDate == '01')
    	{
    		$sStartDate = date('Y-m-16', strtotime('-1 month'));
        	$sEndDate   = date('Y-m-t', strtotime('-1 month'));
    	}
        else
    	{
    		$this->error('未到执行时间');die;
    		$sStartDate = '2017-09-01';
        	$sEndDate   = '2017-09-15';
    	}
		
        $this->writeLog('calculation from ' . $sStartDate.' to '.$sEndDate,true);
        $aTeamProfits = TeamProfit::getTopAgentProfitByDate($sStartDate, $sEndDate);
        foreach ($aTeamProfits as $oTeamProfit) {
            if ($oTeamProfit->is_tester || $oTeamProfit->total_profit > 0) {
                continue;
            }
            $this->writeLog('username=' . $oTeamProfit->username . ', turnover=' . $oTeamProfit->total_turnover . ', profit=' . $oTeamProfit->total_profit);
            $fTeamProfit = abs($oTeamProfit->total_profit);
            //获取指定时间段内的总代日工资
            $fTotalDailyBonus = Transaction::getTopAgentBonus($oTeamProfit->user_id, $sStartDate, $sEndDate);
            $this->writeLog('总代日工资：totalDailySalary=' . $fTotalDailyBonus);
            $fProfit = $fTeamProfit - $fTotalDailyBonus;
            if($fProfit <= 0)
            {
            	continue;
            }
            $fRate = 0.2;
            if(in_array($oTeamProfit->username, array('a68208228','xie228228','406473025')))//该用户特殊处理，按照保底30%发放总代分红
            {
            	$fRate = 0.3;
            }
            $fBonus = floor($fProfit * $fRate);
            $this->writeLog('本次的分红：('.floor($fTeamProfit).' - '.floor($fTotalDailyBonus).') * '.$fRate.' = ' . $fBonus);

            if ($fBonus < 1) {
                continue;
            }
            $oUser = User::find($oTeamProfit->user_id);
            $oAccount = Account::lock($oUser->account_id, $iLocker);
            if (empty($oAccount)) {
                $this->writeLog($oTeamProfit->username.' Account lock failed');
                continue;
            }
            $aExtraData = [
                'note' => '总代'.date('n.j',strtotime($sStartDate)).'-'.date('n.j',strtotime($sEndDate)).'分红',
            ];
            DB::connection()->beginTransaction();
            $bSucc = Transaction::addTransaction($oUser, $oAccount, TransactionType::TYPE_SEND_BONUS, $fBonus, $aExtraData) == Transaction::ERRNO_CREATE_SUCCESSFUL ? true : false;
            if ($bSucc) {
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
            Account::unLock($oUser->account_id, $iLocker, false);
            $this->writeLog('======================', true);
        }
    }
	

}
