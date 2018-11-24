<?php

/**
 * Class ActivityUserTask - 活动用户任务表
 *
 * @author Roy
 */
class ActivityUserTaskFeedBack extends BaseModel
{   
    //@todo Event - name can be retrieve from /activity-condition-classes/index
    public static $availableEventName = 'bomao.activity.stage';

    public static $resourceName = 'ActivityUserTaskFeedBack';
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'activity_user_task_feedbacks';
    static $unguarded = true;
    public static $columnForList = [
        'task_id',
        'user_id',
        'date',
    ];
    public static $htmlSelectColumns = [
        'task_id' => 'aTasks',
        'user_id' => 'aUsers',
    ];
    public static $rules = [
        'task_id' => 'required|integer',
        'user_id' => 'required|integer',
        'date' => 'date',
    ];

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    /**
     * 获得任务
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo('ActivityTask', 'task_id', 'id');
    }

    /**
     * 获得用户指定的任务信息
     *
     * @param $task_id
     * @param $user_id
     * @return mixed
     */
    static public function findByTaskUser($task_id, $user_id)
    {
        return self::where('task_id', '=', $task_id)
                    ->where('user_id', '=', $user_id)
                    ->whereBetween('date', [date('Y-m-d'), date('Y-m-d'). ' 23:59:59'])
                    ->first();
    }

    /**
     * @todo Event fire after user : 
     *       a. deposit
     *       b. withdraw
     *       c. cancel
     *       d. saved
     * 
     * @return bool
     */
    public static function updateEvent()
    {
        //@todo - function used in BetController , TransactionFlowController
        //Event will automatically trigger when the date is expire
        Event::fire(self::$availableEventName,['user_id' => Session::get('user_id')]);
    }

    /**
    * @todo Gather information from database to display in /transaction-flow/index
    * @param $activity_id from Activity table
    * @return object - consist of table data for display
    * @author Roy
    */
    static public function getActivityConditionsNPrice($activity_id)
    {
        if(empty($activity_id))
        {
            return FALSE;
        }

        $oActivityTask = ActivityTask::where('activity_id','=',$activity_id)->get();

        foreach($oActivityTask as $key => & $per_oTask)
        {
            //Retrieve from database table only once instead of keep looping
            $aActivityTaskPrize = $per_oTask->taskPrizes()->get(['prize_id'])->toArray();

            foreach($aActivityTaskPrize as $per_aActivityTaskPrize)
            {
                $oActivityTask[$key]->task_reward = ActivityPrize::getPrizeInfoByPrizeId($per_aActivityTaskPrize['prize_id'],['value'],TRUE,TRUE);
            }   

            $oActivityCondition = ActivityCondition::where('task_id','=',$oActivityTask[$key]->id)
                                                     ->where('activity_id','=',$oActivityTask[$key]->activity_id)
                                                     ->get(['params'])->first();
              
            if(isset($oActivityCondition->params))
            {
                $aConditions = json_decode($oActivityCondition->params);
                $per_oTask->transaction = $aConditions->transaction;
            }
            
        }

        return $oActivityTask;
    }
}
