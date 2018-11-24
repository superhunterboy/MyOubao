<?php

class CasinoMethod extends BaseModel
{
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'casino_methods';
    public $sPosition = null;
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'function',
        'lottery_id',
    ];

    public static $resourceName = 'CasinoMethod';
    public static $sequencable = false;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'function',
        'lottery_id',
    ];

    public static $titleColumn = 'name';
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_type' => 'aLotteryTypes',
        'type' => 'aMethodTypes',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'digital_count' => 'asc',
//        'sequence' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'lottery_type';
    public $digitalCounts = [];
    public static $rules = [
        'name' => '',
        'function' => '',
        'lottery_id' => 'integer',
    ];


}