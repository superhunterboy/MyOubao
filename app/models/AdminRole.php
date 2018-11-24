<?php

/**
 * 管理员角色模型
 *
 * @author frank
 */
class AdminRole extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'admin_roles';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'name',
        'description',
        'rights',
        'priority',
        'is_system',
        'right_settable',
        'user_settable',
        'disabled',
        'sequence',
    ];
    public static $resourceName = 'AdminRole';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'description',
        'priority',
        'is_system',
        'right_settable',
        'user_settable',
        'disabled',
        'sequence',
    ];
    public static $sequencable = true;

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'priority' => 'aPriority',
    ];
    public static $aPriority = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    // 角色类型, 目前暂时有0:管理员, 1: 用户, 2: 特殊角色, 如黑白名单角色等
    public static $aRoleTypes = ['Admin', 'User']; // TIP 'Special' 特殊角色不必要，只需划分管理员和用户, 2014-08-19

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];
    public static $ignoreColumnsInEdit = ['rights'];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'name';
    public static $titleColumn = 'name';
    public static $rules = [
        'name' => 'required|max:40',
        'rights' => 'max:10240',
        'description' => 'max:255',
        'priority' => 'numeric',
        'is_system' => 'in:0,1',
        'right_settable' => 'in:0,1',
        'user_settable' => 'in:0,1',
        'disabled' => 'in:0,1',
        'sequence' => 'numeric',
    ];

    const ADMIN = 1;
    const EVERYONE = 2;
    const DENY = 3;
    const EVERY_USER = 4;
    const NORMAL_ADMIN = 6;
    const GRS_MANAGER = 7;
    const SOURCE_AUDITOR = 8;
    const MONITOR = 10;
    const ADMIN_ROLE_CUSTOMER_NORMAL = 28;
    const ADMIN_ROLE_CUSTOMER_MANAGER = 29;
    const ADMIN_ROLE_FINANCE_NORMAL = 39;
    const ADMIN_ROLE_FINANCE_MANAGER = 39;

    public static $aRoleCustomer = [self::ADMIN_ROLE_CUSTOMER_MANAGER, self::ADMIN_ROLE_CUSTOMER_NORMAL];
    public static $aRoleFinance = [self::ADMIN_ROLE_FINANCE_MANAGER, self::ADMIN_ROLE_FINANCE_NORMAL];

    public function admin_users() {
        return $this->belongsToMany('AdminUser', 'admin_role_users', 'role_id', 'user_id')->withTimestamps();
    }

    // public function users(){
    //     return $this->belongsToMany('User', 'admin_role_users', 'role_id', 'user_id')->withTimestamps();
    // }

    public static function updateUserRole($user_id, $role_id) {
        // pr($role_id .'------'.$user_id);
        $query = RoleUser::where('role_id', '=', $role_id)->where('user_id', '=', $user_id);
        $oUser = User::find($user_id);
        $oRole = self::find($role_id);
        $aParams = ['role_name' => $oRole->name, 'username' => $oUser->username];
        // pr((int)($query->get(['id'])->toArray()));exit;
        if ($query->get(['id'])->toArray())
            $bSucc = 2;
        else {
            self::find($role_id)->admin_users()->attach($user_id, $aParams);
            $bSucc = (int) $query->get(['id'])->toArray();
        }
        return $bSucc;
    }

    protected function beforeValidate() {
        $this->rights or $this->rights = 0;
        $this->priority or $this->priority = 0;
        $this->description or $this->description = '';
        return parent::beforeValidate();
    }

    /**
     * [getAllRoleNameArray 根据角色类型获取角色列表]
     * @param  [integer] $iRoleType [角色类型, 1: 用户, 0: 管理员 ]
     * @return [Array]             [角色列表]
     */
//    public static function getAllRoleNameArray($iRoleType = 0)
//    {
//        $data = [];
//        $aRoles = self::where('role_type', '=', $iRoleType)->get(['id', 'name']);
//        foreach ($aRoles as $key => $value) {
//            $data[$value->id] = $value->name;
//        }
//        return $data;
//    }

    public function getRightArrayAttribute() {
        return explode(',', $this->attribute['rights']);
    }

    /**
     * 返回指定角色ID序列的功能ID的合并后的数组
     * @param array $aRoleIds
     * @return type
     */
    public static function & getRightsOfRoles($aRoleIds) {
        $aRights = [];
        foreach ($aRoleIds as $iRoleId) {
            $aRightsOfRole = & self::getRights($iRoleId);
            $aRights = array_merge($aRights, $aRightsOfRole);
        }
        $aRights = array_unique($aRights);
        return $aRights;
    }

    /**
     * 返回指定角色的有权限的功能ID数组
     * @param int $iRoleId
     * @return array
     */
    public static function & getRights($iRoleId) {
//        exit;
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $obj = self::find($iRoleId);
//                pr($obj->toArray());
            $aRights = & $obj->explodeRights();
        } else {
            $key = self::getRightsCacheKey($iRoleId);
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            if (!$aRights = Cache::get($key)) {
                $obj = self::find($iRoleId);
//                pr($obj->toArray());
//                exit;
                $aRights = & $obj->explodeRights();
                Cache::forever($key, $aRights);
            }
//            pr($aRights);
        }
//        pr($aRights);
//        exit;
        return $aRights;
    }

    /**
     * 返回CacheKey
     * @param int $iRoleId
     * @return string
     */
    public static function getRightsCacheKey($iRoleId) {
        return get_called_class() . '-rights-' . $iRoleId;
    }

    /**
     * 返回有权限的功能ID的数组
     * @return array
     */
    public function & explodeRights() {
        if ($this->id == self::ADMIN) {
            $aRights = [];
            $objs = Functionality::whereIn('realm', [Functionality::REALM_SYSTEM, Functionality::REALM_ADMIN])->get(['id']);
            foreach ($objs as $obj) {
                $aRights[] = $obj->id;
            }
        } else {
            $aRights = explode(',', $this->rights);
        }
        return $aRights;
    }

    protected function afterSave($oSavedModel) {
        if (!parent::afterSave($oSavedModel)) {
            return false;
        }
        self::deleteRightCache($this->id);
//            return parent::afterSave($oSavedModel);
        return true;
    }

    /**
     * 删除角色权限缓存
     * @param int|array $mRoleId 如果为空，则意为删除全部
     * @return boolean
     */
    protected static function deleteRightCache($mRoleId = null) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        if (empty($mRoleId)) {
            $aRoleId = [];
            $oRoles = self::all(['id']);
            foreach ($oRoles as $oRole) {
                $aRoleId[] = $oRole->id;
            }
        } else {
            $aRoleId = (array) $mRoleId;
        }
        foreach ($aRoleId as $iRoleId) {
            $sCacheKey = self::getRightsCacheKey($iRoleId);
            !Cache::has($sCacheKey) or Cache::forget($sCacheKey);
        }
        return true;
    }

}
