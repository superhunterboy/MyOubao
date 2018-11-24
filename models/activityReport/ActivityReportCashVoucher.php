<?php

class ActivityReportCashVoucher extends BaseModel {

    protected $table = 'activity_report_cash_vouchers';
    public static $resourceName = 'ActivityReportCashVoucher';
    public static $columnForList = [
        'username',
        'prize_name',
        'remote_ip',
        'parent',
        'user_type_formatted',
    ];

}
