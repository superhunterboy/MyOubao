<?php

namespace JcModel;
/**
 * 合买模型
 */
class JcGroupBuy extends \BaseModel {
    public static $resourceName = 'JcGroupBuy';
    protected $table = 'jc_group_buys';
    public static $amountAccuracy = 2;

    protected $fillable = [
        'lottery_id',
        'serial_number',
        'user_id',
        'username',
        'account_id',
        'bet_id',
        'method_group_id',
        'fee_rate',
        'fee_amount',
        'amount',
        'buy_amount',
        'guarantee_amount',
        'show_type',
        'end_time',
        'is_finished',
        'status',
        'prize',
        'prize_status',
        'commission_status',
        'progress',
        'sequence'	,
        'allow_type',
        'created_at',
        'is_tester'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id'             => 'required|integer',
        'user_id'             => 'required|integer',
        'username'      => 'required',
        'account_id'             => 'required|integer',
        'bet_id'             => 'integer',
        'method_group_id'  => 'integer',
        'fee_rate'             => 'numeric|min:0',
        'fee_amount'             => 'numeric|min:0',
        'amount'             => 'required|numeric|min:0',
        'buy_amount'             => 'numeric|min:1',
        'guarantee_amount'             => 'numeric|min:0',
        'show_type'             => 'required|integer',
        'end_time'             => 'date',
        'is_finished'	=> 'in:0,1',
        'status'             => 'integer',
        'prize'             => 'numeric',
        'prize_status'             => 'integer',
        'progress_rate' => 'numeric',
        'sequence'	=> 'integer',
        'allow_type' => 'required|in:0,1',
        'is_tester' => 'required|in:0,1'
    ];

    const COMMISSION_STATUS_NORMAL = 0;
    const COMMISSION_STATUS_CALCULATING = 1;
    const COMMISSION_STATUS_CALCULATED = 2;
    const COMMISSION_STATUS_SENDING = 3;
    const COMMISSION_STATUS_SENT = 4;

    const STATUS_NORMAL             = 0; //正常 认购中
    const STATUS_DROPED             = 1; //撤单
    const STATUS_LOST               = 2; //未中奖
    const STATUS_WON                = 3; //已中奖
    const STATUS_PRIZE_SENT         = 4; //已派奖
    const STATUS_DROPED_BY_SYSTEM   = 5; //系统撤单
    const STATUS_AVAILABLE   = 6; //已满员 待开奖
    const STATUS_CANCELLED = 8;
    
    const PRIZE_STATUS_NORMAL_CODE = 0; //正常
//    const PRIZE_STATUS_DRAWING_CODE = 1; //开奖
    const PRIZE_STATUS_CALCULATING_CODE = 2; //计奖
    const PRIZE_STATUS_SENDING_CODE = 3; //派奖
    const PRIZE_STATUS_SENDING_FEE_CODE = 4; //派发佣金
    const PRIZE_STATUS_DONE_CODE = 9; //全部完成
    
    const SHOW_TYPE_PUBLIC_CODE = 0; //公开
    const SHOW_TYPE_AFTER_FOLLOW_CODE = 1; //跟单即公开
    const SHOW_TYPE_AFTER_END_CODE = 2; //截止后公开
    const SHOW_TYPE_HIDE_CODE = 3; //不公开
    
    const ALLOW_TYPE_ALL = 0;
    const ALLOW_TYPE_CHILD = 1;
    
    const SEQUENCE_AVAILABLE = -1; //满员状态的显示排序
    const SEQUENCE_DROPED = -100; //撤单状态的显示排序
    
    protected $User;
    protected $Account;
    
    
    public static $validShowType = [
        self::SHOW_TYPE_PUBLIC_CODE => 'Show type public',
        self::SHOW_TYPE_AFTER_FOLLOW_CODE => 'Show type after follow',
        self::SHOW_TYPE_AFTER_END_CODE => 'Show type after end',
        self::SHOW_TYPE_HIDE_CODE => 'Show type hide',
    ];
    
    public static $validAllowType = [
        self::ALLOW_TYPE_ALL => 'Allow all',
        self::ALLOW_TYPE_CHILD => 'Allow child',
    ];
    
    public static $validStatus = [
        self::STATUS_NORMAL => 'normal',
        self::STATUS_DROPED => 'droped',
        self::STATUS_LOST => 'lost',
        self::STATUS_WON => 'won',
        self::STATUS_PRIZE_SENT => 'prize sent',
        self::STATUS_DROPED_BY_SYSTEM => 'drop by system',
        self::STATUS_AVAILABLE => 'avaliable',
        self::STATUS_CANCELLED => 'cancelled',
    ];
    
    public static $validPrizeStatus = [
        self::PRIZE_STATUS_NORMAL_CODE => 'normal',
        self::PRIZE_STATUS_CALCULATING_CODE => 'calculating',
        self::PRIZE_STATUS_SENDING_CODE => 'sending',
        self::PRIZE_STATUS_SENDING_FEE_CODE => 'fee sent',
        self::PRIZE_STATUS_DONE_CODE => 'done',
    ];
    
    public static $validCommissionStatus = [
        self::COMMISSION_STATUS_NORMAL => 'normal',
        self::COMMISSION_STATUS_CALCULATING => 'calculating',
        self::COMMISSION_STATUS_CALCULATED => 'calculated',
        self::COMMISSION_STATUS_SENDING => 'sending',
        self::COMMISSION_STATUS_SENT => 'done',
    ];

    protected function getFormattedShowTypeAttribute() {
        if (isset(self::$validShowType[$this->attributes['show_type']])){
            self::comaileLangPack();
            return self::translate(self::$validShowType[$this->attributes['show_type']]);
        }
        return '';
    }
    
    protected function getFormattedAllowTypeAttribute() {
        if (isset(self::$validAllowType[$this->attributes['allow_type']])){
            self::comaileLangPack();
            return self::translate(self::$validAllowType[$this->attributes['allow_type']]);
        }
        return '';
    }
    
    protected function getFormattedStatusAttribute() {
        if (isset(self::$validStatus[$this->attributes['status']])){
            self::comaileLangPack();
            return self::translate(self::$validStatus[$this->attributes['status']]);
        }
        return '';
    }
    
    protected function getDisplayNicknameAttribute($sNickName) {
        if (!isset($sNickName)){
            $oUser = \User::find($this->user_id);
            if ($oUser){
                $sNickName = $oUser->display_nickname;
            }
        }
        return $sNickName;
    }
    
    protected function getEndTimeAttribute() {
        if ($this->bet_id > 0){
            $oBet = JcBet::find($this->bet_id);
            $oFirstMatch = $oBet->getFirstMatch();
            $sEndTime = $oFirstMatch->bet_time;
        }else{
            //预投截止时间为3天
            $sEndTime = date('Y-m-d H:i:s', strtotime($this->created_at) + 86400*3);
        }
        return $sEndTime;
    }
    
    protected function getBuyPercentAttribute() {
        return sprintf('%01.2f%%', $this->buy_amount / $this->amount * 100);
    }
    
    protected function getGuaranteePercentAttribute() {
        return sprintf('%01.2f%%', $this->guarantee_amount / $this->amount * 100);
    }
    
    protected function getSystemPrizeAttribute(){
        return $this->system_amount / $this->buy_amount * $this->prize;
    }
    
    public function bindBet($oBet){
        $aUpdateArr = [
            'amount' => $oBet->amount,
            'bet_id' => $oBet->id,
            'method_group_id' => $oBet->method_group_id,
            'end_time' => $oBet->getFirstMatchTime(),
            'progress' => $this->buy_amount / $oBet->amount * 100,
        ];
        $fBetFloatLimit = \SysConfig::readValue('jc_yutou_bet_float_limit');
        if (abs($oBet->amount - $this->amount) > $this->amount * $fBetFloatLimit){
            return false;
        }
        $oQuery = self::where('id', $this->id)
                ->where("bet_id", 0)
                ->whereIn('status', [self::STATUS_NORMAL, self::STATUS_AVAILABLE])
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $oProject = JcProject::find($this->project_id);
            $oQuery = $oProject->bindBet($oBet);
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function setUser($oUser){
        if ($oUser->id){
            $this->User = $oUser;
        }
    }
    
    public function setAccount($oAccount){
        if ($oAccount->id){
            $this->Account = $oAccount;
        }
    }
    
    public function setCalculating(){
        $fSystemPrize = $this->system_amount / $this->buy_amount * $this->prize;
        $fPrizeAmount = $this->prize - $fSystemPrize;
        $fFeeAmount = $this->fee_rate * $fPrizeAmount;
        if ($this->buy_amount >= $fPrizeAmount - $fFeeAmount){ //扣掉佣金仍有盈利时才进行佣金的发放
            $fFeeAmount = 0;
        }
        $this->fee_amount = $fFeeAmount;
        $this->prize_status = self::PRIZE_STATUS_CALCULATING_CODE;
        return $this->save();
    }
    
    public function setPrizeStatus($iPrizeStatus){
        $this->prize_status = $iPrizeStatus;
        return $this->save();
    }
    
    public function setToDroped(){
        $aUpdateArr = [
            'status' => self::STATUS_DROPED,
            'sequence' => self::SEQUENCE_DROPED,
        ];
        $oQuery = self::where('id', $this->id)
                ->whereIn('status', [self::STATUS_NORMAL, self::STATUS_AVAILABLE])
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function setToDropedBySystem(){
        $aUpdateArr = [
            'status' => self::STATUS_DROPED_BY_SYSTEM,
            'sequence' => self::SEQUENCE_DROPED,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', self::STATUS_NORMAL)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    

    public function setToCancelled(){
        $aUpdateArr = [
            'status' => self::STATUS_CANCELLED,
            'sequence' => self::SEQUENCE_DROPED,
        ];
        $oQuery = self::where('id', $this->id)
                ->whereIn('status', [self::STATUS_NORMAL, self::STATUS_AVAILABLE])
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function setToAvailable(){
        $aUpdateArr = [
            'status' => self::STATUS_AVAILABLE,
            'sequence' => self::SEQUENCE_AVAILABLE,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', self::STATUS_NORMAL)
                ->whereRaw('buy_amount = amount')
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
        
    public function setPrizeSent(){
        $aUpdateArr = [
            'status' => self::STATUS_PRIZE_SENT,
            'sent_at' => \Carbon::now()->toDateTimeString(),
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', self::STATUS_WON)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function setFinished(){
        $aUpdateArr = [
            'is_finished' => 1,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('is_finished', 0)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function doDropBySystem(){
        if ($this->setToDropedBySystem()) {
            return $this->setDropTask();
        }
    }
    
    public function doDrop(){
        if ($this->bet_id > 0){
            $oBet = JcBet::find($this->bet_id);
            if ($oBet->isEnd){
                return false;
            }
        }
        if ($this->setToDroped()) {
            return $this->setDropTask();
        }
    }
    
    public function setDropTask(){
        $aJobData = [
          'group_id' => $this->id,  
        ];
        return \BaseTask::addTask('\JcCommand\DropGroupBuy', $aJobData, 'jc_send_money');
    }
    
    public function incrementBuyAmount($amount = 0) {
        if ($amount > 0){
            $oQuery = self::where('id', $this->id)
                    ->whereRaw("buy_amount + {$amount} <= amount")
                    ->where('status', self::STATUS_NORMAL)
                    ->increment('buy_amount', $amount);
//                    ->update($aUpdateArr);
            $this->deleteCache();
            if ($oQuery){
                $this->buy_amount += $amount;
                $fProgress = $this->buy_amount / $this->amount * 100;
                $oQuery = self::where('id', $this->id)
                        ->update([
                            'progress' => $fProgress,
                        ]);
            }
            return $oQuery;
        }
    }
    
    public function decrementBuyAmount($amount = 0) {
        if ($amount > 0){
            $oQuery = self::where('id', $this->id)
                    ->where('status', self::STATUS_NORMAL)
                    ->decrement('buy_amount', $amount);
//                    ->update($aUpdateArr);
            $this->deleteCache();
            if ($oQuery){
                $this->buy_amount -= $amount;
                $fProgress = $this->buy_amount / $this->amount * 100;
                $oQuery = self::where('id', $this->id)
                        ->update([
                            'progress' => $fProgress,
                        ]);
            }
            return $oQuery;
        }
    }
    
    public static function makeSerialNumber($iUserId) {
        return substr('H'.  strtoupper(md5($iUserId . microtime(true) . mt_rand())), 0, 16);
    }
    
    public static function getCountByLotteryIdAndUserId($iLotteryId, $iUserId){
        return self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->whereNotIn('status', [self::STATUS_NORMAL])
                ->count();
    }
    
    public static function getSuccessCountByLotteryIdAndUserId($iLotteryId, $iUserId){
        return self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->whereIn('status', [self::STATUS_LOST, self::STATUS_WON, self::STATUS_PRIZE_SENT])
                ->count();
    }
}
