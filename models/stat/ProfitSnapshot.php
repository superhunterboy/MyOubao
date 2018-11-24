<?php

/**
 * 总盈亏表
 *
 * @author frank
 */
class ProfitSnapshot extends BaseModel {

    protected $table = 'profits';
    public static $resourceName = 'Profit';
    public static $amountAccuracy    = 6;
    public static $htmlNumberColumns = [
        'deposit' => 2,
        'withdrawal' => 2,
        'turnover' => 4,
        'tester_deposit' => 2,
        'tester_withdrawal' => 2,
        'tester_turnover' => 4,
        'net_deposit' => 2,
        'net_withdrawal' => 2,
        'net_turnover' => 4,
    ];
    public static $columnForList = [
        'date',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
        'profit_margin'
    ];

    public static $totalColumns = [
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
    ];

    /**
     *  图表展示数据横坐标
     */
    public static $columnForGraphX = 'date';

    /**
     *  图表展示数据
     */
    public static $columnForGraphList = [
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
//        'profit_margin'
    ];
    protected $fillable = [
        'date',
        'deposit',
        'withdrawal',
        'turnover',
        'prj_count',
        'tester_prj_count',
        'net_prj_count',
        'turnover',
        'prize',
        'profit',
        'commission',
        'dividend',
        'tester_deposit',
        'tester_withdrawal',
        'tester_turnover',
        'tester_prize',
        'tester_profit',
        'tester_commission',
        'tester_dividend',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_profit',
        'net_dividend',
        'profit_margin'
    ];
    public static $listColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [];
    public static $rules = [
        'date' => 'required|date',
        'deposit' => 'numeric|min:0',
        'withdrawal' => 'numeric|min:0',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'commission' => 'numeric|min:0',
        'dividend' => 'numeric|min:0',
        'profit' => 'numeric',
        'tester_deposit' => 'numeric|min:0',
        'tester_withdrawal' => 'numeric|min:0',
        'tester_turnover' => 'numeric|min:0',
        'tester_prize' => 'numeric|min:0',
        'tester_commission' => 'numeric|min:0',
        'tester_profit' => 'numeric',
        'tester_dividend' => 'numeric|min:0',
        'net_deposit' => 'numeric|min:0',
        'net_withdrawal' => 'numeric|min:0',
        'net_turnover' => 'numeric|min:0',
        'net_prize' => 'numeric|min:0',
        'net_commission' => 'numeric|min:0',
        'net_profit' => 'numeric',
        'net_dividend'=>'numeric|min:0',
        'profit_margin' => 'numeric|max:1'
    ];
    public $orderColumns = [
        'date' => 'desc',
    ];
    public static $mainParamColumn = 'date';
    public static $titleColumn = 'date';

    /**
     * 返回对象
     * @param string $sDate
     * @return UserProfit
     */
    public static function getProfitObject($sDate) {
        $obj = self::where('date', '=', $sDate)->get()->first();

        if (!is_object($obj)) {
            $data = [
                'date' => $sDate,
            ];
            $obj = new ProfitSnapshot($data);
        }else{
            $obj->prj_count = 0;
            $obj->tester_prj_count = 0;
            $obj->net_prj_count = 0;

            $obj->deposit = 0;
            $obj->withdrawal = 0;
            $obj->turnover = 0;
            $obj->prize = 0;
            $obj->profit = 0;
            $obj->commission = 0;
            $obj->dividend = 0;

            $obj->tester_deposit = 0;
            $obj->tester_withdrawal = 0;
            $obj->tester_turnover = 0;
            $obj->tester_prize = 0;
            $obj->tester_profit = 0;
            $obj->tester_commission = 0;
            $obj->tester_dividend = 0;

            $obj->net_deposit = 0;
            $obj->net_withdrawal = 0;
            $obj->net_turnover = 0;
            $obj->net_prize = 0;
            $obj->net_profit = 0;
            $obj->net_commission = 0;
            $obj->net_dividend = 0;

            $obj->profit_margin = 0;
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
    }

    /**
     * 累加团队及直属下级红利（促销派奖)
     * @param $fAmount
     * @param bool $bDirect
     * @return bool
     */
    public function addDividend($fAmount, $bTester = false) {
        $this->dividend += $fAmount;
        !$bTester or $this->tester_dividend += $fAmount;
        $this->net_dividend = $this->dividend - $this->tester_dividend;
        $this->calculateProfit($bTester);
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
    }

    private function calculateProfit($bTester = false) {
        $this->profit = $this->turnover - $this->prize - $this->commission - $this->dividend;
        !$bTester or $this->tester_profit = $this->tester_turnover - $this->tester_prize - $this->tester_commission - $this->tester_dividend;
        $this->net_turnover = $this->turnover - $this->tester_turnover;
        $this->net_prize = $this->prize - $this->tester_prize;
        $this->net_commission = $this->commission - $this->tester_commission;
        $this->net_profit = $this->net_turnover - $this->net_prize - $this->net_commission - $this->net_dividend;
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

    public function updateProfitData($sType, $fAmount, $bTester = false) {
        $sFunction = 'add' . ucfirst($sType);
        $this->$sFunction($fAmount, $bTester);
    }



}
