<?php

# 用户和角色的关联

class RoleUser extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'role_users';
    public static $resourceName = 'RoleUser';
    public static $mainParamColumn = 'role_id';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    public static $titleColumn = 'name';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'role_id',
        'user_id',
        'username',
        'role_name',
    ];
    public static $columnForList = [
        'username',
        'role_name',
        'add_date',
        'expire_date',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    // public static $htmlSelectColumns = [
    //     'user_id' => 'aUsers',
    //     'role_id' => 'aRoles',
    // ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $rules = [
        'role_id' => 'required|integer',
        'user_id' => 'required|integer',
        'expire_date' => 'date',
        'add_date' => 'date',
    ];
    public static $htmlSelectColumns = [
        'role_id' => 'aUserRoles',
    ];

    /**
     * 
     * @param int $role_id      角色
     * @param int $user_id     用户
     * @return boolean      核实用户角色是否过期,或存在
     */
    public static function checkUserRoleRelation($role_id, $user_id) {
        if (!$role_id || !$user_id)
            return false;
        $oRoleUser = self::where('role_id', '=', $role_id)->where('user_id', '=', $user_id)->first();
        if (is_object($oRoleUser)) {
            if (empty($oRoleUser->expire_date) || strtotime($oRoleUser->expire_date) > time()) {
                return true;
            } else if (empty($oRoleUser->add_date) || strtotime($oRoleUser->add_date) < time()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function beforeValidate() {
        if ($this->role_id) {
            $oRole = Role::find($this->role_id);
            if (empty($oRole)) {
                return false;
            }
            $this->role_name = $oRole->name;
        }
        if ($this->user_id) {
            $oUser = User::find($this->user_id);
            if (empty($oUser)) {
                return false;
            }
            $this->username = $oUser->username;
        }
        return parent::beforeValidate();
    }

    /**
     * 根据role_id获取用户id信息数组
     * @param int $iRoleId  角色id
     */
    public static function getUserIdsFromRoleId($iRoleId) {
        $data = [];
        $oUserIds = self::where('role_id', '=', $iRoleId)->get(['user_id']);
        foreach ($oUserIds as $value) {
            $data[] = $value->user_id;
        }
        return $data;
    }

    /**
     * 根据role_id, user_id获取用户角色关系学数据
     * @param int $iRoleId  角色id
     * @param int $iUserId  用户id
     */
    public static function getUserRoleFromUserIdRoleId($iRoleId, $iUserId) {
        $oRoleUser = self::where('role_id', '=', $iRoleId)->where('user_id', '=', $iUserId)->first();
        return $oRoleUser;
    }

}
