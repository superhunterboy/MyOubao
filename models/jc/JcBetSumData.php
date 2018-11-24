<?php

namespace JcModel;
/**
 * 投注数据统计模型
 */
class JcBetSumData extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcBetSumData';
    protected $table = 'jc_bet_sum_data';

    /**
     * the columns for list page
     * @var array
     */

    protected $fillable = [
        'id',
        'lottery_id',
        'user_id',
        'match_id',
        'sum_single_amount',
        'status',
        'bet_date'
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
        'lottery_id' => 'required|integer',
        'user_id' => 'required|integer',
        'match_id' => 'required|integer',
        'sum_single_amount' => 'numeric',
        'bet_date' => 'required',
    ];
    
    public static function getBetSumData($iLotteryId, $iUserId, $iMatchId, $dBetDate = null){
        if (empty($dBetDate)){
            $dBetDate = date('Y-m-d');
        }
        $oBetSumData = self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->where('match_id', $iMatchId)
                ->where('bet_date', $dBetDate)
                ->first();
        if (empty($oBetSumData)){
            $aData = [
                'lottery_id' => $iLotteryId,
                'user_id' => $iUserId,
                'match_id' => $iMatchId,
                'bet_date' => $dBetDate,
            ];
            $oBetSumData = new JcBetSumData($aData);
        }
        
        return $oBetSumData;
    }
    
    public function saveBetSumData(){
//        $oBetSumData = self::where('lottery_id', $iLotteryId)
//                ->where('user_id', $iUserId)
//                ->where('match_id', $iMatchId)
//                ->first();
//        if (empty($oBetSumData)){
//            $oBetSumData = new JcBetSumData();
//        }
        return $this->save();
    }
}
