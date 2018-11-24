<?php

/**
 * Class ActivityUserPrize - 活动规则类表
 *
 */
class ActivityUserPrize extends BaseModel implements FactoryObjectClassInterface {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */

    /**
     * 暂时不能使用缓存
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    const SOURCE_TASK_SYSTEM = 1;
    const SOURCE_LOTTERY_SYSTEM = 2;
    const SOURCE_COMMAND = 3;
    const SOURCE_MANUAL = 4;
    const STATUS_NO_SEND = 0;
    const STATUS_SENT = 2;
    const STATUS_ACCEPTED = 3;
    const STATUS_VERIRIED = 4;
    const STATUS_REJECT = 5;
    const STATUS_RECEVIED = 6;

    public static $resourceName = 'ActivityUserPrize';
    protected $table = 'activity_user_prizes';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'activity_id',
        'prize_id',
        'user_id',
        'username',
        'source',
        'count',
        'status',
        'is_verified',
        'remote_ip',
//        'prize_category',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'activity_name',
        'username',
        'is_tester',
        'prize_name',
//        'prize_category',
        'status',
        'accepter',
        'auditor',
        'remote_ip',
        'accepted_at',
        'audited_at',
        'created_at',
    ];
    public static $ignoreColumnsInView = [
        'id',
        'activity_id',
        'prize_id',
        'user_id',
        'updated_at',
        'accepter_id',
        'auditor_id',
        'source',
    ];
    public $orderColumns = [
        'id' => 'desc'
    ];
    public static $aStatus = [
        self::STATUS_NO_SEND => 'not-send',
        self::STATUS_SENT => 'sent',
        self::STATUS_ACCEPTED => 'accepted',
        self::STATUS_VERIRIED => 'verified',
        self::STATUS_REJECT => 'reject',
        self::STATUS_RECEVIED => 'received',
    ];
    public static $listColumnMaps = [
        // 'account_available' => 'account_available_formatted',
        'status' => 'status_formmatted',
        'is_tester' => 'friendly_is_tester',
    ];
    public static $viewColumnMaps = [
        'status' => 'status_formmatted',
        'is_tester' => 'friendly_is_tester',
    ];
    public static $aSources = [
        self::SOURCE_TASK_SYSTEM => 'task-system',
        self::SOURCE_LOTTERY_SYSTEM => 'lottery-system',
        self::SOURCE_COMMAND => 'command-system',
        self::SOURCE_MANUAL => 'manual-system',
    ];
    public static $ignoreColumnsInEdit = ['activity_name', 'username', 'prize_name', 'remote_ip', 'status', 'source', 'activity_id'];
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required|between:1,16',
        'activity_id' => 'required|integer',
        'activity_name' => 'required|between:1,50',
        'prize_id' => 'required|integer',
        'prize_name' => 'required|between:1,50',
        'source' => 'in:1,2,3,4',
        'status' => 'in:0,2,3,4,5,6',
        'remote_ip' => 'ip',
    ];
    public static $htmlSelectColumns = [
        'source' => 'aSources',
        'status' => 'aStatuses',
        'prize_id' => 'aPrizes',
        'user_id' => 'aUser',
    ];

    /**
     * 活动信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity() {
        return $this->belongsTo('Activity', 'activity_id', 'id');
    }

    /**
     * 活动奖品信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prize() {
        return $this->belongsTo('ActivityPrize', 'prize_id', 'id');
    }

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    /**
     * 活动是否有效
     *
     * @return bool
     */
    public function isValidateActivity() {
        $now = date('Y-m-d H:i:s');
        if ($this->start_time <= $now && $this->end_time >= $now) {
            return true;
        }
        return false;
    }

    /**
     * 获得商品价值
     *
     * @param $value
     */
    public function getValueAttribute($value) {
        $prize = $this->prize()->remember(5)->first();

        return ($prize) ? $prize->value * $this->count : 0;
    }

    /**
     *  完成
     *
     * @return bool
     */
    public function completed() {
        $this->status = 2;
        return $this->save();
    }

    /**
     * 受理申请成功
     */
    public function setAccected($iAdminUserId) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $aExtraData = [
            'accepter_id' => $iAdminUserId,
            'accepter' => $oAdminUser->username,
            'accepted_at' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_ACCEPTED,
        ];
        return self::where('id', '=', $this->id)->whereIn('status', [self::STATUS_NO_SEND])->update($aExtraData) > 0;
    }

    /**
     * 审核通过
     */
    public function setToVerified($iAdminUserId, $aExtraInfo = []) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $aExtraData = [
            'auditor_id' => $iAdminUserId,
            'auditor' => $oAdminUser->username,
            'audited_at' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_VERIRIED,
        ];
        $aExtraData = array_merge($aExtraData, $aExtraInfo);
        $this->flushAvailableHBCount();
        $this->flushAvailabelTotalHBAmount();
        return self::where('id', '=', $this->id)->whereIn('status', [self::STATUS_ACCEPTED])->update($aExtraData) > 0;
    }

    public function flushAllCount() {
        $this->flushAvailableHBCount();
        $this->flushReceivedHBCount();
        $this->flushAvailabelTotalHBAmount();
    }

    public function flushAvailableHBCount() {
        $key = self::createAvailableHBCountCacheKey($this->user_id, self::$aStatus[self::STATUS_VERIRIED]);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

    public function flushReceivedHBCount() {
        $key = self::createAvailableHBCountCacheKey($this->user_id, self::$aStatus[self::STATUS_SENT]);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

    public function flushAvailabelTotalHBAmount() {
        $key = self::createAvailableTotalHBAmountCacheKey($this->user_id, self::$aStatus[self::STATUS_SENT]);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

    /**
     * 审核通过
     */
    public function setToReject($iAdminUserId, $aExtraInfo = []) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $aExtraData = [
            'auditor_id' => $iAdminUserId,
            'auditor' => $oAdminUser->username,
            'audited_at' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_REJECT,
        ];
        $aExtraData = array_merge($aExtraData, $aExtraInfo);
        return self::where('id', '=', $this->id)->whereIn('status', [self::STATUS_ACCEPTED])->update($aExtraData) > 0;
    }

    /**
     *
     * 扩展参数修饰器
     *
     * @param $value
     * @return array
     */
    public function getDatasAttribute($value) {
        return (array) @json_decode($this->attributes['data']);
    }

    /**
     * 扩展参数修改器
     *
     * @param $value
     */
    public function setDatasAttribute($value) {
        $data = $this->datas;
        $data = array_merge($data, $value);

        $this->attributes['data'] = json_encode($data);
    }

    /**
     * 获得第day天的数据
     *
     */
    public function getTurnoverDay($day = 1) {
        $date = date('Y-m-d', strtotime($this->created_at . " +{$day} Day"));

        return UserProfit::getUserTotalTurnover($date, $date, $this->user_id);
    }

    /**
     * 获得返现金额
     *
     * @return float|int
     */
    public function getMoneyback() {
        $total = $this->getTurnoverDay(1) + $this->getTurnoverDay(2);

        $num = 0;
        switch ($this->prize_id) {
            case 10:
                $num = 0.2;
                break;
            case 11:
                $num = 0.1;
                break;
        }


        return $num * $total;
    }

    /**
     * 验证之前操作
     *
     * @return bool
     */
    protected function beforeValidate() {
        $oPrize = $this->prize()->first();
        $this->activity_id = $oPrize->activity_id;
        $this->activity_name = $oPrize->activity_name;
        $this->prize_name = $oPrize->name;
        $oUser = User::find($this->user_id);
        Log::info($this->user_id);
        Log::info($oUser);
        $this->username = $oUser->username;
        $this->is_tester = $oUser->is_tester;

        //如果没有IP,则默认读取用户登陆IP
        if (!$this->remote_ip) {
            $this->remote_ip = $oUser->login_ip;
        }
        if (!$this->source) {
            $this->source = self::SOURCE_MANUAL;
        }
        if (!$this->count) {
            $this->count = 1;
        }
        if (!$this->status) {
            $this->status = $oPrize->need_review ? ActivityUserPrize::STATUS_NO_SEND : ActivityUserPrize::STATUS_VERIRIED;
        }
        if (!$this->expire_at) {
            $this->expired_at = date('Y-m-d H:i:s', strtotime('+7 days'));
        }
        if ($oPrize->need_get && is_null($this->data)) {
            $aParam = json_decode($oPrize->params, true);
            if (key_exists('amount_column', $aParam)) {
                $this->data = json_encode([$aParam['amount_column'] => $oPrize->value]);
            }
        }
        return parent::beforeValidate();
    }

    public static function getUserPrizesByUserIdAndPrizeId($iUserId = null, $iPrizeId, $sDate = null) {
        if ($iPrizeId != null) {
            $oQuery = self::where('created_at', 'like', $sDate . '%');
        }
        $aConditions = [];
        if ($iUserId != null) {
            $aConditions['user_id'] = ['=', $iUserId];
        }
        if ($iPrizeId != null) {
            $aConditions['prize_id'] = ['=', $iPrizeId];
        }
        $aData = self::doWhere($aConditions, $oQuery)->get();
        return $aData;
    }

    /**
     * 获取用户所有已中奖数据
     * @param User $oUser 用户对象
     * @param Activity $oActivity 活动对象
     * @param int $iSource 指定奖品来源（默认NULL查全部）
     * @return ActivityUserPrize[] 用户活动奖品对象列表
     */
    public static function findUserzPrizes(User $oUser, Activity $oActivity, $iSource = null) {
        if (!$oUser || !$oActivity) {
            return false;
        }
        $oQuery = self::where('user_id', '=', $oUser->id)
                ->where('activity_id', '=', $oActivity->id);
        if (in_array($iSource, [self::SOURCE_TASK_SYSTEM, self::SOURCE_LOTTERY_SYSTEM])) {
            $oQuery->where('source', '=', $iSource);
        }
        return $oQuery->get();
    }

    /**
     * 设置奖品为发放状态
     * @param type $aExtraData
     * @return type
     */
    public function setToSent($aExtraData = []) {
        return self::where('id', '=', $this->id)->where('status', '=', self::STATUS_VERIRIED)->update($aExtraData) > 0;
    }

    public function addPrizeTask() {
        $aTaskData = [
            'id' => $this->id,
        ];
        return BaseTask::addTask('EventPrizeQueue', $aTaskData, 'activity');
    }

    public function getStatusFormmattedAttribute() {
        return __('_activityuserprize.' . self::$aStatus[$this->status]);
    }

    protected function getFriendlyIsTesterAttribute() {
        return yes_no(intval($this->is_tester));
    }

    /**
     * 获取可用红包个数
     * @param int $IUserId 用户id
     */
    public static function getReceivedHBCount($iUserId) {
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createAvailableHBCountCacheKey($iUserId, self::$aStatus[self::STATUS_SENT]);
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $oQuery = self::getActivityUserPrizes($iUserId, [self::STATUS_SENT, self::STATUS_RECEVIED]);
            return $oQuery->count();
        }
        $iUserBankCardCount = 0;
        if (!($iUserBankCardCount = Cache::get($key))) {
            $oQuery = self::getActivityUserPrizes($iUserId, [self::STATUS_SENT, self::STATUS_RECEVIED]);
            $iUserBankCardCount = $oQuery->count();
            if (static::$cacheMinutes) {
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $iUserBankCardCount);
            }
        } else {
            
        }
        return $iUserBankCardCount;
    }

    /**
     * 获取过期红包个数
     * @param int $IUserId 用户id
     */
    public static function getExpiredHBCount($iUserId) {
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createAvailableHBCountCacheKey($iUserId, 'expired');
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $oQuery = self::getExpiredActivityUserPrizes($iUserId, self::STATUS_VERIRIED);
            return $oQuery->count();
        }
        $iUserBankCardCount = 0;
        if (!($iUserBankCardCount = Cache::get($key))) {
            $oQuery = self::getExpiredActivityUserPrizes($iUserId, self::STATUS_VERIRIED);
            $iUserBankCardCount = $oQuery->count();
            if (static::$cacheMinutes) {
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $iUserBankCardCount);
            }
        }
        return $iUserBankCardCount;
    }

    /**
     * 获取红包个数
     * @param int $IUserId 用户id
     */
    public static function getAvailableHBCount($iUserId) {
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createAvailableHBCountCacheKey($iUserId, self::$aStatus[self::STATUS_VERIRIED]);
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $oQuery = self::getActivityUserPrizes($iUserId, [self::STATUS_VERIRIED]);
            return $oQuery->count();
        }
        $iUserBankCardCount = 0;
        if (!($iHBCount = Cache::get($key))) {
            $oQuery = self::getActivityUserPrizes($iUserId, [self::STATUS_VERIRIED]);
            $iHBCount = $oQuery->count();
            if (static::$cacheMinutes) {
                Cache::put($key, $iHBCount, static::$cacheMinutes);
            } else {
                Cache::forever($key, $iHBCount);
            }
        }
        return $iHBCount;
    }

    public function getSimpleExpiredAtAttribute() {
        return date('m/d', strtotime($this->expired_at));
    }

    /**
     * 获取可领取红包总额
     * @param int $IUserId 用户id
     */
    public static function getAvailableHBTotalAmount($iUserId) {
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createAvailableTotalHBAmountCacheKey($iUserId, 'available');
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return self::getHBTotalAmount();
        }
        $fTotalAmount = self::getHBTotalAmount($iUserId);
        if (!($iUserBankCardCount = Cache::get($key))) {
            if (static::$cacheMinutes) {
                Cache::put($key, $data, static::$cacheMinutes);
            } else {
                Cache::forever($key, $fTotalAmount);
            }
        }
        return $fTotalAmount;
    }

    public static function getHBTotalAmount($iUserId) {
        $oQuery = self::getActivityUserPrizes($iUserId, [self::STATUS_VERIRIED]);
        $fTotalAmount = 0;
        $aResult = $oQuery->get(['data']);
        foreach ($aResult as $data) {
            $aData = json_decode($data['data'], true);
            $fTotalAmount+=$aData['rebate_amount'];
        }
        return $fTotalAmount;
    }

    protected static function getActivityUserPrizes($iUserId, $aStatus) {
        $oQuery = ActivityUserPrize::where('user_id', '=', $iUserId)->whereIn('status', $aStatus);
        return $oQuery;
    }

    protected static function getExpiredActivityUserPrizes($iUserId, $iStatus) {
        $oQuery = self::where('user_id', '=', $iUserId)->where('status', '=', self::STATUS_VERIRIED)->where('expired_at', '<', date('Y-m-d H:i:s'));
        return $oQuery;
    }

    public static function createAvailableHBCountCacheKey($iUserId, $sStatus) {
        $sPrefix = self::getCachePrefix();
        return $sPrefix . $sStatus . '-hb-count' . $iUserId;
    }

    public static function createAvailableTotalHBAmountCacheKey($iUserId, $sStatus) {
        $sPrefix = self::getCachePrefix();
        return $sPrefix . $sStatus . 'total-hb-amount' . $iUserId;
    }

    /**
     * run after save
     * @param $bSucc
     * @param $bNew
     * @return boolean
     */
    protected function afterSave($oSavedModel) {
        $oPrize = $oSavedModel->prize()->first();
        if (!$oPrize->need_get && !$oPrize->need_review) {
            $oSavedModel->addPrizeTask();
        }
        return true;
    }

    public static function getUserPrizeByDate($sBeginTime, $sEndTime) {
        return self::where('created_at', '>=', $sBeginTime)->where('created_at', '<=', $sEndTime)->get();
    }

    /**
     * 向后台任务队列增加任务
     * @param boolean $bPlus
     */
    public function addPromoBonusTask() {
        $oPrize = ActivityPrize::find($this->prize_id);
        $aPrizeData = json_decode($oPrize->params, true);
        $aUserPrizeData = json_decode($this->data, true);

        $fAmount = array_get($aUserPrizeData, array_get($aPrizeData, 'amount_column'));
        $fAmount != null or $fAmount = $oPrize->value;
        return self::addPromoTask(date('Y-m-d'), $this->user_id, $fAmount);
    }

    public static function addPromoTask($sDate, $iUserId, $fAmount) {
        $aData = [
            'type' => 'bonus',
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => $sDate,
        ];
        return BaseTask::addTask('StatUpdateProfit', $aData, 'stat');
    }

}
