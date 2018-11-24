<?php
class UserLastBet extends BaseModel{
    protected $table = 'user_last_bets';
    protected $fillable = [
        'user_id',
        'forefather_ids',
        'last_bet_time',
    ];
    protected $primaryKey='user_id';


    /**
     * 获取约定时间内下注的人数
     * @param type $iUserId
     * @param type $iDate1
     * @param type $iDate2
     * @return type
     */
    public static function getUserStatisticBetweenDate($iUserId = 0,$iDate1 = 0,$iDate2 = 0){

        return self::whereRaw(' find_in_set(?, forefather_ids)', [$iUserId])->where('last_bet_time','>=', $iDate1)->where('last_bet_time','<=',$iDate2)->count();
    }
}

