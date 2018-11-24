<?php

class AccountSnapshot extends BaseModel {

    public static $resourceName = 'AccountSnapshot';
    /**
     * The rules to be applied to the data.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'username' => 'required',
        'available' => 'numeric',
    ];
    protected $fillable = [
        'user_id',
        'username',
        'available',
        'team_turnover',
        'team_prize',
        'team_deposit',
        'team_profit',
        'team_withdrawal',
        'team_commission',
        'date',
    ];

    protected $table = 'account_snapshots';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'user_id',
        'username',
        'available',
        'team_turnover',
        'team_prize',
        'team_deposit',
        'team_profit',
        'team_withdrawal',
        'team_commission',
        'date',
    ];

    const UNBLOCK            = 0;
    const BLOCK_LOGIN        = 1;
    const BLOCK_BUY          = 2;
    const BLOCK_FUND_OPERATE = 3;

    public static $blockedTypes = [
        self::UNBLOCK            => 'unblock',
        self::BLOCK_LOGIN        => 'block-login',
        self::BLOCK_BUY          => 'block-bet',
        self::BLOCK_FUND_OPERATE => 'block-fund',
    ];

}
