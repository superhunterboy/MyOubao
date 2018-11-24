<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 七日签到送礼金
 */
class ActivityDailySignRebate extends BaseCommand {

    protected $sFileName = 'activitydailysign';

    /**
     * The activity cash back command name.
     *
     * @var string
     */
    protected $name = 'activity:daily-sign';

    /**
     * The activity cash back description.
     *
     * @var string
     */
    protected $description = 'activity daily sign rebate';

    public function doCommand(& $sMsg = null) {
        $sDate = date('Y-m-d', strtotime('-1 day'));

        // 获取签到7天为派奖的记录
        $aDailySign = ActivityDailySign::getUnSendRewardRecord();
        Log::info($aDailySign);
        foreach ($aDailySign as $oDailySign) {
            $fTotalTurnover = 0;
            $aUserDailySign = ActivityDailySign::getLatestRecord($oDailySign->user_id, $oDailySign->id);
            foreach ($aUserDailySign as $oUserDailySign) {
                $fTotalTurnover += $oUserDailySign->turnover;
            }
            $fAmount = $fTotalTurnover * 0.005;
            $fAmount <=1888 or $fAmount = 1888;
            $fAmount = intval($fAmount);
            $aExtraData = [
                'turnover' => $fTotalTurnover,
                'rebate_amount' => $fAmount,
            ];
            $oUserPrize = new ActivityUserPrize();
            $oPrize = ActivityPrize::find(ActivityPrize::PRIZE_DAILY_SIGN);
            $oUserPrize->activity_id = 1;
            $oUserPrize->prize_id = $oPrize->id;
            is_null($aExtraData) or $oUserPrize->data = json_encode($aExtraData);
            $oUserPrize->count = 1;
            $oUserPrize->user_id = $oDailySign->user_id;
            $oUserPrize->source = 1;
            $oUserPrize->status = $oPrize->need_review ? ActivityUserPrize::STATUS_NO_SEND : ActivityUserPrize::STATUS_VERIRIED;
            $bSucc = $oUserPrize->save();
            $aExtraInfo = [
                'status' => ActivityUserPrize::STATUS_RECEVIED,
                'received_at' => date('Y-m-d H:i:s'),
            ];
            DB::connection()->beginTransaction();
            !$bSucc or $bSucc = $oUserPrize->setToSent($aExtraInfo);
            $oDailySign->is_send = 1;
            !$bSucc or $bSucc = $oDailySign->save();
            !$bSucc or $bSucc = $oUserPrize->addPrizeTask();
            if ($bSucc) {
                DB::connection()->commit();
            } else {
                DB::connection()->rollback();
            }
        }
    }

}
