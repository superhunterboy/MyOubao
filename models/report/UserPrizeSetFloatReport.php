<?php

/**
 * 分红统计表
 *
 * @author abel
 */
class UserPrizeSetFloatReport extends BaseModel {

    protected $table = 'user_prize_set_float_reports';
    
    public static $resourceName = 'UserPrizeSetFloatReport';
    public static $columnForList = [
        'created_at',
        'updated_at',
        'user_id',
        'username',
        'is_up',
        'old_prize_group',
        'new_prize_group',
        'standard_turnover',
        'total_team_turnover',
    ];
    public $orderColumns = [
        'created_at' => 'asc',
    ];

}
