<?php

namespace JcModel;
/**
 * 方案模型
 */
class JcBet extends \BaseModel {
    public static $resourceName = 'JcBet';
    protected $table = 'jc_bets';
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
        'serial_number',
        'user_id',
        'username',
        'account_id',
        'group_id',
        'method_group_id',
        'game_extra',
        'bet_content',
        'bet_data',
        'multiple',
        'total',
        'coefficient',
        'amount',
        'single_amount',
        'prize',
        'min_prize',
        'max_prize',
        'status',
        'match_ids',
        'danma',
        'type',
        'created_at',
    ];
    
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id'              => 'required|integer',
        'user_id'             => 'required|integer',
        'username'      => 'required',
        'account_id'             => 'required|integer',
        'group_id'             => 'integer',
        'method_group_id'  => 'integer',
        'multiple'              => 'required|integer|min:1',
        'total'              => 'required|integer|min:1',
        'game_extra'         => 'required|regex:/[\d]+_[\d]+(,[\d]+_[\d]+)?$/',
        'bet_content'   => 'required',
        'coefficient' => 'in:1.00,0.10,0.01',
        'single_amount'              => 'required|numeric|min:0',
        'amount' => 'required|numeric|min:0',
        'type' => 'in:1,2,3',
    ];
        
    //public $timestamps = false;
    
    const STATUS_NORMAL             = 0; //正常
    const STATUS_DROPED             = 1; //撤单
    const STATUS_LOST               = 2; //未中奖
    const STATUS_WON                = 3; //已中奖
    const STATUS_PRIZE_SENT         = 4; //已派奖
    const STATUS_DROPED_BY_SYSTEM   = 5; //系统撤单
    const STATUS_CANCELLED   = 8; //取消

    const PRIZE_STATUS_NORMAL_CODE = 0; //正常
//    const PRIZE_STATUS_DRAWING_CODE = 1; //开奖
    const PRIZE_STATUS_CALCULATING_CODE = 2; //计奖
    const PRIZE_STATUS_SENDING_CODE = 3; //派奖
    const PRIZE_STATUS_SENDING_FEE_CODE = 4; //派发佣金
    const PRIZE_STATUS_DONE_CODE = 9; //全部完成
    
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
    
    public static $validTypes = [
        self::TYPE_SELF_BUY => 'self buy',
        self::TYPE_GROUP_BUY => 'group buy',
    ];

    const COMMISSION_STATUS_NORMAL = 0;
    const COMMISSION_STATUS_CALCULATED = 1;
    
    const TYPE_SELF_BUY = 1;
    const TYPE_GROUP_BUY = 2;

    const ERRNO_BET_CONTENT_MATCHID = -10001;
    const ERRNO_BET_CONTENT_METHOD = -10002;
    const ERRNO_BET_CONTENT_DANMA = -10003;
    const ERRNO_BET_CONTENT_WAY = -10004;
    const ERRNO_BET_CONTENT_ODDS = -10005;
    const ERRNO_BET_CONTENT_MATCH_MAX = -10006;
    const ERRNO_BET_CONTENT_MATCH_NOT_ENOUGH = -10007;
    const ERRNO_BET_PARAM_IS_EMPTY = -10008;
    const ERRNO_BET_CONTENT_MULTIPLE = -10009;
    const ERRNO_BET_COUNT_MAX = -10010;
    const ERRNO_BET_DAILY_AMOUNT_MAX = -10011;
    const ERRNO_BET_AMOUNT_MAX = -10012;
    const ERRNO_BET_MULTIPLE_MAX = -10013;
    const ERRNO_BET_MATCHE_NOT_AVAILABLE = -10014;

    const ERRNO_BET_FAILED = -10104;
    const ERRNO_BET_ERROR_LOW_BALANCE          = -10105;
    const ERRNO_BET_ERROR_SAVE          = -10106;
    const ERRNO_BET_SUCCESSFUL          = -10107;
    const ERRNO_BET_ERROR_AMOUNT = -10108;
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
    
    protected function getDisplayNicknameAttribute($sNickName) {
        if (!isset($sNickName)){
            $oUser = \User::find($this->user_id);
            if ($oUser){
                $sNickName = $oUser->display_nickname;
            }
        }
        return $sNickName;
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
    
    protected function getFormattedTypeAttribute() {
        if (isset(self::$validTypes[$this->attributes['type']])){
            self::comaileLangPack();
            return self::translate(self::$validTypes[$this->attributes['type']]);
        }
        return '';
    }
    
    protected function getBuyPercentAttribute() {
        if ($this->group_id == 0){
            return sprintf('%01.2f%%', 100);
        }
    }
    
    //判断方案是否已经截止
    protected function getIsEndAttribute($bIsEnd){
        if (!isset($bIsEnd)){
            $bIsEnd = false;
            $aMatches = $this->match_list;
            if ($aMatches){
                foreach($aMatches as $oMatch){
                    if ($oMatch->isFinished() || $oMatch->match_time <= date('Y-m-d H:i:s')){
                        $bIsEnd = true;
                    }
                }
            }
        }
        return $bIsEnd;
    }
    
    //判断方案是否已经截止
    protected function getIsCancelledAttribute($bIsCancelled){
        if (!isset($bIsCancelled)){
            if ($this->status == self::STATUS_CANCELLED){
                $bIsCancelled = true;
            }else{
                $aMatches = $this->match_list;
                $bIsCancelled = false;
                if ($aMatches){
                    foreach($aMatches as $oMatch){
                        if ($oMatch->status == JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                            $bIsCancelled = true;
                        }else{
                            $bIsCancelled = false;
                            break;
                        }
                    }
                }
            }
            $this->is_cancelled = $bIsCancelled;
        }
        return $bIsCancelled;
    }
    
    protected function getMatchListAttribute($aMatchList) {
        if (!isset($aMatchList)){
            $sMatchIds = $this->match_ids;
            $aMatchIds = explode(',', $sMatchIds);
            $aMatches = \JcModel\JcMatchesInfo::getByMatchIds($aMatchIds);
            $aMatchList = [];
            foreach($aMatches as $oMatch){
                $aMatchList[$oMatch->match_id] = $oMatch;
            }
        }
        return $aMatchList;
    }
    
    public static function getByIds($aIds){
        $aList = [];
        if (count($aIds) > 0){
            $oQueryRes = self::whereIn('id', $aIds)->get();
            foreach($oQueryRes as $oRow){
                $aList[$oRow->id] = $oRow;
            }
        }
        return $aList;
    }
    
    public function bindGroupBuy($oGroupBuy){
        $aUpdateArr = [
            'group_id' => $oGroupBuy->id,
            'type' => self::TYPE_GROUP_BUY,
        ];
        $oQuery = self::where('id', $this->id)
                ->where("group_id", 0)
                ->where('status', self::STATUS_NORMAL)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }

//    /**
//     * 发放奖金
//     * @param array $aData
//     * @return boolean
//     */
//    public function send($aData = []){
//        $fAmount = $aData['amount'];
//        if ($fAmount <= 0){
//            return false;
//        }
//        $aExtraData = $this->getTransactionData($aData);
//        
//        $iReturn = \Transaction::addTransaction($this->User,$this->Account,\TransactionType::TYPE_SEND_PRIZE,$fAmount,$aExtraData);
//        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
//    }
//    
//    /**
//     * 下注
//     * @param array $aData
//     * @return boolean
//     */
//    public function bet($aData = []){
//        $fAmount = $aData['amount'];
//        if ($fAmount <= 0){
//            return false;
//        }
//        if ($this->Account->available < $fAmount){
//            return false;
//        }
//        $aExtraData = $this->getTransactionData($aData);
//
//        $iReturn = \Transaction::addTransaction($this->User, $this->Account,\TransactionType::TYPE_BET,$fAmount,$aExtraData);
//        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
//    }
//    
//    /**
//     * 撤单
//     * @param array $aData
//     * @return boolean
//     */
//    public function dropBet($aData = []){
//        $fAmount = $aData['amount'];
//        if ($fAmount <= 0){
//            return false;
//        }
//        $aExtraData = $this->getTransactionData($aData);
//
//        $iReturn = \Transaction::addTransaction($this->User, $this->Account,\TransactionType::TYPE_DROP,$fAmount,$aExtraData);
//        return $iReturn == \Transaction::ERRNO_CREATE_SUCCESSFUL;
//    }
    
    /**
     * set Account Model
     * @param Account $oAccount
     */
    public function setAccount($oAccount){
        if ($oAccount->id){
            $this->Account = $oAccount;
        }
    }

    /**
     * set User Model
     * @param User $oUser
     */
    public function setUser($oUser){
        if ($oUser->id){
            $this->User = $oUser;
        }
    }
    
//    public function getUserAttribute() {
//        if (!$this->User){
//            $this->User = \User::find($this->user_id);
//        }
//        return $this->User;
//    }
//    
//    public function getAccountAttribute() {
//        if (!$this->Account){
//            $this->Account = \Account::find($this->user_id);
//        }
//        return $this->Account;
//    }
    
    public static function incrementPrize($id, $prize){
        return self::where('id', '=', $id)->increment('prize', $prize);
    }
    
    public function incrementCancelledAmount($fAmount){
        $oQuery = self::where('id', '=', $this->id)
                ->increment('cancelled_amount', $fAmount);
        if ($oQuery){
            $this->deleteCache();
            $this->cancelled_amount += $fAmount;
        }
        return $oQuery;
    }
    
    public function setWonLost(){
        $aUpdateArr = [
            'status' => $this->prize > 0 ? self::STATUS_WON : self::STATUS_LOST,
            'calculated_at' => \Carbon::now()->toDateTimeString(),
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
        
    public function setPrizeSent(){
        $aUpdateArr = [
            'gains' => max($this->prize - $this->amount, 0),
            'return_percent' => max($this->prize / $this->amount * 100, 0),
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
    
    public function getTransactionData($aExtraData = []){
        $aData = [
            'lottery_id' => $this->lottery_id,
            'coefficient' => $this->coefficient,
            'issue' => '',
            'way_id' => 0,
        ];
        $aExtraData = array_merge($aData, $aExtraData);
        $aExtraData[ 'project_id' ] = $this->id;
        $aExtraData[ 'project_no' ] = $this->serial_number;
        return $aExtraData;
    }
    
    /**
     * 撤单
     * @return type
     */
    public function doDrop(){
        if ($this->group_id > 0){
            //合买方案必须从合买撤单
            return false;
        }
        $bSucc = $this->setToDroped();
        $aBetDetailList = JcBetsDetail::getByBetId($this->id);
        foreach($aBetDetailList as $oBetDetail){
            $bSucc = $oBetDetail->setToDroped();
            if (!$bSucc){
                break;
            }
        }
        $aBetMatchList = JcBetsMatch::getByBetId($this->id);
        foreach($aBetMatchList as $oBetMach){
            $bSucc = $oBetMach->setToDroped();
            if (!$bSucc){
                break;
            }
        }
        if ($bSucc){
            $aData = [
              'amount' => $this->amount,  
            ];
            $bSucc = $this->dropBet($aData);
        }
        return $bSucc;
    }
    
    public function setToDropedWithChildSet(){
        $bSucc = $this->setToDroped();
        $aBetDetailList = JcBetsDetail::getByBetId($this->id);
        foreach($aBetDetailList as $oBetDetail){
            $bSucc = $oBetDetail->setToDroped();
            if (!$bSucc){
                $this->validationErrors = $oBetDetail->errors();
                return false;
            }
        }
        $aBetMatchList = JcBetsMatch::getByBetId($this->id);
        foreach($aBetMatchList as $oBetMach){
            $bSucc = $oBetMach->setToDroped();
            if (!$bSucc){
                $this->validationErrors = $oBetMach->errors();
                return false;
            }
        }
        return $bSucc;
    }
    

    public function setToDroped(){
        $aUpdateArr = [
            'status' => self::STATUS_DROPED,
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
    
    public function getFirstMatchTime(){
        $oMatch = self::getFirstMatch();
        if ($oMatch){
            return $oMatch->match_time;
        }
    }
    
    public static function getMatchData($sBetContent){
        $aMatchesData = [];
        $aMatchBetList = explode('+', $sBetContent);
        foreach($aMatchBetList as $sMatchBetContent){
            $aMatchBetInfo = explode(':', $sMatchBetContent);
            if (count($aMatchBetInfo) != 3){
                return [];
            }
            $sMatchId = $aMatchBetInfo[0];
            if (strlen($sMatchId) != 12){
                return [];
            }
            $aBetCode = explode('.', $aMatchBetInfo[1]);
            $aUniqueBetCode = array_unique($aBetCode);
            if (count($aBetCode) != count($aUniqueBetCode)){
                return [];
            }
            $sDanma = $aMatchBetInfo[2];
            if (!in_array($sDanma, ['0', '1'], true)){
                return [];
            }
            $aMatchesData[$sMatchId] = [
                'match_id' => $sMatchId,
                'bet_data' => explode('.', $aMatchBetInfo[1]),
                'is_danma' => $sDanma == 1,
            ];
        }
        if (count($aMatchBetList) != count($aMatchesData)){
            return [];
        }
        return $aMatchesData;
    }
    
    public function getFirstMatch(){
        $aMatchList = $this->match_list;
        if ($aMatchList){
            $aMatchTime = [];
            $aMatchSortList = [];
            foreach($aMatchList as $oMatch){
                $aMatchSortList[$oMatch->match_id] = $oMatch;
                $aMatchTime[$oMatch->match_id] = $oMatch->match_time;
            }
            array_multisort($aMatchTime, $aMatchSortList, SORT_ASC);
            return current($aMatchSortList);
        }
    }
    
    public function getBetMatchData(){
        $aMatchIds = [];
        $aBetMatchList = [];
        
        $iLotteryId = $this->lottery_id;
        $aAllMethods = \JcModel\JcMethod::getAllByLotteryId($iLotteryId);
        
        $aDanmaMatchList = explode(',', $this->danma);
        $aBetData = json_decode($this->bet_data, true);
        
        foreach($aBetData as $iMatchId => $aCodeData){
            foreach($aCodeData as $sCode => $fOdds){
                $oMethod = \JcModel\JcMethod::getMethodByCode($iLotteryId, $sCode);
                $aBetMatchList[$iMatchId][$oMethod->id][$sCode] = $fOdds;
            }
            $aMatchIds[$iMatchId] = $iMatchId;
        }
        $aMatchBetData = \JcModel\JcUserMatchesInfo::getByMatchIdsWithLeagueAndTeam($aMatchIds);
        foreach($aMatchBetData as $key => $oMatch){
            $aMethods = [];
            foreach($aBetMatchList[$oMatch->match_id] as $iMethodId => $aCodeData){
                $oMethod = new \JcModel\JcMethod($aAllMethods[$iMethodId]->getAttributes());
                $aBetMatch = [];
                foreach($aCodeData as $sCode => $fOdds){
                    if ($oMatch->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                        $fOdds = '1.00';
                    }
                    $aBetMatch[$sCode] = (object)[
                        'code' => $sCode,
                        'odds' => $fOdds,
                        'name' => \JcModel\JcMethod::getCodeName($oMatch->lottery_id, $sCode),
                    ];
                }
                $oMethod->result = $oMethod->getResult($oMatch);
//                $oMethod->resultTitle = $oMethod->formatCodeName($oMethod->result);
                $oMethod->resultTitle = $oMethod->getResultTitle($oMatch);
//                var_dump('matchno:'.$data->match_no.'.status:'.$data->status, $oMethod->result, $oMethod->resultTitle);
                $oMethod->codeList = $aBetMatch;
                $aMethods[$oMethod->id] = $oMethod;
            }
            if (in_array($oMatch->match_id, $aDanmaMatchList)){
                $aMatchBetData[$key]->is_danma = 1;
            }
            $aMatchBetData[$key]->method = $aMethods;
        }
        return $aMatchBetData;
    }
    
    public function formatBetDetailData(&$aBetDetailList){
        $aMatchesList = $this->match_list;
        foreach($aBetDetailList as $oBetDetail){
            $aOddsText = [];
            $fRes = 1;
            $aBetData = json_decode($oBetDetail->bet_data, true);
            foreach($aBetData as $iMatchId => $aCodeData){
                foreach($aCodeData as $sCode => $fOdds){
                    break;
                }
                $oMatch = $aMatchesList[$iMatchId];
                if ($oMatch->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                    $fOdds = '1.00';
                }
                $aOddsText[] = "{$oMatch->day}{$oMatch->num}[$fOdds]";
                $fRes *= $fOdds;
            }
//            $fPrize = number_format(\Math::roundoff($fRes * $oBetDetail->single_amount, 2) * $oBetDetail->multiple, 2);
            $fPrize = number_format($fRes * $oBetDetail->single_amount * $oBetDetail->multiple, 4);
            $oBetDetail->formula = implode(' x ', $aOddsText) . ' x ' . number_format($oBetDetail->single_amount) . '元 x ' . $oBetDetail->multiple . '倍 = ' . $fPrize;
        }
        return $aBetDetailList;
    }
    
    public static function getBetCount($iLotteryId, $iUserId){
        return self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->whereIn('type', [self::TYPE_SELF_BUY, self::TYPE_GROUP_BUY])
                ->whereNotIn('status', [self::STATUS_NORMAL])
                ->count();
    }
    
    public static function getWonCount($iLotteryId, $iUserId){
        return self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->whereIn('type', [self::TYPE_SELF_BUY, self::TYPE_GROUP_BUY])
                ->where('status', self::STATUS_PRIZE_SENT)
                ->count();
    }
    
    public static function getWonPrize($iLotteryId, $iUserId){
        return self::where('lottery_id', $iLotteryId)
                ->where('user_id', $iUserId)
                ->whereIn('type', [self::TYPE_SELF_BUY, self::TYPE_GROUP_BUY])
                ->where('status', self::STATUS_PRIZE_SENT)
                ->sum('prize');
    }
    
    public static function makeSerialNumber($iUserId) {
        return substr('Z'.  strtoupper(md5($iUserId . microtime(true) . mt_rand())), 0, 16);
    }
}
