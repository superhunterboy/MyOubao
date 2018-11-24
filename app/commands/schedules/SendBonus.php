<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 每月定时给代理分红
 */
class SendBonus extends BaseCommand {

    protected $sFileName = 'sendbonus';

    const BONUS_AUDIT_TYPE_MONTH = 'Month';
    const BONUS_AUDIT_TYPE_WEEK = 'Week';
    const BONUS_AUDIT_TYPE_DAY = 'Day';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firecat:send-bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send bonus to angent per month';

    public function fire() {
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $this->writeLog('begin sending bonus');
        // 获取所有代理用户
        $aUsers = User::getAllUserArrayByUserType(User::TYPE_AGENT, ['id', 'username', 'blocked']);
        $aBonusBlackUserIds = RoleUser::getUserIdsFromRoleId(Role::BONUS_BLACK);
        // 获取分红开始和结束时间
        $aBonusDate = $this->_getBonusDate();
        $sBeginDate = $aBonusDate['begin_date'];
        $sEndDate = $aBonusDate['end_date'];
        if (!$sBeginDate or ! $sEndDate) {
            $this->writeLog("begin date='. $sBeginDate.', end date='. $sEndDate. ',  error");
            exit;
        }
        if ($sBeginDate >= date('Y-m-d', strtotime(" -1 day"))) {
            $this->writeLog('begin date=' . $sBeginDate . ' > current date -1 day, error');
            exit;
        }
        if ($sEndDate > date('Y-m-d', strtotime(" -1 day"))) {
            $this->writeLog('end date=' . $sEndDate . ' > current date -1 day, error');
            exit;
        }
        // 初始化分红统计的变量
        $fTotalTurnover = 0;
        $fTotalBonus = 0;
        $fTotalAgentCount = 0;
        $fTotalProfit = 0;
        $bSucc = true;
        // 遍历所有代理用户，统计分红数据
        DB::connection()->beginTransaction();
        foreach ($aUsers as $oUser) {
            // 是否冻结
            if ($oUser->blocked) {
                $this->writeLog('userid=' . $oUser->id . ', username=' . $oUser->username . ' locked');
                continue;
            }
            // 是否在分红黑名单
            if (in_array($oUser->id, $aBonusBlackUserIds)) {
                $this->writeLog('userid=' . $oUser->id . ', username=' . $oUser->username . ' in the black list');
                continue;
            }
            $fTotalAgentCount++;
            // 获取代理用户指定时间范围的盈亏记录
            $aUserProfits = UserProfit::getUserProfitByDate($sBeginDate, $sEndDate, $oUser->id);
            $aDirectProfits = [];
            $aTurnovers = [];
            foreach ($aUserProfits as $data) {
                $aDirectProfits[] = $data['direct_profit'];
                $aTurnovers[] = $data['direct_turnover'];
            }
            // 分红统计
            $fProfits = array_sum($aDirectProfits);
            $fTurnover = array_sum($aTurnovers);
            $fTotalProfit += $fProfits;
            $fTotalTurnover += $fTurnover;
            $fAbsProfit = abs($fProfits);
            // 如果盈亏记录大于0，没有分红
            if ($fProfits > 0) {
                $this->writeLog('userid=' . $oUser->id . ', username=' . $oUser->username . ', profit= ' . $fProfits . '> 0, no bonus');
                continue;
            }
            $oBonusRule = BonusRule::getRuleObjectByProfit($fAbsProfit);
            // 查询符合条件的分红规则
            if (is_object($oBonusRule)) {
                $oBonus = Bonus::getBonusByMonthUser($oUser->id, $sBeginDate, $sEndDate);
                if (is_object($oBonus) && $oBonus->status != Bonus::STATUS_WAITING_AUDIT) {
                    $this->writeLog('userid=' . $oUser->id . ', username=' . $oUser->username . ", can not get bonus between $sBeginDate and $sEndDate , bonus status=$oBonus->status");
                    continue;
                }
                $this->_createBonus($oBonus, $oUser, $fProfits, $fTurnover, $oBonusRule, $sBeginDate, $sEndDate);
                $bSucc = $oBonus->save();
                $fTotalBonus += $oBonus->bonus;
                if ($bSucc) {
                    $this->writeLog('success, userid=' . $oUser->id . ', username=' . $oUser->username . ",  between $sBeginDate and $sEndDate ,bonus= $oBonus->bonus");
                } else {
                    $this->writeLog('fail, userid=' . $oUser->id . ', username=' . $oUser->username . ",  between $sBeginDate and $sEndDate , bonus= $oBonus->bonus");
                    exit;
                }
            } else {
                $this->writeLog('userid=' . $oUser->id . ', username=' . $oUser->username . ' can not get bonus, profit=' . $fProfits);
                continue;
            }
        }
        !$bSucc or $bSucc = $this->_saveBonusStatistics($sEndDate, $fTotalTurnover, $fTotalBonus, $fTotalAgentCount, $fTotalProfit);
        $bSucc ? DB::connection()->commit() : DB::connection()->rollback();
    }

    /**
     *
     * @param string $sEndDate            统计结束时间
     * @param float $fTotalTurnover     代理营业额总和
     * @param float $fTotalBonus    代理分红总和
     * @param float $fTotalAgentCount   代理总数
     * @param float $fTotalProfit   直接收益总和
     * @return boolean  保存成功或失败
     */
    private function _saveBonusStatistics($sEndDate, $fTotalTurnover, $fTotalBonus, $fTotalAgentCount, $fTotalProfit) {
        $oBonusStatistics = new BonusStatistics;
        $oBonusStatistics->statistics_date = $sEndDate;
        $oBonusStatistics->total_turnover = $fTotalTurnover;
        $oBonusStatistics->total_bonus = $fTotalBonus;
        $oBonusStatistics->total_agent_count = $fTotalAgentCount;
        $oBonusStatistics->total_profit = $fTotalProfit;
        return $oBonusStatistics->save();
    }

    /**
     * 获取分红的开始日期
     * @return string   开始日期
     */
    private function _getBonusDate() {
        $sLastDate = BonusStatistics::getLastBonusStatisticsDate();
        $iRange = SysConfig::readValue('bonus_range');
        switch (SysConfig::readValue('bonus_range_type')) {
            case self::BONUS_AUDIT_TYPE_DAY:
                if ($sLastDate) {
                    $sBeginDate = date('Y-m-d', strtotime($sLastDate . " +1 days"));
                } else {
                    $sBeginDate = SysConfig::readValue('bonus_start_date');
                }
                $sEndDate = date('Y-m-d', strtotime($sBeginDate . " +" . ($iRange-1) . " days"));
                break;
            case self::BONUS_AUDIT_TYPE_MONTH:
                if ($sLastDate) {
                    $sBeginDate = date('Y-m-d', strtotime($sLastDate . " +1 day"));
                } else {
                    $sBeginDate = SysConfig::readValue('bonus_start_date');
                }
                $sEndDate = date('Y-m-d', strtotime($sBeginDate . " +" . $iRange . " month -1 day"));
                break;
            case self::BONUS_AUDIT_TYPE_WEEK:
                if ($sLastDate) {
                    $sBeginDate = date('Y-m-d', strtotime($sLastDate . " +1 day"));
                } else {
                    $sBeginDate = SysConfig::readValue('bonus_start_date');
                }
                $sEndDate = date('Y-m-d', strtotime($sBeginDate . " +" . $iRange . " week -1 day"));
        }
        return ['begin_date' => $sBeginDate, 'end_date' => $sEndDate];
    }

    private function _createBonus(&$oBonus, $oUser, $fProfits, $fTurnover, $oBonusRule, $sBeginDate, $sEndDate) {
        $fBonus = abs($fProfits) * $oBonusRule->rate;
        if (!is_object($oBonus)) {
            $oBonus = new Bonus;
        }
        $oBonus->begin_date = $sBeginDate;
        $oBonus->end_date = $sEndDate;
        $oBonus->user_id = $oUser->id;
        $oBonus->username = $oUser->username;
        $oBonus->parent_user_id = $oUser->parent_id;
        $oBonus->agent_level = $oUser->parent_id == null ? 0 : 1;
        $oBonus->parent_username = $oUser->parent;
        $oBonus->turnover = $fTurnover;
        $oBonus->direct_profit = $fProfits;
        $oBonus->rate = $oBonusRule->rate;
        $oBonus->bonus = $fBonus;
        $oBonus->status = Bonus::STATUS_WAITING_AUDIT;
        return $oBonus;
    }

}
