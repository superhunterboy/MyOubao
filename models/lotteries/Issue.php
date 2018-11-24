<?php

class Issue extends BaseModel {

    static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'Issue';
    protected $table = 'issues';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [];
    protected $fillable = [];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'issue' => 'asc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'lottery_id';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
            // 'name'                => 'required|between:1,10',
            // 'type'                => 'required|numeric',
            // 'lotto_type'          => 'numeric',
            // 'high_frequency'      => 'in:0,1',
            // 'sort_winning_number' => 'in:0,1',
            // 'valid_nums'          => 'required',
            // 'buy_len'             => 'required',
            // 'code_len'            => 'required',
            // 'identifier'          => 'required|between:3,10',
            // 'days'                => 'numeric',
            // 'issue_over_midnight' => 'in:0,1',
            // 'issue_format'        => 'required',
            // 'begin_time'          => 'required',
            // 'end_time'            => 'required',
            // 'need_draw'           => 'in:0,1',
            // 'sequence'            => 'numeric',
    ];

    /**
     * The array of custom error messages.
     *
     * @var array
     */
    public static $customMessages = [];

    /**
     * title field
     * @var string
     */
    public static $titleColumn = 'name';

    /**
     * 中奖号码状态：等待开奖
     */
    const ISSUE_CODE_STATUS_WAIT_CODE = 1;

    /**
     * 中奖号码状态：已输入号码，等待审核
     */
    const ISSUE_CODE_STATUS_WAIT_VERIFY = 2;

    /**
     * 中奖号码状态：号码已审核
     */
    const ISSUE_CODE_STATUS_FINISHED = 4;

    /**
     * 中奖号码状态：号码已取消开奖
     */
    const ISSUE_CODE_STATUS_CANCELED = 8;

    /**
     * 中奖号码状态：提前开奖A，获取到开奖号码的时间早于官方理论开奖时间
     */
    const ISSUE_CODE_STATUS_ADVANCE_A = 32;

    /**
     * 中奖号码状态：提前开奖B，获取到开奖号码的时间早于销售截止时间
     */
    const ISSUE_CODE_STATUS_ADVANCE_B = 64;

    /**
     * 计奖状态
     */
    const CALCULATE_NONE = 0;
    const CALCULATE_PROCESSING = 1;
    const CALCULATE_PARTIAL = 2;
    const CALCULATE_FINISHED = 4;

    /**
     * 派奖状态
     */
    const PRIZE_NONE = 0;
    const PRIZE_PROCESSING = 1;
    const PRIZE_PARTIAL = 2;
    const PRIZE_FINISHED = 4;

    /**
     * 派佣金状态
     */
    const COMMISSION_NONE = 0;
    const COMMISSION_PROCESSING = 1;
    const COMMISSION_PARTIAL = 2;
    const COMMISSION_FINISHED = 4;

    /**
     * 追号单状态
     */
    const TRACE_PRJ_NONE = 0;
    const TRACE_PRJ_PROCESSING = 1;
    const TRACE_PRJ_PARTIAL = 2;
    const TRACE_PRJ_FINISHED = 4;

    /**
     * 中奖号码状态数组
     * @var array
     */
    public static $winningNumberStatus = [
        self::ISSUE_CODE_STATUS_WAIT_CODE => 'Waiting For Number',
        self::ISSUE_CODE_STATUS_WAIT_VERIFY => 'Waiting For Verify',
        self::ISSUE_CODE_STATUS_FINISHED => 'Verified',
        self::ISSUE_CODE_STATUS_CANCELED => 'Canceled',
    ];
    public static $calculateStatus = [
        self::CALCULATE_NONE => 'waiting',
        self::CALCULATE_PROCESSING => 'running',
        self::CALCULATE_PARTIAL => 'partial',
        self::CALCULATE_FINISHED => 'done'
    ];
    public static $prizeStatus = [
        self::PRIZE_NONE => 'waiting',
        self::PRIZE_PROCESSING => 'running',
        self::PRIZE_PARTIAL => 'partial',
        self::PRIZE_FINISHED => 'done'
    ];
    public static $commissionStatus = [
        self::COMMISSION_NONE => 'waiting',
        self::COMMISSION_PROCESSING => 'running',
        self::COMMISSION_PARTIAL => 'partial',
        self::COMMISSION_FINISHED => 'done'
    ];
    public static $tracePrjStatus = [
        self::TRACE_PRJ_NONE => 'waiting',
        self::TRACE_PRJ_PROCESSING => 'running',
        self::TRACE_PRJ_PARTIAL => 'partial',
        self::TRACE_PRJ_FINISHED => 'done'
    ];

    const ERRNO_ISSUE_MISSING = -910;
    const ERRNO_ISSUE_EXPIRED = -911;
    const ERRNO_ISSUE_OVERBET = -912;
    const ERRNO_ISSUE_ENTERTAINED = -913; //幸运28封盘

    /**
     * 官方未开奖时的中奖号码
     */
    const ISSUE_CODE_CANCELED = 'XXXXX';

    /**
     * 录错号标识
     */
    const ISSUE_CODE_FAIL = 'FFFFF';
    const ISSUE_CODE_CLEAR = 'CCCCC';

    public static $specialFlags = [
        self::ISSUE_CODE_CANCELED,
        self::ISSUE_CODE_FAIL
    ];

    /**
     * 获取指定游戏的奖期对象
     * @param int $iLotteryId
     * @param int $iCount
     * @param int $iBeginTime
     * @return Collection
     */
    function getIssueObjects($iLotteryId, $iCount = null, $iBeginTime = null, $iEndTime = null, $bOrderDesc = false) {
        $iCount or $iCount = 120;
        $aCondtions = [
            'lottery_id' => ['=', $iLotteryId],
        ];
        if ($iBeginTime || $iEndTime) {
            if ($iBeginTime && $iEndTime) {
                $aCondtions['end_time'] = ['between', [$iBeginTime, $iEndTime]];
            } else {
                $sOperator = $iBeginTime ? '>=' : '<=';
                $iTime = $iBeginTime ? $iBeginTime : $iEndTime;
                $aCondtions['end_time'] = [ $sOperator, $iTime];
            }
        }
        $aOrderBy = ['issue' => $bOrderDesc ? 'desc' : 'asc'];
        $oQuery = $this->doWhere($aCondtions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        return $oQuery->paginate($iCount);
    }

    private static function compileIssueListCacheKey($iLotteryId) {
        return get_called_class() . '-lists-lottery-' . $iLotteryId;
    }

    public function & getIssueArrayForBet($iLotteryId, $iCount = null, $iBeginTime = null, $iEndTime = null, $bOrderDesc = false, $bStop = false) {
        $aIssues = $this->_getIssueArrayForBet($iLotteryId, $iCount, $iBeginTime, $iEndTime, $bOrderDesc);
        $iCurrentTime = time();

        foreach ($aIssues as $i => $aIssue) {
            if ($iCurrentTime < $aIssue['end_time']) {
                break;
            }
            if ($iCurrentTime > $aIssue['end_time']) {
                unset($aIssues[$i]);
            }
        }
        sort($aIssues);
        if (count($aIssues) < $iCount && !$bStop) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileIssueListCacheKey($iLotteryId);
            Cache::forget($sCacheKey);
            $aIssuesForBet = $this->getIssueArrayForBet($iLotteryId, $iCount, $iBeginTime, $iEndTime, $bOrderDesc, true);
        } else {
            $aIssuesForBet = [];
            $iCount = min($iCount, count($aIssues));
            for ($i = 0; $i < $iCount; $i++) {
                $aIssuesForBet[] = [
                    'number' => $aIssues[$i]['issue'],
                    'time' => date('Y-m-d H:i:s', $aIssues[$i]['end_time']),
                    'wn_number' => $aIssues[$i]['wn_number'],
                    'cycle' => $aIssues[$i]['cycle'],
                ];
            }
        }
        return $aIssuesForBet;
//        pr($aIssuesForBet);
//        exit;
//        for($i++,$j = 0;$j < $iCount;$i++,$j++){
//            $gameNumbers[] = [
//                'number' => $aIssues[$i]['issue'] ,
//                'time'   => date('Y-m-d H:i:s' , $aIssues[$i]['end_time'])
//            ];
//        }
    }

    /**
     * 获取指定彩种已经开奖的奖期数据
     * @param int $iLotteryId      彩种id
     * @param int $iCount           记录数量
     * @param type $bOrderDesc
     * @return array
     */
    public function & getIssueArrayForWinNum($iLotteryId, $iCount = null) {
        $aIssuesForWinNum = [];
        $aCondtions = [
            'lottery_id' => ['=', $iLotteryId],
            'status' => ['=', Issue::ISSUE_CODE_STATUS_FINISHED],
        ];
        $aOrderBy = ['end_time' => 'desc'];
        $oQuery = $this->doWhere($aCondtions);
        $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
        $oIssues = $oQuery->take($iCount)->get();
        foreach ($oIssues as $oIssue) {
            $aIssuesForWinNum[] = [
                'number' => $oIssue->issue,
                'code' => $oIssue->wn_number,
                'time' => date('Y-m-d H:i:s', $oIssue->end_time),
            ];
        }
        return $aIssuesForWinNum;
    }

    /**
     * 获取指定游戏的奖期数组
     * @param int $iLotteryId
     * @param int $iCount
     * @param int $iBeginTime
     * @return Collection
     */
    private function & _getIssueArrayForBet($iLotteryId, $iCount = null, $iBeginTime = null, $iEndTime = null, $bOrderDesc = false) {
//        $iBeginTime or $iBeginTime = time();
        $iCount or $iCount = 120;
        $bReadDb = true;
        $bPutCache = false;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $sCacheKey = self::compileIssueListCacheKey($iLotteryId);
            if ($aIssues = Cache::get($sCacheKey)) {
                $bReadDb = false;
            } else {
                $bPutCache = true;
            }
        }
        if ($bReadDb) {
            $aCondtions = [
                'lottery_id' => ['=', $iLotteryId],
            ];
            if ($iBeginTime || $iEndTime) {
                if ($iBeginTime && $iEndTime) {
                    $aCondtions['end_time'] = ['between', [$iBeginTime, $iEndTime]];
                } else {
                    $sOperator = $iBeginTime ? '>=' : '<=';
                    $iTime = $iBeginTime ? $iBeginTime : $iEndTime;
                    $aCondtions['end_time'] = [ $sOperator, $iTime];
                }
            }
            $aOrderBy = ['issue' => $bOrderDesc ? 'desc' : 'asc'];
            $oQuery = $this->doWhere($aCondtions);
            $oQuery = $this->doOrderBy($oQuery, $aOrderBy);
            $oIssues = $oQuery->take($iCount * 2)->get();
            $aIssues = [];
            foreach ($oIssues as $oIssue) {
                $aIssues[] = $oIssue->getAttributes();
            }
        }
        if ($bPutCache) {
            Cache::forever($sCacheKey, $aIssues);
        }
//        pr($aIssues);
//        exit;
        return $aIssues;
    }

    /**
     * 返回指定游戏和奖期号的奖期对象
     * @param int $iLotteryId
     * @param string $sIssue
     * @return Issue
     */
    public static function getIssue($iLotteryId, $sIssue) {
        return self::where('lottery_id', '=', $iLotteryId)->where('issue', '=', $sIssue)->get()->first();
    }

    /**
     * 返回最近一期的开奖号码
     * @param int $iLotteryId
     * @return array | false            issue wn_number
     */
    public static function getLatestWnNumber($iLotteryId, $iLimit = 1) {
        $bReadDb = true;
        if (static::$cacheLevel != self::CACHE_LEVEL_NONE) {
            Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
            $key = self::makeLastWnNumberCacheKey($iLotteryId);
            if ($data = Cache::get($key)) {
                $bReadDb = false;
            }
        }
        if ($bReadDb) {
            $aCondtions = [
                'lottery_id' => [ '=', $iLotteryId],
                'end_time' => [ '<', time()],
                'status' => ['=', self::ISSUE_CODE_STATUS_FINISHED],
            ];
            $aOrderBy = ['end_time' => 'desc'];
            $oIssues = self::doWhere($aCondtions)->orderBy('end_time', 'desc')->limit($iLimit)->get(['issue', 'wn_number']);
            $data = [];
            foreach ($oIssues as $key => $oIssue) {
                $data[] = $oIssue->getAttributes();
            }
            // $data = !empty($oIssue) ? $oIssue->getAttributes() : false;
            !isset($key) or Cache::put($key, $data, 1);
        }
        return $data;
    }

    protected static function makeLastWnNumberCacheKey($iLotteryId) {
        return 'last-wnnumber-' . $iLotteryId;
    }

    protected static function compileLastWnNumberCacheKey($iLotteryId, $iCount = 3) {
        return Config::get('cache.prefix') . 'Last-wnnumber-' . $iLotteryId . '-' . $iCount;
    }

    /**
     * 返回最近没有开奖号码的奖期
     * @param int $iLotteryId
     * @return array
     */
    public static function getLatestIssueOfNoWnNumber($iLotteryId) {
        $aCondtions = [
            'lottery_id' => [ '=', $iLotteryId],
            'end_time' => [ '<', time()],
            'status' => ['in', [self::ISSUE_CODE_STATUS_WAIT_CODE, self::ISSUE_CODE_STATUS_WAIT_VERIFY]],
        ];
        // $aOrderBy = ['end_time' => 'desc'];
        $oIssue = self::doWhere($aCondtions)->orderBy('id', 'asc')->first();
        return $oIssue;
    }

    /*
     * 最近一次的奖期 无论是否开奖
     */

    public static function getLastestIssue($iLotteryId) {
        return self::where('lottery_id', '=', $iLotteryId)->where('begin_time', '<=', time())->orderBy('id', 'desc')->first();
    }

    public static function getOnSaleIssue($iLotteryId) {
        return self::where('lottery_id', '=', $iLotteryId)->where('end_time', '>', time())->first();
    }

    public static function getIssuesByLotteryId($iLotteryId) {
        $i = 0;
        $aData = [];
        $sStartTime = date('Y-m-d', (time() - 3600 * 24));
        $aColumns = ['id', 'issue'];
        $aLotteryWays = $oQuery = self::where('lottery_id', '=', $iLotteryId)->where('end_time', '<', time())->where('end_time2', '>', $sStartTime)->orderBy('id', 'desc')->get($aColumns);
        foreach ($aLotteryWays as $id => $value) {
            $aData[$i]['id'] = $value->id;
            $aData[$i]['name'] = $value->issue;
            $i++;
        }
        return $aData;
    }

    /**
     * 根据彩种id和时间获取奖期信息
     * @param int $iLotteryId          彩种id
     * @param int $sLotteryName          彩种名称
     * @param int $iStartTime        开始时间
     * @param int $iEndTime         结束时间
     * @return array
     */
    public static function getIssuesByLotteryIdAndTime($iLotteryId, $iStartTime, $iEndTime, $sLotteryName) {
        $i = 0;
        $aData = [];
        $aColumns = ['issue', 'end_time', 'offical_time'];
        $aIssues = $oQuery = self::where('lottery_id', '=', $iLotteryId)->where('end_time', '<=', $iEndTime)->where('end_time', '>=', $iStartTime)->get($aColumns);
        foreach ($aIssues as $id => $value) {
            $aData[$i]['lottery'] = $sLotteryName;
            $aData[$i]['issue'] = $value->issue;
            $aData[$i]['drawTime'] = date('YmdHis', $value->offical_time);
            $aData[$i]['saleCloseTime'] = date('YmdHis', $value->end_time);
            $i++;
        }
        return $aData;
    }

    /**
     * 获取该彩中最近的开奖号码
     * @param $iLotteryId     * @return bool
     */
    public static function getIssuesForFinish($iLotteryId) {
        return self::where('lottery_id', '=', $iLotteryId)
                        ->where('status', '=', Issue::ISSUE_CODE_STATUS_FINISHED)
                        ->where('status_count', '=', self::CALCULATE_FINISHED)
                        ->where('status_prize', '=', self::PRIZE_FINISHED)
//            ->where('status_commission', '=',  self::COMMISSION_FINISHED)
                        ->orderBy('issue', 'desc')
                        ->first();
    }

    /**
     * 获取指定彩种前几期的奖期数据
     * @param int $iLotteryId      彩种id
     * @param int $iCount           记录数量
     * @param type $bOrderDesc
     * @return array
     */
    public function getLastIssues($iLotteryId, $iCount = 1) {
        $aIssues = self::where('lottery_id', '=', $iLotteryId)
                ->where('begin_time', '<=', time())
                ->orderBy('issue', 'desc')
                ->take($iCount)
                ->get();
        $aLastIssues = [];
        foreach ($aIssues as $key => $oIssue) {
            $aLastIssues[$key]['issue'] = $oIssue->issue;
            $aLastIssues[$key]['wn_number'] = $oIssue->wn_number;
        }

        return $aLastIssues;
    }

    public static function setKl28NumberTask($iLotteryId, $sIssue, $sNumber) {
        $aLotteryIdMap = [
            1 => 54,
            3 => 55,
            6 => 56,
            7 => 57,
        ];
        $aJobData = [
            'lottery_id' => $aLotteryIdMap[$iLotteryId],
            'sIssue' => $sIssue,
            'sWnNumber' => $sNumber,
        ];
        BaseTask::addTask('SetWinningNumberTaskForKl28', $aJobData, 'set_win_number_for_Kl28');
    }

    public static function getWinningResult($iLotteryId, $sDate, $sIssue) {
        $sDate = $sDate ? $sDate : date('Y-m-d');

        $oQuery = self::where('lottery_id', $iLotteryId)
                ->where('offical_time', '>=', strtotime($sDate . ' 00:00:00'))
                ->where('offical_time', '<=', strtotime($sDate . ' 23:59:59'))
                ->where('status', Issue::ISSUE_CODE_STATUS_FINISHED);


        if ($sIssue)
            $oQuery->where('issue', '<=', $sIssue);

        return $oQuery->orderBy('issue', 'desc')->paginate(15);
    }

}
