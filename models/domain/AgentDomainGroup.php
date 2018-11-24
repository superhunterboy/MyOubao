<?php

class AgentDomainGroup extends BaseModel {

    protected $table = 'agent_domain_groups';


    protected $fillable = [
        'id',
        'group_name',
        'status',
    ];
    public static $resourceName = 'AgentDomainGroup';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'group_name',
        'status',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'id';
    public static $rules = [
        'group_name'=>'required|',
        'status' => 'required|in:0,1',
    ];

    const IN_USE = 1;
    const NOT_IN_USE = 0;



    public static $aGroupStatus = [
        self::NOT_IN_USE => 'not-in-use',
        self::IN_USE     => 'in-use',
        // '2' => 'deleted',
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
    ];

    public static $htmlSelectColumns = [
        'status' => 'aGroupStatus',

    ];

    public static $viewColumnMaps = [

        'status' => 'formatted_status',
    ];

    protected function getFormattedStatusAttribute() {
        return __('_domain.' . static::$aGroupStatus[$this->status]);
    }



}
