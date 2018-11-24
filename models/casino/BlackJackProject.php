<?php

class BlackJackProject extends BaseModel    {
    static $cacheLevel = self::CACHE_LEVEL_FIRST;

    protected $table = 'casino_projects';

    public static $amountAccuracy = 6;

    public static $htmlNumberColumns = [
        'amount' => 2,
        'commission' => 2,
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [

        'user_id',
        'username',
        'lottery_id',
        'table_id',
        'banker_number',
        'status',
        'created_at',
        'updated_at',
        'is_tester',
        'auto_finished'

    ];
    public static $listColumnMaps = [
        'status' => 'friendly_status',
        'auto_finished'=> 'friendly_auto_finished',
        'user_id'=>'friendly_user_id',
        'is_tester'=>'friendly_is_tester',
    ];

    protected $fillable = [
        'user_id',
        'username',
        'lottery_id',
        'table_id',
        'banker_number',
        'auto_finished',
        'status',
        'is_tester',
    ];
    public static $rules = [
        'user_id' => 'integer',
        'lottery_id' => 'integer',
        'table_id' => 'integer',
        'banker_number' => '',
        'game_info' => '',
        'status' => 'in:0,1,2,3,4,5',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
        'status' => 'aStatusDesc',
        'table_id' => 'aTables',
        'is_tester'=>'isTest',
    ];
    
    const STATUS_NORMAL = 0;
    const STATUS_FINISHE = 1;
    const STATUS_FINISH_BY_AUTO=2;
    const STATUS_DROPED = 3;
    public static $isTest = [0=>'test_no',1=>'test_yes'];
    
    public static $validStatuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_FINISHE => 'Finished',
        self::STATUS_FINISH_BY_AUTO => 'Fnished By Auto',
        self::STATUS_DROPED => 'Canceled'
    ];
    
    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);

    }


   static function createProject(&$aBetData,$aExtra=array()){
       $info = [];
       $info['user_id']=Session::get('user_id');
       $info['username'] = Session::get('username');
       $info['lottery_id']=$aBetData['lotteryId'];
       $info['table_id']=$aBetData['tableId'];
       $info['status']=self::STATUS_NORMAL;
       $info['is_tester'] = Session::get('is_tester')?1:0;
       $info['banker_number']='';

       $iDate = date('Y-m-d H:i:s');
       $info['created_at']=$iDate;
       $info['updated_at']=$iDate;
       $info = array_merge($info,$aExtra);
       $oManProject = new BlackJackProject($info);
       $oManProject->save(BlackJackProject::$rules);

       return $oManProject;
   }

    static function getProjects($aCondition=array(),$aColumns=['*']){
        return self::doWhere($aCondition)->get($aColumns);
    }

    public function setFinished($type,$auto=false){
        if(!in_array($type,array(self::STATUS_NORMAL,self::STATUS_FINISHE,self::STATUS_FINISH_BY_AUTO,self::STATUS_DROPED))){
            return false;
        }

        $data = array('status'=>$type);
        if($auto){
            $data['auto_finished']=1;
        }
        return $this->update($data);
    }
    protected function getFriendlyStatusAttribute() {
        return __('_blackjackproject.' . self::$validStatuses[$this->status]);
    }
    protected  function getFriendlyIsTesterAttribute(){
        if(is_null($this->is_tester)){$this->is_tester=0;}
        return __('_blackjackproject.' . self::$isTest[$this->is_tester]);
    }
    protected function getFriendlyAutoFinishedAttribute(){
        if($this->auto_finished == 1){
            return __('_blackjackproject.autoyes');
        }else{
            return __('_blackjackproject.autono');
        }
    }
    protected  function getFriendlyUserIdAttribute(){
        return User::find($this->user_id)->username;
    }
}