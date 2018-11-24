<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 玩家销量抽奖
 */
class ActivityPlayerDailyTurnoverRebate extends BaseCommand {

    protected $sFileName = 'activityplayerdailyturnoverrebate';

    /**
     * The activity cash back command name.
     *
     * @var string
     */
    protected $name = 'activity:player-daily-turnover-rebate';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'activity player daily turnover rebate';

    public function doCommand(& $sMsg = null) {
        $aUsers = User::getAllUserArrayByUserType('all', ['forefather_ids', 'forefathers']);
        $sYesterDay = date('Y-m-d', strtotime('-1 day'));
        $this->writeLog('start command, calculate date is ' . $sYesterDay);
        foreach ($aUsers as $oUser) {
            $oReportAgentRebate = ActivityReportDailyDepositAgent::getObjectByParams(['source_user_id' => $oUser->id, 'rebate_date' => $sYesterDay, 'type' => 'user_daily_turnover']);
            // 不能重复计算
            if (is_object($oReportAgentRebate)) {
                continue;
            }
            Log::info($oUser);
            $oUserProfit = UserProfit::getUserProfitObject($sYesterDay, $oUser->id);
            if (!is_object($oUserProfit)) {
                $this->writeLog('no profit data');
                continue;
            }
            $this->writeLog('turnover info, username=' . $oUserProfit->username . ', turnover=' . $oUserProfit->turnover);
            // 销量最低500起
            if ($oUserProfit->turnover < 500) {
                $this->writeLog('turnover or profit not enough, turnover=' . $oUserProfit->turnover . ', profit=' . $oUserProfit->profit);
                continue;
            }
            $fBonus = $this->getPrizeBonusByProfit(abs($oUserProfit->turnover));
            //奖金不满足条件，退出
            if ($fBonus == 0) {
                continue;
            }
            $this->writeLog(" date=$sYesterDay, username=$oUserProfit->username, turnover=$oUserProfit->turnover, profit=$oUserProfit->profit , bonus=$fBonus");
            DB::connection()->beginTransaction();
            $bSucc = $this->saveData($oUserProfit, $sYesterDay, $fBonus, $oUser);
            if ($bSucc) {
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
        }
    }

    private function getPrizeBonusByProfit($fAmount) {
        $fBonus = 0;
        $sfeeExpressions = 'x>=500&&x<1000&&y=3;x>=1000&&x<2000&&y=5;x>=2000&&x<3000&&y=8;x>=3000&&x<5000&&y=10;x>=5000&&x<10000&&y=20;x>=10000&&x<30000&&y=30;x>=30000&&x<50000&&y=90;x>=50000&&x<100000&&y=150;x>=100000&&x<300000&&y=300;x>=300000&&x<600000&&y=1000;x>=600000&&x<1000000&&y=2000;x>=1000000&&y=3000;';
        $sFeeExpressions = str_replace('x', '$fAmount', $sfeeExpressions);
        $sFeeExpressions = str_replace('y', '$fBonus', $sFeeExpressions);
        eval($sFeeExpressions);
        return $fBonus;
    }

    private function saveData($oUserProfit, $sCurrentDate, $fBonus, $oSubUser = null) {
        $oUserPrize = $this->createUserPrize($fBonus, $oUserProfit, $oSubUser);
        $oReportAgentRebate = $this->createReportAgentRebate($fBonus, $oUserProfit->deposit, $sCurrentDate, $oSubUser);
        $bSucc = $oUserPrize->save();
        !$bSucc or $bSucc = $oReportAgentRebate->save();
        return $bSucc;
    }

    /**
     *  创建用户奖品对象
     */
    private function createUserPrize($fBonus, $oUserProfit, $oSubUser = null) {
        $oUserPrize = new ActivityUserPrize();
        $oUserPrize->prize_id = ActivityPrize::PRIZE_DAILY_BET;
        $oActivityPrize = ActivityPrize::find($oUserPrize->prize_id);
        $oUserPrize->activity_id = $oActivityPrize->activity_id;
        $aExtraData = [
            'rebate_amount' => $fBonus,
            'turnover_username' => $oSubUser->username,
            'usernames' => $oSubUser->forefathers,
            'turnover' => $oUserProfit->turnover,
            'profit' => $oUserProfit->profit,
        ];
        $oUserPrize->data = json_encode($aExtraData);
        $oUserPrize->count = 1;
        $oUserPrize->user_id = $oSubUser->id;
        $oUserPrize->source = ActivityUserPrize::SOURCE_COMMAND;
        $oUserPrize->status = ActivityUserPrize::STATUS_VERIRIED;
        return $oUserPrize;
    }

    /**
     * 创建活动报表对象
     */
    private function createReportAgentRebate($fBonus, $fAmount, $sDate, $oSubUser = null) {
        $oReportAgentRebate = new ActivityReportDailyDepositAgent;
        $oReportAgentRebate->user_id = $oSubUser->id;
        $oReportAgentRebate->username = $oSubUser->username;
        $oReportAgentRebate->is_tester = $oSubUser->is_tester ? 1 : 0;
        $oReportAgentRebate->rebate_amount = $fBonus;
        $oReportAgentRebate->rebate_date = $sDate;
        $oReportAgentRebate->source_user_id = $oSubUser != null ? $oSubUser->id : null;
        $oReportAgentRebate->source_username = $oSubUser != null ? $oSubUser->username : null;
        $oReportAgentRebate->deposit_amount = $fAmount;
        $oReportAgentRebate->type = 'user_daily_turnover';
        return $oReportAgentRebate;
    }

}
