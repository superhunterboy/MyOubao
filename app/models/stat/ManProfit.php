<?php

use Illuminate\Support\Facades\Redis;

/**
 * 日度盈亏，用于后台管理
 *
 */
class ManProfit extends Profit {

    protected static $cacheUseParentClass = true;
    public static $amountAccuracy = 6;
    public static $htmlNumberColumns = [
        'registered_count' => 0,
//        'registered_top_agent_count' => 0,
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
        'net_dividend' => 4,
        'net_profit' => 6,
        'net_commission' => 6,
        'net_bonus' => 6,
    ];
    public static $columnForList = [
        'date',
        'week',
//        'registered_top_agent_count',
        'registered_count',
        'signed_count',
//        'online_count',
        'bought_count',
        'net_prj_count',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_prize',
        'net_commission',
//        'net_deposit_fee',
        'net_bonus',
        'net_dividend',
//        'net_daily_salary',
        'net_profit',
        'profit_margin'
    ];
    public static $totalColumns = [
        'registered_count',
//        'registered_top_agent_count',
        'signed_count',
        'bought_count',
        'net_prj_count',
        'net_deposit',
        'net_withdrawal',
        'net_turnover',
        'net_commission',
        'net_deposit_fee',
        'net_prize',
        'net_daily_salary',
        'net_profit',
        'net_bonus',
        'net_dividend',
    ];
    public static $listColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];
    public static $viewColumnMaps = [
        'profit_margin' => 'profit_margin_formatted',
    ];
    public static $ignoreColumnsInView = [
        'signed_users',
        'bought_users'
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

    protected function getProfitMarginFormattedAttribute() {
        return number_format($this->attributes['profit_margin'] * 100, 2) . '%';
    }

    protected function getWeekAttribute() {
        $sWeek = date('D', strtotime($this->attributes['date']));
        return __('_basic.week-' . strtolower($sWeek));
    }

    protected function getOnlineCountAttribute() {
        if ($this->attributes['date'] == date('Y-m-d')) {
            $redis = Redis::connection();
            $redis->select('1');
            $iCount = count($redis->keys("*"));
            $redis->select('0');
            return $iCount;
        } else {
            return $this->signed_count;
        }
    }

}
