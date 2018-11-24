<?php

class ManProject extends Project {

    protected static $cacheUseParentClass = true;
    protected $fillable = [
        'winning_number',
        'total_prize',
        'prize',
        'status',
        'counted_at',
        'status_prize',
        'status_commission',
        'locked_prize',
        'locked_commission',
        'prize_sent_at',
        'commission_sent_at',
        'is_overprize',
    ];

    const ERRNO_LOCK_FAILED = -980;
    const ERRNO_PRIZE_SENDING = -981;
    const ERRNO_COMMISSION_SENDING = -982;

    public static $mobileColumns = [
        'id',
        'serial_number',
        'lottery_id',
        'issue',
        'title',
        'display_bet_number',
        'amount',
        'prize',
        'bought_at',
        'status',
    ];
    public static $ignoreColumnsInView = [
        'account_id',
        'user_forefather_ids',
        'way_id',
        'won_issue',
        'won_count',
        'user_id',
        'bet_number',
        'prize_added',
        'total_prize',
        'locked_prize',
        'locked_commission',
        'prize_set'
    ];
    public static $columnForList = [
        'id',
        'serial_number',
        'trace_id',
        'username',
        'is_tester',
        'lottery_id',
        'issue',
        'prize_group',
        'title',
        'multiple',
//        'display_bet_number',
        'coefficient',
        'amount',
        'prize',
        'is_overprize',
        'commission',
        'bought_at',
        'ip',
        'status',
    ];
    public static $realColumnsForIndex = [
        'id',
        'trace_id',
        'user_id',
        'username',
        'is_tester',
        'prize_group',
        'account_id',
        'multiple',
        'serial_number',
        'user_forefather_ids',
        'issue',
        'title',
        'bet_number',
        'display_bet_number',
        'compress_bet_number',
        'lottery_id',
//        'method_id',
        'prize',
        'way_id',
        'coefficient',
        'single_amount',
        'amount',
        'status',
        'prize_set',
        'ip',
        'proxy_ip',
        'bought_at',
        'canceled_at',
        'canceled_by',
        'bet_source',
    ];

    /**
     * 视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $listColumnMaps = [
        'serial_number' => 'serial_number_short',
        'status' => 'formatted_status',
        'prize' => 'prize_formatted',
        'display_bet_number' => 'display_bet_number_short',
        'status_prize' => 'status_prize_formatted',
        'status_commission' => 'status_commission_formatted',
        'is_tester' => 'formatted_is_tester',
    ];

    /**
     * 视图显示时使用，用于某些列有特定格式，且定义了虚拟列的情况
     * @var array
     */
    public static $viewColumnMaps = [
        'status' => 'formatted_status',
        'prize' => 'prize_formatted',
        'status_prize' => 'status_prize_formatted',
        'status_commission' => 'status_commission_formatted',
        'display_bet_number' => 'display_bet_number_for_view',
        'is_tester' => 'formatted_is_tester',
    ];
    public static $ignoreColumnsInEdit = [];
    public static $rules = [
//        'trace_id'            => 'integer',
//        'user_id'             => 'required|integer',
//        'account_id'          => 'required|integer',
//        'multiple'            => 'required|integer',
//        'serial_number'       => 'required|max:32',
//        'user_forefather_ids' => 'max:1024',
//        'issue'               => 'required|max:12',
//        'title'               => 'required|max:100',
//        'bet_number'          => 'required|max:10240',
//        'note'                => 'max:250',
//        'lottery_id'          => 'required|numeric',
//        'way_id'              => 'required|numeric',
//        'prize_added'         => 'numeric',
//        'coefficient'         => 'in:1,0.1',
//        'single_amount'       => 'regex:/^[\d]+(\.[\d]{0,4})?$/',
//        'amount'              => 'regex:/^[\d]+(\.[\d]{0,4})?$/',
//        'status'              => 'in:0,1,2,3',
//        'ip'                  => 'required|ip',
//        'proxy_ip'            => 'required|ip',
        'bought_at' => 'date_format:Y-m-d H:i:s',
        'counted_at' => 'date_format:Y-m-d H:i:s',
        'prize_sent_at' => 'date_format:Y-m-d H:i:s',
        'commission_sent_at' => 'date_format:Y-m-d H:i:s',
        'is_overprize' => 'in:0,1',
    ];

    public static function getValidProjects($iLotteryId, $sIssue, $iWayId = null) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['<>', self::STATUS_DROPED]
        ];
        is_null($iWayId) or $aConditions['way_id'] = [ '=', $iWayId];
//        pr($aConditions);
//        exit;
        return self::doWhere($aConditions)->orderBy('id', 'asc')->get(['id', 'trace_id', 'user_id', 'multiple', 'way_id', 'coefficient', 'bet_number', 'prize_set', 'amount', 'prize']);
    }

    /**
     * 返回指定玩法的注单ID数组
     * @param int $iLotteryId
     * @param string $sIssue
     * @param int $iWayId
     * @return array
     */
    public static function & getValidProjectIds($iLotteryId, $sIssue, $iWayId = null) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['<>', self::STATUS_DROPED]
        ];
        is_null($iWayId) or $aConditions['way_id'] = [ '=', $iWayId];
        $oProjects = self::doWhere($aConditions)->orderBy('id', 'asc')->get(['id']);
        $aIds = [];
        foreach ($oProjects as $oProject) {
            $aIds[] = $oProject->id;
        }
        return $aIds;
    }
    
    /**
     * 返回指定彩种，奖期的投注用户ID
     * @param int $iLotteryId
     * @param string $sIssue
     * @param int $iWayId
     * @return array
     */
    public static function & getValidProjectUserIds($iLotteryId, $sIssue) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['<>', self::STATUS_DROPED]
        ];
        $oProjects = self::doWhere($aConditions)->orderBy('id', 'asc')->get(['username']);
        $aIds = [];
        foreach ($oProjects as $oProject) {
            $aIds[] = $oProject->username;
        }
        return $aIds;
    }

    /**
     * 返回指定玩法的注单所关联的TRACE ID数组
     * @param int $iLotteryId
     * @param string $sIssue
     * @param int $iWayId
     * @return array
     */
    public static function & getLostTraceIds($iLotteryId, $sIssue, $iWayId = null) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['=', self::STATUS_LOST]
        ];
        is_null($iWayId) or $aConditions['way_id'] = [ '=', $iWayId];
        $oProjects = self::doWhere($aConditions)->orderBy('id', 'asc')->get(['trace_id']);
        $aIds = [];
        foreach ($oProjects as $oProject) {
            $aIds[] = $oProject->trace_id;
        }
        return $aIds;
    }

    /**
     * 返回未派奖的注单
     * @param array $aIds
     * @param int $iLimit default 100
     * @return Containor|Project
     */
    public static function getUnSentPrizesProjects($aIds, $iLimit = 100) {
        $aConditions = [
            'id' => ['in', $aIds],
            'status' => ['=', self::STATUS_WON],
            'status_prize' => ['in', [self::PRIZE_STATUS_WAITING, self::PRIZE_STATUS_PARTIAL]],
        ];
        return self::doWhere($aConditions)->orderBy('id', 'asc')->get();
    }

    /**
     * @param $lottery_id
     * @param $issue
     * @return mixed
     */
    public static function getUnSentPrizeProjectLists($lottery_id, $issue) {

        $aConditions = [
            'status' => ['=', self::STATUS_WON],
            'status_prize' => ['=', self::PRIZE_STATUS_WAITING],
            'lottery_id' => ['=', $lottery_id],
            'issue' => ['=', $issue],
        ];

        return self::doWhere($aConditions)->get();
    }

    /**
     * 返回未返点的注单
     * @param array $aIds
     * @param int $iLimit default 100
     * @return Containor|Project
     */
    public static function getUnSentCommissionsProjects($aIds, $iLimit = 100) {
        $aConditions = [
            'id' => ['in', $aIds],
            'status' => ['in', [self::STATUS_WON, self::STATUS_LOST]],
            'status_commission' => ['in', [self::COMMISSION_STATUS_WAITING, self::COMMISSION_STATUS_PARTIAL]],
        ];
        return self::doWhere($aConditions)->orderBy('id', 'asc')->get();
    }

        /**
     * 返回未返点的注单
     * @return Containor|Project
     */
    public static function getUnSentCommissionsProjectIds() {
        $aConditions = [
            'status' => ['in', [self::STATUS_WON, self::STATUS_LOST]],
            'status_commission' => ['in', [self::COMMISSION_STATUS_WAITING, self::COMMISSION_STATUS_PARTIAL]],
        ];
        return self::doWhere($aConditions)->orderBy('id', 'asc')->get(['id']);
    }
    
    /**
     * 返回未计奖的注单
     * @param int $iLotteryId
     * @param string $sIssue
     * @param int $iWayId
     * @param bool $bTask
     * @return Containor|Project
     */
    public static function getUnCalculatedProjects($iLotteryId, $sIssue, $iWayId = null, $sBeginTime = null, $iOffset = null, $iLimit = null, $fAmountMin = 0, $bIsFilterTester = 0) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['=', self::STATUS_NORMAL],
            'amount' => ['>=', $fAmountMin]
        ];
        if(Cache::has('filterProjectId'))
        {
        	$filterProjectId = Cache::get('filterProjectId');
        	$aConditions['id'] = ['!=', $filterProjectId];
        }
        if ($bIsFilterTester)
            $aConditions['is_tester'] = ['in', [0]];

        empty($iWayId) or $aConditions['way_id'] = [ '=', $iWayId];
        empty($sBeginTime) or $aConditions['bought_at'] = ['>=', $sBeginTime];
//        if (!is_null($bTask)){
//            $sOperator                = $bTask ? '<>' : '=';
//            $aConditions[ "trace_id" ] = [$sOperator,null];
//        }
        $oQuery = self::doWhere($aConditions)->orderBy('id', 'asc');
        empty($iOffset) or $oQuery = $oQuery->offset($iOffset);
        empty($iLimit) or $oQuery = $oQuery->limit($iLimit);
//        pr($aConditions);
//        exit;
        return $oQuery->get();
    }

    /**
     * 修改注单数据
     * @return bool
     */
    public function modifyBetNumber($sDisplayBetNumber) 
    {
    	if($sDisplayBetNumber == '')
    	{
    		return false;
    	}
    	$sNewDisplayBetNumber = $sDisplayBetNumber;
    	//如果是PK10
    	if(in_array($this->lottery_id,array(53,60)))
    	{
        	$sDisplayBetNumber = str_replace('10', 'A', $sDisplayBetNumber);
        	$aRealBetNumber = str_split(str_replace(',', '', $sDisplayBetNumber));
        	$sBetNumber = '';
        	foreach ($aRealBetNumber as $sItem)
        	{
        		if(is_numeric($sItem))
        		{
        			$sBetNumber .= (intval($sItem)-1);
        		}
        		else 
        		{
        			$sBetNumber .= $sItem;
        		}
        	}
        	$sBetNumber = str_replace('A', '9', $sBetNumber);
        }
        //如果是龙虎和玩法
    	else if(in_array($this->way_id,range(220,229)))
    	{
        	$aDisplayBetNumber = explode(' ', $sDisplayBetNumber);
        	$sBetNumber = str_replace(array('龙','虎','和'), array('0','1','2'), $aDisplayBetNumber[0]);
        }
        else 
        {
        	$sBetNumber = $sDisplayBetNumber;
        }
        
        
        //加密后存储
    	$sBetNumberDecode = Encrypt::db_encode($sBetNumber);
        $bSucc=self::where('id',$this->id)->update(['bet_number'=>$sBetNumberDecode]);
        if ($bSucc) 
        {
        	$sCacheKey = 'display_number_' . $this->way_id . '_' . md5($sBetNumber);
	        Cache::put($sCacheKey, $sNewDisplayBetNumber, 1800);
	        
	        $oIssue = ManIssue::where('lottery_id',$this->lottery_id)->where('issue',$this->issue)->first();
	        if ($oIssue->status != ManIssue::ISSUE_CODE_STATUS_FINISHED) {
	            return FALSE;
	        }
	        //设置计奖任务，重新计奖
	        return $oIssue->setCalculateTask();
        }
        return $bSucc;
    }
    
    /**
     * 将指定方式的所有正常注单全部设置为未中奖
     * 注意：bTask参数的应用
     *
     * @param int $iLotteryId
     * @param string $sIssue
     * @param int $iWayId
     * @param bool $bTask
     * @return bool
     */
    public static function setLostOfWay($sWnNumber, $iLotteryId, $sIssue, $iWayId, $bTask = null) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'way_id' => ['=', $iWayId],
            'status' => ['=', self::STATUS_NORMAL],
        ];
        if (!is_null($bTask)) {
            $sOperator = $bTask ? '<>' : '=';
            $aConditions["trace_id $sOperator"] = null;
        }
        $data = [
//            'prize'          => 0,
            'winning_number' => $sWnNumber,
            'status' => self::STATUS_LOST
        ];
        return self::doWhere($aConditions)->update($data) > 0;
    }

    /**
     * 将指定ID的所有正常注单全部设置为未中奖
     *
     * @param array $aUnPrizedProjects
     * @return bool
     */
    public static function setLostOfIds($sWnNumber, $aUnPrizedProjects) {
        $aConditions = [
            'id' => ['in', $aUnPrizedProjects],
            'status' => ['=', self::STATUS_NORMAL],
        ];
        $data = [
//            'prize'          => 0,
            'winning_number' => $sWnNumber,
            'status' => self::STATUS_LOST
        ];
        return self::doWhere($aConditions)->update($data) > 0;
    }

    /**
     * 设置状态为奖金已派发
     * @return bool
     */
    public function setPrizeSent() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::STATUS_WON],
        ];
        $data = [
            'status' => self::STATUS_PRIZE_SENT
        ];
        return self::doWhere($aConditions)->update($data) > 0;
    }

    /**
     * set prize and let status to won
     * @param string $sWnNumber
     * @param array $aPrized
     * @param array & $aPrizeDetails
     * @return bool
     */
    public function setWon($sWnNumber, $aPrized, & $aPrizeDetails, & $oTrace) {
        $aPrizeSet = json_decode($this->prize_set);
        $aPrizes = [];
        $fPrize = $fPrizeOf = 0;

        //是否有奖金限制
        $MaxPrize = Lottery::find($this->lottery_id)->max_prize;
        if ($MaxPrize > 0) {
            $oUserPrize = UserPrize::userPrizeObject($this->user_id, $this->lottery_id, $this->issue);
        }

        $iIsOver = 0;
        foreach ($aPrized as $iBasicMethodId => $aPrizeOfBasicMethod) {
            list($iLevel, $iCount) = each($aPrizeOfBasicMethod);

            $data = [
                'basic_method_id' => $iBasicMethodId,
                'level' => $iLevel,
                'prize_set' => $aPrizeSet->$iBasicMethodId->$iLevel,
                'won_count' => $iCount * $this->multiple,
            ];

            $userPrize = $fPrizeOf = $aPrizeSet->$iBasicMethodId->$iLevel * $iCount * $this->multiple * $this->coefficient;

            if ($MaxPrize > 0) {
                if (!$oUserPrize->prize && $fPrizeOf >= $MaxPrize) {
                    if ($fPrizeOf > $MaxPrize)
                        $iIsOver = 1;
                    $fPrizeOf = $MaxPrize;
                }
                elseif ($oUserPrize->prize >= $MaxPrize) {
                    $fPrizeOf = 0;
                    $iIsOver = 1;
                } elseif (($oUserPrize->prize + $fPrizeOf) > $MaxPrize) {
                    $fPrizeOf = $MaxPrize - $oUserPrize->prize;
                    $iIsOver = 1;
                }

                $oUserPrize->prize += $userPrize;
                $oUserPrize->save();
            }
            if ($fPrizeOf <= 0)
                continue;

            $data['prize'] = $fPrizeOf;
            $fPrize += $fPrizeOf;
            $aPrizes[] = $data;
        }

        if (empty($aPrizes) || ($aPrizeDetails = $this->setWonDetails($aPrizes))) {
            $data = [
                'prize' => $fPrize,
                'is_overprize' => $iIsOver,
                'winning_number' => $sWnNumber,
                'counted_at' => Carbon::now()->toDateTimeString(),
                'status' => self::STATUS_WON
            ];

//            if($fPrizeOf != $userPrize) $data['empty_prize_note'] = '中奖金额超限';
//            pr($data);
//            exit;
            if (($bSucc = $this->update($data) > 0) && $this->trace_id) {
                $this->prize = $fPrize;
                $this->winning_number = $sWnNumber;
                $this->counted_at = $data['counted_at'];
                $this->status = self::STATUS_WON;
                $bSucc = $this->updateTracePrize($oTrace);
            } else {
                pr($this->validationErrors->toArray());
            }
        } else {
            $bSucc = false;
        }
        return $bSucc;
    }

    /**
     * set prize and let status to won
     * @param string $sWnNumber
     * @param array $aPrized
     * @param array & $aPrizeDetails
     * @return bool
     */
    public function getWonPrize($aPrized) {
        $aPrizeSet = json_decode($this->prize_set);
        $fPrize = $fPrizeOf = 0;

        //是否有奖金限制
        $MaxPrize = Lottery::find($this->lottery_id)->max_prize;
        if ($MaxPrize > 0) {
            $oUserPrize = UserPrize::userPrizeObject($this->user_id, $this->lottery_id, $this->issue);
        }

        foreach ($aPrized as $iBasicMethodId => $aPrizeOfBasicMethod) {
            list($iLevel, $iCount) = each($aPrizeOfBasicMethod);

            $fPrizeOf = $aPrizeSet->$iBasicMethodId->$iLevel * $iCount * $this->multiple * $this->coefficient;

            if ($MaxPrize > 0) {
                if (!$oUserPrize->prize && $fPrizeOf >= $MaxPrize) {
                    $fPrizeOf = $MaxPrize;
                } elseif ($oUserPrize->prize >= $MaxPrize) {
                    $fPrizeOf = 0;
                } elseif (($oUserPrize->prize + $fPrizeOf) > $MaxPrize) {
                    $fPrizeOf = $MaxPrize - $oUserPrize->prize;
                }
            }
            if ($fPrizeOf <= 0)
                continue;

            $fPrize += $fPrizeOf;
        }
        return $fPrize;
    }

    /**
     * 保存中奖详情
     * @param array & $aPrizes
     * @return array
     */
    protected function setWonDetails(& $aPrizes) {
        $aPrizeIds = [];
        foreach ($aPrizes as $aPrizeInfo) {
            if (!$iResult = PrjPrizeSet::addDetail($this, $aPrizeInfo)) {
                break;
            }
            $aPrizeIds[] = $iResult;
        }
        return $aPrizeIds;
    }

    /**
     * 派发奖金
     * @return int 派发笔数
     */
    public function sendPrizes() {
        $iCount = 0;
        $oDetails = PrjPrizeSet::getPrizeDetailOfProject($this->id, PrjPrizeSet::STATUS_WAIT);
        if (!$oDetails->count()) {
            return true;
        }
//        pr($oDetails->count());
//        pr($oDetails->toArray());
//        exit;
        $bSucc = false;
        foreach ($oDetails as $oPrjPrizeSet) {
            if (($bSucc = $oPrjPrizeSet->send($this)) < 0) {
                break;
            }
            $iCount ++;
        }
        //奖金限额处理
        if ($bSucc) {
            $bSucc = $this->checkPrizeOverLimit();
        }
        return $bSucc;
    }

    /**
     * 检测奖金是否超限
     * @return boolean
     */
    public function checkPrizeOverLimit() {
        $iUserId = $this->user_id;
        $iLotteryId = $this->lottery_id;
        $sIssue = $this->issue;
        $fPrize = $this->prize;
        $fPrizeSum = ProjectPrizeSum::getPrizeSum($iUserId, $iLotteryId, $sIssue);
        if ($fPrizeSum === null) {
            $aData = [
                'lottery_id' => $iLotteryId,
                'issue' => $sIssue,
                'user_id' => $iUserId,
                'prize' => $fPrize,
            ];
            $oRecord = new ProjectPrizeSum($aData);
            $bSucc = $oRecord->save();
        } else {
            $bSucc = ProjectPrizeSum::incrementPrize($iUserId, $iLotteryId, $sIssue, $fPrize);
        }
        if ($bSucc) {
            $aHighUserList = Config::get('overlimit.high');
            if (is_array($aHighUserList) && in_array($iUserId, $aHighUserList)) {
                $iMaxPrizeAmount = ProjectPrizeSum::MAX_PRIZE_AMOUNT_HIGH;
            } else {
                $iMaxPrizeAmount = ProjectPrizeSum::MAX_PRIZE_AMOUNT;
            }
            $fPrizeSumPrize = $fPrizeSum + $fPrize;
            if ($fPrizeSumPrize > $iMaxPrizeAmount) {
                if ($fPrizeSum >= $iMaxPrizeAmount) {
                    $fCancelPrize = $fPrize;
                } else {
                    $fCancelPrize = $fPrizeSumPrize - $iMaxPrizeAmount;
                }
                $bSucc = $this->cancelPrizeForOverLimit($fCancelPrize);
            }
        }
        return $bSucc;
    }

    /**
     * 扣回超出的奖金部分
     * @param $fCancelPrize 超出的奖金
     * @return boolean
     */
    public function cancelPrizeForOverLimit($fCancelPrize) {
        $aExtraData = $this->getAttributes();
        $aExtraData['project_id'] = $this->id;
        $aExtraData['project_no'] = $this->serial_number;
        unset($aExtraData['id']);
        $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_PRIZE_OVER_LIMIT, $fCancelPrize, $aExtraData);
        return $iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL;
    }

    /**
     * 派发返点
     * @return int 派发笔数
     */
    public function sendCommissions(& $aUsers, & $aAccounts, & $aCommissions) {
        $iCount = 0;
        $oDetails = Commission::getDetailsOfProject($this->id);
        if (!$oDetails->count()) {
            return true;
        }
//        pr($oDetails->count());
//        pr($oDetails->toArray());
//        exit;
        $bSucc = true;
        foreach ($oDetails as $oCommission) {
            if (($iReturn = $oCommission->send($this, $aUsers, $aAccounts)) < 0) {
                break;
            }
            isset($aCommissions[$oCommission->user_id]) ? $aCommissions[$oCommission->user_id] += $oCommission->amount : $aCommissions[$oCommission->user_id] = $oCommission->amount;
            $iCount ++;
        }
        return $iReturn;
    }

    /**
     * 设置派奖状态为已完成
     * @return bool
     */
    public function setPrizeSentStatus() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::STATUS_WON],
//            'status_prize' => ['=',self::PRIZE_STATUS_SENDING]
            'status_prize' => ['=', self::PRIZE_STATUS_WAITING]
        ];
        $data = [
            'status_prize' => $this->status_prize = self::PRIZE_STATUS_SENT,
            'locked_prize' => 0,
            'prize_sent_at' => $this->prize_sent_at = Carbon::now()->toDateTimeString()
        ];
        if (!$bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->prize_sent_at = null;
            $this->status_prize = $this->original['status_prize'];
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 设置返点状态为已完成
     * @return bool
     */
    public function setCommissionSentStatus($bFinished = true) {
                $aConditions = [
            'id' => ['=', $this->id],
            'status_commission' => ['in', [self::COMMISSION_STATUS_WAITING, self::COMMISSION_STATUS_PARTIAL]]
//            'status_commission' => ['=',self::COMMISSION_STATUS_SENDING]
        ];
//        $data        = [
//            'status_commission'  => self::COMMISSION_STATUS_SENT,
//            'locked_commission'  => 0,
//            'commission_sent_at' => Carbon::now()->toDateTimeString()
//        ];
//        return $this->strictUpdate($aConditions, $data);
        $iToStatus = $bFinished ? ManProject::COMMISSION_STATUS_SENT : ManProject::COMMISSION_STATUS_PARTIAL;
        $oCarbon = Carbon::now();
        $data = [
            'status_commission' => $this->status_commission = $iToStatus,
            'locked_commission' => 0,
            'commission_sent_at' => $this->commission_sent_at = $oCarbon->toDateTimeString(),
            'commission_sent_time' => $this->commission_sent_time = $oCarbon->timestamp
        ];
        if (!$bSucc = $this->strictUpdate($aConditions, $data)) {
            $this->commission_sent_at = $this->commission_sent_time = null;
            $this->status_commission = $this->original['status_commission'];
            $this->deleteCache($this->id);
        }
        return $bSucc;
    }

    /**
     * 加发送锁
     * @param bool $bForPrize 是否是派发奖金
     * @return bool
     */
    public function lock($bForPrize) {
        $sFunction = $bForPrize ? 'lockForSendPrize' : 'lockForSendCommission';
        return $this->$sFunction();
    }

    /**
     * 解发送锁
     * @param bool $bForPrize 是否是派发奖金
     * @return bool
     */
    public function unlock($bForPrize) {
        $sFunction = $bForPrize ? 'unlockForSendPrize' : 'unlockForSendCommission';
        return $this->$sFunction();
    }

    /**
     * 加奖金发送锁
     * @return bool
     */
    private function lockForSendPrize() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_prize' => ['=', self::PRIZE_STATUS_WAITING]
        ];
        $data = [
            'locked_prize' => $iThreadId = DbTool::getDbThreadId(),
            'status_prize' => self::PRIZE_STATUS_SENDING
        ];
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->status_prize = self::PRIZE_STATUS_SENDING;
            $this->locked_prize = $iThreadId;
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 加佣金发送锁
     * @return bool
     */
    private function lockForSendCommission() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_commission' => ['=', self::COMMISSION_STATUS_WAITING]
        ];
        $data = [
            'status_commission' => $iThreadId = DbTool::getDbThreadId(),
            'status_commission' => self::COMMISSION_STATUS_SENDING
        ];
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->status_commission = self::COMMISSION_STATUS_SENDING;
            $this->locked_commission = $iThreadId;
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 解奖金发送锁
     * @return bool
     */
    public function unlockForSendPrize() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_prize' => ['=', self::PRIZE_STATUS_SENDING],
            'locked_prize' => $this->locked_prize
        ];
        $data = [
            'locked_prize' => 0,
            'status_prize' => self::PRIZE_STATUS_WAITING
        ];
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->status_commission = self::PRIZE_STATUS_WAITING;
            $this->locked_commission = 0;
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 解佣金发送锁
     * @return bool
     */
    public function unlockForSendCommission() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status_commission' => ['=', self::COMMISSION_STATUS_SENDING],
            'locked_commission' => $this->locked_commission
        ];
        $data = [
            'locked_commission' => 0,
            'status_commission' => self::COMMISSION_STATUS_WAITING
        ];
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->locked_commission = 0;
            $this->status_commission = self::COMMISSION_STATUS_WAITING;
            $this->deleteCache();
        }
        return $bSucc;
    }

    /**
     * 更新注单所属的追号任务的奖金,及向队列增加任务
     * @param Trace & $oTrace 返回Trace对象
     * @return boolean
     */
    function updateTracePrize(& $oTrace) {
        if (empty($this->trace_id)) {
            return true;
        }
        if ($this->status != ManProject::STATUS_WON) {
            return true;
        }
        $oTrace = ManTrace::find($this->trace_id);
        return $oTrace->updatePrize($this->issue, $this->prize);
    }

    /**
     * 重新派发奖金
     * @return bool or self::ERRNO_PRIZE_SENDING
     */
    public function setPrizeTask() {
        if ($bNeedLocker = $this->status_prize == self::PRIZE_STATUS_SENDING) {
            $aThreads = DbTool::getDbThreads();
            if (in_array($this->locked_prize, $aThreads)) {
                return self::ERRNO_PRIZE_SENDING;
            }
        }
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['in', [self::STATUS_WON, self::STATUS_LOST]],
            'status_prize' => ['in', [self::PRIZE_STATUS_WAITING, self::PRIZE_STATUS_SENDING]],
        ];
        !$bNeedLocker or $aConditions['locked_prize'] = ['=', $this->locked_prize];
        $data = [
            'status_prize' => self::PRIZE_STATUS_WAITING,
            'locked_prize' => 0
        ];
        if ($bSucc = self::doWhere($aConditions)->update($data) > 0) {
            $this->deleteCache();
            $aJobData = [
                'type' => 'prize',
                'projects' => [ $this->id],
            ];
            $bSucc = BaseTask::addTask('SendMoney', $aJobData, 'send_money');
//            for ($i = 0,$bSucc = false; $i < 10; $i++){
//                if ($bSucc = Queue::push('SendMoney',$aJobData,Config::get('schedule.send_money')) > 0){
//                    break;
//                }
//            }
        }
        return $bSucc;
    }

    /**
     * 重新派发佣金
     * @return bool or self::ERRNO_COMMISSION_SENDING
     */
    public function setCommissionTask() {
        if ($bNeedLocker = $this->status_commission == self::COMMISSION_STATUS_SENDING) {
            $aThreads = DbTool::getDbThreads();
            if (in_array($this->locked_commission, $aThreads)) {
                return self::ERRNO_COMMISSION_SENDING;
            }
        }
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['in', [self::STATUS_WON, self::STATUS_LOST]],
            'status_commission' => ['in', [self::COMMISSION_STATUS_WAITING, self::COMMISSION_STATUS_SENDING]],
        ];
        !$bNeedLocker or $aConditions['locked_commission'] = ['=', $this->locked_commission];
        $data = [
            'status_commission' => self::COMMISSION_STATUS_WAITING,
            'locked_commission' => 0
        ];
        if ($bSucc = self::doWhere($aConditions)->update($data) > 0) {
            $this->deleteCache();
            $aJobData = [
                'type' => 'commission',
                'projects' => [ $this->id],
            ];
            $bSucc = BaseTask::addTask('SendMoney', $aJobData, 'send_money');
//            for ($i = 0,$bSucc = false; $i < 10; $i++){
//                if ($bSucc = Queue::push('SendMoney',$aJobData,Config::get('schedule.send_money')) > 0){
//                    break;
//                }
//            }
        }
        return $bSucc;
    }

    public function getTransactions() {
        return Transaction::where('project_id', '=', $this->id)->get();
    }

    /**
     * 将注单恢复至待计奖状态
     * @return bool
     */
    public function reset() {
        $aConditions = [
            'id' => ['=', $this->id],
            'status' => ['=', self::STATUS_WON],
            'status_prize' => ['=', self::PRIZE_STATUS_SENT]
        ];
        $data = [
            'status' => self::STATUS_NORMAL,
            'prize' => null,
            'status_prize' => self::PRIZE_STATUS_WAITING,
        ];
        if ($bSucc = $this->doWhere($aConditions)->update($data) > 0) {
            $this->deleteCache();
        }
        return $bSucc;
    }

    public static function & getTraceIdArrayOfDroped($iLotteryId, $sIssue) {
        $aConditions = [
            'lottery_id' => ['=', $iLotteryId],
            'issue' => ['=', $sIssue],
            'status' => ['=', self::STATUS_DROPED],
            'trace_id' => ['<>', null]
        ];
//        pr($aConditions);
        $i = 0;
        $ps = 200;
        $aTraces = [];
        do {
            $iOffset = $ps * $i++;
            $oProjects = self::doWhere($aConditions)->offset($iOffset)->limit($ps)->get(['id', 'trace_id']);
            foreach ($oProjects as $oProject) {
                $aTraces[] = $oProject->trace_id;
            }
        } while ($oProjects->count());
        return $aTraces;
    }

    protected function getStatusPrizeFormattedAttribute() {
        return $this->attributes['status'] == self::STATUS_WON ? (__('_project.' . strtolower(Str::slug(static::$prizeStatuses[$this->attributes['status_prize']])))) : null;
    }

    protected function getStatusCommissionFormattedAttribute() {
        return __('_project.' . strtolower(Str::slug(static::$commissionStatuses[$this->attributes['status_commission']])));
    }

    protected function getDisplayBetNumberShortAttribute() {
        return mb_strlen($this->attributes['display_bet_number']) > 10 ? mb_substr($this->attributes['display_bet_number'], 0, 10) . '...' : $this->attributes['display_bet_number'];
    }

    protected function getdisplayBetNumberForViewAttribute() {
        $iWidthScreen = 120;
        if (strlen($this->attributes['display_bet_number']) > $iWidthScreen) {
            $sSplitChar = Config::get('bet.split_char');
            $aNumbers = explode($sSplitChar, $this->attributes['display_bet_number']);
            $iWidthBetNumber = strlen($aNumbers[0]);
            $aMultiArray = array_chunk($aNumbers, intval($iWidthScreen / $iWidthBetNumber));
            $aText = [];
            foreach ($aMultiArray as $aNumberArray) {
                $aText[] = implode($sSplitChar, $aNumberArray);
            }
            return implode('<br />', $aText);
        } else {
            return $this->attributes['display_bet_number'];
        }
    }

    protected function getFormattedIsTesterAttribute() {
        if ($this->attributes['is_tester'] !== null) {
            return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
        } else {
            return '';
        }
    }

    public static function getBoughtUserCount($sBeginDate, $sEndDate = null) {
        $sEndDate or $sEndDate = "$sBeginDate 23:59:59";
        $sSql = "select count(distinct user_id) count from projects where bought_at between '$sBeginDate' and '$sEndDate' and is_tester = 0";
        $aResults = DB::select($sSql);
        return $aResults[0]->count ? $aResults[0]->count : 0;
    }

}
