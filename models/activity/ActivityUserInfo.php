<?php

/**
 * Class ActivityUserInfo - 活动用户信息表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityUserInfo extends BaseModel
{
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    protected $table = 'activity_user_info';
    static $unguarded=true;


    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User', 'user_id', 'id');
    }
    
    /**
     * 找到用户参与的活动信息
     * @param User $oUser
     * @param Activity $oActivity
     * @return ActivityUserInfo
     */
    public static function findUserInfo(User $oUser, Activity $oActivity) {
        $oUserInfo = self::where('user_id', '=', $oUser->id)
                ->where('activity_id', '=', $oActivity->id)
                ->first();
        return $oUserInfo;
    }
    
    
    /**
     * 扣减抽奖机会
     * @return boolean
     */
    public function deductLoteryCount() {
        $iAffectRows = self::where('id', '=', $this->id)
                ->where('lottery_count', '>', 0)
                ->decrement('lottery_count', 1);
//        dd(DB::getQueryLog());
        $iAffectRows == 1 && $this->lottery_count -= 1;
        return $iAffectRows == 1;
    }
    
    
    /**
     * 增加抽奖机会
     * @param int $iQuantity 增加抽奖机会数量
     * @return boolean
     */
    public function increaseLoteryCount($iQuantity = 1) {
        $iQuantity = intval($iQuantity);
        if($fContribution <= 0) {
            return false;
        }
        $iAffectRows = self::where('id', '=', $this->id)
                ->increment('lottery_count', $iQuantity);
//        dd(DB::getQueryLog());
        return $iAffectRows == 1;
    }


    /**
     * 验证前操作
     *
     * @return bool
     */
    public function beforeValidate()
    {
        $oActivity = Activity::find($this->activity_id);
        if (is_object($oActivity)) {
            $this->activity_name = $oActivity->name;
        } else {
            return false;
        }

        $user   = $this->user()->first();
        $this->username = $user->username;


        return parent::beforeValidate();
    }

}