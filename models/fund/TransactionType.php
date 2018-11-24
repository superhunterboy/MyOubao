<?php
/**
 * 账变类型
 */
class TransactionType extends BaseModel {
    const TYPE_DEPOSIT                 = 1;
    const TYPE_WITHDRAW                = 2;
    const TYPE_TRANSFER_IN             = 3;
    const TYPE_TRANSFER_OUT            = 4;
    const TYPE_FREEZE_FOR_TRACE        = 5;
    const TYPE_UNFREEZE_FOR_BET        = 6;
    const TYPE_BET                     = 7;
    const TYPE_DROP                    = 8;
    const TYPE_FREEZE_FOR_WITHDRAWAL   = 9;
    const TYPE_UNFREEZE_FOR_WITHDRAWAL = 10;
    const TYPE_SEND_PRIZE              = 11;
    const TYPE_CANCEL_PRIZE            = 12;
    const TYPE_SEND_COMMISSION         = 13;
    const TYPE_CANCEL_COMMISSION       = 14;
    const TYPE_UNFREEZE_FOR_TRACE      = 15;
    const TYPE_DEPOSIT_FEE_BACK        = 16;
    const TYPE_WITHDRAW_FEE            = 17;
    const TYPE_DEPOSIT_BY_ADMIN        = 18;
    const TYPE_WITHDRAW_BY_ADMIN       = 19;
    const TYPE_SEND_BONUS              = 20;
    const TYPE_CANCEL_BONUS            = 21;
    const TYPE_SETTLING_CLAIMS         = 22;
    const TYPE_PROMOTIANAL_BONUS       = 23;
    const TYPE_DEPOSIT_COMMISSION      = 24;
    const TYPE_TURNOVER_COMMISSION     = 25;
    const TYPE_PROFIT_COMMISSION       = 26;
    const TYPE_VIOLATION_CLAIMS        = 27;
    const TYPE_BET_COMMISSION          = 28;
    const TYPE_FREEZE_FOR_GUARANTEE    = 29;
    const TYPE_GROUP_BUY_BONUS        = 30;
    const TYPE_UNFREEZE_FOR_GUARANTEE = 31;

    const TYPE_PROMOTIANAL_BONUS_FOR_ELECTRONIC = 32;
    const TYPE_PROMOTIANAL_BONUS_FOR_SPORT = 33;
    
    const TYPE_PRIZE_OVER_LIMIT = 34;
    
    const TYPE_VOUCHER_DEPOSIT = 35;
    const TYPE_DEPOSIT_BY_ADMIN_FOR_LOSS = 36;
    
    const TYPE_ACTIVITY_PROMOTION = 39;

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'transaction_types';
    protected $softDelete = false;
    protected $fillable = [
        'id',
        'parent_id',
        'fund_flow_id',
        'description',
        'cn_title',
        'balance',
        'available',
        'frozen',
        'withdrawable',
        'credit',
        'debit',
        'project_linked',
        'trace_linked',
        'reverse_type'
    ];

    public static $resourceName = 'TransactionType';
    public static $titleColumn = 'description';

    public static $ignoreColumnsInEdit = [
        'balance',
        'available',
        'frozen',
//        'withdrawable',
    ];
    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'description',
        'cn_title',
        'fund_flow_id',
        'balance',
        'available',
        'frozen',
        'withdrawable',
        'credit',
        'debit',
        'project_linked',
        'trace_linked',
        'reverse_type'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'fund_flow_id' => 'aFundFlows',
        'balance' => 'aFundActions',
        'available' => 'aFundActions',
        'frozen' => 'aFundActions',
//        'withdrawable' => 'aFundActions',
        'reverse_type' => 'aTransactionTypes'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'credit' => 'desc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'parent_id';

    public static $rules = [
        'description'    => 'required|max:30',
        'cn_title'      => 'required|max:30',
        'fund_flow_id'   => 'required|integer',
        'reverse_type'  => 'integer',
//        'balance'        => 'required|in:1,0,-1',
//        'available'      => 'required|in:1,0,-1',
//        'frozen'         => 'required|in:1,0,-1',
        'withdrawable'   => 'required|integer',
        'credit'         => 'in:0,1',
        'debit'          => 'in:0,1',
        'project_linked' => 'in:0,1',
        'trace_linked'    => 'in:0,1',
    ];

    protected function beforeValidate(){
        if (!$this->fund_flow_id){
            return false;
        }
        if (!$oFundFlow = FundFlow::find($this->fund_flow_id)){
            return false;
        }
        $this->balance = $oFundFlow->balance;
        $this->available = $oFundFlow->available;
        $this->frozen = $oFundFlow->frozen;
//        $this->withdrawable = $oFundFlow->withdrawable;
        $this->credit or $this->credit = 0;
        $this->debit or $this->debit = 0;
        $this->reverse_type or $this->reverse_type = null;
        return parent::beforeValidate();
    }

    public static function getAllTransactionTypes()
    {
        $aColumns = ['id', 'description','cn_title'];
        $aTransactionTypes = self::all($aColumns);
        return $aTransactionTypes;
    }

    public static function getAllTransactionTypesArray()
    {
        $data = [];
        $aTransactionTypes = self::getAllTransactionTypes();
        foreach ($aTransactionTypes as $oTransactionType) {
            $data[$oTransactionType->id] = $oTransactionType->description;
        }
        return $data;
    }

    public static function getFieldsOfAllTransactionTypesArray()
    {
        $data = [];
        $aTransactionTypes = self::getAllTransactionTypes();
        foreach ($aTransactionTypes as $oTransactionType) {
            $data[$oTransactionType->id] = $oTransactionType->cn_title;
        }
        return $data;
    }

    protected function getFriendlyDescriptionAttribute(){
        return __('_transactiontype.' . strtolower(Str::slug($this->attributes[ 'description' ])));
    }
    protected function getTransactionTypeByTypeId($type_id){
        if($data=self::find($type_id)){
            return $data;
        }
        return false;

    }
}