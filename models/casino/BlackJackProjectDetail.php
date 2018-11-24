<?php

class BlackJackProjectDetail extends Project{
    
    protected $table = 'casino_project_details';
    static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'parent_project_id',
        'username',
        'lottery_id',
        'table_id',
        'stage_id',
        'method_id',
        'way_id',
        'banker_number',
        'player_number',
        'prize',
        'amount',
        'serial_number',
        'status',
        'status_commission',
        'status_prize',
        'created_at',
        'updated_at',
        'is_tester',
    ];
    public static $listColumnMaps = [
        'status' => 'friendly_status',
        'method_id'=> 'friendly_method',
        'way_id'=> 'friendly_way',
        'status_prize'=>'friendly_status_prize',
        'banker_number'=>'friendly_banker_number',
    ];

    protected $fillable = [
        'parent_project_id',
        'user_id',
        'username',
        'lottery_id',
        'table_id',
        'stage_id',
        'account_id',
        'way_id',
        'method_id',
        'player_number',
        'prize',
        'amount',
        'serial_number',
        'status',
        'status_commission',
        'status_prize',
        'bought_at',
        'is_tester',
    ];
    
    
    public static $rules = [
        'user_id' => 'required|integer',
        'parent_project_id'=>'required|integer',
        'username'=>'required',
        'lottery_id' => 'required|integer',
        'table_id'=> 'required|integer',
        'stage_id'=> 'required|integer',
        'account_id' => 'integer',
        'way_id' => 'required|integer',
        'method_id'=>'required|integer',
        'player_number' => '',
        'prize' => '',
        'amount' => 'regex:/^[\d]+(\.[\d]{0,6})?$/',
        'status' => 'in:0,1,2,3',
        'canceled_at' => 'date_format:Y-m-d H:i:s',
        'serial_number' => 'required|max:32',
        'status' => 'in:0,1,2,3,4,5',
        'status_commission' => '',
        'status_prize' => '',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];
    
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'amount' => 2,
        'commission' => 2,
    ];

    const STATUS_NORMAL = 0;
    const STATUS_DROPED = 1;
    const STATUS_LOST = 2;
    const STATUS_WON = 3;

    const STATUS_PRIZE_SENT = 4;
    const STATUS_DROPED_BY_SYSTEM = 5;


    const DROP_BY_USER = 1;
    const DROP_BY_ADMIN = 2;
    const DROP_BY_SYSTEM = 3;
    const COMMISSION_STATUS_WAITING = 0;
    const COMMISSION_STATUS_SENDING = 1;
    const COMMISSION_STATUS_PARTIAL = 2;
    const COMMISSION_STATUS_SENT = 4;
    const PRIZE_STAUTS_NORMAL=0;
    const PRIZE_STATUS_WAITING = 1;
    const PRIZE_STATUS_SENDING = 2;
    const PRIZE_STATUS_PARTIAL = 3;
    const PRIZE_STATUS_SENT = 4;

    public static $validStatuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_DROPED => 'Canceled',
        self::STATUS_LOST => 'Lost',
        self::STATUS_WON => 'Counted',
        self::STATUS_PRIZE_SENT => 'Prize Sent',
        self::STATUS_DROPED_BY_SYSTEM => 'Canceled By System'
    ];
    public static $commissionStatuses = [
        self::COMMISSION_STATUS_WAITING => 'Waiting',
        self::COMMISSION_STATUS_SENDING => 'Sending',
        self::COMMISSION_STATUS_PARTIAL => 'Partial',
        self::COMMISSION_STATUS_SENT => 'Done',
    ];
    public static $prizeStatuses = [
        self::PRIZE_STAUTS_NORMAL => 'Normal',
        self::PRIZE_STATUS_WAITING => 'Waiting',
        self::PRIZE_STATUS_SENDING => 'Sending',
        self::PRIZE_STATUS_PARTIAL => 'Partial',
        self::PRIZE_STATUS_SENT => 'Done',
    ];

    const CASINO_BET_ERROR=-101;

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);

    }

    public function addProject($bNeedIP = true){
        if ($this->Account->available < $this->amount) {
            return self::ERRNO_BET_ERROR_LOW_BALANCE;
        }

        if (!$this->save(BlackJackProjectDetail::$rules)) {
            return self::ERRNO_BET_ERROR_SAVE_ERROR;
        }

        $aExtraData = $this->getAttributes();
        $aExtraData['project_id'] = $this->id;
        $aExtraData['project_no'] = $this->serial_number;
        $aExtraData['coefficient'] = 1.00;
        $aExtraData['issue']=0;
        unset($aExtraData['id']);

        $iReturn = Transaction::addTransaction($this->User, $this->Account, TransactionType::TYPE_BET, $this->amount, $aExtraData);

        if($iReturn == Transaction::ERRNO_CREATE_SUCCESSFUL){
            $iReturn = self::ERRNO_BET_SUCCESSFUL;
        }
        return $iReturn;
    }
    public static function getList($aConditions, $iPageSize = 15, $aColumns = ['*']){
        return self::doWhere($aConditions)->orderby('id', 'desc')->paginate($iPageSize, $aColumns);
    }
    static function getProjects($aCondition=array(),$aColumns=['*']){
        return self::doWhere($aCondition)->get($aColumns);
    }

    static function getProjectByMainId($mainId, $aColumns=['*']){
        return self::where('parent_project_id',$mainId)->get($aColumns);
    }
    public static function makeSeriesNumber($iUserId) {
        return md5($iUserId . microtime(true) . mt_rand());
    }
    protected function beforeValidate() {

        return true;
    }
    public static function updateProject($id,$arr){

    }

    static public function checkeAllProjectFinished($manPid){
        return self::where('parent_project_id',$manPid)->where('status',self::STATUS_NORMAL)->whereIn('status_prize',array(self::PRIZE_STAUTS_NORMAL,self::PRIZE_STATUS_WAITING))->get()->count();
    }

    protected function getMethodTitleAttribute(){
        $method = CasinoMethod::find($this->method_id);

        return $method->name;
    }
    protected function getWayTitleAttribute(){
        $way = CasinoWay::find($this->way_id);
        return $way->name;
    }
    protected  function getGameTitleAttribute(){
        $oLottery = CasinoLottery::find($this->lottery_id);
        $oTable = CasinoTable::find($this->table_id);
        return $oLottery->name.$oTable->table_name.$this->stage_id.'号台';
    }
    protected function getFriendlyStatusAttribute() {
        return __('_blackjackprojectdetail.' . self::$validStatuses[$this->status]);
    }
    protected function getFriendlyStatusPrizeAttribute(){
        return __('_blackjackprojectdetail.' . self::$prizeStatuses[$this->status_prize]);
    }
    protected function getFriendlyMethodAttribute(){
        return $this->getMethodTitleAttribute();
    }
    protected function getFriendlyWayAttribute(){
        return $this->getWayTitleAttribute();
    }
    protected function getFriendlyBankerNumberAttribute(){

        return BlackJackProject::where('id',$this->parent_project_id)->first(array('banker_number'))->banker_number;
    }

}