<?php

/**
 * 用户盈亏表
 *
 * @author snowan
 */
class UserUserSlotProfit extends UserUserProfit {
    protected $table = 'user_profits_slot';
    public static $resourceName = 'UserUserSlotProfit';

    static private $selectSum = [
        'sum(turnover) as turnover',
        'sum(profit) as profit',
        'sum(prize) as prize',
        'sum(commission) as commission',
        'sum(dividend) as dividend',
        'sum(bet_commission) as bet_commission'
    ];

    public static $selectTeamSum = [
        'COALESCE(sum(team_turnover + turnover),0) as team_turnover',
        'COALESCE(sum(team_profit + profit)) as team_profit',
        'COALESCE(sum(team_prize + prize),0) as team_prize',
        'COALESCE(sum(team_commission + commission),0) as team_commission',
        'COALESCE(sum(team_dividend + dividend),0) as team_dividend',
        'COALESCE(sum(team_bet_commission + bet_commission),0) as team_bet_commission'
    ];

    public static $htmlNumberColumns = [
        'team_turnover' => 4,
        'direct_turnover' => 4,
        'turnover' => 4,
        'dividend' =>4,
        'team_dividend' =>4,
        'direct_dividend' =>4
    ];
    public static $columnForList = [
        'date',
        'username',
        'user_type',
        'parent_user',
        'prize_group',
        'team_turnover',
        'team_profit',
        'direct_turnover',
        'direct_prize',
        'direct_profit',
        'direct_commission',

    ];

    public static $totalColumns = [
        'team_turnover',
        'team_profit',
        'direct_turnover',
        'direct_prize',
        'direct_profit',
        'direct_commission',
    ];

    public static $listColumnMaps = [
        'user_type' => 'user_type_formatted',
        'team_turnover' => 'team_turnover_formatted',
        'team_prize' => 'team_prize_formatted',
        'team_profit' => 'team_profit_formatted',
        'team_commission' => 'team_commission_formatted',
        'direct_turnover' => 'direct_turnover_formatted',
        'direct_prize' => 'direct_prize_formatted',
        'direct_profit' => 'direct_profit_formatted',
        'direct_commission' => 'direct_commission_formatted',
        'turnover' => 'turnover_formatted',
        'prize' => 'prize_formatted',
        'profit' => 'profit_formatted',
        'commission' => 'commission_formatted',
    ];
    protected $fillable = [
        'date',
        'user_id',
        'is_agent',
        'is_tester',
        'prize_group',
        'user_level',
        'username',
        'parent_user_id',
        'parent_user',
        'team_turnover',
        'team_prize',
        'team_profit',
        'team_commission',
        'direct_turnover',
        'direct_prize',
        'direct_profit',
        'direct_commission',
        'turnover',
        'prize',
        'profit',
        'commission',
        'dividend',
        'direct_dividend',
        'team_dividend'
    ];
    public static $rules = [
        'date' => 'required|date',
        'user_id' => 'required|integer',
        'is_agent' => 'in:0,1',
        'is_tester'         => 'in:0,1',
        'prize_group' => 'integer',
        'user_level' => 'required|min:0|max:2',
        'username' => 'required|max:16',
        'parent_user_id' => 'integer',
        'parent_user' => 'max:16',
        'team_turnover' => 'numeric|min:0',
        'team_prize' => 'numeric|min:0',
        'team_profit' => 'numeric',
        'team_commission' => 'numeric|min:0',
        'team_dividend' => 'numeric|min:0',
        'direct_turnover' => 'numeric|min:0',
        'direct_prize' => 'numeric|min:0',
        'direct_profit' => 'numeric',
        'direct_commission' => 'numeric|min:0',
        'direct_dividend' => 'numeric|min:0',
        'turnover' => 'numeric|min:0',
        'prize' => 'numeric|min:0',
        'profit' => 'numeric',
        'commission' => 'numeric|min:0',
        'dividend' => 'numeric|min:0',
    ];

    /**
     * 盈亏报表总额
     * @param $oQuery
     * @return mixed
     */
    static public function queryTotal($oQuery)
    {
        $oUserProfit = $oQuery->select(DB::raw(implode(',', self::$selectTeamSum)))->first();
        return $oUserProfit;
    }
    /**
     * 获取个人的盈亏报表
     */
    static public function queryByUserId($iUserId,$dateTo=null,$dateFrom=null)
    {
        $selectRaw = array_merge(['user_id', 'username', 'is_agent'], self::$selectSum);

        $oQuery=self::select(DB::raw(implode(',', $selectRaw)))->where('user_id',$iUserId);

        if ($dateFrom) $oQuery = $oQuery->where('date', '>=', $dateFrom);
        if ($dateTo) $oQuery = $oQuery->where('date', '<=', $dateTo);

        $oUserProfit=$oQuery->first();
        return $oUserProfit;
    }

    /**
     *盈亏报表数据
     * @param $oQuery
     * @param $pagesize
     * @param null $order
     * @return mixed
     */
    static public function compileUserTotal($oQuery, $pagesize)
    {
        $selectRaw = array_merge(['user_id', 'username', 'is_agent'], self::$selectTeamSum);

        $oUserProfit = $oQuery->select(DB::raw(implode(',', $selectRaw)))->groupBy('user_id')->paginate($pagesize);

        return $oUserProfit;
    }
}