<?php

class FunctionalityRelation extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'functionality_relations';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'FunctionalityRelation';
    public static $sequencable = true;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'functionality_id',
        'r_functionality_id',
        'label',
        'precondition',
        'params',
        'for_page',
        'for_item',
        'for_page_batch',
        'new_window',
        'use_redirector',
        'disabled',
        'sequence',
        'updated_at',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'sequence' => 'asc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'functionality_id' => 'aFunctionalities',
        'r_functionality_id' => 'aFunctionalities',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'functionality_id' => 'integer',
        'r_functionality_id' => 'integer',
        'precondition' => 'max:200',
        'params' => 'max:200',
        'label' => 'between:0,50',
        'for_page' => 'in:0, 1',
        'for_item' => 'in:0, 1',
        'for_page_batch' => 'in:0, 1',
        'new_window' => 'in:0, 1',
        'use_redirector' => 'in:0, 1',
        'disabled' => 'in:0, 1',
        'sequence' => 'integer',
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'functionality_id',
        'r_functionality_id',
        'for_page_batch',
        'for_page',
        'for_item',
        'label',
        'precondition',
        'params',
        'new_window',
        'use_redirector',
        'disabled',
        'sequence',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'functionality_id';
    public $autoPurgeRedundantAttributes = true;

    public function functionality_relations() {
        return $this->hasMany('FunctionalityRelation');
    }

    public function roles() {
        return $this->belongsToMany('AdminRole')->withTimestamps();
    }

    public function admin_menus() {
        return $this->hasMany('AdminMenu', 'functionality_id')->withTimestamps();
    }

    /**
     * Explode the rules into an array of rules.
     *
     * @param  string|array  $rules
     * @return array
     */
    protected function explodeRules($rules) {
        foreach ($rules as $key => &$rule) {
            $rule = (is_string($rule)) ? explode('|', $rule) : $rule;
        }

        return $rules;
    }

    protected function beforeValidate() {
        if (!$this->label) {
            $oRightFunctionality = Functionality::find($this->r_functionality_id);
            $this->label = $oRightFunctionality->title;
        }
        return parent::beforeValidate();
    }

    /**
     * 根据前置条件来判断是否显示
     * @param model $model
     * @return bool
     */
    public function isAvailable($model) {
        if (!$this->precondition)
            return true;
        $this->precondition = str_replace('.', '->', $this->precondition);
        $function = '$valid = ' . $this->precondition . ';';
        eval($function);
        return $valid;
    }

}
