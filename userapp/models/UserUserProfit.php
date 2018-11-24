<?php

/**
 * 用户盈亏表
 *
 * @author snowan
 */
class UserUserProfit extends UserProfit {

    static private $selectSum = [
        'sum(turnover) as turnover',
        'sum(profit) as profit',
        'sum(prize) as prize',
        'sum(withdrawal) as withdrawal',
        'sum(deposit) as deposit',
        'sum(commission) as commission',
        'sum(dividend) as dividend',
        'sum(bet_commission) as bet_commission',
        'sum(team_numbers) as team_numbers',
    ];

    public static $selectTeamSum = [
        'COALESCE(sum(team_turnover + turnover),0) as team_turnover',
        'COALESCE(sum(team_profit + profit)) as team_profit',
        'COALESCE(sum(team_withdrawal + withdrawal),0) as team_withdrawal',
        'COALESCE(sum(team_deposit + deposit),0) as team_deposit',
        'COALESCE(sum(team_prize + prize),0) as team_prize',
        'COALESCE(sum(team_commission + commission),0) as team_commission',
        'COALESCE(sum(team_dividend + dividend),0) as team_dividend',
        'COALESCE(sum(team_bet_commission + bet_commission),0) as team_bet_commission',
        'COALESCE(sum(team_numbers),0) as team_numbers',
    ];

    protected function getIsAgentFormattedAttribute() {
        $is_agent = 'is_agent';
        return intval($this->{$is_agent}) === 0 ? '玩家' : '代理';
    }

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
    public static function getTotal(){

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
  
    public static function getProfitsSum($parentUserId,$username,$dateFrom,$dateTo,$iPage,$orderSet)
    {

        $selectRaw = ['user_id', 'username', 'is_agent',
                    'sum(team_turnover + turnover) as team_turnover',
                    'sum(team_profit + profit) as team_profit',

                    'sum(team_prize + prize) as team_prize',
                    'sum(team_commission + commission) as team_commission',
                    'sum(team_dividend + dividend) as team_dividend',
                    'sum(team_bet_commission + bet_commission) as team_bet_commission',
                    ];

        $query = self::select(DB::raw(implode(',', $selectRaw)));
        if(!empty($username))
            $query -> where('username',$username);
        $query->where('parent_user_id',$parentUserId)->where('date','>=',$dateFrom)->where('date','<=',$dateTo);
        if(!empty($orderSet))
            $query->orderby($orderSet[0],$orderSet[1]);
        return $query->groupby('user_id')->paginate($iPage);

    }


}
