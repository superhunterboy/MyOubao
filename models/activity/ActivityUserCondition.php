<?php

/**
 * Class ActivityUserCondition - 活动规则类表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityUserCondition extends BaseModel implements ActivityTaskTypeInterface, FactoryObjectClassInterface {

    public static $resourceName = 'ActivityUserCondition';

    const STATUS_NOT_DONE = 0;
    const STAUTS_DONE = 1;

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'activity_user_conditions';
    static $unguarded = true;
    protected $fillable = [
        'user_id',
        'activity_id',
        'task_id',
        'condition_id',
        'data',
        'status',
        'finish_time',
    ];
    public static $columnForList = [
        'user_id',
        'activity_id',
        'task_id',
        'condition_id',
        'status',
        'finish_time',
    ];
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'task_id' => 'aTasks',
        'user_id' => 'aUsers',
        'status' => 'aStatus',
        'condition_id'=>'aActivityConditions',
    ];
    public static $aStatus = [
        self::STATUS_NOT_DONE => 'not-done',
        self::STAUTS_DONE => 'done',
    ];

    /**
     * 用户信息
     */
    public function user() {
        return $this->hasOne('User', 'id', 'user_id');
    }

    /**
     * 任务信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task() {
        return $this->belongsTo('ActivityTask', 'task_id', 'id');
    }

    /**
     * 用户任务信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTask() {
        return ActivityUserTask::where('activity_id', '=', $this->activity_id)
                        ->where('user_id', '=', $this->user_id)
                        ->where('task_id', '=', $this->task_id);
    }

    /**
     * 条件
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function condition() {
        return $this->belongsTo('ActivityCondition', 'condition_id', 'id');
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
     * 完成
     *
     * @return bool
     */
    public function completed()
    {
        $this->status = 1;
        $this->finish_time = date('Y-m-d H:i:s');
        return $this->save();
    }

    /**
     *
     * 扩展参数修饰器
     *
     * @param $value
     * @return array
     */
    public function getDatasAttribute($value) {
        return (array) @json_decode($this->attributes['data']);
    }

    /**
     * 扩展参数修改器
     *
     * @param $value
     */
    public function setDatasAttribute($value) {
        $data = $this->datas;
        $data = array_merge($data, $value);

        $this->attributes['data'] = json_encode($data);
    }

    /**
     * 判断条件是否完成
     *
     * @return mixed
     */
    public function isFinsh() {
        
        return $this->task()->first()->isFinsh($this);
    }

    /**
     * 条件内送奖品
     *
     * @param $prize_id
     * @param $count
     * @return bool
     */
    public function send($prize_id, $count) {
        $model = new ActivityUserPrize();
        $model->activity_id = $this->activity_id;
        $model->prize_id = $prize_id;

        $model->count = $count;
        $model->user_id = $this->user_id;
        $model->source = 1;
        $model->status = 0;
        $model->is_verified = 1;
        return $model->save();
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
