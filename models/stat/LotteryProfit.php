<?php

/**
 * 彩种盈亏表
 *
 * @author frank
 */
class LotteryProfit extends BaseModel {

    protected $table = 'lottery_profits';
    public static $resourceName = 'LotteryProfit';
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'turnover' => 4,
        'tester_turnover' => 4,
        'net_turnover' => 4,
    ];
    public static $columnForList = [
        'date',
        'lottery_id',
        'turnover',
        'prize',
        'profit',
        'commission',
        'tester_turnover',
        'tester_prize',
        'tester_profit',
        'tester_commission',
        'net_turnover',
        'net_prize',
        'net_profit',
        'net_commission',
        'profit_margin'
    ];
    protected $fillable = [
        'date',
        'lottery_id',
        'prj_count',
        'tester_prj_count',
        'net_prj_count',
        'turnover',
        'prize',
        'profit',
        'commission',
        'tester_turnover',
        'tester_prize',
        'tester_profit',
        'tester_commission',
        'net_turnover',
        'net_prize',
        'net_profit',
        'profit_margin'
    ];
    public static $listColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
    ];
    public static $rules = [
        'date' => 'required|date',
        'lottery_id' => 'required|integer',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'commission' => 'numeric|min:0',
        'profit' => 'numeric',
        'tester_turnover' => 'numeric|min:0',
        'tester_prize' => 'numeric|min:0',
        'tester_commission' => 'numeric|min:0',
        'tester_profit' => 'numeric',
        'net_turnover' => 'numeric|min:0',
        'net_prize' => 'numeric',
        'net_commission' => 'numeric',
        'net_profit' => 'numeric',
        'profit_margin' => 'numeric|max:1'
    ];
    public $orderColumns = [
        'date' => 'desc',
        'lottery_id' => 'asc',
    ];
    public static $mainParamColumn = 'date';
    public static $titleColumn = 'date';

    /**
     * 返回对象
     * @param string $sDate
     * @param int       $iLotteryId
     * @return UserProfit
     */
    public static function getProfitObject($sDate, $iLotteryId) {
        $obj = self::where('date', '=', $sDate)->where('lottery_id', '=', $iLotteryId)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'lottery_id' => $iLotteryId,
                'date' => $sDate,
            ];
            $obj = new LotteryProfit($data);
        }
        return $obj;
    }

    /**
     * 累加销售额
     * @param float $fAmount
     * @param boolean $bTester
     * @return boolean
     */
    public function addTurnover($fAmount, $bTester = false) {
        $this->turnover += $fAmount;
        $fAmount > 0 ? $this->prj_count++ : $this->prj_count--;
        if ($bTester) {
            $this->tester_turnover += $fAmount;
            $fAmount > 0 ? $this->tester_prj_count++ : $this->tester_prj_count--;
        }
        $this->net_prj_count = $this->prj_count - $this->tester_prj_count;
        $this->calculateProfit($bTester);
        return $this->save();
    }

    private function calculateProfit($bTester = false) {
        $this->profit = $this->turnover - $this->prize - $this->commission;
        !$bTester or $this->tester_profit = $this->tester_turnover - $this->tester_prize - $this->tester_commission;
        $this->net_turnover = $this->turnover - $this->tester_turnover;
        $this->net_prize = $this->prize - $this->tester_prize;
        $this->net_commission = $this->commission - $this->tester_commission;
        $this->net_profit = $this->net_turnover - $this->net_prize - $this->net_commission;
//        pr($this->toArray());
        $this->profit_margin = $this->net_turnover ? $this->net_profit / $this->net_turnover : 0;
    }

    /**
     * 累加奖金
     *
     * @param float $fAmount
     * @param boolean $bTester
     * @return boolean
     */
    public function addPrize($fAmount, $bTester = false) {
        $this->prize += $fAmount;
        !$bTester or $this->tester_prize += $fAmount;
        $this->calculateProfit($bTester);
        return $this->save();
    }

    /**
     * 累加团队佣金
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addCommission($fAmount, $bTester = false) {
        $this->commission += $fAmount;
        !$bTester or $this->tester_commission += $fAmount;
        $this->calculateProfit($bTester);
        return $this->save();
    }

    public static function updateProfitData($sType, $sDate, $iLotteryId, $oUser, $fAmount) {
        $sFunction = 'add' . ucfirst($sType);
        $bSucc = true;
        $oLotteryProfit = self::getProfitObject($sDate, $iLotteryId);
        return $oLotteryProfit->$sFunction($fAmount, $oUser->is_tester);
    }

    protected function getProfitMarginFormattedAttribute() {
        return number_format($this->attributes['profit_margin'] * 100, 2) . '%';
    }

    public function setRatio($oDailyProfit = null) {
        if (is_null($oDailyProfit)) {
            $fTurnover = Profit::getTurnoverFromCache($this->date);
        } else {
            $fTurnover = $oDailyProfit->net_turnover;
        }
        $this->turnover_ratio = $fTurnover > 0 ? $this->net_turnover / $fTurnover : null;
//        return $this->isDirty('turnover_ratio') ? $this->save() : true ;
    }

}
