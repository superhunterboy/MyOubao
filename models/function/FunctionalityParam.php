<?php

class FunctionalityParam extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'functionality_params';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'FunctionalityParam';
    public static $sequencable = true;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
		'functionality_id',
		'name',
		'type',
		'default_value',
		'limit_when_null',
        'sequence',
    ];

    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'functionality_id' => 'aFunctionalities',
        'type' => 'aValidTypes'
    ];

    protected $fillable = [
        'id',
		'functionality_id',
		'name',
		'type',
		'default_value',
		'limit_when_null',
        'sequence',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = array(
        'functionality_id' => 'integer',
        'name'     => 'required|between:1,32',
        'type'     => 'required|in:int,string',
        'limit_when_null' => 'in:0,1',
        'sequence'  => 'integer',
    );

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'functionality_id';

    public static function getParams($iFunctionalityId){
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE){
            return self::getParamsFromDb($iFunctionalityId);
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::getFunctionalityParamsCacheKey($iFunctionalityId);
        if (!Cache::has($key)){
            $aParams = self::getParamsFromDb($iFunctionalityId);
            Cache::forever($key, $aParams);
        }
        else{
            $aParams = Cache::get($key);
        }
        return $aParams;
    }

    private function deleteFunctionalityParamCache(){
        $key = $this->getFunctionalityParamsCacheKey($this->functionality_id);
        !Cache::has($key) or Cache::forget($key);
    }

    private static function & getParamsFromDb($iFunctionalityId){
        $data = [];
        $aParamModels = FunctionalityParam::where('functionality_id', '=' , $iFunctionalityId)->orderBy('sequence','asc')->get(['id','name','type','default_value','limit_when_null','sequence']);
//        die(count($aParamModels));
        foreach($aParamModels as $oModel){
            $data[$oModel->name] = $oModel->getAttributes();
        }
        unset($aParamModels);
        return $data;
    }

    private static function getFunctionalityParamsCacheKey($iFunctionalityId){
        return get_called_class() . '-functionality-' . $iFunctionalityId;
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
        $this->deleteFunctionalityParamCache();
        return true;
    }

}