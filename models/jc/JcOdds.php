<?php

namespace JcModel;
/**
 * 赔率模型
 */
class JcOdds extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcOdds';
    protected $table = 'jc_odds';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'lottery_id',
        'match_id',
        'odds',
        'method_id',
        'code'
    ];
    protected $fillable = [
        'id',
        'lottery_id',
        'match_id',
        'odds',
        'method_id',
        'code'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [];

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
        'lottery_id'              => 'required|integer',
        'match_id'             => 'required|integer',
        'odds'              => 'numeric | min:0',
        'method_id'              => 'required|integer',
        'code' => 'required',
    ];
    
    //public $timestamps = false;

    public static function getOddsByMatchIds($iLotteryId = 0, $aMatchId = [], $aColumn = ['*']){
        $oWhere = self::whereIn('match_id',$aMatchId);
        $oWhere->where('lottery_id',$iLotteryId);
        $oQuery = $oWhere->get($aColumn);
        return $oQuery;
    }
    
    public static function getOddsDataByMatchIds($iLotteryId = 0, $aMatchId = [], $aColumn = ['*']){
        $aOddsDataList = [];
        $aSelectMatchIds = [];
        foreach($aMatchId as $iMatchId){
            $aOddsData = self::getOddsDataCache($iMatchId);
            if ($aOddsData){
                $aOddsDataList[$iMatchId] = $aOddsData;
            }else{
                $aSelectMatchIds = $iMatchId;
            }
        }
        if (count($aSelectMatchIds) > 0){
            $oQuery = self::getOddsByMatchIds($iLotteryId, $aMatchId, $aColumn = ['*']);
            $aData = [];
            foreach($oQuery as $oRow){
                $aRowData = $oRow->getAttributes();
                $aRowData['name'] = $oRow->name;
                $aRowData['full_name'] = $oRow->full_name;
                $aData[$aRowData['match_id']][$aRowData['method_id']][$aRowData['code']] = $aRowData;
            }
            foreach($aData as $iMatchId => $aOddsData){
                self::setOddsDataCache($iMatchId, $aOddsData);
                $aOddsDataList[$iMatchId] = $aOddsData;
            }
        }
        
        return $aOddsDataList;
    }
    
    public static function getOddsDataCache($iMatchId){
        $sCacheKey = 'jc_match_odds_' . $iMatchId;
        return \Cache::get($sCacheKey);
    }
    public static function setOddsDataCache($iMatchId, $aOddsData){
        $sCacheKey = 'jc_match_odds_' . $iMatchId;
        return \Cache::put($sCacheKey, $aOddsData, 1800);
    }
    public static function deleteOddsDataCache($iMatchId){
        $sCacheKey = 'jc_match_odds_' . $iMatchId;
        return \Cache::forget($sCacheKey);
    }

    public function getNameAttribute() {
        if (!isset($this->attributes['name'])){
            $this->name = JcMethod::getCodeName($this->lottery_id, $this->code);
        }
        return $this->attributes['name'];
    }

    public function getFullNameAttribute() {
        if (!isset($this->attributes['full_name'])){
            $this->full_name = JcMethod::getCodeFullName($this->lottery_id, $this->code);
        }
        return $this->attributes['full_name'];
    }
    
     public static function getOddsByMatchIdsAndMeothid($aMatchId, $methodid, $aColumn= ['*']){
          $oWhere = self::whereIn('match_id',$aMatchId);
        $oWhere->where('method_id',$methodid);
        $oQuery = $oWhere->get($aColumn);
        return $oQuery;
    }
}
