<?php
class UserTrace extends Trace {

    public static $columnForList =[
        'lottery_id',
        'serial_number',
        'start_issue',
        'title',
        'bet_number',
        'amount',
        'prize',
        'status',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'user_forefather_ids',
        'account_id',
        'total_issues',
        'finished_issues',
        'canceled_issues',
        'stop_on_won',
        'lottery_id',
        'title',
        'bet_number',
        'start_issue',
        'prized_issue',
        'coefficient',
        'single_amount',
        'amount',
        'finished_amount',
        'canceled_amount',
        'status',
        'ip',
        'proxy_ip',
        'bought_at',
        'canceled_at',
        'stoped_at',
    ];

    public $orderColumns = [
        'id' => 'desc'
    ];


    protected function getSplittedWinningNumberAttribute(){
        if (!$this->winning_number){
            return [];
        }

        $oLottery       = Lottery::find($this->lottery_id);
        return $oLottery->type = Lottery::LOTTERY_TYPE_DIGITAL ? str_split($this->winning_number,1) : $aSplitted = explode(' ',$this->winning_number);
    }

    protected function getUpdatedAtDayAttribute(){
        return substr($this->updated_at, 5, 5);
    }

    protected function getUpdatedAtTimeAttribute(){
        return substr($this->updated_at, 11);
    }

    public static function getLatestRecords($iCount = 4)
    {
        $aColumns = ['id', 'lottery_id', 'status', 'updated_at', 'total_issues', 'finished_issues'];
        $iUserId = Session::get('user_id');
        if (Session::get('is_agent')) {
            $aUserIds = [];
            $oUser = User::find($iUserId);
            $aUsers = $oUser->getUsersBelongsToAgent();
            foreach ($aUsers as $oUser) {
                $aUserIds[] = $oUser->id;
            }
            if ($aUserIds) {
                $oQuery = self::whereIn('user_id', $aUserIds);
            }
        } else {
            $oQuery = self::where('user_id', '=', $iUserId);
        }
        $aTraces = isset($oQuery) ? $oQuery->orderBy('updated_at', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aTraces;
    }

    public static function getRecordsByParams($iLotteryId = null, $iCount = 4) {
        $aColumns = ['id', 'lottery_id', 'start_issue', 'title', 'display_bet_number', 'amount', 'prize', 'stop_on_won', 'status', 'finished_issues', 'total_issues', 'updated_at'];
        $iUserId = Session::get('user_id');
        $oQuery = self::where('user_id', '=', $iUserId);
        if ($iLotteryId) {
            $oQuery = $oQuery->where('lottery_id', '=', $iLotteryId);
        }else{
            $oQuery = $oQuery->where(function($oQuery){$oQuery->where('lottery_id','<', '25')->orWhereIn('lottery_id', [53,60]);});
        }
        $aRecords = isset($oQuery) ? $oQuery->orderBy('created_at', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aRecords;
    }
}