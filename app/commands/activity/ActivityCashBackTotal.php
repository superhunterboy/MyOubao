<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 首冲四次返活动
 */
class ActivityCashBackTotal extends BaseCommand {

    protected $sFileName = 'activitycashbacktotal';

    /**
     * The activity cash back command name.
     *
     * @var string
     */
    protected $name = 'firecat:activity-cash-back-total';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'activity cash back total';

    //奖品id
    const YICI_FAN_PRIZE_ID = 16;
    const SICI_FAN_PRIZE_ID = 17;

    public function fire() {
        $aUserPrize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(null, self::SICI_FAN_PRIZE_ID);
        foreach ($aUserPrize as $obj) {
            $oActivity = Activity::find($obj->activity_id);
            $this->_generateSiCiFanActivityCashBacks($oActivity, $obj);
        }
        $aUserPrize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(null, self::YICI_FAN_PRIZE_ID);
        foreach ($aUserPrize as $obj) {
            $oActivity = Activity::find($obj->activity_id);
            $this->_generateYiCiFanActivityCashBacks($oActivity, $obj);
        }
    }

    /**
     * 生成用户从首充成功开始的4周内数据
     * @param type $oActivity       活动对象
     * @param type $oUserPrize  用户奖品对象
     */
    private function _generateSiCiFanActivityCashBacks($oActivity, $oUserPrize) {
        $i = 0;
        $sBeginDate = date('Y-m-d', strtotime($oUserPrize->created_at));
        $fActivityEndTime = date('Y-m-d', strtotime($oActivity->end_time . '+23 days'));
        //todo：事务
        while ($i < 4) {
            $sEndDate = date('Y-m-d', strtotime($sBeginDate . ' this sunday'));
            if ($sEndDate <= $fActivityEndTime) {
                $aCashBacks = ActivityCashBack::getDataByUserId($oUserPrize->user_id, self::SICI_FAN_PRIZE_ID, $sEndDate);
                if (count($aCashBacks) > 0) {
                    $oActivityCashBack = $aCashBacks->first();
                } else {
                    $oActivityCashBack = new ActivityCashBack;
                }
                $oActivityCashBack->user_id = $oUserPrize->user_id;
                $oActivityCashBack->prize_id = self::SICI_FAN_PRIZE_ID;
                $oActivityCashBack->begin_date = $sBeginDate;
                $oActivityCashBack->end_date = $sEndDate;
                $oActivityCashBack->total_turnover = UserProfit::getUserTotalTurnover($sBeginDate, $sEndDate, $oUserPrize->user_id);
                $oActivityCashBack->save();
            } else {
                break;
            }
            $i++;
            $sBeginDate = date('Y-m-d', strtotime($sBeginDate . '+1 weeks  last monday'));
        }
    }

    /**
     * 生成用户从首充成功开始的4周内数据
     * @param type $oActivity       活动对象
     * @param type $oUserPrize  用户奖品对象
     */
    private function _generateYiCiFanActivityCashBacks($oActivity, $oUserPrize) {
        $fActivityEndTime = date('Y-m-d', strtotime($oActivity->end_time));
        $sBeginDate = $oUserPrize->created_at;
        $sEndDate = date('Y-m-d');
        $sEndDate = $sEndDate <= $fActivityEndTime ? $sEndDate : $fActivityEndTime;
        //todo：事务
        $fCurrentDayTurnover = Project::getCurrentDayTurnover($oUserPrize->user_id, $oUserPrize->created_at);
//        $fTotalTurnover = UserProfit::getUserTotalTurnover($sBeginDate, $sEndDate, $oUserPrize->user_id);
        $fTotalTurnover = 0;
        $aCashBacks = ActivityCashBack::getDataByUserId($oUserPrize->user_id, self::YICI_FAN_PRIZE_ID);
        if (count($aCashBacks) > 0) {
            $oActivityCashBack = $aCashBacks->first();
        } else {
            $oActivityCashBack = new ActivityCashBack;
        }
        $oActivityCashBack->user_id = $oUserPrize->user_id;
        $oActivityCashBack->prize_id = self::YICI_FAN_PRIZE_ID;
        $oActivityCashBack->begin_date = $sBeginDate;
        $oActivityCashBack->end_date = $sEndDate;
        $oActivityCashBack->total_turnover = $fCurrentDayTurnover + $fTotalTurnover;
        $oActivityCashBack->save();
    }

}
