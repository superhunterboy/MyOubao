<?php

/**
 * PaymentAccount
 *
 */
class PaymentAccount extends BaseModel {

    const PAY_TYPE_NET_BANK = 0;
    const PAY_TYPE_WEIXIN = 1;
    const PAY_TYPE_ZFB = 2;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'payment_accounts';
    public static $sequencable = false;
    protected $softDelete = true;
    public $timestamps = true; // 取消自动维护新增/编辑时间
    public static $amountAccuracy = 2;
    protected $fillable = [
        'serial_number',
        'platform_id',
        'platform',
        'account',
        'safe_key',
        'ceiling',
        'relay_load_url',
        'relay_query_url',
        'status',
        'is_test',
        'pay_type',
        'is_default',
        'current_total_amount',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'PaymentAccount';
    public static $columnForList = [
        'serial_number',
        'platform',
        'account',
        'is_test',
        'ceiling',
        'status',
        'is_default',
        'current_total_amount',
//        'created_at',
//        'updated_at',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'platform_id' => 'aPlatforms',
        'status' => 'aValidStatuses',
        'pay_type' => 'aPayTypes',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'platform_id' => 'asc',
        'is_default' => 'desc',
        'serial_number' => 'asc',
    ];
    public static $titleColumn = 'serial_number';
    public static $ignoreColumnsInEdit = [
        'platform'
    ];
    public static $ignoreColumnsInView = [
        'safe_key'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'bank';
    public static $rules = [
        'serial_number' => 'required|max:16',
        'platform_id' => 'required|integer',
        'platform' => 'required|max:50',
        'account' => 'required|max:32',
        'safe_key' => 'required|max:1024',
        'ceiling' => 'integer|min:0',
        'relay_load_url' => 'required|max:200',
        'relay_query_url' => 'max:200',
        'status' => 'integer',
        'pay_type' => 'integer|in:0,1,2',
        'is_test' => 'required|in:0,1',
        'is_default' => 'required|in:0,1',
        'current_total_amount' => 'numeric|min:0',
    ];

    /**
     * 状态：可用
     */
    const STATUS_AVAILABLE = 2;

    /**
     * 状态：测试用户可用
     */
    const STATUS_TESTING = 1;

    /**
     * 状态：不可用
     */
    const STATUS_NOT_AVAILABLE = 0;

    public static $validStatuses = [
        self::STATUS_NOT_AVAILABLE => 'closed',
        self::STATUS_TESTING => 'testing',
        self::STATUS_AVAILABLE => 'available',
    ];
    public static $aPayTypes = [
        self::PAY_TYPE_NET_BANK => 'net-bank',
        self::PAY_TYPE_WEIXIN => 'weixin',
        self::PAY_TYPE_ZFB => 'zfb',
    ];

    protected function afterSave($oSavedModel) {
//        self::deleteDefaultPlatformIdCache();
        if ($this->is_default == 1) {
            self::where('platform_id', '=', $this->platform_id)
                    ->where('id', '<>', $this->id)
                    ->where('is_default', '=', 1)
                    ->update(['is_default' => 0]);
        }
        return parent::afterSave($oSavedModel);
    }

    protected function beforeValidate() {
        if ($this->platform_id) {
            $oPlatform = PaymentPlatform::find($this->platform_id);
            $this->platform = $oPlatform->name;
        }
        return parent::beforeValidate();
    }

    public static function getAvailableAccounts($iPlatformId, $bTest = false, $bContainTest = true, $bGetDefault = false) {
        $oQuery = self::where('platform_id', '=', $iPlatformId);
        if ($bTest) {
            $oQuery = $oQuery->where('is_test', '=', 1);
        } else {
            $oQuery = $bContainTest ? $oQuery->where('status', '<>', self::STATUS_NOT_AVAILABLE) : $oQuery->where('status', '=', self::STATUS_AVAILABLE);
            !$bGetDefault or $oQuery = $oQuery->where('is_default', '=', 1);
        }
        return $oQuery->orderBy('id', 'asc')->get();
    }

    public static function getAccountByNoForRF($sAccount) {
        return self::where('account', '=', $sAccount)->get()->first();
    }

    public static function getAccountForDeposit($iPlatformId, $bTest = false, $bContainTest = true, $fAmount = null) {
        $bGetDefault = SysConfig::check('deposit_account_use_default', true);
        $oAccounts = self::getAvailableAccounts($iPlatformId, $bTest, $bContainTest, $bGetDefault);
        if ($bGetDefault) {
            return $oAccounts->count() ? $oAccounts[0] : false;
        }
        $iMinCurrentAmount = 9999999999;
        for ($i = 0, $iIndex = null, $iMinCurrentAmount = 9999999999; $i < count($oAccounts); $i++) {
            $oAccount = $oAccounts[$i];
            if (!$oAccount->ceiling || ($oAccount->current_total_amount + $fAmount <= $oAccount->ceiling)) {
                if ($iMinCurrentAmount > $oAccount->current_total_amount) {
                    $iMinCurrentAmount = $oAccount->current_total_amount;
                    $iIndex = $i;
                }
            }
        }
        return $oAccounts[$iIndex];
//        $aAccounts = [];
//        foreach($oAccounts as $oAccount){
//            if (!$oAccount->ceiling || ($oAccount->current_total_amount  + $fAmount <= $oAccount->ceiling)){
//                $aAccounts[$i++] = $oAccount;
//                if ($iMinCurrentAmount > $oAccount->current_total_amount){
//                    $iMinCurrentAmount = $oAccount->current_total_amount;
//                }
//            }
//        }
//        switch($iCount = count($aAccounts)){
//            case 0:
//                return false;
//            case 1:
//                return $aAccounts[0];
//            default:
//                return $aAccounts[mt_rand(0, $iCount - 1)];
//        }
    }

    public static function getAccountByNo($iPlatformId, $sAccount) {
        return self::where('platform_id', '=', $iPlatformId)->where('account', '=', $sAccount)->get()->first();
    }

    public function updateCurrentTotalAmount($fAmount) {
        $data = [
            'current_total_amount' => $this->current_total_amount + $fAmount
        ];
        return $this->update($data);
    }

    public function resetCurrentTotalAmount($fAmount = 0) {
        $data = [
            'current_total_amount' => $fAmount
        ];
        return $this->update($data);
    }

    public function updateStat($oDeposit) {
        $oPaymentAccountStat = PaymentAccountStat::getObject($this, date('Y-m-d'));
        $oPaymentAccountStat->addAmount($oDeposit->amount);
    }

}
