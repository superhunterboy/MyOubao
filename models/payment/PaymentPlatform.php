<?php

class PaymentPlatform extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_NONE;

    const STATUS_NOT_AVAILABLE = 0;
    const STATUS_AVAILABLE_FOR_TESTER = 1;
    const STATUS_AVAILABLE_FOR_NORMAL_USER = 2;
    const STATUS_AVAILABLE = 3;
    const PAY_TYPE_NET_BANK = 0;
    const PAY_TYPE_WEIXIN = 1;
    const PAY_TYPE_ZFB = 2;
    const PAY_TYPE_ALL = 3;
    const PAY_TYPE_CFT = 4;

    public static $aPayTypes = [
        self::PAY_TYPE_NET_BANK => '网银',
        self::PAY_TYPE_WEIXIN => '微信',
        self::PAY_TYPE_ZFB => '支付宝',
        self::PAY_TYPE_CFT => '财付通',
    ];
    public static $aIconTypes = [
        self::PAY_TYPE_NET_BANK => 'quick',
        self::PAY_TYPE_WEIXIN => 'weixin',
        self::PAY_TYPE_ZFB => 'alipay',
        self::PAY_TYPE_CFT => 'caifutong',
    ];

    /**
     * 支付平台实例
     * @var object
     */
    public $platform;
    protected $table = 'payment_platforms';
    public static $resourceName = 'PaymentPlatform';
    public static $validStatus = [
        self::STATUS_NOT_AVAILABLE => 'Closed',
        self::STATUS_AVAILABLE_FOR_TESTER => 'Testing',
//        self::STATUS_AVAILABLE_FOR_NORMAL_USER => 'Available',
        self::STATUS_AVAILABLE => 'Available'
    ];
    public static $htmlSelectColumns = [
        'status' => 'aValidStatus',
        'icon' => 'aIconTypes',
    ];
    public static $columnForList = [
        'id',
        'name',
        'display_name',
        'web',
        'query_enabled',
        'status',
        'is_show_pc',
        'is_show_mobile',
        'is_default',
        'is_mobile_default',
    ];
    public static $listColumnMaps = [
        'is_default' => 'is_default_formatted',
        'query_enabled' => 'query_enabled_formatted',
        'status' => 'status_formatted',
    ];
    protected $fillable = [
        'identifier',
        'name',
        'icon',
        'display_name',
        'web',
        'ip',
        'load_url',
        'test_load_url',
        'charset',
        'return_url',
        'notify_url',
        'unload_url',
        'query_enabled',
        'query_url',
        'relay_load_url',
        'relay_query_url',
        'min_load',
        'max_load',
        'check_ip',
        'query_on_callback',
        'need_bank',
        'status',
        'is_self',
        'is_show_pc',
        'is_show_mobile',
        'is_default',
        'is_mobile_default',
    ];
    public static $rules = [
        'identifier' => 'required|max:16',
        'name' => 'required|max:50',
        'display_name' => 'required|max:50',
        'is_self' => 'required|integer|in:0,1',
        'is_default' => 'required|integer|in:0,1',
        'is_mobile_default' => 'required|integer|in:0,1',
        'is_show_pc' => 'required|integer|in:0,1',
        'is_show_mobile' => 'required|integer|in:0,1',
        'icon' => 'required|in:0,1,2,3,4,5',
        'min_load' => 'required|integer',
        'max_load' => 'required|integer',
        'web' => 'max:200',
        'ip' => 'max:200',
        'relay_load_url' => 'max:200',
        'load_url' => 'required|max:200',
        'test_load_url' => 'max:200',
        'return_url' => 'required|max:200',
        'notify_url' => 'required|max:200',
        'relay_load_url' => 'max:200',
        'relay_query_url' => 'max:200',
        'charset' => 'max:10',
        'unload_url' => 'max:200',
        'query_url' => 'max:200',
        'check_ip' => 'integer|in:0,1',
        'query_on_callback' => 'integer|in:0,1',
        'query_enabled' => 'integer|in:0,1',
        'need_bank' => 'integer|in:0,1',
        'status' => 'integer|in:0,1,2,3,4'
    ];
    public static $sequencable = false;
    public $orderColumns = [
        'name' => 'asc',
    ];
    public static $mainParamColumn = 'name';
    public static $titleColumn = 'name';

    public static function getObject($sIdentifier) {
        $oOriginPayment = self::where('identifier', '=', $sIdentifier)->get()->first();
        $sClass = 'Payment' . strtoupper($sIdentifier);
        $oOriginPayment->platform = new $sClass;
        return $oOriginPayment;
    }

    public function getLoadUrl($oPaymentAccount) {
        return $oPaymentAccount->relay_load_url ? $oPaymentAccount->relay_load_url : $this->load_url;
    }

    public function getQueryUrl($oPaymentAccount) {
        return $oPaymentAccount->relay_query_url ? $oPaymentAccount->relay_query_url : $this->query_url;
    }

    public function & compileInputData($oPaymentAccount, $oDeposit, $oBank, & $sSafeStr) {
        return $this->platform->compileInputData($this, $oPaymentAccount, $oDeposit, $oBank, $sSafeStr);
    }

    public function compileSign($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        return $this->platform->compileSign($oPaymentAccount, $aInputData, $aNeedKeys);
    }

    public function compileSignReturn($oPaymentAccount, $aInputData, $aNeedKeys = []) {
        return $this->platform->compileSignReturn($oPaymentAccount, $aInputData, $aNeedKeys);
    }

    public function queryFromPlatform($oPaymentAccount, $sOrderNo, $sServiceOrderNo = null, & $aResonses) {
        return $this->platform->queryFromPlatform($this, $oPaymentAccount, $sOrderNo, $sServiceOrderNo, $aResonses);
    }

    private static function _getStatusArray($iNeedStatus) {
        $aStatus = [];
        foreach (static::$validStatus as $iStatus => $sTmp) {
            if (($iStatus & $iNeedStatus) == $iNeedStatus) {
                $aStatus[] = $iStatus;
            }
        }
        return $aStatus;
    }

    public static function getAvailabelPlatforms($iStatus, $aColumn = [], $sSource = 'pc') {
        $aStatus = self::_getStatusArray($iStatus);
        $oQuery = self::whereIn('status', $aStatus)->orderBy('sequence');
        if ($sSource == 'pc') {
            $oQuery = $oQuery->where('is_show_pc', '=', 1);
        } else if ($sSource == 'mobile') {
            $oQuery = $oQuery->where('is_show_mobile', '=', 1);
        }
        $oQuery->orderBy('icon', 'asc');
        if ($aColumn) {
            return $oQuery->get($aColumn);
        } else {
            return $oQuery->get();
        }

//        $oQuery = $bContainTest ? self::where('status', '<>', self::STATUS_CLOSED) : self::where('status','=',self::STATUS_AVAILABLE);
//        return $oQuery->orderBy('sequence','asc')->get();
    }

    protected function getAvailableIpAttribute() {
        return explode(',', $this->attributes['ip']);
    }

    public function setDefault() {
        $bSucc = self::where('id', '=', $this->id)->update(['is_default' => 1]) && self::where('id', '<>', $this->id)->update(['is_default' => 0]) && self::setDefaultPlatformCache($id);
    }

    public function setDefaultPlatformIdCache() {
        $key = self::comaileDefaultPlatformIdCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        Cache::forever($key, $this->id);
        return true;
    }

    public static function getDefaultPlatformId() {
        $bWriteCache = $bReadDb = false;
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            $bReadDb = true;
        } else {
            $key = self::comaileDefaultPlatformIdCacheKey();
            if (!Cache::has($key)) {
                $bReadDb = true;
                $bWriteCache = true;
            } else {
                $iDefaultId = Cache::get($key);
//                $oPlatform = self::find($iDefaultId);
            }
        }
        if ($bReadDb) {
            $oPlatform = self::where('is_default', '=', 1)->first();
            if (is_object($oPlatform)) {
                $iDefaultId = $oPlatform->getAttribute('id');
            } else {
                $iDefaultId = self::where('status', '=', PaymentPlatform::STATUS_AVAILABLE)->first()->getAttribute('id');
            }
        }
        !$bWriteCache or Cache::forever($key, $iDefaultId);
        return $iDefaultId;
    }

    public static function deleteDefaultPlatformIdCache() {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return;
        }
        $key = self::comaileDefaultPlatformIdCacheKey();
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);
        Cache::has($key) && Cache::forget($key);
    }

    public static function getDefaultPlatform() {
        return self::find(self::getDefaultPlatformId());
    }

    private static function comaileDefaultPlatformIdCacheKey() {
        return 'default-payment-platform-id';
    }

    protected function afterSave($oSavedModel) {
        self::deleteDefaultPlatformIdCache();
        $bSucc = true;
        if ($this->is_default == 1) {
            $bSucc = self::where('id', '<>', $this->id)->where('is_default', '=', 1)->update(['is_default' => 0]);
        }
        return !$bSucc or parent::afterSave($oSavedModel);
    }

    protected function getIsDefaultFormattedAttribute() {
        return __('_basic.' . Config::get('var.boolean')[$this->attributes['is_default']]);
    }

    protected function getQueryEnabledFormattedAttribute() {
        return __('_basic.' . Config::get('var.boolean')[$this->attributes['query_enabled']]);
    }

    protected function getStatusFormattedAttribute() {
        return __('_paymentplatform.' . static::$validStatus[$this->attributes['status']]);
    }

    public function addCallBackHistory(& $data, $ip) {
//        return $this->platform->addCallBackHistory($data, $ip);
        $aData = $this->platform->compileCallBackData($data, $ip);
        $aData['platform_id'] = $this->id;
        $aData['platform'] = $this->name;
        $aData['platform_identifier'] = $this->identifier;
        $oDepositCallback = new DepositCallback($aData);
        if ($oDepositCallback->save()) {
            return $oDepositCallback;
        } else {
            pr($oDepositCallback->validationErrors->toArray());
        }
        return false;
//        $aData = self::compileCallBackData($data, $ip);
//        $oDepositCallback = new DepositCallback($aData);
//        return $oDepositCallback->save();
    }

}
