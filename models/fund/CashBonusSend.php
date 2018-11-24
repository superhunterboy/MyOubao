<?php

/**
 * Description of Bonus
 *
 * @author abel
 */
class CashBonusSend extends BaseModel {
    //0.正常 1.审核不通过 2.审核通过
    const STATUS_NORMAL=0;
    const STATUS_AUDIT_REJECT=1;
    const STATUS_AUDIT_FINISH=2;

    const VOUCHER_REGISTER=1001;
    const VOUCHER_DEPOSIT=1002;
    const TOP_AGENT = 0;
    const NORMAL_AGENT = 1;

    protected $table = 'event_send_bonus';
    public static $resourceName = 'CashBonusSend';
    public static $treeable = false;
    public static $sequencable = false;
    protected $softDelete = false;
    public static $aStatus = [
        self::STATUS_NORMAL => 'normal',
        self::STATUS_AUDIT_REJECT => 'rejected',
        self::STATUS_AUDIT_FINISH => 'audited',
    ];
    public static $aVoucheType = [
        self::VOUCHER_DEPOSIT=>'deposit',
        self::VOUCHER_REGISTER=>'register',
    ];
    public static $aAgentLevel = [
        self::TOP_AGENT => 'top agent',
        self::NORMAL_AGENT => 'agent',
    ];
    public static $columnForList = [
        'username',
        'voucher_id',
        'deposit_amount',
        'admin_username',
        'created_at',
        'status',
        'amount',
        'mincost',
        'note',
    ];
    public static $ableEdit = [
        'note'=>'text',
    ];
    public static $listColumnMaps = [
        'status' => 'friendly_status',
        'voucher_id'=>'friendly_voucher'

    ];
    protected $fillable = [
        'user_id',
        'username',
        'voucher_id',
        'deposit_amount',
        'bonus_type',
        'status',
        'note',
        'admin_id',
        'admin_username',
        'created_at',
        'updated_at',
    ];
    public static $rules = [
        'user_id'=>'required|integer',
        'username'=>'required',
        'voucher_id'=>'required',
        'deposit_amount'=>'',
        'admin_username'=>'',
        'status'=>'',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status'=>'aStatus',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc',
    ];


    protected  function getAStatusAttribute($r){

    }
    protected function getFriendlyStatusAttribute() {
        return __('_cashbonussend.' . self::$aStatus[$this->status]);
    }
    protected function getFriendlyVoucherAttribute(){
        return __('_cashbonussend.' . self::$aVoucheType[$this->voucher_id]);
    }
    protected function getAmountAttribute(){
        $oVoucher = Voucher::find($this->voucher_id);
        $oVoucher->getExtraData($this->deposit_amount);
        return number_format($oVoucher->send_amount ? $oVoucher->send_amount : 0, 2);
    }
    protected function getMincostAttribute(){
        $oVoucher = Voucher::find($this->voucher_id);
        $oVoucher->getExtraData($this->deposit_amount);
        return number_format($oVoucher->min_cost ? $oVoucher->min_cost : 0, 2);
    }


    static public function getBonusByCondition($condition=array(),$fields=['*']){
        if(empty($condition)) return [];
        return self::doWhere($condition)->get($fields);
    }
    static public function changeUserStatus($status=null){
        if(!key_exists($status,self::$aStatus)){
            return false;
        }
        self::$status = $status;
        return  self::update();
    }


}
