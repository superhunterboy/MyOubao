<?php

/**
 * 奖期管理模型
 */
class ManIssue extends Issue {

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'lottery_id',
        'issue',
        'begin_time',
        'end_time',
        'offical_time',
        'wn_number',
        'encoder',
        'encoded_at',
        'status',
        'status_count',
        'status_prize',
        'status_commission',
        'status_trace_prj',
        'code_center_return',
    ];

    /**
     * 视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $listColumnMaps = [
        'status_count' => 'formatted_status_count',
        'status' => 'formatted_status',
        'begin_time' => 'formatted_begin_time',
        'end_time' => 'formatted_end_time',
        'offical_time' => 'formatted_offical_time',
        'status_prize' => 'formatted_status_prize',
        'status_commission' => 'formatted_status_commission',
        'status_trace_prj' => 'formatted_status_trace_prj'
    ];

    /**
     * 视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $viewColumnMaps = [
        'status_count' => 'formatted_status_count',
        'status' => 'formatted_status',
        'begin_time' => 'formatted_begin_time',
        'end_time' => 'formatted_end_time',
    ];
    public static $ignoreColumnsInView = [
        'end_time2',
        'locker',
    ];
    protected $fillable = [
        'name',
        'type',
        'lotto_type',
        'lottery_id',
        'issue',
        'allow_encode_time',
        'issue_rule_id',
        'cycle',
        'status',
        'high_frequency',
        'sort_winning_number',
        'valid_nums',
        'buy_len',
        'code_len',
        'identifier',
        'days',
        'issue_over_midnight',
        'issue_format',
        'begin_time',
        'offical_time',
        'end_time',
        'end_time2',
        'open',
        'need_draw',
        'sequence',
    ];

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
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'status' => 'aWnNumberStatus'
    ];

    /**
     * 获取最后一期已经存在的奖期
     * @param int $iLotteryId 彩种id
     */
    public function getLastIssueInfo($iLotteryId, $mBeforeTime = null) {
        $oQuery = DB::table($this->table)->where('lottery_id', '=', $iLotteryId);
        !$mBeforeTime or $oQuery = $oQuery->where('end_time', '<', $mBeforeTime);
        $aIssue = $oQuery->orderBy('end_time', 'desc')->take(1)->get();
        return count($aIssue) > 0 ? objectToArray($aIssue[0]) : '';
    }

    /**
     * 返回指定时间之前的最后一期奖期号
     *
     * @param int $iLotteryId
     * @param int or string $mBeforeTime time int or datetime
     * @return type
     */
    function getLastIssue($iLotteryId, $mBeforeTime = null) {
        $aInfo = $this->getLastIssueInfo($iLotteryId, $mBeforeTime);
        return $aInfo ? $aInfo['issue'] : '';
    }

    /**
     * 获取下一期奖期
     *
     * @param string $sIssueRule 	奖期规则
     * @param string $sLastIssue	 上一期奖期
     * @param string $iStarDate	 	奖期开始时间
     * @return string				下一期奖期
     */
    function getNextIssue($sIssueRule, $sLastIssue = null, $iStarDate, $bIsLow = false) {
        $sIssue = str_replace('(M)', date('m', $iStarDate), $sIssueRule);
        $sIssue = str_replace('(D)', date('d', $iStarDate), $sIssue);
        preg_match_all("/\([N,T,C](.*)\)/", $sIssue, $aIssueOrder);
        $iIssueOrderLength = $aIssueOrder[1][0];
        if ($bIsLow) {
            $bAccumulatingOfYear = strpos($sIssueRule, 'T') !== false;
            if ($bAccumulatingOfYear) {
                $iOldYear = date('Y', $iStarDate);
                date('md', $iStarDate) > '0101' or $iOldYear--;
                $iNewYear = date('Y', $iStarDate + 3600 * 24);
                if ($iNewYear > $iOldYear) {
                    $iYear = $iNewYear;
                    $sNextIssueOrder = 1;
                } else {
                    $iYear = $iOldYear;
                    $sLastIssueOrder = substr($sLastIssue, strlen($sLastIssue) - $iIssueOrderLength, $iIssueOrderLength);
                    $sNextIssueOrder = $sLastIssueOrder + 1;
                }
            }
        } else {
            $iYear = date('Y', $iStarDate);
            $sLastIssueOrder = substr($sLastIssue, strlen($sLastIssue) - $iIssueOrderLength, $iIssueOrderLength);
            $sNextIssueOrder = $sLastIssueOrder + 1;
        }
        $sIssue = str_replace('(Y)', $iYear, $sIssue);
        $sIssue = str_replace('(y)', substr($iYear, 2), $sIssue);
//        $sNextIssueOrder = (string) $sNextIssueOrder;

        $sNextIssueOrder = str_pad($sNextIssueOrder, $iIssueOrderLength, 0, STR_PAD_LEFT);
//        pr($sNextIssueOrder);
        return preg_replace("/\([N,T,C](.*)\)/", $sNextIssueOrder, $sIssue);
    }

    /**
     * 获取期号中的日期标记信息
     *
     * @param 	string $sIssueRule    			奖期规则
     * @param 	string $sIssue					指定奖期
     * @param   int    $sDate					检测时间
     * @return 	string $sIssueDateMessage		期号中的日期标记信息
     */
    function getIssueDateMessage($sIssueRule, $sIssue = '', $sDate = '') {
        $sIssueDateMessage = '';
        $iDateMessageLength = 0;
        strpos($sIssueRule, 'Y') === false or $iDateMessageLength += 4;
        strpos($sIssueRule, 'y') === false or $iDateMessageLength += 2;
        strpos($sIssueRule, 'M') === false or $iDateMessageLength += 2;
        strpos($sIssueRule, 'D') === false or $iDateMessageLength += 2;
        if ($sDate) {
            $iDate = strtotime($sDate);
            $sIssueDateMessage = str_replace('(Y)', date('Y', $iDate), $sIssueRule);
            $sIssueDateMessage = str_replace('(y)', date('y', $iDate), $sIssueDateMessage);
            $sIssueDateMessage = str_replace('(M)', date('m', $iDate), $sIssueDateMessage);
            $sIssueDateMessage = str_replace('(D)', date('d', $iDate), $sIssueDateMessage);
            $sIssueDateMessage = substr($sIssueDateMessage, 0, $iDateMessageLength);
        } elseif ($sIssue) {
            $sIssueDateMessage = substr($sIssue, 0, $iDateMessageLength);
        }
        return $sIssueDateMessage;
    }

    /**
     * 保存奖期，用于奖期生成程序
     * @param array $aData
     * @return bool
     */
    function saveAllIssues($aData) {
        return DB::table($this->table)->insert($aData);
    }

    function findAllIssuesByCond($iLotteryId, $iBeginTime, $iEndTime) {
        return DB::table($this->table)->where('lottery_id', $iLotteryId)->where('end_time', '>=', $iBeginTime)->where('end_time', '<=', $iEndTime)->orderBy('end_time', 'asc')->get();
    }

    function deleteAll($aConditions) {
        $oQuery = $this->doWhere($aConditions);
        return $oQuery->delete();
    }

    function field($sField, $aConditions) {
        $oModels = $this->doWhere($aConditions)->get([$sField]);
        return ($oModels != null && isset($oModels[0])) ? $oModels[0]->$sField : null;
    }

    /**
     * 返回第一个没有号码的奖期对象
     * @param int $iLotteryId
     * @return Issue
     */
    public static function getFirstNonNumberIssue($iLotteryId) {
        return self::where('lottery_id', '=', $iLotteryId)->where('end_time', '<', time())->where('status', '=', self::ISSUE_CODE_STATUS_WAIT_CODE)->orderBy('issue', 'asc')->first();
    }

    public function setCalulated($bFinished = true) {
        $iToStatus = $bFinished ? self::CALCULATE_FINISHED : self::CALCULATE_PARTIAL;
        $iCount = $this->where('id', '=', $this->id)->where('status_count', '=', self::CALCULATE_PROCESSING)->update(['status_count' => $iToStatus]);
        if ($bSucc = $iCount > 0) {
            $this->status_count = $iToStatus;
        }
        return $bSucc;
    }

    public function setPrizeFinishStatus($bFinished = true) {
        $iToStatus = $bFinished ? self::PRIZE_FINISHED : self::PRIZE_PARTIAL;
        $aFromStatus = [self::PRIZE_PROCESSING, self::PRIZE_PARTIAL];
        $iCount = $this->where('id', '=', $this->id)->whereIn('status_prize', $aFromStatus)->update(['status_prize' => $iToStatus]);
        if ($bSucc = $iCount > 0) {
            $this->status_prize = $iToStatus;
        }
        return $bSucc;
    }

    public function setCommissionFinishStatus($bFinished = true) {
        $iToStatus = $bFinished ? self::COMMISSION_FINISHED : self::COMMISSION_PARTIAL;
        $aFromStatus = [self::COMMISSION_PROCESSING, self::COMMISSION_PARTIAL];
        $iCount = $this->where('id', '=', $this->id)->whereIn('status_commission', $aFromStatus)->update(['status_commission' => $iToStatus]);
        if ($bSucc = $iCount > 0) {
            $this->status_commission = $iToStatus;
        }
        return $bSucc;
    }

    public function setTracePrjFinishStatus($bFinished = true) {
        $iToStatus = $bFinished ? self::TRACE_PRJ_FINISHED : self::TRACE_PRJ_PARTIAL;
        $aFromStatus = [self::TRACE_PRJ_PROCESSING, self::TRACE_PRJ_PARTIAL];
        $iCount = $this->where('id', '=', $this->id)->whereIn('status_trace_prj', $aFromStatus)->update(['status_trace_prj' => $iToStatus]);
        if ($bSucc = $iCount > 0) {
            $this->status_trace_prj = $iToStatus;
        }
        return $bSucc;
    }

    public function setPrizeProcessing() {
        if ($bSucc = $this->where('id', '=', $this->id)->where('status_prize', '=', self::PRIZE_NONE)->update(['status_prize' => self::PRIZE_PROCESSING]) > 0) {
            $this->status_prize = self::PRIZE_PROCESSING;
        }
        return $bSucc;
    }

    public function setCommissionProcessing() {
        if ($bSucc = $this->where('id', '=', $this->id)->where('status_commission', '=', self::COMMISSION_NONE)->update(['status_commission' => self::COMMISSION_PROCESSING]) > 0) {
            $this->status_commission = self::COMMISSION_PROCESSING;
        }
        return $bSucc;
    }

    public function setTracePrjProcessing() {
        if ($bSucc = $this->where('id', '=', $this->id)->where('status_trace_prj', '=', self::TRACE_PRJ_NONE)->update(['status_trace_prj' => self::TRACE_PRJ_PROCESSING]) > 0) {
            $this->status_trace_prj = self::TRACE_PRJ_PROCESSING;
        }
        return $bSucc;
    }

    public static function getIssueObject($iLotteryId, $sIssue) {
        $aConditions = [
            'lottery_id' => [ '=', $iLotteryId],
            'issue' => [ '=', $sIssue],
        ];
        return self::doWhere($aConditions)->get()->first();
    }

    /**
     * 将计奖状态写为CALCULATE_PROCESSING
     * @return bool
     */
    public function lockCalculate() {
        $aConditions = [
            'id' => [ '=', $this->id],
            'wn_number' => [ '<>', ''],
            'status' => [ '=', self::ISSUE_CODE_STATUS_FINISHED],
            'status_count' => [ 'in', [ self::CALCULATE_NONE, self::CALCULATE_PARTIAL]],
            'end_time' => [ '<', time()]
        ];
        $data = [
            'status_count' => self::CALCULATE_PROCESSING,
            'locker' => $iLocker = DbTool::getDbThreadId()
        ];
        if ($bSucc = self::doWhere($aConditions)->update($data) > 0) {
            $this->status_count = self::CALCULATE_PROCESSING;
            $this->locker = $iLocker;
        }
        return $bSucc;
    }

    /**
     * 将计奖状态由CALCULATE_PROCESSING改为CALCULATE_NONE
     * @param int $iLotteryId
     * @param string $sIssue
     * @return Issue|false
     */
    public static function unlockCalculate($iLotteryId, $sIssue, $iLocker, $bReturnObject = true) {
        $aConditions = [
            'lottery_id' => [ '=', $iLotteryId],
            'issue' => [ '=', $sIssue],
            'status' => [ '=', self::ISSUE_CODE_STATUS_FINISHED],
            'status_count' => [ '=', self::CALCULATE_PROCESSING],
            'locker' => [ '=', $iLocker],
        ];
//        pr($aConditions);
        $data = [
            'status_count' => self::CALCULATE_NONE,
            'locker' => 0,
        ];
        $iCount = self::doWhere($aConditions)->update($data);
//        pr($iCount);
        if ($iCount > 0) {
//            $iLocker = 0;
            return $bReturnObject ? self::find($id) : true;
        }
        return false;
    }

    /**
     * 设置中奖号码
     * @param string $sWinningNumber
     * @param CodeCenter $oCodeCenter
     * @return boolean
     */
    public function setWinningNumber($sWinningNumber, $oCodeCenter = null) {
        if (time() < $this->allow_encode_time) {
            return -1;
        }
        $this->compileCode($sWinningNumber);
        $data = [
            'wn_number' => $sWinningNumber,
            'status' => self::ISSUE_CODE_STATUS_FINISHED,
            'encoded_at' => Carbon::now()->toDateTimeString(),
            'encoder_id' => $oCodeCenter ? 60000 + $oCodeCenter->id : Session::get('admin_user_id'),
            'encoder' => $oCodeCenter ? $oCodeCenter->name : Session::get('admin_username')
        ];
//        pr($data);
//        exit;
        $iCount = DB::table($this->table)->where('id', '=', $this->id)->where('wn_number', '=', '')->where('status', '=', self::ISSUE_CODE_STATUS_WAIT_CODE)->update($data);
        if ($bSucc = $iCount > 0) {
            foreach ($data as $key => $value) {
                $this->$key = $value;
            }
        }
        return $bSucc;
    }

    protected function getFormattedStatusCountAttribute() {
        return __('_issue.' . strtolower(Str::slug(static::$calculateStatus[$this->attributes['status_count']])));
    }

    protected function getFormattedStatusPrizeAttribute() {
        return __('_issue.' . strtolower(Str::slug(static::$calculateStatus[$this->attributes['status_prize']])));
    }

    protected function getFormattedStatusCommissionAttribute() {
        return __('_issue.' . strtolower(Str::slug(static::$calculateStatus[$this->attributes['status_commission']])));
    }

    protected function getFormattedStatusTracePrjAttribute() {
        return __('_issue.' . strtolower(Str::slug(static::$calculateStatus[$this->attributes['status_trace_prj']])));
    }

    protected function getFormattedStatusAttribute() {
        return __('_issue.' . strtolower(Str::slug(static::$winningNumberStatus[$this->attributes['status']])));
    }

    protected function getFormattedBeginTimeAttribute() {
        return Carbon::createFromTimestamp($this->attributes['begin_time'])->toDateTimeString();
    }

    protected function getFormattedEndTimeAttribute() {
        return Carbon::createFromTimestamp($this->attributes['end_time'])->toDateTimeString();
    }

    protected function getFormattedOfficalTimeAttribute() {
        return Carbon::createFromTimestamp($this->attributes['offical_time'])->toDateTimeString();
    }

        public function addCalculateTask() {
        $aJobData = [
            'lottery_id' => $this->lottery_id,
            'issue' => $this->issue,
        ];
        return BaseTask::addTask('CalculatePrize', $aJobData, 'calculate');
    }
    
    /**
     * 发起计奖任务
     * @return bool
     */
    public function setCalculateTask() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::ISSUE_CODE_STATUS_FINISHED],
//            'status_count' => ['<>',self::CALCULATE_PROCESSING]
        ];

        if ($bSucc = self::doWhere($aConditions)->update(['status_count' => self::CALCULATE_NONE]) > 0) {
            $aJobData = [
                'lottery_id' => $this->lottery_id,
                'issue' => $this->issue,
            ];
             $bSucc = $this->addCalculateTask();
        }
        return $bSucc;
    }

    /**
     * 发起未开奖撤单任务
     * @return bool
     */
    public function setCancelTask($sBeginTime = null) {
        if ($this->status == self::ISSUE_CODE_STATUS_CANCELED) {
            $bSucc = true;
        } else {
            $aConditions = [
                'id' => ['=', $this->id],
                'status' => ['=', self::ISSUE_CODE_STATUS_WAIT_CODE],
            ];
            $data = [
                'status' => self::ISSUE_CODE_STATUS_CANCELED,
                'status_count' => self::CALCULATE_FINISHED
            ];
            $bSucc = self::doWhere($aConditions)->update($data) > 0;
        }
        if ($bSucc) {
            $aJobData = [
                'lottery_id' => $this->lottery_id,
                'issue' => $this->issue,
            ];
            $sBeginTime == null or $aJobData['begin_time'] = $sBeginTime;
            $bSucc = BaseTask::addTask('CancelIssue', $aJobData, 'calculate');
            $this->setKl28CancelTask($this->lottery_id, $this->issue);
//            for ($i = 0,$bSucc = false; $i < 10; $i++){
//                if ($bSucc = Queue::push('CancelIssue',$aJobData,Config::get('schedule.calculate')) > 0){
//                    break;
//                }
//            }
        }
        return $bSucc;
    }

    public function setKl28CancelTask($iLotteryId, $sIssue) {
        $aLotteryIdMap = [
            1 => 54,
            3 => 55,
            6 => 56,
            7 => 57,
        ];
        if (isset($aLotteryIdMap[$iLotteryId])) {
            $oIssue = self::getIssue($aLotteryIdMap[$iLotteryId], $sIssue);
            if ($oIssue->status == self::ISSUE_CODE_STATUS_CANCELED) {
                $bSucc = true;
            } else {
                $aConditions = [
                    'id' => ['=', $oIssue->id],
                    'status' => ['=', self::ISSUE_CODE_STATUS_WAIT_CODE],
                ];
                $data = [
                    'status' => self::ISSUE_CODE_STATUS_CANCELED,
                    'status_count' => self::CALCULATE_FINISHED
                ];
                $bSucc = self::doWhere($aConditions)->update($data) > 0;
            }
            if ($bSucc) {
                $aJobData = [
                    'lottery_id' => $aLotteryIdMap[$iLotteryId],
                    'issue' => $sIssue,
                ];
                BaseTask::addTask('CancelIssue', $aJobData, 'calculate');
            }
        }
    }

    /**
     * 发起计奖重新开奖任务
     * @return boolean
     */
    public function setCancelPriceTask($sCustomerKey, $sNewCode) {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::ISSUE_CODE_STATUS_FINISHED],
        ];
        $data = [
            'status' => self::ISSUE_CODE_STATUS_CANCELED,
//            'status_count' => self::CALCULATE_NONE,
        ];
        if ($bSucc = self::doWhere($aConditions)->update($data) > 0) {
            $aJobData = [
                'lottery_id' => $this->lottery_id,
                'issue' => $this->issue,
                'new_code' => $sNewCode,
                'customer_key' => $sCustomerKey,
            ];
            $bSucc = BaseTask::addTask('CancelPrize', $aJobData, 'calculate');
        }
        return $bSucc;
    }

    /**
     * 返回第一个在当前期以前尚未计奖完成的奖期对象
     * @param int $iLotteryId
     * @return Issue
     */
    public function getFirstUnCalculatedIssueBeforeIssue() {
        return self::where('lottery_id', '=', $this->lottery_id)->where('issue', '<', $this->issue)->where('status_count', '<>', self::CALCULATE_FINISHED)->orderBy('issue', 'asc')->get()->first();
    }

    public function updateWnNumberCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE)
            return true;
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = $this->compileLastWnNumberCacheKey($this->lottery_id);
        Cache::forget($key);
        return true;
    }

    /**
     * 发起计奖重新开奖任务
     * @return boolean
     */
    public function reset() {
        $aConditions = [
            'id' => ['=', $this->id],
        ];
        $data = [
            'status' => self::ISSUE_CODE_STATUS_WAIT_CODE,
//            'status_count' => self::CALCULATE_NONE,
            'status_prize' => self::PRIZE_NONE,
            'status_commission' => self::COMMISSION_NONE,
            'status_trace_prj' => self::TRACE_PRJ_NONE,
            'wn_number' => '',
        ];
        $bSucc = self::doWhere($aConditions)->update($data) > 0;
        return $bSucc;
    }

    public static function getNeedCalculateIssues($iLotteryId = null) {
        $aConditions = [
            'status' => ['=', ISSUE::ISSUE_CODE_STATUS_FINISHED],
            'status_count' => ['in', [ISSUE::CALCULATE_NONE, ISSUE::CALCULATE_PARTIAL]],
        ];
        empty($iLotteryId) or $aConditions['lottery_id'] = ['=', $iLotteryId];
        return self::dowhere($aConditions)->orderBy('lottery_id', 'asc')->orderBy('issue', 'asc')->get(['id', 'lottery_id', 'issue']);
    }

    /**
     * 生成下一个奖期
     * @return bool
     */
    public function generateNextIssue($lottery_id) {
        $oLottery = Lottery::find($lottery_id);

        if (!$oLottery)
            return false;

        if (!$oLottery->is_trace_issue) {
            return true;
        }

        $iStartDay = time();
        $sIssueFormat = $oLottery->issue_format;

        $oLastIssue = self::where('lottery_id', '=', $lottery_id)->orderBy('id', 'desc')->first();

        if ($oLastIssue && $oLastIssue->end_time >= $iStartDay)
            return false;

        $sNextIssue = $this->getNextIssue($sIssueFormat, null, $iStartDay);

        if ($oLastIssue) {
            $sLastIssue = $oLastIssue->issue;

            preg_match_all("/\([N,T,C](.*)\)/", $sIssueFormat, $aIssueOrder);
            $iIssueOrderLength = $aIssueOrder[1][0];

            $sLastIssueOrder = substr($sLastIssue, 0, strlen($sLastIssue) - $iIssueOrderLength);
            $sNextIssueOrder = substr($sNextIssue, 0, strlen($sNextIssue) - $iIssueOrderLength);

            if ($sLastIssueOrder == $sNextIssueOrder) {
                $sNextIssue = $this->getNextIssue($sIssueFormat, $sLastIssue, $iStartDay);
            }
        }

        $oIssueRule = new IssueRule;
        $aIssueRules = $oIssueRule->findIssueRulesByLotteryId($lottery_id);

        if (!$delay = Series::find($oLottery->series_id)->delay_issue_start_time) {
            $delay = SysConfig::readValue('delay_next_issue_start_time');
        }

        foreach ($aIssueRules as $aRuleData) {

            $iIssueBeginTime = $iStartDay;
            if ($oLastIssue)
                $iIssueBeginTime += $delay;

            $iIssueEndTime = $iIssueBeginTime + $aRuleData->cycle;
            $iOfficalTime = $iIssueEndTime + $aRuleData->stop_adjust_time;

            $data = array(
                'lottery_id' => $oLottery->id,
                'issue' => $sNextIssue,
                'begin_time' => $iIssueBeginTime,
                'offical_time' => $iOfficalTime,
                'end_time' => $iIssueEndTime,
                'end_time2' => date('Y-m-d H:i:s', $iIssueEndTime),
                'status' => Issue::ISSUE_CODE_STATUS_WAIT_CODE,
                'allow_encode_time' => $iIssueEndTime,
                'issue_rule_id' => $aRuleData->id,
                'cycle' => $aRuleData->cycle
            );

            $oIssue = new ManIssue($data);

            if ($result = $oIssue->save()) {
                $sProjectTime = $this->getProjectTime($oIssue->end_time);
//                $date = Carbon::now()->addSeconds($sProjectTime-$iStartDay);
                $date = Carbon::now()->addSeconds($iOfficalTime - $iStartDay - 1);
                $queueData = ['lottery_id' => $oLottery->id, 'issue' => $sNextIssue, 'project_time' => date('Y-m-d H:i:s', $sProjectTime)];
                Queue::later($date, 'GetTraceIssueCodeFromMmc', $queueData, 'get_trace_issue_code');
            }

            return $result;
        }
        return false;
    }

    public function getProjectTime($endTime) {
        if (empty($endTime))
            return false;
        return $endTime;
        $endSecond = date('s', $endTime);
        $fSecond = $endSecond % 10 > 5 ? 10 : 0;
        $intSecond = intval($endSecond / 10) * 10;

        return strtotime(date('Y-m-d H:i:00', $endTime)) + $intSecond + $fSecond;
    }

    /**
     * 获取有效开奖号
     * @param $sWinningNumber
     * @return string
     */
    public function compileCode(& $sWinningNumber) {

        $sSeriesName = Series::find(Lottery::find($this->lottery_id)->series_id)->name;

        if ($sSeriesName == 'BJL' && $sWinningNumber) { //百家乐

            $splitChar = Config::get('bet.split_char');
            $splitCharInArea = Config::get('bet.split_char_lotto_in_area');

            if (strstr($sWinningNumber, $splitChar))
                return;

            $aWinningNumber = explode($splitCharInArea, $sWinningNumber);

            foreach ($aWinningNumber as $key => $iWnNumber) {
                $aWnNumber[$key] = $iWnNumber <= 13 ? intval($iWnNumber) : ($iWnNumber % 13 ? $iWnNumber % 13 : 13);
                $aWnNumber[$key] = $aWnNumber[$key] < 10 ? $aWnNumber[$key] : 0;
            }

            $aXNumber = array_slice($aWnNumber, 0, 3);
            $aZNumber = array_slice($aWnNumber, 3, 3);

            $aXDigital = array($aXNumber[0], $aXNumber[1]);
            $aZDigital = array($aZNumber[0], $aZNumber[1]);

            //如果闲家或者庄家两点之和是8点或者9点，则不用抽第三张牌，直接比较大小
            if (($aXNumber[0] + $aXNumber[1]) % 10 < 8 && ($aZNumber[0] + $aZNumber[1]) % 10 < 8) {
                //计算抽取三张的时候
                $iZSum = array_sum($aZDigital) % 10;
                if (array_sum($aXDigital) % 10 <= 5)
                    $aXDigital = $aXNumber;

                if (count($aXDigital) == 2 && $iZSum < 6)
                    $aZDigital = $aZNumber;
                elseif (count($aXDigital) == 3) {
                    if ($iZSum <= 2)
                        $aZDigital = $aZNumber;
                    elseif (isset($aXDigital[2])) {
                        if (($iZSum == 3 && $aXDigital[2] != 8) || ($iZSum == 4 && !in_array($aXDigital[2], [0, 1, 8, 9])) || ($iZSum == 5 && in_array($aXDigital[2], [4, 5, 6, 7])) || ($iZSum == 6 && in_array($aXDigital[2], [6, 7]))) {
                            $aZDigital = $aZNumber;
                        }
                    }
                }
            }
            $sWinningNumber = implode($splitCharInArea, array_slice($aWinningNumber, 0, count($aXDigital))) . $splitChar . implode($splitCharInArea, array_slice($aWinningNumber, 3, count($aZDigital)));
        }
    }

    /**
     * 返回最早的没有号码的奖期对象集
     * @param int $iLotteryId
     * @param int $iCount 数量
     * @return Issue
     */
    public static function getNonNumberIssues($iLotteryId, $iCount) {
        return self::where('lottery_id', '=', $iLotteryId)->where('allow_encode_time', '<', time())->where('status', '=', self::ISSUE_CODE_STATUS_WAIT_CODE)->orderBy('issue', 'asc')->take($iCount)->get();
    }
    
      /**
     * 生成奖期并保存
     * 前后台合并为一个方法统一调用
     * @param array $oLottery       彩种配置数组
     * @param array $aRules         奖期规则数组
     * @param date $dBeginDate      开始日期
     * @param date $dEndDate        结束日期
     * @param object $sLastIssue    最后一次奖期
     * @param string $sStartIssue   开始奖期号
     * @param string $iCount        生成的issue总条数
     * @return bool                 是否成功
     */
    public function autoGenerateIssues($oLottery, $aRules, $dBeginDate, $dEndDate, $sLastIssue, $sStartIssue = '', & $iCount) {
        set_time_limit(0);
        $sIssueFormat = $oLottery->issue_format;
        $bAccumulating = $oLottery->isAccumulating();
        //奖期是否需要全年累加
        if ( $bAccumulating ) {
            if (!$sStartIssue) {
                $sLastIssue = $this->getLastIssue($oLottery->id);
                $sStartIssue = $this->getNextIssue($oLottery->issue_format, $sLastIssue, strtotime($dBeginDate), !$oLottery->high_frequency);
            }
            if ($this->getIssueDateMessage($sIssueFormat, $sStartIssue) != $this->getIssueDateMessage($sIssueFormat, '', $dBeginDate)) {
                return FALSE;
            }
        }

        $iStartDay = strtotime($dBeginDate);
        $iEndDay = strtotime($dEndDate); //需要添加的奖期结束时间
        // pr($dBeginDate);
        // exit;
        
        if ($bAccumulating) {    //数据库中原来不存在奖期数据
            $sNextIssue = $sStartIssue; //输入的开始奖期
        } else {
            $sNextIssue = $this->getNextIssue($sIssueFormat, null, $iStartDay); //获取下一期奖期
        }
        // restdays
        $aRestdayList = array();    // temp, need modify

        // 获取休市信息
        // $oRestSetting = new RestSetting;
        // $aRestSetting = $oRestSetting->getClosedMarketInfoByLotteryId($oLottery->id);
        // if (!empty($aRestSetting)) {
        //     $iExceptionType = $aRestSetting['close_type'];
        //     if ($iExceptionType == RestSetting::TYPE_DRAW_TIME) {
        //         $iExceptionBeginTime = strtotime($aRestSetting['start_date']);
        //         $iExceptionEndTime = strtotime($aRestSetting['end_date']);
        //     }
        //     $iExceptionSuccessive = $aRestSetting['issue_successive'];
        //     if ($iExceptionType == RestSetting::TYPE_REPEATE)
        //         $aExceptionDays = $aRestSetting['week'] != '' ? explode(",", $aRestSetting['week']) : array();
        // }

        $bSucc = true;
        while ($iStartDay <= $iEndDay) {
            $data = [];
            $weekDay = date('w', $iStartDay);
            if ((($oLottery->days) & pow(2, $weekDay)) == 0) {//休息日判断
                continue; //检测到对应天是休息日,不添加这一天的奖期号，继续循环下一天
            }
            if (date('Y', $iStartDay) > date('Y', $iStartDay - 3600 * 24) && strpos($sIssueFormat, 'C') === false) {     //跨年
                $sNextIssue = $this->getNextIssue($sIssueFormat, null, $iStartDay);
            }
            if (!$bAccumulating) {//不需要全年累加期数的情况
                $sNextIssue = $this->getNextIssue($sIssueFormat, null, $iStartDay);
            }
            // if (isset($iExceptionType) && $iExceptionType == 2 && in_array($weekDay, $aExceptionDays)) {
            //     $iExceptionBeginTime = strtotime(date('Y-m-d', $iStartDay) . ' ' . $this->request->data['IssueBatch']['exceptionBeginTime']);
            //     $iExceptionEndTime = strtotime(date('Y-m-d', $iStartDay) . ' ' . $this->request->data['IssueBatch']['exceptionEndTime']);
            // }
            
            // 判断是否有例外，在例外指定的时间段内不产生奖期数据
            // if ($iException == 1) {
            //     if($iExceptionType == 1){
            //  一段时间没有奖期数据
            //     }else if($iExceptionType == 2){
            //       //重复时间段内没有奖期数据
            //     }
            //     $iBonusDay += 3600 * 24; //在最近一期的基础上增加一天
            //     continue; //检测到对应天是休息日,不添加这一Day的奖期号，继续循环下一天
            // }
            
            // 得到指定玩法的奖期规则
            foreach ($aRules as $aRuleData) {
                $iBeginTimeOfRule = strtotime(date('Y-m-d', $iStartDay) . $aRuleData->begin_time); //第一期开始时间
                $iIssueBeginTime = strtotime(date('Y-m-d', $iStartDay) . $aRuleData->first_time) - $aRuleData->cycle;
                $iEndTime = strtotime(date('Y-m-d', $iStartDay) . $aRuleData->end_time); //最后一期结束时间
                $aRuleData->begin_time < $aRuleData->end_time or $iEndTime += 3600 * 24; //最后一期结束时间，跨天增加一天
                $bIsFirstIssue = true;      // first
                while ($iIssueBeginTime + $aRuleData->stop_adjust_time <= $iEndTime - $aRuleData->cycle) {
                    $iIssueEndTime = $iIssueBeginTime + $aRuleData->cycle;
                    !in_array($oLottery->id, [26, 29, 32, 35, 39, 42]) or $iIssueEndTime = $iIssueEndTime - 30;
                    !in_array($oLottery->id, [45,48,51]) or $iIssueEndTime = $iIssueEndTime - 60;
                    !$bIsFirstIssue or $iIssueEndTime -= $aRuleData->stop_adjust_time;
                    $iOfficalTime = $iIssueEndTime + $aRuleData->stop_adjust_time;

                    // if (isset($iExceptionType) && $iExceptionType == 1) {
                    //     if ($iIssueBeginTime >= $iExceptionBeginTime && $iIssueBeginTime < $iExceptionEndTime) {
                    //         if ($iExceptionSuccessive == 0)
                    //             $sNextIssue = $this->getNextIssue($sIssueFormat, $sNextIssue, $iStartDay);
                    //         $iIssueBeginTime = $iIssueEndTime;
                    //         continue;
                    //     }
                    // }else if (isset($iExceptionType) && $iExceptionType == RestSetting::TYPE_REPEATE && in_array($weekDay, $aExceptionDays)) {
                    //     if ($iIssueBeginTime >= $iExceptionBeginTime && $iIssueBeginTime < $iExceptionEndTime) {
                    //         // 不是连续的奖期
                    //         if ($iExceptionSuccessive == 0)
                    //             $sNextIssue = $this->getNextIssue($sIssueFormat, $sNextIssue, $iStartDay);
                    //         $iIssueBeginTime = $iIssueEndTime;
                    //         continue;
                    //     }
                    // }
                    
                    $data[] = array(
                        'lottery_id' => $oLottery->id,
                        'issue' => $sNextIssue,
                        'begin_time' => $iIssueBeginTime,
                        // 'sale_close_time' => $iIssueEndTime - $aRuleInfo['stop_adjust_time'],
                        'offical_time' => $iOfficalTime,
                        'end_time' => $iIssueEndTime,
                        'end_time2' => date('Y-m-d H:i:s', $iIssueEndTime),
                        'status' => Issue::ISSUE_CODE_STATUS_WAIT_CODE,
                        'allow_encode_time' => $iIssueEndTime + $aRuleData->encode_time,
                        'issue_rule_id' => $aRuleData->id,
                        'cycle' => $aRuleData->cycle
                    );
                    $bIsFirstIssue = false;
                    $sNextIssue = $this->getNextIssue($sIssueFormat, $sNextIssue, $iStartDay);
                    !in_array($oLottery->id, [26, 29, 32, 35, 39, 42]) or $iIssueEndTime = $iIssueEndTime + 30;
                    !in_array($oLottery->id, [45,48,51]) or $iIssueEndTime = $iIssueEndTime + 60;
                    $iIssueBeginTime = $iIssueEndTime;
                }
            }
            if(!$bSucc = self::saveAllIssues($data)) {
                // $queries = DB::getQueryLog();
                // pr($queries);exit;
                break;
            }
            $iStartDay += 3600 * 24; //在最近一期的基础上增加一天
            $iCount += count($data);
        }
        return $bSucc;
    }
    
    
    public static function getLastIssueObject($iLotteryId, $iBeforeTime = null) {
        $oQuery = self::where('lottery_id', '=', $iLotteryId);
        !$iBeforeTime or $oQuery = $oQuery->where('end_time', '<', $iBeforeTime);
        return $oQuery->orderBy('end_time', 'desc')->take(1)->first();
    }


}
