<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 16-7-4
 * Time: 下午3:23
 */
class CasinoWay extends BaseModel{
    
    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'casino_ways';



    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'wn_function',
        'name',

    ];
    
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_type' => 'aLotteryTypes',
    ];
    
    public static $listColumnMaps = [
        'lottery_id' => 'friendly_name'
    ];

    protected $fillable = [
        'lottery_id',
        'wn_function',
        'name',
    ];
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
    public static $mainParamColumn = 'lottery_type';
    public static $titleColumn = 'name';
    public static $rules = [
        'lottery_type' => '',
        'name'          => '',
        'description'   => '',
        'function'     => '',
        'sequence'      => '',
    ];

    static function getWaysByLotterId($lotteryId){

        return self::where('lottery_id',$lotteryId)->get();
    }
    static function getMethodsByCondition($aCondtions=array()){

        return self::doWhere($aCondtions)->get();
    }




}
