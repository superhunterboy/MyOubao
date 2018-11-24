<?php

/**
 * 用户奖期销售额表
 *
 * @author frank
 */
class UserTurnover extends BaseModel {

    protected $table = 'user_turnovers';
    public static $resourceName = 'UserTurnover';
    public static $amountAccuracy = 4;
    public static $htmlNumberColumns = [
        'turnover' => 4,
    ];
    public static $columnForList = [
        'lottery_id',
        'issue',
        'username',
        'turnover',
    ];
    public static $listColumnMaps = [
        'turnover' => 'turnover_formatted',
    ];
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
    public $orderColumns = [
        'lottery_id' => 'asc',
        'issue' => 'desc',
        'username' => 'asc'
    ];
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'username';

    /**
     * 返回UserProfit对象
     *
     * @param string $sDate
     * @param string $iUserId
     * @return UserProfit
     */
    public static function getUserTurnverObject($iLotteryId, $sIssue, $iUserId) {
        $obj = self::where('lottery_id', '=', $iLotteryId)->where('issue', '=', $sIssue)->where('user_id', '=', $iUserId)->get()->first();
        if (!is_object($obj)) {
            $oUser = User::find($iUserId);
            $data = [
                'lottery_id' => $iLotteryId,
                'issue' => $sIssue,
                'user_id' => $iUserId,
                'account_id' => $oUser->account_id,
                'username' => $oUser->username,
                'parent_user_id' => $oUser->parent_id,
                'parent_user' => $oUser->parent,
            ];
            $obj = new UserTurnover($data);
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
//        pr($this->attributes);
        return $this->save();
    }

    public static function updateTurnoverData($iLotteryId, $sIssue, $iUserId, $fAmount) {
        $oTurnover = self::getUserTurnverObject($iLotteryId, $sIssue, $iUserId);
        return $oTurnover->addTurnover($fAmount);
    }

    // protected function getUserTypeFormattedAttribute() {
    //     // return static::$aUserTypes[($this->parent_user_id != null ? 'not_null' : 'null')];
    //     return __('_userprofit.' . strtolower(static::$aUserTypes[intval($this->parent_user_id != null) - 1]));
    // }

    protected function getTurnoverFormattedAttribute() {
        return $this->getFormattedNumberForHtml('turnover');
    }

    /**
     * 返回指定期的用户销售额数组
     * @param int $iLotteryId
     * @param string $sIssue
     * @param bool $bUsed
     * @return array
     */
    public static function getIssueUserTurnOvers($iLotteryId, $sIssue, $bUsed = false) {
        $iUsed = $bUsed ? 1 : 0;
        return self::where('lottery_id', '=', $iLotteryId)->where('issue', '=', $sIssue)->where('used', '=', $iUsed)->where('turnover', '>', 0)
                        ->orderBy('user_id', 'asc')->get(['id', 'user_id', 'account_id', 'turnover'])->toArray();
    }

    public static function setToUsed($id) {
        return self::where('id', '=', $id)->update(['used' => 1]) > 0;
    }

    /**
     * 返回指定期的用户销售额数组
     * @param string $sBeginTime
     * @param bool $bUsed
     * @return array
     */
    public static function getUserTurnOversByDate($sBeginTime, $bUsed = false) {
        $iUsed = $bUsed ? 1 : 0;
        return self::where('created_at', '>=', $sBeginTime)->where('used', '=', $iUsed)->where('turnover', '>', 0)
                        ->get(['id', 'user_id', 'lottery_id', 'issue', 'account_id', 'turnover'])->toArray();
    }

}
