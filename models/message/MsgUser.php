<?php

class MsgUser extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'msg_users';
    protected $softDelete = true;
    public static $resourceName = 'MsgUser';
    public static $mainParamColumn = 'receiver_id';

    public static $rules = [
        'received_id' => 'integer',
        'receiver'    => 'required|between:3,16',
        'sender_id'   => 'integer',
        'sender'      => 'required|between:3,16',
        'msg_id'      => 'required|integer',
        'msg_title'   => 'required|between1,30',
        'is_keep'     => 'in:0,1',
        'is_to_all'   => 'in:0,1',
        'is_readed'   => 'in:0,1',
        'is_deleted'  => 'in:0,1',
        'readed_at'   => 'date',
    ];

    protected $fillable = [
        'received_id',
        'receiver',
        'sender_id',
        'sender',
        'msg_id',
        'is_keep',
        'is_to_all',
        'is_readed',
        'is_deleted',
        'readed_at',
        'deleted_at',
    ];

    public static $columnForList = [
        'receiver',
        'sender',
        'msg_title',
        'is_keep',
        'deleted_at',
        'readed_at',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'updated_at' => 'desc'
    ];

    public static $aDeletedStatus = ['未删', '已删'];
    public static $aReadedStatus  = ['未读', '已读'];

    public static $ignoreColumnsInView = ['receiver_id','sender_id','msg_id','type_id','is_keep','is_to_all', 'is_readed','is_deleted','updated_at'];

}