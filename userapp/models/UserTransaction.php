<?php
class UserTransaction extends Transaction
{
    protected $fillable = [];
    public static $columnForList = [
        //订单号   账变时间    用户名 账变类型    游戏  玩法  模式  收入  支出  可用余额
        'serial number',
        'created_at',
        'description',
        'lottery_id',
        'way_id',
        'type_id',
        'coefficient',
        'amount',
        'available',
        'status',
    ];

    const TYPE_DEPOSIT_ONLINE    = 1;
    const TYPE_DEPOSIT_MANUAL    = 18;
    const TYPE_WITHDRAWAL_USER   = 2;
    const TYPE_WITHDRAWAL_ADMIN  = 19;

    public static function getLatestRecords($iCount = 4)
    {
        $aColumns = ['id', 'amount', 'type_id', 'description', 'updated_at','is_income'];
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
        $aTransactions = isset($oQuery) ? $oQuery->orderBy('updated_at', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aTransactions;
    }

    public static function getUserOwnLatestRecords($iCount = 1, $aTypes = []) {
        $aColumns = ['id', 'amount'];
        $iUserId = Session::get('user_id');
        // from(DB::raw('transactions force index (idx_created_at)'))->
        $oQuery = self::where('user_id', '=', $iUserId)->where('created_at', '>=', Carbon::today()->toDateTimeString());
        if (count($aTypes)) {
            $oQuery = $oQuery->whereIn('type_id', $aTypes);
        }
        $aTransactions = isset($oQuery) ? $oQuery->orderBy('updated_at', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aTransactions;
    }


    /**
     * 用户派奖,(计算红利)
     * @param $userId
     * @param $starDate
     * @param $endDate
     */
    public static function getUserBonus($forefatherId, $starDate=null, $endDate=null)
    {
        $oQuery = self::whereRaw(' find_in_set(?, user_forefather_ids)', [$forefatherId])->where('type_id', '=', 23);

        if($starDate) $oQuery = $oQuery->where('created_at', '>=', $starDate);

        if($endDate) $oQuery = $oQuery->where('created_at', '<=', $endDate);

        return $oQuery->select(DB::raw('sum(amount) as team_amount'))->first();
    }

    public static function getFirstDepositByUid($uid){
        $aColumns = ['id', 'amount', 'type_id', 'description', 'updated_at','is_income'];
        $oQuery = self::where('user_id', '=', $uid);
        $oQuery->whereIn('type_id',[
            TransactionType::TYPE_DEPOSIT,
            TransactionType::TYPE_VIOLATION_CLAIMS,
            TransactionType::TYPE_DEPOSIT_BY_ADMIN_FOR_LOSS,
        ]);
        $aTransactions = isset($oQuery) ? $oQuery->orderBy('created_at', 'asc')->first($aColumns) : [];
        return $aTransactions;
    }

}