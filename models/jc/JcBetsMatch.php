<?php

namespace JcModel;
/**
 * 方案赛事关联模型
 */
class JcBetsMatch extends \BaseModel {
    public static $resourceName = 'JcBetsMatch';
    protected $table = 'jc_bets_matches';

    /**
     * the columns for list page
     * @var array
     */
//    public static $columnForList = [
//        'id',
//        'bet_id',
//        'match_id',
//        'status',
//    ];
    
    protected $fillable = [
        'lottery_id',
        'bet_id',
        'detail_ids',
        'match_id',
        'user_id',
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
        'lottery_id'             => 'required|integer',
        'bet_id'             => 'required|integer',
        'match_id'              => 'required|integer',
        'user_id'              => 'required|integer',
    ];
    
    //public $timestamps = false;
    
    const STATUS_NORMAL             = 0; //正常
    const STATUS_WAITING             = 1; //待开奖
    const STATUS_FINISHED               = 2; //已开奖
    
    public static function getByBetId($iBetId){
        return self::where('bet_id', $iBetId)->get();
    }
    
    public static function addFillTask($iBetId, $iStartId = 0){
        $aJobData = ['bet_id' => $iBetId, 'start_id' => $iStartId];
        return \BaseTask::addTask('\JcCommand\FillBetMatches', $aJobData, 'jc_calculate');
    }
    
    public static function getUnFinishedList($iMatchId, $iLimit){
        return self::whereIn('status', [self::STATUS_NORMAL, self::STATUS_WAITING])
                ->where('match_id', $iMatchId)
                ->limit($iLimit)
                ->get();
    }
    
    public static function setToWaitingByBetId($iBetId){
        $aUpdateArr = [
            'status' => self::STATUS_WAITING,
        ];
        return self::where('status', self::STATUS_NORMAL)
                ->where('bet_id', $iBetId)
                ->update($aUpdateArr);
    }
    
    public function addBetsMatch(){
        return $this->save();
    }
 
}
