<?php

/**
 *  提供帐变报表下载功能，每日凌晨生成指定帐变类型的报表记录，提供下载功能给相关部门
 */
class ReportDownloadInfo extends BaseModel {

    public $orderColumns = [
        'id' => 'desc'
    ];
    protected $table = 'report_download_infos';
    public static $resourceName = 'ReportDownloadInfo';
    public static $columnForList = [
        'file_name',
        'file_type',
        'begin_time',
        'end_time',
        'created_at',
    ];
    public static $htmlSelectColumns = [
//        'file_type' => 'aReportTypes',
    ];
    public static $listColumnMaps = [
        // 'account_available' => 'account_available_formatted',
        'file_type' => 'report_type_formatted',
    ];

    protected function getReportTypeFormattedAttribute() {
        return __('_reportdownloadconfig.' . ReportDownloadConfig::$aReportType[$this->file_type]);
    }

}
