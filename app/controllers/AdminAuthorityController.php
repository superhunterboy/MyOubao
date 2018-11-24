<?php

//dezend by http://www.yunlu99.com/ QQ:270656184
class AdminAuthorityController extends Controller {

    public function signin() {
        $bSecureCard = SysConfig::check('enable_secure_card_check', true);
        $bCaptcha = SysConfig::check('enable_identifying_code_check', true);

        if (Request::method() == 'POST') {
            if ($bCaptcha) {
                $captcha = array('captcha' => Input::get('captcha'));
                $rules = array('captcha' => 'required|captcha');
                $validator = Validator::make($captcha, $rules);

                if (!$validator->passes()) {
                    return $this->goBack('error', __('_basic.login-fail-captcha'));
                }
            }

            $sUsername = Input::get('username');
            $sPassword = Input::get('password');
            $credentials = array('username' => $sUsername, 'password' => $sPassword);
            $remember = Input::get('remember-me', 0);
            $user = AdminUser::where('username', '=', $sUsername)->first();
            if (empty($user) || !Hash::check($sPassword, $user->password)) {
                return $this->goBack('error', __('_basic.login-fail-wrong'));
            }

            if ($bSecureCard) {
                $secure_passwrod = Input::get('secure_password');
                $sSecureCardNumber = $user->secure_card_number;
                $oSecureCard = SecureCard::where('number', '=', $sSecureCardNumber)->first();
                $sSafeString = $oSecureCard->safe_string;
                useclass('seamoonapi');
                $seamoon = new seamoonapi();
                error_reporting(0);
                $result = $seamoon->checkpassword($sSafeString, $secure_passwrod);

                if (strlen($result) <= 3) {
                    return $this->goBack('error', __('_basic.login-fail-secure'));
                }

                $check_result = $seamoon->passwordsyn($sSafeString, $secure_passwrod);

                if (strlen($check_result) <= 3) {
                    return $this->goBack('error', __('_basic.login-fail-secure'));
                }

                $oSecureCard->safe_string = $check_result;
                $oSecureCard->number = $sSecureCardNumber;
                $oSecureCard->save();
            }

            Session::put('admin_user_id', $user->id);
            Session::put('admin_username', $user->username);
            Session::put('admin_language', $user->language);
            $user->signin_at = Carbon::now()->toDateTimeString();
            $user->save();
            $roles = $this->_getUserRole();

            if (in_array(Role::DENY, $roles)) {
                return $this->goBack('error', __('_basic.login-fail-wrong'));
            }

            Session::put('CurUserRole', $roles);
            Session::put('IsAdmin', in_array(Role::ADMIN, $roles));
            $bFlagForFinance = !empty(array_intersect($roles, AdminRole::$aRoleFinance));
            $bFlagForCustomer = !empty(array_intersect($roles, AdminRole::$aRoleCustomer));
            Session::put('CurUserRole', $roles);
            Session::put('IsAdmin', in_array(Role::ADMIN, $roles));
            // 提现申请提示音
            Session::put('bFlagForFinance', $bFlagForFinance);
            Session::put('bFlagForCustomer', $bFlagForCustomer);
            return Redirect::route('admin.frameset');
        } else {
            App::setlocale('en');
            $sAppTitle = SysConfig::readValue('app_title');
            $sAppName = SysConfig::readValue('app_name');
            return View::make('adminAuthority.signin')->with(compact('bSecureCard', 'bCaptcha', 'sAppTitle', 'sAppName'));
        }
    }

    public function logout() {
        if (0 < Session::get('admin_user_id')) {
            Session::flush();
        }

        return Redirect::route('admin.frameset');
    }

    public function signup() {
        if (Request::method() == 'PUT') {
            $data = Input::all();
            $rules = array('username' => 'required|between:4,16|unique:users', 'password' => 'required|alpha_dash|between:6,16|confirmed');
            $messages = array('username.required' => '请输入用户名。', 'username.between' => '用户名长度请保持在:min到:max位之间。', 'password.required' => '请输入密码。', 'password.alpha_dash' => '密码格式不正确。', 'password.between' => '密码长度请保持在:min到:max位之间。', 'password.confirmed' => '两次输入的密码不一致。');
            $validator = Validator::make($data, $rules, $messages);

            if ($validator->passes()) {
                $user = new User();
                $user->email = Input::get('email');
                $user->password = Input::get('password');

                if ($user->save()) {
                    $activation = new Activation();
                    $activation->email = $user->email;
                    $activation->token = str_random(40);
                    $activation->save();
                    $with = array('activationCode' => $activation->token);
                    Mail::send('adminAuthority.email.activation', $with, function($message) use($user) {
                        $message->to($user->email)->subject('账号激活邮件');
                    });
                    return Redirect::route('signupSuccess', $user->email);
                } else {
                    return Redirect::back()->withInput()->withErrors(array('add' => '注册失败。'));
                }
            } else {
                return Redirect::back()->withInput()->withErrors($validator);
            }
        }

        return View::make('adminAuthority.signup');
    }

    public function getSignupSuccess($email) {
        $activation = Activation::whereRaw('email = \'' . $email . '\'')->first();
        is_null($activation) && App::abort(404);
        return View::make('adminAuthority.signupSuccess')->with('email', $email);
    }

    public function getActivate($activationCode) {
        $activation = Activation::where('token', $activationCode)->first();
        is_null($activation) && App::abort(404);
        $user = User::where('email', $activation->email)->first();
        $user->activated_at = Carbon::now()->toDateTimeString();
        $user->save();
        $activation->delete();
        return View::make('adminAuthority.activationSuccess');
    }

    public function getForgotPassword() {
        return View::make('adminAuthority.password.remind');
    }

    public function postForgotPassword() {
        $response = Password::remind(Input::only('email'), function($m, $user, $token) {
                    $m->subject('密码重置邮件');
                });

        switch ($response) {
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::REMINDER_SENT:
                return Redirect::back()->with('status', Lang::get($response));
        }
    }

    public function getReset($token) {
        is_null(PassowrdReminder::where('token', $token)->first()) && App::abort(404);
        return View::make('adminAuthority.password.reset')->with('token', $token);
    }

    public function postReset() {
        $credentials = Input::only('email', 'password', 'password_confirmation', 'token');
        $response = Password::reset($credentials, function($user, $password) {
                    $user->password = $password;
                    $user->save();
                    Auth::login($user);
                });

        switch ($response) {
            case Password::INVALID_PASSWORD:
            case Password::INVALID_TOKEN:
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::PASSWORD_RESET:
                return Redirect::to('/admin');
        }
    }

    protected function _getUserRole() {
        $iUserId = Session::get('admin_user_id');
        $roles = AdminUser::find($iUserId)->getRoleIds();
        $adminRoleId = Role::ADMIN;
        $everyOneId = Role::EVERYONE;
        array_push($roles, $everyOneId);
        array_map(function($value) {
            return (int) $value;
        }, $roles);
        array_unique($roles);
        return $roles;
    }

    protected function goBack($sMsgType, $sMessage, $bWithModelErrors = false) {
        $oRedirectResponse = Redirect::back()->withInput()->with($sMsgType, $sMessage);
        !$bWithModelErrors || ($oRedirectResponse = $oRedirectResponse->withErrors($this->model->validationErrors));
        return $oRedirectResponse;
    }

}

?>