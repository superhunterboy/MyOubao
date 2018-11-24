<?php

/**
 * Class Activitys - 活动表
 *
 */
class Activity extends BaseModel {

    /**
     * 活动状态：开启
     */
    const STATUS_OPEN = 1;

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activities';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'prize_limit',
        'admin_id',
        'admin_name',
        'start_time',
        'end_time',
    ];
    public static $resourceName = 'Activity';

    /**
     * The columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'prize_limit',
        'admin_name',
        'start_time',
        'end_time',
    ];
    public static $titleColumn = 'name';
    public static $ignoreColumnsInEdit = ['admin_id', 'admin_name'];
    public static $rules = [
        'name' => 'required|between:0,45',
        'prize_limit' => 'required|integer',
        'admin_id' => 'required|integer',
        'admin_name' => 'required|between:1,16',
        'start_time' => 'required|date',
        'end_time' => 'required|date',
    ];

    /**
     * 活动是否有效
     *
     * @return bool
     */
    public function isValidateActivity() {
        $now = date('Y-m-d H:i:s');
        if ($this->start_time <= $now && $this->end_time >= $now) {
            return true;
        }
        return false;
    }

    /**
     * 获得所有有效的活动
     *
     * @return mixed
     */
    public static function findAllValidActivity()
    {
        $now    = date('Y-m-d H:i:s');

        //缓存5分钟
        return self::remember(5)
                    ->where('start_time', '<=', $now)
                    ->where('end_time', '>=', $now)
                    ->get();
    }

}
