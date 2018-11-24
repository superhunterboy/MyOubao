<?php

//---------------for test------------------



// Route::get('admin/controller/newviews', array('as' => 'test', 'uses' => 'AdminController@index'));

Route::get('lmongo', function () {
    $id = LMongo::collection('users')->insert(['email' => 'testmongo@test.com', 'username' => 'testmongo']);
    pr($id);
    $user = LMongo::collection('users')->where('username', 'testmongo')->first();
    pr($user);
});

Route::get('test', function ()
{
    /**active_red_envelopes
     * 测试活动
     */
//    $this->data=array ( 'username' => 'wanjia131', 'turnover' => '2.000000', 'user_id' => '2', 'lottery_id' => '1', 'way_id' => '33', 'issue' => 151124045, 'date' => '2015-11-24 13:28:19', );
//           extract($this->data);
    $data=array ( 'username' => 'wanjia131', 'turnover' => '2.000000', 'user_id' => '2', 'lottery_id' => '1', 'way_id' => '33', 'issue' => 151124045, 'date' => '2015-11-24 13:28:19', );
           extract($data);

        
   

        if (!$user_id || !$username || !$turnover || !$lottery_id || !$way_id || !$issue) {
            $this->log = "ERROR: Invalid Data, Exiting";
            return self::TASK_SUCCESS;
        }
        //检查游戏玩法是不是在活动游戏里
        $ways=LotteryWay::getLotteryWaysByLotteryId($lottery_id);
        $way_ids=array_column($ways,'id');
        if(!in_array($way_id,$way_ids)){
                return self::TASK_SUCCESS;
        }
        //获取红包期id
        $currentActive=ActiveRedEnvelopeUser::getCurrentRedEnvelopeId();
        if(!is_object($currentActive)){
                 $this->log = "ERROR: Invalid activeRedEnvelop, Exiting";
            return self::TASK_SUCCESS;
        }
        //统计入库
        
        $DB = DB::connection();
        $oUser = User::find($user_id);
//        pr($oUser->toArray());
        $DB->beginTransaction();
//        $type  = $this->data['type'];
        

        // 更新用户盈亏数据
        if (!$bSucc = ActiveRedEnvelopeUser::updateUserActiveProfitData( $currentActive, $oUser, $turnover)) {
            $DB->rollback();
            $this->log = "User Profit Update Failed";
            return self::TASK_RESTORE;
        }

    
    
    exit;
    
    /***测试Dashpay****/
   $sxml=' <?xml version="1.0" encoding="utf-8" ?>
<message>
<!-- cmd: PC 6006, Mobile 6009--> 
<cmd>6006</cmd>
<merchantid>AQ9948</merchantid >
<language>zh-cn</language>
<userinfo>
<order>635474128841458461</order>
<username>张三</username>
<money>1.00</money>
<unit>1</unit>
<time>2014-09-27 11:08:31</time>
<remark>Test Pay</remark>
<backurl>http://www.melc.cn/backUrl.aspx</backurl>
</userinfo></message>';
       $kye1='RtBBeV9KIlU=';
       $kye2='KOHatfK97YY=';
       $md5='c02fee0287044aaf966ca4c57e53cab1';
       
       $md5_encrypt=  md5($sxml.$md5);
       
       $random_value=  rand();
       $data_to_encrpt=$sxml.$md5_encrypt.md5($random_value);
       
//        $input = trim(chop(base64_decode($input)));
        $td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
          $size=mcrypt_enc_get_iv_size($td);//设置初始向量的大小
            $iv = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量
            $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
            $key_1 = substr($kye1,0,$ks);
            mcrypt_generic_init($td, $key_1, $iv); //初始处理
        $decrypted_data = mdecrypt_generic($td, $data_to_encrpt);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $des= trim(chop($decrypted_data));
   
//        var_dump($des);exit;
        
        //解密
//             $td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', '');
//          $size=mcrypt_enc_get_iv_size($td);//设置初始向量的大小
//            $iv = mcrypt_create_iv($size, MCRYPT_RAND);//创建初始向量
//            $ks = mcrypt_enc_get_key_size($td);//返回所支持的最大的密钥长度（以字节计算）
//            $key_1 = substr($kye1,0,$ks);
//            mcrypt_generic_init($td, $key_1, $iv); //初始处理
//        $decrypted_data = mdecrypt_generic($td, $data_to_encrpt);
//        mcrypt_generic_deinit($td);
//        mcrypt_module_close($td);
//        $des= trim(chop($decrypted_data));
        
        
    
        $buildurl= Url::build_url('https://sdpay.officenewline.org:21103/ToService.aspx');
        $aRequestData=[
            'pid'=>'123',
            'des'=>$des,
        ];
//        echo $buildurl;exit;
        $oCurl = new MyCurl($buildurl);
        $oCurl->setTimeout(10);
        $oCurl->setPost($buildurl);
        $oCurl->setReferer('http://testing.dayu.ph/');
        $oCurl->createCurl();
        $oCurl->execute();
    
        $sJsonResult = $oCurl->__tostring();
        var_dump($sJsonResult);exit;
        $aResult = json_decode($sJsonResult, TRUE);
    
    
    
    
    
    
    
    /********************/
//    $aRecords = Project::whereIn('id', [1,2])->get();
//    $data = [];
//    foreach ($aRecords as $key => $value) {
//        $data[$value->id] = gzuncompress($value->compress_bet_number);
//    }
//    pr($data);exit;
    // $columns = Schema::getColumnListing('sys_configs'); // users table
    // $columns = DB::connection()->getDoctrineColumn('sys_configs', 'item')->getType()->getName();
    // $sm = DB::connection()->getSchemaManager();
    // $columns = $sm->listTableColumns('sys_configs');
    // dd($columns);
    // $data = [];
    // $columns = (SysConfig::whereNull('parent_id')->get(['id', 'parent_id', 'item', 'title']));
    // foreach ($columns as $key => $value) {
    //     array_push($data, $value);
    // }
    // $data = SysConfig::find(1);
    // dd($data->getAttributes());
    // $sc = new SysConfig;
    // $data = $sc->format(1, 'boolean');
    // $data = $sc->getDataByParams([['item', '=', 'app_title']]);
    // $data = $sc->readValue('app_title');
    // $aColumns = ['id', 'value', 'default_value', 'data_type', 'validation', 'data_source'];
    // $data = $sc->getObject('app_title', 1, ['conditions' => [['item', '=', 'app_title']], 'columns' => $aColumns]);
    // var_dump($data[0]->value);
    // $au = AdminUser::where('username', '=', 'System')->lists('password')[0];
    // var_dump($au);
    // if (Hash::check('111111', $au->password)) {
    //     var_dump($au->secure_card_number);
    // }
    // var_dump(app_path() . '/lib/seamoonapi.php');
    // $u = SecureCard::find(212)->lists('id', 'number');
    // var_dump($u);
    // $user = new AdminUser;
    // $user->username = 'tetet';
    // $user->name = 'tetete';
    // $user->email = "phil@ipbrown.com";
    // $user->password = '111111';
    // $user->password_confirmation = '111111';
    // // $user->secure_card_number = 'ab12344';

    // $rules = [
    //     'username'  => 'required|between:4,32', // |unique:admin_users,username
    //     'name'      => 'required|between:0,50',
    //     'email'     => 'email|between:0, 200',
    //     'password'  => 'required|min:6|',
    //     'password_confirmation' => 'required|min:6',
    //     'language'  => 'between:0, 10',
    //     'menu_link' => 'in:0, 1',
    //     'menu_context' => 'in:0, 1',
    //     'actived'   => 'in:0, 1',
    //     'secure_card_number' => 'between:0, 10'
    // ];

    // $valid = $user->validate($rules);
    // pr($valid);exit;
    // if ($valid) {
        // $user->password = Hash::make($user->password);
        // $saved = $user->save($rules);
    // }
    // pr($saved);exit;
    // var_dump($user->validationErrors);
    // $ar = AdminUser::find(1);
    // $users = $ar->admin_roles()->get();
    // var_dump($users->toJson());
    //
    // $aur = AdminUserRole::firstOrNew(['role_id' => 1, 'user_id' => 1, 'rights' => '']);
    // $aur->save();
    // var_dump($aur->id);
    // $aur = AdminRole::find(2); //where('role_id', '=', '1')->get();
    // $ids = AdminUserRole::where('role_id', '=', $aur->id)->get(['user_id']);
    // $userIds = [];
    // foreach ($ids as $key => $value) {
    //     $userIds[] = $value->user_id;
    // }
    // $users = AdminUser::whereIn('id', $userIds)->get(['username']);
    // $users = AdminRole::find(2)->admin_users()->get(['admin_users.id', 'admin_users.username']);
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr('../' . base_path());exit;
    // $usernames = [];
    // foreach ($users as $key => $value) {
    //     $usernames[] = $value->username;
    // }
    // pr($usernames);exit;
    // $data = [];
    // foreach ($users as $key => $value) {
    //     $data[] = $value->username;
    // }
    // var_dump($data);
    // $role = AdminRole::where('id', '=', 1)->where('role_type', '=', 1)->first();
    // if ($role) $users = $role->admin_users()->get();
    // else $users = [];
    // $data = [];
    // foreach ($users as $key => $value) {
    //     $data[] = $value->username;
    // }
    // var_dump($data);
    // $audit = [
    //     'type_id' => 1,
    //     'user_id' => 1,
    //     'admin_id' => 34,
    //     'auditor_id' => 0,
    //     'user_name' => 'Agent',
    //     'admin_name' => 'snowan',
    //     'auditor_name' => '',
    //     'audit_data' => 'password=222222',
    // ];
    // $audit = ['id' => 1, 'status' => 1, 'auditor_id' => 1, 'auditor_name' => 'system'];
    // $succ = App::make('AuditListController')->updateAuditRecord($audit);
    // var_dump($succ);
    // var_dump('users.reset-password');
    // var_dump(Route::getRoutes()->getByName('error'));
    // pr(User::find(1)->id);exit;

    // $users = User::find(1)->children()->get(['id']);
    // pr($users);exit;
    // $arr = [];
    // foreach ($users as $user) {
    //     $arr[] = $user->id;
    // }
    // var_dump($arr);

    // $aSucc = [];
    // $model = User::find(1);
    // $model->getConnection()->beginTransaction();
    // $blocked = 1;
    // $rules = ['blocked' => 'in:0,1,2,3'];
    // $data = ['id' => 1, 'blocked' => $blocked];
    // $model = $model->fill($data);
    // $bSucc = $model->save($rules);
    // $aSucc[] = (int)$bSucc;
    // if (true) {
    //     $children = $model->children()->get(['id']);
    //     // pr($children[0]->toArray()['id']);exit;
    //     foreach ($children as $key => $user) {
    //         $aUserInfo = $user->toArray();
    //         $data = ['id' => $aUserInfo['id'], 'blocked' => $blocked];
    //         $user = $user->fill($data);
    //         $bSucc = $user->save($rules);
    //         $aSucc[] = (int)$bSucc;
    //     }
    // }
    // pr($aSucc);exit;

    // $bSucc = App::make('AuditListController')->audit(2);
    // var_dump($bSucc);
    // $funs = User::all(['id', 'username'])->toArray();
    // var_dump($funs);
    //
    // $query = AdminUserRole::where('role_id', '=', 1)->where('user_id', '=', 2);
    // $bSucc = (int)$query->get(['id'])->toArray();
    // var_dump($bSucc);
    //

    // $aCities = District::getCitiesByProvince();
    // var_dump(json_encode($aCities));
    //
    // $userBankCard = UserBankCard::where('account', '=', '4387671256786789')->first();
    // $user = User::where('id', '=', $userBankCard->user_id)->{'first'}();
    // pr($user->toArray());
    // pr('---------');
    // pr($userBankCard->toArray());
    // pr('-----------');
    // pr((boolean)3);
    //

    // $route = Route::getRoutes()->getByAction('AdInfoController@uploadImages')->getName();

    // $route = Route::getRoutes()->getByAction('RoleController@updateAdminUserBinding')->getName();
    // pr(route($route, 1));exit;
    //
    // $val = (int)preg_match('/^[0-9]+(.[0-9]{1,2})?$/', '2.333');
    // var_dump($val);
    //
    // $time = Carbon::now()->toDateTimeString();
    // var_dump($time);
    //
    // $accounts = UserBankCard::getUserAccounts(2);
    // var_dump($accounts['25'][0]['account']);
    //
    // $withdrawal = new Withdrawal;
    // var_dump($withdrawal->getAllListArray());
    // $withdrawal->setToWaitingForConfirmation(1);
    //
    // $user = User::where('username', 'LIKE', '%1%')->get()->toArray();
    // var_dump($user);

    // $u = new User;
    // $data = User::find(1);
    // // Cache::setDefaultDriver('memcached');
    // // $data = Cache::get('User_1');
    // // pr($u->$cacheLevel);
    // pr('User_1');
    // pr('--------');
    // pr($data);
    // exit;

    // $oRole      = Role::find(20);
    // $checked = $oRole->users()->paginate(5, ['users.id', 'users.username']);
    // $ids = array_map(function ($item) {
    //     return $item->id;
    // }, $checked);
    // $ids = [];
    // foreach ($checked as $key => $value) {
    //     $ids[] = $value->id;
    // }
    // pr($ids);exit;
    //
    // $aConditions = [ 'username' => ['like', '%User%'] ];
    // $data = Role::find(12)->users()->doWhere($aConditions)->paginate(5, ['id', 'username']);
    // pr($data->toArray());exit;
    //
    // pr(date('Y-m-d H:i:s', '1408096200'));exit;
    // $route_action = 'WithdrawalController@waitingForConfirmation';
    // $router = Route::getRoutes()->getByAction($route_action)->getName();
    // pr($router);exit;
    // pr(route('withdrawals.waiting', 1));
    //
    // $aUsers = User::getUsersBelongsToAgent(1);
    // pr($aUsers->toArray());exit;
    //
    // $aAgents = MsgUser::where('receiver', '=', 'Agent')->withTrashed()->get(['id', 'receiver', 'is_deleted']);
    // pr($aAgents->toArray());exit;
    //
    // $fr = FunctionalityRelation::find(652);
    // $model = User::find(1);
    // pr($model->toArray());
    // pr(Cache::get('User_1')->toArray());
    // $bSucc = $fr->isAvailable($model);
    // pr((int)$bSucc);
    // pr((string)$_SERVER['SERVER_NAME']);
    // pr(RegisterLink::getActiveLink(42)->toArray());
    // pr(get_proxy_ip());
    // $data = & Series::getLotteriesGroupBySeries(0, 1);
    // $data2 = UserPrizeSet::generateLotteriesPrizeWithSeries(299);
    // pr(($data2));
    // pr(($data2));
    //
    // $oUser = AdminUser::find(44); // 329 -- User54
    // $pwd = md5(md5(md5('testadmin3admin123')));
    // pr(Hash::check($pwd, $oUser->password));
    //
    // pr(Lang::get('_user.update-fund-password-fail', ['reason' => 'test']));
    // $oUser = User::find(1);
    // $data = $oUser->roles()->get();
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);exit;
    //
    // $oUpdator = new RegisterLinkPrizeGroupSetUpdator();
    // $result = $oUpdator->updatePrizeGroup();
    // pr($result);
    //
    // pr(Lottery::getAllLotteries());
    //
    pr((4 && 4 == 5 && in_array(1, [1,12])) || (in_array(1, [0,1]) && !4));
});
Route::get('drivers', function (){
    // var_dump(Cache::getDrivers());
    // Cache::setDefaultDriver('memcached');
    var_dump(Cache::getDefaultDriver());
    $data = Cache::get('user_3');
    var_dump($data);
});
Route::get('cache/{type?}/{id?}', function($type = 1, $id = 1){
    // return json_encode(Route::getRoutes());
    // Session::put('CurUserRole', implode(',', [1, 2]));
    // $roleIds = explode(',', Session::get('CurUserRole'));
    // var_dump($roleIds);
    // var_dump( Route::getRoutes() );

    // $fun_ids = Functionality::where('action', '=', 'index')->get(['controller', 'id']);
    // $ids = [];
    // foreach ($fun_ids as $key => $value) {
    //     ($ids[ $value->controller ] = $value->id);
    // }
    // var_dump($ids['Admin_UserResource']);
    // $ac = 'Admin_AdminUserResource@index';
    // $router = Route::getRoutes()->getByAction($ac);
    // $route_name = $router->getName();
    // echo $route_name;
    // $fm = new FunctionalityModel;
    // var_dump($fm->getAllFunctionalities());

    // $u = User::find(1);
    // $u->id = 11;
    // $u->username = 'Agent_1';
    // $u->password = 'password';
    // $u->passwrod_confirmation = 'password';
    // var_dump($u->save());

    // $u = new User;
    // $data = [
    //     'username' => 'TestUser',
    //     'password' => 'password'
    // ];
    // // passwrod_confirmation = 'password';
    // var_dump(DB::table('users')->insertGetId($data));
    //
    // $id = 1;
    // $file = null;
    // $memcached = null;
    // $mongo = null;

    // $driver = null;
    // var_dump(Cache::getDefaultDriver() .'---'. $type . ',' . $id);
    // switch ((int)$type) {
    //     case 1:
    //         Cache::setDefaultDriver('memcached');
    //         break;
    //     case 2:
    //         Cache::setDefaultDriver('mongo');
    //         break;
    // }
    // var_dump(Cache::getDefaultDriver());
    // Cache::remember('user_' . $id, 60, function () use ($id) {
    //     return AdminUser::find($id);
    // });
    // Cache::remember('user_' . $id);
    // $data = Cache::get('user_' . $id);
    // $au = new AdminUser;
    // $data = $au->getObject($id);
    $data = AdminUser::getObject($id);
    // $data = $au->setObject($id, 2);
    // $fun = new Functionality;
    // $data = $fun->getAllFunctionalities();
    var_dump($data);
    // $data1 = is_null($mongo) or $mongo->get('user_1');
    // $data2 = is_null($memcached) or $memcached->get('user_1');
    // $data3 = is_null($file) or $file->get('user_1');
    // var_dump($data1);
    // echo '------------------';
    // var_dump($data2);
    // echo '------------------';
    // var_dump($data3);

    // $file = Cache::driver('file');
    // $memcached = Cache::driver('memcached');
    // $mongo = Cache::driver('mongo');
    // $data = Cache::remember('user_' . $id, 60, function () use ($id) {
    //     return User::find($id);
    // });
    // var_dump($data);
    // $data = $file->get('user_2');
    // $data2 = $memcached->get('user_2');
    // $data3 = $mongo->get('user_2');
    // var_dump($data);
    // var_dump(compact('data3', 'data2'));

    // return Request::method();
    // return Route::uri();
    // Session::put('curPage', 'http://test.firecat.com/test');
    // return Redirect::to(Session::get('curPage'));
});
// Route::get('ulist/{id?}', function(){
//     // return View::make('admin.user.userList');
//     // return '<a href="' . order_by('title') . '">order</a>';
// });

//---------------for test------------------