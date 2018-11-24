<?php

class ActivityReportDepositOnce extends BaseModel {

    protected $table = 'activity_report_deposit_1times';
    public static $resourceName = 'ActivityReportDepositOnce';
    public static $columnForList = [
        'username',
        'real_amount',
        'deposit_total_turnover',
        'turnover_32times_at',
        'created_at',
        'parent',
        'user_type_formatted',
    ];

}
