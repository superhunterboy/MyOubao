<?php

/**
 * 用户盈亏表
 *
 * @author frank
 */
class UserPrize extends BaseModel {

    protected $table = 'user_prizes';
    public static $resourceName = 'UserPrize';

    protected $fillable = [
        'user_id',
        'lottery_id',
        'issue',
        'prize',
    ];

    public static $rules = [
        'user_id' => 'required|integer',
        'lottery_id' => 'required|integer',
        'issue' => 'required|numeric',
        'prize' => 'numeric|min:0',
    ];

    /**
     * 获取返奖
     * @param $user_id
     * @param $lottery_id
     * @param $issue
     * @return UserPrize
     */
    public static function userPrizeObject($user_id, $lottery_id, $issue){

        $oUserPrize = self::where('user_id', '=', $user_id)
            ->where('lottery_id', '=', $lottery_id)
            ->where('issue', '=', $issue)
            ->first();

        if(! is_object($oUserPrize)){
            $data = ['lottery_id' => $lottery_id, 'user_id' => $user_id, 'issue' => $issue, 'prize'=>0];
            $oUserPrize = new UserPrize($data);
        }

        return $oUserPrize;
    }
}
