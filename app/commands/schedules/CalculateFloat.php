<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 计算总代升降点
 */
class CalculateFloat extends BaseCommand {

    protected $sFileName = 'calculatefloat';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'firecat:calculate-float';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'calculate float for top agent';

    public function fire() {
        // 设置日志文件保存位置
        !$this->writeTxtLog or $this->logFile = $this->logPath . DIRECTORY_SEPARATOR . $this->sFileName;
        $this->writeLog('begin calculating float.');
        $aUsers = User::getAllUserArrayByUserType(User::TYPE_TOP_AGENT, null);
        $aLiftRules = PrizeSetFloatRule::getRuleList();
        // 验证是否全局设置有效
        if (SysConfig::readValue('prize_group_float_enabled') == false) {
            $this->writeLog('the globe setting already close.');
            exit;
        }

        // 遍历总代列表，进行升降点计算
        $sCurrentDate = date('Y-m-d', strtotime("-1 day"));
        // 获取总代的最高和最低奖金组
        $iTopAgentMaxPrizeGroup = SysConfig::readValue('top_agent_max_grize_group');
        $iTopAgentMinPrizeGroup = SysConfig::readValue('top_agent_min_grize_group');
        foreach ($aUsers as $oUser) {
            // 过滤奖金组不符合总代奖金组范围的用户
            if ($oUser->prize_group > $iTopAgentMaxPrizeGroup || $oUser->prize_group < $iTopAgentMinPrizeGroup) {
                continue;
            }
            // 获取指定总代最后一次升降点记录时间
            $sLastPrizeSetDate = UserPrizeSetFloat::getLastCalculateFloatDate($oUser->id);
            $aFinalClassicPrize = $aUpClassicPrize = $aDownCLassicPrize = [];
            //判断用户是否禁止升降点,条件:没有进入升降点黑名单,并且不在最高或者最低奖金组
            $bUpEnabled = RoleUser::checkUserRoleRelation(Role::DONT_UP_PRIZE, $oUser->id) || $oUser->prize_group == $iTopAgentMaxPrizeGroup;
            $bDownEnabled = RoleUser::checkUserRoleRelation(Role::DONT_DOWN_PRIZE, $oUser->id) || $oUser->prize_group == $iTopAgentMinPrizeGroup;
            //查看升降点记录，分别取出升点记录和降点记录对总代进行判断
            // 计算保点条件，确定指定总代的保点奖金组
            if (key_exists(PrizeSetFloatRule::FLOAT_TYPE_STAY, $aLiftRules) && !$bDownEnabled) {
                $aDownCLassicPrize = $this->_calculateClassicPrize($aLiftRules, PrizeSetFloatRule::FLOAT_TYPE_STAY, $sLastPrizeSetDate, $sCurrentDate, $oUser);
                if (!empty($aDownCLassicPrize) && $oUser->prize_group <= $aDownCLassicPrize['finalClassicPrize']) {
                    $aDownCLassicPrize = [];
                }
            }
            // 计算升点条件，确定总代的升点奖金组
            if (key_exists(PrizeSetFloatRule::FLOAT_TYPE_UP, $aLiftRules) && !$bUpEnabled) {
                $aUpClassicPrize = $this->_calculateClassicPrize($aLiftRules, PrizeSetFloatRule::FLOAT_TYPE_UP, $sLastPrizeSetDate, $sCurrentDate, $oUser);
                if (!empty($aUpClassicPrize) && $oUser->prize_group >= $aUpClassicPrize['finalClassicPrize']) {
                    $aUpClassicPrize = [];
                }
            }
            // 获取最终总代的奖金组设置
            if ($aDownCLassicPrize && $aUpClassicPrize) {
                $aFinalClassicPrize = $aDownCLassicPrize['finalClassicPrize'] > $aUpClassicPrize['finalClassicPrize'] ? $aDownCLassicPrize : $aUpClassicPrize;
            } else if ($aDownCLassicPrize) {
                $aFinalClassicPrize = $aDownCLassicPrize;
            } else if ($aUpClassicPrize) {
                $aFinalClassicPrize = $aUpClassicPrize;
            }
            if (empty($aFinalClassicPrize)) {
                $this->writelog('no classic prize, username=' . $oUser->username);
                continue;
            }
            // 增加总代奖金组设置记录
            DB::connection()->beginTransaction();
            $bSucc = $this->_addPrizeSet($aFinalClassicPrize, $oUser);
            if ($bSucc) {
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
//            pr($oUser->getAttributes());
            // 读取升降点记录表，获取指定代理的上次升降点操作时间，如果没有记录，去系统配置中的时间记录作为开始时间
            //获取升降点记录表中符合天数计算条件的条件记录
            //遍历条件记录中的信息，按照条件进行升点和保点操作
        }
    }

    /**
     *
     * @param int $iClassicPrize     奖金组信息
     * @param object $oUser               用户对象
     * @return boolean                      成功或失败
     */
    private function _addPrizeSet($aFinalClassicPrize, $oUser) {
        $oUserPrizeSetFloat = new UserPrizeSetFloat;
        $oUserPrizeSetFloat->user_id = $oUser->id;
        $oUserPrizeSetFloat->username = $oUser->username;
        $oUserPrizeSetFloat->old_prize_group = $oUser->prize_group;
        $oUserPrizeSetFloat->new_prize_group = $aFinalClassicPrize['finalClassicPrize'];
        $oUserPrizeSetFloat->standard_turnover = $aFinalClassicPrize['standardTurnover'];
        $oUserPrizeSetFloat->total_team_turnover = $aFinalClassicPrize['totalTeamTurnover'];
        $oUserPrizeSetFloat->day = $aFinalClassicPrize['day'];
        $oUserPrizeSetFloat->begin_date = $aFinalClassicPrize['beginDate'];
        $oUserPrizeSetFloat->end_date = $aFinalClassicPrize['endDate'];
        $oUserPrizeSetFloat->is_up = $aFinalClassicPrize['isUp'];
        return $oUserPrizeSetFloat->save();
    }

    /**
     * 计算升降点奖金组信息
     * @param array $aLiftRules                      升降点规则数组
     * @param int $iUp                                 升点或降点
     * @param string $sLastPrizeSetDate     最后一次升降点计算日期
     * @param string $sCurrentDate             当前日期
     * @param object $oUser                           用户对象
     * @return data                                         返回符合条件的升降点数组信息
     */
    private function _calculateClassicPrize($aLiftRules, $iUp, $sLastPrizeSetDate, $sCurrentDate, $oUser) {
        $aData = [];
        asort($aLiftRules[$iUp]);
        foreach ($aLiftRules[$iUp] as $day => $aTurnover) {
            asort($aTurnover);
            $sBeginDate = date('Y-m-d', strtotime("-" . $day . " day"));
            if ($sBeginDate < $sLastPrizeSetDate) {
                continue;
            }
            // 获取用户团队销售总额
            $fTotalTeamTurnover = UserProfit::getUserTotalTeamTurnover($sBeginDate, $sCurrentDate, $oUser->id);
            foreach (array_reverse($aTurnover, true) as $iClassicPrize => $iTurnover) {
                $iTurnover = $iTurnover * PrizeSetFloatRule::NUMBER_WAN;
                if ($fTotalTeamTurnover >= $iTurnover) {
                    if (empty($aData)) {
                        $aData = [
                            'finalClassicPrize' => $iClassicPrize,
                            'totalTeamTurnover' => $fTotalTeamTurnover,
                            'standardTurnover' => $iTurnover,
                            'isUp' => $iUp,
                            'day' => $day,
                            'beginDate' => $sBeginDate,
                            'endDate' => $sCurrentDate,
                        ];
                    }
                    break;
                }
            }
            // 处理降点到最低点的业务逻辑，额外判断
            if ($iUp == UserPrizeSetFloat::DOWN_POINT && empty($aData)) {
                $aData = [
                    'finalClassicPrize' => SysConfig::readValue('top_agent_min_grize_group'),
                    'totalTeamTurnover' => $fTotalTeamTurnover,
                    'standardTurnover'  => 0,
                    'isUp'              => $iUp,
                    'day'               => $day,
                    'beginDate'         => $sBeginDate,
                    'endDate'           => $sCurrentDate,
                ];
            }
            $fTotalTeamTurnover == 0 or $this->writeLog('username=' . $oUser->username . ', team_turnover=' . $fTotalTeamTurnover . ' between ' . $sBeginDate . ' and ' . $sCurrentDate . ', data=' . var_export($aData, true));
        }
        return $aData;
    }

}
