<?php

class UserBankCard extends BaseModel {

    const ERRNO_CARD_LOCKED = -2100;
    const ERRNO_CARD_OPERATION_STEP_ERROR = -2101;
    const ERRNO_VALID_FAILED = -2102;
    const ERRNO_NO_CARD = -2103;
    const ERRNO_BIND_CARD_FAILED = -2104;
    const ERRNO_MODIFY_CARD_FAILED = -2105;
    const ERRNO_MODIFY_CARD_SUCCESS = -2106;
    const ERRNO_BIND_CARD_SUCCESS = -2107;
    const ERRNO_LOCK_CARD_SUCCESS = -2108;
    const ERRNO_LOCK_CARD_FAILED = -2109;
    const ERRNO_UNLOCK_CARD_FORBIDDEN = -2110;
    const ERRNO_MISSING_BANK_CARD = -2111;
    const ERRNO_DELETE_SUCCESS = -2112;
    const ERRNO_OUT_MAX_CARD_NUM = -2113;
    const ERRNO_VALID_SUCCESS = -2114;
    const ERRNO_DELETE_FAILED = -2115;

    /**
     * 用户绑卡数量限制
     */
    const BIND_CARD_NUM_LIMIT = 4;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_bank_cards';
    public static $resourceName = 'UserBankCard';
    protected static $cacheLevel = 1;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
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
        'user_id',
        'username',
        'parent_user_id',
        'parent_username',
        'user_forefather_ids',
        'user_forefathers',
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
        'account_confirmation',
        'status',
        'is_agent',
        'is_tester',
        'bank_card_id',
    ];
    public static $mobileColumns = [
        'id',
        'account_name',
        'account',
        'bank_id',
        'bank',
        'province_id',
        'province',
        'city_id',
        'city',
        'branch',
        'islock',
    ];
    public static $columnForList = [
        'username',
        'account_name',
        'account',
        'bank',
        'province',
        'city',
        'branch',
        'status',
//        'locker',
//        'lock_time',
//        'unlocker',
//        'unlock_time',
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

    const STATUS_IN_USE = 1;
    const STATUS_DELETED = 2;
    const STATUS_LOCKED = 3;

    public static $validStatuses = [
        self::STATUS_IN_USE => 'In Use',
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_LOCKED => 'locked',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        // 'user_id'  => 'aUsers',
        'bank_id' => 'aBanks',
        'province_id' => 'aProvinces',
        'city_id' => 'aCities',
        'status' => 'aStatus',
        'user_id'=>'aUsers',
    ];
    public $orderColumns = [
        'updated_at' => 'desc'
    ];
    public static $rules = [
        'user_id' => 'required|integer|min:1',
        // 'username'          => 'alpha_num|custom_first_character|between:6,16',
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
//        'account_confirmation' => 'required|regex:/^[0-9]*$/|between:16,19',
        'status' => 'in:1,2,3',
        'is_agent' => 'in:0,1',
        'is_tester' => 'in:0,1',
        'locker' => 'integer',
        'lock_time' => 'date',
        'unlocker' => 'integer',
        'unlock_time' => 'date',
    ];
    public $autoPurgeRedundantAttributes = true;
    // 编辑表单中隐藏的字段项
    public static $aHiddenColumns = ['bank', 'province', 'city'];
    public static $ignoreColumnsInEdit = ['id', 'parent_user_id', 'parent_username', 'user_forefather_ids', 'user_forefathers', 'locker', 'lock_time', 'unlocker', 'unlock_time', 'is_agent', 'is_tester', 'status'];
    public static $ignoreColumnsInView = ['id', 'user_id', 'parent_user_id', 'user_forefather_ids', 'bank_id', 'province_id', 'city_id'];

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
        if (intval($this->user_id)) {
            $oUser = User::find($this->user_id);
            // 如果更新用户上下级关系，需要更新该部分数据
            $this->parent_user_id = $oUser->parent_id;
            $this->parent_username = $oUser->parent;
            $this->user_forefather_ids = $oUser->forefather_ids;
            $this->user_forefathers = $oUser->forefathers;
            if (!$this->username) {
                $this->username = $oUser->username;
            }
            if (!$this->is_agent) {
                $this->is_agent = $oUser->is_agent;
            }
            if (!$this->is_tester) {
                $this->is_tester = $oUser->is_tester;
            }
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
        $aAccounts = RUserBankCard::all(['account']);
        $data = [];
        foreach ($aAccounts as $account) {
            $data[$account->account] = $account->account;
        }
        return $data;
    }

    protected static function getUserBankCards($iUserId) {
        $oQuery = self::where('user_id', '=', $iUserId)->where('status', '<>', self::STATUS_DELETED);
        return $oQuery;
    }

    /**
     * [getUserCardsInfo get user's binded cards info]
     * @return [Int] [cards info]
     */
    public static function getUserCardsInfo($iUserId, $aColumns = null) {
        $aColumns or $aColumns = ['id', 'account'];
        $oQuery = self::getUserBankCards($iUserId);
        $aInfo = $oQuery->get($aColumns);
        return $aInfo;
    }

    /**
     * 获取用户绑定银行卡数量
     * @param type $iUserId
     * @return type
     */
    public static function getUserBankCardsCount($iUserId = null) {
        $oQuery = self::getUserBankCards($iUserId);
        return $oQuery->count();
    }

    /**
     * 设置用户绑定的银行卡为锁卡状态
     * @param int $iUserId  用户id
     */
    public static function setLockStatus($iUserId) {
        $aExtraData = [
            'status' => self::STATUS_LOCKED,
        ];
        return self::_setStatus($iUserId, self::STATUS_IN_USE, $aExtraData);
    }

    /**
     * 设置用户绑定的银行卡为使用状态
     * @param int $iUserId  用户id
     */
    public static function setInUseStatus($iUserId) {
        $aExtraData = [
            'status' => self::STATUS_IN_USE,
        ];
        return self::_setStatus($iUserId, self::STATUS_LOCKED, $aExtraData);
    }

    /**
     * 设置用户绑定的银行卡为删除状态
     * @param int $iUserId  用户id
     */
    public function setDeleteStatus() {
        $aExtraData = [
            'status' => self::STATUS_DELETED,
        ];
        return self::where('id', '=', $this->id)->where('status', '=', self::STATUS_IN_USE)->update($aExtraData) > 0;
    }

    public static function _setStatus($iUserId, $iFromStatus, $aExtraData = []) {
        return self::where('user_id', '=', $iUserId)->where('status', '=', $iFromStatus)->update($aExtraData) > 0;
    }

    protected function getFormattedStatusAttribute() {
        return __('_userbankcard.' . strtolower(Str::slug(static::$validStatuses[$this->attributes['status']])));
    }

    protected function getDisplayAccountNoAttribute() {
        return substr($this->attributes['account'], -4);
    }

    protected function getFormattedAccountNameAttribute() {
        return '**' . substr($this->account_name, -3);
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

    public static function createUserBankCardCountCacheKey($iUserId) {
        $sPrefix = self::getCachePrefix();
        return $sPrefix . 'user-bank-card-count' . $iUserId;
    }

    public function flushUserBankCardCount() {
        $key = self::createUserBankCardCountCacheKey($this->user_id);
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        !Cache::has($key) or Cache::forget($key);
    }

}
