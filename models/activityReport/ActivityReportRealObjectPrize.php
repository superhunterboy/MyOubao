<?php

class ActivityReportRealObjectPrize extends BaseModel {

    protected $table = 'activity_report_real_object_prizes';
    public static $resourceName = 'ActivityReportRealObjectPrize';
    public static $columnForList = [
        'username',
        'prize_name',
        'created_at',
        'remote_ip',
        'value',
        'activity_turnover',
        'parent',
        'user_type_formatted',
    ];

}
