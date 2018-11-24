<?php

/**
 * Class ActivityUserTask - 活动用户任务表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityUserTask extends BaseModel implements ActivityTaskTypeInterface {
    public static $resourceName = 'ActivityUserTask';
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'activity_user_tasks';
    static $unguarded = true;
    public static $columnForList = [
        'activity_id',
        'task_id',
        'user_id',
        'status',
        'is_signed',
        'signed_time',
        'finish_time',
    ];
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'task_id' => 'aTasks',
        'user_id' => 'aUsers',
    ];
    public static $rules=[
        'is_signed'=>'in:0,1',
        'status'=>'in:0,1',
    ];

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User', 'user_id', 'id');
    }


    /**
     * 获得任务
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task() {
        return $this->belongsTo('ActivityTask', 'task_id', 'id');
    }

    /**
     * 用户所有的条件完成情况
     *
     */
    public function userConditions() {
        return ActivityUserCondition::where('task_id', '=', $this->task_id)
                        ->where('user_id', '=', $this->user_id);
    }

    /**
     * 提供给类型的接口
     *
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * 获得结束时间,提供给类型的接口
     *
     * @return mixed
     */
    public function getFinshTime() {
        return $this->finish_time;
    }

    /**
     * 判断条件是否完成
     *
     * @return mixed
     */
    public function isFinsh() {
        return $this->task()
                        ->first()
                        ->isFinsh($this);
    }

    public function checkTaskExist($task_id) {
        if (!is_array($task_id))
            $task_id = [$task_id];
        if (empty($this->where('user_id', Session::get('user_id'))->wherein("task_id", $task_id)->get()->first())) {
            return false;
        }
        return true;
    }

    /**
     *  完成
     *
     * @return bool
     */
    public function completed() {
        $this->status = 1;
        $this->finish_time = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     * 根据活动信息获得所有用户任务信息
     *
     * @param ActivityUserInfo $userInfo
     * @return mixed
     */
    static public function findAllByActivityUser($activity_id,$user_id) {
        
        $data = [];

        $userTasks = self::where('user_id', '=', $user_id)
                         ->where('activity_id', '=', $activity_id)
                         ->get();

        foreach ($userTasks as $userTask) 
        {
            $task = $userTask->task()->remember(5)->first();

            $uData = $userTask->toArray();
            $uData['task_name'] = $task->name;
            $uData['isFinsh'] = $userTask->isFinsh();

            foreach ($userTask->userConditions()->get() as $userConditions) {

                $condition = $userConditions->condition()->remember(5)->first();

                if (!empty($condition)) 
                {
                    $uData['condition'][$userConditions['id']] = $userConditions->toArray();
                    $uData['condition'][$userConditions['id']]['condition_name'] = $condition->name;
                    $uData['condition'][$userConditions['id']]['isFinsh'] = $userConditions->isFinsh();
                }
            }

            $data[$userTask['task_id']] = $uData;
        }
        return $data;
    }


    static public function findAllByDateActivityUser($activity_id,$user_id,$start_time,$end_time)
    {
        $data = [];

        $userTasks = self::where('user_id', '=', $user_id)
                         ->where('activity_id', '=', $activity_id)
                         ->whereBetween('finish_time',[$start_time,$end_time])
                         ->get();

        foreach ($userTasks as $userTask) 
        {
            $task = $userTask->task()->remember(5)->first();

            $uData = $userTask->toArray();
            $uData['task_name'] = $task->name;
            $uData['isFinsh'] = $userTask->isFinsh();

            foreach ($userTask->userConditions()->get() as $userConditions) {

                $condition = $userConditions->condition()->remember(5)->first();

                if (!empty($condition)) 
                {
                    $uData['condition'][$userConditions['id']] = $userConditions->toArray();
                    $uData['condition'][$userConditions['id']]['condition_name'] = $condition->name;
                    $uData['condition'][$userConditions['id']]['isFinsh'] = $userConditions->isFinsh();
                }
            }

            $data[$userTask['task_id']] = $uData;
        }
        return $data;
    }

    /**
     * 验证之前操作
     *
     * @return bool
     */
    protected function beforeValidate() {
        $user   = $this->user()->first();
        $this->username = $user->username;

        return parent::beforeValidate();
    }

}
