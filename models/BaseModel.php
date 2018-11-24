<?php

class BaseModel extends \LaravelBook\Ardent\Ardent {

    const CACHE_LEVEL_NONE = 0;
    const CACHE_LEVEL_FIRST = 1;
    const CACHE_LEVEL_SECOND = 2;
    const CACHE_LEVEL_THIRD = 3;

    static protected $cacheUseParentClass = false;

    /**
     * cache level
     * @var int
     */
    static protected $cacheLevel = self::CACHE_LEVEL_NONE;

    /**
     * 缓存的有效时间
     * @var int
     */
    static protected $cacheMinutes = 0;

    /**
     *  图表展示数据横坐标
     */
    static public $columnForGraphX = '';

    /**
     *  图表展示数据
     */
    static public $columnForGraphList = array();

    /**
     * 可用的缓存级别
     * @var array
     */
    protected $validCacheLevels = array(self::CACHE_LEVEL_NONE, self::CACHE_LEVEL_FIRST, self::CACHE_LEVEL_SECOND, self::CACHE_LEVEL_THIRD);

    /**
     * 缓存驱动
     * @var array
     */
    static protected $cacheDrivers = array(self::CACHE_LEVEL_FIRST => 'memcached', self::CACHE_LEVEL_SECOND => 'redis', self::CACHE_LEVEL_THIRD => 'mongo');

    /**
     * 默认语言包
     * @var type
     */
    static public $defaultPreFix;
    static public $defaultLangPack;

    /**
     * if custom sequencable
     * @var true
     */
    static public $sequencable = false;

    /**
     * sequence column
     * @var string
     */
    static public $sequenceColumn = 'sequence';

    /**
     * 是否出现全选checkbox
     * @var type
     */
    static public $checkboxenable = false;

    /**
     * 自定义验证消息Att
     * @var array
     */
    protected $validatorMessages = array();

    /**
     * 区别前后台错误信息展示格式
     * @var boolean
     */
    protected $isAdmin = true;

    /**
     * valid cache levels
     * @var array
     */
    protected $iDefaultCacheLevel = 1;

    /**
     * 资源名称
     * @var string
     */
    static public $resourceName = '';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;

    /**
     * 建立实例时获取的字段数组
     * @var array
     */
    protected $defaultColumns = array('*');

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = array();

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = array();

    /**
     * If Tree Model
     * @var Bool
     */
    static public $treeable = false;

    /**
     * forefather id field
     * @var Bool
     */
    static public $foreFatherIDColumn = '';

    /**
     * forefather field
     * @var Bool
     */
    static public $foreFatherColumn = '';

    /**
     * the columns for list page
     * @var array
     */
    static public $columnForList = array();

    /**
     * 需要显示页面小计的字段数组
     * @var array
     */
    static public $totalColumns = array();

    /**
     * 加租显示的列
     * @var array
     */
    public static $weightFields = [];

    /**
     * 显示为不同颜色的列
     * @var array
     */
    public static $classGradeFields = [];
    public static $floatDisplayFields = [];

    /**
     * 需要显示记录总计的字段数组
     */
    static public $totalColumnsAllPages = array();

    /**
     * 不显示orderby按钮的列，供列表页使用
     * @var array
     */
    static public $noOrderByColumns = array();

    /**
     * ignore columns for view
     * @var array
     */
    static public $ignoreColumnsInView = array();

    /**
     * ignore columns for edit
     * @var array
     */
    static public $ignoreColumnsInEdit = array();

    /**
     * index视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    static public $listColumnMaps = array();

    /**
     * view视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    static public $viewColumnMaps = array();

    /**
     * 下拉列表框字段配置
     * @var array
     */
    static public $htmlSelectColumns = array();

    /**
     * 编辑框字段配置
     * @var array
     */
    static public $htmlTextAreaColumns = array();

    /**
     * number字段配置
     * @var array
     */
    static public $htmlNumberColumns = array();

    /**
     * 金额字段的存储精度
     * @var int
     */
    static public $amountAccuracy;

    /**
     * Columns
     * @var array
     */
    static public $originalColumns;

    /**
     * Column Settings
     * @var array
     */
    public $columnSettings = array();

    /**
     * order by config
     * @var array
     */
    public $orderColumns = array();

    /**
     * title field
     * @var string
     */
    static public $titleColumn = 'title';

    /**
     * the main param for index page
     * @var string
     */
    static public $mainParamColumn = 'parent_id';

    /**
     * save the column types
     * @var array
     */
    public $columnTypes = array();

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->comaileLangPack();
    }

    protected function getFriendlyCreatedAtAttribute() {
        return friendly_date($this->created_at);
    }

    protected function getFriendlyUpdatedAtAttribute() {
        return friendly_date($this->updated_at);
    }

    protected function getFriendlyDeletedAtAttribute() {
        return friendly_date($this->deleted_at);
    }

    public function parseKey($id) {
        return $this->getTable() . '_' . $id;
    }

    static public function find($id, $columns = array()) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            !empty($columns) || ($columns = array('*'));
            return parent::find($id, $columns);
        }

        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createCacheKey($id);

        if ($aAttributes = Cache::get($key)) {
            $obj = new static();
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::find($id);

            if (!is_object($obj)) {
                return false;
            }

            $data = $obj->getAttributes();

            if (static::$cacheMinutes) {
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $data);
            }
        }

        if (is_array($columns) && !empty($columns) && !in_array('*', $columns)) {
            $aAllColumns = array_keys($obj->attributes);
            $aExpertColumns = array_diff($aAllColumns, $columns);

            foreach ($aExpertColumns as $sColumn) {
                unset($obj->attributes[$sColumn]);
            }
        }

        return $obj;
    }

    public function deleteCache($sKeyData = NULL) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return true;
        }
        !empty($sKeyData) || ($sKeyData = $this->id);
        $key = self::createCacheKey($sKeyData);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) || Cache::forget($key);
    }

    public function getDataByParams($aOptions, $aOrderby = array('id', 'asc')) {
        $query = $this->orderBy($aOrderby[0], $aOrderby[1]);

        foreach ($aOptions['conditions'] as $key => $value) {
            $query = $query->where($value[0], $value[1], $value[2]);
        }

        $oData = $query->get($aOptions['columns']);
        return $oData;
    }

    public function makeColumnConfigures($bForEdit = true) {
        static::$originalColumns = Schema::getColumnListing($this->table);
        $this->columnTypes = $this->getColumnTypes();
        $rules = $this->explodeRules(static::$rules);
        $aColumnRules = array();

        if ($bForEdit) {
            $aIgnoreColumns = array($this->primaryKey, $this->getCreatedAtColumn(), $this->getUpdatedAtColumn(), $this->getDeletedAtColumn());
            $aIgnoreColumns = array_merge(static::$ignoreColumnsInEdit, $aIgnoreColumns);
        } else {
            $aIgnoreColumns = static::$ignoreColumnsInView;
        }

        if (static::$treeable) {
            $bForEdit || $aIgnoreColumns[] = 'parent_id';

            if (static::$foreFatherIDColumn) {
                $aIgnoreColumns[] = static::$foreFatherIDColumn;
                $aIgnoreColumns[] = static::$foreFatherColumn;
            }
        }

        $aIgnoreColumns = array_unique($aIgnoreColumns);

        foreach (static::$originalColumns as $sColumn) {
            if (in_array($sColumn, $aIgnoreColumns)) {
                continue;
            }

            if (isset(static::$htmlSelectColumns[$sColumn])) {
                $bDone = true;
                $aColumnRules[$sColumn]['type'] = 'select';
                $aColumnRules[$sColumn]['form_type'] = 'select';
                $aColumnRules[$sColumn]['options'] = static::$htmlSelectColumns[$sColumn];
                continue;
            }

            if (in_array($sColumn, static::$htmlTextAreaColumns)) {
                $bDone = true;
                $aColumnRules[$sColumn]['type'] = 'text';
                $aColumnRules[$sColumn]['form_type'] = 'textarea';
                continue;
            }

            $bDone = false;

            if (isset($rules[$sColumn])) {
                $bDone = true;
                $sFormType = 'text';
                $bRequired = false;

                foreach ($rules[$sColumn] as $sRule) {
                    $a = explode(':', $sRule);

                    switch ($a[0]) {
                        case 'required':
                            $bRequired = true;
                            $sType = 'text';
                            break;

                        case 'in':
                            if (str_replace(' ', '', $a[1]) == '0,1') {
                                $sType = 'bool';
                                $sFormType = 'bool';
                            } else {
                                $sFormType = 'select';
                                $sType = 'select';
                            }

                            break;

                        case 'between':
                            $sFormType = 'text';
                            $sType = 'string';
                            break;

                        case 'numeric':
                        case 'integer':
                            $sFormType = 'text';
                            $sType = $a[0];
                            break;

                        case 'min':
                        case 'max':
                            if (!isset($aColumnRules[$sColumn]['type'])) {
                                $sFormType = 'text';
                                $sType = 'string';
                            }

                            break;

                        default:
                            $sFormType = 'text';
                            $sType = 'string';
                    }

                    $aColumnRules[$sColumn]['required'] = $bRequired;
                    $aColumnRules[$sColumn]['type'] = $sType;
                    $aColumnRules[$sColumn]['form_type'] = $sFormType;
                }
            }

            if (!$bDone) {
                $aColumnRules[$sColumn]['form_type'] = 'ignore';
                $aColumnRules[$sColumn]['type'] = 'text';
            }
        }

        $this->columnSettings = $aColumnRules;
    }

    protected function explodeRules($rules) {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule) ? explode('|', $rule) : $rule);
        }

        return $rules;
    }

    public function getTree(&$aTree, $iParentId = NULL, $aConditions = array(), $aOrderBy = array(), $sTitlePrev = '--') {
        if (!static::$treeable) {
            return false;
        }

        static $deep = 0;
        $aConditions['parent_id'] = array('=', $iParentId);
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $deep++;
        $aModels = $oQuery->get(array('id', static::$titleColumn));

        foreach ($aModels as $oModel) {
            $sTitle = (empty($sTitlePrev) ? $oModel->{static::$titleColumn} : str_repeat($sTitlePrev, $deep - 1) . $oModel->{static::$titleColumn});
            $aTree[$oModel->id] = $sTitle;
            $this->getTree($aTree, $oModel->id, $aConditions, $aOrderBy, $sTitlePrev);
        }

        $deep--;
    }

    public function doOrderBy($oQuery = NULL, $aOrderBy = NULL) {
        $aOrderBy || ($aOrderBy = $this->orderColumns);
        $oQuery || ($oQuery = $this);

        foreach ($aOrderBy as $sColumn => $sDirection) {
            $oQuery = $oQuery->orderBy($sColumn, $sDirection);
        }

        return isset($oQuery) ? $oQuery : $this;
    }

    public function doGroupBy($oQuery = NULL, $aGroupBy = NULL) {
        $aGroupBy || ($aGroupBy = $this->groupByColumns);
        $oQuery || ($oQuery = $this);

        foreach ($aGroupBy as $sColumn) {
            $oQuery = $oQuery->groupBy($sColumn);
        }

        return isset($oQuery) ? $oQuery : $this;
    }

    public static function doWhere($aConditions = [], $oQuery = null) {
        is_array($aConditions) || ($aConditions = array());

        foreach ($aConditions as $sColumn => $aCondition) {
            if (!is_array($aCondition)) {
                $aCondition = array('=', $aCondition);
            }

            $sObject = (isset($oQuery) ? '$oQuery->' : 'self::');
            $statement = '';

            switch ($aCondition[0]) {
                case '=':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn , \'=\' , $aCondition[ 1 ]);';
                    }

                    break;

                case 'in':
                    $array = (is_array($aCondition[1]) ? $aCondition[1] : explode(',', $aCondition[1]));
                    $statement = '$oQuery = ' . $sObject . 'whereIn($sColumn , $array);';
                    break;

                case '>=':
                case '<=':
                case '<':
                case '>':
                case 'like':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNotNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn,$aCondition[ 0 ],$aCondition[ 1 ]);';
                    }

                    break;

                case '<>':
                case '!=':
                    if (is_null($aCondition[1])) {
                        $statement = '$oQuery = ' . $sObject . 'whereNotNull($sColumn);';
                    } else {
                        $statement = '$oQuery = ' . $sObject . 'where($sColumn,\'<>\',$aCondition[ 1 ]);';
                    }

                    break;

                case 'between':
                    $statement = '$oQuery = ' . $sObject . 'whereBetween($sColumn,$aCondition[ 1 ]);';
                    break;
            }

            eval($statement);
        }

        if (!isset($oQuery)) {
            $oQuery = self::where('id', '>', '0');
        }

        return $oQuery;
    }

    public function setForeFather() {
        if (!static::$treeable) {
            return false;
        }

        $sColumn = static::$foreFatherIDColumn;
        $oParentModel = $this->find($this->parent_id);
        $this->$sColumn = empty($oParentModel->$sColumn) ? $this->parent_id : $oParentModel->$sColumn . ',' . $this->parent_id;

        if ($this->$sColumn) {
            if ($this->parent_id) {
                $oParentModel = $this->find($this->parent_id);

                if ($sForeColumn = static::$foreFatherColumn) {
                    $this->$sForeColumn = empty($oParentModel->$sForeColumn) ? $oParentModel->{static::$titleColumn} : $oParentModel->$sForeColumn . ',' . $oParentModel->{static::$titleColumn};
                }
            }
        } else {
            $this->attributes[static::$foreFatherIDColumn] = '';

            if ($sForeColumn = static::$foreFatherColumn) {
                $this->attributes[$sForeColumn] = '';
            }
        }
    }

    protected function beforeValidate() {
        if (static::$treeable) {
            $this->parent_id = $this->parent_id;
        }

        return true;
    }

    protected function afterUpdate() {
        $this->deleteCache($this->id);
    }

    protected function afterSave($oSavedModel) {
        $sModelName = get_class($oSavedModel);
        $this->deleteCache($this->id);
        $bSucc = true;

        if ($sModelName::$treeable) {
            $aSubs = &$oSavedModel->getSubObjectArray($this->id);

            if ($aSubs) {
                foreach ($aSubs as $oModel) {
                    $oModel->parent_id = $this->id;

                    if (!($bSucc = $oModel->save())) {
                        break;
                    }
                }
            }
        }

        return $bSucc;
    }

    protected function afterDelete($oDeletedModel) {
        $this->deleteCache($oDeletedModel->id);
        return true;
    }

    public function& getColumnTypes() {
        if (empty($this->columnTypes)) {
            $sDatabase = $this->getConnection()->getConfig('database');
            $sql = 'select column_name, data_type from information_schema.columns where table_schema = \'' . $sDatabase . '\' and table_name = \'' . $this->table . '\' order by ordinal_position;';
            $aColumns = DB::select($sql);
            $data = array();

            foreach ($aColumns as $aConfig) {
                $data[$aConfig->column_name] = $aConfig->data_type;
            }

            $this->columnTypes = $data;
            return $data;
        } else {
            return $this->columnTypes;
        }
    }

    public function getValueListArray($sColumn = NULL, $aConditions = array(), $aOrderBy = array(), $bUsePrimaryKey = false) {
        $sColumn || ($sColumn = static::$titleColumn);
        $aColumns = ($bUsePrimaryKey ? array('id', $sColumn) : array($sColumn));
        $aOrderBy || ($aOrderBy = array($sColumn => 'asc'));
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oModels = $oQuery->get($aColumns);
        $data = array();

        foreach ($oModels as $oModel) {
            $sKeyField = ($bUsePrimaryKey ? $oModel->id : $oModel->$sColumn);
            $data[$sKeyField] = $oModel->$sColumn;
        }

        return $data;
    }

    public function& getValidationErrorString() {
        $aErrMsg = array();

        if ($this->isAdmin) {
            $aErrMsg = ($this->exists ? array($this->id . ':') : array($this->{static::$titleColumn} . ':'));

            foreach ($this->validationErrors->toArray() as $sColumn => $sMsg) {
                $aErrMsg[] = $sColumn . ': ' . implode(',', $sMsg);
            }
        } else {
            foreach ($this->validationErrors->toArray() as $sMsg) {
                $aErrMsg[] = implode(',', $sMsg);
            }
        }

        $sError = implode(' ', $aErrMsg);
        return $sError;
    }

    public function& getSubObjectArray($iParentId = NULL, $aConditions = array(), $aOrderBy = array()) {
        if (!static::$treeable) {
            return false;
        }

        $data = array();
        !empty($aConditions) || ($aConditions = array());
        $aConditions['parent_id'] = array('=', $iParentId);
        $oQuery = $this->doWhere($aConditions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oModels = $oQuery->get();

        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel;
        }

        return $data;
    }

    protected function setParentIdAttribute($iParentId) {
        $this->attributes['parent_id'] = $iParentId;
        $sModelName = get_class($this);

        if (array_key_exists('parent', $this->attributes)) {
            if ($iParentId && $this->parent) {
                $oParent = $sModelName::find($this->parent_id);
                $this->parent = $oParent->{static::$titleColumn};
            } else {
                $this->parent = '';
            }
        }

        if (static::$foreFatherIDColumn) {
            $this->setForeFather();
        }
    }

    static public function getObjectByParams(array $aParams = array('*')) {
        foreach ($aParams as $key => $value) {
            if (isset($oQuery) && is_object($oQuery)) {
                $oQuery = $oQuery->where($key, '=', $value);
            } else {
                $oQuery = self::where($key, '=', $value);
            }
        }

        return $oQuery->get()->first();
    }

    public static function getObjectCollectionByParams(array $aParams = ['*'], $aColumn = []) {
        //         $aParams or $aParams = ['*'];
        foreach ($aParams as $key => $value) {
            if (isset($oQuery) && is_object($oQuery)) {
                $oQuery = $oQuery->where($key, '=', $value);
            } else {
                $oQuery = self::where($key, '=', $value);
            }
        }
        if (count($aColumn) > 0) {
            return $oQuery->get($aColumn);
        } else {
            return $oQuery->get();
        }
    }

    protected function getFormattedNumberForHtml($sColumn) {
        $iAccuracy = (isset(static::$htmlNumberColumns[$sColumn]) ? static::$htmlNumberColumns[$sColumn] : static::$amountAccuracy);
        return number_format($this->$sColumn, $iAccuracy);
    }

    static protected function createCacheKey($data) {
        $sClass = get_called_class();
        !static::$cacheUseParentClass || ($sClass = get_parent_class($sClass));
        return $sClass . '_' . $data;
    }

    static public function& getTitleList($bOrderByTitle = true) {
        $aColumns = array('id', static::$titleColumn);
        $sOrderColumn = ($bOrderByTitle ? static::$titleColumn : 'id');
        $oModels = self::orderBy($sOrderColumn, 'asc')->get($aColumns);
        $data = array();

        foreach ($oModels as $oModel) {
            $data[$oModel->id] = $oModel->{static::$titleColumn};
        }

        return $data;
    }

    protected static function getRealClassForCache() {
        $sClass = get_called_class();
        !static::$cacheUseParentClass or $sClass = get_parent_class($sClass);
        return $sClass;
    }

    protected static function getCachePrefix($bPlural = false) {
        $sClass = self::getRealClassForCache();
        !$bPlural or $sClass = Str::plural($sClass);
        return Config::get('cache.prefix') . $sClass . '-';
    }

    static public function comaileLangPack() {
        $sClass = self::getRealClassForCache();
        return static::$defaultLangPack = '_' . strtolower($sClass);
    }

    static public function translate($sText, $iUcType = 3, $aReplace = array()) {
        return __(static::$defaultLangPack . '.' . strtolower($sText), $aReplace, $iUcType);
    }

    static public function translateArray(&$aTexts, $iUcType = 2, $aReplace = array()) {
        self::comaileLangPack();

        foreach ($aTexts as $key => $sText) {
            $aTexts[$key] = __(static::$defaultLangPack . '.' . strtolower($sText), $aReplace, $iUcType);
        }
    }

    protected function strictUpdate($aConditions, $data) {
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->afterUpdate();
        }
        return $bSucc;
    }

}

?>
