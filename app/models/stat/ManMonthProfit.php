<?php

/**
 * 月度盈亏,用于后台管理
 *
 * @author frank
 */
class ManMonthProfit extends MonthProfit {

    protected static $cacheUseParentClass = true;
    public static $amountAccuracy = 6;
    public static $htmlOriginalNumberColumns = [
        'year',
        'month'
    ];
    public static $htmlNumberColumns = [
        'registered_count' => 0,
        'registered_top_agent_count' => 0,
        'signed_count' => 0,
        'bought_count' => 0,
        'net_prj_count' => 0,
        'deposit' => 2,
        'withdrawal' => 2,
        'turnover' => 4,
        'prize' => 4,
        'profit' => 6,
        'commission' => 6,
        'tester_deposit' => 2,
        'tester_withdrawal' => 2,
        'tester_turnover' => 4,
        'tester_prize' => 4,
        'tester_profit' => 6,
        'tester_commission' => 6,
        'net_deposit' => 2,
        'net_withdrawal' => 2,
        'net_turnover' => 4,
        'net_prize' => 4,
        'net_profit' => 6,
        'net_commission' => 6,
    ];
    public static $columnForList = [
        'year',
        'month',
        'registered_top_agent_count',
        'registered_count',
        'signed_count',
        'bought_count',
        'net_prj_count',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_bonus',
        'net_dividend',
        'net_profit',
        'profit_margin'
    ];
    public static $totalColumns = [
        'registered_count',
        'registered_top_agent_count',
        'signed_count',
        'bought_count',
        'net_prj_count',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
        'net_profit',
        'net_bonus',
        'net_daily_salary',
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
    public static $listColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];
    public static $viewColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];
    public static $htmlSelectColumns = [];

    protected function getProfitMarginFormattedAttribute() {
        return number_format($this->attributes['profit_margin'] * 100, 2) . '%';
    }

}
