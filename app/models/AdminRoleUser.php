<?php

/**
 * 管理员角色关系模型
 *
 * @author frank
 */
class AdminRoleUser extends BaseModel {

    protected $table = 'admin_role_users';
    public static $resourceName = 'AdminRoleUser';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
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
        'created_at',
        'updated_at',
    ];
    public static $htmlSelectColumns = [
        'role_id' => 'aRoles',
        'user_id' => 'aAdministrators'
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $rules = [
        'role_id'        => 'required|integer',
        'user_id'        => 'required|integer',
    ];
    public static $mainParamColumn = 'role_id';

    protected function beforeValidate(){
        if ($this->role_id){
            $oRole = AdminRole::find($this->role_id);
            if (empty($oRole)){
                return false;
            }
            $this->role_name = $oRole->name;
        }
        if ($this->user_id){
            $oAdmin = AdminUser::find($this->user_id);
            if (empty($oAdmin)){
                return false;
            }
            $this->username = $oAdmin->username;
        }
        return parent::beforeValidate();
    }

    public static function checkUserRoleRelation ($role_id, $user_id){
        if (!$role_id || !$user_id) return false;
        return self::where('role_id', '=', $role_id)->where('user_id', '=', $user_id)->exists();
    }

}
