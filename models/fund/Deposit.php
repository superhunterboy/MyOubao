<?php

class Deposit extends BaseModel {

    protected $table = 'deposits';

    const DEPOSIT_THIRD_AVAILABLE_TIME = 2400;

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'user_id',
        'username',
        'is_tester',
        'is_agent',
        'user_parent',
        'user_forefather_ids',
        'top_agent_id',
        'top_agent',
        'bank_id',
        'amount',
        'order_no',
        'deposit_mode',
        'ip',
        'web_url',
        'postscript',
        'service_order_no',
        'service_time',
        'service_order_status',
        'service_bank_seq_no',
        'collection_bank_id',
        'accept_card_num',
        'accept_email',
        'accept_acc_name',
        'real_amount',
        'fee',
        'pay_time',
        'accept_bank_address',
        'status',
        'error_msg',
        'mode',
        'break_url',
        'mc_token',
        'merchant_key',
        'merchant_code',
        'account_no',
        'notify_type',
        'notify_data',
        'put_at',
        'deposit_id',
        'deposit',
        'platform_identifier',
        'platform_id',
        'platform',
        'query_enabled',
        'commission_sent_at',
        'commission',
        'status_commission',
        'note',
    ];
    public static $resourceName = 'Deposit';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'is_tester',
        'top_agent',
        'user_parent',
        'created_at',
        'amount',
        'platform',
        'account_no',
        'bank_id',
        'order_no',
        'put_at',
        'status',
        'service_order_no',
        'service_time',
        'accepter',
        'verify_accepter',
//        'real_amount',
        'postscript',
//        'fee',
//        'deposit_mode',
    ];
    public static $totalColumns = [
        'amount',
    ];
    public static $htmlNumberColumns = [
        'amount' => 2,
        'real_amount' => 2,
        'fee' => 2,
    ];
    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'deposit_mode' => 'formatted_deposit_mode',
        'is_tester' => 'friendly_is_tester',
        'created_at' => 'friendly_apply_time',
        'put_at' => 'friendly_put_time',
        'service_time' => 'friendly_pay_time'
    ];
    public static $viewColumnMaps = [
        'status' => 'formatted_status',
        'amount' => 'amount_formatted',
        'real_amount' => 'real_amount_formatted',
        'fee' => 'fee_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status' => 'validStatuses',
        'bank_id' => 'aBanks',
        'aPaymentPlatform' => 'aPaymentPlatform',
        'deposit_mode' => 'aDepositMode',
    ];
    public static $noOrderByColumns = [
        'add_game_money_time'
    ];

    /**
     * API: 充值请求
     * @var int
     */
    const DEPOSIT_API_REQUEST = 1;

    /**
     * API: 充值响应
     * @var int
     */
    const DEPOSIT_API_RESPONSE = 2;

    /**
     * API: 充值确认
     * @var int
     */
    const DEPOSIT_API_APPROVE = 3;

    /**
     * 充值渠道：银行卡
     * @var int
     */
    const DEPOSIT_MODE_BANK_CARD = 1;

    /**
     * 充值渠道：第三方
     * @var int
     */
    const DEPOSIT_MODE_THIRD_PART = 2;

    /**
     * 状态：未处理（新订单）
     * @var int
     */
    const DEPOSIT_STATUS_NEW = 0;

    /**
     * 状态：申请成功
     * @var int
     */
    const DEPOSIT_STATUS_RECEIVED = 1;

    /**
     * 状态：受理
     * @var int
     */
    const DEPOSIT_STATUS_ACCEPTED = 2;

    /**
     * 状态：校验成功，等待加币
     * @var int
     */
    const DEPOSIT_STATUS_CHECK_SUCCESS = 3;

    /**
     * 状态：成功
     * @var int
     */
    const DEPOSIT_STATUS_SUCCESS = 4;

    /**
     * 状态：加游戏币失败
     * @var int
     */
    const DEPOSIT_STATUS_ADD_FAIL = 5;

    /**
     * 状态：关闭
     */
    const DEPOSIT_STATUS_CLOSED = 6;

    /**
     * 状态：待审核
     */
    const DEPOSIT_STATUS_WAITING_VERIFY = 7;

    /**
     * 状态：审核受理
     */
    const DEPOSIT_STATUS_VERIFY_ACCEPTED = 8;
    const DEPOSIT_STATUS_VERIFY_REJECTED = 9;
    const DEPOSIT_STATUS_EXCEPTION = 10;
    const COMMISSION_STATUS_WAITING = 0;
    const COMMISSION_STATUS_SENT = 2;

    public static $validStatuses = [
        self::DEPOSIT_STATUS_NEW => 'New',
        self::DEPOSIT_STATUS_RECEIVED => 'apply-received',
        self::DEPOSIT_STATUS_ACCEPTED => 'accepted',
        self::DEPOSIT_STATUS_CHECK_SUCCESS => 'waiting-load',
        self::DEPOSIT_STATUS_SUCCESS => 'success',
        self::DEPOSIT_STATUS_ADD_FAIL => 'add-failure',
        self::DEPOSIT_STATUS_CLOSED => 'closed',
        self::DEPOSIT_STATUS_WAITING_VERIFY => 'wait-verify',
        self::DEPOSIT_STATUS_VERIFY_ACCEPTED => 'verify-accepted',
        self::DEPOSIT_STATUS_VERIFY_REJECTED => 'verify-rejected',
        self::DEPOSIT_STATUS_EXCEPTION => 'exception-deposit',
    ];
    public static $aDepositMode = [
        self::DEPOSIT_MODE_BANK_CARD => 'bankcard',
        self::DEPOSIT_MODE_THIRD_PART => 'the-third-part',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required|between:1,50',
        'top_agent' => 'between:1,50',
        'bank_id' => 'integer',
        'amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'order_no' => 'between:1,64',
        'deposit_mode' => 'in:1,2',
        //'web_url' => '',
        'postscript' => 'between:1,32',
        'service_order_no' => 'between:1,50',
        'collection_bank_id' => 'integer',
        'accept_card_num' => 'numeric',
        'accept_email' => 'between:1,200',
        'accept_acc_name' => 'between:1,19',
        'real_amount' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'fee' => 'regex:/^[0-9]+(.[0-9]{1,2})?$/',
        'pay_time' => 'date',
        'accept_bank_address' => 'between:1,100',
        'status' => 'in:0,1,2,3,4,5,6,7,8,9,10',
        'error_msg' => 'between:1,255',
        'mode' => 'in:0,1,2',
        'break_url' => 'between:1,1000',
    ];
    public static $aReportType = [
        ReportDownloadConfig::TYPE_DEPOSIT_THE_THIRD_PART => self::DEPOSIT_STATUS_SUCCESS,
    ];
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = [];
    // 表单只读字段
    public static $aReadonlyInputs = [];
    public static $ignoreColumnsInView = [
        'mode',
        'merchant_key',
        'merchant_code',
        'sign',
        'break_url',
        'ip',
    ];
    public static $ignoreColumnsInEdit = []; // TODO 待定, 是否在新增form中忽略user_id, 使用当前登录用户的信息(管理员可否给用户生成提现记录)

    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->order_no);
        return parent::afterSave($oSavedModel);
    }

    /**
     * 添加新订单，并返回该订单实例
     * @param array $aInitData
     * @return \Deposit
     */
    public static function createDeposit(array $aInitData) {
        $oDeposit = new Deposit($aInitData);
        if (!$bSucc = $oDeposit->save()) {
//            pr($oDeposit->validationErrors->toArray());
//            exit;
            return false;
        }
        return $oDeposit;
    }

    /**
     * [getAccountHiddenAttribute 访问器方法, 生成只显示末尾4位的银行卡账号信息, 且每4位空格隔开]
     * @return [String]          [只显示末尾4位的银行卡账号信息,且每4位空格隔开]
     */
    protected function getAccountHiddenAttribute() {
        $str = str_repeat('*', (strlen($this->account) - 4));
        $account_hidden = preg_replace('/(\*{4})(?=\*)/', '$1 ', $str) . ' ' . substr($this->account, -4);
        return $account_hidden;
    }

    protected function beforeValidate() {
        $this->commission = $this->countCommission();
        return parent::beforeValidate();
    }

    /**
     * [countTransactionCharge 计算手续费 ]
     * @param  [type] $iAmount [description]
     * @return [type]          [description]
     */
    public function countTransactionCharge($iAmount) {
        // TODO
        return 0;
    }

    /**
     * _updateStatus 更新提现记录状态
     * @param  Int $iToStatus   将要改变的状态值
     * @param  Array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    private function _updateStatus($iToStatus, array $aExtraData = []) {
        if (!$this->exists) {
            return FALSE;
        }
        if (!empty($aExtraData) && is_array($aExtraData)) {
            $this->fill($aExtraData);
        }
        $aExtraData['status'] = $iToStatus;
        $iAffectRows = self::where('id', '=', $this->id)->where('status', '=', $this->status)->where('status', '<>', $iToStatus)->update($aExtraData);
        $iAffectRows <= 0 or $this->status = $iToStatus;
//        pr($this->validationErrors);
        return $iAffectRows > 0;
    }

    /**
     * 设置状态：订单申请成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setReceived(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_NEW && $this->_updateStatus(self::DEPOSIT_STATUS_RECEIVED, $aExtraData);
    }

    /**
     * 设置状态：订单申请失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setRefused(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_NEW && $this->_updateStatus(self::DEPOSIT_STATUS_REFUSED, $aExtraData);
    }

    /**
     * 设置状态：订单完成，充值成功
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setSuccess(array $aExtraData = []) {
        return in_array($this->status, [self::DEPOSIT_STATUS_ACCEPTED, self::DEPOSIT_STATUS_CHECK_SUCCESS]) && $this->_updateStatus(self::DEPOSIT_STATUS_SUCCESS, $aExtraData);
    }

    /**
     * 设置状态：等待加币
     * @return boolean
     */
    public function setWaitingLoad($aExtraData = []) {
        $aFromStatus = [self::DEPOSIT_STATUS_RECEIVED, self::DEPOSIT_STATUS_VERIFY_ACCEPTED];
        return in_array($this->status, $aFromStatus) && $this->_updateStatus(self::DEPOSIT_STATUS_CHECK_SUCCESS, $aExtraData);
    }

    /**
     * 设置状态：等待加币
     * @return boolean
     */
    public function setWaitingVerify($aExtraData = []) {
        return in_array($this->status, [self::DEPOSIT_STATUS_ACCEPTED, self::DEPOSIT_STATUS_VERIFY_REJECTED]) && $this->_updateStatus(self::DEPOSIT_STATUS_WAITING_VERIFY, $aExtraData);
    }

    /**
     * 设置状态：订单关闭
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setClosed(array $aExtraData = []) {
        return $this->status == self::DEPOSIT_STATUS_RECEIVED && $this->_updateStatus(self::DEPOSIT_STATUS_CLOSED, $aExtraData);
    }

    public function setAccected($iAdminUserId) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $data = [
            'accepter_id' => $iAdminUserId,
            'accepter' => $oAdminUser->username,
            'accepted_at' => date('Y-m-d H:i:s'),
            'status' => self::DEPOSIT_STATUS_ACCEPTED,
        ];
        return self::where('id', '=', $this->id)->whereIn('status', [self::DEPOSIT_STATUS_NEW, self::DEPOSIT_STATUS_RECEIVED])->update($data) > 0;
    }

    public function setVerifyAccected($iAdminUserId) {
        $oAdminUser = AdminUser::find($iAdminUserId);
        $data = [
            'verify_accepter_id' => $iAdminUserId,
            'verify_accepter' => $oAdminUser->username,
            'verify_accepted_at' => date('Y-m-d H:i:s'),
            'status' => self::DEPOSIT_STATUS_VERIFY_ACCEPTED,
        ];
        return self::where('id', '=', $this->id)->whereIn('status', [self::DEPOSIT_STATUS_WAITING_VERIFY])->update($data) > 0;
    }

    /**
     * 设置状态：添加游戏币失败
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setAddFail(array $aExtraData = []) {
        return in_array($this->status, [self::DEPOSIT_STATUS_ACCEPTED, self::DEPOSIT_STATUS_RECEIVED, self::DEPOSIT_STATUS_VERIFY_REJECTED]) && $this->_updateStatus(self::DEPOSIT_STATUS_ADD_FAIL, $aExtraData);
    }

    /**
     * 设置状态：拒绝通过，重新上传凭证
     * @param array $aExtraData  额外需要更新的数据
     * @return boolean
     */
    public function setReject(array $aExtraData = []) {
        return in_array($this->status, [self::DEPOSIT_STATUS_VERIFY_ACCEPTED]) && $this->_updateStatus(self::DEPOSIT_STATUS_VERIFY_REJECTED, $aExtraData);
    }

    /**
     * 用平台订单号获取订单对象
     * @param string $sCompanyOrderNum 平台订单号
     * @return Deposit|null
     */
    public static function findDepositByCompanyOrderNum($sCompanyOrderNum) {
        return Deposit::firstByAttributes(['company_order_num' => $sCompanyOrderNum]);
    }

    public static function getDepositAmountByDate($sBeginDate, $sEndDate, $iUserId) {
        $oQuery = self::where('user_id', '=', $iUserId);
        if (!is_null($sBeginDate)) {
            $oQuery->where('created_at', '>=', $sBeginDate);
        }
        if (!is_null($sEndDate)) {
            $oQuery->where('created_at', '<=', $sEndDate);
        }
        $oQuery->where('status', '=', self::DEPOSIT_STATUS_SUCCESS);
        $aUserProfits = $oQuery->get(['amount']);
        $data = [];
        $i = 0;
        foreach ($aUserProfits as $oUserProfit) {
            $data[$i]['amount'] = $oUserProfit->amount;
            $i++;
        }
        return $data;
    }

    public static function getMaxAmountByDate($sBeginDate, $sEndDate, $iUserId) {
        $aUserDeposits = self::getDepositAmountByDate($sBeginDate, $sEndDate, $iUserId);
        $fMaxDepositAmount = 0;
        foreach ($aUserDeposits as $data) {
            $fMaxDepositAmount >= $data['amount']  or $fMaxDepositAmount= $data['amount'];
        }
        return $fMaxDepositAmount;
    }

    
    
    public static function getTotalAmountByDate($sBeginDate, $sEndDate, $iUserId) {
        $aUserDeposits = self::getDepositAmountByDate($sBeginDate, $sEndDate, $iUserId);
        $aTotalDeposits = [];
        foreach ($aUserDeposits as $data) {
            $aTotalDeposits[] = $data['real_amount'];
        }
        $fTotalDeposit = array_sum($aTotalDeposits);
        return $fTotalDeposit;
    }

    /**
     * 向任务队列追加充值额统计任务
     * @param date $sDate
     * @param int $iUserId
     * @param float $fAmount
     * @return bool
     */
    public static function addProfitTask($sDate, $iUserId, $fAmount) {
        $aTaskData = [
            'type' => 'deposit',
            'user_id' => $iUserId,
            'amount' => $fAmount,
            'date' => substr($sDate, 0, 10),
        ];
        return BaseTask::addTask('StatDepositUpdateProfit', $aTaskData, 'statDeposit');
    }

    public static function getDepositByNo($sOrderNo) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::where('order_no', '=', $sOrderNo)->first();
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        $key = self::createCacheKey($sOrderNo);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::where('order_no', '=', $sOrderNo)->first();
            if (!is_object($obj)) {
                return false;
            }
            Cache::put($key, $obj->getAttributes(), 5);
        }

        return $obj;
//        return self::where('order_no', '=', $sOrderNo)->get()->first();
    }

    /**
     * 向任务队列追加充值任务
     * @param int $id
     * @return bool
     */
    public static function addDepositTask($id) {
        return BaseTask::addTask('DoDeposit', ['id' => $id], 'deposit');
    }

    public function addCommissionTask() {
        if (!$this->commission || !$this->user_parent) {
            return true;
        }
        return BaseTask::addTask('SendDepositCommission', ['id' => $this->id], 'deposit');
    }
    
    public function addMessageTask(){
        return BaseTask::addTask('SendDepositMessage', ['id' => $this->id], 'deposit');
    }

    /**
     * 向任务队列追加check任务
     * @param int $id
     * @return bool
     */
    public static function addCheckTask($id, $time = 0) {
        return BaseTask::addTask('CheckDeposit', ['id' => $id], 'withdraw', $time);
    }

    /**
     * [getSerialNumberShortAttribute 获取序列号的截断格式]
     * @return [type] [4位序列号的截断格式]
     */
    protected function getCompanyOrderNumShortAttribute() {
        return substr($this->company_order_num, 0, 4) . '...';
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedStatusAttribute() {
        return __('_deposit.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedDepositModeAttribute() {
        return __('_deposit.' . strtolower(Str::slug(static::$aDepositMode[$this->attributes['deposit_mode']])));
    }

    protected function getAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('amount');
    }

    protected function getFeetFormattedAttribute() {
        return $this->getFormattedNumberForHtml('fee');
    }

    protected function getRealAmountFormattedAttribute() {
        return $this->getFormattedNumberForHtml('real_amount');
    }

    protected function getFriendlyIsTesterAttribute() {
        return yes_no(intval($this->is_tester));
    }

    public function setServiceInfo($aServiceInfo) {
        $this->service_order_status = $aServiceInfo['trade_status'];
        $this->service_time = $aServiceInfo['trade_time'];
        $this->service_order_no = $aServiceInfo['trade_no'];
        !isset($aServiceInfo['bank_seq_no']) or $this->service_bank_seq_no = $aServiceInfo['bank_seq_no'];
        return $this->save();
    }

    protected function getFriendlyApplyTimeAttribute() {
        return substr($this->attributes['created_at'], 5);
    }

    protected function getFriendlyPutTimeAttribute() {
        return substr($this->attributes['put_at'], 5);
    }

    protected function getFriendlyPayTimeAttribute() {
        return substr($this->attributes['service_time'], 5);
    }

    private function countCommission() {
        if (!SysConfig::check('deposit_commission_enabled', true)) {
            return null;
        }
        if (!$this->user_parent) {
            return null;
        }
        if ($this->amount < 200) {
            return 0;
        }
        if ($this->amount < 400) {
            return 1;
        }
        if ($this->amount < 1000) {
            return 2;
        }
        if ($this->amount < 3000) {
            return 6;
        }
        if ($this->amount < 5000) {
            return 18;
        }
        if ($this->amount < 10000) {
            return 33;
        }
        return 66;
    }

    public function setCommissionSent() {
        $data = [
            'status_commission' => self::COMMISSION_STATUS_SENT,
            'commission_sent_at' => date('Y-m-d H:i:s')
        ];
        return self::where('id', '=', $this->id)->where('status', '=', self::DEPOSIT_STATUS_SUCCESS)
                        ->where('status_commission', '=', self::COMMISSION_STATUS_WAITING)
                        ->update($data) > 0;
    }

    public static function checkNewFlag() {
        $key = self::makeNewFlagCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[1]);
        if ($bSucc = Cache::has($key)) {
            Cache::forget($key);
        }
        return $bSucc;
    }

    public function setNewFlag() {
        $key = self::makeNewFlagCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[1]);
        Cache::put($key, $this->id, 1);
    }

    private static function makeNewFlagCacheKey() {
        return 'new-deposit';
    }

    public function setException() {
        return in_array($this->status, [self::DEPOSIT_STATUS_ACCEPTED, self::DEPOSIT_STATUS_VERIFY_ACCEPTED]) && $this->_updateStatus(self::DEPOSIT_STATUS_EXCEPTION);
    }

    /**
     * 下载报表实现类，根据不同model，下载报表内容不同
     * @param int $iReportType      报表类型
     * @param int $iFreqType        下载频率类型，如：每天，每周，每月等
     */
    public function download($iReportType, $aDownloadTime, $sFileName, $sDir = './') {
        $oQuery = self::whereBetween('created_at', array_values($aDownloadTime))->where('is_tester', '=', 0)->where('deposit_mode', '=', self::DEPOSIT_MODE_THIRD_PART);
        $iReportType == 0 or $oQuery->where('status', '=', $iReportType);

        $aConvertFields = [
            'status' => 'formatted_status',
            'bank_id' => 'bank',
            'deposit_mode' => 'deposit_mode',
            'created_at' => 'date',
            'updated_at' => 'deposit_add_game_money_time',
            'is_tester' => 'boolean',
        ];

        $aBanks = Bank::getTitleList();
        $aColumn = array_merge(self::$columnForList);
        $aData = $oQuery->get($aColumn);
        $aData = $this->makeData($aData, $aColumn, $aConvertFields, $aBanks);
        return $this->downloadExcel($aColumn, $aData, $sFileName, $sDir);
    }

    function makeData($aData, $aFields, $aConvertFields, $aBanks = null) {
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
                        case 'bank':
                            $a[] = is_null($oDeposit->$key) ? '' : array_get($aBanks, $oDeposit->$key);
                            break;
                        case 'deposit_mode':
                            $a[] = $oDeposit->formatted_deposit_mode;
                            break;
                        case 'deposit_add_game_money_time':
                            if ($key == 'updated_at') {
                                if ($oDeposit->status == Deposit::DEPOSIT_STATUS_SUCCESS && is_object($oDeposit->updated_at)) {
                                    $a[] = $oDeposit->updated_at->toDateTimeString();
                                } else {
                                    $a[] = '';
                                }
                            }
                            break;
                        case 'boolean':
                            $a[] = $oDeposit->$key ? __('Yes') : __('No');
                            break;
                        case 'date':
                            if (is_object($oDeposit->$key)) {
                                $a[] = $oDeposit->$key->toDateTimeString();
                            } else {
                                $a[] = '';
                            }
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
