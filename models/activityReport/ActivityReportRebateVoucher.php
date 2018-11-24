<?php

class ActivityReportRebateVoucher extends BaseModel {

    protected $table = 'activity_report_rebate_vouchers';
    public static $resourceName = 'ActivityReportRebateVoucher';
    public static $columnForList = [
                'username',
                'prize_name',
                'created_at',
                'remote_ip',
                'turnover_48h',
                'rebate_amount',
                'parent',
                'user_type_formatted',
    ];

}
