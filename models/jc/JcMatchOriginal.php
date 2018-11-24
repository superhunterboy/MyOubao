<?php

namespace JcModel;

/**
 * 赛事采集原始数据模型
 */
class JcMatchOriginal extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_NONE;
    public static $resourceName = 'JcMatchOriginal';
    protected $table = 'jc_matchs';

    public $orderColumns = [
        'match_id' => 'asc'
    ];

    protected $fillable = [
        'id',
        'match_id',
        'num',
        'date',
        'time',
        'b_date',
        'status',
        'hot',
        'l_id',
        'l_cn',
        'h_id',
        'h_cn',
        'a_id',
        'a_cn',
        'index_show',
        'show',
        'l_cn_abbr',
        'h_cn_abbr',
        'a_cn_abbr',
        'l_background_color',
        'weather_color',
        'weather',
        'weather_city',
        'temperature',
        'weather_pic',
        'match_info',
        'last_updated',
        'crs',
        'hhad',
        'had',
        'ttg',
        'hafu',
        'half_score',
        'score',
        'updated_at'
    ];
    
    public static $rules = [
        'match_id' => 'required',
        'num' => 'required',
        'date' => 'required | date_format:Y-m-d',
        'time' => 'required | date_format:H:i:s',
        'b_date' => 'required | date_format:Y-m-d',
        'l_cn' => 'required',
        'h_cn' => 'required',
        'a_cn' => 'required',
        'l_cn_abbr' => 'required',
        'h_cn_abbr' => 'required',
        'a_cn_abbr' => 'required',
        'crs' => '',
        'hhad' => '',
        'had' => '',
        'ttg' => '',
        'hafu' => ''
    ];
    
    //public $timestamps = false;
    const MATCHE_STATUS_NEW = 0;//待审核
    const MATCHE_STATUS_VERIFIED = 1;//已审核
    const MATCHE_STATUS_TEST = 2;

    protected function getMatchTimeAttribute() {
        return $this->date . ' ' . $this->time;
    }
    
    protected function getBetEndTimeAttribute() {
        $iBetStopTime = \SysConfig::readValue('jc_bet_stop_time');
        return date('Y-m-d H:i:s', strtotime($this->match_time) - $iBetStopTime * 60);
    }
    
    public static function getByMatchId($iMatchId){
        return self::where('match_id', $iMatchId)->first();
    }
    
    /**
     * 
     * @param string $sTime
     * @param string $sNum
     * @return string
     */
    public static function makeMatchId($sTime, $sNum = ''){
        return date('Ymdw', strtotime($sTime)) . sprintf('%03d',$sNum);
    }

}
