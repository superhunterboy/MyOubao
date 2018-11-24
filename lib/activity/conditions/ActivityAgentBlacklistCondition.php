<?php
/**
 * Class ActivityAgentBlacklistCondition - 代理商黑名单条件
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityAgentBlacklistCondition extends BaseActivityCondition
{
    /**
     * 参数列表
     *
     * @var array
     */
    static protected  $params=[
        'agents'=>'代理商列表',
    ];

    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($userCondition)
    {
        $data = $userCondition->user()->first()->toArray();
        $agents = explode(',', $data['forefather_ids']);
        $blacklist = explode(',', $this->data->get('agents'));

        $blacklistids = User::getUsersByUsernames($blacklist)->fetch('id')->toArray();

        //为空代表不在禁止范围内,返回true
        return empty(array_intersect($blacklistids, $agents));
    }
}