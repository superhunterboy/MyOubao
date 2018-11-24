<?php

namespace JcModel;

/**
 * 赛事数据模型
 */
class JcMatchesInfo extends \BaseModel {
    public static $resourceName = 'JcMatchesInfo';
    protected $table = 'jc_matches_info';

    protected $fillable = [
        'id',
        'num',
        'lottery_id',
        'match_id',
        'original_id',
        'bet_date',
        'match_time',
        'prize_status',
        'league_id',
        'home_id',
        'away_id',
        'handicap',
        'weather',
        'temperature',
        'weather_pic',
        'score_status',
        'half_score',
        'score',
        'status',
        'prize_status',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
//        'match_id' => 'asc',
        'match_time' => 'desc'
    ];
    public static $htmlSelectColumns = [
        'status' => 'validStatuses',
        'league_id' => 'validLeagues',
        'home_id' => 'validHome',
        'away_id' => 'validAway',
        'lottery_id' => 'validLottery',
//        'is_hot' => 'validHot'
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'status';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id' => 'required|integer',
        'match_id' => 'required|integer',
        'num' => '',
        'original_id' => 'required|integer',
        'bet_date' => 'required|date',
        'match_time' => 'required|date',
        'is_hot' => 'integer',
        'status' => 'integer',
        'prize_status' => 'integer',
        'home_id' => 'required|integer',
        'away_id' => 'required|integer',
        'handicap' => 'integer',
        'half_score' => 'sometimes|regex:/[\d]+:[\d]+$/',
        'score' => 'sometimes|regex:/[\d]+:[\d]+$/',
    ];

    public static $listColumnMaps = [
        'status' => 'formatted_status',
        'is_hot' => 'formatted_hot',
        'prize_status' => 'formatted_prize_status',
    ];

    public static $viewColumnMaps = [
//        'status' => 'formatted_status',
//        'is_hot' => 'formatted_hot'
    ];
    
    //public $timestamps = false;
    
    
    const MATCH_UNPUBLISHED_STATUS_CODE = 0; //待售状态
    const MATCH_SELLING_STATUS_CODE = 1; //在售状态
    const MATCH_WAITING_STATUS_CODE = 2; //已抓到赛果 待开奖
    const MATCH_END_STATUS_CODE = 3; //结束状态
    const MATCH_CANCEL_STATUS_CODE = 4; //取消状态
    
    const PRIZE_STATUS_NORMAL_CODE = 0; //正常
    const PRIZE_STATUS_DRAWING_CODE = 1; //开奖
    const PRIZE_STATUS_CALCULATING_CODE = 2; //计奖
    const PRIZE_STATUS_SENDING_CODE = 3; //派奖
    const PRIZE_STATUS_DONE_CODE = 9; //全部完成

    const MATCHE_HOT = 1;
    const MATCHE_NOT_HOT = 0;
    
    public static $applyCanChangeStatus = [
        self::MATCH_UNPUBLISHED_STATUS_CODE,
        self::MATCH_SELLING_STATUS_CODE,
        self::MATCH_WAITING_STATUS_CODE,
        self::MATCH_END_STATUS_CODE,
        self::MATCH_CANCEL_STATUS_CODE,
    ];
    public static $manualCanChangeStatus = [
        self::MATCH_UNPUBLISHED_STATUS_CODE,
        self::MATCH_SELLING_STATUS_CODE,
        self::MATCH_WAITING_STATUS_CODE,
        self::MATCH_END_STATUS_CODE,
        self::MATCH_CANCEL_STATUS_CODE,
    ];

    public static $validStatuses = [
        self::MATCH_UNPUBLISHED_STATUS_CODE => 'Un Published',
        self::MATCH_SELLING_STATUS_CODE => 'Selling',
        self::MATCH_WAITING_STATUS_CODE => 'Waiting',
        self::MATCH_END_STATUS_CODE => 'End',
        self::MATCH_CANCEL_STATUS_CODE => 'Cancel',
    ];

    public static $validHot = [
        self::MATCHE_HOT => 'Yes',
        self::MATCHE_NOT_HOT => 'No',
    ];

   public static $validPrizeStatus = [
       self::PRIZE_STATUS_NORMAL_CODE => 'prize_waiting',
        self::PRIZE_STATUS_DRAWING_CODE =>  'prize_drawing',
        self::PRIZE_STATUS_CALCULATING_CODE =>  'prize_calculating',
        self::PRIZE_STATUS_SENDING_CODE =>  'prize_sending',
        self::PRIZE_STATUS_DONE_CODE =>  'prize_done',
   ];
   
    const ERRNO_SELLING_MATCH_IS_EMPTY = -10201;
    
    
    /**
     * [getFormattedStatusAttribute 获取状态的翻译文本]
     * @return [type] [状态的翻译文本]
     */
    protected function getFormattedStatusAttribute() {
        if (isset(self::$validStatuses[$this->attributes['status']])){
            return __('_manjcmatchesinfo.' . strtolower(\Str::slug(self::$validStatuses[$this->attributes['status']])));
        }
        return '';
    }

    protected function getFormattedHotAttribute() {
        if (isset(self::$validHot[$this->attributes['is_hot']])){
            return __('_manjcmatchesinfo.' . strtolower(\Str::slug(self::$validHot[$this->attributes['is_hot']])));
        }
        return '';
    }
    
    protected function getFormattedPrizeStatusAttribute() {
        if (isset(self::$validPrizeStatus[$this->attributes['prize_status']])){
            return __('_manjcmatchesinfo.' . strtolower(self::$validPrizeStatus[$this->attributes['prize_status']]));
        }
        return '';
    }
    
    protected function getBetTimeAttribute() {
        $iEndTime = strtotime($this->match_time) - \SysConfig::readValue('jc_bet_stop_time') * 60;
        return date('Y-m-d H:i:s', $iEndTime);
    }

    protected function getMatchNoAttribute(){
        return substr($this->match_id, -3, 3);
    }
    
    protected function getMatchDateAttribute(){
        return date('Y-m-d', strtotime($this->match_time));
    }
    
    protected function getDayAttribute(){
        return getWeekDay($this->bet_date);
    }
    
    protected function getLeagueNameAttribute(){
        //todo 尽量避免获取器中查询数据库
        $oLeague = JcLeague::find(self::getAttribute('league_id'));
        return $oLeague->name;
    }

    protected function getHomeTeamNameAttribute(){
        //todo 尽量避免获取器中查询数据库
        $oTeam = JcTeam::find(self::getAttribute('home_id'));
        return $oTeam->name;
    }

    protected function getAwayTeamNameAttribute(){
        $oTeam = JcTeam::find(self::getAttribute('away_id'));
        return $oTeam->name;
    }
    
    protected function getIsSellingAttribute(){
        $iStopBetTime = \SysConfig::readValue('jc_bet_stop_time') * 60;
        if ($this->status == self::MATCH_SELLING_STATUS_CODE && strtotime($this->match_time) - $iStopBetTime > time()){
            return true;
        }
        return false;
    }
    
    protected function getIsCancelledAttribute(){
        return $this->status == self::MATCH_CANCEL_STATUS_CODE;
    }
    
    public static function getSellingMatchByIds($aMatchId = []){
        $oQuery = self::_querySellingMatch();
        $oQuery->whereIn('match_id',$aMatchId);
        return $oQuery->get();
    }
    
    public static function getMatchByDate($dDate = null, $aColumns = ['*']){
        if (isset($dDate)){
            $oQuery = self::where('bet_date', '=', $dDate);
        }else{
            $dDate = date('Y-m-d');
            $oQuery = self::where('bet_date', '>=', $dDate);
        }
        $oQuery->orderby('match_id', 'asc');
        return $oQuery->get($aColumns);
    }
    
    public static function getSellingMatch($aColumns = ['*']){
        $oQuery = self::_querySellingMatch();
        return $oQuery->get($aColumns);
    }
    public static function countSellingMatch(){
        $oQuery = self::_querySellingMatch();
        return $oQuery->count();
    }
    private static function _querySellingMatch(){
        $iStopBetTime = \SysConfig::readValue('jc_bet_stop_time') * 60;
        
        $sBetTime = date('Y-m-d H:i:s', time() + $iStopBetTime);
        $oQuery = self::where('status', '=', self::MATCH_SELLING_STATUS_CODE);
        $oQuery->where('match_time', '>', $sBetTime);
        $oQuery->orderby('match_id', 'asc');
        return $oQuery;
    }


    public static function getLastMatches($aColumns = ['*'], $iPageSize = 100){
        $oQuery = self::query();
        $oQuery->limit($iPageSize);
        $oQuery->orderby('match_id', 'desc');
        return $oQuery->get($aColumns);
    }
    
    public static function getByMatchId($iMatchId = 0){
        //todo cache
        return self::where('match_id','=',$iMatchId)->first();
    }
    
    public static function getByMatchIds($aMatchIds = []){
        return self::whereIn('match_id', $aMatchIds)->get();
    }
    
    public static function getByMatchIdsWithLeagueAndTeam($aMatchIds = []){
        $oQuery = self::whereIn('match_id', $aMatchIds)->get();

        if (count($oQuery) > 0){
            foreach($oQuery as $oRow){
                $aTeamIds[$oRow['home_id']] = $oRow['home_id'];
                $aTeamIds[$oRow['away_id']] = $oRow['away_id'];
                $aLeagueIds[$oRow['league_id']] = $oRow['league_id'];
            }
            $aTeams = \JcModel\JcTeam::getByIds($aTeamIds);
            $aTeamList = [];
            foreach($aTeams as $oTeam){
                $aTeamList[$oTeam->id] = $oTeam;
            }
            $aLeagues = \JcModel\JcLeague::getByIds($aLeagueIds);
            $aLeagueList = [];
            foreach($aLeagues as $oLeague){
                $aLeagueList[$oLeague->id] = $oLeague;
            }
            foreach($oQuery as $sKey => $oRow){
                if (isset($aLeagueList[$oRow->league_id])){
                    $oQuery[$sKey]->league = $aLeagueList[$oRow->league_id];
                }
                if (isset($aTeamList[$oRow->home_id])){
                    $oQuery[$sKey]->home_team = $aTeamList[$oRow->home_id];
                }
                if (isset($aTeamList[$oRow->away_id])){
                    $oQuery[$sKey]->away_team = $aTeamList[$oRow->away_id];
                }
            }
        }
        return $oQuery;
    }

    public static function getMatcheByMatchId($match_id){
        return self::where('match_id','=',$match_id)->first();
    }

    public static function getMatchesByMatchIds($aMatchIds){
        return self::whereIn('match_id',$aMatchIds)->get();
    }

    public static function getHandicap($sHhadJson){
        if(!$sHhadJson) return '';
        $aHhad = json_decode($sHhadJson,true);
        return $aHhad['fixedodds'];
    }
    
    public function isFinished(){
        if ($this->status != self::MATCH_SELLING_STATUS_CODE && $this->status != self::MATCH_WAITING_STATUS_CODE){
            return true;
        }
        return false;
    }
    
    public function setDarwtingPrizeStatus(){
        $aUpdateArr = [
            'prize_status' => self::PRIZE_STATUS_DRAWING_CODE,
        ];
        $oQuery = self::where('id', $this->id)
//                ->where('status', '!=', self::MATCH_SELLING_STATUS_CODE)
//                ->where('prize_status', self::PRIZE_STATUS_NORMAL_CODE)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    public function setCalculatingPrizeStatus(){
        $aUpdateArr = [
            'prize_status' => self::PRIZE_STATUS_CALCULATING_CODE,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', '!=', self::MATCH_SELLING_STATUS_CODE)
                ->where('prize_status', self::PRIZE_STATUS_DRAWING_CODE)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    public function setSendingPrizeStatus(){
        $aUpdateArr = [
            'prize_status' => self::PRIZE_STATUS_SENDING_CODE,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', '!=', self::MATCH_SELLING_STATUS_CODE)
                ->where('prize_status', self::PRIZE_STATUS_CALCULATING_CODE)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    public function setDonePrizeStatus(){
        $aUpdateArr = [
            'prize_status' => self::PRIZE_STATUS_SENDING_CODE,
        ];
        $oQuery = self::where('id', $this->id)
                ->where('status', '!=', self::MATCH_SELLING_STATUS_CODE)
                ->update($aUpdateArr);
        $this->deleteCache();
        if ($oQuery){
            $this->fill($aUpdateArr);
        }
        return $oQuery;
    }
    
    public function checkWin($sCode){
        if ($this->status == self::MATCH_CANCEL_STATUS_CODE){
            return true;
        }
        if (!isset($sCode)){
            return false;
        }
        if (!$this->resultList){
            $aMethods = JcMethod::getAllByLotteryId($this->lottery_id);
            $aResult = [];
            foreach($aMethods as $oMethod){
                $sResult = $oMethod->getResult($this);
                $aResult[$sResult] = $sResult;
            }
            $this->resultList = $aResult;
        }
        $aResult = $this->resultList;
        return isset($aResult[$sCode]);
    }

    public function beforeValidate(){
        if(empty($this->attributes['match_id'])){
            $this->attributes['match_id'] = JcMatchOriginal::makeMatchId($this->attributes['bet_date'],$this->attributes['num']);
        }
        return parent::beforeValidate();
    }
    
      /**
     * 获取昨天比赛信息
     * @param type $start
     * @param type $end
     */
    public static function getLastMatchInfo($start, $end){
            if ( empty($start) || empty($end) ) {
                return false;
            }
            return self::where('match_time', '>=', $start)->where('match_time', '<', $end)->where('status', self::MATCH_END_STATUS_CODE)->orderBy('match_time', 'desc')->take(3)->get();
    }
}
