<?php

class UserProject extends Project {

    protected static $cacheUseParentClass = true;
    public static $columnForList = [
        'lottery_id',
        'serial_number',
        'bet_number',
        'amount',
        'prize',
        'status',
    ];
    public static $amountAccuracy = 4;
    protected $fillable = [
        'trace_id',
        'user_id',
        'account_id',
        'multiple',
        'serial_number',
        'user_forefather_ids',
        'issue',
        'title',
        'bet_number',
        'note',
        'lottery_id',
        'method_id',
        'way_id',
        'prize_added',
        'coefficient',
        'single_amount',
        'amount',
        'status',
        'ip',
        'proxy_ip',
        'bought_at',
    ];

    protected function getSplittedWinningNumberAttribute() {
        if (!$this->winning_number) {
            return [];
        }
//        $oLottery     = Lottery::find($this->lottery_id);

        $splitCharInArea = Config::get('bet.split_char_lotto_in_area');
        $splitCharSumDigital = Config::get('bet.splitCharSumDigital');
        if (strstr($this->winning_number, $splitCharInArea)) {

            if (strstr($this->winning_number, '|')) {
                $this->winning_number = str_replace('|', ' | ', $this->winning_number);
            }

            return explode($splitCharInArea, $this->winning_number);
        }if (strstr($this->winning_number, $splitCharSumDigital)) {
            return explode($splitCharSumDigital, $this->winning_number);
        } else {
            return str_split($this->winning_number, 1);
        }

//        return $oLottery->type == Lottery::LOTTERY_TYPE_DIGITAL ? str_split($this->winning_number,1) : $aSplitted = explode(' ',$this->winning_number);
    }

    public static function getLatestRecords($iCount = 4) {
        $aColumns = ['id', 'lottery_id', 'amount', 'status', 'updated_at'];
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
        $aProjects = isset($oQuery) ? $oQuery->orderBy('id', 'desc')->limit($iCount)->get($aColumns) : [];
        return $aProjects;
    }

    public static function getRecordsByParams($iLotteryId = null, $iCount = 4) {
        $aColumns = ['id', 'lottery_id', 'user_id', 'way_id', 'bet_number', 'is_encoded', 'issue', 'title', 'winning_number', 'position', 'display_bet_number', 'amount', 'commission', 'prize', 'status', 'updated_at', 'prize_group', 'bought_at', 'is_overprize'];
        $iUserId = Session::get('user_id');
        $oQuery = self::where('user_id', '=', $iUserId);
        if ($iLotteryId) {
            $oQuery = $oQuery->where('lottery_id', '=', $iLotteryId);
        } else
            $oQuery = $oQuery->where(function($oQuery) {
                $oQuery->where('lottery_id', '<', '25')->orWhere('lottery_id', '>=', '53');
            });

        $aRecords = isset($oQuery) ? $oQuery->orderBy('id', 'desc')->limit($iCount)->get($aColumns) : [];

        return $aRecords;
    }

    public static function getRecordsByIssue($iLotteryId = null, $issue = null) {
        if (!$iLotteryId || !$issue) {
            return [];
        }
        $iUserId = Session::get('user_id');
        $oQuery = self::where('user_id', '=', $iUserId)->where('lottery_id', '=', $iLotteryId)->where('issue', '=', $issue);
        return $oQuery->orderBy('id', 'desc')->get();
    }

    /**
     * 统计用户当期投注的数据
     * @param type $iLotteryId
     * @param type $issue
     * @return type
     */
    public static function countByLotteryIdAndIssue($iLotteryId = null, $issue = null) {
        if (!$iLotteryId || !$issue) {
            return [];
        }
        $iUserId = Session::get('user_id');
        $oQuery = self::where('user_id', '=', $iUserId)->where('lottery_id', '=', $iLotteryId)->where('issue', '=', $issue);
        return $oQuery->count();
    }

    /**
     * 获取用户最后投注的期号
     * @param int $iLotteryId
     * @return int
     */
    public static function getLastIssueByLotteryId($iLotteryId = null) {
        if (!$iLotteryId) {
            return [];
        }
        $iUserId = Session::get('user_id');
        $oQuery = self::where('user_id', '=', $iUserId)->where('lottery_id', '=', $iLotteryId);
        $oRecord = $oQuery->orderBy('id', 'desc')->limit(1)->get(['issue'])->first();
        return $oRecord['issue'];
    }

    protected function getAmountFormattedAttribute() {
        return number_format($this->amount, 4);
    }

    protected function getFormattedDisplayBetNumberAttribute() {
        $iUserId = Session::get('user_id');

        if ($iUserId != $this->user_id && $this->status == Project::STATUS_NORMAL) {
            return str_repeat('*', strlen($this->display_bet_number));
        } else {
            return $this->display_bet_number;
        }
    }

    public static function getProjectsByIds($aIds) {
        return self::whereIn('id', $aIds)->where('status', Project::STATUS_NORMAL)->get();
    }

    public static function getTeamProjectInfo($parentId, $sBeginDate, $sEndDate, $bIncludeSelf = FALSE) {
        $oQuery = DB::table('projects')->select(DB::raw('status, count(*) total_count'));
        if ($bIncludeSelf) {
            $oQuery = $oQuery->where(function($query) use($parentId){
                $query->whereRaw(' find_in_set(?, user_forefather_ids)', [$parentId])->orwhereRaw('user_id=?', [$parentId]);
            });
        } else {
            $oQuery = $oQuery->whereRaw(' find_in_set(?, user_forefather_ids)', [$parentId]);
        }
        $oQuery = $oQuery->where('bought_at', '>=', $sBeginDate)->where('bought_at', '<=', $sEndDate. ' 23:59:59');
        $oQuery = $oQuery->groupBy('status');
        $aResult = $oQuery->get();
        $aData = [];
        foreach ($aResult as $obj) {
            $aData[$obj->status] = $obj->total_count;
        }
        return $aData;
    }

    public static function getBetedAmount($iUserId, $iLotteryId, $sIssue, $sBetNumber, $iWayId) {
        return self::where('user_id', $iUserId)
                        ->where('lottery_id', $iLotteryId)
                        ->where('way_id', $iWayId)
                        ->where('issue', $sIssue)
                        ->where('bet_number', $sBetNumber)
                        ->where('status', self::STATUS_NORMAL)
                        ->sum('amount');
    }

}
