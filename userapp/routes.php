<?php

/*
  |--------------------------------------------------------------------------
  | 前台路由
  |--------------------------------------------------------------------------
 */
Route::get('404', ['as' => '404', function () {
        return View::make('system.404');
    }]);
Route::get('403', ['as' => '403', function () {
        return View::make('system.403');
    }]);


#品牌
Route::any('brand', ['as' => 'brand', function() {
        return View::make('adjunctive.brand');
    }])->before('ip-blocked');
#游戏介绍
Route::any('introduce', ['as' => 'introduce', function() {
        return View::make('adjunctive.introduce');
    }])->before('ip-blocked');
// $sRouteDir = Config::get('route.root');
include('testCase.php');

# 帮助中心
Route::group(['prefix' => 'help', 'before' => 'ip-blocked'], function () {
    $resource = 'help';
    $controller = 'HelpController@';
    Route::get('/{category_id?}', ['as' => $resource . '.index', 'uses' => $controller . 'helpIndex']);
});

Route::group(['prefix' => 'reg', 'before' => 'ip-blocked'], function () {
    $Authority = 'AuthorityController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/{prize}', ['as' => 'reg', 'uses' => $Authority . 'signup', 'before' => 'max-access']);
    });
});

Route::group(['prefix' => 'zc', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'zc', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'qm', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'qm', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'cg', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'cg', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'dg', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'dg', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'sp', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'sp', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'xiaomi', 'before' => 'ip-blocked'], function () {
    $Authority = 'SpoController@';
    Route::group(['before' => 'guest'], function () use ($Authority) {
        Route::any('/', ['as' => 'xiaomi', 'uses' => $Authority . 'signup']);
    });
});

Route::group(['prefix' => 'auth'], function () {
    $Authority = 'AuthorityController@';
    # 退出
    Route::get('logout', ['as' => 'logout', 'uses' => $Authority . 'logout']);
    Route::group(['before' => 'guest'], function () use ($Authority) {
        # 登录
        Route::any('signin', ['as' => 'signin', 'uses' => $Authority . 'signin']);
        Route::any('signup', ['as' => 'signup', 'uses' => $Authority . 'signup', 'before' => 'max-access']);
        Route::any('check-username-is-exist', ['as' => 'check-username-is-exist', 'uses' => $Authority . 'checkUsernameIsExist']);
        Route::any('check-captcha-error', ['as' => 'check-captcha-error', 'uses' => $Authority . 'checkCaptchaError']);
    });
    Route::any('reg-transition', ['as' => 'authority.reg-transition', function() {
            return View::make('authority.reg-transition');
        }]);


    // # 忘记密码
    // Route::get(          'forgot-password', array('as' => 'forgotPassword', 'uses' => $Authority.'getForgotPassword'));
    // Route::post(         'forgot-password', $Authority.'postForgotPassword');
    // # 密码重置
    // Route::get(  'forgot-password/{token}', array('as' => 'reset'         , 'uses' => $Authority.'getReset'));
    // Route::post( 'forgot-password/{token}', $Authority.'postReset');
});

Route::any('brand', ['as' => 'brand', function() {
        return View::make('adjunctive.brand');
    }])->before('ip-blocked');

Route::group(['before' => 'user-auth|ip-blocked|csrf'], function () {
    $controller = 'HomeController@';
    $resource = 'home';
    # 博客首页
    Route::get('/', ['as' => 'home', 'uses' => $controller . 'getIndex']);
    Route::get('/agent-center', ['as' => $resource . '.agent-center', 'uses' => $controller . 'getAgentCenter']);
    Route::get('/get-team-data/{period?}', ['as' => $resource . '.get-team-data', 'uses' => $controller . 'getMyTeamData']);
    Route::get('/get-agent-month-rank/{rankby?}', ['as' => $resource . '.get-agent-month-rank', 'uses' => $controller . 'getAgentMonthRank']);
    Route::get('/get-month-team-turnover', ['as' => $resource . '.get-month-team-turnover', 'uses' => $controller . 'getUserMonthTeamTurnover']);
    Route::any('/set-cache', ['as' => $resource . '.set-cache', 'uses' => $controller . 'setCache']);
    Route::any('/get-cache', ['as' => $resource . '.get-cache', 'uses' => $controller . 'getCache']);

    # include start #
    $sRouteDir = Config::get('route.root');
    $aRouteFiles = glob($sRouteDir . '*.php');
    foreach ($aRouteFiles as $sRouteFile) {
        include($sRouteFile);
    }
    unset($aRouteFiles);
});

include(Config::get('route.trend'));
#### tudo 临时路由##
// Route::any('events', ['as' => '404', function () {
//         return View::make('events.xinyunmao');
//     }]);
// Route::get('signupSuccess', ['as' => '404', function () {
//     return View::make('authority.signupSuccess');
// }]);
#######
// 投注
Route::group(['prefix' => 'bets'], function () {
    $resource = 'bets';
//     Route::post('bet/{lottery_id?}', [ 'as' => $resource . '.bet', 'uses' => 'BetController@bet']);
    Route::post('/upload-bet-number', ['as' => $resource . '.upload-bet-number', 'uses' => function () {
//        pr(Input::all());
//        if (Request::getMethod() !== 'GET' && Session::token() != Input::get('_token')){
//            die('请先登录');
//        }
            $aLimits = [
                'extension' => [ 'txt'],
                'mime_type' => [ 'text/plain'],
                'max_size' => 1024 * 1024 * 3
            ];
            $aInputData = Input::all();
            $oFileInfo = $aInputData['betNumber'];
            in_array($oFileInfo->getClientOriginalExtension(), $aLimits['extension']) or die();
            in_array($oFileInfo->getClientMimeType(), $aLimits['mime_type']) or die();
            $oFileInfo->getClientSize() <= $aLimits['max_size'] or die();
            $rs = file_get_contents($oFileInfo->getPathName());
            echo '<script>(function(){var Games = window.parent.bomao.Games,current = Games.getCurrentGame().getCurrentGameMethod(),data=' . json_encode($rs) . '; current.getFile(data)})()</script>';
            exit;
        }]);
            Route::get('wn-numbers/{lottery_id?}', ['as' => $resource . '.wn-numbers', 'uses' => 'BetController@getLatestIssueWnNumbers']);
        });

// 预约成为总代
        Route::any('reserve-agent/index', ['as' => 'reserve-agent', function () {
                return View::make('events.reserve_agent.index');
            }])->before('ip-blocked');
        Route::any('reserve-agent/form', ['as' => 'reserve-agent.form', function () {
                return View::make('events.reserve_agent.form');
            }])->before('ip-blocked');
// 禁止IP页面
        Route::any('forbid', ['as' => 'forbid', function () {
                return View::make('events.forbid.index');
            }]);

// 充值回调
        Route::group(['prefix' => 'depositapi'], function () {
            $resource = 'depositapi';
//            $controller = 'DepositNotifyController';
            Route::any('zf', ['as' => $resource . '.zf', 'uses' => "ZHIFUDepositNotifyController@doCallback"]);
            Route::any('ips', ['as' => $resource . '.ips', 'uses' => "IPSDepositNotifyController@doCallback"]);
            Route::any('xs', ['as' => $resource . '.xs', 'uses' => "XINSHENGDepositNotifyController@doCallback"]);
            Route::any('gfb', ['as' => $resource . '.gfb', 'uses' => "GUOFUBAODepositNotifyController@doCallback"]);
            Route::any('gfbwap', ['as' => $resource . '.gfbwap', 'uses' => "GUOFUBAOWAPDepositNotifyController@doCallback"]);
            Route::any('zfb', ['as' => $resource . '.zfb', 'uses' => "ZHIFUBAODepositNotifyController@doCallback"]);
            Route::any('ly', ['as' => $resource . '.ly', 'uses' => "LEYINGDepositNotifyController@doCallback"]);
            Route::any('lywx', ['as' => $resource . '.lywx', 'uses' => "LEYINGWXDepositNotifyController@doCallback"]);
            Route::any('lyzfb', ['as' => $resource . '.lyzfb', 'uses' => "LEYINGZFBDepositNotifyController@doCallback"]);
            Route::any('th', ['as' => $resource . '.th', 'uses' => "TONGHUIDepositNotifyController@doCallback"]);
            Route::any('thwy', ['as' => $resource . '.thwy', 'uses' => "TONGHUIWYDepositNotifyController@doCallback"]);
            Route::any('thzfb', ['as' => $resource . '.thzfb', 'uses' => "TONGHUIZFBDepositNotifyController@doCallback"]);
            Route::any('thwxpc', ['as' => $resource . '.thwxpc', 'uses' => "TONGHUIWXPCDepositNotifyController@doCallback"]);
            Route::any('rf', ['as' => $resource . '.rf', 'uses' => "RFPAYDepositNotifyController@doCallback"]);
            Route::any('khb', ['as' => $resource . '.khb', 'uses' => "KHBDepositNotifyController@doCallback"]);
            Route::any('zhf', ['as' => $resource . '.zhf', 'uses' => "ZHIHFDepositNotifyController@doCallback"]);
            Route::any('zhfwx', ['as' => $resource . '.zhfwx', 'uses' => "ZHIHFWXDepositNotifyController@doCallback"]);
            Route::any('zhfzfb', ['as' => $resource . '.zhfzfb', 'uses' => "ZHIHFUZFBDepositNotifyController@doCallback"]);
            Route::any('zhfqq', ['as' => $resource . '.zhfqq', 'uses' => "ZHIHFUQQDepositNotifyController@doCallback"]);
            Route::any('zs', ['as' => $resource . '.zs', 'uses' => "ZESHENGDepositNotifyController@doCallback"]);
            Route::any('zswx', ['as' => $resource . '.zswx', 'uses' => "ZESHENGWXDepositNotifyController@doCallback"]);
            Route::any('zsqq', ['as' => $resource . '.zsqq', 'uses' => "ZESHENGQQDepositNotifyController@doCallback"]);
            Route::any('zszfb', ['as' => $resource . '.zszfb', 'uses' => "ZESHENGZFBDepositNotifyController@doCallback"]);
            Route::any('wefu', ['as' => $resource . '.wefu', 'uses' => "WEFUDepositNotifyController@doCallback"]);
            Route::any('wfzfb', ['as' => $resource . '.wfzfb', 'uses' => "WFZFBDepositNotifyController@doCallback"]);
            Route::any('wfwx', ['as' => $resource . '.wfwx', 'uses' => "WFWXDepositNotifyController@doCallback"]);
            Route::any('wfqq', ['as' => $resource . '.wfqq', 'uses' => "WFQQDepositNotifyController@doCallback"]);
        });
        Route::group(['prefix' => 'dnotify'], function () {
            $resource = 'dnotify';
//            $controller = 'DepositNotifyController';
            Route::any('zf', ['as' => $resource . '.zf', 'uses' => "ZHIFUDepositNotifyController@doCallback"]);
            Route::any('ips', ['as' => $resource . '.ips', 'uses' => "IPSDepositNotifyController@doCallback"]);
            Route::any('xs', ['as' => $resource . '.xs', 'uses' => "XINSHENGDepositNotifyController@doCallback"]);
            Route::any('gfb', ['as' => $resource . '.gfb', 'uses' => "GUOFUBAODepositNotifyController@doCallback"]);
            Route::any('gfbwap', ['as' => $resource . '.gfbwap', 'uses' => "GUOFUBAOWAPDepositNotifyController@doCallback"]);
            Route::any('zfb', ['as' => $resource . '.zfb', 'uses' => "ZHIFUBAODepositNotifyController@doCallback"]);
            Route::any('ly', ['as' => $resource . '.ly', 'uses' => "LEYINGDepositNotifyController@doCallback"]);
            Route::any('lywx', ['as' => $resource . '.lywx', 'uses' => "LEYINGWXDepositNotifyController@doCallback"]);
            Route::any('lyzfb', ['as' => $resource . '.lyzfb', 'uses' => "LEYINGZFBDepositNotifyController@doCallback"]);
            Route::any('th', ['as' => $resource . '.th', 'uses' => "TONGHUIDepositNotifyController@doCallback"]);
            Route::any('thwy', ['as' => $resource . '.thwy', 'uses' => "TONGHUIWYDepositNotifyController@doCallback"]);
            Route::any('thzfb', ['as' => $resource . '.thzfb', 'uses' => "TONGHUIZFBDepositNotifyController@doCallback"]);
            Route::any('thwxpc', ['as' => $resource . '.thwxpc', 'uses' => "TONGHUIWXPCDepositNotifyController@doCallback"]);
            Route::any('khb', ['as' => $resource . '.khb', 'uses' => "KHBDepositNotifyController@doCallback"]);
            Route::any('zhf', ['as' => $resource . '.zhf', 'uses' => "ZHIHFDepositNotifyController@doCallback"]);
            Route::any('zhfwx', ['as' => $resource . '.zhfwx', 'uses' => "ZHIHFWXDepositNotifyController@doCallback"]);
            Route::any('zhfzfb', ['as' => $resource . '.zhfzfb', 'uses' => "ZHIHFUZFBDepositNotifyController@doCallback"]);
            Route::any('zhfqq', ['as' => $resource . '.zhfqq', 'uses' => "ZHIHFUQQDepositNotifyController@doCallback"]);
            Route::any('zs', ['as' => $resource . '.zs', 'uses' => "ZESHENGDepositNotifyController@doCallback"]);
            Route::any('zswx', ['as' => $resource . '.zswx', 'uses' => "ZESHENGWXDepositNotifyController@doCallback"]);
            Route::any('zsqq', ['as' => $resource . '.zsqq', 'uses' => "ZESHENGQQDepositNotifyController@doCallback"]);
            Route::any('zszfb', ['as' => $resource . '.zszfb', 'uses' => "ZESHENGZFBDepositNotifyController@doCallback"]);
            Route::any('wefu', ['as' => $resource . '.wefu', 'uses' => "WEFUDepositNotifyController@doCallback"]);
            Route::any('wfzfb', ['as' => $resource . '.wfzfb', 'uses' => "WFZFBDepositNotifyController@doCallback"]);
            Route::any('wfwx', ['as' => $resource . '.wfwx', 'uses' => "WFWXDepositNotifyController@doCallback"]);
            Route::any('wfqq', ['as' => $resource . '.wfqq', 'uses' => "WFQQDepositNotifyController@doCallback"]);
        });

// 手机客户端下载页面
        Route::any('mobile', ['as' => 'mobile', function () {
                return View::make('events.mobile.index');
            }])->before('ip-blocked');

        Route::any('mobile/help', ['as' => 'mobile.help', function () {
                return View::make('events.mobile.help');
            }])->before('ip-blocked');


        Route::any('mobileo', ['as' => 'mobile', function () {
                return View::make('events.mobile.indexo');
            }])->before('ip-blocked');


// 新代理制度页面
        Route::any('policy', ['as' => 'policy', function () {
                return View::make('events.policy.index');
            }]);


// 闯关奖上奖活动
        Route::any('pass', ['as' => 'pass', function () {
                return View::make('events.pass.index');
            }])->before('ip-blocked');


// 禁止IP页面
        Route::any('forbid', ['as' => 'forbid', function () {
                return View::make('events.forbid.index');
            }]);

//代理活动页面
        Route::any('moneycat', ['as' => 'moneycat', function () {
                return View::make('events.moneycat.index');
            }])->before('ip-blocked');
////代理扶持-一代
//Route::any('fuchi-y', ['as' => 'fuchi-y', function () {
//    return View::make('events.fuchi-y.index');
//}])->before('ip-blocked');
////代理扶持-总代
//Route::any('fuchi-z', ['as' => 'fuchi-z', function () {
//    return View::make('events.fuchi-z.index');
//}])->before('ip-blocked');

        Route::group([], function () {
            $sController = 'UserReserveAgentController@';
            Route::any('/reserve-agent/reserve', ['as' => 'reserve-agent.reserve', 'uses' => $sController . 'reserve']);
        });
////代理扶持-一代
//Route::any('bonus', ['as' => 'bonus', function () {
//    return View::make('events.bonus.index');
//}])->before('ip-blocked');
//活动
        /* Route::group(['prefix' => 'activity'], function () {
          $resource = 'activity';
          $controller = 'UserActivityController@';
          Route::any('is-complete-condition', ['as' => $resource . '.isCompleteCondition', 'uses' => $controller . 'isCompleteCondition']);
          Route::any('is-complete-task', ['as' => $resource . '.isCompleteTask', 'uses' => $controller . 'isCompleteTask']);
          Route::any('receive-task', ['as' => $resource . '.receiveTask', 'uses' => $controller . 'receiveTask']);
          Route::any('apply', ['as' => $resource . '.apply', 'uses' => $controller . 'apply']);
          Route::any('{id}/lucky-draw', ['as' => $resource . '.luckyDraw', 'uses' => $controller . 'luckyDraw']);
          }); */


//域名管理 登陆地址配置接口
        Route::group([], function () {
            $controller = 'DomainApiController@';
            Route::any('/domain-api/get-domains', ['as' => 'domain-api.get-domains', 'uses' => $controller . 'getDomains']);
            Route::any('/domain-api/get-software-info', ['as' => 'domain-api.get-software-info', 'uses' => $controller . 'getSoftwareInfo']);
            Route::get('/domain-api/getencode', ['as' => 'get_encode', 'uses' => $controller . 'getEncode']);
        });

        Route::group(['prefix' => 'mobile-auth'], function () {
            $Authority = 'MobileAuthorityController@';
            # 退出
            Route::get('logout', ['as' => 'mobile-auth.logout', 'uses' => $Authority . 'logout']);
            # 登录
            Route::any('login', ['as' => 'mobile-auth.login', 'uses' => $Authority . 'login']);
            Route::any('register', ['as' => 'mobile-auth.register', 'uses' => $Authority . 'register']);
            // # 忘记密码
            // Route::get(          'forgot-password', array('as' => 'forgotPassword', 'uses' => $Authority.'getForgotPassword'));
            // Route::post(         'forgot-password', $Authority.'postForgotPassword');
            // # 密码重置
            // Route::get(  'forgot-password/{token}', array('as' => 'reset'         , 'uses' => $Authority.'getReset'));
            // Route::post( 'forgot-password/{token}', $Authority.'postReset');
        });

//获取服务器时间，供移动端对接使用
        Route::get('/mobile/server-time', function() {
            $oMessage = new Message([], true);
            $data = ['server_time' => date('Y-m-d H:i:s')];
            $oMessage->output(1, 'info', null, $data);
            exit;
        });

        Route::group(['before' => 'mobile-auth'], function () {
            $sRouteDir = Config::get('route.mobile');
            $aRouteFiles = glob($sRouteDir . '*.php');
            foreach ($aRouteFiles as $sRouteFile) {
                include($sRouteFile);
            }
            unset($aRouteFiles);
        });

//抢红包活动首页
        Route::group(['prefix' => 'anniversary'], function () {

            Route::get('/get-datas', array('as' => 'anniversary.get-datas', 'uses' => 'UserActiveRedEnvelopeUserController@getDatas'));
            Route::get('/', function() {
                return View::make('events.anniversary.index');
            });
        });

//pk10推广页
        Route::group(['prefix' => 'pk10'], function () {
            $controller = 'Pk10Controller@';
            Route::group(['before' => 'guest'], function () use ($controller) {
                Route::get('/', ['as' => 'pk10' . '.index', 'uses' => $controller . 'index']);
            });
        });

        Route::group([], function () {
            $aGongcaiHost = Config::get('gongcai_host');
            $sHttpHost = Request::server('HTTP_HOST');
            if (is_array($aGongcaiHost) && in_array($sHttpHost, $aGongcaiHost)) {
                Session::put('is_client', 1);

                Route::group(['ip-blocked|csrf'], function () {
                    $controller = 'HomeController@';
                    $resource = 'home';

                    # 直客首页
                    Route::get('/', ['as' => 'home', 'uses' => $controller . 'getIndex']);
                });
            } else {
                Session::put('is_client', 0);
            }
        });


        Route::group(['prefix' => 'issueannoucement'], function () {
            $resource = 'issueannoucement';
            $controller = 'IssueAnnoucementController@';
//    Route::get( '/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
            Route::group(['before' => 'guest'], function () use ($resource, $controller) {
                Route::get('/', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
            });
        });

        Route::group(['prefix' => 'lotteryinformation'], function () {
            $resource = 'lotteryinformation';
            $controller = 'LotteryInformationController@';
            Route::get('/index/{cate_id}/{name}', ['as' => $resource . '.index', 'uses' => $controller . 'index']);
            Route::any('create', ['as' => $resource . '.create', 'uses' => $controller . 'create']);
            Route::get('{id}/view', ['as' => $resource . '.view', 'uses' => $controller . 'view']);
            Route::any('{id}/edit', ['as' => $resource . '.edit', 'uses' => $controller . 'edit']);
            Route::delete('{id}', ['as' => $resource . '.destroy', 'uses' => $controller . 'destroy']);
        });
        