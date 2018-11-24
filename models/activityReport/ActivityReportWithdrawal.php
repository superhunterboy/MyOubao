<?php

class ActivityReportWithdrawal extends BaseModel {

    protected $table = 'activity_report_withdrawals';
    public static $resourceName = 'ActivityReportWithdrawal';
    public static $columnForList = [
        'username',
//        'transaction_amount',
        'parent',
        'user_type_formatted',
        'created_at',
    ];

}
