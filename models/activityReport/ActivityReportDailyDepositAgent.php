<?php

class ActivityReportDailyDepositAgent extends BaseModel {

    protected $table = 'activity_report_daily_deposit_agent';
    public static $resourceName = 'ActivityReportDailyDepositAgent';
    public static $columnForList = [
        'username',
        'rebate_amount',
        'rebate_date',
        'source_username',
        'deposit_amount',
    ];

    /**
     * 获取当天用户总奖金
     * @param int $iUserId  用户id
     * @param string $sYesterDay    日期
     */
    public static function getCurrentDayTotalBonus($iUserId, $sCurrentDate) {
        $aUserBonus = self::getCurrentDayBonus($iUserId, $sCurrentDate);
        $aBonus = [];
        foreach ($aUserBonus as $data) {
            $aBonus[] = $data['rebate_amount'];
        }
        $fTotalBonus = array_sum($aBonus);
        return $fTotalBonus;
    }

    public static function getCurrentDayBonus($iUserId, $sCurrentDate) {
        $oQuery = self::where('user_id', '=', $iUserId)->where('rebate_date', '=', $sCurrentDate);
        $aUserBonus = $oQuery->get(['rebate_amount']);
        $data = [];
        $i = 0;
        foreach ($aUserBonus as $oUserBonus) {
            $data[$i]['rebate_amount'] = $oUserBonus->rebate_amount;
            $i++;
        }
        return $data;
    }

}
