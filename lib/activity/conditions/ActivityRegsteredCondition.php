<?php
/**
 * Class ActivityRegsteredCondition - 注册条件
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityRegsteredCondition extends BaseActivityCondition
{
    /**
     * 参数列表
     *
     * @var array
     */
    static protected  $params=[
        'start_time'=>'注册开始时间',
        'end_time'=>'注册结束时间',
    ];

    /**
     * 条件是否满足
     *
     * @return bool
     */
    public function complete($userCondition)
    {
        $data = $userCondition->user()->first()->toArray();
        $registration_time  = $data['register_at'];

        $userCondition->data    = json_encode($data);

        //临时先加上代理商身份限制,回头把这里做成一个身份限制条件类
        if ($data['is_agent'])
        {
            return false;
        }

        if ($registration_time >= $this->data->get('start_time') && $registration_time < $this->data->get('end_time') )
        {
            return true;
        }
        return false;
    }
}