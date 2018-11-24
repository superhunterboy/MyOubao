<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends BaseModel implements UserInterface, RemindableInterface {

    protected static $cacheLevel = self::CACHE_LEVEL_FIRST;
    protected $table = 'users';
    protected $softDelete = true;
    protected $fillable = [
        'account_id',
        'username',
        'nickname',
        'phone',
        'qq',
        'email',
        'parent_id',
        'parent',
        'forefather_ids',
        'forefathers',
        'prize_group',
        'blocked',
        'activated_at',
        'signin_at',
        'register_at',
        'is_agent',
        'is_tester',
        'fund_password',
        'fund_password_confirmation',
        'password',
        'password_confirmation',
        'register_ip',
        'login_ip',
    ];
    // protected $hidden = ['password', 'fund_password'];
    /**
     * 资源名称
     * @var string
     */
    public static $resourceName = 'User';

    /**
     * If Tree Model
     * @var Bool
     */
    public static $treeable = true;
    public static $foreFatherIDColumn = 'forefather_ids';

    /**
     * forefather field
     * @var Bool
     */
    public static $foreFatherColumn = 'forefathers';

    /**
     * the columns for list page
     * @var array
     */
    public static $columnForList = [
        'parent',
        'username',
        'nickname',
        // 'user_type_formatted',
        'account_available',
        // 'group_account_sum',
//        'email',
        'prize_group',
        'blocked',
        'activated_at',
        'signin_at',
        'created_at',
        'is_agent',
        'is_tester'
    ];
    public static $noOrderByColumns = [
        'account_available'
    ];
    public static $listColumnMaps = [
        // 'account_available' => 'account_available_formatted',
        'is_agent' => 'user_type_formatted',
        'signin_at' => 'friendly_signin_at',
        // 'created_at'   => 'friendly_created_at',
        'activated_at' => 'friendly_activated_at',
        'blocked' => 'friendly_block_type',
        'is_tester' => 'friendly_is_tester',
    ];
    public static $ignoreColumnsInView = ['id', 'role_ids', 'password', 'fund_password', 'remember_token', 'email'];
    public static $ignoreColumnsInEdit = ['password', 'fund_password', 'blocked'];

    /**
     * the main param for index page
     * @var string
     */
    public static $mainParamColumn = 'parent_id';
    public static $titleColumn = 'username';

    /**
     * 下拉列表框字段配置
     * @var array
     */
    public static $htmlSelectColumns = [
//        'parent_id' => 'aParentIds',
        'blocked' => 'aBlockedTypes',
    ];
    public static $aUserTypes = [
        self::TYPE_USER => 'User',
        self::TYPE_AGENT => 'Agent',
        self::TYPE_TOP_AGENT => 'top-agent',
    ];
    public $autoPurgeRedundantAttributes = true;
    public $autoHashPasswordAttributes = true;
    public static $passwordAttributes = ['password', 'fund_password'];
    public static $rules = [
        'username' => 'required|regex:/^[a-zA-Z0-9]{6,16}$/|unique:users,username,',
        'nickname' => 'between:2,16',
        'phone' => 'regex:/^1\d{10}$/',
        'qq' => 'between:0,9999999999',
        'email' => 'email|between:0, 50', // |unique:users,email,
        // 'password'                   => 'required|regex:/^(?![^a-zA-Z]+$)(?!\D+$).{6,16}$/|confirmed|different_before_hash:fund_password',
        // 'password_confirmation'      => 'required|regex:/^(?![^a-zA-Z]+$)(?!\D+$).{6,16}$/',
//         'fund_password'              => 'required|regex:/^(?![^a-zA-Z]+$)(?!\D+$).{6,16}$/|confirmed|different_before_hash:password', // 资金账户密码
//         'fund_password_confirmation' => 'required|regex:/^(?![^a-zA-Z]+$)(?!\D+$).{6,16}$/',
        'parent_id' => 'integer',
        // 'parent'                     => 'required',
        // 'parent_str'                 => 'required',
        'account_id' => 'integer',
        'blocked' => 'in:0,1,2,3',
        'forefathers' => 'between:0,1024',
        'forefather_ids' => 'between:0,100',
        'is_agent' => 'in:0, 1',
        'is_tester' => 'in:0, 1',
        'activated_at' => 'date',
        'signin_at' => 'date',
        'register_at' => 'date',
        'register_ip' => 'between:0,15',
        'login_ip' => 'between:0,15',
        'prize_group' => 'required',
    ];
    // 单独提取出密码的验证规则, 以便在hash之前完成验证并将password字段替换为username . password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度6-16, 不能连续3位字符相同, 不能和资金密码字段相同
    public static $passwordRules = [
        'password' => 'required|custom_password|confirmed|different:username',
        'password_confirmation' => 'required',
    ];
    // 单独提取出资金密码的验证规则, 以便在hash之前完成验证并将fund_password字段替换为username . fund_password三次md5后的字符串
    // 正则表达式: 大小写字母+数字, 长度6-16, 不能连续3位字符相同, 不能和密码字段相同
    public static $fundPasswordRules = [
        'fund_password' => 'required|custom_fund_password|confirmed|different:username',
        'fund_password_confirmation' => 'required',
    ];
    // 按钮指向的链接，查询列名和实际参数来源的列名的映射
    // public static $aButtonParamMap = ['prize_group' => 'prize_group'];


    public $orderColumns = [
        'username' => 'asc'
    ];

    const TYPE_TOP_AGENT = 2;
    const TYPE_AGENT = 1;
    const TYPE_USER = 0;
    const UNBLOCK = 0;
    const BLOCK_LOGIN = 1;
    const BLOCK_BUY = 2;
    const BLOCK_FUND_OPERATE = 3;

    public static $blockedTypes = [
        self::UNBLOCK => 'unblock',
        self::BLOCK_LOGIN => 'block-login',
        self::BLOCK_BUY => 'block-bet',
        self::BLOCK_FUND_OPERATE => 'block-fund',
    ];
    public static $betMaxPrize10Ids = [
        14 => 14,
        13 => 13
    ];

    /**
     * 昵称屏蔽过滤
     * @return sring
     */
    protected function getDisplayNicknameAttribute() {
        $sNickName = $this->nickname;
        if (Session::get('user_id') == $this->id) {
            $sDisplayNickName = $sNickName;
        } else {
            $sParttern = '/(\d{3})(\d+)/';
            $sDisplayNickName = preg_replace($sParttern, '\1***', $sNickName);
        }

        if (mb_strlen($sDisplayNickName) > 8) {
            $sDisplayNickName = mb_substr($sDisplayNickName, 0, 8, 'utf-8') . '***';
        }
        return $sDisplayNickName;
    }

    public function roles() {
        return $this->belongsToMany('Role', 'role_users', 'user_id', 'role_id')->withTimestamps();
    }

    public function parents() {
        return $this->belongsTo('User', 'parent_id');
    }

    public function children() {
        return $this->hasMany('User', 'parent_id');
    }

    public function msg_messages() {
        return $this->belongsToMany('MsgMessage', 'msg_user', 'receiver_id', 'msg_id')->withTimestamps();
    }

    /**
     * 获得用户所有的奖金组
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function user_prize_groups() {
        return $this->hasMany('UserPrizeSet', 'user_id');
    }

    /**
     * 账户信息关系
     *
     * @return mixed
     */
    public function account() {
        return $this->hasOne('Account', 'user_id', 'id');
    }

    // public function user_bank_cards()
    // {
    //     return $this->hasMany('UserBankCard', '');
    // }

    public function create_user_links() {
        return $this->belongsToMany('RegisterLink', 'register_link_users', 'user_id', 'register_link_id')->withTimestamps();
    }

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken() {
        return $this->remember_token;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value) {
        $this->remember_token = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName() {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail() {
        return $this->email;
    }

    /**
     * 判断该账户是否激活
     *
     * @return bool 是否激活
     */
    public function isActivated() {
        return $this->email && $this->activated_at;
    }

    /**
     * 发送激活邮件
     *
     * @return mixed
     */
    public function sendActivateMail() {
        //给用户发送一封激活邮件
        $code = mt_rand(1000000, 9999999);

        Cache::section('bindEmail')->put($this->id, $code, 1440);

        $user = $this;

        return Mail::send('emails.auth.activation', [
                    'code' => $code,
                    'user' => $this,
                        ], function($message) use ($user) {
                    $message->to($user->email, $user->username)->subject('绑定邮箱确认');
                });
    }

    /**
     * 获得用户的最大奖金组
     *
     * @return mixed
     */
    public function getMaxGroupPrize() {
        return UserPrizeSet::getMaxGroup($this->id);
    }

    /**
     * 访问器：友好的最后登录时间
     * @return string
     */
    protected function getFriendlySigninAtAttribute() {
        if (is_null($this->signin_at))
            return __('_user.not-before'); // '新账号尚未登录'
        else
            return friendly_date($this->signin_at);
    }

    protected function getFriendlyCreatedAtAttribute() {
        // return friendly_date($this->created_at);
        return $this->created_at->toDateTimeString();
    }

    protected function getFriendlyBlockTypeAttribute() {
        return __('_user.' . self::$blockedTypes[$this->blocked]);
    }

    protected function getFriendlyIsTesterAttribute() {
        return yes_no(intval($this->is_tester));
    }

    protected function getUserTypeFormattedAttribute() {
        if ($this->parent_id)
            $sUserType = static::$aUserTypes[$this->is_agent];
        else
            $sUserType = static::$aUserTypes[self::TYPE_TOP_AGENT];
        return __('_user.' . $sUserType);
    }

    protected function getSigninAtFormattedAttribute() {
        return $this->signin_at ? $this->signin_at : __('_user.not-signin-yet');
    }

    /**
     * [generatePasswordStr 生成3次md5后的密码字符串]
     * @param  [Integer] $iPwdType [密码字段类型]
     * @return [Array]    ['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]
     */
    public function generatePasswordStr($iPwdType = 1) {
        if ($iPwdType == 2) {
            $aPwdRules = static::$fundPasswordRules;
            $sPwdName = 'fund_password';
        } else {
            $aPwdRules = static::$passwordRules;
            $sPwdName = 'password';
        }

        $customAttributes = [
            "password" => __('_user.login-password'),
            "password_confirmation" => __('_user.password_confirmation'),
            "fund_password" => __('_user.fund_password'),
            "fund_password_confirmation" => __('_user.fund_password_confirmation'),
            "username" => __('_user.login-username'),
        ];

        $oValidator = Validator::make($this->toArray(), $aPwdRules);

        $oValidator->setAttributeNames($customAttributes);

        if (!$oValidator->passes()) {

            // $aErrMsg = [];
            foreach ($oValidator->errors()->toArray() as $sColumn => $sMsg) {
                // $aErrMsg[] = implode(',', $sMsg);
                // TIP 只取第一个验证错误信息
                $sError = $sMsg[0];
                break;
            }

            return ['success' => false, 'msg' => $sError];
        }

        $sPwd = strtolower($this->username) . $this->{$sPwdName};
        $sPwd = md5(md5(md5($sPwd)));

        return ['success' => true, 'msg' => $sPwd];
    }

    /**
     * [resetPassword 重置密码]
     * @param  [Array] $aFormData [数据数组]
     * @return [Array]    [['success' => true/false:验证成功/失败, 'msg' => 返回消息, 成功: 加密后的密码字符串, 失败: 错误信息]]
     */
    public function resetPassword($aFormData) {
        $this->password = $aFormData['password'];
        $this->password_confirmation = $aFormData['password_confirmation'];

        $aReturnMsg = $this->generatePasswordStr(1);
        if ($aReturnMsg['success']) {
            $this->password = $aReturnMsg['msg'];
            if ($bSucc = $this->save()) {
                $aReturnMsg['msg'] = __('_user.password-updated');
            }
        }
        return $aReturnMsg;
    }

    /**
     * [resetFundPassword 重置资金密码]
     * @param  [Array] $aFormData [数据数组]
     * @return [type]           [description]
     */
    public function resetFundPassword($aFormData) {
        $this->fund_password = $aFormData['fund_password'];
        $this->fund_password_confirmation = $aFormData['fund_password_confirmation'];

        $aReturnMsg = $this->generatePasswordStr(2);
        if ($aReturnMsg['success']) {
            $this->fund_password = $aReturnMsg['msg'];
            if ($bSucc = $this->save()) {
                $aReturnMsg['msg'] = __('_user.fund-password-updated');
            }
        }
        return $aReturnMsg;
    }

    /**
     * [checkPassword 检查密码]
     * @param  [String] $sPassword [密码字符串]
     * @return [Boolean]           [验证成功/失败]
     */
    public function checkPassword($sPassword) {
        $sPwd = strtolower($this->username) . $sPassword;
        $sUserPassword = md5(md5(md5($sPwd)));
        // pr($sUserPassword);exit;
        return Hash::check($sUserPassword, $this->password);
    }

    /**
     * [checkFundPassword 检查资金密码]
     * @param  [String] $sFundPassword [资金密码字符串]
     * @return [Boolean]               [验证成功/失败]
     */
    public function checkFundPassword($sFundPassword) {
        $sPwd = strtolower($this->username) . $sFundPassword;
        $sUserFundPassword = md5(md5(md5($sPwd)));
        // pr($sUserFundPassword);exit;
        return Hash::check($sUserFundPassword, $this->fund_password);
    }
    
    public function checkPasswordFront($sPassword) {
        $sPwd = strtolower($this->username) . $sPassword;
        $sUserPassword = md5(md5(md5($sPwd)));
        $oUser = self::where('username', '=', Session::get('username'))->first(['password']);
        return Hash::check($sUserPassword, $oUser->password);
    }

    public function checkFundPasswordFront($sFundPassword) {
        $sPwd = strtolower($this->username) . $sFundPassword;
        $sUserFundPassword = md5(md5(md5($sPwd)));
        $oUser = self::where('username', '=', Session::get('username'))->first(['fund_password']);
        return Hash::check($sUserFundPassword, $oUser->fund_password);
    }
    
    /**
     * [checkUsernameExist 判断用户名是否存在]
     * @param  [String] $sUsername [用户名]
     * @return [Boolean]           [true:存在, false:不存在]
     */
    public static function checkUsernameExist($sUsername) {
        return User::where('username', '=', $sUsername)->exists();
    }

    public static function checkNickrnameExist($nickname, $notId = null) {

        if (!is_null($notId)) {
            $notId = intval($notId);
            return User::where('nickname', '=', $nickname)->where('id', '!=', $notId)->exists();
        } else
            return User::where('nickname', '=', $nickname)->exists();
    }

    /**
     * [checkEmailExist 判断邮箱是否已经被绑定]
     * @param  [String] $sEmail [邮箱名]
     * @return [Boolean]           [true:存在, false:不存在]
     */
    public static function checkEmailExist($sEmail) {
        return User::where('email', '=', $sEmail)->whereNotNull('activated_at')->exists();
    }

    public static function getAllUserNameArrayByUserType($iUserType = self::TYPE_USER, $iAgentLevel = null) {
        $data = [];
        $aColumns = ['id', 'username'];
        if ($iUserType == 'all') {
            $aUsers = User::all($aColumns);
        } else {
            $oQuery = User::where('is_agent', '=', $iUserType);

            switch ($iAgentLevel) {
                case 1:
                    $oQuery = $oQuery->whereNull('parent_id');
                    break;
                case 2:
                    $oQuery = $oQuery->whereNotNull('parent_id');
                    break;
            }
            $aUsers = $oQuery->get($aColumns);
        }

        foreach ($aUsers as $key => $value) {
            $data[$value->id] = $value->username;
        }
        return $data;
    }

    /**
     * [getRoleIds 获取用户的角色id]
     * @return [Array] [用户的角色id数组]
     */
    public function getRoleIds() {
        if (!$aRoles = RoleUser::where('user_id', '=', $this->id)->get())
            return false;
        $aRoleId = [];
        foreach ($aRoles as $oRole) {
            $aRoleId[] = $oRole->role_id;
        }
        // $aRoleId = explode(',', $this->role_ids);
        return $aRoleId;
    }

    /**
     * [getUserRoleNames 获取用户组 ]
     * @return [String]          [用户组]
     */
    public function getUserRoleNames() {
        // $aRoles = User::find($iUserId)->roles()->get();
        $aRoles = $this->roles()->get();
        $aRoleNames = [];
        foreach ($aRoles as $oRole) {
            if (in_array($oRole->role_type, [Role::ADMIN_ROLE, Role::USER_ROLE])) {
                $aRoleNames[] = $oRole->name;
            }
        }
        return implode(',', $aRoleNames);
    }

    /**
     * [getAgentDirectChildrenNum 获取代理的直属用户数量]
     * @return [Int]          [直属用户数量]
     */
    public function getAgentDirectChildrenNum() {
        // $oUser = User::find($iUserId);
        if (!$this->is_agent)
            return 0;
        $iNum = $this->children()->count();
        return $iNum;
    }

    /**
     * [getGroupAccountSum 获取代理的团队余额]
     * @param  [Boolean] [返回值类型, true: 团队余额, flase: 包含团队余额的代理用户信息]
     * @return [Float/Object]          [true: 玩家或代理团队账户余额, flase: 包含团队余额的玩家或代理信息]
     */
    public function getGroupAccountSum() {
        // 通过forefather_ids 免递归查团队余额

        $sql = "select sum(accounts.available) as team_available from accounts "
                . " left join users on users.id = accounts.user_id "
                . " where users.id=" . $this->id . " OR find_in_set(" . $this->id . ",forefather_ids)";
        $r = DB::select($sql);

        $amount = $r[0]->team_available;

        return $amount;
    }

    /**
     * [getAllUsersBelongsToAgent 查询属于某代理的所有下级的id ]
     * @param  [Integer] $iAgentId [代理id]
     * @return [Array]           [id数组]
     */
    public static function getAllUsersBelongsToAgent($iAgentId) {
        $aColumns = ['id', 'username', 'is_agent'];
        $aUsers = User::whereRaw(' find_in_set(?, forefather_ids)', [$iAgentId])->where('deleted_at', '=', null)->get($aColumns);
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        $aUserIds = [];
        foreach ($aUsers as $oUser) {
            $aUserIds[] = $oUser->id;
        }
        return $aUserIds;
    }

    /**
     * [getAllUsersBelongsToAgentByUsername 按用户名称查询属于某代理的所有下级的id ]
     * @param  [Integer] $iAgentId [代理id]
     * @return [Array]           [id数组]
     */
    public static function getAllUsersBelongsToAgentByUsername($sAgentName, $bIncludeSelf = TRUE) {
        $aColumns = ['id', 'username', 'is_agent'];
        $oQuery = User::whereRaw(' find_in_set(?, forefathers)', [$sAgentName]);
        if ($bIncludeSelf) {
            $aUsers = $oQuery->orwhereRaw('username=?', [$sAgentName])->get($aColumns);
        } else {
            $aUsers = $oQuery->get($aColumns);
        }
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        $aUserIds = [];
        foreach ($aUsers as $oUser) {
            $aUserIds[] = $oUser->id;
        }
        return $aUserIds;
    }

    /**
     * [getUsersByIds 根据用户id数组获取用户信息]
     * @param  [Array] $aUserIds [用户id数组]
     * @param  [Array] $aColumns [要返回的列]
     * @return [Array]           [用户信息数组]
     */
    public static function getUsersByIds($aUserIds, $aColumns = null) {
        if (!$aUserIds) {
            return [];
        }
        is_array($aUserIds) or $aUserIds = explode(',', $aUserIds);
        $aColumns or $aColumns = ['id', 'username'];
        $aUsers = self::whereIn('id', $aUserIds)->get($aColumns);
        return $aUsers;
    }

    /**
     * [getUsersByUsernames 根据用户名数组获取用户信息]
     * @param  [array]   $aUsernames [用户名数组]
     * @param  [boolean] $bNeedCount [是否返回数据总数]
     * @param  [Array]  $aColumns   [要返回的列]
     * @return [type]              [用户信息数组]
     */
    public static function getUsersByUsernames(array $aUsernames, $bNeedCount = false, $aColumns = null) {
        $aColumns or $aColumns = ['id', 'username', 'is_agent', 'forefather_ids'];
        // pr($aColumns);exit;
        $oQuery = self::whereIn('username', $aUsernames);
        if ($bNeedCount) {
            $result = $oQuery->count('id');
        } else {
            $result = $oQuery->orderBy('created_at')->get($aColumns);
        }
        // if (!$bNeedCount) {
        //     $result = [];
        //     foreach ($aUsers as $oUser) {
        //         $result[$oUser->id] = $oUser->username;
        //     }
        // }

        return $result;
    }

    public static function getRegisterCount($sDate, $bOnlyTop = false) {
        $sSql = "select count(distinct id) count from users where register_at between '$sDate' and '$sDate 23:59:59' and is_tester = 0";
        !$bOnlyTop or $sSql .= " and parent_id is null";
        $aResults = DB::select($sSql);
        return $aResults[0]->count ? $aResults[0]->count : 0;
    }

    /**
     * [getUsersBelongsToAgent 获取代理的所有直接下级用户]
     * @return [Object]           [代理的所有直接下级用户]
     */
    public function getUsersBelongsToAgent($aColumns = null) {
        $aColumns or $aColumns = ['id', 'username', 'is_agent'];
        // pr($iAgentId);
        $aUsers = $this->children()->get($aColumns);
        // pr($aUsers->toArray());exit;
        return $aUsers;
    }

    protected function beforeValidate() {
        // TIP 如果有父用户，则子用户的is_tester属性应该和父用户保持一致
        if ($this->parent_id) {
            $oParent = self::find($this->parent_id);
            $this->is_tester = $oParent->is_tester;
        }
        $this->parent_str = $this->forefather_ids;
        $this->signin_at or $this->signin_at = null;
        // $this->password              = md5(md5(md5($this->username . $this->password)));
        // $this->password_confirmation = md5(md5(md5($this->username . $this->password_confirmation)));
        // $this->account_id != 0 or $this->account_id = null;
        // TODO 激活时间, 应该是邮件激活的时间
        // $this->activated_at or $this->activated_at = Carbon::now()->toDateTimeString();
        // $this->login_ip = get_client_ip();
        // pr($this->toArray());exit;
        if ($this->id) {
            // self::$rules['username'] .= $this->id; // str_replace('{:id}', $this->id, self::$rules['username'] );
            self::$rules['username'] = 'required|alpha_num|between:6,16|unique:users,username,' . $this->id;
            // self::$rules['email']    = 'email|between:0, 50|unique:users,email,' . $this->id;
        }
        // pr($this->toArray());
        // pr(User::$rules);
        // exit;
        return parent::beforeValidate();
    }

    /**
     * 取得玩法设置数组，供渲染投注页面或奖金页面使用
     * @param int $iUserId
     * @param Lottery $oLottery
     * @param bool $bForBet
     * @return array &
     */
    public static function & getWaySettings($iUserId, $oLottery, $bForBet = false) {
        if ($oLottery->series_id == 19) {
            $uPrizeGroupName = Session::get('user_prize_group') > 1950 ? 1950 : Session::get('user_prize_group');
            $oPrizeGroup = PrizeGroup::getPrizeGroupByClassicPrizeAndSeries($uPrizeGroupName, $oLottery->series_id);
            $iGroupId = $oPrizeGroup->id;
        } else {
            $iGroupId = UserPrizeSet::getGroupId($iUserId, $oLottery->id, $sGroupName);
        }
        if (empty($iGroupId)) {
            $a = [];
            return $a;
        }
        // pr($iGroupId);exit;
        // $iGroupId = 512;
        $aPrizes = & PrizeGroup::getPrizeDetails($iGroupId);

        $fMaxPrize = $bForBet ? self::getPrizeLimit($iUserId, $oLottery->id) : null;
        return WayGroup::getWayInfos($oLottery, $aPrizes, $fMaxPrize);
    }

    /**
     * 取得奖金限额
     *
     * @param int $iUserId
     * @return int
     */
    public static function getPrizeLimit($iUserId, $iLotteryId = null) {
        if ($iLotteryId && in_array($iLotteryId, self::$betMaxPrize10Ids)) {
            return self::getLowPrizeLimit($iUserId);
        }
        return SysConfig::readValue('bet_max_prize');
    }

    /**
     * 取得低频彩种奖金限额
     *
     * @param int $iUserId
     * @return int
     */
    public static function getLowPrizeLimit($iUserId) {
        return SysConfig::readValue('bet_max_prize_10');
    }

    /**
     * [checkUserBelongsToAgent 检查用户是否属于当前登录的代理]
     * @param  [Integer] $iUserId [用户ID]
     * @return [Boolean]          [true/false: 属于/不属于]
     */
    public function checkUserBelongsToAgent($iUserId) {
        // $iUserId or $iUserId = Session::get('user_id');
        if (Session::get('is_agent')) {
            // $oUser = User::find($iUserId);
            $aUsers = $this->getUsersBelongsToAgent();
            $aUserIds = [];
            foreach ($aUsers as $oUser) {
                $aUserIds[] = $oUser->id;
            }
            return in_array($iUserId, $aUserIds);
        }
        return false;
    }

    /**
     * [getTopAgentPrizeGroupDistribution 按奖金组分组查询总代用户]
     * @return [Collection] [用户集合]
     */
    public static function getTopAgentPrizeGroupDistribution() {
        $aColumns = ['prize_group', 'num'];
        $oQuery = self::selectRaw(' *, count(distinct id) as num ')->whereNull('parent_id')->orWhere('parent_id', '=', '')->groupBy('prize_group')->orderBy('prize_group', 'desc');
        return $oQuery->get($aColumns);
    }

    /**
     * [getUserLevelAttribute 获取用户级别]
     * @return [Integer] [用户级别]
     */
    protected function getUserLevelAttribute() {
        if (is_null($this->parent_id)) {
            return 0;
        }
        return count(explode(',', $this->forefather_ids));
    }

    /**
     * [getExpirenceAgent 获取体验账户的虚拟总代]
     * @return [Object] [虚拟总代对象]
     */
    public static function getExpirenceAgent() {
        $aColumns = ['id', 'username', 'is_agent', 'is_tester', 'prize_group'];
        return self::find(Config::get('vagent.user_id'), $aColumns);
    }

    /**
     * [getUserByParams 根据参数查询用户对象]
     * @param  [Array] $aParams [参数数组]
     * @param  [Array] $aInSetKeys [需要使用find_in_set函数的查询条件的key值数组]
     * @return [Object]          [用户对象]
     */
    public static function getUserByParams(array $aParams = ['*'], $aInSetKeys) {
        $oQuery = self::where('id', '>', 0);
        foreach ($aParams as $key => $value) {
            if (in_array($key, $aInSetKeys)) {
                $oQuery = $oQuery->whereRaw(' find_in_set(?, ' . $key . ')', [$value]);
            } else {
                $oQuery = $oQuery->where($key, '=', $value);
            }
        }
        return $oQuery->get()->first();
    }

    /**
     * [generateAccountInfo 根据用户对象创建账户对象]
     * @return [Object]        [账户对象]
     */
    public function generateAccountInfo() {
        $oAccount = new Account;
        $oAccount->user_id = $this->id;
        $oAccount->username = $this->username;
        $oAccount->withdrawable = 0;
        $oAccount->status = 1;
        return $oAccount;
    }

    /**
     * [generateUserInfo 生成新建用户的信息]
     * @param [String] $sPrizeGroup [如果是代理, 则prize_group为其奖金组, 玩家有多种奖金组, 所以置空值]
     * @param [Array] $data         [表单参数]
     * @return [Array]              [生成成功/失败提示信息]
     */
    public function generateUserInfo($sPrizeGroup, $data) {
        $data['username'] = strtolower($data['username']);

        (isset($data['nickname']) && $data['nickname']) or $data['nickname'] = $data['username']; // TODO 页面没有填写nickname字段，先用username替代nickname
        (isset($data['fund_password']) && $data['fund_password']) or $data['fund_password'] = '';

        // TIP 此处的prize_group实际是prize_groups表的classic_prize字段
        if ($sPrizeGroup) {
            $data['prize_group'] = $sPrizeGroup;
        }

        $data['register_ip'] = get_client_ip();

        // 验证成功，添加用户
        $this->fill($data);

        // TODO 这两个字段不能为空, parent_str可能已经被弃用, 后续可以考虑写到User模型的beforeValidate里
        $this->parent_str = $this->forefather_ids;

        $aReturnMsg = ['success' => true, 'msg' => __('_user.user-info-generated')];

        if ($this->password) {
            $aReturnMsg = $this->generatePasswordStr(1);

            if ($aReturnMsg['success']) {
                $this->password = $aReturnMsg['msg'];
                $aReturnMsg['msg'] = __('_user.password-generated');
            }

            unset($this->password_confirmation);

            $this->addAgentStat();
        } else {
            return [
                'success' => FALSE,
                'msg' => __('_user.no-password')
            ];
        }

        return $aReturnMsg;
    }

    /**
     * [generateLotteryPrizeGroup 创建所有彩种奖金组]
     * @param  [Array] $aPrizeGroup [链接开户的奖金组配置]
     * @return [Array]              [彩种奖金组]
     */
    public function generateLotteryPrizeGroup($aPrizeGroup) {
        $data = [];
        $oSeries = new Series;
        $aSeriesLinkTo = $oSeries->getValueListArray('link_to', [], [], true);
        if (isset($aPrizeGroup[0]->lottery_id)) {
            // 玩家开户
            $aLotteryPrizeGroups = [];
            $aLotteriesGroupBySeries = Lottery::getAllLotteries();
            // pr($aLotteriesGroupBySeries);exit;
            foreach ($aLotteriesGroupBySeries as $key => $value) {
                // 考虑彩系link_to属性
                if (isset($aSeriesLinkTo[$value['series_id']])) {
                    $value['series_id'] = $aSeriesLinkTo[$value['series_id']];
                }
                $data[$value['id']] = $value;
            }
            // pr($data);exit;
            foreach ($aPrizeGroup as $key => $value) {
                if (array_key_exists($value->lottery_id, $data)) {
                    $aLotteryPrizeGroups[] = [
                        'series_id' => $data[$value->lottery_id]['series_id'],
                        'lottery_id' => $value->lottery_id,
                        'classic_prize' => $value->prize_group
                    ];
                }
            }
            // $aLotteryPrizeGroups = $aPrizeGroup;
        } else {
            // 代理开户
            $aSeriesLotteries = & Series::getLotteriesGroupBySeries();
            $aLotteryPrizeGroups = [];
            // pr($aSeriesLotteries);exit;
            foreach ($aSeriesLotteries as $key => $value) {
                $data[$value['id']] = $value['children'];
            }
            // pr($data);exit;
            foreach ($aPrizeGroup as $key => $value) {
                if (isset($data[$value->series_id])) {
                    foreach ($data[$value->series_id] as $key2 => $aLottery) {
                        // pr($key2);
                        // pr($aLottery);
                        // exit;
                        $aLotteryPrizeGroups[] = [
                            'series_id' => $value->series_id,
                            'lottery_id' => $aLottery['id'],
                            'classic_prize' => $value->prize_group
                        ];
                    }
                }
            }
        }
        return $aLotteryPrizeGroups;
    }

    /**
     * [generateUserPrizeGroups 生成用户的所有彩种的奖金组数据]
     * @param  [Array] $aLotteryPrizeGroups [所有彩种的奖金组数据]
     * @return [Array]                      [用户的奖金组数据]
     */
    public function generateUserPrizeGroups($aLotteryPrizeGroups) {
        $aUserPrizeGroups = [];
        $aParams = array_column($aLotteryPrizeGroups, 'classic_prize');
        $aGroups = PrizeGroup::getPrizeGroupsWithOnlyKey($aParams);
        foreach ($aLotteryPrizeGroups as $value) {
            $oUserPrizeSet = new UserPrizeSet;
            $key = $value['series_id'] . '_' . $value['classic_prize'];
            $data = [
                'user_id' => $this->id,
                'user_parent_id' => $this->parent_id,
                'user_parent' => $this->parent ? $this->parent : '',
                'username' => $this->username,
                'is_agent' => $this->is_agent,
                'lottery_id' => $value['lottery_id'],
                'prize_group' => $aGroups[$key]['name'],
                'group_id' => $aGroups[$key]['id'],
                'classic_prize' => $value['classic_prize'],
            ];
            $aUserPrizeGroups[] = $data;
        }
        return $aUserPrizeGroups;
    }

    public static function getAllUserArrayByUserType($iUserType = self::TYPE_USER, $aExtraColumn) {
        $aColumns = ['id', 'username', 'blocked', 'parent_id', 'parent', 'account_id', 'prize_group'];
        $aColumns = array_merge($aColumns, $aExtraColumn);
        if ($iUserType === 'all') {
            $aUsers = User::all($aColumns);
        } else {
            if ($iUserType == self::TYPE_TOP_AGENT) {
                $oQuery = User::where('is_agent', '=', self::TYPE_AGENT)->whereNull('parent_id');
            } else {

                $oQuery = User::where('is_agent', '=', $iUserType);
            }
            $aUsers = $oQuery->get($aColumns);
        }
        return $aUsers;
    }

    /**
     * 获取用户的用户类型
     * @return int 用户类型整数值
     */
    public function getUserType() {
        if ($this->is_agent == self::TYPE_USER) {
            return self::TYPE_USER;
        } else {
            if ($this->parent_id) {
                return self::TYPE_AGENT;
            } else {
                return self::TYPE_TOP_AGENT;
            }
        }
    }

    /**
     * 根据用户名查找
     *
     * @param $username
     * @return \LaravelBook\Ardent\Ardent|\LaravelBook\Ardent\Collection|static
     */
    public static function findUser($username) {
        if (static::$cacheLevel == self::CACHE_LEVEL_NONE) {
            return parent::where('username', '=', $username)->first();
        }
        Cache::setDefaultDriver(static::$cacheDrivers[static::$cacheLevel]);

        $key = self::createCacheKey($username);
        if ($aAttributes = Cache::get($key)) {
            $obj = new static;
            $obj = $obj->newFromBuilder($aAttributes);
        } else {
            $obj = parent::where('username', '=', $username)->first();
            if (!is_object($obj)) {
                return false;
            }
            Cache::forever($key, $obj->getAttributes());
        }

        return $obj;
    }

    /**
     * 保存之后出发的事件
     *
     * @param $oSavedModel
     * @return bool
     */
    protected function afterSave($oSavedModel) {
        $this->deleteCache($this->username);
        return parent::afterSave($oSavedModel);
    }

    /**
     * 添加用户后增加直属代理的开户数
     */
    private function addAgentStat() {
        AgentCreateStat::updateAgentCreateStat($this->parent_id);
    }

    /**
     * 获取代理团队的用户ID
     * @param $fatherId
     * @return array|null
     */
    public static function getTeamAgentUserIds($fatherId) {
        $aColumns = ['id', 'username', 'is_agent'];
        $aUsers = User::whereRaw(' find_in_set(?, forefather_ids)', [$fatherId])->where('is_agent', '=', self::TYPE_AGENT)->get($aColumns)->toArray();

        return $aUsers ? array_column($aUsers, 'id') : null;
    }

    /**
     * 获取玩家团队的用户ID
     * @param $fatherId
     * @return array|null
     */
    public static function getTeamPlayerUserIds($fatherId) {
        $aColumns = ['id', 'username', 'is_agent'];
        $aUsers = User::whereRaw(' find_in_set(?, forefather_ids)', [$fatherId])->where('is_agent', '=', self::TYPE_USER)->get($aColumns)->toArray();

        return $aUsers ? array_column($aUsers, 'id') : null;
    }

    /**
     * [getUserPrizeGroupLimitRange 获取该用户可以创建的下级奖金组]
     * @param  [Integer] $iCurrentPrizeGroup [该用户的奖金组]
     * @return [Array]                     [可用的奖金组数据]
     */
    public function generatePrizeGroupSetData() {
        $iCurUserPrizeGroup = $this->prize_group;

        $iAgentPrizeGroup = PrizeSysConfig::getPrizeGroups(PrizeSysConfig::TYPE_AGENT);
        $iPlayerPrizeGroup = PrizeSysConfig::getPrizeGroups(PrizeSysConfig::TYPE_USER);

        if (in_array($this->username, config::get('useLowPrizeGroupWhiteList.user_list'))) {
            $iAgentMinPrizeGroup = config::get('useLowPrizeGroupWhiteList.prize_group');
        } else {
            $iAgentMinPrizeGroup = $iAgentPrizeGroup[0];
        }
        $aData = [
            'iCurrentUserPrizeGroup' => $iCurUserPrizeGroup,
            'iAgentMinPrizeGroup' => min($iAgentMinPrizeGroup, $iCurUserPrizeGroup),
            'iAgentMaxPrizeGroup' => min($iAgentPrizeGroup[1], $iCurUserPrizeGroup),
            'iPlayerMinPrizeGroup' => min($iPlayerPrizeGroup[0], $iCurUserPrizeGroup),
            'iPlayerMaxPrizeGroup' => min($iPlayerPrizeGroup[1], $iCurUserPrizeGroup)
        ];
        RETURN $aData;
//        return ['iCurrentUserPrizeGroup'=>$iCurUserPrizeGroup, 'iAgentMinPrizeGroup' => $iAgentPrizeGroup[0], 'iAgentMaxPrizeGroup' => min($iAgentPrizeGroup[1], $iCurUserPrizeGroup), 'iPlayerMinPrizeGroup' => $iPlayerPrizeGroup[0], 'iPlayerMaxPrizeGroup' => min($iPlayerPrizeGroup[1], $iCurUserPrizeGroup) ];
    }

    protected function getUserLevelFormattedAttribute() {
        return is_null($this->parent_id) ? 0 : count(explode(',', $this->forefather_ids));
    }

    /**
     * 判断代理用户是否可以有配额指标
     * @return type
     */
    public function hasOverlimitQuotaOrNot() {
        $iUserId = Session::get('user_id');
//        var_dump(OverlimitPrizeGroup::isOverlimit($iUserId));
//        exit;
        //    if(!OverlimitPrizeGroup::getPrizeGroups($iUserId)) return false;
        $aOverLimitPrizeGroup = OverlimitPrizeGroup::getPrizeGroupByTopAgentId($iUserId);
        if (!$aOverLimitPrizeGroup)
            return false;
//        pr($aOverLimitPrizeGroup);
//        exit;
//        echo $this->prize_group;exit;
        foreach ($aOverLimitPrizeGroup as $v) {
            if ((int) $v['prize_group'] <= UserPrizeGroupTmp::getForeverPrize($this)) {
                return true;
                break;
            }
        }
        return false;
    }

    public function getUserCountsBelongsToAgentId() {
        $r = DB::select("select count(*) as total from users where FIND_IN_SET(" . $this->id . ",forefather_ids) and   isnull(users.deleted_at)");
        return $r[0]->total;
    }

    public static function getByFuzzyNickName($sNickName, $aColumns = ['*']) {
        return self::where('nickname', 'like', "{$sNickName}%")->get($aColumns);
    }

    public function isEnableVoucher() {
        return $this->parent == Voucher::VIRTUAL_SPORT_AGENT;
    }

    public function getVoucherAmountAttribute($fVoucherAmount) {
        if (!isset($fVoucherAmount)) {
            $fVoucherAmount = UserVoucher::getVoucherAmount($this->id);
            $this->voucher_amount = $fVoucherAmount;
        }
        return $fVoucherAmount;
    }

    public static function checkIsSafeUser($iUserId) {
        $oUser = self::find($iUserId);
        if (empty($oUser)) {
            return false;
        }
        if ($oUser->is_tester) {
            return true;
        }
        $oDeposit = Deposit::where('user_id', $iUserId)
                ->where('status', Deposit::DEPOSIT_STATUS_SUCCESS)
                ->where('deposit_mode', Deposit::DEPOSIT_MODE_BANK_CARD)
                ->first();
        $oWithdrawal = Withdrawal::where('user_id', $iUserId)
                ->where('username', $oUser->username)
                ->where('status', Withdrawal::WITHDRAWAL_STATUS_SUCCESS)
                ->first();
        $aCardsInfo = UserBankCard::getUserCardsInfo($iUserId, ['id', 'islock']);
        $bLockCard = false;
        foreach ($aCardsInfo as $oBankCard) {
            if ($oBankCard->islock) {
                $bLockCard = true;
                break;
            }
        }
        return $oDeposit || ($oWithdrawal && $bLockCard);
    }

    public static function compileUserSessionKey($username) {
        return self::getCachePrefix(true) . 'serssion-' . $username;
    }

    public static function userLogout($username) {
        $userSessionKey = self::compileUserSessionKey($username);
        $redis = Redis::connection();
        if ($redis->exists($userSessionKey)) {
            $userSession = $redis->get($userSessionKey);
            if ($redis->exists($userSession)) {
                $redis->del($userSession);
            }
        }
    }
    
        public function getTopAgentId() {
        if (!$this->parent_id) {
            return $this->id;
        } else {
            $aFores = explode(',', $this->forefather_ids);
            return $aFores[0];
        }
    }
        public function getTopAgentUserName() {
        if (!$this->parent_id) {
            return $this->username;
        } else {
            $aFores = explode(',', $this->forefathers);
            return $aFores[0];
        }
    }

    public function blockUser() {
        $oQuery = $this->where('id', $this->id)->update(array('blocked' => 1));
        $this->deleteCache();
        return $oQuery;
    }

}
