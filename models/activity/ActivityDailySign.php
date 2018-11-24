<?php

/**
 *  活动每日签到表
 *
 */
class ActivityDailySign extends BaseModel {

    /**
     * 活动状态：开启
     */
    const STATUS_OPEN = 1;

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_daily_sign';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'day',
        'user_id',
        'username',
        'sign_date',
        'turnover',
    ];
    public static $resourceName = 'ActivityDailySign';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'day',
        'user_id',
        'username',
        'sign_date',
        'turnover',
    ];
    public static $titleColumn = 'username';
/**    public static $rules = [
        'username' => 'required|between:0,16',
        'sign_date' => 'required|date',
    ];

    protected function beforeValidate() {
        // TIP 如果有父用户，则子用户的is_tester属性应该和父用户保持一致
        if ($this->user_id) {
            $oUser = UserUser::find($this->user_id);
            $this->username = $oUser->username;
        }
        $this->sign_date = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }
*/
    /**
     *  获取最后一条签到记录
     */
    public static function getLastSign($userId) {
        $oQuery = self::where('user_id', '=', $userId)->orderBy('id', 'desc');
        return $oQuery->first();
    }

    /**
     *  获取最近七条签到记录
     */
    public static function getLatestRecord($userId, $id = null) {
        $oQuery = self::where('user_id', '=', $userId);
        $id == null or $oQuery = $oQuery->where('id', '<=', $id);
        $oQuery = $oQuery->orderBy('id', 'desc')->take(7);
        return $oQuery->get();
    }

    /**
     *  获取昨日签到记录
     */
    public static function getYesterDayRecord($userId) {
        $sYesterday = date('Y-m-d', strtotime("-1 day"));
        $oQuery = self::where('user_id', '=', $userId)->where('sign_date', '>=', $sYesterday . ' 00:00:00')->where('sign_date', '<=', $sYesterday . ' 23:59:59')->orderBy('id', 'desc');
        return $oQuery->first();
    }

    /**
     * 得到未派奖的记录
     * @param int $userId
     * @param bool $bSend
     */
    public static function getUnSendRewardRecord() {
        $oQuery = self::where('day', '=', 7)->where('is_send', '=', 0);
        return $oQuery->get();
    }

}
