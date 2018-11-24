<?php

class Withdrawal extends BaseModel {

    protected $table = 'withdrawals';
    public static $amountAccuracy = 2;
    public static $htmlNumberColumns = [
        'amount' => 2,
        'transaction_amount' => 2,
        'transaction_charge' => 2,
    ];
    public static $totalColumns = [
        'amount',
        'transaction_charge',
        'transaction_amount',
    ];
    public static $htmlOriginalNumberColumns = [
        'account'
    ];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'id',
        'serial_number',
        'user_id',
        'username',
        'is_tester',
        'request_time',
        'amount',
        'is_large',
        'account',
        'account_name',
        'province',
        'bank_id',
        'bank',
        'branch',
        'branch_address',
        'error_msg',
        'remark',
        'status',
        'auditor_id',
        'auditor',
        'verify_accepter_id',
        'verify_accepter',
        'verified_time',
        'finish_time',
        'transaction_charge',
        'transaction_amount',
    ];
    // const WITHDRAWAL_LIMIT_PER_DAY = 30;

    public static $resourceName = 'Withdrawal';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_tester',
        'request_time',
        'amount',
        'is_large',
//        'account',
//        'account_name',
        'bank',
        'province',
        'branch',
        'auditor',
        'verified_time',
//        'transaction_amount',
        'status',
        'serial_number',
        'verify_accepter',
        'withdrawal_accepter',
        'remittance_auditor',
    ];
    public static $listColumnMaps = [
        'account' => 'account_flag',
        'amount' => 'formatted_amount',
        'transaction_charge' => 'formatted_transaction_charge',
        'transaction_amount' => 'formatted_transaction_amount',
        'is_tester' => 'friendly_is_tester',
        'status' => 'formatted_status',
    ];
    public static $viewColumnMaps = [
//        'account' => 'formatted_account',
        'amount' => 'formatted_amount',
        'transaction_charge' => 'formatted_transaction_charge',
        'transaction_amount' => 'formatted_transaction_amount',
        'is_tester' => 'friendly_is_tester',
        'status' => 'formatted_status',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'validStatuses',
    ];

    //9.MC处理中. 10:MC异常返回
    const WITHDRAWAL_STATUS_NEW = 0; // 待审核
    const WITHDRAWAL_STATUS_RECEIVED = 1; //申请成功
    const WITHDRAWAL_STATUS_VERIFY_ACCEPTED = 2; //已受理审核
    const WITHDRAWAL_STATUS_REFUSE = 3; // 未通过审核（审核拒绝）
    const WITHDRAWAL_STATUS_VERIFIED = 4; // 审核通过，待处理
    const WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED = 5; //受理提现
    const WITHDRAWAL_STATUS_SUCCESS = 7; // 成功
    const WITHDRAWAL_STATUS_DEDUCT_SUCCESS = 8; //扣款成功
    const WITHDRAWAL_STATUS_REMITT_VERIFIED = 9; //汇款审核
    const WITHDRAWAL_STATUS_FAIL = 5; // 失败
    const WITHDRAWAL_STATUS_DEDUCT_FAIL = 6; // 扣游戏币失败
//    const WITHDRAWAL_STATUS_PART = 7; // mc部分成功，扣减部分游戏币
    const WITHDRAWAL_STATUS_REFUND = 8; // 已退游戏币（审核拒绝情况下）
    const WITHDRAWAL_STATUS_MC_PROCESSING = 9; //9.MC处理中
    const WITHDRAWAL_STATUS_MC_ERROR_RETURN = 10; //10:MC异常返回
    const WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL = 11; //10:MC异常返回

    public static $applyCanChangeStatus = [
        self::WITHDRAWAL_STATUS_NEW
    ];
    public static $manualCanChangeStatus = [
        self::WITHDRAWAL_STATUS_VERIFIED
    ];
    public static $statusWorkFlow = [
        '0' => [1, 2, 3],
        '1' => [2, 3],
        '2' => [4, 5, 6, 7, 8, 9, 10],
        '3' => [5, 8],
        '4' => [],
        '5' => [],
        '6' => [],
        '7' => [],
        '8' => [],
        '9' => [],
        '10' => [],
    ];
    public static $validStatuses = [
        self::WITHDRAWAL_STATUS_NEW => 'New',
        self::WITHDRAWAL_STATUS_RECEIVED => 'apply-received',
//        self::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM => 'Waiting-For-Confirmation',
        self::WITHDRAWAL_STATUS_VERIFY_ACCEPTED => 'verify-accepted',
        self::WITHDRAWAL_STATUS_REFUSE => 'Rejected',
        self::WITHDRAWAL_STATUS_VERIFIED => 'Verified',
        self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED => 'withdrawal-accepted',
        self::WITHDRAWAL_STATUS_SUCCESS => 'Success',
//        self::WITHDRAWAL_STATUS_FAIL => 'Failed',
//        self::WITHDRAWAL_STATUS_DEDUCT_FAIL => 'Deduct-Failture',
//        self::WITHDRAWAL_STATUS_PART => 'Part-Success',
        self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS => 'deduct-success',
        self::WITHDRAWAL_STATUS_REMITT_VERIFIED => 'remit-verified',
        self::WITHDRAWAL_STATUS_MC_ERROR_RETURN => 'MC-error-return',
        self::WITHDRAWAL_STATUS_MC_WITHDRAW_FAIL => 'MC create withdrawal failed',
    ];
    // 除审核通过及其后续状态外的其他状态
    public static $unVerifiedStatus = [
        self::WITHDRAWAL_STATUS_NEW,
        self::WITHDRAWAL_STATUS_RECEIVED,
        self::WITHDRAWAL_STATUS_VERIFY_ACCEPTED,
        self::WITHDRAWAL_STATUS_REFUSE,
    ];
    // 审核通过及其后续状态
    public static $verifiedStatus = [
        self::WITHDRAWAL_STATUS_VERIFIED,
        self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED,
        self::WITHDRAWAL_STATUS_SUCCESS,
        self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS,
    ];
    // 汇款审核及其后续状态
    public static $remitStatus = [
        self::WITHDRAWAL_STATUS_SUCCESS,
        self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS,
        self::WITHDRAWAL_STATUS_REMITT_VERIFIED,
    ];

    // 用某银行卡提现时，需要延后x个小时
    const WITHDRAWAL_TIME_LIMIT = 2;

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $rules = [
        'user_id' => 'required|integer',
        'serial_number' => 'between:1,25',
        'mownecum_order_num' => 'between:1,50',
        'request_time' => 'required|date',
        'amount' => 'required|integer',
        'is_large' => 'in:0,1',
        'province' => 'required|between:1,20',
        'branch' => 'required|between:1,50',
        'branch_address' => 'between:1,100',
        // 'account_id'    => 'required|integer',
        'account_name' => 'required|between:1,20',
        'account' => 'required|numeric',
        'bank_id' => 'required|integer',
        // 'bank'          => 'required|max:50',
        'error_msg' => 'max:50',
        'remark' => 'max:50',
        // 'status'        => 'in:0,1,2,3,4,5,6,7,8',
        'auditor_id' => 'integer',
        'auditor' => 'between:4,16',
        'verified_time' => 'date',
        'finish_time' => 'date',
        'transaction_charge' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'transaction_amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
    ];
    public static $aReportType = [
        ReportDownloadConfig::TYPE_WITHDRAWAL => 0,
        ReportDownloadConfig::TYPE_WITHDRAWAL_SUCCESS => [self::WITHDRAWAL_STATUS_SUCCESS, self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS, self::WITHDRAWAL_STATUS_REMITT_VERIFIED],
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = ['bank', 'username'];
    // 表单只读字段
    public static $aReadonlyInputs = ['province', 'branch', 'branch_address', 'account_name'];
    public static $ignoreColumnsInView = ['id', 'user_id', 'bank_id', 'auditor_id'];
    public static $ignoreColumnsInEdit = ['serial_number', 'request_time', 'finish_time', 'error_msg', 'remark', 'status', 'auditor_id', 'auditor', 'verified_time']; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)
    private $User;
    private $Account;

    /**
     * [getAccountHiddenAttribute 访问器方法, 生成只显示末尾4位的银行卡账号信息, 且每4位空格隔开]
     * @return [String]          [只显示末尾4位的银行卡账号信息,且每4位空格隔开]
     */
    protected function getAccountHiddenAttribute() {
        $str = str_repeat('*', (strlen($this->account) - 4));
        $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($this->account, -4);
        return $account_hidden;
    }

    /**
     * [getSerialNumberShortAttribute 获取序列号的截断格式]
     * @return [type] [4位序列号的截断格式]
     */
    protected function getSerialNumberShortAttribute() {
        return substr($this->serial_number, 0, 4) . '...';
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedStatusAttribute() {
        return __('_withdrawal.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的金额]
     * @return [type] [格式化后的金额]
     */
    protected function getFormattedAmountAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    /**
     * [getFormattedAccountAttribute 返回经格式化后的金额]
     * @return [type] [格式化后的金额]
     */
    protected function getFormattedAccountAttribute() {
        return '***' . substr($this->account, -4);
    }

    /**
     * [getFormattedAccountAttribute 返回经格式化后的金额]
     * @return [type] [格式化后的金额]
     */
    protected function getAccountFlagAttribute() {
        $oBankCard = BankCard::getObjectByParams(['account' => $this->account]);
        $sFlag = '';
        switch ($oBankCard->status) {
            case BankCard::STATUS_BLACK:
                $sFlag = 'black';
                break;
            case BankCard::STATUS_DELETED:
                $sFlag = 'delete';
                break;
        }
        return $this->account . ' ' . $sFlag;
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的实际提现金额]
     * @return [type] [格式化后的实际提现金额]
     */
    protected function getFormattedTransactionAmountAttribute() {
        return $this->getFormattedNumberForHtml('transaction_amount');
    }

    /**
     * [getFormattedAmountAttribute 返回经格式化后的手续费]
     * @return [type] [格式化后的手续费]
     */
    protected function getFormattedTransactionChargeAttribute() {
        return $this->getFormattedNumberForHtml('transaction_charge');
    }

    // public function getIgnoreColumnsForView()
    // {
    //     $aIgnoreColumnsForView = parent::getIgnoreColumnsForView();
    //     $aIgnoreColumnsForView = array_merge($aIgnoreColumnsForView, $this->ignoreColumnsInView);
    //     return $aIgnoreColumnsForView;
    // }
    // public function getIgnoreColumnsForEdit()
    // {
    //     $aIgnoreColumnsForEdit = parent::getIgnoreColumnsForEdit();
    //     $aIgnoreColumnsForEdit = array_merge($aIgnoreColumnsForEdit, $this->ignoreColumnsInEdit);
    //     return $aIgnoreColumnsForEdit;
    // }

    protected function beforeValidate() {
        if (!$this->exists) {
//            $this->status = self::WITHDRAWAL_STATUS_RECEIVED;
            $this->request_time = Carbon::now()->toDateTimeString();
        }
        $this->transaction_charge or $this->transaction_charge = 0;
        $this->transaction_amount or $this->transaction_amount = 0;
        return parent::beforeValidate();
    }

    /**
     * [_updateStatus 更新提现记录状态]
     * @param  [Int] $iFromStatus [未改变前的状态值, 默认为待审核状态]
     * @param  [Int] $iToStatus   [将要改变的状态值]
     * @param  [Array] $aExtraData  [额外需要更新的数据]
     * @return [Response]              [description]
     */
    private function _updateStatus($iFromStatus = self::WITHDRAWAL_STATUS_NEW, $iToStatus, array $aExtraData = []) {
        if (!$this->exists) {
            return false;
        }
        // TODO 判断从fromStatus到toStatus是否符合提现流程
        if (!$bSucc = $this->judgeProcess($iFromStatus, $iToStatus)) {
            return false;
        }
        $aExtraData['status'] = $iToStatus;
        $bSucc = self::where('id', '=', $this->id)->where('status', '=', $iFromStatus)->update($aExtraData);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr($oWithdrawal->validationErrors);
        return $bSucc;
    }

    /**
     * [judgeProcess 判断提现流程是否符合]
     * @param  [Integer] $iFromStatus [未改变前的状态值, 默认为待审核状态]
     * @param  [Integer] $iToStatus   [将要改变的状态值]
     * @return [Boolean]              [是否符合流程]
     */
    private function judgeProcess($iFromStatus, $iToStatus) {
        return in_array($iToStatus, self::$statusWorkFlow[$iFromStatus]);
    }

    // public function addWithdrawal($id)
    // {
    //     return $this->_updateStatus($id, self::WITHDRAWAL_STATUS_NEW);
    // }
    /**
     * [setToWaitingForConfirmation 设置状态为客服待定]
     */
    public function setToWaitingForConfirmation($iStatus, $aExtraData = []) {
        return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_WAIT_FOR_CONFIRM, $aExtraData);
    }

    /**
     * [setToRejection 设置状态为未通过审核（审核拒绝）]
     * @param [Integer] $iStatus    [当前状态值]
     * @param [String] $sMsg       [拒绝备注]
     * @param [Array] $aExtraData [额外的参数]
     */
    public function setToRejection($iStatus, $aExtraData = [], $account_info = []) {
        $o_account = $account_info['oAccount'];
        $o_user = $account_info['oUser'];
        $amount_freeze = $account_info['amount'];
        $succ = Transaction::addTransaction($o_user, $o_account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $amount_freeze);
        if ($succ == Transaction::ERRNO_CREATE_SUCCESSFUL) {
            return $this->_updateStatus($iStatus, self::WITHDRAWAL_STATUS_REFUSE, $aExtraData);
        }
        return false;
    }

    /**
     * [setToFailture 设置状态为失败]
     */
    public function setToFailture() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_FAIL);
    }

    /**
     * [setToDeductFailture 设置状态为扣游戏币失败]
     */
    public function setToDeductFailture() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_DEDUCT_FAIL);
    }

    /**
     * [setToPartSuccess 设置状态为mc部分成功，扣减部分游戏币]
     */
    public function setToPartSuccess() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_VERIFIED, self::WITHDRAWAL_STATUS_PART);
    }

    /**
     * [setToPartSuccess 设置状态为已退款（审核拒绝情况下）]
     */
    public function setToRefund() {
        return $this->_updateStatus(self::WITHDRAWAL_STATUS_REFUSE, self::WITHDRAWAL_STATUS_REFUND);
    }

    /**
     * 向任务队列追加扣减游戏币任务
     * @param int $id
     * @return bool
     */
    public static function addWithdrawalTask($id) {
        return BaseTask::addTask('DoWithdraw', ['id' => $id], 'withdraw');
    }

    public function addMessageTask($id) {
//        return BaseTask::addTask('SendWithdrawalMessage', ['id' => $this->id], 'withdraw');
        return true;
    }

    /**
     * 向任务队列追加提现额统计任务
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @return bool
     */
    public static function addProfitTask($sDate, $iUserId, $fAmount, $sType = 'withdrawal') {
        $aTaskData = [
            'type' => $sType,
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => substr($sDate, 0, 10),
        ];
        return BaseTask::addTask('StatWithdrawalUpdateProfit', $aTaskData, 'statWithdrawal');
    }

    /**
     * [getTranslateValidStatus 翻译后的提现状态]
     * @return [Array] [提现状态]
     */
    public static function getTranslateValidStatus($iVerified = null) {
        $aValidStatuses = [];
        $aStatuses = [];
        if (!is_null($iVerified)) {
            if ($iVerified == 1) {
                $aStatuses = self::$verifiedStatus;
            } else if ($iVerified == 2) {
                $aStatuses = self::$unVerifiedStatus;
            } else if ($iVerified == 3) {
                $aStatuses = self::$remitStatus;
            }
        } else {
            $aStatuses = array_keys(self::$validStatuses);
        }
        // pr($aStatuses);exit;

        foreach ($aStatuses as $key => $value) {
            $sDesc = self::$validStatuses[$value];
            $aValidStatuses[$value] = __('_withdrawal.' . strtolower($sDesc));
        }
        // foreach(self::$validStatuses as $key => $value) {
        //     $aValidStatuses[$key] = __('_withdrawal.' . strtolower($value));
        // }
        return $aValidStatuses;
    }

    public static function findDepositBySerialNumberNum($sSerialNumber) {
        return self::firstByAttributes(['serial_number' => $sSerialNumber]);
    }

    protected function getFriendlyIsTesterAttribute() {
        return yes_no(intval($this->is_tester));
    }

    public function deductUserFund() {
        $iReturnUnfreeze = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $this->amount);
        if ($iReturnUnfreeze != Transaction::ERRNO_CREATE_SUCCESSFUL) {
            return false;
        }
        $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_WITHDRAW, $this->amount);
        return $iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL;
    }

    public function ReFund() {
        $iReturnUnfreeze = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_UNFREEZE_FOR_WITHDRAWAL, $this->amount);
        return $iReturnUnfreeze == Transaction::ERRNO_CREATE_SUCCESSFUL;
    }

    public function setUser($oUser) {
        if ($this->user_id != $oUser->id) {
            return false;
        }
        $this->User = $oUser;
        return true;
    }

    public function setAccount($oAccount) {
        if ($this->user_id != $oAccount->user_id) {
            return false;
        }
        $this->Account = $oAccount;
        return true;
    }

    public static function checkNewFlag() {
        $key = self::compileNewFlagCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        return intval(Cache::get($key));
    }

    public static function checkNewFlagForFinance() {
        $key = self::compileNewFlagCacheKey('finance');
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        return intval(Cache::get($key));
    }

    public function setNewFlag() {
        $key = self::compileNewFlagCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        Cache::has($key) or Cache::forever($key, 0);
        Cache::increment($key);
//        pr(Cache::get($key));
//        pr($key);
//        exit;
    }

    public static function updateNewFlag() {
        $key = self::compileNewFlagCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        !Cache::has($key) or Cache::decrement($key);
    }

    /**
     * 客服审核完毕后，提示财务专员提款
     */
    public function setNewFlagForFinance() {
        $key = self::compileNewFlagCacheKey('finance');
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        Cache::has($key) or Cache::forever($key, 0);
        Cache::increment($key);
//        pr(Cache::get($key));
//        pr($key);
//        exit;
    }

    public static function updateNewFlagForFinance() {
        $key = self::compileNewFlagCacheKey('finance');
        Cache::setDefaultDriver(static::$cacheDrivers[self::CACHE_LEVEL_FIRST]);
        !Cache::has($key) or Cache::decrement($key);
    }

    private static function compileNewFlagCacheKey($suffix = '') {
        return self::getCachePrefix(true) . 'new-withdrawal' . $suffix;
    }

    /**
     * 受理申请成功的提现订单
     * @param type $iAdminUserId
     * @return type
     */
    public function setVerifyAccected($iAdminUserId) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $aExtraData = [
            'verify_accepter_id' => $iAdminUserId,
            'verify_accepter' => $oAdminUser->username,
            'verify_accepted_at' => date('Y-m-d H:i:s'),
            'status' => self::WITHDRAWAL_STATUS_VERIFY_ACCEPTED,
        ];
        return self::where('id', '=', $this->id)->whereIn('status', [self::WITHDRAWAL_STATUS_NEW, self::WITHDRAWAL_STATUS_RECEIVED])->update($aExtraData) > 0;
    }

    /**
     * 审核通过
     */
    public function setToVerified($aExtraData = []) {
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_VERIFY_ACCEPTED)->update($aExtraData) > 0;
    }

    /**
     * 受理审核通过的提现订单
     * @param type $iAdminUserId
     * @return type
     */
    public function setWithdrawalAccected($iAdminUserId) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $aExtraData = [
            'withdrawal_accepter_id' => $iAdminUserId,
            'withdrawal_accepter' => $oAdminUser->username,
            'withdrawal_accepted_at' => date('Y-m-d H:i:s'),
            'status' => self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED,
        ];
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_VERIFIED)->update($aExtraData) > 0;
    }

    public function setVerifyDeduct($aExtraData) {
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED)->update($aExtraData) > 0;
    }

    /**
     * 审核扣款通过
     * @return boolean
     */
    public function setToDeduct($aExtraData = []) {
        $aExtraData = array_merge($aExtraData, ['remittance_submited_at' => date('Y-m-d H:i:s')]);
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED)->update($aExtraData) > 0;
    }

    /**
     * 审核拒绝
     * @return boolean
     */
    public function setReject($aExtraData) {
        return self::where('id', '=', $this->id)->whereIn('status', [self::WITHDRAWAL_STATUS_VERIFY_ACCEPTED, self::WITHDRAWAL_STATUS_WITHDRAWAL_ACCEPTED])->update($aExtraData) > 0;
    }

    /**
     * 设置状态为扣款成功
     */
    public function setToDeductSuccess() {
        $aExtraData = [
            'status' => self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS,
        ];
        if ($this->status == self::WITHDRAWAL_STATUS_REMITT_VERIFIED) {
            return true;
        }
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_SUCCESS)->update($aExtraData) > 0;
    }

    /**
     * 设置状态为汇款已审核
     */
    public function setToRemitVerified($aExtraData) {
        return self::where('id', '=', $this->id)->where('status', '=', self::WITHDRAWAL_STATUS_DEDUCT_SUCCESS)->update($aExtraData) > 0;
    }

    /**
     * 下载报表实现类，根据不同model，下载报表内容不同
     * @param int $iReportType      报表类型
     * @param int $iFreqType        下载频率类型，如：每天，每周，每月等
     */
    public function download($iReportType, $aDownloadTime, $sFileName, $sDir = './') {
        $oQuery = self::whereBetween('created_at', array_values($aDownloadTime))->where('is_tester', '=', 0);
        $iReportType == 0 or $oQuery->whereIn('status', $iReportType);
        $aConvertFields = [
            'status' => 'formatted_status',
            'is_large' => 'boolean',
        ];

        $aUser = User::getTitleList();
        $aColumn = array_merge(self::$columnForList, ['withdrawal_accepted_at', 'remittance_submited_at']);
        $aData = $oQuery->get($aColumn);
        $aData = $this->makeData($aData, $aColumn, $aConvertFields, null, $aUser);
        return $this->downloadExcel($aColumn, $aData, $sFileName, $sDir);
    }

    public function makeData($aData, $aFields, $aConvertFields, $aBanks = null, $aUser = null) {
        $aResult = array();
        foreach ($aData as $oDeposit) {
            $a = [];
            foreach ($aFields as $key) {
                if ($oDeposit->$key === '') {
                    $a[] = $oDeposit->$key;
                    continue;
                }
                if (array_key_exists($key, $aConvertFields)) {
                    switch ($aConvertFields[$key]) {
                        case 'formatted_status':
                            $a[] = $oDeposit->formatted_status;
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit->$key ? __('Yes') : __('No');
                            break;
                    }
                } else {
                    $a[] = $oDeposit->$key;
                }
            }
            $aResult[] = $a;
        }
        return $aResult;
    }

}
