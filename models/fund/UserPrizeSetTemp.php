<?php

class UserPrizeSetTemp extends UserPrizeSet {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_prize_set_temps';
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = [
        'user_id',
        'username',
        'user_parent_id',
        'user_parent',
        'lottery_id',
        'group_id',
        'prize_group',
        'classic_prize',
        'valid',
        'is_agent',
        'valid_days',
        'expired_at'
    ];

    public static $resourceName = 'User Prize Set Temp';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'lottery_id',
        'prize_group',
        'classic_prize',
        'valid',
        'valid_days',
        'expired_at'
    ];
    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
        'lottery_id' => 'aLotteries',
//        'group_id' => 'aPrizeGroups',
    ];

    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'user_id' => 'asc'
    ];

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = false;

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = '';

    public static $rules = [
        'user_id'   => 'required|integer',
        'group_id'  => 'required|integer',
    ];

    public static $aUserTypes = ['Top Agent', 'Agent'];

    const ERRNO_MISSING_PRIZE_SET = -940;

    protected function getUserTypeFormattedAttribute()
    {
        return static::$aUserTypes[intval($this->parent_id != null)];
    }

}