<?php
/**
 * Class ActivityTaskFeedBackCondition - 判断任务是否已经有了反馈 | Information about the task's feedback
 *
 * @author Roy
 */
class ActivityTaskFeedBackCondition extends BaseActivityCondition
{
    /**
     * 参数列表
     *
     * @var array
     */
    static protected  $params = [
    ];

    /**
     * 条件是否满足
     *
     * @param FactoryObjectClassInterface $userCondition
     * @return bool
     */
    public function complete($userCondition)
    {
        return ActivityUserTaskFeedBack::where('user_id', '=', $userCondition->user_id)
            ->where('task_id', '=', $userCondition->task_id)
            ->whereBetween('date', [date('Y-m-d'), date('Y-m-d').' 23:59:59'])
            ->exists();
    }
}