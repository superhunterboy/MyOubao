<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the Closure to execute when that URI is requested.
  |
 */
$sRouteDir = Config::get('route.root');
include($sRouteDir . 'testCase.php');

// Route::any('error', ['as' => 'error', function () {
//     return View::make('system.404');
// }]);
#### tudo 升级点管理页面临时路由##
Route::any('liftRules', ['as' => 'liftRules.index', function () {
        return View::make('liftRules.index');
    }]);
Route::any('accountsSearch', ['as' => 'accountsSearch.index', function () {
        return View::make('liftRules.as');
    }]);
Route::any('fee', ['as' => 'liftRules.index', function () {
        return View::make('fee.index');
    }]);
//Route::any('set-note', ['as' => 'basic-methods.set-note', function () {
//    return View::make('basicMethods.edit');
//}]);
Route::any('testMcCurl',['as'=>'testMcCurl',function(){
    
   
     $o_mc_order = new McOrder();
    
               $o_bank = new Bank();
            $a['bank_id']			= $o_bank->find(27)->getAttribute("mc_bank_id");
            $a['company_id']			= 9;
            $a['amount']				= 1.00;
            $a['card_num']			= '6228480339008633572';
            $a['card_name']			= 'test';
            $a['company_user'] 		= $o_mc_order->_getUserFlag(39342);
            pr($a);
     $key=$o_mc_order->_getKey($a,McOrder::WITHDRAW_APPROVE);
     
     pr($key);
     
//echo "eee";exit;
//    $sWithdrawCheckUrl  = "http://10.89.7.11/Mownecum_2_API_Live/QueryWithdrawal?format=json";
    $sWithdrawCheckUrl = 'http://192.168.1.15/Mownecum_api/Withdrawal?format=json';
    $orderCheckData['company_id']		= '9';
    $orderCheckData['company_order_num']	= 'FC18007353236622';
    $orderCheckData['mownecum_order_num']	= '';
    $orderCheckData['key']			= 'logivo&mow=pass&1207';
    $orderCheckData= array (
            'company_id' => '9',
            'company_order_num' => 'FC18007353236622',
            'mownecum_order_num' => '',
            'key' => 'c3b3efd51af446a5cce3cd492ae47cc7',
    );
        file_put_contents('/tmp/mc_tt',  var_export($orderCheckData,true),FILE_APPEND);

    $oCurl = new MyCurl($sWithdrawCheckUrl);
    $oCurl->setPost($orderCheckData);
    $oCurl->createCurl();
    $oCurl->execute();
    $aCheckResult = $oCurl->__tostring();
//    $status=$oCurl->getHttpStatus();
//    var_dump( var_export($orderCheckData));
//    pr($aCheckResult);
//    exit;
    file_put_contents("/tmp/mc_".date("Y-m-d"),$aCheckResult,FILE_APPEND);
//    if($aCheckResult != "") {
        echo($aCheckResult);
        $re = json_decode($aCheckResult, true);
        pr($re);
        exit;
//    }
}]);


Route::any('testMcCurlRe',['as'=>'testMcCurlRe',function(){

    $sWithdrawUrl='http://192.168.1.15/Mownecum_api/Withdrawal?format=json';
    $postData=[    'bank_id' => 2,
        'company_id' => '9',
        'amount' => '1030.00',
        'card_num' => '6228480339008633572',
        'card_name' => '张金芳',
        'company_user' => 'G',
        'company_order_num' => 'FC18007353236622',
        'key' => 'c3b3efd51af446a5cce3cd492ae47cc7'
    ];

    //Curl 请求
    $oCurl = new MyCurl($sWithdrawUrl);
    $oCurl->setPost($postData);
    $oCurl->createCurl();
    $oCurl->execute();
    $aApiRes = $oCurl->__tostring();
    file_put_contents("/tmp/mc_".date("Y-m-d"),var_export($aApiRes,true),FILE_APPEND);
    file_put_contents("/tmp/mc_".date("Y-m-d"),$oCurl->getHttpStatus(),FILE_APPEND);
    if($aApiRes != "") {
        echo($aApiRes);
        $re = json_decode($aApiRes, true);
        var_dump($re);
        exit;
    }
}]);
#######


/*
  |--------------------------------------------------------------------------
  | 基础权限
  |--------------------------------------------------------------------------
 */
// Route::group(array('prefix' => 'auth'), function () {
//     $Authority = 'AuthorityController@';
//     # 退出
//     Route::get('logout', array('as' => 'logout', 'uses' => $Authority.'logout'));
//     Route::group(array('before' => 'guest'), function () use ($Authority) {
//         # 登录
//         Route::any('signin', array('as' => 'signin', 'uses' => $Authority.'signin'));
//         // Route::post(                  'signin', $Authority.'postSignin');
//     });
//     // # 忘记密码
//     // Route::get(          'forgot-password', array('as' => 'forgotPassword', 'uses' => $Authority.'getForgotPassword'));
//     // Route::post(         'forgot-password', $Authority.'postForgotPassword');
//     // # 密码重置
//     // Route::get(  'forgot-password/{token}', array('as' => 'reset'         , 'uses' => $Authority.'getReset'));
//     // Route::post( 'forgot-password/{token}', $Authority.'postReset');
// });

Route::group(['prefix' => 'admin-auth'], function () {
    $Authority = 'AdminAuthorityController@';
    # 退出
    Route::get('logout', ['as' => 'admin-logout', 'uses' => $Authority . 'logout']);
    Route::group(['before' => 'guest'], function () use ($Authority) {
        # 登录
        Route::any('signin', ['as' => 'admin-signin', 'uses' => $Authority . 'signin']);
        // Route::post(                  'signin', $Authority.'postSignin');
    });
    // # 忘记密码
    // Route::get(          'forgot-password', ['as' => 'admin-forgotPassword', 'uses' => $Authority.'getForgotPassword']);
    // Route::post(         'forgot-password', $Authority.'postForgotPassword');
    // # 密码重置
    // Route::get(  'forgot-password/{token}', ['as' => 'admin-reset'         , 'uses' => $Authority.'getReset']);
    // Route::post( 'forgot-password/{token}', $Authority.'postReset');
});

/*
  |--------------------------------------------------------------------------
  | 后台管理
  |--------------------------------------------------------------------------
 */
Route::group(['before' => 'admin-auth|csrf'], function () {
    $admin = 'AdminController@';
    # 框架frameset
    Route::get('/', ['as' => 'admin.frameset', 'uses' => $admin . 'getFrameset']);
    # 后台头部
    Route::get('header', ['as' => 'admin.header', 'uses' => 'AdminMenuController@getHeader']);
    # 后台菜单
    Route::get('sidemenu', ['as' => 'admin.sidemenu', 'uses' => 'AdminMenuController@display']);
    # 后台首页
    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses' => $admin . 'getIndex']);

    # include start #
    $sRouteDir = Config::get('route.root');
    $aRouteFiles = glob($sRouteDir . '*.php');
    /*================支持子目录================*/
    $aDirList = scandir($sRouteDir);
    foreach($aDirList as $sPath){
        $sFilePath = $sRouteDir . $sPath . '/';
        if ($sPath != '.' && $sPath != '..' && is_dir($sFilePath)){
            $aRouteFiles = array_merge($aRouteFiles, glob($sFilePath . '*.php'));
        }
    }
    /*================支持子目录================*/
    foreach ($aRouteFiles as $sRouteFile) {
        include($sRouteFile);
    }
    unset($aRouteFiles);
});

Route::group([], function () {
    $sController = 'WinningNumberController@';
    # 框架frameset
    Route::any('/winning-number/set', ['as' => 'winning-number.set', 'uses' => $sController . 'setWinningNumber']);
});


Route::group([], function () {
    $sController = 'ReviseWinningNumberController@';
    # 框架frameset
    Route::any('/winning-number/revise', ['as' => 'winning-number.revise', 'uses' => $sController . 'reviseWinningNumber']);
});

Route::group([], function () {
    $sController = 'AlarmWinningNumberController@';
    # 框架frameset
    Route::any('/winning-number/alarm', ['as' => 'winning-number.alarm', 'uses' => $sController . 'alarmWinningNumber']);
});

Route::group([], function () {
    $sController = 'DrawWinningNumberController@';
    # 框架frameset
    Route::any('/winning-number/getcodecheck', ['as' => 'winning-number.getcodecheck', 'uses' => $sController . 'drawWinningNumberProCheck']);
});

Route::group([], function () {
    $sController = 'IssueCompareController@';
    # 框架frameset
    Route::any('/winning-number/getissues', ['as' => 'winning-number.getissues', 'uses' => $sController . 'getIssues']);
});

Route::group([], function() { // 'before' => 'user-auth'
    $resource = 'cms.';
    Route::get('acms', function() use ($resource) {
        return View::make($resource . 'index');
    });
});

//
Route::group([], function() {
    $resource = 'link.';
    Route::get('link', function() use ($resource) {
        return View::make($resource . 'index');
    });
});

// 提现推送接口
Route::group(['prefix' => 'mc-api'], function () {
    $resource = 'mc-withdraw';
    $controller = 'McApiController@';
    Route::any('/type=requestWithdrawApproveInformation', ['as' => $resource . '.info', 'uses' => $controller . 'verifyWithdrawInfo']);
    Route::any('/type=withdrawalResult', ['as' => $resource . '.result', 'uses' => $controller . 'verifyWithdrawResult']);   //withdrawalResult
});

// 充值接收推送接口
Route::group(['prefix' => 'mc-api'], function () {
    $resource = 'mc-deposit';
    $controller = 'DepositApiController@';
    Route::any('/type=addTransfer', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
});
//sdpay 充值接收推送接口
Route::group(['prefix' => 'mc-api'], function () {
    $resource = 'mc-deposit';
    $controller = 'SdpayDepositApiController@';
    Route::any('/type=sdpayAddTransfer', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
});

// 异常充值接收推送接口
Route::group(['prefix' => 'mc-api'], function () {
    $resource = 'mc-exception';
    $controller = 'ExceptionDepositApiController@';
    Route::any('/type=exceptionWithdrawApply', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
});

// SDPay提现推送接口
Route::group(['prefix' => 'sd-api'], function () {
    $resource = 'sd-withdraw';
    $controller = 'SdApiController@';
    Route::any('/type=withdrawalResult', ['as' => $resource . '.result', 'uses' => $controller . 'verifyWithdrawResult']);   //withdrawalResult
});


// 代理商校验接口
Route::group(['prefix' => 'agent-api'], function () {
    $resource = 'agentapi';
    $controller = 'AgentApiController@';
    Route::any('verification', ['as' => $resource . '.verification', 'uses' => $controller . 'verification']);
    Route::any('info', ['as' => $resource . '.info', 'uses' => $controller . 'info']);
});

Route::group(['prefix' => 'tonghuika'], function () {
    $resource = 'tonghuika';
    $controller = 'TongHuiKaController@';
    Route::any('/deposit-return', ['as' => $resource . '.deposit-return', 'uses' => $controller . 'depositReturn']);
});
Route::group(['prefix' => 'youfu'], function () {
    $resource = 'youfu';
    $controller = 'YoufuController@';
    Route::any('/deposit-return', ['as' => $resource . '.deposit-return', 'uses' => $controller . 'depositReturn']);
});