<?php

/**
 * 用户代金券日志
 */
class UserVoucherLog extends BaseModel {
//    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    public static $resourceName = 'UserVoucherLog';
    protected $table = 'user_voucher_logs';
    public static $amountAccuracy = 2;

    /**
     * the columns for list page
     * @var array
     */

    protected $fillable = [
        'user_id',
        'username',
        'lottery_id',
        'voucher_id',
        'amount',
    ];
    public static $columnForList = [
        'user_id',
        'username',
        'is_tester',
        'lottery_id',
        'voucher_id',
        'amount',
        'created_at',
        'updated_at',
    ];
    public static $listColumnMaps = [
        'is_tester'     => 'is_tester_formatted',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'id' => 'desc'
    ];
    public static $htmlSelectColumns = [
        'voucher_id'     => 'aVouchers',
    ];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer',
        'username' => 'required',
        'lottery_id' => 'integer',
        'voucher_id' => 'integer',
        'amount' => 'numeric',
    ];
    
    public function getIsTesterFormattedAttribute(){
        return yes_no(intval($this->is_tester));
    }
    
}
