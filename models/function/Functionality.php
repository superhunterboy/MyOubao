<?php

class Functionality extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected static $cacheMinutes = 0;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'functionalities';
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'Functionality';

    public static $sequencable = true;
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'title',
        'controller',
        'action',
        'refresh_cycle',
        'menu',
        'need_curd',
        'need_search',
        'need_log',
        'disabled',
        'sequence',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'parent_id';


    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'parent_id' => 'functionalitiesTree',
        'realm' => 'aValidRealms',
        'search_config_id' =>'aSearchConfigs',
    ];


    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;
    /**
     * forefather id field
     * @var Bool
     */
    public static $foreFatherIDColumn = 'forefather_ids';
    public static $foreFatherColumn = 'forefathers';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'title'       => 'required|between:1,50|unique:functionalities,title,%s,id,parent_id,%s',
        'controller'  => 'required|between:1,40',
        'search_config_id'  => 'integer',
        'action'      => 'required|alpha_dash|between:1,40',
        'description' => 'between:1,255',
        'refresh_cycle' => 'integer',
        'menu'     => 'in:0, 1',
        'need_curd'     => 'in:0, 1',
        'need_search'   => 'in:0, 1',
        'need_log'  => 'in:0, 1',
        'disabled'  => 'in:0, 1',
        'realm'     => 'required|in:0,1,2',
        'sequence'  => 'integer',
    ];

    const REALM_SYSTEM = 0;
    const REALM_ADMIN = 1;
    const REALM_USER  = 2;
    public static $realms = [
        self::REALM_SYSTEM => 'System',
        self::REALM_ADMIN => 'Admin',
        self::REALM_USER  => 'User',
    ];
    public static $ignoreColumnsInView = [
        'parent_id'
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [

    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'parent_id',
        'forefathers',
        'forefather_ids',
        'search_config_id',
        'title',
        'controller',
        'action',
        'description',
        'refresh_cycle',
        'menu',
        'need_curd',
        'need_search',
        'realm',
        'need_log',
        'disabled',
        'sequence'
    ];

    public $autoPurgeRedundantAttributes = true;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function functionality_relations()
    {
        return $this->hasMany('FunctionalityRelation');
    }
    //------------- 实际使用了roles表的rights字段来存储权限，functionality_role关联表没有使用, snowan comment on 2014-08-27
    // public function roles()
    // {
    //     return $this->belongsToMany('AdminRole', 'functionality_role', 'functionality_id', 'functionality_id')->withTimestamps();
    // }
    //--------------------------------------------------------

    public function admin_menus()
    {
        return $this->hasMany('AdminMenu', 'functionality_id')->withTimestamps();
    }

    /**
     * get the params configs
     * @param int $iFunctionalityId
     * @return array
     */
//    public static function getParams($iFunctionalityId){
//        $data = [];
//        $aParamModels = FunctionalityParam::where('functionality_id', '=' , $iFunctionalityId)->orderBy('sequence','asc')->get(['id','name','type','default_value','limit_when_null','sequence']);
////        die(count($aParamModels));
//        foreach($aParamModels as $oModel){
//            $data[$oModel->name] = $oModel->getAttributes();
//        }
//        unset($aParamModels);
//        return $data;
//    }

    protected function afterCreate($oCreatedModel){
        Role::deleteRightCache();
        return true;
    }

    /**
     * run after save
     * @param bool $bSucc
     * @return bool
     */
    protected function afterSave($oSavedModel){
        if (!parent::afterSave($oSavedModel)){
            return false;
        }
        $this->deleteCACache();
        if (!$oMenus = AdminMenu::where('functionality_id', '=', $this->id)->get()){
            return true;
        }
        $bSucc = true;
        foreach($oMenus as $oMenu){
            $oMenu->controller = $this->controller;
            $oMenu->action = $this->action;
            if (!$bSucc = $oMenu->save()){
                break;
            }
        }
//            return parent::afterSave($oSavedModel);
        return $bSucc;
    }

    protected function afterDelete($oDeletedModel){
        Role::deleteRightCache();
        return true;
    }

    public function getRelationFunctionalities($aFunctionalityIds = null,& $aRelationIds){
//        pr($aFunctionalityIds);
//        exit;
//        $oFunctionRelation = new FunctionalityRelation;
        !empty($aRelationIds) or $aRelationIds                        = [];
        $aConditions = [
            'functionality_id' => ['=', $this->id],
            'disabled' => ['=',0]
        ];
        is_null($aFunctionalityIds) or $aConditions[ 'r_functionality_id' ] = ['in',$aFunctionalityIds];
//        pr($aConditions);
//        exit;
        $oQuery                              = FunctionalityRelation::doWhere($aConditions);
        $oQuery                              = App::make('FunctionalityRelation')->doOrderBy($oQuery);
        $oRelations                          = $oQuery->get();
        // $aRelationIds                        = [];
        // pr($this->id);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);
        // pr($oRelations->toArray());exit;
        foreach ($oRelations as $oRelation){
            $aRelationIds[] = $oRelation->r_functionality_id;
        }
        return $oRelations;
    }

    protected function beforeValidate() {
        $this->refresh_cycle or $this->refresh_cycle = null;
        $this->search_config_id or $this->search_config_id = null;
        return parent::beforeValidate();
    }

    public static function & getActionArray($aFunctionalityIds = null){
        $data = [];
        if (empty($aFunctionalityIds)){
            return $data;
        }
        $aColumns = ['id','title','controller','action'];
        $oFunctionalities = self::whereIn('id',$aFunctionalityIds)->where('disabled','=',0)->orderBy('id','asc')->get($aColumns);
        foreach ($oFunctionalities as $oFunctionality){
            $data[ $oFunctionality->id ] = [$oFunctionality->title,$oFunctionality->controller,$oFunctionality->action];
        }
        return $data;
    }

    public static function getByCA($sController, $sAction, $bAdmin = false){
        $realms = static::$realms;
        $unUseFul = $bAdmin ? Functionality::REALM_USER : Functionality::REALM_ADMIN;
        unset($realms[$unUseFul]);
        $aNeedReams = array_keys($realms);
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            $sCacheKey = self::getCacheKeyByCA($sController,$sAction);
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            if ($aAttributes = Cache::get($sCacheKey)){
                $obj = new static;
                $obj = $obj->newFromBuilder($aAttributes);
//                pr($obj->getAttributes());
            }
            else{
                $obj = self::getByCAFromDb($sController,$sAction);
                if (is_object($obj)){
                    Cache::forever($sCacheKey, $obj->getAttributes());
                }
                else{
                    return false;
                }
            }
            if (!in_array($obj->realm,$aNeedReams)){
                unset($obj);
            }
        }
        if (!isset($obj)){
        // pr($this->controller);exit;
            $obj = self::getByCAFromDb($sController,$sAction,$aNeedReams);
        }
        return $obj;
    }

    private static function getByCAFromDb($sController, $sAction, $aNeedReams = false){
        $aCondtions = [
            'controller' => [ '=', $sController ],
            'action' => [ '=', $sAction ],
        ];
        !$aNeedReams or $aCondtions['realm'] = [ 'in', $aNeedReams];

        return self::doWhere($aCondtions)->get()->first();
    }

    private static function getCacheKeyByCA($sController = null, $sAction = null){
        return get_called_class() .  '-' . $sController . '@' . $sAction;
    }

    public function deleteCACache(){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) return true;
        $sCacheKey = $this->getCacheKeyByCA($this->controller, $this->action);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($sCacheKey) or Cache::forget($sCacheKey);
    }

    /**
     * 将用户类型的可能的全部功能信息数组放在$aTree
     * @param int $iRealm
     */
    public static function getTreeArray(& $aTree, $iParentId = null, $iRealm = null){
        static $deep = 0;

        $aColumns = ['id', 'title', 'parent_id', 'forefather_ids', 'description', 'controller', 'action', 'realm', 'disabled'];
        $oQuery = self::orderBy('sequence');
        $oQuery = is_null($iParentId) ? $oQuery->whereNull('parent_id') : $oQuery->where('parent_id' , '=', $iParentId);
        !$iRealm or $oQuery = $oQuery->where('realm','=',$iRealm);
        $oFunctionalities = $oQuery->get($aColumns);

        $deep++;
        foreach ($oFunctionalities as $oFunctionality){
            $aTree[ $oFunctionality->id ] = $oFunctionality->getAttributes();
            $aTree[ $oFunctionality->id]['level'] = $deep;
            self::getTreeArray($aTree,$oFunctionality->id, $iRealm);
        }
        $deep--;
    }
}