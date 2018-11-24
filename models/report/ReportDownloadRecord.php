<?php

/**
 *  下载报表历史记录
 */
class ReportDownloadRecord extends BaseModel {

    protected $table = 'report_download_records';
    public static $resourceName = 'ReportDownloadRecord';
    public static $columnForList = [
        'report_name',
        'admin_name',
        'created_at',
    ];

    /**
     * 保存下载记录
     * @param int $iReportId  报表id
     * @param int $iUserId      用户id
     */
    public static function saveDownloadRecord($iReportId, $iUserId) {
        $oRecord = new ReportDownloadRecord;
        $oDownloadInfo = ReportDownloadInfo::find($iReportId);
        if (!is_object($oDownloadInfo)) {
            return false;
        }
        $oRecord->report_id = $iReportId;
        $oRecord->report_name = $oDownloadInfo->file_name;
        $oUser = AdminUser::find($iUserId);
        if (!is_object($oUser)) {
            return false;
        }
        $oRecord->admin_id = $iUserId;
        $oRecord->admin_name = $oUser->username;
        return $oRecord->save();
    }

}
