<?php

class ActivityReportRegister10 extends BaseModel {

    protected $table = 'activity_report_register_10s';
    public static $resourceName = 'ActivityReportRegister10';
    public static $columnForList = [
        'username',
        'register_at',
        'lock_time',
        'register_ip',
        'parent',
        'user_type_formatted',
    ];

}
