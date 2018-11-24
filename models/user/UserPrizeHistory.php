<?php
/**
 * Created by PhpStorm.
 * User: echo
 * Date: 15-6-19
 */

class UserPrizeHistory extends BaseModel
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agent_prize_group_log';
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'UserPrizeHistory';
    public static $columnForList = [
        'id',
        'agent_id',
        'assign_agent_id',
        'prize_group',
        'plus_num',
        'subtract_num',
        'note',
        'updated_at',
        'created_at',
    ];
    public static $rules = [
        'agent_id'   => 'required|integer',
        'assign_agent_id'=>'required|integer',
        'plus_num'      => 'required|integer',
        'subtract_num'      => 'required|integer',
    ];

}