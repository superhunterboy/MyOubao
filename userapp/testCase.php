<?php
//---------------for test------------------

Route::get('test-cb',function(){

    $oIssue = CountPrize::where('lottery_id', '=', 27)->where('issue','=','1601120059')->first();

    $startTime = time();

    $aWinCode = ['333','123','456','111','322','112','332','113','222','331'];

    foreach($aWinCode as $WinCode){
        $aBetPrize = $oIssue->getBetAndPrize($WinCode);
    }

    echo time() - $startTime;exit;


    $aWithdrawInfo = Withdrawal::find(1)->toArray();
    $SDWithdraw = new SDWithdraw;
    $SDWithdraw->api($aWithdrawInfo);

    exit;

  $profit = Project::getCurrentDayTurnover(
                446,
                "2015-06-24 10:57:01",
                Carbon::now()
            );

  echo 'Profit :: ' . $profit;
  echo '<br />'; 
  exit;

    try
    {
      $tasks = ActivityTask::findAllByEvent("bomao.activity.stage");

      foreach ($tasks as $task)
      {
         $users = [
             '446',
         ];
         
         $randomUser = rand(0,0);

         if (!$task->complete($users[$randomUser]))
         {
             pr($task->errors()->getMessages());
         }
      }
    }
    catch(Exception $e)
    {
      echo 'asdas';
      echo $e->getMessage();
    }
     
     echo ' ######################################### Outside The Route File ######################################### ';
     echo '<br />';
     exit;

});


Route::get('captcha-test', function () {
    // return Captcha::img();
    $content = Form::open(array(URL::to(Request::segment(1))));
    $content .= '<p>' . HTML::image(Captcha::img(), 'Captcha image') . '</p>';
    // $content .= Captcha::url();
    $content .= '<p>' . Form::text('captcha') . '</p>';
    $content .= '<p>' . Form::submit('Check') . '</p>';
    $content .= '<p>' . Form::close() . '</p>';
    return $content;
});

Route::get('generate-selector', function() {
    $generator = new SelectTemplateGenerator;
    $result = $generator->generate();
    var_dump(json_encode($result));
});

Route::get('test-event-source', function(){

    $response = new Symfony\Component\HttpFoundation\StreamedResponse(function() {
        $iOldNum = 0;
        while (true) {
            $iNewNum = UserMessage::getUserUnreadMessagesNum();
            // Log::info('test-event-source: ' . $iNewNum);
            if ($iNewNum != $iOldNum) {
                echo '{data: ' . ($iNewNum) . '}\n\n';
                ob_flush();
                flush();
            }
            sleep(30);
        }

        $iOldNum = $iNewNum;
    });
    $response->headers->set('Content-Type', 'text/event-stream');
    return $response;
    // $iNewNum = App::make('StationLetterController')->getUserUnreadNum();

});

Route::get('lmongo', function () {
    $id = LMongo::collection('users')->insert(['email' => 'testmongo@test.com', 'username' => 'testmongo']);
    pr($id);
    $user = LMongo::collection('users')->where('username', 'testmongo')->first();
    pr($user);
});

// Route::get('admin/controller/newviews', array('as' => 'test', 'uses' => 'AdminController@index'));
Route::get('test-user', function () {
    $ip = get_client_ip();
    // $ip = ip2long('124.104.223.47');
    pr($ip);
    $bBlocked = BlockedIp::checkInBlockedIps($ip);
    pr($bBlocked);
    exit;
    // $aIps = BlockedIp::all();
    // foreach ($aIps as $key => $value) {
    //     $value->ip_start_long = ip2long($value->ip_start);
    //     $value->ip_end_long   = ip2long($value->ip_end);
    //     $value->save();
    // }

    pr(Carbon::parse('first day of this month')->addDays(0));
    // pr(Carbon::now());
    exit;

    // $sPrizeGroup = '5dd2aa45bd697d1f0612591c4fce680a'; // '657d3f543bf922cedca68b90dec76477'; // '5dd2aa45bd697d1f0612591c4fce680a';
    // $oPrizeGroup = UserCreateUserLink::getRegisterUserPrizeGroup($sPrizeGroup);
    // // pr($oPrizeGroup);exit;
    // $aPrizeGroup = json_decode($oPrizeGroup->prize_group_sets);
    // // pr($aPrizeGroup[0]);
    // // pr(isset($aPrizeGroup[0]->series_id));exit;

    // // $data = Series::getLotteriesGroupBySeries();
    // if (isset($aPrizeGroup[0]->lottery_id)) {
    //     $aLotteryPrizeGroups = $aPrizeGroup;
    // } else {
    //     $aSeriesLotteries = Series::getLotteriesGroupBySeries();
    //     $data = [];
    //     $aLotteryPrizeGroups = [];
    //     foreach ($aSeriesLotteries as $key => $value) {
    //         $data[$value->id] = $value->children;
    //     }
    //     // pr($data);exit;
    //     foreach ($aPrizeGroup as $key => $value) {
    //         if (isset($data[$value->series_id])) {
    //             foreach ($data[$value->series_id] as $key2 => $oLottery) {
    //                 // pr($key2);
    //                 // pr($oLottery);
    //                 // exit;
    //                 $aLotteryPrizeGroups[] = arrayToObject(['lottery_id' => $oLottery['id'], 'prize_group' => $value->prize_group]);
    //             }
    //         }
    //     }
    // }

    // $data[] = array_map(function ($item) {
    //     return [$item['id'] => $item['children']];
    // }, $aSeriesLotteries->toArray());
    // pr($aLotteryPrizeGroups);exit;
    // $routeRoot = Config::get('route.root');
    // var_dump($routeRoot);
    //
    // $withdrawal = UserWithdrawal::first();
    // var_dump($withdrawal->toArray());
    //
    // $sAccountRule = UserBankCard::$rules['account'];
    // $aRules = array_merge(UserBankCard::$rules, ['account_confirmation' => $sAccountRule]);
    // $aRules['account'] .= '|confirmed';
    // var_dump($aRules);
    //
    // $uInfo = UserBankCard::rember(89);
    // Cache::setDefaultDriver('memcached');
    // $uInfo = Cache::get('UserBankCard_89');
    // var_dump($uInfo);

    // $ucard = new UserUserBankCard;
    // $status = $ucard->getUserCardsLockStatus(2);
    // var_dump((int)$status);
    //
    // $provinces = District::getCitiesByProvince();
    // var_dump(json_encode($provinces));
    //
    // var_dump(app_path());
    //
    // $aDistricts = District::getCitiesByProvince();
    // $sRootPath = app_path() . '/../widgets/';
    // $sPath = $sRootPath . 'data/';
    // $sDataName = 'province_city';
    // $sFile = $sDataName . '.json';
    // $sDisplayName = $sFile;
    // $sFileName = $sPath . $sFile;
    // if (file_exists($sFileName)){
    //     if (!is_writable($sFileName)){
    //         return ['successful' => false, 'message' => __('_basic.file-not-writable', ['file' => $sDisplayName])];
    //     }
    // }
    // else{
    //     if (!is_writeable($sPath)){
    //         return ['successful' => false, 'message' => __('_basic.file-write-fail-path', ['path' => $sPath])];
    //     }
    // }
    // if (!$bSucc = @file_put_contents($sFileName, "var provinceCities =  " . json_encode($aDistricts) . '')){
    //     break;
    // }
    // $sLangKey = '_basic.' . ($bSucc ? 'generated' : 'file-write-fail');
    // $aReturn = [
    //     'successful' => $bSucc,
    //     'message' => __($sLangKey, ['file' => $sDisplayName])
    // ];
    // return $aReturn;
    //
    // return View::make('widgets.province_city');
    //
    // var_dump(app_path() . '/../widgets/data/province_city.json');
    //
    // $cities = District::getCitiesByProvince();
    // var_dump(json_encode($cities));
    //
    // return View::make('widgets.banks')->with('sSelectName', 'id');
    //
    // $iNum = UserWithdrawal::where('user_id', '=', '2')->where('request_time', 'like', date('Y-m-d') . '%')->count();
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
    // var_dump($iNum);
    //
    // $year_code = array('A','B','C','D','E','F','G','H','I','J');
    // $order_sn = $year_code[intval(date('Y'))-2014].
    // strtoupper(dechex(date('m'))).date('d').
    // substr(time(),-5).substr(microtime(),2,5).sprintf('%02d',rand(0,99));
    // var_dump($order_sn);
    //
    // var_dump(App::environment());
    //
    // $aUsers = User::getUsersBelongsToAgent(1);
    // var_dump(json_encode($aUsers->toArray()));
    //
    // $iNum = User::find(1)->children()->count();
    // var_dump($iNum);
    //
    // var_dump((int)Hash::check('User123', User::find(2)->password));
    //
    // $user = User::find(2);
    // $formData = [
    //     'fund_password' => 'User123',
    //     'fund_password_confirmation' => 'User123',
    // ];
    // $bSucc = $user->resetFundPassword($formData);
    // var_dump($user->validationErrors);
    //
    // $manager = new Illuminate\Auth\Reminders\DatabaseReminderRepository;

    // 获取pdo对象的mysql线程id
    // $link = DB::connection('mysql')->getPdo();
    // $thread_id = $link->query('SELECT CONNECTION_ID() as cid')->fetch(PDO::FETCH_ASSOC)['cid'];
    // var_dump($thread_id);
    //
    // $oLottery = Lottery::find(1);
    // $data = User::getWaySettings(3, $oLottery);
    // $count = [];
    // foreach ($data as $aWayGroup) {
    //     $iCount = 0;
    //     foreach ($aWayGroup['children'] as $aWay) {
    //         $count[$aWay['id']] = count($aWay['children']);
    //         $iCount += $count[$aWay['id']];
    //     }
    //     $count[$aWayGroup['id']] = $iCount;
    // }

    // pr(($data));
    // pr($count);
    // exit;

    // $aUsers = User::getUsersBelongsToAgent(1);
    // // pr($aUsers->toArray());exit;
    // $aUsers = array_map(function ($item) {
    //     return $item['id'];
    // }, $aUsers->toArray());
    // pr($aUsers);exit;
    //
    // $aUsers = User::forPage(1, 5)->get(['username']);
    // pr($aUsers->toArray());exit;
    //
    // pr(implode(',', ['a' => 1, 'b' => 2]));
    // $oUser = User::find(1);
    // $aUserIds = User::getAllUsersBelongsToAgent(1);
    // $aAccounts = Account::getAccountInfoByUserId($aUserIds);
    // pr($aAccounts->toArray());exit;
    //
    // pr((int)![] . (int)!'');
    //
    // $aLotteries = Series::find(1)->lotteries()->get();
    // pr($aLotteries->toArray());exit;
    //
    // $data = Series::getLotteriesGroupBySeries();
    // pr($data->toArray());exit;
    //
    // $aProjects = UserProject::getLatestRecords();
    // $aTraces   = UserTrace::getLatestRecords();
    // $aTransactions = UserTransaction::getLatestRecords();
    // pr($aProjects->toArray());
    // pr('---------');
    // pr($aTraces->toArray());
    // pr('---------');
    // pr($aTransactions->toArray());
    // pr('---------');
    // pr(Session::get('user_id'));
    // exit;
    //
    // $testData = json_decode('[{"id":"2","pid":0,"name_cn":"\u4e94\u661f","name_en":"wuxing","children":[{"id":"4","pid":"2","name_cn":"\u76f4\u9009","name_en":"zhixuan","children":[{"id":"68","pid":"4","name_cn":"\u76f4\u9009\u590d\u5f0f","name_en":"fushi","price":"2","prize":"171000.00"},{"id":"7","pid":"4","name_cn":"\u76f4\u9009\u5355\u5f0f","name_en":"danshi","price":"2","prize":"171000.00"},{"id":"15","pid":"4","name_cn":"\u76f4\u9009\u7ec4\u5408","name_en":"zuhe","price":"2","prize":"17100.00,171000.00,1710.00,171.00,17.10"}]},{"id":"5","pid":"2","name_cn":"\u7ec4\u9009","name_en":"zuxuan","children":[{"id":"32","pid":"5","name_cn":"\u7ec4\u9009120","name_en":"zuxuan120","price":"2","prize":"1425.00"},{"id":"31","pid":"5","name_cn":"\u7ec4\u900960","name_en":"zuxuan60","price":"2","prize":"2850.00"},{"id":"30","pid":"5","name_cn":"\u7ec4\u900930","name_en":"zuxuan30","price":"2","prize":"5700.00"},{"id":"29","pid":"5","name_cn":"\u7ec4\u900920","name_en":"zuxuan20","price":"2","prize":"8550.00"},{"id":"28","pid":"5","name_cn":"\u7ec4\u900910","name_en":"zuxuan10","price":"2","prize":"17100.00"},{"id":"27","pid":"5","name_cn":"\u7ec4\u90095","name_en":"zuxuan5","price":"2","prize":"34200.00"}]}]},{"id":"3","pid":0,"name_cn":"\u56db\u661f","name_en":"sixing","children":[{"id":"6","pid":"3","name_cn":"\u76f4\u9009","name_en":"zhixuan","children":[{"id":"67","pid":"6","name_cn":"\u76f4\u9009\u590d\u5f0f","name_en":"fushi","price":"2","prize":"17100.00"},{"id":"6","pid":"6","name_cn":"\u76f4\u9009\u5355\u5f0f","name_en":"danshi","price":"2","prize":"17100.00"},{"id":"79","pid":"6","name_cn":"\u76f4\u9009\u7ec4\u5408","name_en":"zuhe","price":"2","prize":"17100.00,1710.00,171.00,17.10"}]},{"id":"7","pid":"3","name_cn":"\u7ec4\u9009","name_en":"zuxuan","children":[{"id":"26","pid":"7","name_cn":"\u7ec4\u900924","name_en":"zuxuan24","price":"2","prize":"712.50"},{"id":"25","pid":"7","name_cn":"\u7ec4\u900912","name_en":"zuxuan12","price":"2","prize":"1425.00"},{"id":"24","pid":"7","name_cn":"\u7ec4\u90096","name_en":"zuxuan6","price":"2","prize":"2850.00"},{"id":"23","pid":"7","name_cn":"\u7ec4\u90094","name_en":"zuxuan4","price":"2","prize":"4275.00"}]}]},{"id":"8","pid":0,"name_cn":"\u524d\u4e09","name_en":"qiansan","children":[{"id":"10","pid":"8","name_cn":"\u76f4\u9009","name_en":"zhixuan","children":[{"id":"65","pid":"10","name_cn":"\u76f4\u9009\u590d\u5f0f","name_en":"fushi","price":"2","prize":"1710.00"},{"id":"1","pid":"10","name_cn":"\u76f4\u9009\u5355\u5f0f","name_en":"danshi","price":"2","prize":"1710.00"},{"id":"71","pid":"10","name_cn":"\u76f4\u9009\u548c\u503c","name_en":"hezhi","price":"2","prize":"1710.00"},{"id":"60","pid":"10","name_cn":"\u76f4\u9009\u8de8\u5ea6","name_en":"kuadu","price":"2","prize":"1710.00"},{"id":"14","pid":"10","name_cn":"\u76f4\u9009\u7ec4\u5408","name_en":"zuhe","price":"2","prize":"1710.00,17.10,171.00"}]},{"id":"11","pid":"8","name_cn":"\u7ec4\u9009","name_en":"zuxuan","children":[{"id":"16","pid":"11","name_cn":"\u7ec4\u4e09","name_en":"zusan","price":"2","prize":"570.00"},{"id":"17","pid":"11","name_cn":"\u7ec4\u516d","name_en":"zuliu","price":"2","prize":"285.00"},{"id":"13","pid":"11","name_cn":"\u6df7\u5408\u7ec4\u9009","name_en":"hunhezuxuan","price":"2","prize":"570.00,285.00"},{"id":"75","pid":"11","name_cn":"\u7ec4\u9009\u548c\u503c","name_en":"hezhi","price":"2","prize":"570.00,285.00"},{"id":"2","pid":"11","name_cn":"\u7ec4\u4e09\u5355\u5f0f","name_en":"zusandanshi","price":"2","prize":"570.00"},{"id":"3","pid":"11","name_cn":"\u7ec4\u516d\u5355\u5f0f","name_en":"zuliudanshi","price":"2","prize":"285.00"},{"id":"64","pid":"11","name_cn":"\u5305\u80c6","name_en":"baodan","price":"2","prize":"570.00,285.00"}]},{"id":"12","pid":"8","name_cn":"\u5176\u5b83","name_en":"qita","children":[{"id":"33","pid":"12","name_cn":"\u548c\u503c\u5c3e\u6570","name_en":"hezhiweishu","price":"2","prize":"17.10"},{"id":"48","pid":"12","name_cn":"\u7279\u6b8a\u53f7\u7801","name_en":"teshuhaoma","price":"2","prize":"171.00,28.50,6.34"}]}]},{"id":"1","pid":0,"name_cn":"\u540e\u4e09","name_en":"housan","children":[{"id":"13","pid":"1","name_cn":"\u76f4\u9009","name_en":"zhixuan","children":[{"id":"69","pid":"13","name_cn":"\u76f4\u9009\u590d\u5f0f","name_en":"fushi","price":"2","prize":"1710.00"},{"id":"8","pid":"13","name_cn":"\u76f4\u9009\u5355\u5f0f","name_en":"danshi","price":"2","prize":"1710.00"},{"id":"73","pid":"13","name_cn":"\u76f4\u9009\u548c\u503c","name_en":"hezhi","price":"2","prize":"1710.00"},{"id":"62","pid":"13","name_cn":"\u76f4\u9009\u8de8\u5ea6","name_en":"kuadu","price":"2","prize":"1710.00"},{"id":"82","pid":"13","name_cn":"\u76f4\u9009\u7ec4\u5408","name_en":"zuhe","price":"2","prize":"1710.00,171.00,17.10"}]},{"id":"9","pid":"1","name_cn":"\u7ec4\u9009","name_en":"zuxuan","children":[{"id":"49","pid":"9","name_cn":"\u7ec4\u4e09","name_en":"zusan","price":"2","prize":"570.00"},{"id":"50","pid":"9","name_cn":"\u7ec4\u516d","name_en":"zuliu","price":"2","prize":"285.00"},{"id":"81","pid":"9","name_cn":"\u6df7\u5408\u7ec4\u9009","name_en":"hunhezuxuan","price":"2","prize":"570.00,285.00"},{"id":"80","pid":"9","name_cn":"\u7ec4\u9009\u548c\u503c","name_en":"hezhi","price":"2","prize":"570.00,285.00"},{"id":"9","pid":"9","name_cn":"\u7ec4\u4e09\u5355\u5f0f","name_en":"zusandanshi","price":"2","prize":"570.00"},{"id":"10","pid":"9","name_cn":"\u7ec4\u516d\u5355\u5f0f","name_en":"zuliudanshi","price":"2","prize":"285.00"},{"id":"83","pid":"9","name_cn":"\u5305\u80c6","name_en":"baodan","price":"2","prize":"570.00,285.00"}]},{"id":"14","pid":"1","name_cn":"\u5176\u5b83","name_en":"qita","children":[{"id":"54","pid":"14","name_cn":"\u548c\u503c\u5c3e\u6570","name_en":"hezhiweishu","price":"2","prize":"17.10"},{"id":"57","pid":"14","name_cn":"\u7279\u6b8a\u53f7\u7801","name_en":"teshuhaoma","price":"2","prize":"171.00,28.50,6.34"}]}]},{"id":"15","pid":0,"name_cn":"\u4e8c\u661f","name_en":"erxing","children":[{"id":"16","pid":"15","name_cn":"\u76f4\u9009","name_en":"zhixuan","children":[{"id":"70","pid":"16","name_cn":"\u540e\u4e8c\u590d\u5f0f","name_en":"houerfushi","price":"2","prize":"171.00"},{"id":"11","pid":"16","name_cn":"\u540e\u4e8c\u5355\u5f0f","name_en":"houerdanshi","price":"2","prize":"171.00"},{"id":"74","pid":"16","name_cn":"\u540e\u4e8c\u548c\u503c","name_en":"houerhezhi","price":"2","prize":"171.00"},{"id":"63","pid":"16","name_cn":"\u540e\u4e8c\u8de8\u5ea6","name_en":"houerkuadu","price":"2","prize":"171.00"},{"id":"66","pid":"16","name_cn":"\u524d\u4e8c\u590d\u5f0f","name_en":"qianerfushi","price":"2","prize":"171.00"},{"id":"4","pid":"16","name_cn":"\u524d\u4e8c\u5355\u5f0f","name_en":"qianerdanshi","price":"2","prize":"171.00"},{"id":"72","pid":"16","name_cn":"\u524d\u4e8c\u548c\u503c","name_en":"qianerhezhi","price":"2","prize":"171.00"},{"id":"61","pid":"16","name_cn":"\u524d\u4e8c\u8de8\u5ea6","name_en":"qianerkuadu","price":"2","prize":"171.00"}]},{"id":"17","pid":"15","name_cn":"\u7ec4\u9009","name_en":"zuxuan","children":[{"id":"59","pid":"17","name_cn":"\u540e\u4e8c\u590d\u5f0f","name_en":"houerfushi","price":"2","prize":"85.50"},{"id":"12","pid":"17","name_cn":"\u540e\u4e8c\u5355\u5f0f","name_en":"houerdanshi","price":"2","prize":"85.50"},{"id":"77","pid":"17","name_cn":"\u540e\u4e8c\u548c\u503c","name_en":"houerhezhi","price":"2","prize":"85.50"},{"id":"85","pid":"17","name_cn":"\u540e\u4e8c\u5305\u80c6","name_en":"houerbaodan","price":"2","prize":"85.50"},{"id":"20","pid":"17","name_cn":"\u524d\u4e8c\u590d\u5f0f","name_en":"qianerfushi","price":"2","prize":"85.50"},{"id":"5","pid":"17","name_cn":"\u524d\u4e8c\u5355\u5f0f","name_en":"qianerdanshi","price":"2","prize":"85.50"},{"id":"76","pid":"17","name_cn":"\u524d\u4e8c\u548c\u503c","name_en":"qianerhezhi","price":"2","prize":"85.50"},{"id":"84","pid":"17","name_cn":"\u524d\u4e8c\u5305\u80c6","name_en":"qianerbaodan","price":"2","prize":"85.50"}]}]},{"id":"18","pid":0,"name_cn":"\u4e00\u661f","name_en":"yixing","children":[{"id":"19","pid":"18","name_cn":"\u5b9a\u4f4d\u80c6","name_en":"dingweidan","children":[{"id":"78","pid":"19","name_cn":"\u5b9a\u4f4d\u80c6","name_en":"fushi","price":"2","prize":"17.10,17.10,17.10,17.10,17.10"}]}]},{"id":"20","pid":0,"name_cn":"\u4e0d\u5b9a\u4f4d","name_en":"budingwei","children":[{"id":"21","pid":"20","name_cn":"\u4e09\u661f\u4e0d\u5b9a\u4f4d","name_en":"sanxingbudingwei","children":[{"id":"51","pid":"21","name_cn":"\u540e\u4e09\u4e00\u7801\u4e0d\u5b9a\u4f4d","name_en":"housanyimabudingwei","price":"2","prize":"6.31"},{"id":"52","pid":"21","name_cn":"\u540e\u4e09\u4e8c\u7801\u4e0d\u5b9a\u4f4d","name_en":"housanermabudingwei","price":"2","prize":"31.67"},{"id":"18","pid":"21","name_cn":"\u524d\u4e09\u4e00\u7801\u4e0d\u5b9a\u4f4d","name_en":"qiansanyimabudingwei","price":"2","prize":"6.31"},{"id":"21","pid":"21","name_cn":"\u524d\u4e09\u4e8c\u7801\u4e0d\u5b9a\u4f4d","name_en":"qiansanermabudingwei","price":"2","prize":"31.67"}]},{"id":"22","pid":"20","name_cn":"\u56db\u661f\u4e0d\u5b9a\u4f4d","name_en":"sixingbudingwei","children":[{"id":"34","pid":"22","name_cn":"\u56db\u661f\u4e00\u7801\u4e0d\u5b9a\u4f4d","name_en":"sixingyimabudingwei","price":"2","prize":"4.98"},{"id":"35","pid":"22","name_cn":"\u56db\u661f\u4e8c\u7801\u4e0d\u5b9a\u4f4d","name_en":"sixingermabudingwei","price":"2","prize":"17.55"}]},{"id":"23","pid":"20","name_cn":"\u4e94\u661f\u4e0d\u5b9a\u4f4d","name_en":"wuxingbudingwei","children":[{"id":"36","pid":"23","name_cn":"\u4e94\u661f\u4e8c\u7801\u4e0d\u5b9a\u4f4d","name_en":"wuxingermabudingwei","price":"2","prize":"11.65"},{"id":"37","pid":"23","name_cn":"\u4e94\u661f\u4e09\u7801\u4e0d\u5b9a\u4f4d","name_en":"wuxingsanmabudingwei","price":"2","prize":"39.31"}]}]},{"id":"24","pid":0,"name_cn":"\u5927\u5c0f\u5355\u53cc","name_en":"daxiaodanshuang","children":[{"id":"25","pid":"24","name_cn":"\u5927\u5c0f\u5355\u53cc","name_en":"daxiaodanshuang","children":[{"id":"58","pid":"25","name_cn":"\u540e\u4e8c\u5927\u5c0f\u5355\u53cc","name_en":"houerdaxiaodanshuang","price":"2","prize":"6.84"},{"id":"53","pid":"25","name_cn":"\u540e\u4e09\u5927\u5c0f\u5355\u53cc","name_en":"housandaxiaodanshuang","price":"2","prize":"13.68"},{"id":"19","pid":"25","name_cn":"\u524d\u4e8c\u5927\u5c0f\u5355\u53cc","name_en":"qianerdaxiaodanshuang","price":"2","prize":"6.84"},{"id":"22","pid":"25","name_cn":"\u524d\u4e09\u5927\u5c0f\u5355\u53cc","name_en":"qiansandaxiaodanshuang","price":"2","prize":"13.68"}]}]},{"id":"26","pid":0,"name_cn":"\u8da3\u5473","name_en":"quwei","children":[{"id":"27","pid":"26","name_cn":"\u8da3\u5473","name_en":"quwei","children":[{"id":"38","pid":"27","name_cn":"\u4e94\u7801\u8da3\u5473\u4e09\u661f","name_en":"wumaquweisanxing","price":"2","prize":"6840.00"},{"id":"39","pid":"27","name_cn":"\u56db\u7801\u8da3\u5473\u4e09\u661f","name_en":"simaquweisanxing","price":"2","prize":"3420.00"},{"id":"55","pid":"27","name_cn":"\u540e\u4e09\u8da3\u5473\u4e8c\u661f","name_en":"housanquweierxing","price":"2","prize":"342.00"},{"id":"40","pid":"27","name_cn":"\u524d\u4e09\u8da3\u5473\u4e8c\u661f","name_en":"qiansanquweierxing","price":"2","prize":"342.00"}]},{"id":"28","pid":"26","name_cn":"\u533a\u95f4","name_en":"qujian","children":[{"id":"41","pid":"28","name_cn":"\u4e94\u7801\u533a\u95f4\u4e09\u661f","name_en":"wumaqujiansanxing","price":"2","prize":"42750.00"},{"id":"42","pid":"28","name_cn":"\u56db\u7801\u533a\u95f4\u4e09\u661f","name_en":"simaqujiansanxing","price":"2","prize":"8550.00"},{"id":"56","pid":"28","name_cn":"\u540e\u4e09\u533a\u95f4\u4e8c\u661f","name_en":"housanqujianerxing","price":"2","prize":"855.00"},{"id":"43","pid":"28","name_cn":"\u524d\u4e09\u533a\u95f4\u4e8c\u661f","name_en":"qiansanqujianerxing","price":"2","prize":"855.00"}]},{"id":"29","pid":"26","name_cn":"\u7279\u6b8a","name_en":"teshu","children":[{"id":"44","pid":"29","name_cn":"\u4e00\u5e06\u98ce\u987a","name_en":"yifanfengshun","price":"2","prize":"4.17"},{"id":"45","pid":"29","name_cn":"\u597d\u4e8b\u6210\u53cc","name_en":"haoshichengshuang","price":"2","prize":"20.99"},{"id":"46","pid":"29","name_cn":"\u4e09\u661f\u62a5\u559c","name_en":"sanxingbaoxi","price":"2","prize":"199.76"},{"id":"47","pid":"29","name_cn":"\u56db\u5b63\u53d1\u8d22","name_en":"sijifacai","price":"2","prize":"3717.39"}]}]}]');
    // // pr($testData);exit;
    // $aCounts = [];
    // foreach ($testData as $aWayGroup) {
    //     $iCount = 0;
    //     foreach ($aWayGroup->children as $aWay) {
    //         $iICount = 0;
    //         foreach ($aWay->children as $aMethod) {
    //             if ($aMethod->id == 78) {
    //                 $aMethod->prize = explode(',', $aMethod->prize)[0];
    //                 // $item = $aMethod['prize'];
    //                 // pr($item);exit;
    //             }
    //             // $item = $aMethod->prize;
    //             $aCounts['method_' . $aMethod->id] = count(explode(',', $aMethod->prize));
    //             $aMethod->count = count(explode(',', $aMethod->prize));
    //             $iICount += $aCounts['method_' . $aMethod->id];
    //             $iCount += $aCounts['method_' . $aMethod->id];
    //         }
    //         $aCounts['way_' . $aWay->id] = $iICount;
    //         $aWay->count = $iICount;
    //     }
    //     $aCounts['waygroup_' . $aWayGroup->id] = $iCount;
    //     $aWayGroup->count = $iCount;
    // }
    // pr(json_encode($testData));exit;
    //
    // $banks = Bank::getAllBankNameArray();
    // pr($banks);exit;
    //
    // $hashed = Hash::make('Userfund123');
    // pr($hashed);
    // $user = User::find(2);
    // pr($user->fund_password);
    // $bValidated = Hash::check('Userfund123', $user->fund_password);
    // pr((int)$bValidated);exit;

    // pr((int)(123.43 == number_format(123.43, 2)));
    // echo (Carbon::today()->addDays(7));exit;
    //
    // pr(Series::getLotteriesGroupBySeries()->toArray());exit;
    //
    // $iUserId                  = Session::get('user_id');
    // $aLotteriesPrizeSets      = UserPrizeSet::getUserLotteriesPrizeSets($iUserId);
    // pr($aLotteriesPrizeSets->toArray());exit;
    //
    // $data = UserPrizeSet::generateLotteriesPrizeWithSeries();
    // pr($data);exit;
    //
    // pr(min(6, max(7, 3)));
    //
    // $data = UserTrend::getIssuesByParams(1, '1407600300', '1407811200');
    // pr($data);exit;
    //
    // echo route('user-trends.index', [1, '1407600300', '1407811200']);
    //
    // pr(Cache::get('LOGIN_TIMES'));exit;
    //
    // $x = 9.7;
    // $y = [];
    // // 四舍五入：
    // $y[] = (int)($x +0.5);

    // // 五舍六入：
    // $y[] = (int)($x +0.4);
    // // 六舍七入：
    // $y[] = (int)($x +0.3);
    // // 七舍八入：
    // $y[] = (int)($x +0.2);
    // pr($y);exit;
    //
    // echo Carbon::now();
    //
    // $aBalls = [4,5,7];
    // $pArray = [1, 2, 3, 5, 7];
    // array_walk($aBalls, function ($item, $pArray) {
    //     pr($pArray);exit;
    //     return (int)(in_array($item, $pArray));
    // }, $pArray);
    // pr($aBalls);
    //
    // pr(PrizeGroup::getPrizeGroupsBelowExistGroup(1955,1)->toArray());
    //
    // $oAccount = new Account;
    // $param = [
    //     'user_id'      => 157,
    //     'username'     => 'User31',
    //     'withdrawable' => 0,
    //     'status'       => 1
    // ];
    // $oAccount->fill($param);
    // $bSucc = $oAccount->save();
    // pr(intval($bSucc));exit;
    //
    // $data = Series::getLotteriesGroupBySeries(0,1);
    // pr($data);
    // pr(Request::getClientIp(true));
    // return App::abort(403);
    // return App::abort(404);
    // return App::abort(500);
    // $data = Lottery::getAllLotteries();
    // pr($data);
    // $oUser = User::find(1);
    // $oUser->password = 'User123';
    // $oUser->password_confirmation = 'User123';
    // $data = User::generatePassword($oUser);
    // pr($data);
    //
    // $preg = '/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z]+?)\1\1).{6,16}$/';
    // $preg = '/([0-9a-zA-Z])*([0-9a-zA-Z])\2{2}([0-9a-zA-Z])*{6,16}$/';
    // $str = [
    //     'abcdef',
    //     '123456',
    //     '___1',
    //     'aaaa',
    //     '111abc',
    //     'aa11bbb',
    //     '12ab222',
    //     '121a2344345',
    // ];
    // $rules = ['pwd' => 'custom_password'];
    // $data = [];
    // foreach ($str as $key => $item) {
    //     $validator = Validator::make(['pwd' => $item], $rules);
    //     $data[] = $validator->passes();
    //     // $data[] = preg_match($preg, $item);
    // }
    // return $data;
    //
    // pr(User::checkUsernameExist('User_1'));
    //
    // 73a2c415bb0591965d755d22679d0a71
    // $oUser = User::find(314);
    // $pwd = md5(md5(md5('User51User123')));
    // pr($pwd);
    // pr(Hash::check($pwd, $oUser->password));
    //
    // $data1 = Series::getLotteriesGroupBySeries();
    // $data2 = Series::getLotteriesGroupBySeries(null, false);
    // pr($data1);
    // pr($data2);
    // exit;
    // $str = '40dd87751e2391a9df1c4542346d13b8';
    // $oUser = User::find(2); // 329 -- User54
    // $pwd = md5(md5(md5('rootabelfund123')));
    // // pr(Hash::make($pwd));
    // pr(Hash::check($pwd, $oUser->fund_password));
    // $fwd = md5(md5(md5('topagent20Fund1234')));
    // pr(Hash::check($fwd, $oUser->fund_password));

    // $aUsers = User::all();
    // foreach ($aUsers as $key => $oUser) {
    //     $oUser->password =
    // }
    //
    // Session::forget('CardBindingStatus');
    //
    // $data = [
    //     'account'              => '6225111122223333444',
    //     'account_confirmation' => '6225111122223333444',
    // ];
    // $rules = [
    //     'account'              => 'required|regex:/^[0-9]*$/|between:16,19|confirmed',
    //     'account_confirmation' => 'required|regex:/^[0-9]*$/|between:16,19',
    // ];
    // $validator = Validator::make($data, $rules);
    // if (!$validator->passes()) {
    //     pr($validator->errors());exit;
    // }
    //
    // $card = Array
    // (
    //     'bank_id'              => 25,
    //     'bank'                 => '中国工商银行',
    //     'province_id'          => 1,
    //     'province'             => '北京',
    //     'city_id'              => 35,
    //     'city'                 => '东城区',
    //     'branch'               => '东城支行',
    //     'account_name'         => '斯诺',
    //     'account'              => '6225111122223333444',
    //     'account_confirmation' => '6225111122223333444',
    //     'user_id'              => 0,
    //     'islock'               => 0,
    //     'locker'               => 0,
    //     'unlocker'             => 0,
    // );
    // $oCard = new UserBankCard($card);
    // // pr($oCard->toArray());exit;
    // $oCard->account_confirmation = '6225111122223333444';
    // $oCard->user_id = Session::get('user_id');
    // $oCard->username = Session::get('username');
    // $bSucc = $oCard->save();
    // if (! $bSucc) {
    //     pr($oCard->getValidationErrorString());
    // }

    // $aAccountInfos = Account::getAccountInfoByUserId([20]);
    // pr(($aAccountInfos));
    // $mAccounts = User::find(18);
    // $data = $mAccounts->getGroupAccountSum();
    // pr($data);
    //
    // $value = '12223asd';
    // pr(preg_match('/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z]+?)\1\1).{6,16}$/', $value));
    //
    // $params = [
    //     'password'                   => '123qwe',
    //     'password_confirmation'      => '123qwe',
    //     'fund_password'              => '123qwe',
    //     'fund_password_confirmation' => '123qwe',
    // ];
    // $rules = [
    //     'password'              => 'different_before_hash:fund_password',
    //     // 'password_confirmation' => '',
    // ];
    // $validator = Validator::make($params, $rules);
    // pr($validator->passes());
    // pr($validator->errors());
    //
    // $str = md5(md5(md5('topsnowfund123')));
    // $str = '5def48b5dedd77f7dff0cec2b05d9e50';
    // $check = '$2y$10$O4U1ls86Dn94e7z4wCa4xe9nBbxCOQQFahluKqdhAaZCg4fpqn5LW'; // $2y$10$O4U1ls86Dn94e7z4wCa4xe9nBbxCOQQFahluKqdhAaZCg4fpqn5LW
    // pr(Hash::check($str, $check));
    //
    // pr(Lang::get('_user.update-fund-password-fail', ['reason' => 'test']));
    //
    // 测试ajax接口
    // return View::make('centerUser.user.testAjax');

    // $iUserId = 18;
    // $aLotteriesPrizeSets = UserPrizeSet::getUserLotteriesPrizeSets($iUserId);
    // $sCurrentAgentPrizeGroup = $aLotteriesPrizeSets[0]->classic_prize;
    // pr($sCurrentAgentPrizeGroup);
    //
    // $data = SysConfig::getSource(sysConfig::readDataSource('agent_default_prize_group'));
    // $data2 = SysConfig::getSource(sysConfig::readDataSource('top_agent_default_prize_group'));
    // pr($data);
    // pr('-----------------------');
    // pr($data2);
    // exit;
    //
    // $admin = AdminUser::find(10);
    // // $form = [
    // //     'password' => '123qweasd',
    // //     'password_confirmation' => '123qweasd',
    // // ];
    // // $bSucc = $admin->resetPassword($form);
    // // $text = Hash::make('123qweasd');
    // $bSucc = Hash::check(md5(md5(md5('testadmin7123qweasd'))), $admin->password);
    // pr($bSucc);
    // $params = [
    //     'num1' => 112,
    //     'num2' => 0.1,
    // ];
    // $rules = [
    //     'num1' => 'numeric',
    //     'num2' => 'integer'
    // ];
    // $oValidator = Validator::make($params, $rules);
    // pr($oValidator->errors());
    // $url = 'user.local.firecat.com/authority/signup?prize=384043c9867bb49a1ed0d4aeb1daea67';
    // // $sub = 'user.local.firecat.com';
    // $sub = '384043c9867bb49a1ed0d4aeb1daea67';
    // $bIn = stripos($url, $sub);
    // pr($bIn);
    // $params = [
    //     'num1' => '斯诺一代7号',
    // ];
    // $rules = [
    //     'num1' => 'regex:/^[a-z,A-Z,0-9,\一-\龥]+$/',
    // ];
    // $oValidator = Validator::make($params, $rules);
    // $oValidator->passes();
    // pr($oValidator->errors());
    // Session::forget('IsFirstBinding');
    // Session::forget('CardBindingStatus');
    // pr(Session::get('IsFirstBinding'));
    // pr(__('_role.Withdrawal White List'));exit;
    //
    // pr(Carbon::today()->addDays(2)->toDateTimeString());
    //
    // pr(Carbon::parse('2014-12-05 15:00:00')->subHours(1)->toDateTimeString());
    // Session::put('WithdrawalStatus', 0);
    //
    // $oWithdrawal = Withdrawal::where('id', '=', 31)->where('status', '=', 3)->first();
    // pr(count($oWithdrawal));
    //
    // $bSucc = 10;
    // foreach(['123456', '300000001', ] as $key => $value) {
    //     if (! preg_match("/^\d*$/",$value) ) {
    //         $bSucc = 'no-num-' . $key;
    //         break;
    //     }
    //     if ( $value < 50000 || $value > 300000000) {
    //         $bSucc = 'over-' . $key;
    //         break;
    //     }
    // }
    // pr($bSucc);
    //
    // $data = Domain::getRandomDomainInPool(0);
    // pr($data);
    // $oUser = User::find(18);
    // $sMd5 = md5(md5(md5($oUser->username . '123qwe')));
    // pr(Hash::check('5def48b5dedd77f7dff0cec2b05d9e50', $oUser->password));
    //
    // $aLotteriesArray = Lottery::getAllLotteries(1);
    // pr($aLotteriesArray);exit;
    // $bt = preg_match('/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]+?)\1\1).{6,16}$/', 'q1q1q1');
    // $bt = preg_match('/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/', '111q1q');
    // pr(intval($bt));
    //
    // $oIssue = new Issue;
    // $data = $oIssue->getIssueArrayForWinNum(1,10);
    // var_dump($data);exit;
    //
    // $aBets = UserProject::getRecordsByParams(1, 4);
    // $data = UserTransaction::getUserOwnLatestRecords();
    // // $aTraces = UserTrace::getRecordsByParams(1, 4);
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);

    // pr($aBets->toArray());exit;
    $aJsConfig = Config::get('var.js_config')[Config::get('var.environment')];
    $sFile = '../userpublic' . $aJsConfig['base_path'] . 'game-all' . $aJsConfig['suffix'];
    // pr(file_exists($sFile));exit;
    $data = sha1_file( $sFile);
    pr($data);exit;
});
Route::get('test', function ()
{
    // $ac = 'UserTrendController@index';
    // $router = Route::getRoutes()->getByAction($ac);
    // $route_name = $router->getName();
    // echo $route_name;
    //
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

    // $rules = [
    //     'username'  => 'required|between:4,32', // |unique:admin_users,username
    //     'name'      => 'required|between:0,50',
    //     'email'     => 'email|between:0, 200',
    //     'password'  => 'required|min:6|',
    //     'password_confirmation' => 'required|min:6|same:password',
    //     'language'  => 'between:0, 10',
    //     'menu_link' => 'in:0, 1',
    //     'menu_context' => 'in:0, 1',
    //     'actived'   => 'in:0, 1',
    //     'secure_card_number' => 'numeric|between:0, 10'
    // ];

    // $valid = $user->validate($rules);
    // if ($valid) {
    //     $user->password = Hash::make($user->password);
    //     $user->save();
    // }
    // var_dump($user->validationErrors);
    //
    // $ar = AdminUser::find(1);
    // $users = $ar->roles()->get();
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
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
    // $oTrend = new UserTrend;
    // $data = $oTrend->getProbabilityOfOccurrenceByParams(1, 5, null, null, 50);
    // pr($data);

    // $oLink = UserCreateUserLink::getRegisterUserPrizeGroup('e52567a4a5888e34f9457c86eb0778f5');
    // $data = $oLink->users()->get();
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
    // pr($data->toArray());
    //
    // $data = UserCreateUserLink::where('username', '=', 'Agent')->get(); // where('expired_at', '>', Carbon::now())->
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
    // pr(Carbon::now()->toDateTimeString());
    // pr($data->toArray());
    // $re = [];
    // foreach ($data as $key => $value) {
    //     if ($value->status == 0 && Carbon::now()->toDateTimeString() > $value->expired_at) {
    //         $re[] = $value->expired_at;
    //         $re[] = 'Overtime';
    //     }
    // }
    // pr($re);
    //
    // pr(UserPrizeSet::getTopAgentPrizeGroupDistribution()->toArray());
    // $queries = DB::getQueryLog();
    // $last_query = end($queries);
    // pr($last_query);
    //
    // pr(date('Y-m-d H:i:s'));
    // pr(time());
    //

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

