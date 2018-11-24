<?php

class SearchConfig extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'search_configs';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'SearchConfig';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'functionality_id' => 'aFunctionalities',
        'realm' => 'aValidRealms',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
		'name',
		'form_name',
		'row_size',
		'realm',
    ];

    protected $fillable = [
        'id',
		'name',
		'form_name',
		'row_size',
		'realm',
    ];

    /**
     * the main param for index page
     * @var string
     */
//    public static $mainParamColumn = 'functionality_id';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
//        'functionality_id'      => 'integer',
        'name'     => 'required|between:1,30',
        'form_name'     => 'between:1,64',
        'row_size' => 'integer',
        'realm'     => 'required|in:1,2',
    ];

    const REALM_ADMIN = 1;
    const REALM_USER  = 2;
    const REALM_ALL  = 3;
    public static $realms = [
        self::REALM_ADMIN => 'Admin',
        self::REALM_USER  => 'User',
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    /**
     * get search form config
     * @param int $iFunctionalityId
     * @return SearchConfig
     */
    public static function getForm($iFunctionalityId, $bAdmin = true) {
        $realms = static::$realms;
        $sGiveUpKey = $bAdmin ? self::REALM_USER : self::REALM_ADMIN;
        unset($realms[$sGiveUpKey], $sGiveUpKey);
        $oFunction = Functionality::find($iFunctionalityId);
        $realms = array_keys($realms);
        return self::where('id', '=', $oFunction->search_config_id)->whereIn('realm', $realms)->get(['id', 'name', 'row_size', 'form_name'])->first();
    }

    /**
     * get all items
     * @return Array
     */
    public function & getItems(){
        $data = [];
        $aItemObj = SearchItem::where('search_config_id', '=', $this->id)->orderBy('sequence')->get();
        foreach($aItemObj as $oSearchItem){
            $data[$oSearchItem->field] = $oSearchItem->getAttributes();
        }
        return $data;
    }


    public static function makeSearhFormInfo(& $aSearchItems, & $aParams, & $aSearchFields) {
        $bNeedCalendar = false;
        $aSearchFields = [];
        foreach ($aSearchItems as $aFieldInfo) {
            $aData = & $aFieldInfo;
            $sField = & $aData['field'];
            $aSearchFields[$sField] = array(
                'type' => $aData['type'],
//                'label' => false,
                'label' => strtolower($aData['label']),
                'value' => isset($aParams[$sField]) ? $aParams[$sField] : ''
            );
            switch ($aData['type']) {
                case 'select':
//                    $aSearchFields[$sField]['type'] = 'select';
                    $aSearchFields[$sField]['options'] = SearchItem::explainSource($aData['source']);
                    $aSearchFields[$sField]['div'] = false;
                    $aSearchFields[$sField]['empty'] = $aData['empty'] ? ($aData['empty_text'] ? $aData['empty_text'] : true) : false;
                    $aSearchFields[$sField]['is_date'] = false;
                    break;
                case 'date':
                    $aSearchFields[$sField]['type'] = 'text';
                    if (isset($aParams[$sField])) {
                        $aSearchFields[$sField]['value'] = $aParams[$sField];
                    } else {
                        $aSearchFields[$sField]['value'] = $aData['default_value'] == 'CURRENT_DATE' ? date('Y-m-d') : $aData['default_value'];
                    }
                    $aSearchFields[$sField]['dateFormat'] = 'YMD';
                    $aSearchFields[$sField]['maxYear'] = date('Y');
                    $aSearchFields[$sField]['div'] = $aData['div'];
                    $aSearchFields[$sField]['empty'] = $aData['empty_text'];
                    $aSearchFields[$sField]['is_date'] = true;
                    $bNeedCalendar = true;
                    break;
                default :
                    $aSearchFields[$sField]['is_date'] = false;
            }
        }
//        pr($aSearchFields);
//        exit;
        return $bNeedCalendar;
//        $this->setVars(compact('aSearchFields', 'bNeedCalendar'));
//        $this->setVars('searchConfig',$this->searchConfig);
    }

}