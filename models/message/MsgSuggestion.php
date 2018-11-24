<?php
class MsgSuggestion extends BaseModel
{
    protected $table = 'msg_suggestions';

    protected $fillable = [
        'user_id',
        'username',
        'type',
        'comment',
        'remark',
        'ip',
    ];
    /**
     * order by config
     * @var array
     */
    public $orderColumns = [
        'created_at' => 'desc'
    ];
    public static $titleColumn = 'title';

    public static $resourceName = 'MsgSuggestion';
    public static $mainParamColumn = 'id';
    public static $columnForList = [
        'id',
        'username',
        'comment',
        'created_at',
    ];
    public static $rules = [
        'id'        => 'integer',
        'user_id'   => 'required|integer',
        'type'      => 'integer',
        'username'  => 'between:0,50',
        'ip'        => 'between:0,15',
        'comment'   => 'required|between:1,2000',
    ];

    const ERRNO_SUGGESTION_SUBMIT_FAILED = -2001;
    const ERRNO_SUGGESTION_OVERLOAD      = -2002;
    const ERRNO_SUGGESTION_NO_RIGHT      = -2003;
    const ERRNO_SUGGESTION_SIZE_OVERLOAD = -2004;

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() {
        return $this->belongsTo('User', 'user_id', 'id');
    }

    /**
     * 获得用户今日建议信息
     *
     * @param $user_id
     * @return mixed
     */
    public static function findUserTodayMsg($user_id)
    {
        return self::where('user_id', '=', $user_id)
                    ->whereBetween('created_at', [Carbon::today()->toDateString(), Carbon::today()->toDateString() . ' 23:59:59']);
    }

    /**
     * 保存之前
     *
     * @return mixed
     */
    protected function beforeValidate()
    {
        if ($this->username===null)
        {
            $user   = $this->user()->first() and $this->username = $user->username;
        }
        return parent::beforeValidate();
    }
}
