<?php

namespace JcModel;
/**
 * 过关方式模型
 */
class JcWay extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcWay';
    protected $table = 'jc_ways';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_id',
        'name',
        'identifier',
        'function',
        'description',
        'choose_count',
        'all_count',
        'child_ways',
        'rule',
        'prize_limit',
    ];
    protected $fillable = [
        'lottery_id',
        'name',
        'identifier',
        'function',
        'description',
        'choose_count',
        'all_count',
        'child_ways',
        'rule',
        'prize_limit',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [
        'lottery_id' => 'validLotteries'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id' => 'required',
        'name' => 'required',
        'identifier' => 'required',
        'function' => '',
        'description' => '',
        'choose_count' => 'required',
        'all_count' => 'required',
        'child_ways' => '',
        'rule' => '',
        'prize_limit' => 'integer',
    ];
    
    //public $timestamps = false;
    
    /*
    public function getWayRuleByIdentifier($identifier = ''){
        $oQuery = self::where('identifier', '=', $identifier)->limit(1)->get(['rule'])->first();
        return $oQuery ? $oQuery->rule : null;
    }
     * 
     */
    
    public static function getWayByLotteryId($iLotteryId = 0){
        $oQuery = self::where('lottery_id', '=', $iLotteryId)->get();
        return $oQuery;
    }
    
    public static function getWayByLotteryIdAndIdentifier($iLotteryId = 0, $identifier = ''){
        $oQuery = self::where('identifier', '=', $identifier)->where('lottery_id', '=', $iLotteryId)->limit(1)->get()->first();
        return $oQuery;
    }
    
    public static function getWayByLotteryIdAndIdentifiers($iLotteryId = 0, $aIdentifiers = ''){
        $oQuery = self::whereIn('identifier', $aIdentifiers)->where('lottery_id', '=', $iLotteryId)->get();
        return $oQuery;
    }
    
    public static function getWayByWayIds($aWayIds = []){
        $oQuery = self::whereIn('id', $aWayIds)->get();
        return $oQuery;
    }
    
    public static function checkIsSingleWay($oWay){
        return $oWay->choose_count == 1 && $oWay->all_count == 1;
    }

    protected function setChildWaysAttribute(){
        if(!$this->attributes['rule']) return '';
        $aWays = json_decode($this->attributes['rule'],true);
        $aIdendifier = array_keys($aWays);
        $oWays = self::getWayByLotteryIdAndIdentifiers($this->lottery_id, $aIdendifier);
        $aIds = [];
        if(!$oWays) return '';
        foreach($oWays as $v){
            $aIds[] = $v['id'];
        }
        $this->attributes['child_ways'] = implode(',',$aIds);
    }

}
