<?php
class MsgMessage extends BaseModel {
    protected $table = 'msg_messages';

    const ERRNO_MISSING_MESSAGE = -2300;

    protected $fillable = [
        'type_id',
        'sender_id',
        'sender',
        'for_admin',
        'title',
        'content',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'updated_at' => 'desc'
    ];
    public static $titleColumn = 'title';

    public static $resourceName = 'MsgMessage';
    public static $mainParamColumn = 'type_id';
    public static $columnForList = [
        'type_id',
        'sender',
        'for_admin',
        'title',
        'content',
        'updated_at',
    ];
    public static $rules = [
        'type_id'   => 'integer',
        'sender_id' => 'integer',
        'sender'    => 'between:0,50',
        'title'     => 'required|between:1,30',
        'content'   => 'required',
        'for_admin' => 'in:0,1',
    ];
    public static $htmlSelectColumns = [
        'type_id' => 'aMsgTypes',
    ];
    public static $htmlTextAreaColumns = [
        'content',
    ];

    public static $ignoreColumnsInEdit = ['sender_id','sender'];

    public function msg_types()
    {
        return $this->belongsTo('MsgType', 'type_id');
    }

    public function users()
    {
        return $this->belongsToMany('User', 'msg_users', 'msg_id', 'receiver_id')->withTimestamps();
    }

    public function admin_users()
    {
        return $this->belongsToMany('AdminUser', 'msg_users', 'msg_id', 'sender_id')->withTimestamps();
    }

    protected function beforeValidate() {
        // pr(Session::get('admin_user_id'));
        // pr(Session::get('admin_username'));
        // exit;
        $this->sender_id = Session::get('admin_user_id');
        $this->sender    = Session::get('admin_username');
        $this->for_admin = MsgType::find($this->type_id)->for_admin;
        return parent::beforeValidate();

}
}
