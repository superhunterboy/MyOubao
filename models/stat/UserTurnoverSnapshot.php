<?php

/**
 * 用户奖期销售额表
 *
 * @author frank
 */
class UserTurnoverSnapshot extends BaseModel {

    protected $table = 'user_turnovers';
    public static $resourceName = 'UserTurnover';
    public static $amountAccuracy = 4;

    protected $fillable = [
        'lottery_id',
        'issue',
        'user_id',
        'account_id',
        'username',
        'turnover',
        'parent_user_id',
        'parent_user',
    ];
    public static $rules = [
        'user_id' => 'required|integer',
        'account_id' => 'required|integer',
        'username' => 'required|max:16',
        'parent_user_id' => 'integer',
        'parent_user' => 'max:16',
        'turnover' => 'numeric',
    ];

    /**
     * 返回UserProfit对象
     *
     * @param string $sDate
     * @param string $iUserId
     * @return UserProfit
     */
    public static function getUserTurnverObject($iLotteryId, $sIssue, $iUserId) {
        $obj = self::where('lottery_id','=',$iLotteryId)->where('issue','=',$sIssue)->where('user_id', '=', $iUserId)->get()->first();
        if (!is_object($obj)) {
            if(!$oUser = User::find($iUserId)){
                return false;
            }
            $data = [
                'lottery_id' => $iLotteryId,
                'issue' => $sIssue,
                'user_id' => $iUserId,
                'account_id' => $oUser->account_id,
                'username' => $oUser->username,
                'parent_user_id' => $oUser->parent_id,
                'parent_user' => $oUser->parent,
            ];
            $obj = new UserTurnoverSnapshot($data);
        }else{
            $obj->turnover = 0;
        }
        return $obj;
    }

    /**
     * 累加销售额
     * @param float $fAmount
     * @return boolean
     */
    public function addTurnover($fAmount) {
        $this->turnover += $fAmount;

    }

    public function updateTurnoverData($fAmount) {

        return $this->addTurnover($fAmount);
    }

}
