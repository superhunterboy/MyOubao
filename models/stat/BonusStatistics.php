<?php

/**
 * 分红统计表
 *
 * @author abel
 */
class BonusStatistics extends BaseModel {

    protected $table = 'bonus_statistics';
    public static $resourceName = 'BonusStatistics';
    public static $columnForList = [
        'total_turnover',
        'total_bonus',
        'total_agent_count',
        'total_profit',
        'statistics_date',
    ];
    public $orderColumns = [
        'statistics_date' => 'asc',
    ];

    public static function getLastBonusStatisticsDate() {
        return DB::table('bonus_statistics')->max('statistics_date');
    }

}
