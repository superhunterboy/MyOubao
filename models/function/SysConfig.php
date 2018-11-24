<?php

class SysConfig extends BaseModel {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sys_configs';
    public $timestamps = false;
    public static $sequencable = true;

    private static $cacheKeyForArray = 'sys-config-array';
    private $configData = [];
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'SysConfig';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;

    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'parent_id' => 'aParentSysConfigs',
        'data_type' => 'aValidDataTypes',
        'form_face' => 'aValidInputTypes',
        'validation' => 'aValidValidateTypes',
    ];

    /**
     * 编辑框字段配置
     * @var array
     */
    public static $htmlTextAreaColumns = [
        'data_source'
    ];

    /**
     * all data type
     * @var array
     */
    public static $validDataTypes = [
        'none' => 'none',
        'bool' => 'bool',
        'int' => 'int',
        'float' => 'float',
        'string' => 'string',
        'array' => 'array',
    ];

    /**
     * all input type
     * @var array
     */
    public static $validInputTypes = [
        'none' => 'none',
        'text' => 'text',
        'edit' => 'edit',
        'select' => 'select',
        'multi_select' => 'multi_select',
        'checkbox' => 'checkbox',
        'radio' => 'radio',
    ];

    /**
     * all validate rules
     * @var array
     */
    public static $validValidateRules = [
        'none' => 'none',
        'display' => 'display',
        '<' => '<',
        '<=' => '<=',
        '==' => '==',
        '>=' => '>=',
        '>' => '>',
        'like %' => 'like %',
        'like %%' => 'like %%',
        'bool' => 'bool',
        'inlist' => 'inlist',
        'regular' => 'regular',
    ];

    /**
     * 软删除
     * @var bool
     */
    protected $softDelete = false;

    protected $fillable = ['id','parent_id','item','value','default_value','title','data_type','form_face','validation', 'data_source', 'description', 'sequence'];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
   public static $rules = [
        'item'          => 'required|between:1,255',
        'title'         => 'required|between:1,255',
        'value'         => 'required',
        'default_value' => 'between:1,1024',
        'data_type'     => 'required|in:none,bool,int,float,string,array',
        'form_face'     => 'required|in:none,text,edit,select,multi_select,checkbox,radio',
        'validation'    => 'required|in:none,display,<,<=,==,>=,>,like %,like %%,bool,inlist,regular',
        'data_source'   => 'between:0,1024',
        'description'   => 'between:0,1024',
//        'formated_value' => 'between:1,1024',
//        'formated_default_value' => 'between:1,1024',
        'sequence'  => 'integer'
    ];

    public static $columnForList = [
        'parent_id',
        'title',
        'item',
        'value',
        'default_value',
        'data_type',
        'form_face',
        'validation',
        'sequence',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'parent_id';

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [

    ];

    public $orderColumns = [
        'sequence' => 'asc'
    ];

    public function parent()
    {
        return $this->belongsTo('SysConfig', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('SysConfig', 'parent_id');
    }

    /**
     * 读取配置值，需要对返回值进行严格检查
     * @param string $sItem
     * @return string or bool    未设置时返回默认值，配置项不存在时返回false
     */
    public static function readValue($sItem) {
        if ($oConfig = self::_readByItem($sItem)){
            return strlen($oConfig->value) ? $oConfig->value : $oConfig->default_value;
        }
        else{
            return false;
        }
    }

    /**
     * 设置配置值
     * @param string $sItem
     * @param string $sValue
     * @return bool
     */
    public static function setValue($sItem, $sValue) {
        if (!$oConfig = self::_readByItem($sItem)){
            return false;
        }
        $oConfig->value = $sValue;
        return $oConfig->save();
    }

    /**
     * 返回指定配置项的数据源
     * @param string $sItem
     * @return string
     */
    public static function readDataSource($sItem){
        $oConfig = self::_readByItem($sItem);
        return $oConfig->data_source;
    }

    /**
     * 读取指定的配置值
     * @param string $sItem
     * @return SysConfig
     */
    protected static function _readByItem($sItem){
        $aColumns = ['id', 'value', 'default_value', 'data_type', 'validation', 'data_source'];
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key = self::createCacheKey($sItem);
//            echo $key.'<br />';
            if (!$data = Cache::get($key)){
                $obj = self::where('item','=',$sItem)->get()->first();
                //Log::info($sItem);
                Cache::forever($key,$data = $obj->getAttributes());
            }
            else{
                $obj = new static;
                $obj = $obj->newFromBuilder($data);
            }
            return $obj;
        }
        else{
            $obj = self::where('item','=',$sItem)->get()->first();
            return $obj;
        }
    }

    /**
     * 参数检验，根据设置的规则检查给定的参数是否合法
     * @param string $sItem
     * @param mixed $mValue
     * @return mixed            规则为none或display时为设置值，否则返回true或false
     */
    public static function check($sItem, $mValue = null) {
        $oConfig = self::_readByItem($sItem);
        if (empty($oConfig)){
            return false;
        }
        $mSetValue =  strlen($oConfig->value) ? $oConfig->value : $oConfig->default_value;
        $bValid = true;
        switch ($oConfig->validation) {
            case 'none':
            case 'display':
                $bValid = $mSetValue;
                break;
            case '<':
            case '<=':
            case '==':
            case '>=':
            case '>':
                $statement = "\$bValid = $mValue " . $oConfig->validation . " $mSetValue;";
                break;
            case 'like %':
                $sPattern = "!^{$mValue}!";
                $bValid = (bool)preg_match($sPattern, $mSetValue);
                break;
            case 'like %%':
                $sPattern = "!{$mValue}!";
                $bValid = (bool)preg_match($sPattern, $mSetValue);
                break;
            case 'bool':
                $bValid = (bool) $mValue == (bool) $mSetValue;
                break;
            case 'inlist':
                $aSetValue = explode(',', $mSetValue);
                $bValid = in_array($mValue, $aSetValue);
                break;
            case 'regular':
                $bValid = true;
                $aSetValue = explode(',',$mSetValue);
                foreach($aSetValue as $sPattern){
                    if (preg_match($sPattern, $mValue)){
                        $bValid = false;
                        break;
                    }
                }
                break;
        }
        return $bValid;
    }

    /**
     * 解析给定的字符串返回数据源供表单控件使用，控件为select,checkbox,radio
     * @param string $sDataSource
     * @return array
     */
    public static function getSource($sDataSource){
//        die($sDataSource);
        $aSource = explode("\n",$sDataSource);
        $aSource = array_map('trim',$aSource);
        $aDataSource = array();
        foreach($aSource as $sSource){
            if (!strlen($sSource))    continue;
            $aTmp = explode(':',$sSource);
            $sValue = $aTmp[0];
            isset($aTmp[1]) ? $sText = $aTmp[1] : $sText = $sValue;
            $aDataSource[trim($sValue)] = trim($sText);
        }
        return $aDataSource;
    }

    function format($mValue, $sType){
        return ($sType == 'bool') ? Config::get('var.boolean')[$mValue] : $mValue;
    }

    protected function afterSave($oSavedModel){
        if (parent::afterSave($oSavedModel) && static::$cacheLevel != self::CACHE_LEVEL_NONE){
            $this->deleteCache($this->item);
        }
    }

    /**
     * run before save()
     */
    protected function beforeValidate(){
        $this->formated_value = $this->format($this->value, $this->validation);
        $this->formated_default_value = $this->format($this->default_value, $this->validation);
        return parent::beforeValidate();
    }

    /**
     * get the groups array
     * @param bool $bEmpty
     * @return array
     */
    public static function getGoupArray($bEmpty = false)
    {
        $parentSysConfigs = $bEmpty ? ['' => ''] : [];
        $columns = (static::whereNull('parent_id')->get(['id', 'title']));
        foreach ($columns as $key => $value) {
            $parentSysConfigs[$value->id] = $value->title;
        }
        return $parentSysConfigs;
    }

    protected function setValueAttribute($mValue){
        switch($this->data_type){
            case 'bool':
                $mValue = intval((bool)$mValue);
                break;
            case 'int':
                $mValue = intval($mValue);
                break;
            case 'float':
                $mValue = floatval($mValue);
                break;
            case 'array':
                $mValue = implode(',', $mValue);
                break;
        }
        $this->attributes['value'] = $mValue;
    }

    protected static function createCacheKey($data){
        return get_called_class() . '_' . $data;
    }

}