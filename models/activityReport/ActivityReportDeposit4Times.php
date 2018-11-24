<?php

class ActivityReportDeposit4Times extends BaseModel {

    protected $table = 'activity_report_deposit_4times';
    public static $resourceName = 'ActivityReportDeposit4Times';
    public static $columnForList = [
        'username',
        'real_amount',
        'cash_bank_weekly',
        'first_week',
        'second_week',
        'third_week',
        'fourth_week',
        'fifth_week',
        'sixth_week',
        'seventh_week',
        'created_at',
        'parent',
        'user_type_formatted',
    ];

}
