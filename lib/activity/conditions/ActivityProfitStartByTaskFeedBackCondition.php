<?php
/**
 * WaterFall Logic
 * Class ActivityProfitStartByTaskFeedBackCondition - 根据任务报名时间计算流水
 *
 * @author Roy
 */
class ActivityProfitStartByTaskFeedBackCondition extends BaseActivityCondition
{
    /**
     * 参数列表
     *
     * @var array
     */
    static protected  $params=[
        
        //流水要求
        'transaction'=>'流水额度',

        //previous stage of Task ID
        'prev_task_id' => '前阶段的任务ID',
    ];

    /**
    * @todo  Verify the time to make sure user click at the button
    * @param $task_id from ActivityTask table
    * @param $user_id from User table
    * @author Roy
    * @return time ( yyyy-mm-dd )
    */

    public function isTimeSatisfied($task_id,$user_id)
    {
        $taskFeedBack   = ActivityUserTaskFeedBack::findByTaskUser($task_id,$user_id);

        //有报名,并且报名时间为当天
        if ($taskFeedBack && date('Y-m-d', strtotime($taskFeedBack->date) == date('Y-m-d'))) //no register meaning not satisfied the condition
        {
            $startTime  = $taskFeedBack->date;
        }
        else
        {
            return FALSE;   
        }

        return $startTime;
    }

    /**
     * 条件是否满足
     *
     * @param FactoryObjectClassInterface $userCondition
     * @author Roy
     * @return bool
     */
    public function complete($userCondition)
    {
        $prev_task_id = $this->data->get('prev_task_id');
        $transaction  = intval($this->data->get('transaction'));

        $startTime  = Carbon::today();
        $endTime    = Carbon::now();

        if($prev_task_id)
        {
            if(!$startTime = $this->isTimeSatisfied($prev_task_id, $userCondition->user_id))
            {
                return FALSE;
            }
        }
 
        $profit  = Project::getCurrentDayTurnover($userCondition->user_id, $startTime, $endTime);
        
        return intval($profit) >= $transaction;
    }

}