<?php

/**
 *  下载报表配置信息
 */
class ReportDownloadRole extends BaseModel {

    protected $table = 'report_download_roles';
    public static $resourceName = 'ReportDownloadRole';
    public static $columnForList = [
        'admin_name',
        'report_type_id',
    ];
    protected $fillable = [
        'admin_user_id',
        'admin_name',
        'report_type_id',
    ];
    public static $rules = [
        'admin_user_id' => 'required',
        'report_type_id' => 'required|integer',
    ];
    public static $htmlSelectColumns = [
        'report_type_id' => 'aReportTypes',
        'admin_user_id' => 'aAdminUsers',
    ];
    public static $listColumnMaps = [
        // 'account_available' => 'account_available_formatted',
        'report_type_id' => 'report_type_formatted',
    ];

    /**
     * 判断用户是否有权限现在该报表
     * @param int $iReportTypeId  报表类型id
     * @param int $iUserId              后台管理员id
     * @return boolean
     */
    public static function checkRoleExist($iReportTypeId, $iUserId) {
        if (is_null($iReportTypeId) || is_null($iUserId))
            return false;
        return self::where('report_type_id', '=', $iReportTypeId)->where('admin_user_id', '=', $iUserId)->exists();
    }

    public static function getReportRoleByUserId($iUserId) {
        $aConditions = [
            'admin_user_id' => ['=', $iUserId]
        ];
        return self::doWhere($aConditions)->get();
    }

    public static function getReportTypeIdByUserId($iUserId) {
        $aReportRole = self::getReportRoleByUserId($iUserId);
        $aTypeId = [];
        foreach ($aReportRole as $oReportRole) {
            $aTypeId[] = $oReportRole->report_type_id;
        }
        return $aTypeId;
    }

    protected function beforeValidate() {
        if ($this->admin_user_id) {
            $oAdmin = AdminUser::find($this->admin_user_id);
            $this->admin_name = $oAdmin->username;
        }
        return parent::beforeValidate();
    }

    protected function getReportTypeFormattedAttribute() {
        return __('_reportdownloadconfig.' . ReportDownloadConfig::$aReportType[$this->report_type_id]);
    }

}
