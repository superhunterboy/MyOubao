<?php

/**
 * 计奖
 *
 * */
class CountPrize extends Issue {

    /**
     * 是否初始化
     * @var bool
     */
    public $isInit = false;

    /**
     * 最大派奖金额
     * @var int
     */
    public $iMaxPrize = 0;
    /**
     * 系列投注方式
     * @var array
     */
    private $oSeriesWays = [];

    /**
     * 系列投注方法
     * @var array
     */
    private $oSeriesMethods = [];

    /**
     * 注单
     * @var array
     */
    private $oProjects = [];

    /**
     * 彩种
     * @var null
     */
    private $oLottery = null;

    public function getBetAndPrize($wn_number, $bIsFilterTester = 0, $fProjectAmountMin = 0){
        $this->prizeAmount = 0;

        if($iProjectCount = ManProject::getUnCalcutatedCount($this->lottery_id, $this->issue, null, null, $fProjectAmountMin, $bIsFilterTester))
        {
            if(!$this->isInit){
                $this->init();
            }
            $aWnNumberOfMethods = $this->getWnNumberOfSeriesMethods($wn_number);

            foreach($this->oSeriesWays as $wayId => $oSeriesWay){
                $oSeriesWay->getWinningNumber($aWnNumberOfMethods);
                $this->calculateProjectsOfWay($oSeriesWay, $bIsFilterTester, $fProjectAmountMin);

            }
        }


        return $this->prizeAmount;
    }

    /**
     * 初始化
     */
    private function init()
    {
        $this->oLottery = Lottery::find($this->lottery_id);
        $this->iMaxPrize = $this->oLottery->max_prize;

        $oSeriesMethods = SeriesMethod::where('series_id', '=', $this->oLottery->series_id)->get();
        foreach($oSeriesMethods as $oSeriesMethod) {
            $this->oSeriesMethods[$oSeriesMethod->id] = $oSeriesMethod;
        }

        $oSeriesWays = SeriesWay::where('series_id', '=', $this->oLottery->series_id)->get();
        foreach ($oSeriesWays as $oSeriesWay) {
            $this->oSeriesWays[$oSeriesWay->id] = $oSeriesWay;
        }

        $this->isInit = true;
    }

    /**
     * 由中奖号码分析得出各投注方式的中奖号码数组
     * @param Lottery $oLottery
     * @param string $sFullWnNumber
     * @param bool $bNameKey
     * @return array &
     */
    private function & getWnNumberOfSeriesMethods($sFullWnNumber, $bNameKey = false) {
        $aWnNumbers = [];
        $sKeyColumn = $bNameKey ? 'name' : 'id';
        foreach ($this->oSeriesMethods as $oSeriesMethod) {
            $aWnNumbers[$oSeriesMethod->$sKeyColumn] = $oSeriesMethod->getWinningNumber($sFullWnNumber);
        }
        return $aWnNumbers;
    }


    /**
     * @param $oSeriesWay
     * @return array
     */
    private function calculateProjectsOfWay($oSeriesWay, $bIsFilterTester = 0, $fProjectAmountMin = 0) {

        if(! isset($this->oProjects[$oSeriesWay->id])){
            $this->oProjects[$oSeriesWay->id] = ManProject::getUnCalculatedProjects($this->lottery_id, $this->issue, $oSeriesWay->id, null, null, null, $fProjectAmountMin, $bIsFilterTester);
        }

        $oProjects = & $this->oProjects[$oSeriesWay->id];

        if ($oSeriesWay->WinningNumber === false || $oProjects->count() <= 0)
        {
//            if($oProjects->count() > 0){
//                foreach($oProjects as $oManProject){
//                    $this->betAmount += $oManProject->amount;
//                }
//            }
        }else{
            $aPrizedOfBetNumbers = [];

            foreach ($oProjects as $oProject) {
                if($bIsFilterTester && $oProject->is_tester && $oProject->amount < $fProjectAmountMin) continue;
                $this->calculateProject($oSeriesWay, $oProject, $aPrizedOfBetNumbers);
            }
        }
    }

    /**
     * 对注单计奖
     * @param SeriesWay $oSeriesWay
     * @param Project $oProject
     * @param array & $aPrizedOfBetNumbers
     * @return array &
     */
    private function calculateProject($oSeriesWay, & $oProject, & $aPrizedOfBetNumbers) {

//        $this->betAmount += $oProject->amount;

        if(!$this->isOverPrize())
        {
            $sBetNumber = $oProject->bet_number;
            $sPosition = $oProject->position;
            $sKey = md5($sBetNumber . $sPosition);
            if (array_key_exists($sKey, $aPrizedOfBetNumbers)) {
                $aPrized = $aPrizedOfBetNumbers[$sKey];
            } else {
                $aPrized = $oSeriesWay->checkPrize($sBetNumber, $sPosition);
                !$aPrized or $aPrizedOfBetNumbers[$sKey] = $aPrized;
            }
            if ($aPrized) {
                $this->addPrize($oProject, $aPrized);
            }
        }
    }

    /**
     *增加派奖金额
     * @param Project $oProject
     * @param array $aPrized
     * @param array & $aPrizeDetails
     * @return bool
     */
    public function addPrize($oProject, $aPrized) {
        $aPrizeSet = json_decode($oProject->prize_set);

        foreach ($aPrized as $iBasicMethodId => $aPrizeOfBasicMethod)
        {
            list($iLevel, $iCount) = each($aPrizeOfBasicMethod);

            $fPrizeOf = $aPrizeSet->$iBasicMethodId->$iLevel * $iCount * $oProject->multiple * $oProject->coefficient;

            //如果存在最大派奖金额
            if($this->iMaxPrize > 0)
            {
                //首次派奖，且奖金大于最大派奖金额
                if(!$this->prizeAmount && $fPrizeOf >= $this->iMaxPrize){
                    $fPrizeOf = $this->iMaxPrize;
                }
                //已派奖金额大于最大派奖金额
                elseif($this->prizeAmount >= $this->iMaxPrize) {
                    $fPrizeOf = 0;
                }
                //已派奖金额和本次派奖金额大于最大派奖金额
                elseif(($this->prizeAmount + $fPrizeOf) > $this->iMaxPrize) {
                    $fPrizeOf = $this->iMaxPrize - $this->prizeAmount;
                }
            }

            $this->prizeAmount += $fPrizeOf;
        }
    }

    /**
     * 奖金是否超限
     * @return bool
     */
    private function isOverPrize(){
        if($this->iMaxPrize > 0 && $this->prizeAmount >= $this->iMaxPrize){
            return true;
        }
        return false;

    }

    public function getMinPrizeNumber($aWinNumbers, $oJackpot){

        if(!$oJackpot && !Cache::has('jackpot_'.$this->lottery_id)) return [$aWinNumbers[0],'jackpot not exist',0];

        $this->jackpotCacheInit();

        $iJackpotId = Cache::get('jackpot_'.$this->lottery_id);
        $bStatus = Cache::get('jackpot_status_'.$iJackpotId);
        $fInitPoolPrize = Cache::get('jackpot_init_pool_amount_'.$iJackpotId);
        $fProfitRatio = Cache::get('jackpot_profit_rate_'.$iJackpotId);
        $fMaxPrizeRatio = Cache::get('jackpot_max_prize_rate_'.$iJackpotId);
        $fProjectAmountMin = Cache::get('jackpot_project_amount_min_'.$iJackpotId);
        $iNumberCount = Cache::get('jackpot_number_count_'.$iJackpotId);
        $fAccessFrequency = Cache::get('jackpot_access_frequency_'.$iJackpotId);
        $fMaxAmount = Cache::get('jackpot_max_amount_'.$iJackpotId);
        $bLocked = Cache::get('jackpot_locked_'.$iJackpotId);
        $sEnableAt = Cache::get('jackpot_enable_at_'.$iJackpotId);
        $sEndAt = Cache::get('jackpot_end_at_'.$iJackpotId);
        $bIsFilterTester = Cache::get('jackpot_is_filter_tester_'.$iJackpotId);

        if($sEnableAt != 0 && $sEnableAt >= date('Y-m-d H:i:s') && $sEndAt != 0 && $sEndAt <= date('Y-m-d H:i:s')) {
            return [$aWinNumbers[0],'jackpot is not available',0];
        }

        //总投注金额
        $fBetAmount = $this->getBetAmount($bIsFilterTester);

        if(!$fBetAmount) return [$aWinNumbers[0],'no bet',0];

        //可赔付金额
        if($fInitPoolPrize + $fBetAmount*(1-$fProfitRatio) >= $fMaxAmount){
            $fAvailablePrize = $fMaxAmount*$fMaxPrizeRatio;
        }else{
            $fAvailablePrize = ($fInitPoolPrize + $fBetAmount*(1-$fProfitRatio))*$fMaxPrizeRatio;
        }

        $a = [];
        $iWinNumber = '';
        $bBreak = 0;
        foreach($aWinNumbers as $iNumber){
            //总派奖金额
            $fPrizeAmount = $this->getBetAndPrize($iNumber, $bIsFilterTester, $fProjectAmountMin);

            $a[$iNumber] = $fPrizeAmount;

            $aProfit[$iNumber] = $fAvailablePrize - $fPrizeAmount;

            if(($fAvailablePrize - $fPrizeAmount) >= 0) {
                $iWinNumber = $iNumber;
                $bBreak = 1;
                break;
            }


        }

        if(!$bBreak) {
            asort($aProfit);
            $aKeys = array_keys($aProfit);
            $iWinNumber = $aKeys[count($aKeys)-1];
        }

        if($fInitPoolPrize + $fBetAmount*(1-$fProfitRatio) >= $fMaxAmount)
            $fInitPoolPrize = $fMaxAmount*(1-$fMaxPrizeRatio) + $aProfit[$iWinNumber];
        else
            $fInitPoolPrize = ($fInitPoolPrize + $fBetAmount*(1-$fProfitRatio))*(1-$fMaxPrizeRatio) + $aProfit[$iWinNumber];

        if($fInitPoolPrize >= $fMaxAmount) $fInitPoolPrize = $fMaxAmount;

        DB::beginTransaction();

        do{

            sleep(2);

        }while($bLocked);

        Jackpots::lock($iJackpotId);

        $bSucc = Jackpots::doWhere([
            'id' => ['=',$iJackpotId]
        ])->update(['init_pool_amount'=>$fInitPoolPrize]);


        if($bSucc)
        {

            Jackpots::unlock($iJackpotId);

            Cache::forever('jackpot_init_pool_amount_'.$iJackpotId, $fInitPoolPrize);

            DB::commit();
        }
        else
            DB::rollback();


        return [$iWinNumber,json_encode($a),$fBetAmount];

    }

    public function getBetAmount($bIsFilterTester = 0){
        if($bIsFilterTester) $aIsTester = [0];
        else $aIsTester = [0,1];
        return ManProject::doWhere(
            [
                'lottery_id'=>['=',$this->lottery_id],
                'issue'=>['=',$this->issue],
                'status'=>['in',[
                    ManProject::STATUS_NORMAL
                ]],
                'is_tester'=>['in',$aIsTester]
            ]
        )->sum('amount');
    }

    public function jackpotCacheInit(){
        if(!Cache::has('jackpot_'.$this->lottery_id)){
            $oJackpot = Jackpots::getAvailableJackpotByLottery($this->lottery_id);
            Cache::forever('jackpot_'.$this->lottery_id, $oJackpot->id);
            $iJackpotId = Cache::get('jackpot_'.$this->lottery_id);
            if(!Cache::has('jackpot_status_'.$this->lottery_id))
                Cache::forever('jackpot_status_'.$iJackpotId, $oJackpot->status);
            if(!Cache::has('jackpot_init_pool_amount_'.$iJackpotId))
                Cache::forever('jackpot_init_pool_amount_'.$iJackpotId, $oJackpot->init_pool_amount);
            if(!Cache::has('jackpot_profit_rate_'.$iJackpotId))
                Cache::forever('jackpot_profit_rate_'.$iJackpotId, $oJackpot->profit_rate);
            if(!Cache::has('jackpot_max_prize_rate_'.$iJackpotId))
                Cache::forever('jackpot_max_prize_rate_'.$iJackpotId, $oJackpot->max_prize_rate);
            if(!Cache::has('jackpot_project_amount_min_'.$iJackpotId))
                Cache::forever('jackpot_project_amount_min_'.$iJackpotId, $oJackpot->project_amount_min);
            if(!Cache::has('jackpot_number_count_'.$iJackpotId))
                Cache::forever('jackpot_number_count_'.$iJackpotId, $oJackpot->number_count);
            if(!Cache::has('jackpot_access_frequency_'.$iJackpotId))
                Cache::forever('jackpot_access_frequency_'.$iJackpotId, $oJackpot->access_frequency);
            if(!Cache::has('jackpot_max_amount_'.$iJackpotId))
                Cache::forever('jackpot_max_amount_'.$iJackpotId, $oJackpot->max_amount);
            if(!Cache::has('jackpot_locked_'.$iJackpotId))
                Cache::forever('jackpot_locked_'.$iJackpotId, $oJackpot->locked);
            if(!Cache::has('jackpot_enable_at_'.$iJackpotId))
                Cache::forever('jackpot_enable_at_'.$iJackpotId, $oJackpot->enable_at);
            if(!Cache::has('jackpot_end_at_'.$iJackpotId))
                Cache::forever('jackpot_end_at_'.$iJackpotId, $oJackpot->end_at);
            if(!Cache::has('jackpot_is_filter_tester_'.$iJackpotId))
                Cache::forever('jackpot_is_filter_tester_'.$iJackpotId, $oJackpot->is_filter_tester);
        }
    }
}
