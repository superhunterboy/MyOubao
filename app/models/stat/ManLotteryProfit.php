<?php
/**
 * 彩种盈亏表
 *
 */

class ManLotteryProfit extends LotteryProfit{
    protected static $cacheUseParentClass = true;
    public static $amountAccuracy    = 6;
    public static $htmlNumberColumns = [
        'net_prj_count' => 0,
        'turnover'          => 4,
        'prize'             => 4,
        'profit'            => 6,
        'commission'        => 6,
        'tester_turnover'   => 4,
        'tester_prize'      => 4,
        'tester_profit'     => 6,
        'tester_commission' => 6,
        'net_turnover'      => 4,
        'net_prize'         => 4,
        'net_profit'        => 6,
        'net_commission'    => 6,
    ];
    public static $columnForList = [
        'date',
        'lottery_id',
        'net_prj_count',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
        'profit_margin',
        'turnover_ratio',
    ];
    public static $totalColumns = [
        'net_prj_count',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
    ];
    public static $listColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
        'turnover_ratio' => 'turnover_ratio_formatted',
    ];
    public static $viewColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
        'turnover_ratio' => 'turnover_ratio_formatted',
    ];
    public static $weightFields = [
        'net_turnover',
        'net_profit',
        'profit_margin'
    ];
    public static $classGradeFields = [
        'net_profit',
        'profit_margin'
    ];

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
    ];

    protected function getProfitMarginFormattedAttribute() {
        return number_format($this->attributes['profit_margin'] * 100, 2) . '%';
    }

    protected function getTurnoverRatioFormattedAttribute() {
        return number_format($this->attributes['turnover_ratio'] * 100, 2) . '%';
    }

}
