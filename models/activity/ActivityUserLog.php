<?php

/**
 * ActivityUserLog
 *
 */
class ActivityUserLog extends BaseModel {

    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;

    /**
     * 状态：未中奖
     */
    const STATUS_NOT_GET_PRIZE = 0;

    /**
     * 状态：已中奖
     */
    const STATUS_IS_GET_PRIZE = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'activity_user_logs';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    public static $resourceName = 'ActivityUserLog';
    public static $mainParamColumn = '';
    public static $titleColumn = null;
    protected $fillable = [
        'activity_id',
        'activity_name',
        'user_id',
        'username',
        'user_prize_today',
        'user_left_chance',
        'user_contribution',
        'status',
        'action_time',
        'rules_detail',
    ];
    public static $rules = [
        'activity_id' => 'required|integer',
        'activity_name' => 'required|between:1,45',
        'user_id' => 'required|integer',
        'username' => 'required|alpha_num|between:6,16',
        'user_prize_today' => 'integer',
        'user_left_chance' => 'integer',
        'user_contribution' => 'numeric',
        'action_time' => 'required|date',
//        'rules_detail' => '',
    ];

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'activity_name',
        'username',
        'user_prize_today',
        'user_left_chance',
//        'user_contribution',
        'status',
        'action_time',
    ];
    public static $htmlSelectColumns = [];
    public static $ignoreColumnsInEdit = [];

}
