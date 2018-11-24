<?php

/**
 * Class ActivityPrizeClass - 活动奖品类表
 *
 * @author Johnny <Johnny@anvo.com>
 */
class ActivityPrizeClass extends BaseModel {
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel= self::CACHE_LEVEL_FIRST;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_prize_classes';
    public static $titleColumn = 'name';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'name',
        'class_name',
        'params',
    ];
    public static $resourceName = 'ActivityPrizeClass';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'name',
        'class_name',
        'params',
    ];
    public static $htmlSelectColumns = [
    ];
    public static $ignoreColumnsInEdit = ['activity_name'];
    public static $rules = [
        'name' => 'required|between:0,50',
        'class_name' => 'between:0,50',
        'params' => 'between:0,5000',
    ];

}