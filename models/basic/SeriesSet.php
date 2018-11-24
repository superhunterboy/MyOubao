<?php

class SeriesSet extends BaseModel {

    public static $resourceName      = 'SeriesSet';
    protected $table                 = 'series_sets';

    public static $titleColumn = 'name';


    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'type_id',
        'series_ids',
        'name',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'type_id' => 'aTypeIds',
    ];

    public $timestamps = false;

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'type_id'    => 'required|integer',
        'series_ids'    => 'between:0,100',
    ];

    protected $fillable = [
        'type_id',
        'series_ids',
        'name',
    ];

    const TYPE_LOTTERY    = 1;  //彩票
    const TYPE_ELECTRONIC = 2;  //电子游戏
    CONST TYPE_SPORT      = 3;  //竞彩

    CONST ID_LOTTERY         = 1; //所有彩票
    CONST ID_DICE            = 2; //骰宝
    CONST ID_LHD             = 3; //龙虎斗
    CONST ID_BJL             = 4; //百家乐
    CONST ID_FOOTBALL_SINGLE = 5; //足球单关
    CONST ID_FOOTBALL_MIX    = 6; //足球混合

    public static $aTypeIds   = [
      self::TYPE_LOTTERY    => 'lottery',
      self::TYPE_ELECTRONIC => 'electronic',
      self::TYPE_SPORT      => 'sport',
    ];

    public static $aSequenceIds = [
      self::ID_LOTTERY,
//      self::ID_FOOTBALL_MIX,
//      self::ID_FOOTBALL_SINGLE,
      self::ID_DICE,
      self::ID_BJL,
      self::ID_LHD,
    ];

    public static $aSingleIds = [
        self::ID_FOOTBALL_SINGLE,
    ];
    
    public static $aMixIds = [
        self::ID_FOOTBALL_MIX,
    ];

    public static function getTypeId($iSeriesId){
        $oSeriesSet = self::whereRaw(' find_in_set(?, series_ids)', [$iSeriesId])->first();
        if($oSeriesSet) return $oSeriesSet->type_id;

        return false;
    }
    /***
     * 通过seriesId获取seriesSetId
     */
    public static function getSeriesSetIdBySeriesId($series_id=null){
        if(!$series_id) return 1;
        $cache_key = "seriesSet-series_id-".$series_id;
        $iSeriesSetId = Cache::get($cache_key);
        
        if(!$iSeriesSetId){
         $oSeriesSetId = SeriesSet::whereRaw(' find_in_set(?, series_ids)', [$series_id])->get(['id'])->first();
         $iSeriesSetId = $oSeriesSetId->id;
         Cache::forever($cache_key,$iSeriesSetId);
                 
         
        }
        if(!$iSeriesSetId) return 1;//如果查不到则取ssc的seriesid
        return $iSeriesSetId;
    }

    /**
     * @return array
     */
    public static function & getTypeList() {
        $data = [];
        foreach(self::$aTypeIds as $typeId => $name){
            $data[$typeId] = __('_seriesset.' . $name);
        }

        return $data;
    }
    public static function getSeriesIdByType(){
        $cache_key=__CLASS__.'_'.__FUNCTION__;
        $seriesData = Cache::get($cache_key);
        if(!$seriesData){
            $seriesData = array();
            $data = SeriesSet::all();
            foreach($data as $d){
                $d = $d->getAttributes();
                $seriesData[$d['type_id']] = $d['series_ids'];
            }
            Cache::forever($cache_key,$seriesData);
        }

        return $seriesData;

    }

}
