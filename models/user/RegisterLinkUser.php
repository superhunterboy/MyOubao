<?php
/**
 * RegisterLink所开的用户模型
 */
class RegisterLinkUser extends BaseModel {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'register_link_users';

    public static $resourceName = 'RegisterLinkUser';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];
    protected $guarded = [];
    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;

    // public static $titleColumn = 'name';
    protected $fillable = [
        'id',
        'register_link_id',
        'user_id',
        'username',
        'url',
    ];
    public static $columnForList = [
        // 'url',
        'username',
        'created_at',
        // 'updated_at',
    ];
    public $orderColumns = [
        'updated_at' => 'asc'
    ];
    public static $ignoreColumnsInView = ['id', 'register_link_id','user_id', 'updated_at'];

    public static $rules = [
        'user_id'             => 'required|integer',
        'register_link_id' => 'required|integer',
    ];

    public static function checkRegisterLinkUserRelation ($register_link_id, $user_id)
    {
        if (!$register_link_id || !$user_id) return false;
        return self::where('register_link_id', '=', $register_link_id)->where('user_id', '=', $user_id)->exists();
    }
}