<?php

namespace JcModel;
/**
 * 注单模型
 */
class JcCommission extends \BaseModel {
    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'JcCommission';
    protected $table = 'jc_commissions';

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'asc'
    ];
    

    protected $fillable = [
        'lottery_id',
        'project_id',
        'user_id',
        'account_id',
        'username',
        'is_tester',
        'user_forefather_ids',
        'serial_number',
        'coefficient',
        'multiple',
        'base_amount',
        'amount',
        'status',
    ];

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'lottery_id'    => 'required|integer',
        'project_id'    => 'required|integer',
        'user_id'       => 'required|integer',
        'username'       => 'required',
        'serial_number'    => 'required',
        'coefficient'    => 'required|in:0.01,0.1,1',
        'multiple'      => 'required|integer',
        'status'        => 'integer',
        'base_amount'   => 'required|numeric',
        'amount'        => 'required|numeric',
        'is_tester'     => 'required|in:0,1',
    ];
    
    const STATUS_NORMAL = 0;
    const STATUS_DROPED = 1;
    const STATUS_SENT    = 2;
    
    public static function getSumCommissionByBetId($iBetId){
        return self::where('bet_id', $iBetId)
                ->where('status', self::STATUS_SENT)
                ->sum('amount');
    }
}
