<?php

namespace JcModel;
/**
 * 注单模型
 */
class ManJcCommissionUser extends JcCommissionUser {
    
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcCommissionUser';
    protected $table = 'jc_commission_user';

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id'             => 'required|integer',
        'single_rate'             => 'required|numeric|min:0',
        'multiple_rate'              => 'required|numeric|min:0',
    ];

    public static function getCommissionUsersByUserIds($aIds){
        return self::whereIn('user_id',$aIds)->orderBy('user_id','desc')->get();
    }
    
}
