<?php

class Account extends BaseModel {

    const ERRNO_LOCK_FAILED = -120;
    const RELEASE_DEAD_LOCK_NONE = 0;
    const RELEASE_DEAD_LOCK_RUNNING = 1;
    const RELEASE_DEAD_LOCK_SUCCESS = 2;
    const RELEASE_DEAD_LOCK_FAILED = 3;

    public static $releaseDeadLockMessages = [
        self::RELEASE_DEAD_LOCK_NONE => 'Unlocked',
        self::RELEASE_DEAD_LOCK_RUNNING => 'The Locker is Still Runing',
        self::RELEASE_DEAD_LOCK_SUCCESS => 'Released',
        self::RELEASE_DEAD_LOCK_FAILED => 'Unlock Failed!!!',
    ];
    public static $resourceName = 'Account';
    public static $amountAccuracy = 6;
//    public static $htmlNumberColumns = [
//        'balance'      => 6,
//        'available'    => 6,
//        'withdrawable' => 6,
//        'frozen'       => 6
//    ];
    public static $ignoreColumnsInView = [
        'id',
        'user_id',
        'locked',
        'status',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_tester',
        'balance',
        'frozen',
        'available',
        'withdrawable',
    ];
    public static $listColumnMaps = [
        'balance' => 'balance_formatted',
        'frozen' => 'frozen_formatted',
        'available' => 'available_formatted',
        'withdrawable' => 'withdrawable_formatted',
    ];
    public static $viewColumnMaps = [
        'balance' => 'balance_formatted',
        'frozen' => 'frozen_formatted',
        'available' => 'available_formatted',
        'withdrawable' => 'withdrawable_formatted',
    ];
    public static $totalColumns = [
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'prohibit_amount',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'username' => 'required',
        'balance' => 'numeric',
        'frozen' => 'numeric',
        'available' => 'numeric',
        'withdrawable' => 'numeric',
        'status' => 'required|integer',
        'locked' => 'integer',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'status',
        'locked',
    ];
    protected $table = 'accounts';

//    public static $columns = include(app_path() . '/lang/en/basic.php');

    protected function getBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('balance');
    }

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen');
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available');
    }

    /**
     * [getWithdrawableFormattedAttribute User's withdrawal money is the min value between available and withdrawal]
     * @return [Float] [Formatted number]
     */
    protected function getWithdrawableFormattedAttribute() {
        return number_format(min($this->attributes['withdrawable'], $this->attributes['available']), static::$amountAccuracy);
    }

    protected function getTrueWithdrawableAttribute() {
        return formatNumber(min($this->attributes['withdrawable'], $this->attributes['available']), static::$amountAccuracy);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getAccountInfoByUserId($mUserID, $aColunms = ['*']) {
        if (!$mUserID)
            return false;
        if (is_array($mUserID)) {
            return self::whereIn('user_id', $mUserID)->get($aColunms);
        } else {
            return self::where('user_id', '=', $mUserID)->first($aColunms);
        }
    }

    /**
     * 获取指定用户的可用余额
     * @param int $user_id
     * @return float
     */
    public static function getAvaliable($user_id) {
        $oAccount = self::getAccountInfoByUserId($user_id, ['available']);
        return number_format($oAccount->available, 6, '.', '');
    }

    public function checkBalance() {
        return $this->balance == number_format($this->frozen + $this->available, static::$amountAccuracy, '.', '');
    }

    /**
     * Lock Account
     * @param int $id
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lock($id, & $iLocker) {
        $iThreadId = DbTool::getDbThreadId();

        $iCount = self::where('id', '=', $id)->where('locked', '=', 0)->update(['locked' => $iThreadId]);

        if ($iCount > 0) {
            $iLocker = $iThreadId;
            return self::find($id);
        } else {
            self::addReleaseLockTask($id);
        }

        return false;
    }

    /**
     * Lock Account By User ID
     * @param int $iUserId
     * @param int $iLocker
     * @return Account|boolean
     */
    public static function lockByUserId($iUserId, & $iLocker) {
        $iThreadId = DbTool::getDbThreadId();
        $iCount = self::where('user_id', '=', $iUserId)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
        if ($iCount > 0) {
            $iLocker = $iThreadId;
            return self::where('user_id', '=', $iUserId)->get()->first();
        }
        return false;
    }

    /**
     * 一次性对多个账户加锁
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function lockManyOfUsers($aUserIds, & $iLocker) {
        if (empty($aUserIds)) {
            return false;
        }
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
//        pr($aUserIds);
//        pr($iCount);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = self::whereIn('user_id', $aUserIds)->where('locked', '=', 0)->update(['locked' => $iThreadId]);
//        pr($iLockedCount);
//        exit;
        if ($iLockedCount == $iCount) {
            $iLocker = $iThreadId;
            return self::whereIn('user_id', $aUserIds)->get();
        }
        return false;
    }

    /**
     * 一次性解锁多个账户
     * @param array $aUserIds
     * @param int $iLocker
     * @return boolean
     */
    public static function unlockManyOfUsers($aUserIds, $iLocker) {
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $iCount = count($aUserIds);
        $iThreadId = DbTool::getDbThreadId();
        $iLockedCount = self::whereIn('user_id', $aUserIds)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        return $iLockedCount == $iCount;
    }

    /**
     * Unlock Account
     * @param int $id
     * @param int $iLocker
     * @param bool $bReturnObject
     * @return Account|boolean
     */
    public static function unLock($id, & $iLocker, $bReturnObject = true) {
        if (empty($iLocker))
            return true;
        $iCount = self::where('id', '=', $id)->where('locked', '=', $iLocker)->update(['locked' => 0]);
        if ($iCount > 0) {
            $iLocker = 0;
            return $bReturnObject ? self::find($id) : true;
        }
        return false;
    }

    /**
     * [getLockedAccounts Get all locked accounts]
     * @return [Object Array] [Locked accounts array]
     */
    public static function getLockedAccounts() {
        return self::where('locked', '>', 0)->get(['id', 'locked']);
    }

    /**
     * 根据用户ID返回Account对象
     * @param int|array $mUserID
     * @return Collection|Account
     */
    public static function getUserIdsByAvailable($fFromAccount, $fToAccount) {
        if (!empty($fFromAccount) && !empty($fToAccount)) {
            $aConditions['available'] = [ 'between', [$fFromAccount, $fToAccount]];
        } else if (!empty($fFromAccount)) {
            $aConditions['available'] = [ '>=', $fFromAccount];
        } else if (!empty($fToAccount)) {
            $aConditions['available'] = [ '<=', $fToAccount];
        }
        $aUserIds = [];
        if (isset($aConditions)) {
            $aColumns = ['id', 'user_id'];
            $oQuery = self::doWhere($aConditions);
            $aAccounts = $oQuery->get($aColumns);
            foreach ($aAccounts as $oAccount) {
                $aUserIds[] = $oAccount->user_id;
            }
        }
        return $aUserIds;
    }

    /**
     * 获取实际可提现余额，即账户余额和可提现余额中较小的金额
     * @return float
     */
    public function getWithdrawableAmount() {
        return $this->available > $this->withdrawable ? $this->withdrawable : $this->available;
    }

    /**
     * 强制解锁，用于解开未及时解开的锁。
     * 强烈提示：本方法不检查加锁者是否是当前进程，因此，需特别小心！！
     * @param int $id
     * @param int $iLocker
     * @return int
     *      self;:RELEASE_DEAD_LOCK_NONE: 未锁定
     *      self;:RELEASE_DEAD_LOCK_RUNNING：加锁的进程仍在运行中
     *      self;:RELEASE_DEAD_LOCK_SUCCESS：解锁成功
     *      self::RELEASE_DEAD_LOCK_FAILED：解锁失败
     */
    public static function releaseDeadLock($id, $iLocker = null) {
        !is_null($iLocker) or $iLocker = self::getLocker($id);
        if (!$iLocker) {
            return self::RELEASE_DEAD_LOCK_NONE;
        }
        $aDbThreads = DbTool::getDbThreads();
        if (!in_array($iLocker, $aDbThreads)) {
            return self::unLock($id, $iLocker, false) ? self::RELEASE_DEAD_LOCK_SUCCESS : self::RELEASE_DEAD_LOCK_FAILED;
        }
        return self::RELEASE_DEAD_LOCK_RUNNING;
    }

    public static function getTeamAccountInfo($parentId, $bIncludeSelf = FALSE) {
        if ($bIncludeSelf) {
            $sql = 'select sum(accounts.available) sum_available, sum(accounts.frozen) sum_frozen, sum(accounts.withdrawable) sum_withdrawable from accounts left join users  on users.id = accounts.user_id where find_in_set(?, forefather_ids) or users.id=?';
            $aResult = DB::select(DB::raw($sql), [$parentId, $parentId]);
        } else {
            $sql = 'select sum(accounts.available) sum_available, sum(accounts.frozen) sum_frozen, sum(accounts.withdrawable) sum_withdrawable from accounts left join users on users.id = accounts.user_id where find_in_set(?, forefather_ids)';
            $aResult = DB::select(DB::raw($sql), [$parentId]);
        }
        return $aResult;
    }

    /**
     * 返回加锁者
     * @param int $id
     * @return int | false
     */
    private static function getLocker($id) {
        if (empty($id)) {
            return false;
        }
        $oAccount = self::where('id', '=', $id)->get(['locked'])->first();
        return is_object($oAccount) ? $oAccount->getAttribute('locked') : false;
    }

    /**
     * 向队列增加解锁任务
     * @param int $id
     * @return bool
     */
    public static function addReleaseLockTask($id) {
        return BaseTask::addTask('ReleaseDeadAccountLock', ['id' => $id], 'account');
    }

    public function setWithdrawable($fAddAmount) {
        $this->withdrawable += $fAddAmount;
        return $this->save();
    }

    public function setProhibitAmount($fAddAmount) {
        $this->prohibit_amount += $fAddAmount;
        return $this->save();
    }

}
