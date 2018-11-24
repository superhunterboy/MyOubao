<?php

class ActivityReportPlayerStat extends BaseModel {

    protected $table = 'activity_report_player_stats';
    public static $resourceName = 'ActivityReportPlayerStat';
    public static $columnForList = [
        'username',
        'count_cash_voucher',
        'count_real_object',
        'prize_total_price',
        'count_lottery',
        'deposit_total_amount',
        'activity_total_turnover',
        'activity_total_profits',
        'register_at',
        'parent',
        'user_type_formatted',
//        'created_at',
    ];

}
