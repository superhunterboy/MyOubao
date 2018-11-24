<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcProject extends \BaseModel {
    public static $resourceName = 'JcProject';
    protected $table = 'jc_projects';
    public static $amountAccuracy = 2;

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    protected $fillable = [
        'lottery_id',
        'user_id',
        'username',
        'account_id',
        'is_tester',
        'bet_id',
        'group_id',
        'method_group_id',
        'serial_number',
        'coefficient',
        'amount',
        'prize',
        'status',
        'prize_status',
        'commission_status',
        'type',
        'cancelled_by',
        'is_system',
        'buy_type',
        'created_at',
    ];
    
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id'              => 'required|integer',
        'bet_id'             => 'integer',
        'group_id'             => 'integer',
        'method_group_id'  => 'integer',
        'serial_number' => 'required',
        'multiple'              => 'integer',
        'coefficient' => 'in:1.00,0.10,0.01',
        'is_system' => 'in:0,1',
        'amount' => 'required|numeric|min:0',
        'prize'              => 'numeric|min:0',
        'status'              => 'integer',
        'prize_status'      => 'integer',
        'commission_status'   => 'integer',
        'type' => 'integer',
        'buy_type' => 'in:0,1,2,3',
    ];

    public static $htmlSelectColumns = [
        'prize_status' => 'validPrizeStatus',
        'commission_status' => 'validCommissionStatus',
        'type' => 'validType',
        'method_group_id' => 'validMethodGroups',
        'buy_type' => 'validBuyType'
    ];
        
    //public $timestamps = false;
    
    const STATUS_NORMAL             = 0; //正常
    const STATUS_DROPED             = 1; //撤单
    const STATUS_LOST               = 2; //未中奖
    const STATUS_WON                = 3; //已中奖
    const STATUS_PRIZE_SENT         = 4; //已派奖
    const STATUS_DROPED_BY_SYSTEM   = 5; //系统撤单
    const STATUS_CANCELLED   = 8; //取消
    
    const ERRNO_SUCCESSFUL = -10601;
    const ERRNO_SAVE_FAILED = -10602;
    const ERRNO_PROJECT_IS_NOT_EXISTS = -10603;

    const PRIZE_STATUS_NORMAL_CODE = 0; //正常
//    const PRIZE_STATUS_DRAWING_CODE = 1; //开奖
    const PRIZE_STATUS_CALCULATING_CODE = 2; //计奖
    const PRIZE_STATUS_SENDING_CODE = 3; //派奖
    const PRIZE_STATUS_SENDING_FEE_CODE = 4; //派发佣金
    const PRIZE_STATUS_DONE_CODE = 9; //全部完成

    const BUY_TYPE_FIRST = 0;
    const BUY_TYPE_FOLLOW = 1;
    const BUY_TYPE_GUARANTEE = 2;
    const BUY_TYPE_SYSTEM = 3;
    
    public static $validStatuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_DROPED => 'Droped',
        self::STATUS_LOST => 'Lost',
        self::STATUS_WON => 'Won',
        self::STATUS_PRIZE_SENT => 'Prize Sent',
        self::STATUS_DROPED_BY_SYSTEM => 'Droped By System',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    public static $validPrizeStatus = [
        self::PRIZE_STATUS_NORMAL_CODE => 'normal',
        self::PRIZE_STATUS_CALCULATING_CODE => 'calculating',
        self::PRIZE_STATUS_SENDING_CODE => 'sending',
        self::PRIZE_STATUS_SENDING_FEE_CODE => 'fee sent',
        self::PRIZE_STATUS_DONE_CODE => 'done',
    ];

    public static $validCommissionStatuses = [
        self::COMMISSION_STATUS_CALCULATED => 'Calculated',
        self::COMMISSION_STATUS_NORMAL => 'Normal',
        self::COMMISSION_STATUS_SENT => 'Sent'
    ];

    public static $validType = [
        self::TYPE_SELF_BUY => 'self buy',
        self::TYPE_GROUP_BUY => 'group buy',
        self::TYPE_GROUP_BUY_FOLLOW => 'group buy follow'
    ];

    const COMMISSION_STATUS_NORMAL = 0;
    const COMMISSION_STATUS_CALCULATED = 1;
    const COMMISSION_STATUS_SENT = 2;

    const TYPE_SELF_BUY = 1;
    const TYPE_GROUP_BUY = 2;
    const TYPE_GROUP_BUY_FOLLOW = 3;

    public static $validBuyType = [
        self::BUY_TYPE_FIRST => 'first',
        self::BUY_TYPE_FOLLOW => 'follow',
        self::BUY_TYPE_GUARANTEE => 'guarantee',
        self::BUY_TYPE_SYSTEM => 'system'
    ];

    /**
     * User
     * @var User|Model
     */
    protected $User;

    /**
     * Account
     * @var Account|Model
     */
    protected $Account;

    protected function beforeValidate(){
        if(!isset($this->attributes['is_system'])){
            if (empty($this->user_id)){
                return false;
            }
            if (empty($this->username)){
                return false;
            }
            if (empty($this->account_id)){
                return false;
            }
        }
        return parent::beforeValidate();
    }

    protected function getDisplayNicknameAttribute($sNickName){
        if (isset($this->attributes['is_system']) && $this->attributes['is_system']){
            return '平台小秘书';
        }
        if (!isset($sNickName)){
            $oUser = \User::find($this->user_id);
            if ($oUser){
                $sNickName = $oUser->display_nickname;
            }
        }
        return $sNickName;
    }

    protected function getFormattedSerialNumberAttribute() {
        return substr($this->attributes['serial_number'], 0, 8) . '...';
    }
    
    protected function getFormattedStatusAttribute() {
        if (isset(self::$validStatuses[$this->attributes['status']])){
            self::comaileLangPack();
            return self::translate(self::$validStatuses[$this->attributes['status']]);
        }
        return '';
    }

    protected function getFormattedPrizeStatusAttribute() {
        if (isset(self::$validPrizeStatus[$this->attributes['prize_status']])){
            self::comaileLangPack();
            return self::translate(self::$validPrizeStatus[$this->attributes['prize_status']]);
        }
        return '';
    }

    protected function getFormattedCommissionStatusAttribute() {
        if (isset(self::$validCommissionStatuses[$this->attributes['commission_status']])){
            self::comaileLangPack();
            return self::translate(self::$validCommissionStatuses[$this->attributes['commission_status']]);
        }
        return '';
    }

    protected function getFormattedTypeAttribute() {
        if (isset(self::$validType[$this->attributes['type']])){
            self::comaileLangPack();
            return self::translate(self::$validType[$this->attributes['type']]);
        }
        return '';
    }

    protected function getFormattedBuyTypeAttribute() {
        if (isset(self::$validBuyType[$this->attributes['buy_type']])){
            self::comaileLangPack();
            return self::translate(self::$validBuyType[$this->attributes['buy_type']]);
        }
        return '';
    }

    protected function getFormattedIsSystemAttribute() {
        if (isset($this->attributes['is_system'])){

            return $this->attributes['is_system'] ? '是' : '否';
        }
        return '';
    }
    
    protected function getBetSerialNumberAttribute() {
        if ($this->userGroupBuy){
            return $this->userGroupBuy->serial_number;
        }
        if ($this->userBet){
            return $this->userBet->serial_number;
        }
    }
    protected function getAuthorAttribute() {
        if ($this->userGroupBuy){
            return $this->userGroupBuy->display_nickname;
        }
        if ($this->userBet){
            return $this->userBet->display_nickname;
        }
    }
    protected function getTotalAmountAttribute() {
        if ($this->userBet){
            return $this->userBet->amount;
        }
        if ($this->userGroupBuy){
            return $this->userGroupBuy->amount;
        }
    }
    protected function getTotalBuyPercentAttribute() {
        if ($this->userGroupBuy){
            return $this->userGroupBuy->buy_percent;
        }
        if ($this->group_id == 0){
            return '100%';
        }
    }
    protected function getBuyPercentAttribute() {
        if ($this->userBet){
            return sprintf('%01.2f%%', $this->amount / $this->userBet->amount * 100);
        }
        if ($this->userGroupBuy){
            return sprintf('%01.2f%%', $this->amount / $this->userGroupBuy->amount * 100);
        }
    }
    
    public static function getByBetId($iBetId){
        return self::where('bet_id', $iBetId)->first();
    }
    
    public static function getByBetIds($aBetIds = []){
        return self::whereIn('bet_id', $aBetIds)->get();
    }
    
    public function bindBet($oBet){
        $aUpdateArr = [
            'bet_id' => $oBet->id,
            'method_group_id' => $oBet->method_group_id,
            'multiple' => $oBet->multiple,
            'coefficient' => $oBet->coefficient,
            'single_amount' => $oBet->single_amount,
        ];
       $oQuery = self::where('id', $this->id)
                ->where("bet_id", 0)
                ->where("group_id", '>', 0)
                ->where('status', self::STATUS_NORMAL)
                ->update($aUpdateArr);
       $this->deleteCache();
       return $oQuery;
    }

    /**
     * 发放奖金
     * @param array $aData
     * @return boolean
     */
    public function send($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        $fAmount = floor($fAmount * 100) / 100; //保留两位小数
        $aExtraData = $this->getTransactionData($aData);
        
        $iReturn = \Transaction::addTransaction($this->User,$this->Account,\TransactionType::TYPE_SEND_PRIZE,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    public function sendCommission($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        $aExtraData = $this->getTransactionData($aData);
        $iTransactionType = $this->user_id == $this->User->id ? \TransactionType::TYPE_BET_COMMISSION : \TransactionType::TYPE_SEND_COMMISSION;
        
        $iReturn = \Transaction::addTransaction($this->User,$this->Account,$iTransactionType,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    /**
     * 下注
     * @param array $aData
     * @return boolean
     */
    public function bet($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        if ($this->Account->available < $fAmount){
            return false;
        }
        $aExtraData = $this->getTransactionData($aData);

        $iReturn = \Transaction::addTransaction($this->User, $this->Account,\TransactionType::TYPE_BET,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    /**
     * 撤单
     * @param array $aData
     * @return boolean
     */
    public function drop($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        $aExtraData = $this->getTransactionData($aData);

        $iReturn = \Transaction::addTransaction($this->User, $this->Account,\TransactionType::TYPE_DROP,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    public function freezeForGuarantee($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        if ($this->Account->available < $fAmount){
            return false;
        }
        $aExtraData = $this->getTransactionData($aData);

        $iReturn = \Transaction::addTransaction($this->User,$this->Account,\TransactionType::TYPE_FREEZE_FOR_GUARANTEE,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    public function unfreezeForBet($aData = []){
        $fAmount = $aData['amount'];
        if ($fAmount <= 0){
            return false;
        }
        $aExtraData = $this->getTransactionData($aData);

        $iReturn = \Transaction::addTransaction($this->User,$this->Account,\TransactionType::TYPE_UNFREEZE_FOR_GUARANTEE,$fAmount,$aExtraData);
        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
    }
    
    /**
     * set Account Model
     * @param Account $oAccount
     */
    public function setAccount($oAccount){
        if ($oAccount->id == $this->account_id){
            $this->Account = $oAccount;
        }
    }

    /**
     * set User Model
     * @param User $oUser
     */
    public function setUser($oUser){
        if ($oUser->id == $this->user_id){
            $this->User = $oUser;
        }
    }
    
    public function getTransactionData($aExtraData = []){
        $aData = [
            'lottery_id' => $this->lottery_id,
            'coefficient' => $this->coefficient,
            'issue' => '',
//            'way_id' => 0,
        ];
        $aExtraData = array_merge($aData, $aExtraData);
        $aExtraData[ 'project_id' ] = $this->id;
        $aExtraData[ 'project_no' ] = $this->serial_number;
        $aExtraData[ 'way_id' ] = $this->method_group_id;
        return $aExtraData;
    }
    
    /**
     * 生成序列号
     * @param int $iUserId
     * @return string
     */
    public static function makeSeriesNumber($iUserId){
        return substr('JC'.  strtoupper(md5($iUserId . microtime(true) . mt_rand())), 0, 32);
    }
    
    /**
     * 撤单
     * @return type
     */
    public function doDrop(){
        if ($this->bet_id > 0){
            $oBet = JcBet::find($this->bet_id);
            if ($oBet->isEnd){
                return false;
            }
        }
        if ($this->group_id > 0){
            if ($this->buy_type != self::BUY_TYPE_FOLLOW){
                return false;
            }
        }
        $bSucc = $this->setToDroped();
        if ($bSucc){
            $aData = [
              'amount' => $this->amount,  
            ];
            $bSucc = $this->drop($aData);
            if ($bSucc && $this->group_id > 0){
                $oGroup = JcGroupBuy::find($this->group_id);
                $bSucc = $oGroup->decrementBuyAmount($this->amount);
            }
        }
        return $bSucc;
    }
    
    public function setStatus($iFromStatus, $iToStatus, $aUpdateArr = []){
        $aStatus = (array)$iFromStatus;
        $aUpdateArr['status'] = $iToStatus;
        $iRes = self::where('id', '=', $this->id)
                ->whereIn('status', $aStatus)
                ->update($aUpdateArr);
        if ($iRes){
            $this->deleteCache();
            foreach($aUpdateArr as $sKey => $sVal){
                $this->$sKey = $sVal;
            }
        }
        return $iRes;
    }

    public function setCommissionStatus($iFromStatus, $iToStatus, $aUpdateArr = []){
        $aStatus = (array)$iFromStatus;
        $aUpdateArr['commission_status'] = $iToStatus;
        $iRes = self::where('id', '=', $this->id)
            ->whereIn('commission_status', $aStatus)
            ->update($aUpdateArr);
        if ($iRes){
            $this->deleteCache();
            foreach($aUpdateArr as $sKey => $sVal){
                $this->$sKey = $sVal;
            }
        }
        return $iRes;
    }

    public function setToDroped(){
        $aUpdateArr = [
            'cancelled_at' => \Carbon::now()->toDateTimeString(),
        ];
        if (\Session::get('user_id') > 0){
            $aUpdateArr['cancelled_by'] = \Session::get('username');
        }
        if (\Session::get('admin_user_id') > 0){
            $aUpdateArr['cancelled_by'] = \Session::get('admin_username');
        }
        $iFromStatus = self::STATUS_NORMAL;
        $iToStatus = self::STATUS_DROPED;
        return $this->setStatus($iFromStatus, $iToStatus, $aUpdateArr);
    }
    
    public function setToDropedBySystem(){
        $aUpdateArr = [
            'cancelled_at' => \Carbon::now()->toDateTimeString(),
        ];
        $iFromStatus = self::STATUS_NORMAL;
        $iToStatus = self::STATUS_DROPED_BY_SYSTEM;
        return $this->setStatus($iFromStatus, $iToStatus, $aUpdateArr);
    }
    
    public function setToCancelled(){
        $aUpdateArr = [
            'cancelled_at' => \Carbon::now()->toDateTimeString(),
        ];
        $iFromStatus = self::STATUS_NORMAL;
        $iToStatus = self::STATUS_CANCELLED;
        return $this->setStatus($iFromStatus, $iToStatus, $aUpdateArr);
    }
    
    public function setWonLost(){
        $iFromStatus = self::STATUS_NORMAL;
        $iToStatus = $this->prize > 0 ? self::STATUS_WON : self::STATUS_LOST;
        return $this->setStatus($iFromStatus, $iToStatus);
    }
        
    public function setPrizeSent(){
        $aUpdateArr = [
            'sent_at' => \Carbon::now()->toDateTimeString(),
        ];
        $iFromStatus = [self::STATUS_NORMAL, self::STATUS_WON];
        $iToStatus = self::STATUS_PRIZE_SENT;
        return $this->setStatus($iFromStatus, $iToStatus, $aUpdateArr);
    }

    public function setCommissionSent(){
        $aUpdateArr = [
            'sent_at' => \Carbon::now()->toDateTimeString(),
        ];
        $iFromCommissionStatus = [self::COMMISSION_STATUS_NORMAL, self::COMMISSION_STATUS_CALCULATED];
        $iToCommissionStatus = self::COMMISSION_STATUS_SENT;
        return $this->setCommissionStatus($iFromCommissionStatus, $iToCommissionStatus, $aUpdateArr);
    }
    
    public function incrementPrize($prize){
        $oQuery = self::where('id', '=', $this->id)->increment('prize', $prize);
        $this->deleteCache();
        return $oQuery;
    }
    
    public function addProject(){
        if (isset($this->User)){
            $this->user_id = $this->User->id;
            $this->username = $this->User->username;
            $this->account_id = $this->User->account_id;
            $this->is_tester = $this->User->is_tester;
        }else{
            $this->user_id = null;
            $this->username = null;
            $this->account_id = null;
            $this->is_tester = null;
        }
        $this->coefficient = 1.00;
        $this->status = self::STATUS_NORMAL;
//        $this->single_amount = 2 * $this->coefficient;
//        $this->amount = 2 * $this->multiple * $this->total * $this->coefficient;
        $this->serial_number = self::makeSeriesNumber($this->user_id);
//        var_dump($this->getAttributes());
        if ($this->amount <= 0){
            return self::ERRNO_SAVE_FAILED;
        }
        if ($this->save()){
            $aData = $this->getAttributes();
//            $aData['way_id'] = $this->method_group_id;
            if(!isset($aData['is_system']) || !$aData['is_system']){
                if ($this->bet($aData)){
                    return self::ERRNO_SUCCESSFUL;
                }else{
                    return self::ERRNO_SAVE_FAILED;
                }
            }
            return self::ERRNO_SUCCESSFUL;
        }
        
//        var_dump($this->errors()->getMessages());die;
        return self::ERRNO_SAVE_FAILED;

    }
    
    public static function getSumCost($iUserId, $iLotteryId, $sStartTime, $sEndTime){
        return self::where('user_id', $iUserId)
                ->where('lottery_id', $iLotteryId)
                ->where('created_at', '>=' , $sStartTime)
                ->where('created_at', '<=',  $sEndTime)
                ->where('type', self::TYPE_SELF_BUY)
                ->sum('amount');
    }
}
