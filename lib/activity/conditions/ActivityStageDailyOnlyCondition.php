<?php
/**
 * It's to ensure that the player only can participate the game 1 time within a day, not twice
 * Class ActivityStageDailyOnlyCondition - 控制单个IP单个活动最大得奖次数
 *
 * @author Roy
 */
class ActivityStageDailyOnlyCondition extends BaseActivityCondition
{
    /*
    @Note
    - 1 ip
    - 1 activities
    - Everyday 1 time activities
    */

    /**
     * 参数列表
     *
     * @var array
     */
    static protected  $params = [
        'max_num'   => '最大次数',
        'prize_ids' => '奖品列表',
        'source'    => '来源',
        'isAll'     => '是否应用全部活动',//是否应用全部活动, 1为是 0为否
    ];

    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($oUser)
    {
        $data = $oUser->user()->first();

        //1. Check remote IP
        $query  = ActivityUserPrize::whereIn('remote_ip',[
            $data->login_ip, 
            $data->register_ip
        ])->whereBetween('created_at',[
            Carbon::today(),
            Carbon::now()
        ]);

        //Note
        //1. Once user participate the game , meet conditions, then data will auto occur in ActivityUserPrize
        //2. Check only 1 day 1 activities

        if (!$this->data->get('isAll'))
        {
            $query  = $query->where('activity_id', '=', $oUser->activity_id);
        }

        if ($this->data->get('source'))
        {
            $query  = $query->where('source', '=', $this->data->get('source'));
        }

        if ($this->data->get('prize_ids'))
        {
            $prize_ids  = @explode(',', $this->data->get('prize_ids'));
            $query  = $query->whereIn('prize_id', $prize_ids);
        }

        $sum = $query->sum('count');

        //1 - already participated and retrieve the prize
        return ($sum < $this->data->get('max_num'));
    }
}