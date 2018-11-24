<?php
class AgentCreateStat extends BaseModel{
    protected $table = 'agent_create_stat';
    protected $fillable = [
        'agent_id',
        'total',
        'parent_agent_id',
        'month'
    ];
    protected $primaryKey   =   'agent_id';


    /**
     * 更新代理开户量
     * @param type $agent_id
     * @return type
     */
    public static function updateAgentCreateStat($agent_id = null){
        if(!$agent_id) return;
        $oUser = User::find($agent_id);
        $data = [
            'agent_id'        => $agent_id,
            'parent_agent_id' => $oUser->parent_id,
            'month'           => date('Ym')
        ];
        $oStat = self::firstOrCreate($data);
        $oStat->increment('total');
    }

    /**
     * o获取开户量为前10的代理
     * @param type $agent_id
     * @return type
     */
    public static function getTop10AgentCreateStat($agent_id = null){
        if(!$agent_id) return;
        return self::join('users','users.id','=','agent_create_stat.agent_id')->where(function($query) use ( $agent_id ){

            $query->Where('parent_agent_id','=',$agent_id);
        })->where('month','=',date('Ym'))->orderBy('total','desc')->limit(0,10)->get(['agent_id as user_id','total as data','username','prize_group']);

    }
    /**
     * 获取代理排名
     * @param type $rankBy
     * @param type $iCurrentUserId
     * @return type
     */
    public static function getMyAgentRank($rankBy = 'sale',$iCurrentUserId = null){
        if(!$iCurrentUserId) return;

        $aData = [];

        //$where = ['parent_user_id=$iCurrentUserId ], 'date' => [ '>=', date('Y-m-1'), 'date' <= date('Y-m-d') ]];
        $where =" parent_user_id=".$iCurrentUserId." and date between '".date('Y-m')."-01' and '".date('Y-m-d')."'";
        $selectRaw = ' user_profits.user_id, user_profits.username,user_profits.prize_group ';
        switch($rankBy){
            case 'newaccount':

                break;
            case 'profit':
                $selectRaw .=',if(sum(team_profit + profit + team_dividend +dividend + team_commission + commission)<0,sum(team_profit + profit + team_dividend +dividend + team_commission + commission),0) as data';
                $order = ' asc';
                break;
            case 'sale':
                $order = ' desc';
                $selectRaw .= ',sum(turnover+team_turnover) as data';
        }

        $userTotal = [];

        //排名为开户数的开户数
        if ($rankBy == 'newaccount')
        {
            if($aoAgents = self::getTop10AgentCreateStat($iCurrentUserId)->toArray()){

                return $aoAgents;
            }
            return $aData;
        }

        //$oUserProfit = UserProfit::doWhere($where)->select(DB::raw($selectRaw))->groupBy('user_id')->orderBy('data','desc')->get()->toArray();    
        $sql= 'select *  from (select '.$selectRaw.' from user_profits  where '.$where.' group by user_id ) as tmp order by  data '.$order.' limit 0,10';

        $oUserProfits = DB::select($sql);
        $aReturn = [];
        foreach($oUserProfits as $oValue){
            $aReturn[] =['user_id'=>$oValue->user_id,'username'=>$oValue->username, 'prize_group'=>$oValue->prize_group,'data'=>$oValue->data];
        }

        return $aReturn;
    }

    /**
     * 获取我的团队数据
     * @param type $iUserId
     * @param type $iFromDate
     * @param type $iEndDate
     * @return type
     */
    public static function getMyTeamData($iUserId,$iFromDate,$iEndDate){

        $endDate = date("Y-m-d 23:59:59", $iEndDate);
        $startDate = date("Y-m-d 00:00:01", $iFromDate);

        $iNewUserCount = UserUser::whereRaw(' find_in_set(?, forefather_ids) ', [$iUserId])->whereBetween('register_at', [$startDate,$endDate])->count();

        $iNewPlayerCount = UserUser::whereBetween('register_at',[$startDate,$endDate])->where('parent_id', '=', $iUserId)->where('is_agent', '=', UserUser::TYPE_AGENT)->count();

        $aData = [
            'howmanypeoplebet'=>UserLastBet::getUserStatisticBetweenDate($iUserId,$iFromDate,$iEndDate),
            'howmanynewaccount' =>$iNewUserCount,
            'howmanynewplayer' =>$iNewPlayerCount,
        ];
        return $aData;
    }
}
