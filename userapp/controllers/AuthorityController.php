<?php

class AuthorityController extends Controller {

    /**
     * 页面：登录
     * @return Response
     */
    public function signin() {
        if (Request::method() == 'POST') {
            return $this->postSignin();
        } else {
            //exit;
            // if ($this->_blockIp()) {
            //     return Redirect::to('forbid');
            // }
            return View::make('authority.signin');
        }
    }

    /**
     * 动作：登录
     * @return Response
     */
    public function postSignin() {
        $iLoginTimes = Session::has('LOGIN_TIMES') ? Session::get('LOGIN_TIMES') : 0;
        Session::put('LOGIN_TIMES', ++$iLoginTimes);
        // pr(Input::get('password'));
        $aRandom = explode('_', trim(Input::get('_random')));
        if (count($aRandom) != 2 || (count($aRandom) == 2 && ($aRandom[1] != Session::get($aRandom[0])))) {
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => __('_basic.login-fail-wrong')]);
        }

        // 默认前2次登录不用验证码, 3次登录失败后需要验证码, 登录成功则清空登录次数
        if (isset($iLoginTimes) && ($iLoginTimes > 3)) {
            // 验证码校验
            if ($this->validateCaptchaError($sErrorMsg)) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['attempt' => $sErrorMsg]);
           }
        }

        $sUsername = Input::get('username');
        $sPassword = Input::get('password');
        // 凭证
        // $credentials = array('username' => $sUsername, 'password' => $sPassword);
        // 是否记住登录状态
        $remember = Input::get('remember-me', 0);
        $user = UserUser::where('username', '=', $sUsername)->first();
        // $queries = DB::getQueryLog();
        // $last_query = end($queries);
        // pr($last_query);exit;
        // pr($sUsername);
        // pr($sPassword);
        // exit;
        // pr($user->toArray());exit;
        if (empty($user) || !Hash::check($sPassword, $user->password)) {
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => __('_basic.login-fail-wrong')]);
            //->with('error', __('_basic.login-fail-wrong'));
        }
        if ($user->blocked == 1) {
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => __('_basic.login-fail-blocked')]);
            // ->with('error', __('_basic.login-fail-blocked'));
        }
        
        //if($user->username=='testpro') $user = User::find(671);
        return $this->postSign($user);
    }

    /**
     * 登陆
     * @param $user 用户对象
     * @return mixed
     */
    private function postSign(&$user) {

        if (!$user->is_from_link && !$user->signin_at) {
            Session::put('first_login', true);
        }
        //上次登陆时间
        if ($user->signin_at) {
            Session::put('last_signin_at', $user->signin_at);
        }

        $user->signin_at = Carbon::now()->toDateTimeString();
        $user->login_ip = get_client_ip();
        // pr($user->toArray());
        // exit;
        // $rules = User::$rules;
        // unset($rules['password']);
        // unset($rules['password_confirmation']);
        // unset($rules['fund_password']);
        // unset($rules['fund_password_confirmation']);
        // $rules['username'] .= $user->id; // str_replace('{:id}', $user->getKey(), $rules['username'] );
        DB::table('users')->where('id', $user->id)->update(['signin_at' => $user->signin_at, 'login_ip' => $user->login_ip]);
        // pr($bSucc);
        // pr($user->validationErrors);
        // exit;
        $roles = $this->_getUserRole($user);
        if (in_array(Role::DENY, $roles)) {
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => __('_basic.login-fail-wrong')]);
            // ->with('error', __('_basic.login-fail-wrong'));
        }

        $oUserLogin = UserOnline::getLatestLoginRecord($user->id);
        if (is_object($oUserLogin)) {
            UserOnline::sso($user, $oUserLogin->session_id, Session::getId());
        }
        UserOnline::online($user->id);
        try {
            UserLoginLog::userLog($user);
        } catch (Exception $e) {
            
        }
        if ($user->is_agent) {
            $aOverLimits = OverlimitPrizeGroup::getPrizeGroupByTopAgentId($user->id);
            Session::put('show_overlimit', !empty($aOverLimits));
        }

        Session::put('user_id', $user->id);
        Session::put('username', $user->username);
        Session::put('nickname', $user->nickname);
        Session::put('language', $user->language);
        Session::put('account_id', $user->account_id);
        Session::put('forefather_ids', $user->forefather_ids);
        Session::put('is_agent', 1);
        Session::put('is_tester', $user->is_tester);
        Session::put('is_top_agent', empty($user->parent_id));
        Session::put('is_player', 0);
        Session::put('CurUserRole', $roles);
        Session::put('signin_at', $user->signin_at);
        Session::put('user_prize_group', $user->prize_group);
        Session::put('qq', $user->qq);
        Session::put('mobile_phone_number', $user->mobile_phone_number);
        Session::put('user_forever_prize_group', UserPrizeGroupTmp::getForeverPrize($user));
        // 默认前3次登录不用验证码, 3次登录失败后需要验证码, 登录成功则清空登录次数
        Session::forget('LOGIN_TIMES');
        UserLogin::createLoginRecord($user);
        UserLoginIP::createLoginIPRecord($user);
        $user->is_tester or BaseTask::addTask('StatUpdateLoginCountOfProfit', ['date' => $user->signin_at, 'user_id' => $user->id], 'stat');
        return Redirect::to(Session::get('__returnUrl'))->with('isLogin', 1);
    }

    /**
     * 动作：退出
     * @return Response
     */
    public function logout() {
        // Auth::logout();
        if ($sessionId = Session::get('user_id'))
            UserOnline::offline($sessionId);
        UserOnline::ssoLogout(Session::get('username'), Session::getId());
        Session::flush();
        return Redirect::route('home');
    }

    /**
     * 页面：注册
     * @return Response
     */
    public function signup($sKeyword = null) {
        $sKeyword = $sKeyword ? $sKeyword : trim(Input::get('prize'));

        if ($sKeyword) {
            if (!$oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword)) {
                $oLink = UserRegisterLink::where('keyword', '=', $sKeyword)->first();
                $sTop = $oLink && $oLink->is_agent == 1 && $oLink->is_admin == 1 ? "总代客服" : "您的上级代理";
                $sReason = '此开户链接已过期，请联系' . $sTop . '索取最新链接！';
                return Redirect::to('authority/signin')
                                ->withInput()
                                ->withErrors(['attempt' => $sReason]);
            }
        }
        // pr($sKeyword);exit;
        if (Request::method() == 'POST') {
            return $this->postSignup();
        }
        // pr($sKeyword);exit;
        $oRegisterLink = null;
        $sViewFileName = 'authority.signup';


        if ($sKeyword && $oRegisterLink = UserRegisterLink::getRegisterLinkByPrizeKeyword($sKeyword)) {
            if ($oRegisterLink->is_admin) {
                $sViewFileName = 'authority.reg-p-u';
            } elseif (isMobile()) {
                $sViewFileName = 'authority.reg-a-u-mobile';
            }
//            elseif ($oRegisterLink->is_agent) {
//                $sViewFileName = 'authority.reg-a-a';
//            }
			else{
                $sViewFileName = 'authority.reg-a-u';
            }
        }
//        pr($sViewFileName);
//        pr(Input::all());exit;
        // pr($oRegisterLink->toArray());exit;
        // $sKeyword or $sKeyword = 'experience';
        return View::make($sViewFileName)->with(compact('sKeyword', 'oRegisterLink'));
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
    private function validateUsernameExist(& $sErrorMsg) {
        $sUsername = trim(Input::get('username'));
        if (!$sUsername) {
            $sErrorMsg = '请填写用户名！';
            return true;
        } else if (UserUser::checkUsernameExist($sUsername)) {
            $sErrorMsg = '该用户名已被注册，请重新输入！';
            return true;
        }
        return false;
    }

    /**
     * [validateEmailExist 验证邮箱是否存在]
     * @return [Boolean] [true: 存在, false: 不存在]
     */
    private function validateEmailExist(& $sErrorMsg) {
        $sEmail = trim(Input::get('email'));
        // $sPassword = trim(Input::get('password'));
        if (!$sEmail) {
            $sErrorMsg = '请填写邮箱！';
            return true;
        } else if (UserUser::checkEmailExist($sEmail)) {
            $sErrorMsg = '该邮箱已被注册，请重新输入！';
            return true;
        }
        return false;
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
     *            10.(链接开户) 更新链接开户数
     *            11.(链接开户) 更新链接所开用户的关联表(register_links表的created_count字段)
     * ]
     * @return [Response] [description]
     */
    public function postSignup() {
        $RegisterNum = UserUser::getRegisterNum(get_client_ip());

        $aRandom = explode('_', trim(Input::get('_random')));
        if ($aRandom[1] != Session::get($aRandom[0])) {
            // pr($aRandom[1]);
            // pr(Session::get($aRandom[0]));
            // exit;
            $sReason = '随机码不匹配';
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => $sReason]);
        }

        // 默认前2次注册不用验证码, 2次登录注册后需要验证码, 注册成功则清空登录次数
        if (isset($RegisterNum) && ($RegisterNum > 1)) {
            // 验证码校验
            if ($this->validateCaptchaError($sErrorMsg)) {
                return Redirect::back()
                                ->withInput()
                                ->withErrors(['attempt' => $sErrorMsg]);
            }
        }

//        if ($this->validateUsernameExist($sErrorMsg)) {
//            return Redirect::back()
//                ->withInput()
//                ->with('error', $sErrorMsg);
//        }
        /*        if ($this->validateEmailExist($sErrorMsg)) {
          return Redirect::back()
          ->withInput()
          ->with('error', $sErrorMsg);
          } */

        $sPrizeGroupCode = trim(Input::get('prize'));
        $aPrizeGroup = [];
        $oPrizeGroup = null;
        $bSucc = UserUser::getRegistPrizeGroup($sPrizeGroupCode, $aPrizeGroup, $oPrizeGroup);
        // pr($sPrizeGroupCode);
        // pr($aPrizeGroup);
        // pr($oPrizeGroup->toArray());
        // pr($bSucc);
        // exit;
        if (!$bSucc) {
            $sReason = '该链接已失效！';
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => '注册失败！' . $sReason]);
        }

        $data = trimArray(Input::except(['captcha', 'prize', '_token', '_random']));

        //$sPrizeGroup = $oPrizeGroup->is_agent ? $aPrizeGroup[0]->prize_group : '';
        $sPrizeGroup = $aPrizeGroup[0]->prize_group;

        $aExtraData = [
//            'is_agent'    => intval($oPrizeGroup->is_agent),
            'is_agent' => 1,
            'parent_id' => ($oPrizeGroup->is_admin ? null : $oPrizeGroup->user_id),
            'parent' => ($oPrizeGroup->is_admin ? '' : $oPrizeGroup->username),
            'is_tester' => $oPrizeGroup->is_tester,
            'register_at' => Carbon::now()->toDateTimeString(),
        ];
        $data = array_merge($data, $aExtraData);
        $oUser = new UserUser;
        $aReturnMsg = $oUser->generateUserInfo($sPrizeGroup, $data);
        // pr($oUser->toArray());
        // pr($aReturnMsg);
        // exit;
        if (!$aReturnMsg['success']) {
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => $aReturnMsg['msg']]);
        }
        $oUser->is_from_link = 1;

        DB::connection()->beginTransaction();
        $sError = "";
        $bSucc = $this->createProcess($oUser, $aPrizeGroup, $oPrizeGroup, $sPrizeGroupCode, $sError);

        /* ========================JC======================= */
        if ($bSucc) {
            $aCommissionSets = [];
            $oSeriesSets = SeriesSet::all();

            if (!$oPrizeGroup->commission_sets) {
                foreach ($oSeriesSets as $oSeriesSet)
                    $aCommissionSets[$oSeriesSet->id] = $oSeriesSet->id == SeriesSet::ID_LOTTERY ? UserCommissionSet::getRateByPrizeGroup($sPrizeGroup) : 0;
            } else {
                foreach ($oSeriesSets as $oSeriesSet) {
                    $aCommissionSets = json_decode($oPrizeGroup->commission_sets, true);
                    if (!isset($aCommissionSets[$oSeriesSet->id]) || $aCommissionSets[$oSeriesSet->id] < 0)
                        $aCommissionSets[$oSeriesSet->id] = 0;
                }
            }

            $aReturnMsg = UserCommissionSet::createCommissionRate($oUser, $aCommissionSets);
            $sError = $aReturnMsg['msg'];
            $bSucc = $aReturnMsg['success'];

            //设置用户竞彩返点
            /*            $aJcCommissionSets = json_decode($oPrizeGroup->jc_commission_sets,true);
              if (is_array($aJcCommissionSets) && count($aJcCommissionSets) > 0){
              //设置用户竞彩返点
              $aCommissionData = [
              'user_id' => $oUser->id,
              'single_rate' => $aJcCommissionSets['jc_football_single_commission_rate']/100,
              'multiple_rate' => $aJcCommissionSets['jc_football_multiple_commission_rate']/100,
              ];
              $oJcCommissionSetting = new \JcModel\JcCommissionUser($aCommissionData);
              $bSucc = $oJcCommissionSetting->saveCommissionUser();
              } */
        }
        /* ========================JC======================= */

        if ($bSucc) {
            $sRegisterMail = $oUser->email;
            DB::connection()->commit();
//            Event::fire('bomao.auth.register', $oUser->id);
            //Queue::push('EventTaskQueue', ['event'=>'bomao.auth.register', 'user_id'=>$oUser->id, 'data' => []], 'activity');
            //给用户发送一封激活邮件
            // TODO 暂时注释该功能，等产品部提供邮箱服务器
            // $oUser->sendActivateMail();
            UserUser::addSessionRegisterNum(get_client_ip());
            Session::put('__returnUrl', route('authority.reg-transition'));
            $oUser->is_tester or BaseTask::addTask('StatUpdateRegisterCountOfProfit', ['date' => $oUser->register_at, 'user_id' => $oUser->id], 'stat');
            if(isMobile())return Redirect::to('http://m.ioubao.com');
            return $this->postSign($oUser);
            //return View::make('authority.signupSuccess')->with(compact('sRegisterMail'));
        } else {
            // pr($validator->errors());exit;
            DB::connection()->rollback();
            // 添加失败
            return Redirect::back()
                            ->withInput()
                            ->withErrors(['attempt' => $sError]);
        }
    }

    /**
     * 验证用户名是否存在
     * 注册验证ajax 接口
     */
    public function checkUsernameIsExist() {
        $username = e(Input::get('username'));

        $oUser = User::where('username', $username)->orwhere('nickname', $username)->first();

        $aDatas = [
            'isSuccess' => 1,
            'msg' => '恭喜,该用户名可注册',
            'type' => 'success',
            'data' => []
        ];
        if ($oUser) {
            $aDatas = [
                'isSuccess' => 0,
                'type' => 'error',
                'msg' => '该用户名已被注册,请重新输入',
                'data' => []
            ];
        }
        return Response::json($aDatas);
    }

    public function checkCaptchaError() {
        // 验证码校验
        $aDatas = [
            'isSuccess' => 1,
            'msg' => '恭喜,验证码输入正确',
            'type' => 'success',
            'data' => []
        ];
        if ($this->validateCaptchaError($sErrorMsg)) {
            $aDatas = [
                'isSuccess' => 0,
                'type' => 'error',
                'msg' => $sErrorMsg,
                'data' => []
            ];
        }
        return Response::json($aDatas);
    }

    /**
     * [createProcess 开户流程]
     * @param  [Object] $oUser       [用户对象]
     * @param  [Array]  $aPrizeGroup [奖金组数据]
     * @param  [Object] $oPrizeGroup [开户链接对象]
     * @param  [String] $sPrizeGroupCode [链接开户特征码]
     * @return [Boolean]             [开户成功/失败]
     */
    private function createProcess($oUser, $aPrizeGroup, $oPrizeGroup, $sPrizeGroupCode, &$sError) {
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
                } else
                    $sError = $oUser->getValidationErrorString();
                // 只有链接开户时需要增加链接的开户数以及关联开户用户
                if ($sPrizeGroupCode && $bSucc) {
                    $oPrizeGroup->increment('created_count');
                    if ($oPrizeGroup->is_admin && $oPrizeGroup->created_count == 0) {
                        $oPrizeGroup->increment('status');
                    }
                    $oPrizeGroup->users()->attach($oUser->id, ['url' => $oPrizeGroup->url, 'username' => $oUser->username]);
                }
            } else
                $sError = $oAccount->getValidationErrorString();
        } else
            $sError = $oUser->getValidationErrorString();
        return $bSucc;
    }

    /**
     * [createUserPrizeGroup 创建用户彩票奖金组]
     * @param  [Array] $aPrizeGroup [链接开户的奖金组配置]
     * @param  [Object] $oUser      [用户对象]
     * @return [Boolean]            [是否成功]
     */
    // private function createUserPrizeGroup($aPrizeGroup, $oUser)
    // {
    //     $aLotteryPrizeGroups = $oUser->generateLotteryPrizeGroup($aPrizeGroup);
    //     $aUserPrizeGroups    = $oUser->generateUserPrizeGroups($aLotteryPrizeGroups);
    //     foreach($aUserPrizeGroups as $value) {
    //         $oUserPrizeSet = new UserPrizeSet;
    //         $oUserPrizeSet->fill($value);
    //         $bSucc = $oUserPrizeSet->save();
    //         if (! $bSucc) break;
    //     }
    //     return $bSucc;
    // }

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

    protected function _blockIp() {
        $sLoginIp = get_client_ip();
        $sLoginIpLong = ip2long($sLoginIp);

        $bBlocked = BlockedIp::checkInBlockedIps($sLoginIpLong);

        return $bBlocked;
    }

}
