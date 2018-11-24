<?php

class BankCard extends BaseModel {

    protected $table = 'bank_cards';
    public static $resourceName = 'BankCard';
    protected static $cacheLevel = self::CACHE_LEVEL_NONE;
    protected $hidden = [];
    protected $guarded = [];

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public static $mainParamColumn = 'user_id';
    public static $titleColumn = 'account';
    public static $listColumnMaps = [
        'status' => 'formatted_status',
//        'account' => 'display_account_no',
    ];
    protected $fillable = [
        'bank_id',
        'bank',
        'province_id',
        'province',
        'city_id',
        'city',
        'branch',
        'branch_address',
        'account_name',
        'account',
        'status',
    ];
    public static $columnForList = [
        'bank',
        'province',
        'city',
        'branch',
        'account',
        'status',
        'created_at',
        'updated_at',
    ];
    // 账号反查功能展示列
    public static $columnForUserSearchList = [
        'username',
        'parent_username',
        'bank',
        'province',
        'city',
        'blocked',
        'blocked_type',
        'is_tester',
    ];

    const LOCKED = 1; // user's bankcards locked status
    const UNLOCKED = 0;
    const STATUS_NOT_IN_USE = 0;
    const STATUS_IN_USE = 1;
    const STATUS_DELETED = 2;
    const STATUS_BLACK = 3;

    public static $validStatuses = [
        self::STATUS_NOT_IN_USE => 'Not In Use',
        self::STATUS_IN_USE => 'In Use',
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_BLACK => 'Black List',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'bank_id' => 'aBanks',
        'province_id' => 'aProvinces',
        'city_id' => 'aCities',
        'status' => 'aStatus',
    ];
    public $orderColumns = [
        'updated_at' => 'desc'
    ];
    public static $rules = [
        'bank_id' => 'required|integer|min:1',
        // 'bank'              => 'required|max:50',
        'province_id' => 'integer|min:1',
        // 'province'          => 'between:0,20',
        'city_id' => 'integer|min:1',
        // 'city'              => 'between:0,20',
        'branch' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
        'branch_address' => 'between:1,100',
        'account_name' => 'required|between:1,20|regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
        'account' => 'required|regex:/^[0-9]*$/|between:16,19',
        'status' => 'in:0,1,2,3',
    ];
    public $autoPurgeRedundantAttributes = true;
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = ['bank', 'province', 'city'];
    public static $ignoreColumnsInEdit = ['id', 'status'];
    public static $ignoreColumnsInView = ['id', 'bank_id', 'province_id', 'city_id'];

    protected function beforeValidate() {
        if (intval($this->bank_id) && !$this->bank) {
            $oBank = Bank::find($this->bank_id);
            $this->bank = $oBank->name;
        }
        if (intval($this->province_id) && !$this->province) {
            $oProvince = District::find($this->province_id);
            $this->province = $oProvince ? $oProvince->name : '';
        }
        if (intval($this->city_id) && !$this->city) {
            $oCity = District::find($this->city_id);
            $this->city = $oCity ? $oCity->name : '';
        }
        if (!$this->branch_address) {
            $this->branch_address = $this->province . $this->city . $this->branch;
        }
        if ($this->id) {
            self::$rules['account'] = 'required|regex:/^[0-9]*$/|between:16,19' . $this->id;
        }
        return parent::beforeValidate();
    }

    /**
     * [getUserAccounts 根据用户id获取该用户的银行卡]
     * @param  [Integer] $iUserId [用户id]
     * @return [Array]          [银行卡数组]
     */
    public static function getUserAccounts($iUserId) {
        $aColumns = ['id', 'bank_id', 'account_name', 'account', 'province', 'branch', 'branch_address'];
        $aUserAccounts = UserBankCard::where('user_id', '=', $iUserId)->get($aColumns);
        $data = [];
        // pr((int)isset($data[$aUserAccounts[0]->bank_id]));exit;
        foreach ($aUserAccounts as $account) {
            if (!isset($data[$account->bank_id]) || !is_array($data[$account->bank_id])) {
                $data[$account->bank_id] = [];
            }
            $data[$account->bank_id][] = $account->toArray();
        }
        return $data;
    }

    public static function getAllAccountNames($user_id) {
        $aAccounts = UserBankCard::all(['account']);
        $data = [];
        foreach ($aAccounts as $account) {
            $data[$account->account] = $account->account;
        }
        return $data;
    }

    protected static function getUserCards($iUserId) {
        // $iUserId or $iUserId = Session::get('user_id');
        $iStatus = self::STATUS_DELETED;
        $oQuery = UserUserBankCard::where('user_id', '=', $iUserId)->where('status', '<>', $iStatus);
        return $oQuery;
    }

    /**
     * [getUserCardsInfo get user's binded cards info]
     * @return [Int] [cards info]
     */
    public static function getUserCardsInfo($iUserId, $aColumns = null) {
        // if (!$columns || (is_array($columns) && !count($columns))) $columns = [self::$titleColumn];
        // $aColumns = array_merge(['account'], $columns);
        $aColumns or $aColumns = ['id', 'account'];
        // $iUserId = Session::get('user_id');
        $oQuery = self::getUserCards($iUserId);
        $aInfo = $oQuery->get($aColumns);
        return $aInfo;
    }

    /**
     * [setLockStatus 设置用户银行卡为锁定状态]
     * @param [Int] $iCardId [用户银行卡id]
     * @param [Int] $iStatus [锁定/解锁状态值]
     */
    public static function setLockStatus($iCardId, $iStatus, $iLockerId) {
        if (!$iLockerId || !$iCardId)
            return false;
        $oCard = self::find($iCardId);
        // $iLockerId or $iLockerId = Session::get('admin_user_id');
        if (!$oCard)
            return false;
        $oCard->islock = $iStatus;
        if ($iStatus == self::LOCKED) {
            $oCard->locker = $iLockerId;
            $oCard->lock_time = Carbon::now()->toDateTimeString();
        } else {
            $oCard->unlocker = $iLockerId;
            $oCard->unlock_time = Carbon::now()->toDateTimeString();
        }
        $oCard->account_confirmation = $oCard->account;
        // pr($oCard->toArray());exit;
        return $oCard->save();
    }

    protected function getFormattedStatusAttribute() {
        return __('_bankcard.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    protected function getDisplayAccountNoAttribute() {
        return substr($this->attributes['account'], -4);
    }

    protected function getFormattedAccountNameAttribute() {
        return '**' . substr($this->account_name, -3);
    }

}
