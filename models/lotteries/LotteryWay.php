<?php

class LotteryWay extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'lottery_ways';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'series_id',
        'lottery_id',
        'name',
        'short_name',
        'series_way_id',
    ];
    public static $resourceName = 'Lottery Way';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'series_id',
        'lottery_id',
        'name',
        'short_name',
        'series_way_id',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'series_id' => 'aSeries',
        'series_way_id' => 'aSeriesWays',
        'lottery_id' => 'aLotteries',
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
    public static $mainParamColumn = 'lottery_id';
    public static $rules = [
        'series_id' => 'required|integer',
        'lottery_id' => 'required|integer',
        'series_way_id' => 'required|integer',
        'name' => 'required|max:30',
        'short_name' => 'required|max:30',
    ];

    public static function & getLotteryWaysByLotteryId($iLotteryId) {
        $aData = [];
        $aColumns = ['series_way_id', 'name'];
        $aLotteryWays = $oQuery = self::where('lottery_id', '=', $iLotteryId)->get($aColumns);
        foreach ($aLotteryWays as $id => $value) {
            $aData[] = [
                'id' => $value->series_way_id,
                'name' => $value->name,
            ];
        }
        return $aData;
    }

}
