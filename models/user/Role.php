<?php

/**
 * 角色模型
 */
class Role extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'roles';

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
    public static $resourceName = 'Role';

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
        'non_expired',
        'disabled',
        'sequence',
    ];

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
    public static $mainParamColumn = 'role_type';
    public static $rules = [
        'name' => 'required|max:40',
        'rights' => 'max:10240',
        'description' => 'max:255',
        'priority' => 'numeric',
        'is_system' => 'in:0,1',
        'right_settable' => 'in:0,1',
        'user_settable' => 'in:0,1',
        'non_expired' => 'in:0,1',
        'disabled' => 'in:0,1',
        'sequence' => 'integer',
    ];

    //------------role_type-----------
    const ADMIN_ROLE = 0; // 后台管理员角色
    const USER_ROLE = 1; // 前台用户角色
    const BLACK_WHITE_ROLE = 2; // 黑白名单角色
    //------------role_type-----------
    //------------roles---------------
    const ADMIN = 1;
    const EVERYONE = 2;
    const DENY = 3;
    const EVERY_USER = 4;
    const NORMAL_ADMIN = 6;
    const GRS_MANAGER = 7;
    const SOURCE_AUDITOR = 8;
    const MONITOR = 10;
    const DENY_USER = 11;
    const TOP_AGENT = 12; // 总代
    const AGENT = 13; // 一代
    const PLAYER = 14; // 玩家
    const DONT_UP_PRIZE = 15; // 升点黑名单
    const DONT_DOWN_PRIZE = 16; // 降点黑名单
    const BONUS_BLACK = 17; // 分红黑名单
    const WITHDRAW_WHITE = 18; // 提现白名单
    const ICBC_DEPOSIT_WHITE = 19; // 工行充值白名单
    const WITHDRAW_BLACK = 21; // 提现黑名单
    const BAD_RECORD = 22; // 用户不良记录
    //------------roles---------------
    const BLACK_WHITE_LIST_ROLE_TYPE = 2;  // TODO, 角色表中黑白名单角色类型值，暂定

    // public function admin_users(){
    //     return $this->belongsToMany('AdminUser', 'admin_role_users', 'role_id', 'user_id')->withTimestamps();
    // }

    public function users() {
        return $this->belongsToMany('User', 'role_users', 'role_id', 'user_id')->withTimestamps();
    }

    //------------- 实际使用了roles表的rights字段来存储权限，functionality_role关联表没有使用, snowan comment on 2014-08-27
    // public function functionalities()
    // {
    //     return $this->belongsToMany('Functionality', 'functionality_role', 'role_id', 'role_id')->withTimestamps();
    // }
    //----------------------------------------------------------
    // TODO 待调整初始化黑白名单角色值
    // public static function initBlackWhiteListRoles()
    // {
    //     $columns = ['id', 'name', 'description'];
    //     $blackWhiteListRoles = [];
    //     $lists = Role::where('role_type', '=', BLACK_WHITE_LIST_ROLE_TYPE)->get($columns);
    //     foreach ($lists as $key => $value) {
    //         $blackWhiteListRoles[$value->id] = $value->name;
    //     }
    //     return $blackWhiteListRoles;
    // }

    public static function updateUserRole($user_id, $role_id, $sRolename, $sUsername) {
        // pr($role_id .'------'.$user_id);
        $query = RoleUser::where('role_id', '=', $role_id)->where('user_id', '=', $user_id);
        // $oUser = User::find($user_id);
        // $oRole = Role::find($role_id);
        $aParams = [
            'role_id' => $role_id,
            'user_id' => $user_id,
            'role_name' => $sRolename,
            'username' => $sUsername,
        ];
        // pr((int)($query->get(['id'])->toArray()));exit;
        if ($query->get(['id'])->toArray())
            $bSucc = 2;
        else {
            // Role::find($role_id)->users()->attach($user_id, $aParams);
            $oRoleUser = new RoleUser($aParams);
            $bSucc = $oRoleUser->save();
            // $bSucc = (int)$query->get(['id'])->toArray();
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
    public static function getAllRoleNameArray($iRoleType = 0) {
        $data = [];
        $aRoles = Role::where('role_type', '=', $iRoleType)->get(['id', 'name']);
        foreach ($aRoles as $key => $value) {
            $data[$value->id] = $value->name;
        }
        return $data;
    }

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
        if (is_array($aRoleIds)){
            foreach ($aRoleIds as $iRoleId) {
                $aRightsOfRole = & self::getRights($iRoleId);
                $aRights = array_merge($aRights, $aRightsOfRole);
            }
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
        if ($this->id == Role::ADMIN) {
            $aRights = [];
            $objs = Functionality::where('realm', '=', Functionality::REALM_ADMIN)->get(['id']);
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
