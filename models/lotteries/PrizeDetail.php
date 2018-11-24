<?php

class PrizeDetail extends BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'prize_details';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'series_id',
        'group_id',
        'group_name',
        'classic_prize',
        'probability',
        'method_id',
        'level',
        'probability',
        'prize',
        'full_prize'
    ];

    public static $amountAccuracy    = 4;

    /**
     * number字段配置
     * @var array
     */
    public static $htmlNumberColumns = [
        'prize' => 2
    ];

    public static $resourceName = 'Prize Detail';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'method_id',
        'group_name',
        'level',
//        'probability',
        'prize',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'type' => 'aLotteryTypes',
        'method_id' => 'aBasicMethods',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'method_id' => 'asc',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'group_id';

    public $digitalCounts = [];
    public static $rules = [
        'group_id' => 'required|integer',
        'method_id' => 'required|integer',
        'level'           => 'required|numeric',
        'probability'     => 'required|numeric|max:0.9',
        'prize'      => 'numeric',
    ];

    protected function beforeValidate(){
//        if (empty($this->basic_method_id)){
//            return false;
//        }
//        $oBasicMethod = BasicMethod::find($this->basic_method_id);
//        $this->lottery_type_id = $oBasicMethod->type;
        return parent::beforeValidate();
    }

    public static function & getPrizeSetting($iGroupId,$iBasicMethodId){
        $a = self::where('group_id', '=', $iGroupId)->where('method_id', '=', $iBasicMethodId)->get(['level','prize']);
        $aPrize = [];
        foreach($a as $oPrizeDetail){
            $aPrize[$oPrizeDetail->level] = $oPrizeDetail->prize;
        }

        return $aPrize;
    }

    public static function getPrizes($iGroupId, $iMethodId){
        $oSettings = self::where('group_id', '=', $iGroupId)->where('method_id', '=', $iMethodId)->get(['level','prize']);
        $data = [];
        foreach($oSettings as $oDetail){
            $data[$oDetail->level] = $oDetail->prize;
        }
        ksort($data);
        return $data;
    }

    public static function & getDetails($iGroupId){
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE){
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::makeCacheKeyOfGroup($iGroupId);
            if ($aDetails = Cache::get($sCacheKey)) {
                $bReadDb = false;
            }
            else{
                $bPutCache = true;
            }
        }
        if ($bReadDb){
            $oPrizeDetails = PrizeDetail::where('group_id', '=', $iGroupId)->get();
//        $oBasicMethods = BasicMethod::all();
            $aDetails = [];
    //        $aBasicMethods = [];
    //        foreach($oBasicMethods as $oBasicMethod){
    //            $aBasicMethods[$oBasicMethod->id] = $oBasicMethod;
    //        }

            foreach($oPrizeDetails as $oPrizeDetail){
                $aDetails[$oPrizeDetail->method_id] = $oPrizeDetail->getAttributes();
            }
        }
        if ($bPutCache){
            Cache::forever($sCacheKey, $aDetails);
        }
        return $aDetails;

    }

    public static function countPrize($oSeries, $fClassicPrize, $iHighEstGroup, & $aBasicLevel){
//        pr(func_get_args());
        switch($oSeries->type){
            case Lottery::LOTTERY_TYPE_DIGITAL:
                $fPrize = formatNumber($fClassicPrize / $oSeries->classic_amount * $aBasicLevel['full_prize'], 4);
                $aBasicLevel['max_group'] >= $iHighEstGroup or $fPrize *= $aBasicLevel['max_group'] / $iHighEstGroup;
                break;
            case Lottery::LOTTERY_TYPE_LOTTO:
                $fPrize = $aBasicLevel['full_prize'] * ($fClassicPrize - $iHighEstGroup + $aBasicLevel['max_group']) / $oSeries->classic_amount;
                break;
        }
        return formatNumber($fPrize, self::$amountAccuracy);
    }

    private static function makeCacheKeyOfGroup($iGroupId){
        return get_called_class() . '_group_' . $iGroupId;
    }

}