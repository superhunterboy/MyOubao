<?php

/**
 * Class ActivityCashBack - 活动首冲四次返
 */
class ActivityCashBack extends BaseModel {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    protected $table = 'activity_cash_back';
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'total_turnover',
        'is_cash_back',
    ];
    public $timestamps = false;

    public static function getDataByUserId($iUserId, $iPrizeId, $sEndDate = null) {
        $oQuery = self::where('user_id', $iUserId)->where('prize_id', $iPrizeId);
        if ($sEndDate != null) {
            $oQuery = $oQuery->where('end_date', $sEndDate);
        }
        return $oQuery->get();
    }

}
