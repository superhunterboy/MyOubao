<?php

/**
 * Class ActivityConditionClass - 活动规则类表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityConditionClass extends BaseModel {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    protected $table = 'activity_condition_classes';
    protected $fillable = [
        'name',
        'class_name',
        'params',
        'event',
    ];
    public static $columnForList = [
        'name',
        'class_name',
        'params',
        'event',
    ];
    public static $rules = [
        'name' => 'required|between:1,50',
        'class_name' => 'required|between:1,50',
        'params' => 'between:0,5000',
        'event' => 'between:1,50',
    ];
    public static $titleColumn = 'name';
    public static $resourceName = 'ActivityConditionClass';

    /**
     * 获得所有活动条件
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function conditions() {
        return $this->hasMany('ActivityCondition', 'condition_class_id', 'id');
    }

    /**
     * 根据事件获得所有相关类
     *
     * @param $event
     * @return static
     */
    static public function findAllByEvent($event) {
        return static::where('event', '=', $event)->get();
    }

//    /**
//     * 获得所有有效的事件
//     *
//     * @return mixed
//     */
//    static public function findAllValidEvent()
//    {
//        //获取到所有有效的的活动
//        $activityIds   = Activity::findAllValidActivity()->lists('id');
//        //根据活动获取到相关事件ID
//        $conditionClassIds = empty($activityIds) ? [] : ActivityCondition::findAllValidByActivity($activityIds)->lists('condition_class_id');
//
//        //根据事件ID获取到所有事件信息
//        return empty($conditionClassIds) ? [] : static::remember(5)
//                    ->where('event', '<>', '')
//                    ->whereIn('id', $conditionClassIds)
//                    ->get();
//    }

}
