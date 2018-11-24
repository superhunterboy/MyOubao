<?php

/**
 * 
 * 活动类
 *
 */
class UserActivityController extends UserBaseController {

    protected $resourceView = 'events.activity';
    protected $modelName = 'Activity';

    /**
     * 错误：活动不可用
     */
    const ERROR_ACTIVITY_NOT_AVAILABLE = 1;

    /**
     * 抽奖错误：无可用抽奖次数
     */
    const ERROR_LOTTERY_NO_MORE_CHANCES = 2;

    /**
     * 抽奖错误：已达每日中奖上限
     */
    const ERROR_LOTTERY_PRIZE_LIMIT_DAILY = 3;

    /**
     * 抽奖错误：上锁失败
     */
    const ERROR_LOTTERY_LOCK_FAIL = 4;

    /**
     * 抽奖错误：保存／更新数据失败
     */
    const ERROR_LOTTERY_SAVE_DATA_FAIL = 5;

    /**
     * 抽奖消息：未中奖
     */
    const MESSAGE_LOTTERY_NO_PRIZE = 6;

    /**
     * 抽奖消息：中奖
     */
    const MESSAGE_LOTTERY_GET_PRIZE = 7;

    /**
     * 错误提示信息
     * @var type 
     */
    private $aErrorMessages = [
        self::ERROR_ACTIVITY_NOT_AVAILABLE => '活动不存在',
        self::ERROR_LOTTERY_NO_MORE_CHANCES => '无可用抽奖次数',
        self::ERROR_LOTTERY_PRIZE_LIMIT_DAILY => '未中奖',
        self::ERROR_LOTTERY_LOCK_FAIL => '系统繁忙，请稍后再试',
        self::ERROR_LOTTERY_SAVE_DATA_FAIL => '系统错误，请联系客服',
        self::MESSAGE_LOTTERY_NO_PRIZE => '未中奖',
        self::MESSAGE_LOTTERY_GET_PRIZE => '恭喜，中奖了',
    ];

    /**
     * 判断是否完成条件
     *
     */
    public function isCompleteCondition() {
        //条件ID
        $condition_id = trim(Input::get('condition_id'));
        //用户ID
        $user_id = Session::get('user_id');

        //1:获取到任务
        $data = ActivityUserCondition::firstByAttributes(array(
                    'user_id' => $user_id,
                    'condition_id' => $condition_id,
        ));

        if (!empty($data) && $data['status'] == 1) {
            return $this->endJsonMsg(1, '您已完成该条件!');
        }

        return $this->endJsonMsg(-1, '您已完成该条件!');
    }

    /**
     * 判断是否完成任务
     *
     */
    public function isCompleteTask() {
        //任务ID
        $task_id = trim(Input::get('task_id'));
        //用户ID
        $user_id = Session::get('user_id');

        $data = ActivityUserTask::firstByAttributes(array(
                    'user_id' => $user_id,
                    'task_id' => $task_id,
        ));

        if (!empty($data) && $data['status'] == 1) {
            return $this->endJsonMsg(1, '您已完成该任务!');
        }

        return $this->endJsonMsg(-1, '您暂未完成该任务!');
    }

    /**
     * 领取任务奖品
     *
     * @note 暂时没有需要手动领取的奖品,后期考虑
     */
    public function receiveTask() {
        
    }

    /**
     * 报名
     *
     * @note: 其实报名这一块也有可能会有报名条件.目前活动暂时没有,后期再考虑这一块
     * 这里有互斥条件...
     * 时间来不及的话,就单独控制一下
     *
     */
    public function apply() {
        //任务ID
        $task_id = trim(Input::get('task_id'));
        //用户ID
        $user_id = Session::get('user_id');

        $task = ActivityTask::find($task_id);

        if ($task['need_apply'] == 0) {
            return $this->endJsonMsg(-1, '该活动不需要报名!');
        }

        if (!$task->isValidateTask()) {
            return $this->endJsonMsg(-4, '该任务无效或活动已过期!');
        }

        $userTask = ActivityUserTask::firstOrNew(array(
                    'task_id' => $task_id,
                    'user_id' => $user_id,
        ));

        if ($userTask['status'] == 1) {
            return $this->endJsonMsg(-2, '您已经报过名了,无需重复报名!');
        }

        //此处强制做限制了,以后优化

        $userTask->status = 1;
        if ($userTask->save()) {
            return $this->endJsonMsg(1, '报名成功');
        }

        return $this->endJsonMsg(-3, '未知错误');
    }

    /* public function apply()
      {
      //任务ID
      $task_id = trim(Input::get('task_id'));
      //用户ID
      $user_id = Session::get('user_id');

      $task    = ActivityTask::find($task_id);

      if ($task['need_apply'] == 0)
      {
      return $this->endJsonMsg(-1, '该活动不需要报名!');
      }

      if (!$task->isValidateTask())
      {
      return $this->endJsonMsg(-4, '该任务无效或活动已过期!');
      }

      $userTask   = ActivityUserTask::firstOrNew(array(
      'task_id'=>$task_id,
      'user_id'=>$user_id,
      ));

      if ($userTask['status'] == 1)
      {
      return $this->endJsonMsg(-2, '您已经报过名了,无需重复报名!');
      }

      //此处强制做限制了,以后优化

      $userTask->status   = 1;
      if ($userTask->save())
      {
      return $this->endJsonMsg(1, '报名成功');
      }

      return $this->endJsonMsg(-3, '未知错误');
      } */

    /**
     * 终止请求，回给请求方信息
     *
     * @param $code
     * @param $msg
     * @param $data
     * @return JsonResponse
     */
    private function endJsonMsg($code, $msg = '', $data = []) {
        if (!$msg && isset($this->aErrorMessages[$code])) { // 没有 指定输出信息时取配置信息
            $msg = $this->aErrorMessages[$code];
        }
        $msgs = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return Response::json($msgs);
    }

    /**
     * 幸运大抽奖
     * @param type $id 活动ID
     * @return type
     */
    public function luckyDraw($id) {
        $oUser = UserUser::find(Session::get('user_id'));
        $oActivity = Activity::find($id);
        $sNowTime = date('Y-m-d H:i:s');
        if (!$oActivity || $sNowTime < $oActivity->start_time || $sNowTime > $oActivity->end_time) { // 活动不存在，或不可用
            return $this->endJsonMsg(self::ERROR_ACTIVITY_NOT_AVAILABLE);
        }
        // 当前用户是否还有剩余抽奖次数
        $oActivityUserInfo = ActivityUserInfo::findUserInfo($oUser, $oActivity);
        if (!$oActivityUserInfo || $oActivityUserInfo->lottery_count <= 0) {
            return $this->endJsonMsg(self::ERROR_LOTTERY_NO_MORE_CHANCES); // 无可用抽奖次数
        }
        // 获取用户已中奖记录（每日中奖上限 & 每个奖品中奖上限）
        $aUserzPrizes = ActivityUserPrize::findUserzPrizes($oUser, $oActivity, ActivityUserPrize::SOURCE_LOTTERY_SYSTEM);
        $iUserGetToday = 0;
        $aUserTotalPrize = [];
        foreach ($aUserzPrizes as $oActivityUserPrize) {
            if (date('Y-m-d', strtotime($oActivityUserPrize->created_at)) == date('Y-m-d')) {
                $iUserGetToday++;
            }
            if (isset($aUserTotalPrize[$oActivityUserPrize->prize_id])) {
                $aUserTotalPrize[$oActivityUserPrize->prize_id] ++;
            } else {
                $aUserTotalPrize[$oActivityUserPrize->prize_id] = 1;
            }
        }
        $oUserExtraInfo = UserExtraInfo::findByUser($oUser);
        // 用户参与日志
        $aActivityUserLog = [
            'activity_id' => $oActivity->id,
            'activity_name' => $oActivity->name,
            'user_id' => $oUser->id,
            'username' => $oUser->username,
            'user_prize_today' => $iUserGetToday,
            'user_left_chance' => $oActivityUserInfo->lottery_count,
            'user_contribution' => $oUserExtraInfo->getAttribute('contribution') ? $oUserExtraInfo->contribution : 0,
            'action_time' => $sNowTime,
        ];
        $aRulesDetail = []; // 规则详细数据
        $oActivityUserLog = new ActivityUserLog($aActivityUserLog);
        // 是否达到每日中奖上限
        if ($oActivity->prize_limit > 0 && $iUserGetToday >= $oActivity->prize_limit) {
            $oActivityUserInfo->deductLoteryCount(); // 扣减用户抽奖机会

            $oActivityUserLog->user_left_chance = $oActivityUserInfo->lottery_count;
            $oActivityUserLog->status = ActivityUserLog::STATUS_NOT_GET_PRIZE;
            $oActivityUserLog->save(); // 保存日志
//            pr($oActivityUserLog->validationErrors->toArray());
//            pr($oActivityUserLog->toArray());
            return $this->endJsonMsg(self::ERROR_LOTTERY_PRIZE_LIMIT_DAILY); // 已达每日中奖上限
        }
        // 锁定所有规则
        if (!$iLocker = ActivityRule::lock($oActivity)) {
            return $this->endJsonMsg(self::ERROR_LOTTERY_LOCK_FAIL); // 上锁失败 
        }
        DB::connection()->beginTransaction(); // 开启事务
        $aActivityRules = ActivityRule::findAvailableRules($oActivity, $iLocker);
        // 逐个规则判定用户抽奖
        foreach ($aActivityRules as $k => $oActivityRule) {
            $aRulesDetail[$oActivityRule->id] = [
                'rule_id' => $oActivityRule->id,
                'prize_name' => $oActivityRule->prize_name,
                'left_count' => $oActivityRule->left_count,
                'user_prize_amount' => array_get($aUserTotalPrize, $oActivityRule->prize_id, 0),
            ];
            if ($oActivityRule->left_count <= 0) {
//                echo '#0 ';
                continue; // 奖品已派完
            }
            // 用户是否已达该规则奖品的中奖上限
            if (array_get($aUserTotalPrize, $oActivityRule->prize_id, 0) >= $oActivityRule->user_limit) {
//                echo '#1 ';
                continue; // 已达该奖品中奖上限
            }
            // 用户的贡献值是否满足该奖品
            if ($oActivityRule->contribution > 0 && (!$oUserExtraInfo || $oUserExtraInfo->contribution < $oActivityRule->contribution)) {
//                echo '#2 ';
                continue; // 不满足需要的贡献值
            }
            // -------------- 开始抽奖 -----------------
            /* @var $oActivityRulePrizeTime ActivityRulePrizeTime */
            if (!$oActivityRulePrizeTime = ActivityRulePrizeTime::findAvailableOne($oActivityRule)) {
//                echo '#3 ';
                continue; // 无可用奖品（如果出现了这种情况，需要检查数据表rules总数量与rules_prize_time总记录数是否吻合）
            }
            $aRulesDetail[$oActivityRule->id]['prize_rand_time'] = $oActivityRulePrizeTime->rand_time;
            if ($oActivityRule->type == ActivityRule::TYPE_PROBABILITY_LOTTERY) { // 概率抽奖
                // 取用户贡献作概率计算
                $fUserContribution = $oUserExtraInfo ? $oUserExtraInfo->contribution : 0;
                $iProbobility = $oActivityRule->calculateProbobility($fUserContribution) * 100;
                $iRand = mt_rand(1, 10000); // TODO 是否需要存储随机因素值
//                pr([$fUserContribution, $iProbobility, $iRand]);
                $aRulesDetail[$oActivityRule->id]['user_contribution'] = $fUserContribution;
                $aRulesDetail[$oActivityRule->id]['probobility'] = $iProbobility;
                $aRulesDetail[$oActivityRule->id]['rand'] = $iRand;
                if ($iRand > $iProbobility) { // 用户未中奖
                    continue;
                }
            } else if ($oActivityRule->type == ActivityRule::TYPE_TIME_LOTTERY) { // 时间抽奖
                // 可在此处对时间抽奖作特别处理
            }
            // -------------- 用户已中奖 -----------------
            if (!$oActivityRulePrizeTime->used($oUser)) { // 标记奖品已使用
                DB::connection()->rollback(); // 事务回滚
                ActivityRule::unlock($oActivity, $iLocker); // 解锁
                return $this->endJsonMsg(self::ERROR_LOTTERY_SAVE_DATA_FAIL); // 系统出错
            }
            if (!$this->_afterUserGetPrize($oUser, $oUserExtraInfo, $oActivityRule, $iLocker)) { // 用户中奖后续更新
                DB::connection()->rollback(); // 事务回滚
                ActivityRule::unlock($oActivity, $iLocker); // 解锁
                return $this->endJsonMsg(self::ERROR_LOTTERY_SAVE_DATA_FAIL); // 系统出错
            }
            $oActivityUserInfo->deductLoteryCount(); // 扣减用户抽奖机会
            DB::connection()->commit(); // 事务提交
            ActivityRule::unlock($oActivity, $iLocker); // 解锁
            $aPrizeData = [
                'prize_id' => $oActivityRule->prize_id,
                'prize_name' => $oActivityRule->prize_name,
                'prize_value' => $oActivityRule->prize_value,
            ];

            $oActivityUserLog->user_left_chance = $oActivityUserInfo->lottery_count;
            $oActivityUserLog->status = ActivityUserLog::STATUS_IS_GET_PRIZE;
            $oActivityUserLog->user_prize_today = $iUserGetToday + 1;
            $oActivityUserLog->user_contribution = $oUserExtraInfo->getAttribute('contribution') ? $oUserExtraInfo->contribution : 0;
            $oActivityUserLog->rules_detail = json_encode($aRulesDetail);
            $oActivityUserLog->save(); // 保存日志
//            pr($oActivityUserLog->validationErrors->toArray());
//            pr($oActivityUserLog->toArray());
            return $this->endJsonMsg(self::MESSAGE_LOTTERY_GET_PRIZE, '', $aPrizeData); // 抽奖成功
        }
        $oActivityUserInfo->deductLoteryCount(); // 扣减用户抽奖机会
        DB::connection()->commit(); // 事务提交
        ActivityRule::unlock($oActivity, $iLocker); // 解锁

        $oActivityUserLog->user_left_chance = $oActivityUserInfo->lottery_count;
        $oActivityUserLog->status = ActivityUserLog::STATUS_NOT_GET_PRIZE;
        $oActivityUserLog->rules_detail = json_encode($aRulesDetail);
        $oActivityUserLog->save(); // 保存日志
//        pr($oActivityUserLog->validationErrors->toArray());
//        pr($oActivityUserLog->toArray());
        return $this->endJsonMsg(self::MESSAGE_LOTTERY_NO_PRIZE); // 未中奖
    }

    /**
     * 用户抽奖中奖后的处理动作
     * @param User $oUser 参与活动的用户
     * @param UserExtraInfo $oUserExtraInfo 用户的扩展信息
     * @param ActivityRule $oActivityRule 当前中奖的规则
     * @param type $iLocker 锁定当前中奖规则的线程ID
     * @param type $aLogData 扩展日志数据
     * @return boolean 是否成功
     * @throws Exception
     */
    private function _afterUserGetPrize(User $oUser, UserExtraInfo $oUserExtraInfo, ActivityRule $oActivityRule, $iLocker, $aLogData = []) {
        try {
            // 扣除用户对应的贡献值
            if (!$oUserExtraInfo->deductContribution($oActivityRule->contribution_cost)) {
                throw new Exception('deductContribution failed');
            }
            // 添加用户中奖记录
            $aActivityUserPrize = [
                'activity_id' => $oActivityRule->activity_id,
                'prize_id' => $oActivityRule->prize_id,
                'user_id' => $oUser->id,
                'username' => $oUser->username,
                'source' => ActivityUserPrize::SOURCE_LOTTERY_SYSTEM,
                'count' => 1,
                'status' => ActivityUserPrize::STATUS_NO_SEND,
                'is_verified' => ActivityUserPrize::STATUS_VERIFIED,
                'remote_ip' => get_client_ip(),
            ];
            $oActivityUserPrize = new ActivityUserPrize($aActivityUserPrize);
            if (!$oActivityUserPrize->save()) {
                throw new Exception('save ActivityUserPrize failed');
            }
            // $oActivityRule对应奖品扣减
            if (!$oActivityRule->deduct($iLocker)) {
                throw new Exception('deduct ActivityRule.left_count failed');
            }
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
            return false;
        }
        return true;
    }

    /**
     * 每日投注
     * @return type
     */
    public function dailyBet() {
        // 获取今日有效投注额
        $sToday = date('Y-m-d');
        $fTodayTurnover = UserProfit::getUserTotalTurnover($sToday, $sToday, Session::get('user_id'));
        // 获取昨日有效投注额和奖金
        // todo 按照时间和奖品id查找对应奖品，如果没有就是0,如果有显示到前台
        $sYesterday = date('Y-m-d', strtotime("-1 day"));
        $fYesterdayTurnover = UserProfit::getUserTotalTurnover($sYesterday, $sYesterday, Session::get('user_id'));
        $fReward = 0;
        if ($fYesterdayTurnover >= 500 && $fYesterdayTurnover < 1000) {
            $fReward = 3;
        } else if ($fYesterdayTurnover >= 1000 && $fYesterdayTurnover < 2000) {
            $fReward = 5;
        } else if ($fYesterdayTurnover >= 2000 && $fYesterdayTurnover < 3000) {
            $fReward = 8;
        } else if ($fYesterdayTurnover >= 3000 && $fYesterdayTurnover < 5000) {
            $fReward = 10;
        } else if ($fYesterdayTurnover >= 5000 && $fYesterdayTurnover < 10000) {
            $fReward = 20;
        } else if ($fYesterdayTurnover >= 10000 && $fYesterdayTurnover < 30000) {
            $fReward = 30;
        } else if ($fYesterdayTurnover >= 30000 && $fYesterdayTurnover < 50000) {
            $fReward = 90;
        } else if ($fYesterdayTurnover >= 50000 && $fYesterdayTurnover < 100000) {
            $fReward = 150;
        } else if ($fYesterdayTurnover >= 100000 && $fYesterdayTurnover < 300000) {
            $fReward = 300;
        } else if ($fYesterdayTurnover >= 300000 && $fYesterdayTurnover < 600000) {
            $fReward = 1000;
        } else if ($fYesterdayTurnover >= 600000 && $fYesterdayTurnover < 1000000) {
            $fReward = 2000;
        } else if ($fYesterdayTurnover >= 1000000) {
            $fReward = 3000;
        }
        $this->setVars(compact('fYesterdayTurnover', 'fTodayTurnover', 'fReward'));
        return $this->render();
    }

    /**
     * 每日投注奖励
     */
    public function getDailyBetReward($fAmount) {
        $iPrizeId = ActivityPrize::PRIZE_DAILY_BET;
        $aPrizes = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(Session::get('user_id'), $iPrizeId, date('Y-m-d'));
        if (count($aPrizes) <= 0) {
            return $this->goBack('error', '昨日销量不满足要求，继续加油！！');
        }
        $oUserPrize = $aPrizes[0];
        $aPrizeData = json_decode($oUserPrize->data, true);
        if ($oUserPrize->status != ActivityUserPrize::STATUS_VERIRIED) {
            return $this->goBack('error', '奖励已成功领取！');
        }
        if ($fAmount > $aPrizeData['rebate_amount']) {
            return $this->goBack('error', '领取金额不正确，请领取指定金额的奖励');
        }
        if ($fAmount < $aPrizeData['rebate_amount']) {
            $aPrizeData['prize_amount'] = $fAmount;
        }
        $aExtraInfo = [
            'status' => ActivityUserPrize::STATUS_RECEVIED,
            'received_at' => date('Y-m-d H:i:s'),
            'data' => json_encode($aPrizeData),
        ];
        DB::connection()->beginTransaction();
        $bSucc = $oUserPrize->setToSent($aExtraInfo);
        !$bSucc or $bSucc = $oUserPrize->addPrizeTask();
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBack('success', '恭喜您，获取现金奖励：' . $fAmount . '元！！');
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', '很抱歉，获取奖励异常，请您联系客服！');
        }
    }

    /**
     * 每日签到
     * @return type
     */
    public function dailySignin() {
        // 获取今日有效投注额
        $sToday = date('Y-m-d');
        $fTodayTurnover = UserProfit::getUserTotalTurnover($sToday, $sToday, Session::get('user_id'));
        // 获取昨日有效投注额和奖金
        // todo 按照时间和奖品id查找对应奖品，如果没有就是0,如果有显示到前台
        $aActivityDailySign = ActivityDailySign::getLatestRecord(Session::get('user_id'));
        $fTotalTurnover = 0;
        $aDailyData = [];
//        $oYesterdaySign = ActivityDailySign::geYesterDayRecord(Session::get('user_id'));
//        if (is_object($oYesterdaySign)) {
        foreach ($aActivityDailySign as $oDailySign) {
            if($oDailySign->is_send==1)
                break;
            $fTotalTurnover += $oDailySign->turnover;
            $aDailyData[$oDailySign->day] = $oDailySign->sign_date;
            if ($oDailySign->day == 1) {
                break;
            }
        }
//        }
        $this->setVars(compact('fTodayTurnover', 'fTotalTurnover', 'aDailyData'));
        return $this->render();
    }

    /**
     * 每日签到
     */
    public function punchIn($iDay) {
        // 判断当日销量是否符合要求
        $sDate = date('Y-m-d');
        $fTurnover = UserProfit::getUserTotalTurnover($sDate, $sDate, Session::get('user_id'));
        if ($fTurnover < 1000) {
            return $this->goBack('error', '当日销量不满足要求，继续加油！！');
        }
        // 获取最后一次签到的日期
        $oDailySign = ActivityDailySign::getLastSign(Session::get('user_id'));
        if (is_object($oDailySign)) {
            if (strpos($oDailySign->sign_date, date('Y-m-d')) !== false) {
                return $this->goBack('error', '今日已签到！！');
            }
            $sYesterDay = date('Y-m-d', strtotime("-1 day"));
            if (strpos($oDailySign->sign_date, $sYesterDay) !== false && $oDailySign->day < 7) {
                $iDay = $oDailySign->day + 1;
            } else {
                $iDay = 1;
            }
        } else {
            $iDay = 1;
        }
        // 进行签到操作
        $oNewDailySign = new ActivityDailySign();
        $oNewDailySign->user_id = Session::get('user_id');
        $oNewDailySign->day = $iDay;
        $oNewDailySign->username = Session::get('username');
        $oNewDailySign->sign_date = date('Y-m-d H:i:s');
        $oNewDailySign->turnover = $fTurnover;
        $bSucc = $oNewDailySign->save();
        if ($bSucc) {
            return $this->goBack('success', '签到成功');
        } else {
            return $this->goBack('error', '签到失败，请稍后再次尝试');
        }
    }

    /**
     * 新人首充
     * @return type
     */
    public function newCharge() {
        $oUserPrize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(Session::get('user_id'), ActivityPrize::PRIZE_NEWER_DEPOSIT);
        $this->setVars('newChargePrize', $oUserPrize);
        // 获取今日有效投注额
        //$oFirstDeposit = UserDeposit::getFirstDepositByDate(Session::get('user_id'));
        $oFirstDeposit = Transaction::getUserFirstDeposit(Session::get('user_id'));
        if (is_object($oFirstDeposit)) {
            $iEndTime = strtotime($oFirstDeposit->created_at) + 48*3600;
	        $sEndTime = date('Y-m-d H:i:s', $iEndTime);
	        $fTurnover = Project::getCurrentDayTurnover2(Session::get('user_id'), $oFirstDeposit->created_at, $sEndTime);
        }else{
            $fTurnover = 0;
        }
        // 获取昨日有效投注额和奖金
        // todo 按照时间和奖品id查找对应奖品，如果没有就是0,如果有显示到前台
        $this->setVars(compact('fTurnover', 'oFirstDeposit'));
        return $this->render();
    }

    /**
     * 新人首充奖励
     */
    public function getNewDepositReward($fAmount) {
        $oUserPrize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(Session::get('user_id'), ActivityPrize::PRIZE_NEWER_DEPOSIT);
        if (count($oUserPrize) > 0) {
            return $this->goBack('error', '新人首充活动已完成！！');
        }
        //$oDailyFirstDeposit = UserDeposit::getFirstDepositByDate(Session::get('user_id'), 100);
        $oFirstDeposit = Transaction::getUserFirstDeposit(Session::get('user_id'));
        if (!is_object($oFirstDeposit)) {
            return $this->goBack('error', '新人首充没有达到最低要求，继续加油！！');
        }
        if ($oFirstDeposit->amount < 100) {
            return $this->goBack('error', '新人首充没有达到最低100元要求！！');
        }
        $oUser = User::find(Session::get('user_id'));
        $iTime = strtotime($oUser->register_at) + 24*7*3600;
        $sTime = date('Y-m-d H:i:s', $iTime);
        if($oFirstDeposit->created_at > $sTime)
        {
        	return $this->goBack('error', '注册日起7日内未充值的新用户为放弃此活动！！'); 
        }
        $iEndTime = strtotime($oFirstDeposit->created_at) + 48*3600;
        $sEndTime = date('Y-m-d H:i:s', $iEndTime);
        $fTurnover = Project::getCurrentDayTurnover2(Session::get('user_id'), $oFirstDeposit->created_at, $sEndTime);
        $fReward = 0;
        if ($fTurnover >= 500 && $fTurnover < 1000) {
            $fReward = 8;
        } else if ($fTurnover >= 1000 && $fTurnover < 2000) {
            $fReward = 18;
        } else if ($fTurnover >= 2000 && $fTurnover < 5800) {
            $fReward = 38;
        } else if ($fTurnover >= 5800 && $fTurnover < 8000) {
            $fReward = 58;
        } else if ($fTurnover >= 8000 && $fTurnover < 10000) {
            $fReward = 88;
        } else if ($fTurnover >= 10000 && $fTurnover < 15000) {
            $fReward = 118;
        } else if ($fTurnover >= 15000 && $fTurnover < 18000) {
            $fReward = 168;
        } else if ($fTurnover >= 18000) {
            $fReward = 218;
        }
        if ($fReward <= 0) {
            return $this->goBack('error', '销量不满足要求，继续加油！！');
        }
        $aExtraData = [
            'turnover' => $fTurnover,
            'deposit_amount' => $fAmount < $oFirstDeposit->amount ? $fAmount : $oFirstDeposit->amount,
            'rebate_amount' => $fReward,
        ];
        $oUserPrize = new ActivityUserPrize();
        $oPrize = ActivityPrize::find(ActivityPrize::PRIZE_NEWER_DEPOSIT);
        $oUserPrize->activity_id = 1;
        $oUserPrize->prize_id = $oPrize->id;
        is_null($aExtraData) or $oUserPrize->data = json_encode($aExtraData);
        $oUserPrize->count = 1;
        $oUserPrize->count = 1;
        $oUserPrize->user_id = Session::get('user_id');
        $oUserPrize->source = 1;
        $oUserPrize->status = $oPrize->need_review ? ActivityUserPrize::STATUS_NO_SEND : ActivityUserPrize::STATUS_VERIRIED;
        $bSucc = $oUserPrize->save();
        $aExtraInfo = [
            'status' => ActivityUserPrize::STATUS_RECEVIED,
            'received_at' => date('Y-m-d H:i:s'),
        ];
        DB::connection()->beginTransaction();
        !$bSucc or $bSucc = $oUserPrize->setToSent($aExtraInfo);
        !$bSucc or $bSucc = $oUserPrize->addPrizeTask();
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBack('success', '恭喜您，获取现金奖励：' . $fReward . '元！！');
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', '很抱歉，获取奖励异常，请您联系客服！');
        }
    }

    /**
     * 每日首充
     * @return type
     */
    public function dailyCharge() {
        // 获取今日首充金额
        //$oDailyFirstDeposit = UserDeposit::getFirstDepositByDate(Session::get('user_id'), 3888, date('Y-m-d'));
        $oDailyFirstDeposit = Transaction::getUserFirstDeposit(Session::get('user_id'),0,date("Y-m-d 00:00:00"));
        if (is_object($oDailyFirstDeposit)) {
            $firstDeposit = $oDailyFirstDeposit->amount;
        } else {
            $firstDeposit = 0;
        }
        // 获取昨日有效投注额和奖金
        // todo 按照时间和奖品id查找对应奖品，如果没有就是0,如果有显示到前台
        $sYesterday = date('Y-m-d', strtotime("-1 day"));
        $fYesterdayTurnover = UserProfit::getUserTotalTurnover($sYesterday, $sYesterday, Session::get('user_id'));
        $this->setVars(compact('firstDeposit', 'fTodayTurnover'));
        return $this->render();
    }

    /**
     * 每日首充奖励
     */
    public function getDailyDepositReward($fAmount) {
        $oUserPrize = ActivityUserPrize::getUserPrizesByUserIdAndPrizeId(Session::get('user_id'), ActivityPrize::PRIZE_DAILY_DEPOSIT, date('Y-m-d'));
        if (count($oUserPrize) > 0) {
            return $this->goBack('error', '今天首充奖励已领取！！');
        }
        //$oDailyFirstDeposit = UserDeposit::getFirstDepositByDate(Session::get('user_id'), 3888, date('Y-m-d'));
        $oDailyFirstDeposit = Transaction::getUserFirstDeposit(Session::get('user_id'),3888,date("Y-m-d 00:00:00"));
        if (!is_object($oDailyFirstDeposit)) {
            return $this->goBack('error', '今日首充没有达到最低要求，继续加油！！');
        }
        $fReward = 0;
        if ($oDailyFirstDeposit->amount >= 3888 && $oDailyFirstDeposit->amount < 18888) {
            $fReward = 8;
        } else if ($oDailyFirstDeposit->amount >= 18888 && $oDailyFirstDeposit->amount < 38888) {
            $fReward = 38;
        } else if ($oDailyFirstDeposit->amount >= 18888) {
            $fReward = 88;
        }
        $fStandardTurnover = $oDailyFirstDeposit->amount * 0.3;
        $fTurnover = Project::getCurrentDayTurnover2(Session::get('user_id'), $oDailyFirstDeposit->created_at, date('Y-m-d 23:59:59'));
        if ($fTurnover < $fStandardTurnover) {
            return $this->goBack('error', '今日销量不满足要求，继续加油！！');
        }
        $aExtraData = [
            'turnover' => $fTurnover,
            'deposit_amount' => $fAmount < $oDailyFirstDeposit->amount ? $fAmount : $oDailyFirstDeposit->amount,
            'rebate_amount' => $fReward,
        ];
        $model = new ActivityUserPrize();
        $oPrize = ActivityPrize::find(ActivityPrize::PRIZE_DAILY_DEPOSIT);
        $model->activity_id = 1;
        $model->prize_id = $oPrize->id;
        is_null($aExtraData) or $model->data = json_encode($aExtraData);
        $model->count = 1;
        $model->user_id = Session::get('user_id');
        $model->source = 1;
        $model->status = $oPrize->need_review ? ActivityUserPrize::STATUS_NO_SEND : ActivityUserPrize::STATUS_VERIRIED;
        $bSucc = $model->save();
        $aExtraInfo = [
            'status' => ActivityUserPrize::STATUS_RECEVIED,
            'received_at' => date('Y-m-d H:i:s'),
        ];
        DB::connection()->beginTransaction();
        !$bSucc or $bSucc = $model->setToSent($aExtraInfo);
        !$bSucc or $bSucc = $model->addPrizeTask();
        if ($bSucc) {
            DB::connection()->commit();
            return $this->goBack('success', '恭喜您，获取现金奖励：' . $fReward . '元！！');
        } else {
            DB::connection()->rollback();
            return $this->goBack('error', '很抱歉，获取奖励异常，请您联系客服！');
        }
    }

}
