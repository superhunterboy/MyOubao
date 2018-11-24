<?php
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;


class AdminUser extends BaseModel implements UserInterface, RemindableInterface {
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'admin_users';
    public static $titleColumn = 'username';

    /**
     * 软删除
     * @var boolean
     */
    protected $softDelete = false;
    protected $fillable = ['id', 'username', 'name', 'email', 'password', 'language', 'menu_link', 'menu_context', 'actived', 'secure_card_number', 'password_confirmation'];
    // protected $hidden = ['password'];
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'AdminUser';
        /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'username',
        'name',
        'email',
        // 'password',
        'language',
        // 'menu_link',
        // 'menu_context',
        'actived',
        // 'secure_card_number',
    ];
    public static $ignoreColumnsInView = ['password','remember_token'];
    public static $ignoreColumnsInEdit = ['password'];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'username';

    /**
     * 下拉列表框字段配置
     * @var array
     */

    public static $htmlSelectColumns = [
        'language' => 'aLanguages',
    ];


    public $autoPurgeRedundantAttributes = true;
    public $autoHashPasswordAttributes = true;
    public static $passwordAttributes = ['password'];

    public static $rules = [
        'username'              => 'required|alpha_num|custom_first_character|between:4,32|unique:admin_users,username,', // |unique:admin_users,username
        'name'                  => 'between:0,50',
        'email'                 => 'email|between:0, 200',
        // 'password'              => 'required|between:6,16|confirmed',
        // 'password_confirmation' => 'required|between:6,16',
        'language'              => 'between:0, 10',
        'menu_link'             => 'in:0, 1',
        'menu_context'          => 'in:0, 1',
        'actived'               => 'in:0, 1',
        'secure_card_number'    => 'between:0, 10'
    ];

    // 单独提取出密码的验证规则, 以便在hash之前完成验证并将password字段替换为username . password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度8-16, 不能连续3位字符相同, 不能和资金密码字段相同
    public static $passwordRules = [
        'password'              => 'required|custom_admin_password|confirmed',
        'password_confirmation' => 'required',
    ];

    public $orderColumns = [
        'username' => 'asc'
    ];

    public function roles()
    {
        return $this->belongsToMany('Role', 'admin_role_users', 'user_id', 'role_id')->withTimestamps();
    }
    public function msg_messages()
    {
        return $this->belongsToMany('MsgMessage', 'msg_user', 'sender_id', 'msg_id')->withTimestamps();
    }

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

    }

    public function beforeValidate()
    {
        $this->secure_card_number or $this->secure_card_number = null;
        $this->username = strtolower($this->username);
        if ($this->id) {
            self::$rules['username'] = 'required|between:4,32|unique:admin_users,username,' . $this->id;
        }
        // pr(self::$rules);exit;
        return parent::beforeValidate();
    }
    /**
     * [getRoleIds get admin's role ids]
     * @return [Array] [admin's role ids]
     */
    public function getRoleIds()
    {
        if (!$aRoles = AdminRoleUser::where('user_id', '=', $this->id)->get())
        {
            return false;
        }
        $aRoleId = array();
        foreach($aRoles as $oRole){
            $aRoleId[] = $oRole->role_id;
        }
        return $aRoleId;
    }
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }
    /**
     * 访问器：友好的最后登录时间
     * @return string
     */
    public function getFriendlySigninAtAttribute()
    {
        if (is_null($this->signin_at))
            return __('Not login before');
        else
            return friendly_date($this->signin_at);
    }

    // public static function getAllUserNameArray()
    // {
    //     $aAdminUsers = AdminUser::all(['id', 'username']);
    //     $data = [];
    //     foreach ($aAdminUsers as $key => $value) {
    //         $data[$value->id] = $value->username;
    //     }
    //     return $data;
    // }

    public static function getUsersByIds($aUserIds, $aColumns = null)
    {
        !$aColumns or $aColumns = ['id', 'username'];
        $aUsers = self::whereIn('id', $aUserIds)->get($aColumns);
        return $aUsers;
    }
    /**
     * [resetPassword 重置管理员密码]
     * @param  [Array] $aFormData [表单数据]
     * @return [Integer]            [0:失败, 1:成功]
     */
    // public function resetPassword($aFormData)
    // {
    //     $this->password              = $aFormData['password'];
    //     $this->password_confirmation = $aFormData['password_confirmation'];

    //     $sPwd  = $this->generatePasswordStr();
    //     if (! $sPwd) return 0;
    //     $this->password = $sPwd;
    //     // $aRules = User::$rules;
    //     // $aRules['username'] = str_replace('{:id}', $this->id, $aRules['username'] );
    //     $bSucc = $this->save();
    //     return (int)$bSucc;
    // }
    public function resetPassword($aFormData) {
        $this->password              = $aFormData['password'];
        $this->password_confirmation = $aFormData['password_confirmation'];
        // pr($aFormData);exit;
        $aReturnMsg = $this->generatePasswordStr();
        // pr($aReturnMsg);exit;
        if ($aReturnMsg['success']) {
            $this->password = $aReturnMsg['msg'];
            if ($bSucc = $this->save()) {
                $aReturnMsg['msg'] = __('_user.password-updated');
            }
        }
        return $aReturnMsg;
    }
    /**
     * [generatePasswordStr 生成3次md5后的密码字符串]
     * @return [Array]    ['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]
     */
    public function generatePasswordStr()
    {
        $aPwdRules = static::$passwordRules;
        $sPwdName = 'password';

        $customAttributes = [
            "password"                   => __('_user.password'),
            "password_confirmation"      => __('_user.password_confirmation'),
            "fund_password"              => __('_user.fund_password'),
            "fund_password_confirmation" => __('_user.fund_password_confirmation'),
            "username"                   => __('_user.username'),
        ];
        $oValidator = Validator::make($this->toArray(), $aPwdRules);
        $oValidator->setAttributeNames($customAttributes);

        if (! $oValidator->passes()) {
            $aErrMsg = [];
            foreach ($oValidator->errors()->toArray() as $sColumn => $sMsg) {
                $aErrMsg[] = implode(', ', $sMsg);
            }
            $sError = implode(' ', $aErrMsg);
            return ['success' => false, 'msg' => $sError];
        }
        // pr($this->username);
        // pr($this->{$sPwdName});
        // exit;
        // return md5(md5(md5(strtolower($this->username) . $this->{$sPwdName})));
        $sPwd = strtolower($this->username) . $this->{$sPwdName};
        $sPwd = md5(md5(md5($sPwd)));
        // pr($sPwd);exit;
        return ['success' => true, 'msg' => $sPwd];
    }
    /**
     * [generateUserInfo 生成新建用户的信息]
     * @param [Array] $data         [表单参数]
     * @return [Array]              [生成成功/失败提示信息]
     */
    public function generateUserInfo($data) {
        $data['username'] = strtolower($data['username']);
        (isset($data['nickname']) && $data['nickname']) or $data['nickname'] = $data['username']; // TODO 页面没有填写nickname字段，先用username替代nickname
        // 验证成功，添加用户
        $this->fill($data);
        // pr($this->toArray());
        $aReturnMsg = ['success' => true, 'msg' => __('_user.user-info-generated')]; // ['success' => true, 'msg' => __('_user.user-info-generated')];
        if ($this->password) {
            $aReturnMsg = $this->generatePasswordStr();
            if ($aReturnMsg['success']) {
                $this->password = $aReturnMsg['msg'];
                $aReturnMsg['msg'] = __('_user.password-generated');
            }
            unset($this->password_confirmation);
        } else {
            return ['success' => false, 'msg' => __('_user.no-password')];
        }
        // pr($this->toArray());exit;

        return $aReturnMsg;
    }
}