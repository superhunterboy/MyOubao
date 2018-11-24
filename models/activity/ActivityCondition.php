<?php

/**
 * Class ActivityCondition - 活动规则表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityCondition extends BaseModel implements FactoryClassInterface {

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $sequencable = true;
    protected $table = 'activity_conditions';
    protected $fillable = [
        'activity_id',
        'name',
        'task_id',
        'task_name',
        'condition_class_id',
        'condition_class_name',
        'params',
        'sequence',
    ];
    public static $resourceName = 'ActivityCondition';
    public static $htmlSelectColumns = [
        'activity_id' => 'aActivities',
        'condition_class_id' => 'aActivityConditionClasses',
        'task_id' => 'aTasks',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'task_name',
        'name',
        'condition_class_name',
        'params',
        'sequence',
    ];
    public static $titleColumn = 'name';
    public static $ignoreColumnsInEdit = ['activity_name', 'task_name', 'condition_class_name'];
    public static $rules = [
        'name' => 'required|between:0,45',
        'task_name' => 'required|between:0,50',
        'condition_class_name' => 'required|between:0,50',
        'activity_id' => 'required|integer',
        'task_id' => 'required|integer',
        'condition_class_id' => 'required|integer',
        'params' => 'between:0,5000',
        'sequence' => 'required|integer',
    ];

    /**
     * 条件类
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conditionClass() {
        return $this->belongsTo('ActivityConditionClass', 'condition_class_id', 'id');
    }

    /**
     * 获得任务信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task() {
        return $this->belongsTo('ActivityTask', 'task_id', 'id');
    }

    /**
     * 获得实现类类名
     *
     * @return mixed
     */
    public function getClassName() {
        $class = $this->conditionClass()->first();
        return $class->class_name;
    }

    /**
     * 获得实现类所需参数
     *
     * @return mixed
     */
    public function getParams() {
        return $this->param;
    }

    /**
     *
     * 扩展参数修饰器
     *
     * @param $value
     * @return array
     */
    public function getParamAttribute($value) {
        $data = (array) json_decode($this->attributes['params']);
        $task = $this->task()->first();

        $arr = [
            'start_time' => $task->task_start_time,
            'end_time' => $task->task_end_time,
        ];

        return array_merge($arr, $data);
    }

    /**
     * 扩展参数修改器
     *
     * @param $value
     */
    public function setParamAttribute($value) {
        $params = $this->param;
        $params = array_merge($params, $value);

        $this->attributes['params'] = json_encode($params);
    }

    /**
     * 根据事件名称获得相关任务
     *
     * @param $event
     */
    public static function findAllByEvent($event) {
        return parent::where('event', '=', $event)->get();
    }

    /**
     * 验证之前操作
     *
     * @return bool
     */
    protected function beforeValidate() {
        $oActivity = Activity::find($this->activity_id);
        if (is_object($oActivity)) {
            $this->activity_name = $oActivity->name;
        } else {
            return false;
        }
        $oActivityTask = ActivityTask::find($this->task_id);
        if (is_object($oActivityTask)) {
            $this->task_name = $oActivityTask->name;
        } else {
            return false;
        }
        $oActivityConditionClass = ActivityConditionClass::find($this->condition_class_id);
        if (is_object($oActivityConditionClass)) {
            $this->condition_class_name = $oActivityConditionClass->name;
        } else {
            return false;
        }
        return parent::beforeValidate();
    }

}
