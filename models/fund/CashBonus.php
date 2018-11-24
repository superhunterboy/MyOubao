<?php

/**
 * Description of Bonus
 *
 * @author abel
 */
class CashBonus extends BaseModel {
    //0.待处理 1.审核不通过 2.审核通过 4.系统过滤
    const STATUS_WAITING_AUDIT = 0;
    const STATUS_AUDIT_REJECT = 1;
    const STATUS_AUDIT_FINISH = 2;
    //const STATUS_BONUS_SENT = 3;
    const STATUS_SYS_DEL = 4;

    const VOUCHER_ID=1001;
    const TOP_AGENT = 0;
    const NORMAL_AGENT = 1;

    protected $table = 'event_register_bonus';
    public static $resourceName = 'CashBonus';
    public static $treeable = false;
    public static $sequencable = false;
    protected $softDelete = false;
    public static $aStatus = [
        self::STATUS_WAITING_AUDIT => 'waiting audit',
        self::STATUS_AUDIT_REJECT => 'rejected',
        self::STATUS_AUDIT_FINISH => 'audited',
      //  self::STATUS_BONUS_SENT => 'bonus sent',
        self::STATUS_SYS_DEL => 'sys del',
    ];
    public static $aAgentLevel = [
        self::TOP_AGENT => 'top agent',
        self::NORMAL_AGENT => 'agent',
    ];
    public static $columnForList = [
        'username',
        'phone',
        'register_ip',
        'status_register',
        'bank_card',
        'created_at',
        'note',
    ];
    public static $ableEdit = [
        'note'=>'text',
    ];
    public static $noOrderByColumns = [
        'bank_card',
    ];
    public static $listColumnMaps = [
        'status_register' => 'friendly_status',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'phone',
        'register_ip',
        'status_register',
        'status_deposit',
        'created_at',
        'updated_at',

    ];
    public static $rules = [
        'user_id'=>'required|integer',
        'username'=>'required',
        'phone'=>'required',
        'register_ip'=>'required',
        'status_register'=>'',
        'status_deposit'=>'',
        'created_at'=>'required',
        'updated_at'=>'required',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'status_register' => 'aStatus',
        'status_deposit'=>'aStatus',
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

        return __('_cashbonus.' . self::$aStatus[$this->status_register]);
    }
    protected function getFriendlyDepositStatusAttribute() {
        return __('_cashbonus.' . self::$aStatus[$this->status_deposit]);
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
    static public function getRepeatBonus($user_name,$phone,$ip,$except_id){
        $oQuery  = self::where('status_register',CashBonus::STATUS_WAITING_AUDIT);
        $oQuery ->whereRaw('(username=\''.$user_name.'\' || phone=\''.$phone.'\' || register_ip=\''.$ip.'\') and id <> '.$except_id);
        $aBonuses  = $oQuery->get(['id']);
        $bonusIds = [];
        foreach($aBonuses as $bonus ){
            $bonusIds[]=$bonus->id;
        }
        return $bonusIds;
    }
    static public function updateByIds($ids,$update){
        $iSucc = false;
        if(!empty($ids)){
            $iSucc = self::whereIn('id', $ids)->where('status_register',CashBonus::STATUS_WAITING_AUDIT)->update($update);
        }
        return $iSucc;
    }
    static public function setRepeatBonus($user_name,$phone,$ip,$except_id){
        $ids = self::getRepeatBonus($user_name,$phone,$ip,$except_id);
        if(!empty($ids)){
            $update = array('status_register'=>CashBonus::STATUS_SYS_DEL);
            self::updateByIds($ids,$update);
        }

    }
}
