<?php

class AuditList extends BaseModel {
    const STATUS_IN_AUDITING = 0;
    const STATUS_AUDITED = 1;
    const STATUS_REJECTED = 2;
    const STATUS_CANCELED = 3;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'audit_lists';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'AuditList';

    public static $columnForList = [
        'type_id',
        'username',
        'admin_name',
        'auditor_name',
        'description',
        'status',
        'updated_at',
    ];

    public static $htmlSelectColumns = [
        'type_id' => 'aAuditTypes',
        'status' => 'aStatus', // 0:审核中, 1: 审核通过, 2: 审核拒绝, 3: 撤销密码重置
    ];

    public static $statusDesc = [
        self::STATUS_IN_AUDITING => 'in-auditting',
        self::STATUS_AUDITED     => 'audited',
        self::STATUS_REJECTED    => 'rejected',
        self::STATUS_CANCELED    => 'canceled'
    ];

    protected $fillable = [
        'id',
        'type_id',
        'user_id',
        'admin_id',
        'auditor_id',
        'username',
        'admin_name',
        'auditor_name',
        'audit_data',
        'description',
        'status',
    ];
    public static $rules = [
        'type_id'       => 'required|integer',
        'user_id'       => 'required|integer',
        'admin_id'      => 'required|integer',
        'auditor_id'    => 'integer',
        'username'     => 'required|between:0,16',
        'admin_name'    => 'required|between:0,16',
        'auditor_name'  => 'between:0,16',
        'audit_data'    => 'required',
        'description'   => 'between:0,255',
        'status'        => 'integer|in:0,1,2,3',
    ];
    public $orderColumns = [
        'updated_at' => 'desc'
    ];

    /**
     * [explodeParams 解析审核数据]
     * @param  [Object] $filledModel [待填充数据的审核Model对象]
     * @return [Object]              [填充完毕的数据的审核Model对象]
     */
    public function explodeParams(& $filledModel)
    {
        if (! $this->audit_data) return false;
        $aParam = explode(',', $this->audit_data);
        foreach ($aParam as $key => $value) {
            $aItem = explode('=', $value);
            $filledModel->{$aItem[0]} = $aItem[1];
            // pr($aItem[1]);
            // pr(Hash::check('Userfund123', $aItem[1]));
            // pr($filledModel->{$aItem[0]} . '------' . $aItem[1]);exit;
        }
        return true;
        // pr($filledModel);exit;
        // return $filledModel;
    }
    // /**
    //  * [_makeRules 创建审核记录的更新规则]
    //  * @param  integer $type   [规则类型, 0: 新增, 1:审核通过/拒绝/取消]
    //  * @return [Array]          [规则数组]
    //  */
    // protected static function _makeRules($type = 0)
    // {
    //     $aRules = [];
    //     switch ($type) {
    //         case 1:
    //             $aRules = ['status' => 'required|numeric|in:0,1,2,3'];
    //             break;
    //         case 0:
    //         default:
    //             $aRules = static::$rules;
    //             break;
    //     }
    //     return $aRules;
    // }

    // /**
    //  * [compileAuditData 拼装审核记录数据]
    //  * @param  [Array] $modelData [模型数据, 来自被管理员操作的用户模型]
    //  * @param  [String] $sDesc  [描述信息, 管理员提交审核记录时填写]
    //  * @param  [String] $sAuditData [审核数据]
    //  * @param  [Integer] $iType [审核类型, 目前只有 1: 密码审核, 2: 资金密码审核]
    //  * @return [Array]            [拼装好的审核记录格式的数据]
    //  */
    // protected static function compileAuditData ($modelData, $sDesc, $sAuditData, $iType)
    // {
    //     // $oCurrentUserInfo = Auth::admin()->get()->toArray();
    //     $iAdminId       = Session::get('admin_user_id');
    //     $sAdminUsername = Session::get('admin_username');
    //     $data = [
    //         'type_id'     => $iType,
    //         'user_id'     => $modelData['id'],
    //         'admin_id'    => $iAdminId,
    //         'username'    => $modelData['username'],
    //         'admin_name'  => $sAdminUsername,
    //         'audit_data'  => $sAuditData,
    //         'description' => $sDesc,
    //         'status'      => 0,
    //     ];
    //     return $data;
    // }

}