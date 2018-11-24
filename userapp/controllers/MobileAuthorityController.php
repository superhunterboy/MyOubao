<?php

class MobileAuthorityController extends Controller {

    const MINUTES_SEVEN_DAYS = 10080;

    protected $errorFiles = ['mobile'];
    protected $Message;

    /**
     * 页面：登录
     * @return Response
     */
    public function login() {
        if (Request::method() == 'POST') {
            return $this->postLogin();
        } else {
            $this->halt(false, 'loginTimeout', UserUser::ERRNO_LOGIN_EXPIRED);
        }
    }

    /**
     * 动作：登录
     * @return Response
     */
    public function postLogin() {

        $data = getJsonData();
        $sUsername = $data['username'];
        $sPassword = $data['password'];

        $user = UserUser::where('username', '=', $sUsername)->first();
        if (empty($user) || !Hash::check($sPassword, $user->password)) {
            $this->halt(false, 'loginFailed', UserUser::ERRNO_LOGIN_FAILED, $data);
        }
        if ($user->blocked == 1) {
            $this->halt(false, 'userBlocked', UserUser::ERRNO_USER_LOGIN_BLOCKED, $data);
        }
        if (!$user->is_from_link && !$user->signin_at) {
            Session::put('first_login', true);
        }
        $user->signin_at = Carbon::now()->toDateTimeString();
        $user->login_ip = get_client_ip();


        $roles = $this->_getUserRole($user);
        // TODO 暂时取消代理用户可以登录移动端，因为登录后大部分接口不可用
        if ($user->is_agent) {
            $this->halt(false, 'loginNotAllowed', UserUser::ERRNO_USER_LOGIN_BLOCKED, $data);
        }
        $sJessionId = md5($user->username . Session::getId());
        Session::setId($sJessionId);
        Session::put('user_id', $user->id);
        Session::put('username', $user->username);
        Session::put('nickname', $user->nickname);
        Session::put('account_id', $user->account_id);
        Session::put('forefather_ids', $user->forefather_ids);
        Session::put('is_agent', $user->is_agent);
        Session::put('is_tester', $user->is_tester);
        Session::put('is_top_agent', $user->is_agent && empty($user->parent_id));
        Session::put('is_player', !$user->is_agent);
        Session::put('CurUserRole', $roles);
        // 默认前3次登录不用验证码, 3次登录失败后需要验证码, 登录成功则清空登录次数
        Session::save();
        if (Cache::has($sJessionId)) {
            Cache::put($sJessionId, Cache::get($sJessionId), self::MINUTES_SEVEN_DAYS);
        }
        $data = $this->_collectJsonData($user, $sJessionId);
        $user->save();
        $this->halt(true, 'loginSuccess', UserUser::ERRNO_LOGIN_SUCCESS, $aSuccessedBets, $aFailedBets, $data);
    }

    public function _collectJsonData($oUser, $sJessionId) {
        $fAvailable = formatNumber(Account::getAvaliable($oUser->id), 1);
        $data['available'] = $fAvailable;
        $data['jsessionid'] = $sJessionId;
        $data['user_id'] = $oUser->id;
        $data['username'] = $oUser->username;
        $data['nickname'] = $oUser->nickname;
        $data['user_type'] = $oUser->getUserType();
        $data['is_tester'] = $oUser->is_tester;
        $data['is_set_fund_password'] = intval(strlen($oUser->fund_password) > 0);
        $data['unread_msg_count'] = UserMessage::getUserUnreadMessagesNum();
        return $data;
    }

    /**
     * 动作：退出
     * @return Response
     */
    public function logout() {
        Session::flush();
        Session::save();
        $this->halt(true, 'loginOutSuccess', UserUser::ERRNO_LOGINOUT_SUCCESS, $data);
    }

    /**
     * 页面：注册
     * @return Response
     */
    public function register($sKeyword = null) {
        // pr($sKeyword);exit;
        if (Request::method() == 'POST') {
            return $this->postRegister();
        } else {
            $this->halt(false, 'registerError', UserUser::ERRNO_REGISTER_ERROR);
        }
    }

    /**
     * [postSignup 实际处理注册流程
     *         注册流程:
     *            1. 判断随机码是否正确
     *            2. 判断验证码是否正确
     *            3. 判断用户名是否已经存在
     *            4. 获取开户奖金组信息, 如果有链接开户的特征码, 则获取对应的奖金组信息, 否则, 获取体验账户的奖金组
     *            5. 生成用户信息
     *            6. 新建用户
     *            7. 新建用户的账户
     *            8. 更新用户的account_id字段
     *            9. 创建用户奖金组
     * ]
     * @return [Response] [description]
     */
    public function postRegister() {

        $data = getJsonData();

        if ($this->validateUsernameExist($iErrno, $data)) {
            $this->halt(false, 'registerError', $iErrno);
        }
        if ($this->validateEmailExist($iErrno, $data)) {
            $this->halt(false, 'registerError', $iErrno);
        }

        $aPrizeGroup = [];
        $oPrizeGroup = null;
        $bSucc = UserUser::getRegistPrizeGroup(null, $aPrizeGroup, $oPrizeGroup);

        if (!$bSucc) {
            $this->halt(false, 'registerError', UserUser::ERRNO_REGISTER_ERROR);
        }

        $sPrizeGroup = $oPrizeGroup->is_agent ? $aPrizeGroup[0]->prize_group : '';
        $aExtraData = [
            'is_agent' => intval($oPrizeGroup->is_agent),
            'parent_id' => ($oPrizeGroup->is_admin ? null : $oPrizeGroup->user_id),
            'parent' => ($oPrizeGroup->is_admin ? '' : $oPrizeGroup->username),
            'is_tester' => $oPrizeGroup->is_tester,
            'register_at' => Carbon::now()->toDateTimeString(),
        ];
        $data = array_merge($data, $aExtraData);
        $oUser = new UserUser;
        $aReturnMsg = $this->generateUserInfo($oUser, $sPrizeGroup, $data);

        if (!$aReturnMsg['success']) {
            $this->halt(true, 'registerError', $aReturnMsg['msg']);
        }
        $oUser->is_from_link = 1;

        DB::connection()->beginTransaction();
        $bSucc = $this->createProcess($oUser, $aPrizeGroup, $oPrizeGroup);
        if ($bSucc) {
            DB::connection()->commit();
            $this->halt(true, 'registerSuccess', UserUser::ERRNO_REGISTER_SUCCESS);
        } else {
            DB::connection()->rollback();
            $this->halt(false, 'registerError', UserUser::ERRNO_REGISTER_ERROR);
        }
    }

    /**
     * [validateCaptchaError 验证验证码]
     * @return [Boolean/Response] [验证成功/失败]
     */
    private function validateCaptchaError(& $sErrorMsg) {
        $aDatas = ['captcha' => trim(Input::get('captcha'))];
        $aRules = ['captcha' => 'required|captcha'];

        $oValidator = Validator::make($aDatas, $aRules);
        if (!$oValidator->passes()) {
            $sErrorMsg = __('_basic.captcha-error');
            return true;
        }
        return false;
    }

    /**
     * [validateUsernameExist 验证用户名是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateUsernameExist(& $iErrno, $data) {
        $sUsername = array_get($data, 'username');
        if (!$sUsername) {
            $iErrno = UserUser::ERRNO_MISSING_USERNAME;
//            $sErrorMsg = '请填写用户名！';
            return true;
        } else if (UserUser::checkUsernameExist($sUsername)) {
            $iErrno = UserUser::ERRNO_EXIST_USERNAME;
//            $sErrorMsg = '该用户名已被注册，请重新输入！';
            return true;
        }
        return false;
    }

    /**
     * [validateEmailExist 验证邮箱是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateEmailExist(& $iErrno, $data) {
        $sEmail = array_get($data, 'email');
        // $sPassword = trim(Input::get('password'));
        if (!$sEmail) {
            $iErrno = UserUser::ERRNO_MISSING_EMAIL;
//            $sErrorMsg = '请填写邮箱！';
            return true;
        } else if (UserUser::checkEmailExist($sEmail)) {
            $iErrno = UserUser::ERRNO_EXIST_EMAIL;
//            $sErrorMsg = '该邮箱已被注册，请重新输入！';
            return true;
        }
        return false;
    }

    /**
     * [generateUserInfo 生成新建用户的信息]
     * @param [String] $sPrizeGroup [如果是代理, 则prize_group为其奖金组, 玩家有多种奖金组, 所以置空值]
     * @param [Array] $data         [表单参数]
     * @return [Array]              [生成成功/失败提示信息]
     */
    public function generateUserInfo(& $oUser, $sPrizeGroup, $data) {
        $data['username'] = strtolower($data['username']);
        (isset($data['nickname']) && $data['nickname']) or $data['nickname'] = $data['username'];
        (isset($data['fund_password']) && $data['fund_password']) or $data['fund_password'] = '';
        // TIP 此处的prize_group实际是prize_groups表的classic_prize字段
        if ($sPrizeGroup) {
            $data['prize_group'] = $sPrizeGroup;
        }
        $data['register_ip'] = get_client_ip();
        // 验证成功，添加用户
        $oUser->fill($data);
        // TODO 这两个字段不能为空, parent_str可能已经被弃用, 后续可以考虑写到User模型的beforeValidate里
        $oUser->parent_str = $oUser->forefather_ids;
        $aReturnMsg = ['success' => true, 'msg' => UserUser::ERRNO_USER_INFO_GENERATED];
        if ($oUser->password) {
            $aReturnMsg = $oUser->generatePasswordStr(1);
            if ($aReturnMsg['success']) {
                $oUser->password = $aReturnMsg['msg'];
                $aReturnMsg['msg'] = UserUser::ERRNO_PASSWORD_GENERATED;
            }
            unset($oUser->password_confirmation);
        } else {
            return ['success' => false, 'msg' => UserUser::NO_PASSWORD];
        }

        return $aReturnMsg;
    }

    /**
     * [createProcess 开户流程]
     * @param  [Object] $oUser       [用户对象]
     * @param  [Array]  $aPrizeGroup [奖金组数据]
     * @param  [Object] $oPrizeGroup [开户链接对象]
     * @param  [String] $sPrizeGroupCode [链接开户特征码]
     * @return [Boolean]             [开户成功/失败]
     */
    private function createProcess($oUser, $aPrizeGroup, $oPrizeGroup) {
        $bSucc = false;
        // $aRules = User::$rules;
        // $aRules['username'] = str_replace('{:id}', '', $aRules['username']);
        if ($bSucc = $oUser->save()) {
            $oAccount = $oUser->generateAccountInfo();
            if ($bSucc = $oAccount->save()) {
                // $aRules = User::$rules;
                // $aRules['username'] = str_replace('{:id}', $oUser->id, $aRules['username'] );
                $oUser->account_id = $oAccount->id;
                // $bSucc = $oUser->save($aRules);
                if ($bSucc = $oUser->save()) {
                    $aReturnMsg = UserPrizeSet::createUserPrizeGroup($oUser, $aPrizeGroup); // $this->createUserPrizeGroup($aPrizeGroup, $oUser);
                    $bSucc = $aReturnMsg['success'];
                }
            }
        }
        return $bSucc;
    }

    /**
     * 输出信息并终止运行
     * @param string $msg
     */
    protected function halt($bSuccess, $sType, $iErrno, & $aSuccessedBets = null, & $aFailedBets = null, & $aData = null, $sLinkUrl = null) {
        is_object($this->Message) or $this->Message = new Message($this->errorFiles, true);
        $this->Message->output($bSuccess, $sType, $iErrno, $aData, $aSuccessedBets, $aFailedBets, $sLinkUrl);
        exit;
    }

    /**
     * 页面：注册成功，提示激活
     * @param  string $email 用户注册的邮箱
     * @return Response
     */
    public function getSignupSuccess($email) {
        // 确认是否存在此未激活邮箱
        $activation = Activation::whereRaw("email = '{$email}'")->first();
        // 数据库中无邮箱，抛出404
        is_null($activation) AND App::abort(404);
        // 提示激活
        return View::make('authority.signupSuccess')->with('email', $email);
    }

    /**
     * 动作：激活账号
     * @param  string $activationCode 激活令牌
     * @return Response
     */
    public function getActivate($activationCode) {
        // 数据库验证令牌
        $activation = Activation::where('token', $activationCode)->first();
        // 数据库中无令牌，抛出404
        is_null($activation) AND App::abort(404);
        // 数据库中有令牌
        // 激活对应用户
        $user = UserUser::where('email', $activation->email)->first();
        $user->activated_at = new Carbon;
        $user->save();
        // 删除令牌
        $activation->delete();
        // 激活成功提示
        return View::make('authority.activationSuccess');
    }

    /**
     * 页面：忘记密码，发送密码重置邮件
     * @return Response
     */
    public function getForgotPassword() {
        return View::make('authority.password.remind');
    }

    /**
     * 动作：忘记密码，发送密码重置邮件
     * @return Response
     */
    public function postForgotPassword() {
        // 调用系统提供的类
        $response = Password::remind(Input::only('email'), function ($m, $user, $token) {
                    $m->subject('密码重置邮件'); // 标题
                });
        // 检测邮箱并发送密码重置邮件
        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::REMINDER_SENT:
                return Redirect::back()->with('status', Lang::get($response));
        }
    }

    /**
     * 页面：进行密码重置
     * @return Response
     */
    public function getReset($token) {
        // 数据库中无令牌，抛出404
        is_null(PassowrdReminder::where('token', $token)->first()) AND App::abort(404);
        return View::make('authority.password.reset')->with('token', $token);
    }

    /**
     * 动作：进行密码重置
     * @return Response
     */
    public function postReset() {
        // 调用系统自带密码重置流程
        $credentials = Input::only(
                        'email', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
                    // 保存新密码
                    $user->password = $password;
                    $user->save();
                    // 登录用户
                    Auth::login($user);
                });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            // no break
            case Password::INVALID_TOKEN:
            // no break
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::PASSWORD_RESET:
                return Redirect::to('/');
        }
    }

    protected function _getUserRole($oUser) {
        $roles = $oUser->getRoleIds();

        $aDefaultRoles[] = Role::EVERY_USER;

        if ($oUser->is_agent) {
            $aDefaultRoles[] = Role::AGENT;
            if (empty($oUser->parent_id)) {
                $aDefaultRoles[] = Role::TOP_AGENT;
            }
        } else {
            $aDefaultRoles[] = Role::PLAYER;
        }
        $roles = array_merge($roles, $aDefaultRoles);
        $roles = array_unique($roles);
        $roles = array_map(function($value) {
            return (int) $value;
        }, $roles);

        return $roles;
    }

}
