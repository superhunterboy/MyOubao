<?php

/**
 * 奖金发放统计
 */
class ProjectPrizeSum extends BaseModel
{
    protected $table = 'project_prize_sum';

    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'ProjectPrizeSum';
    protected $fillable = [
        'user_id',
        'lottery_id',
        'issue',
        'prize',
        'status',
    ];

    public static $rules = [
        'user_id' => 'required|integer',
        'lottery_id'  => 'required|integer',
        'issue' => 'required|max:12',
        'prize'  => 'numeric',
    ];
    
    const MAX_PRIZE_AMOUNT = 400000;
    const MAX_PRIZE_AMOUNT_HIGH = 1500000;
    
    public static function getRecord($iUserId, $iLotteryId, $sIssue){
        return self::where('user_id', $iUserId)
                    ->where('lottery_id', $iLotteryId)
                    ->where('issue', $sIssue)
                    ->first();
    }
    
    public static function getPrizeSum($iUserId, $iLotteryId, $sIssue){
        $oRecord = self::getRecord($iUserId, $iLotteryId, $sIssue);
        if ($oRecord){
            $fPrizeSum = $oRecord->prize;
            return $fPrizeSum;
        }else{
            return null;
        }
    }
    
    public static function incrementPrize($iUserId, $iLotteryId, $sIssue, $fPrize){
        $oQuery = self::where('user_id', $iUserId)
                    ->where('lottery_id', $iLotteryId)
                    ->where('issue', $sIssue)
                    ->increment('prize', $fPrize);
        return $oQuery ? true : false;
    }
    
//    public static function getCacheKey($iLotteryId, $sIssue, $iWayId, $iUserId){
//        return "project_prize_sum_cache_{$iLotteryId}_{$sIssue}_{$iWayId}_{$iUserId}";
//    }
}