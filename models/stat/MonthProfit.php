<?php

/**
 * 总盈亏表
 *
 * @author frank
 */
class MonthProfit extends BaseModel {

    protected $table = 'month_profits';
    public static $resourceName = 'MonthProfit';
    protected $fillable = [
        'year',
        'month',
        'deposit',
        'withdrawal',
        'turnover',
        'prj_count',
        'tester_prj_count',
        'net_prj_count',
        'turnover',
        'daily_salary',
        'dividend',
        'prize',
        'profit',
        'bonus',
        'commission',
        'tester_deposit',
        'tester_withdrawal',
        'tester_turnover',
        'tester_prize',
        'tester_daily_salary',
        'tester_profit',
        'tester_commission',
        'tester_bonus',
        'tester_dividend',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_bonus',
        'net_profit',
        'net_daily_salary',
        'net_dividend',
        'profit_margin',
        'registered_count',
        'registered_top_agent_count',
        'signed_count',
        'bought_count',
    ];
    public static $rules = [
        'year' => 'required|integer|min:2014|max:2050',
        'month' => 'required|integer|min:1|max:12',
        'registered_count' => 'integer',
        'registered_top_agent_count' => 'integer',
        'signed_count' => 'integer',
        'bought_count' => 'integer',
        'deposit' => 'numeric|min:0',
        'withdrawal' => 'numeric|min:0',
        'prj_count' => 'integer',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'commission' => 'numeric|min:0',
        'bonus' => 'numeric|min:0',
        'profit' => 'numeric',
        'tester_deposit' => 'numeric|min:0',
        'tester_withdrawal' => 'numeric|min:0',
        'tester_prj_count' => 'integer',
        'tester_turnover' => 'numeric|min:0',
        'tester_prize' => 'numeric|min:0',
        'tester_commission' => 'numeric|min:0',
        'tester_bonus' => 'numeric|min:0',
        'tester_profit' => 'numeric',
        'net_deposit' => 'numeric|min:0',
        'net_withdrawal' => 'numeric|min:0',
        'net_prj_count' => 'integer',
        'net_turnover' => 'numeric|min:0',
        'net_prize' => 'numeric|min:0',
        'net_commission' => 'numeric|min:0',
        'net_bonus' => 'numeric|min:0',
        'net_profit' => 'numeric',
        'profit_margin' => 'numeric|max:1',
    ];
    public $orderColumns = [
        'year' => 'desc',
        'month' => 'desc',
    ];
    public static $mainParamColumn = 'date';
    public static $titleColumn = 'date';

    protected function beforeValidate() {
        $this->registered_count or $this->registered_count = null;
        $this->registered_top_agent_count or $this->registered_top_agent_count = null;
        $this->bought_count or $this->bought_count = null;
        $this->signed_count or $this->signed_count = $this->bought_count;
        return parent::beforeValidate();
    }

    /**
     * 返回对象
     * @param string $sDate
     * @return UserProfit
     */
    public static function getMonthProfitObject($iYear, $iMonth) {
        $obj = self::where('year', '=', $iYear)->where('month', '=', $iMonth)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'year' => $iYear,
                'month' => intval($iMonth),
            ];
            $obj = new MonthProfit($data);
        }
        return $obj;
    }

    /**
     * 累加充值额
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addDeposit($fAmount, $bTester = false) {
        $this->deposit += $fAmount;
        !$bTester or $this->tester_deposit += $fAmount;
        $this->net_deposit = $this->deposit - $this->tester_deposit;
        return $this->save();
    }

    /**
     * 累加充值额
     * @param float $fAmount
     * @param boolean $bDirect
     * @return boolean
     */
    public function addWithdrawal($fAmount, $bTester = false) {
        $this->withdrawal += $fAmount;
        !$bTester or $this->tester_withdrawal += $fAmount;
        $this->net_withdrawal = $this->withdrawal - $this->tester_withdrawal;
        return $this->save();
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

    public function calculateProfit($bTester = false) {
        $this->profit = $this->turnover - $this->prize - $this->commission - $this->bonus;
        !$bTester or $this->tester_profit = $this->tester_turnover - $this->tester_prize - $this->tester_commission - $this->tester_bonus;
        $this->net_turnover = $this->turnover - $this->tester_turnover;
        $this->net_deposit = $this->deposit - $this->tester_deposit;
        $this->net_withdrawal = $this->withdrawal - $this->tester_withdrawal;
        $this->net_prj_count = $this->prj_count - $this->tester_prj_count;
        $this->net_prize = $this->prize - $this->tester_prize;
        $this->net_commission = $this->commission - $this->tester_commission;
        $this->net_bonus = $this->bonus - $this->tester_bonus;
        $this->net_daily_salary = $this->daily_salary - $this->tester_daily_salary;
        $this->net_dividend = $this->dividend - $this->tester_dividend;
        $this->net_profit = $this->net_turnover - $this->net_prize - $this->net_commission - $this->net_bonus - $this->net_daily_salary - $this->net_dividend;
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
     * 添加促销派奖
     * @param float $fAmount
     * @param object $oUser
     */
    public function addBonus($fAmount, $oUser) {
        $this->bonus +=$fAmount;
        !$oUser->is_tester or $this->tester_bonus += $fAmount;
        $this->calculateProfit($oUser->is_tester);
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

    public static function updateProfitData($sType, $sDate, $fAmount, $bTester = false) {
        $sFunction = 'add' . ucfirst($sType);
        $bSucc = true;
        $oProfit = self::getProfitObject($sDate);
        pr($oProfit->getAttributes());
//        exit;
        return $oProfit->$sFunction($fAmount, $bTester);
    }

}
