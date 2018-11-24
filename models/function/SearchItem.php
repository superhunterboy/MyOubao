<?php

class SearchItem extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'search_items';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'SearchItem';
    public static $sequencable = true;

    /**
     * 编辑框字段配置
     * @var array
     */
    public static $htmlTextAreaColumns = [
        'source',
        'match_rule'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'functionality_id' => 'aFunctionalities',
        'search_config_id' => 'aSearchForms'
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'search_config_id',
        'label',
        'type',
        'default_value',
        'div',
        'empty',
        'sequence'
    ];

    protected $fillable = [
        'search_config_id',
        'label',
        'model',
        'field',
        'type',
        'default_value',
        'source',
        'div',
        'empty',
        'empty_text',
        'match_rule',
        'sequence'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
//        'functionality_id' => 'integer',
        'search_config_id' => 'integer',
        'model' => 'between:1,32',
        'field' => 'between:1,32',
        'label' => 'between:1,32',
        'type' => 'between:1,32',
        'default_value' => 'between:1,32',
        'div' => 'in:0,1',
        'empty' => 'in:0,1',
        'match_rule' => 'required|between:1,1024',
        'sequence' => 'integer',
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
    public static $titleColumn = 'label';

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'search_config_id';

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * 解析给定的数据源，以数组的形式返回，仅用于type为select时
     *
     * @param string $sSource
     * @return array
     */
    public static function explainSource($sSource){
        $aSearchFields = [];
        switch ($sSource{0}) {
            case '*':   // 与模型关联
                $sRelatedConfig = substr($sSource, 1);
                $aRelatedConfig = explode('|', $sRelatedConfig);        // 分解，数组0为模型与字段信息，数组1为条件
                $aRelatedModelInfo = explode('.', $aRelatedConfig[0]);  // 分解模型与字段信息，数组0为模型名，数组1为字段
                $sRelatedModel = $aRelatedModelInfo[0];
                $oRelatedModel = App::make($sRelatedModel);             // Create Related Model
                if (isset($aRelatedModelInfo[1])) {                     // 如果指定了字段，则取此字段值的列表，注意：此时option的值与字段值相同
                    $sRelatedField = $aRelatedModelInfo[1];
                    $bGetSingleField = true;
                } else {                                        // 未指定字段，则意为options的来源为使用id为键，Model::$titleColumn字段为值的数组，此时Options的值为数字，即Model的ID
                    $sRelatedField = $sRelatedModel::$titleColumn;
                    $bGetSingleField = false;
                }
//                pr($sRelatedField);
//                pr($oRelatedModel);
//                exit;
                $aColumns = & $oRelatedModel->getColumnTypes();
//                pr($aColumns);
//                exit;
                $aInfoForFindMethod = [];
                $aConditions = [];
                if (array_key_exists('disabled', $aColumns)) {
                    $aConditions["disabled"] = ['=',false];
                }
//                pr(count($aRelatedConfig));
                if (count($aRelatedConfig) > 1) {
                    $aRelatedContidions = explode(',', $aRelatedConfig[1]);
//                    pr($aRelatedContidions);
                    foreach ($aRelatedContidions as $sCondition) {
                        list($sField, $sValue) = explode('=', $sCondition);
                        $aConditions[$sField] = ['=', $sValue == 'null' ? null : $sValue];
                    }
                }
//                pr($aConditions);
//                pr($bGetSingleField);
//                exit;
                if ($bGetSingleField) {
                    $source = $oRelatedModel->getValueListArray($sRelatedField, $aConditions);
                } else {
                    if ($sRelatedModel::$treeable && empty($aConditions)) {
                        $oRelatedModel->getTree($source,null,null,[$sRelatedModel::$titleColumn => 'asc']);
                    } else {
                        $source = $oRelatedModel->getValueListArray($sRelatedField, $aConditions, null, true);
                    }
                }
//                pr($source);
//                exit;
                break;
//                        pr($source);
            case '$':       // var mode, return the original string
                $source = trim($sSource);
                break;
            default:
                $source = explode("\n",$sSource);
                break;
        }
        return $source;
    }

    function beforeValidate(){
        if ($this->search_config_id){
            $oSearchConfig = SearchConfig::find($this->search_config_id);
//            $this->functionality_id = $oSearchConfig->functionality_id;
        }
        return parent::beforeValidate();
    }
}