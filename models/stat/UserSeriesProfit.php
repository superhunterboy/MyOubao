<?php

/**
 * 用户盈亏表
 *
 * @author frank
 */
class UserSeriesProfit extends BaseModel {

    protected $table = 'user_series_profits';
    public static $resourceName = 'UserSeriesProfit';
    public static $amountAccuracy    = 6;
    public static $htmlNumberColumns = [
        'turnover' => 4,
        'prize' => 4,
        'profit' => 4,
    ];
    public static $columnForList = [
        'series_id',
        'date',
        'user_id',
        'turnover',
        'prize',
        'profit',
        'created_at',
        'updated_at',
    ];

    public static $totalColumns = [
        'turnover',
        'prize',
        'profit',
    ];

    public static $listColumnMaps = [
        'turnover' => 'turnover_formatted',
        'prize' => 'prize_formatted',
        'profit' => 'profit_formatted',
    ];
    protected $fillable = [
        'series_id',
        'date',
        'user_id',
        'turnover',
        'prize',
        'profit',
        'created_at',
        'updated_at',
    ];
    public static $rules = [
        'date' => 'required|date',
        'user_id' => 'required|integer',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'profit' => 'numeric',
    ];
    public $orderColumns = [
        'date' => 'desc'
    ];
    public static $mainParamColumn = 'user_id';

    public static function getProfitsBySeriesIdAndUserId($iSeriesId,$iUserId,$sStartTime,$sEndTime,$iCount){
        $oQuery = self::where('series_id',$iSeriesId)->where('user_id',$iUserId);
        if($sStartTime && $sEndTime) $oQuery->where('date','>=',$sStartTime)->where('date','<=',$sEndTime);
        else $oQuery->where('date','<=',date('Y-m-d'));
        return $oQuery->take($iCount)
                   ->orderBy('date', 'desc')
                   ->get();
    }
}
