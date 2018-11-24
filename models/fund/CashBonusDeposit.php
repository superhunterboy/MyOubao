<?php

/**
 * Description of Bonus
 *
 */
class CashBonusDeposit extends BaseModel {
    //0.待处理 1.审核不通过 2.审核通过

    const STATUS_DEPOSIT_WAITING_AUDIT=0;
    const STATUS_DEPOSIT_AUDIT_REJECT=1;
    const STATUS_DEPOSIT_AUDIT_FINISH=2;

    const VOUCHER_ID=1002;
    const TOP_AGENT = 0;
    const NORMAL_AGENT = 1;

    protected $table = 'event_register_bonus';
    public static $resourceName = 'CashBonusDeposit';
    public static $treeable = false;
    public static $sequencable = false;
    protected $softDelete = false;

    public static $aStatus = [
        self::STATUS_DEPOSIT_WAITING_AUDIT => 'deposit_waiting_audit',
        self::STATUS_DEPOSIT_AUDIT_REJECT => 'deposit_rejected',
        self::STATUS_DEPOSIT_AUDIT_FINISH=>'deposit_audited',
    ];
    public static $aAgentLevel = [
        self::TOP_AGENT => 'top agent',
        self::NORMAL_AGENT => 'agent',
    ];
    public static $columnForList = [
        'username',
        'phone',
        'register_ip',
        'status_deposit',
        'created_at',
        'updated_at',
        'note',
    ];
    public static $ableEdit = [
        'note'=>'text',
    ];
    public static $listColumnMaps = [
        'status_deposit' => 'friendly_deposit_status',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'phone',
        'register_ip',
        'status_deposit',
        'created_at',
        'updated_at',

    ];
    public static $rules = [
        'user_id'=>'required|integer',
        'username'=>'required',
        'phone'=>'required',
        'register_ip'=>'required',
        'status_deposit'=>'',
        'created_at'=>'',
        'updated_at'=>'',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status_deposit' => 'aStatus',
        'status_register' => 'aStatus',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc',
    ];
    
    protected function getFriendlyStatusAttribute() {
        return __('_bonus.' . self::$aStatus[$this->status_register]);
    }
    protected function getFriendlyDepositStatusAttribute() {
        return __('_cashbonusDeposit.' . self::$aStatus[$this->status_deposit]);
    }
    protected function getFirstDepositAttribute(){
        $aTransaction = UserTransaction::getFirstDepositByUid($this->user_id);
        return number_format($aTransaction ? $aTransaction->amount : 0, 2);
    }
    protected function getNoteAttribute(){
        $aCondition = array('user_id'=>$this->user_id,'voucher_id'=>self::VOUCHER_ID);
        $oBonusSend = CashBonusSend::getBonusByCondition($aCondition)->first();
        if($oBonusSend)
            return $oBonusSend->note;
        else
            return ;
        //return $oBonusSend->note;

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
    static public function getLastRegisterUser(){
        $oQuery = self::selectRaw('MAX(user_id) as maxuserid');
        return $oQuery->first();
    }
}
