<?php

/**
 * 账变模型
 */
class Transaction extends BaseModel {

    protected $table = 'transactions';
    protected $softDelete = false;
    protected $fillable = [
        'serial_number',
        'user_id',
        'username',
        'is_tester',
        'user_forefather_ids',
        'account_id',
        'type_id',
        'is_income',
        'trace_id',
        'lottery_id',
        'issue',
        'method_id',
        'way_id',
        'coefficient',
        'description',
        'project_id',
        'project_no',
        'amount',
        'note',
        'previous_balance',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'balance',
        'frozen',
        'available',
        'withdrawable',
        'tag',
        'admin_user_id',
        'administrator',
        'ip',
        'proxy_ip',
    ];
    public static $resourceName = 'Transaction';
    public static $amountAccuracy = 6;
    public static $mobileColumns = [
        'id',
        'serial_number',
        'created_at',
        'type_id',
        'lottery_id',
        'way_id',
        'coefficient',
        'amount',
        'available',
        'is_income',
    ];

//    public static $totalColumnsAllPages = [
//        'amount'
//    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'serial_number',
        'created_at',
        'username',
        'user_forefather_ids',
        'is_tester',
        'description',
        'lottery_id',
        'way_id',
        'coefficient',
        'amount',
        'available',
        'note',
        'ip',
        'administrator'
    ];
    public static $totalColumns = [
        'amount',
    ];
    public static $listColumnMaps = [
        'description' => 'friendly_description',
        'amount' => 'amount_formatted',
        'available' => 'available_formatted',
        'is_tester' => 'formatted_is_tester',
        'serial_number' => 'serial_number_short',
        'user_forefather_ids' => 'user_forefather_ids_formatted'
    ];
    public static $viewColumnMaps = [
        'is_tester' => 'formatted_is_tester',
        'description' => 'friendly_description',
        'amount' => 'amount_formatted',
        'available' => 'available_formatted',
        'frozen' => 'frozen_formatted',
        'balance' => 'balance_formatted',
        'withdrawable' => 'withdrawable_formatted',
        'previous_available' => 'previous_available_formatted',
        'previous_frozen' => 'previous_frozen_formatted',
        'previous_balance' => 'previous_balance_formatted',
        'previous_withdrawable' => 'previous_withdrawable_formatted',
        'serial_number' => 'serial_number',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'way_id' => 'aWays',
        'coefficient' => 'aCoefficients',
//        'admin_user_id' => 'aAdminUsers',
    ];
    public static $ignoreColumnsInView = [
        'account_id',
        'user_id',
        'user_forefather_ids',
        'type_id',
        'method_id',
        'is_income',
        'bet_number',
        'prize_added',
        'total_prize',
        'locked_prize',
        'locked_commission',
        'prize_set',
        'admin_user_id',
        'previous_balance',
        'previous_frozen',
        'previous_available',
        'previous_withdrawable',
        'balance',
        'frozen',
        'withdrawable',
        'safekey',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'user_id';
    public static $rules = [
        'type_id' => 'required|integer',
        'is_income' => 'required|in:0,1',
        'serial number' => 'max:20',
        'username' => 'max:16',
        'lottery_id' => 'integer',
        'method_id' => 'integer',
        'coefficient' => 'in:1.00,0.10,0.01',
        'amount' => 'numeric',
        'previous_balance' => 'numeric',
        'previous_frozen' => 'numeric',
        'previous_available' => 'numeric',
        'previous_withdrawable' => 'numeric',
        'balance' => 'numeric',
        'frozen' => 'numeric',
        'withdrawable' => 'numeric',
        'available' => 'numeric',
        'ip' => 'ip',
        'proxy_ip' => 'ip',
        'note' => 'max:100',
        'tag' => 'max:30',
        'admin_user_id' => 'integer',
        'administrator' => 'max:16',
        'description' => 'required|max:50',
    ];

    const ERRNO_CREATE_SUCCESSFUL = -100;
    const ERRNO_CREATE_ERROR_DATA = -101;
    const ERRNO_CREATE_ERROR_SAVE = -102;
    const ERRNO_CREATE_ERROR_BALANCE = -103;
    const ERRNO_CREATE_LOW_BALANCE = -104;

    protected function getLinkAttribute($sLink){

        if (empty($sLink)){
            $sLink = '#';
            if ($this->project_id && $this->lottery_id){
                if ($this->lottery_id > 9000){
                    $oProject = \JcModel\JcProject::find($this->project_id);
                    if ($oProject){
                        if ($oProject->type == \JcModel\JcProject::TYPE_SELF_BUY){
                            $sLink = route('jc.bet_view', $oProject->bet_id);
                        }elseif ($oProject->type == \JcModel\JcProject::TYPE_GROUP_BUY || $oProject->type == \JcModel\JcProject::TYPE_GROUP_BUY_FOLLOW){
                            $sLink = route('jc.follow', $oProject->group_id);
                        }
                    }
                }else if($this->lottery_id>8000){
                    $sLink =route('projects.view', $this->project_id).'?mode=casino';
                }else{
                    $sLink = route('projects.view',$this->project_id);
                }
            }
        }
        return $sLink;
    }


    public static function makeSeriesNumber($iUserId) {
        return md5($iUserId . microtime(true) . mt_rand());
    }

    protected function beforeValidate() {
//        pr($this->toArray());
//        exit;
        $this->serial_number = self::makeSeriesNumber($this->user_id);
        $this->makeSafeKey();
        return parent::beforeValidate();
    }

//    public function addTransaction($aAttributes, & $aNewBalance){
//        if (!$this->compileData($aAttributes, $aNewBalance)){
//            return false;
//        }
//        return $this->save(static::$rules);
//    }

    private static function compileData($oUser, $oAccount, $iTypeId, $fAmount, & $aNewBalance, & $aExtraData = []) {
        $oTransactionType = TransactionType::find($iTypeId);
//        pr($oTransactionType->getAttributes());
        $fAmount = formatNumber($fAmount, static::$amountAccuracy);
//        pr($aExtraData);
//        exit;
        isset($aExtraData['trace_id']) or $aExtraData['trace_id'] = null;
        $aAttributes = [
            'trace_id' => $aExtraData['trace_id'],
            'user_id' => $oUser->id,
            'is_tester' => $oUser->is_tester,
            'amount' => $fAmount,
            'type_id' => $iTypeId,
            'is_income' => $oTransactionType->credit,
            'previous_frozen' => $oAccount->frozen,
            'previous_balance' => $oAccount->balance,
            'previous_available' => $oAccount->available,
            'previous_withdrawable' => $oAccount->withdrawable,
            'frozen' => $oAccount->frozen,
            'balance' => $oAccount->balance,
            'available' => $oAccount->available,
            'withdrawable' => $oAccount->withdrawable,
            'user_forefather_ids' => $oUser->forefather_ids,
            'account_id' => $oAccount->id,
            'username' => $oUser->username,
            'description' => $oTransactionType->description,
        ];

//        pr($aAttributes);
//        exit;
        if (isset($aExtraData['client_ip'])) {
            $aAttributes['ip'] = $aExtraData['client_ip'];
        }
        if (isset($aExtraData['proxy_ip'])) {
            $aAttributes['proxy_ip'] = $aExtraData['proxy_ip'];
        }
        if ($oTransactionType->trace_linked) {
            if (!isset($aExtraData['lottery_id']) || !isset($aExtraData['way_id']) || !isset($aExtraData['coefficient'])
            ) {
                return false;
            }
            $aAttributes['trace_id'] = $aExtraData['trace_id'];
        }
//        pr($aExtraData);
        if ($oTransactionType->project_linked) {
            if (!isset($aExtraData['project_id']) || !isset($aExtraData['project_no']) || !isset($aExtraData['lottery_id']) || !isset($aExtraData['issue']) || !isset($aExtraData['way_id']) || !isset($aExtraData['coefficient'])
            ) {
                return false;
            }
            $aAttributes['project_id'] = $aExtraData['project_id'];
            $aAttributes['project_no'] = $aExtraData['project_no'];
            isset($aExtraData['trace_id']) or $aAttributes['trace_id'] = $aExtraData['trace_id'];
        }
//        exit;
        if ($oTransactionType->trace_linked || $oTransactionType->project_linked) {
            $aAttributes['lottery_id'] = $aExtraData['lottery_id'];
            $aAttributes['way_id'] = $aExtraData['way_id'];
            $aAttributes['coefficient'] = $aExtraData['coefficient'];
        }
        !isset($aExtraData['issue']) or $aAttributes['issue'] = $aExtraData['issue'];
        !isset($aExtraData['admin_user_id']) or $aAttributes['admin_user_id'] = $aExtraData['admin_user_id'];
        !isset($aExtraData['administrator']) or $aAttributes['administrator'] = $aExtraData['administrator'];
        !isset($aExtraData['note']) or $aAttributes['note'] = $aExtraData['note'];

        // deal amount
        $aSubAccounts = ['balance', 'available', 'frozen', 'withdrawable'];
//        pr($aAttributes);
        foreach ($aSubAccounts as $sField) {
            if (!$oTransactionType->$sField) {
                continue;
            }
            $aAttributes[$sField] += $oTransactionType->$sField * $fAmount;
            $aNewBalance[$sField] = $aAttributes[$sField];
        }
        $aAttributes['withdrawable'] >= 0 or $aNewBalance['withdrawable'] = $aAttributes['withdrawable'] = 0;
//        pr('dow');
//        pr($aAttributes);
//        pr($aNewBalance);
//        exit;
//        pr($oAccount->toArray());
//        exit;
        return $aAttributes;
    }

    public function makeSafeKey() {
        $aFields = [
            'user_id',
            'type_id',
            'account_id',
            'trace_id',
            'amount',
            'lottery_id',
            'issue',
            'way_id',
            'coefficient',
            'description',
            'project_id',
            'amount',
            'admin_user_id',
            'ip',
            'proxy_ip'
        ];
        $aData = [];
        foreach ($aFields as $sField) {
            $aData[] = $this->$sField;
        }
        return $this->safekey = md5(implode('|', $aData));
    }

    protected function setAmountAttribute($fAmount) {
        $this->attributes['amount'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setBalanceAttribute($fAmount) {
        $this->attributes['balance'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setAvailableAttribute($fAmount) {
        $this->attributes['available'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setFrozenAttribute($fAmount) {
        $this->attributes['frozen'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setWithdrawableAttribute($fAmount) {
        $this->attributes['withdrawable'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousBalanceAttribute($fAmount) {
        $this->attributes['previous_balance'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousAvailableAttribute($fAmount) {
        $this->attributes['previous_available'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousFrozenAttribute($fAmount) {
        $this->attributes['previous_frozen'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setPreviousWithdrawableAttribute($fAmount) {
        $this->attributes['previous_withdrawable'] = formatNumber($fAmount, static::$amountAccuracy);
    }

    protected function setSerialNumberAttribute($sSerialNumber) {
        $this->attributes['serial_number'] = strtoupper($sSerialNumber);
    }

    protected function getAmountFormattedAttribute() {
        return ($this->is_income ? '+' : '-') . $this->getFormattedNumberForHtml('amount');
    }

    protected function getDirectAmountAttribute() {
        return ($this->is_income ? '' : '-') . formatNumber($this->attributes['amount'], static::$amountAccuracy);
    }

    protected function getSerialNumberShortAttribute() {
        return substr($this->serial_number, 0, 4) . '...';
    }

    protected function getAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('available');
    }

    protected function getFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('frozen');
    }

    protected function getBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('balance');
    }

    protected function getWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('withdrawable');
    }

    protected function getPreviousBalanceFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_balance');
    }

    protected function getPreviousFrozenFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_frozen');
    }

    protected function getPreviousAvailableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_available');
    }

    protected function getPreviousWithdrawableFormattedAttribute() {
        return $this->getFormattedNumberForHtml('previous_withdrawable');
    }

//    protected function getBalanceFormattedAttribute() {
//        return $this->getFormattedNumberForHtml('balance');
//    }

    protected function getUpdatedAtDayAttribute() {
        // $sDay = explode(' ', $this->updated_at);
        return substr($this->updated_at, 5, 5);
    }

    protected function getUpdatedAtTimeAttribute() {
        $sTime = explode(' ', $this->updated_at);
        return $sTime[1];
    }

    /**
     * 增加新的账变
     * @param User      $oUser
     * @param Account   $oAccount
     * @param int      $iTypeId
     * @param float     $fAmount
     * @param array     $aExtraData
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public static function addTransaction($oUser, $oAccount, $iTypeId, $fAmount, $aExtraData = []) {
//        $aNewBalance = [];
//        pr('ddd' . $fAmount);
//        exit;
        if ($fAmount == 0) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }
        if (!$aAttributes = self::compileData($oUser, $oAccount, $iTypeId, $fAmount, $aNewBalance, $aExtraData)) {
            return self::ERRNO_CREATE_ERROR_DATA;
        }
//        exit;
        $oNewTransaction = new Transaction($aAttributes);
        if (!$oNewTransaction->save()) {
//            pr($oNewTransaction->validationErrors->toArray());
            return self::ERRNO_CREATE_ERROR_SAVE;
        }
//        pr($aNewBalance);
//        exit;
        $oAccount->fill($aNewBalance);
//        pr($oAccount->toArray());
        if (!$oAccount->save()) {
//            pr($oNewTransaction->validationErrors->toArray());
            return self::ERRNO_CREATE_ERROR_BALANCE;
        }
        if(in_array($iTypeId,[TransactionType::TYPE_TRANSFER_OUT, TransactionType::TYPE_TRANSFER_IN]) && $aExtraData['related_user_id']){
            DB::table('transactions_related_users')->insert(
                ['transaction_id'=>$oNewTransaction->id,
                    'related_user_id'=>$aExtraData['related_user_id'],
                    'related_user_name'=>$aExtraData['related_user_name']
                ]
            );
        }
        return self::ERRNO_CREATE_SUCCESSFUL;
    }

    protected function getFriendlyDescriptionAttribute() {
        return __('_transactiontype.' . strtolower(Str::slug($this->attributes['description'])));
    }

    /**
     * 反转，即进行逆操作
     *
     * @param Account $oAccount
     * @return int      0: 成功; -1: 数据错误; -2: 账变保存失败; -3: 账户余额保存失败
     */
    public function reverse($oAccount) {
        $oType = TransactionType::find($this->type_id);
        if (empty($oType) || empty($oType->reverse_type)) {
            return true;
        }
        $oUser = User::find($this->user_id);
        $aExtractData = $this->getAttributes();
        unset($aExtractData['id']);
//        if ($this->project_id){
//            $aExtractData[ 'serial_number' ] = $this->project_no;
//        }
        return self::addTransaction($oUser, $oAccount, $oType->reverse_type, $this->amount, $aExtractData);
    }

    public static function getTransactions($iTypeId, $iLotteryId, $sIssue, $iProjectId = null, $iOffset = null, $iLimit = 100) {
        $aConditions = [
            'type_id' => [ '=', $iTypeId],
            'lottery_id' => [ '=', $iLotteryId],
            'issue' => [ '=', $sIssue],
        ];
        is_null($iProjectId) or $aConditions['project_id'] = ['=', $iProjectId];
        $oQuery = self::doWhere($aConditions)->orderBy('id', 'asc');
        empty($iOffset) or $oQuery = $oQuery->offset($iOffset);
        empty($iLimit) or $oQuery = $oQuery->limit($iLimit);
//        pr($aConditions);
//        exit;
        return $oQuery->get();
    }

    protected function getFormattedIsTesterAttribute() {
        return __('_basic.' . strtolower(Config::get('var.boolean')[$this->attributes['is_tester']]));
    }

    protected function getUserForefatherIdsFormattedAttribute() {
        if ($this->user_forefather_ids) {
            $aIds = explode(',', $this->user_forefather_ids);
            $user = User::find($aIds[(count($aIds) - 1)]);
            if (is_object($user)) {
                return $user->username;
            } else {
                return '';
            }
        } else {
            return '';
        }
    }

    public static function getClientLastIpByUserId($iUserId){
        return self::where('user_id', $iUserId)->where('type_id', TransactionType::TYPE_TRANSFER_OUT)->orderBy('id', 'desc')->first();
    }
    
    public static function getDayTransAmountByUserId($iUserId){
        $start_time = date("Y-m-d 00:00:00", time());
        $end_time = date("Y-m-d 23:59:59", time());
        return self::where('created_at', '>', $start_time)
                ->where('created_at', '<', $end_time)
                ->where('user_id', $iUserId)
                ->where('type_id', TransactionType::TYPE_TRANSFER_OUT)
                ->sum('amount');
    }
    
    /**
     * 获取总代分红帐变中的总代日工资（不包括总代的半月分红）
     */
    public static function getTopAgentBonus($iUserId, $sStartDate, $sEndDate){
        $sStartTime = date("Y-m-d 00:00:00", strtotime($sStartDate));
        $sEndTime 	= date("Y-m-d 23:59:59", strtotime($sEndDate));
        return self::where('user_id', $iUserId)
                ->where('type_id', TransactionType::TYPE_SEND_BONUS)
                ->where('note', '总代日工资')
                ->whereBetween('created_at', array($sStartTime, $sEndTime))
                ->sum('amount');
    }
    
    public static function getUserFirstDeposit($iUserId, $fAmount = 0, $sBeginDate = null){
        $oQuery = self::where('user_id', $iUserId)
                ->whereIn('type_id', [TransactionType::TYPE_DEPOSIT,TransactionType::TYPE_DEPOSIT_BY_ADMIN])
                ->where('amount', '>=', $fAmount);
       	empty($sBeginDate) or $oQuery = $oQuery->where('created_at', '>=', $sBeginDate);
        return $oQuery->first(['amount','created_at']);
    }
}
