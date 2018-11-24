<?php

/**
 * 代理盈亏表
 *
 * @author frank
 */
class IssueProfitSnapshot extends BaseModel {

    protected $table = 'issue_profits';
    public static $resourceName = 'IssueProfit';
    public static $amountAccuracy = 6;

    protected $fillable = [
        'lottery_id',
        'issue',
        'end_time',
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

    public static $rules = [
        'lottery_id' => 'required|integer',
        'issue' => 'required|max:15',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'commission' => 'numeric|min:0',
        'profit' => 'numeric',
        'tester_turnover' => 'numeric|min:0',
        'tester_prize' => 'numeric|min:0',
        'tester_commission' => 'numeric|min:0',
        'tester_profit' => 'numeric',
        'net_turnover' => 'numeric|min:0',
        'net_prize' => 'numeric|min:0',
        'net_commission' => 'numeric|min:0',
        'net_profit' => 'numeric',
        'profit_margin' => 'numeric|max:1'
    ];

    /**
     * 返回IssueProfit对象
     * @param int       $iLotteryId
     * @param string    $sIssue
     * @return UserProfit
     */
    public static function getProfitObject($iLotteryId, $sIssue) {
        $obj = self::where('lottery_id', '=', $iLotteryId)->where('issue', '=', $sIssue)->get()->first();

        if (!is_object($obj)) {
            if(!$oIssue = Issue::getIssue($iLotteryId,$sIssue)) return false;
            $data = [
                'lottery_id' => $iLotteryId,
                'issue' => $sIssue,
                'end_time' => $oIssue->end_time,
            ];
            $obj = new IssueProfitSnapshot($data);
        }else{
            $obj->prj_count = 0;
            $obj->tester_prj_count = 0;
            $obj->net_prj_count = 0;
            $obj->turnover = 0;
            $obj->prize = 0;
            $obj->profit = 0;
            $obj->commission = 0;
            $obj->tester_turnover = 0;
            $obj->tester_prize = 0;
            $obj->tester_profit = 0;
            $obj->tester_commission = 0;
            $obj->net_turnover = 0;
            $obj->net_prize = 0;
            $obj->net_profit = 0;
            $obj->profit_margin = 0;
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
        if ($bTester){
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
    }

    public function updateProfitData($sType, $fAmount, $is_tester) {
        $sFunction = 'add' . ucfirst($sType);
        return $this->$sFunction($fAmount, $is_tester);
    }

}
