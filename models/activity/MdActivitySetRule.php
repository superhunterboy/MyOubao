<?php

class MdActivitySetRule extends BaseModel {
    public static $resourceName = 'activity_md_setrule';
    /**
     * 开启CACHE机制
     *
     * CACHE_LEVEL_FIRST : memcached
     *
     * @var int
     */
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'activity_md_setrule';
//    static $unguarded = true;
    public static $columnForList = [
        'name',
        'day_times',
        'vr_price',
        'total_times',
        'rand_num',
        'gift_totals',
    ];
    public static $htmlSelectColumns = [
//        'activity_id' => 'aActivities',
//        'task_id' => 'aTasks',
//        'user_id' => 'aUsers',
    ];
    public static $rules=[
        'is_signed'=>'in:0,1',
        'status'=>'in:0,1',
    ];

 

}

