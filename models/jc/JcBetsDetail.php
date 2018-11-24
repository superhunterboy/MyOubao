<?php

namespace JcModel;
/**
 * 方案详细模型
 */
class JcBetsDetail extends \BaseModel {
    public static $resourceName = 'JcBetsDetail';
    protected $table = 'jc_bets_detail';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'id',
        'bet_id',
        'user_id',
        'username',
        'way_id',
        'multiple',
        'total_matches',
        'finished_matches',
        'amount',
        'prize',
        'status'
    ];
    protected $fillable = [
        'bet_id',
        'lottery_id',
        'user_id',
        'username',
        'way_id',
        'bet_data',
        'multiple',
        'total_matches',
        'finished_matches',
        'single_amount',
        'amount',
        'coefficient',
        'prize',
        'status'
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    public static $htmlSelectColumns = [];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'bet_id'             => 'required|integer',
        'lottery_id'              => 'required|integer',
        'user_id'              => 'required|integer',
        'username'        => 'required',
        'way_id'              => 'required|integer',
        'multiple'              => 'required|integer|min:1',
        'total_matches'              => 'required|integer',
        'finished_matches'              => 'integer',
        'coefficient' => 'required|in:1.00,0.10,0.01',
        'single_amount'              => 'required|numeric|min:0',
        'amount'              => 'required|numeric|min:0',
    ];
    
    const STATUS_NORMAL             = \JcModel\JcBet::STATUS_NORMAL;
    const STATUS_DROPED             = \JcModel\JcBet::STATUS_DROPED;
    const STATUS_LOST               = \JcModel\JcBet::STATUS_LOST;
    const STATUS_WON                = \JcModel\JcBet::STATUS_WON;
    const STATUS_PRIZE_SENT         = \JcModel\JcBet::STATUS_PRIZE_SENT;
    const STATUS_DROPED_BY_SYSTEM   = \JcModel\JcBet::STATUS_DROPED_BY_SYSTEM;
    const STATUS_CANCELLED   = \JcModel\JcBet::STATUS_CANCELLED;
    
    //public $timestamps = false;

    public static $validStatuses = [
        self::STATUS_NORMAL => 'Normal',
        self::STATUS_DROPED => 'Droped',
        self::STATUS_LOST => 'Lost',
        self::STATUS_WON => 'Won',
        self::STATUS_PRIZE_SENT => 'Prize Sent',
        self::STATUS_DROPED_BY_SYSTEM => 'Droped By System',
        self::STATUS_CANCELLED => 'Cancelled',
    ];

    protected function getFormattedStatusAttribute() {
        if (isset(self::$validStatuses[$this->attributes['status']])){
            self::comaileLangPack();
            return self::translate(self::$validStatuses[$this->attributes['status']]);
        }
        return '';
    }
    
    public static function getByIds($aIds){
        return self::whereIn('id', $aIds)->get();
    }
    
    public static function getByBetId($iBetId){
        return self::where('bet_id', $iBetId)->get();
    }

    public static function getByBetIdAndStartId($iBetId, $aColumns = ['*'], $iStartId = 0, $iLimit = 0){
        $oQuery = self::where('bet_id', $iBetId)->where('id', '>', $iStartId);
        if ($iLimit > 0){
            $oQuery->limit($iLimit);
        }
        $oQuery->orderby('id', 'asc');
        return $oQuery->get($aColumns);
    }
    
    public function addBetsDetail(){
        $this->user_id = \Session::get('user_id');
        $this->amount = $this->single_amount * $this->multiple;
        $this->save();
        return $this->id;
    }
    
    public function insertBetsDetail(){
        $aInsertData = $this->getAttributes();
        $aInsertData['user_id'] = \Session::get('user_id');
        $aInsertData['amount'] = $aInsertData['single_amount'] * $aInsertData['multiple'];
        if ($aInsertData['total_matches'] <= 0){
            return false;
        }
        if ($aInsertData['single_amount'] <= 0 || $aInsertData['multiple'] < 1 || $aInsertData['amount'] <= 0){
            return false;
        }
        return self::insertGetId($aInsertData);
    }
    
    public static function addInsertBetsDetail(&$aInsertData){
        $iLimit = 1000;
        if(count($aInsertData) > $iLimit){
            return self::batchInsertBetsDetail($aInsertData);
        }else{
            return true;
        }
    }
    public static function batchInsertBetsDetail(&$aInsertData){
//            $aExecData = array_slice($aInsertData, 0, $iLimit);
        if (count($aInsertData) <= 0){
            return false;
        }
        $aKeys = $aValueSql = [];
        foreach($aInsertData as $aInsert){
            $aSingleValueSql = [];
            $aInsert['created_at'] = $aInsert['updated_at'] = date('Y-m-d H:i:s');
            foreach($aInsert as $sKey=>$sVal){
                $aKeys[$sKey] = "`{$sKey}`";
                $aSingleValueSql[] = "'{$sVal}'";
            }
            $aValueSql[] = "(" . implode(',', $aSingleValueSql) . ")";
        }
        unset($aInsert);
        unset($aSingleValueSql);
        $sKeySql = implode(',', $aKeys);
        $sql = "INSERT INTO `jc_bets_detail` ($sKeySql) VALUES ". implode(',', $aValueSql);
        unset($aKeys);
        unset($aValueSql);
//            $bSucc = false;
        $bSucc = \DB::statement($sql);
        unset($sql);
        $aInsertData = [];
        if ($bSucc !== TRUE){
            return false;
        }
        return $bSucc;
//        echo $sql;
////        return self::insert($aInsertData);
    }
    
    public static function incrementFinishedMatches($id){
        $aIds = (array)$id;
        return self::whereIn('id', $aIds)
//                ->where('status', self::STATUS_NORMAL)
                ->increment('finished_matches');
    }
    
    public static function incrementCancelledMatches($id){
        $aIds = (array)$id;
        return self::whereIn('id', $aIds)
//                ->where('status', self::STATUS_NORMAL)
                ->increment('cancelled_matches');
    }
    
    /**
     * 待取消的列表
     * @param int $iLotteryId
     * @param int $iLimit
     * @return object
     */
    public static function getUnCalculateCancelledList($iLotteryId, $iLimit = 0){
        $oQueryWhere = self::where('lottery_id', $iLotteryId);
        $oQueryWhere->where('status', self::STATUS_NORMAL);
        $oQueryWhere->whereRaw('total_matches = cancelled_matches');
        if ($iLimit > 0){
            $oQueryWhere->limit($iLimit);
        }
        return $oQueryWhere->get();
    }
    
    /**
     * 待计奖的列表
     * @param int $iLotteryId
     * @param int $iLimit
     * @param int $iStartId
     * @return object
     */
    public static function getUnCalculateList($iLotteryId = 0, $iLimit = 0, $iStartId = 0){
        $oQuery = self::where('lottery_id', '=', $iLotteryId);
        $oQuery->where('status', self::STATUS_NORMAL);
        $oQuery->whereRaw('total_matches = finished_matches AND total_matches > cancelled_matches');
        $oQuery->where('id', '>', $iStartId);
        $oQuery->orderby('id', 'asc');
        if ($iLimit > 0){
            $oQuery->limit($iLimit);
        }
        return $oQuery->get();
    }
    
    /**
     * 统计待计奖列表的数量
     * @param int $iBetId
     * @return object
     */
    public static function countUnCalculateByBetId($iBetId = 0){
        $oQuery = self::where('bet_id', '=', $iBetId);
         $oQuery->where('status', self::STATUS_NORMAL);
         $oQuery->whereRaw('total_matches > cancelled_matches');
        return $oQuery->count();
    }
    
    /**
     * 待派奖的列表(自购)
     * @param int $iLotteryId
     * @param int $iLimit
     * @param int $iStartId
     * @return object
     */
    public static function getUnPrizeList($iLotteryId = 0, $iLimit = 0, $iStartId = 0){
        $oQuery = self::where('lottery_id', '=', $iLotteryId);
        $oQuery->whereRaw('total_matches = finished_matches');
        $oQuery->where('status', '=', self::STATUS_WON);
        $oQuery->where('id', '>', $iStartId);
        $oQuery->orderby('id', 'asc');
        if ($iLimit > 0){
            $oQuery->limit($iLimit);
        }
        return $oQuery->get();
    }
    
    /**
     * 统计待派奖列表的数量
     * @param int $iBetId
     * @return object
     */
    public static function countUnPrizeByBetId($iBetId = 0){
        $oQuery = self::where('bet_id', '=', $iBetId)->where('status', '=', self::STATUS_WON);
        return $oQuery->count();
    }
    
    /**
     * 统计中奖列表的数量
     * @param int $iBetId
     * @return object
     */
    public static function countWonByBetId($iBetId = 0){
        $oQuery = self::where('bet_id', '=', $iBetId)->where('status', '=', self::STATUS_WON);
        return $oQuery->count();
    }
    
    /**
     * 统计中奖列表的奖金
     * @param int $iBetId
     * @return object
     */
    public static function sumPrizeByBetId($iBetId = 0){
        $aStatus = [ self::STATUS_WON, self::STATUS_PRIZE_SENT ];
        $oQuery = self::where('bet_id', $iBetId)->whereIn('status', $aStatus);
        return $oQuery->sum('prize');
    }
    
    public static function countUnCancelledByBetId($iBetId = 0){
        $oQuery = self::where('bet_id', $iBetId);
        $oQuery->whereRaw('total_matches > cancelled_matches');
        return $oQuery->count();
    }
    
    /**
     * 更新奖金及计奖状态
     * @param object $oBetsDetail
     * @param object $oBetsMatches
     * @return object
     */
    public function setPrize(){
        $iTotalMatches = $this->total_matches;
        if ($iTotalMatches == $this->cancelled_matches){
            $iStatus = self::STATUS_CANCELLED;
            $fPrize = 0;
        }else if ($iTotalMatches == $this->finished_matches){
            $aBetData = json_decode($this->bet_data, true);
            $bIsWin = true;
            $fTotalOdds = 1;
            foreach($aBetData as $iMatchId => $aCodeData){
                foreach($aCodeData as $sCode => $fOdds){
                    break;
                }
//                    $oMatchInfo = $aMatchList[$iMatchId];
                $oMatchInfo = JcMatchesInfo::getByMatchId($iMatchId);
                if (!$oMatchInfo->checkWin($sCode)){
                    $bIsWin = false;
                    break;
                }
                if ($oMatchInfo->status == \JcModel\JcMatchesInfo::MATCH_CANCEL_STATUS_CODE){
                    $fOdds = 1;
                }
                $fTotalOdds = $fTotalOdds * $fOdds;
            }
            if ($bIsWin){
//                $fPrize = \Math::roundoff($this->single_amount * $fTotalOdds, 2) * $this->multiple;
                $fCalculatePrize = $this->single_amount * $fTotalOdds * $this->multiple;//暂不进行小数位处理

                //奖金限额处理
                $oWay = JcWay::find($this->way_id);
                if ($oWay && $oWay->prize_limit > 0){
                    $fCalculatePrize = min($fCalculatePrize, $oWay->prize_limit);
                }

                $iStatus = self::STATUS_WON;
                $fPrize = $fCalculatePrize;
            }else{
                $iStatus = self::STATUS_LOST;
                $fPrize = 0;
            }
        }else{
            //异常的注单，强制设为未中奖
            $iStatus = self::STATUS_LOST;
            $fPrize = 0;
        }
        $aUpdateArr = [
            'status' => $iStatus,
            'prize' => $fPrize,
        ];
        $bSucc = self::where('id', $this->id)
                ->where('status', self::STATUS_NORMAL)
                ->update($aUpdateArr);
        if ($bSucc){
            $this->deleteCache();
            $this->fill($aUpdateArr);
        }
        
        return $bSucc;
    }
    
    public function setToCancelled(){
        $aUpdateArr = [
            'status' => self::STATUS_CANCELLED,
        ];
        return self::where('id', $this->id)->where('status', self::STATUS_NORMAL)->update($aUpdateArr);
    }
    
    public function setToDroped(){
        $aUpdateArr = [
            'status' => self::STATUS_DROPED,
        ];
        return self::where('id', $this->id)->where('status', self::STATUS_NORMAL)->update($aUpdateArr);
    }
    
    public function setPrizeSent(){
        $aUpdateArr = [
            'status' => self::STATUS_PRIZE_SENT,
            'sent_at' => \Carbon::now()->toDateTimeString(),
        ];
        return self::where('id', $this->id)->where('status', self::STATUS_WON)->update($aUpdateArr);
    }
}
