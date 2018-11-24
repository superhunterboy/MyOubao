<?php

/**
 * 平台银行卡
 *
 * @author white
 */
class PaymentBankCard extends BaseModel {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'payment_bank_cards';
    public static $sequencable = false;
    protected $softDelete = false;
    public $timestamps = false; // 取消自动维护新增/编辑时间
    protected $fillable = [
        'bank_id',
        'bank',
        'account_no',
        'owner',
        'email',
        'mobile',
        'status',
        'created_at',
        'updated_at',
    ];
    public static $resourceName = 'Bankcard';
    public static $columnForList = [
        'bank',
        'account_no',
        'owner',
        'email',
        'mobile',
        'status'
    ];
    public static $listColumnMaps = [
        'status' => 'formatted_status'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'bank_id' => 'aBanks',
        'status' => 'aValidStatuses'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'bank' => 'asc'
    ];
    public static $titleColumn = 'name';
    public static $ignoreColumnsInEdit = [
        'bank'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'bank';
    public static $rules = [
        'bank_id' => 'required|integer',
        'bank' => 'required|max:50',
        'account_no' => 'required',
        'owner' => 'required|max:30',
        'email' => 'max:100',
        'mobile' => 'max:13',
        'status' => 'integer',
    ];

    /**
     * 状态：可用
     */
    const STATUS_AVAILABLE = 1;

    /**
     * 状态：不可用
     */
    const STATUS_NOT_AVAILABLE = 0;

    public static $validStatuses = [
        self::STATUS_AVAILABLE => 'Available',
        self::STATUS_NOT_AVAILABLE => 'Not Available',
    ];

    protected function beforeValidate() {
        if ($this->bank_id) {
            $oBank = Bank::find($this->bank_id);
            $this->bank = $oBank->name;
        }
        return parent::beforeValidate();
    }

    public static function getAvailableBankcards($iBankId) {
        return self::where('status', '=', self::STATUS_AVAILABLE)->get();
    }

    public static function getBankcardForDeposit($iBankId) {
        $oBanks = self::getAvailableBankcards($iBankId);
        $iCount = $oBanks->count();
        if ($iCount == 0) {
            return false;
        }
        if ($iCount == 1) {
            return $oBanks[0];
        }
        return $oBanks[mt_rand(0, $iCount - 1)];
    }

    public function getFormattedStatusAttribute() {
        return __('_paymentbankcard.' . self::$validStatuses[$this->status]);
    }

}
