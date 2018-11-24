<?php

/*
|--------------------------------------------------------------------------
| 拓展配置文件
|--------------------------------------------------------------------------
|
*/
$aJsConfig = Config::get('var.js_config')[Config::get('var.environment')];
$sStaticPath = Config::get('var.domain_static');


return array(

    /**
     * 网站静态资源文件别名配置
     */
    'webAssets' => array(

        'cssAliases' => array(  //  样式文件别名配置

            'global'                  => $sStaticPath.'assets/images/global/global.css',
            'global-v2'               => $sStaticPath.'assets/images/global/global-v2.css',
            'global-v3'               => $sStaticPath.'assets/images/global-v3/global.css',
            'global-v4'               => $sStaticPath.'assets/images/global-v4/global.css',
            'index'                   => $sStaticPath.'assets/images/index/index.css',
            'index-v3'                => $sStaticPath.'assets/images/index-v3/index.css',
            'index-v4'                => $sStaticPath.'assets/images/index-v4/index.css',
            'game'                    => $sStaticPath.'assets/images/game/game.css',
            'ucenter'                 => $sStaticPath.'assets/images/ucenter/ucenter.css',
            'login'                   => $sStaticPath.'assets/images/login/login.css',
            'login-v2'                => $sStaticPath.'assets/images/login/login-v2.css',
            'login-v3'                => $sStaticPath.'assets/images/login-v3/login.css',
            'login-v4'                => $sStaticPath.'assets/images/login-v4/login.css',
            'reg'                     => $sStaticPath.'assets/images/reg/reg.css',
            'reg-v2'                  => $sStaticPath.'assets/images/reg-v2/reg.css',
            'reg-v3'                  => $sStaticPath.'assets/images/reg-v3/reg.css',
            'reg-v4'                  => $sStaticPath.'assets/images/reg-v4/reg.css',
            'chart'                   => $sStaticPath.'assets/images/chart/chart.css',

            'game-l115'               => $sStaticPath.'assets/images/game/l115/game.css',
            'game-pk10'               => $sStaticPath.'assets/images/game/pk10/game.css',
            'game-k3'                 => $sStaticPath.'assets/images/game/k3/game.css',
            'game-v2'                 => $sStaticPath.'assets/images/game/game-v2.css',
            'game-v3'                 => $sStaticPath.'assets/images/game/game-v3.css',
            'game-table'              => $sStaticPath.'assets/images/game/table/game-table.css',
            //扑克牌
            'game-poker'              => $sStaticPath.'assets/images/game/table/game-poker.css',
            'game-lhd'                => $sStaticPath.'assets/images/game/table/lhd/game-lhd.css',
            'help'                    => $sStaticPath.'assets/images/help/help.css',
            'findpassword'            => $sStaticPath.'assets/images/findpassword/findpassword.css',

            // 百家乐
            'game-bjl'                => $sStaticPath.'assets/images/game/table/bjl/game-bjl.css',

            //静态页样式
            'brand'                   => $sStaticPath.'assets/images/brand/brand.css',
            'brand-v2'                => $sStaticPath.'assets/images/brand/brand-v2.css',
            'login-brand-v2'          => $sStaticPath.'assets/images/brand/login-brand-v2.css',

            //活动样式
            //幸运猫：Deta:2014-11-18
            'eventLottery'            => $sStaticPath.'events/xinyunmao/images/lottery.css',
            //预约成为总代
            'merchants'               => $sStaticPath.'events/reserve_agent/images/merchants.css',
            'reg-z-y'                 => $sStaticPath.'events/reg-z-y/style/style.css',
            'reg-d-u'                 => $sStaticPath.'events/reg-d-u/style/style.css',

            'spo'                     => $sStaticPath.'assets/images/spo/spo.css',

            'proxy-global'            => $sStaticPath.'assets/images/proxy/global.css',
            'proxy'                   => $sStaticPath.'assets/images/proxy/proxy.css',

            'bootstrap'               => $sStaticPath.'assets/images/bootstrap/bootstrap.min.css',
            'r-bootstrap'               => $sStaticPath.'assets/images/bootstrap/r-bootstrap.css',
            'outer'                   => $sStaticPath.'assets/images/outer/outer.css',

            'font-awesome'            => $sStaticPath.'assets/images/proxy/font-awesome.css',
            'animate'                 => $sStaticPath.'assets/images/global-v4/animate.css',

            //春节2016活动
            'spring2016'            => $sStaticPath.'assets/images/events/spring2016/spring.css',

            //体育
            'sports-base'             => $sStaticPath.'assets/images/sports/sports-base.css',

            //eurocup
            'eurocup'             => $sStaticPath.'events/eurocup/eurocup.css',

            //幸运28
            'lucky28'          =>$sStaticPath.'assets/images/lucky28/lucky28-base.css',
            'daterangepicker'          =>$sStaticPath.'assets/images/lucky28/daterangepicker.css',

            //幸运28-宣传页
            'lucky28-ad'          =>$sStaticPath.'assets/images/lucky28-ad/style.css',

            //抽奖活动
            'win-prize-activety'  =>$sStaticPath.'assets/images/win-prize-activity/activity.css',
            //公彩开始
            'indexClient'             => $sStaticPath.'assets/images/indexClient/css/indexClient.css',
            'indexClient-hf'          => $sStaticPath.'assets/images/indexClient/css/headfoot.css',
            'indexClient-lr'          => $sStaticPath.'assets/images/indexClient/css/client-lr.css',
            //公彩结束
            
            //六合彩
            'lhc'          => $sStaticPath.'dist/assets/css/lhc/lhc-all.min.css',

        ),

        'jsAliases'  => array(  //  脚本文件别名配置
            //工具
            'jquery-1.9.1'             => $sStaticPath.'assets/js/jquery-1.9.1.min.js',
            'jquery.min.map'           => $sStaticPath.'assets/js/jquery.min.map.js',
            'jquery.easing.1.3'        => $sStaticPath.'assets/js/jquery.easing.1.3.js',
            'jquery.flexslider'        => $sStaticPath.'assets/js/jquery.flexslider-min.js',
            'jquery.tmpl'              => $sStaticPath.'assets/js/jquery.tmpl.min.js',
            'jquery.mousewheel'        => $sStaticPath.'assets/js/jquery.mousewheel.min.js',
            'jquery.cookie'            => $sStaticPath.'assets/js/jquery.cookie.js',
            'jquery.jscrollpane'       => $sStaticPath.'assets/js/jquery.jscrollpane.js',
            'jquery.cookie'            => $sStaticPath.'assets/js/jquery.cookie.js',
            'jquery.flot'              => $sStaticPath.'assets/js/jquery.flot.js',
            'jquery.flot.crosshair'    => $sStaticPath.'assets/js/jquery.flot.crosshair.js',
            'jquery.flot.pie'          => $sStaticPath.'assets/js/jquery.flot.pie.js',
            'jquery.flot.categories'   => $sStaticPath.'assets/js/jquery.flot.categories.js',
            'IE-excanvas'              => $sStaticPath.'assets/js/excanvas.min.js',
            'bomao.Encrypt'            => $sStaticPath.'assets/js/bomao.Encrypt.js',
            'jquery.easing'            => $sStaticPath.'events/eurocup/jquery.easing.1.3.js',
            'jquery.lavalamp'          => $sStaticPath.'events/eurocup/jquery.lavalamp.min.js',
            //组件
            'bomao.base'               => $sStaticPath.'assets/js/bomao.base.js',
            'bomao.Tab'                => $sStaticPath.'assets/js/bomao.Tab.js',
            'bomao.Slider'             => $sStaticPath.'assets/js/bomao.Slider.js',
            'bomao.Hover'              => $sStaticPath.'assets/js/bomao.Hover.js',
            'bomao.Select'             => $sStaticPath.'assets/js/bomao.Select.js',
            'bomao.Timer'              => $sStaticPath.'assets/js/bomao.Timer.js',
            'bomao.Mask'               => $sStaticPath.'assets/js/bomao.Mask.js',
            'bomao.MiniWindow'         => $sStaticPath.'assets/js/bomao.MiniWindow.js',
            'bomao.Tip'                => $sStaticPath.'assets/js/bomao.Tip.js',
            'bomao.Message'            => $sStaticPath.'assets/js/bomao.Message.js',
            'bomao.DatePicker'         => $sStaticPath.'assets/js/bomao.DatePicker.js',
            'bomao.Ernie'              => $sStaticPath.'assets/js/bomao.Ernie.js',
            'bomao.SliderBar'          => $sStaticPath.'assets/js/bomao.SliderBar.js',
            'bomao.Alive'              => $sStaticPath.'assets/js/bomao.Alive.js',
            'bomao.SideTip'            => $sStaticPath.'assets/js/bomao.SideTip.js',
            //游戏类
            'bomao.Games'              => $sStaticPath.'assets/js/game/bomao.Games.js', //游戏命名空间
            'bomao.Game'               => $sStaticPath.'assets/js/game/bomao.Game.js?_v=1.0', //游戏基类
            'bomao.GameMethod'         => $sStaticPath.'assets/js/game/bomao.GameMethod.js', //玩法基类
            'bomao.GameMessage'        => $sStaticPath.'assets/js/game/bomao.GameMessage.js', //游戏消息基类
            'bomao.GameTypes'          => $sStaticPath.'assets/js/game/bomao.GameTypes.js', //玩法分类(静态类)
            'bomao.GameStatistics'     => $sStaticPath.'assets/js/game/bomao.GameStatistics.js', //选球统计(静态类)
            'bomao.GameOrder'          => $sStaticPath.'assets/js/game/bomao.GameOrder.js', //号码栏订单(静态类)
            'bomao.GameTrace'          => $sStaticPath.'assets/js/game/bomao.GameTrace.js', //追号(静态类)
            'bomao.GameSubmit'         => $sStaticPath.'assets/js/game/bomao.GameSubmit.js', //提交(静态类)
            'bomao.GameRecords'        => $sStaticPath.'assets/js/game/bomao.GameRecords.js', //Records(静态类)
            //时时彩游戏
            'bomao.Games.SSC'          => $sStaticPath.'assets/js/game/ssc/bomao.Games.SSC.js', //时时彩游戏类
            'bomao.Games.SSC.Danshi'   => $sStaticPath.'assets/js/game/ssc/bomao.Games.SSC.Danshi.js?v=20150615', //时时彩单式类
            'bomao.Games.SSC.Message'  => $sStaticPath.'assets/js/game/ssc/bomao.Games.SSC.Message.js', //时时彩游戏类
            //L115 Game
            'bomao.Games.L115'         => $sStaticPath.'assets/js/game/l115/bomao.Games.L115.js', //L115游戏类
            'bomao.Games.L115.Danshi'  => $sStaticPath.'assets/js/game/l115/bomao.Games.L115.Danshi.js?v=20150615', //L115单式类 danshi
            'bomao.Games.L115.Message' => $sStaticPath.'assets/js/game/l115/bomao.Games.L115.Message.js', //L115游戏类 message

            //K3 Game
            'bomao.Games.K3'         => $sStaticPath.'assets/js/game/k3/bomao.Games.K3.js', //k3游戏类
            'bomao.Games.K3.Danshi'  => $sStaticPath.'assets/js/game/k3/bomao.Games.K3.Danshi.js?v=20150615', //k3单式类 danshi
            'bomao.Games.K3.Message' => $sStaticPath.'assets/js/game/k3/bomao.Games.K3.Message.js', //k3游戏类 message


            'bomao.GameChart'          => $sStaticPath.'assets/js/bomao.GameChart.js', // 时时彩走势图
            'bomao.GameChart.Case'     => $sStaticPath.'assets/js/bomao.GameChart.case.js', // 时时彩走势图

            //其他辅助
            'bomao.U-groupgame'        => $sStaticPath.'assets/js/bomao.ucenter.groupgame.js',
            // 'functions'             => $sStaticPath.'assets/js/functions.js',
            'ZeroClipboard'            => $sStaticPath.'assets/js/ZeroClipboard.js',
            'loginSlides'              => $sStaticPath.'assets/images/login/slides.js', //login-slides.js
            'md5'                      => $sStaticPath.'assets/js/md5.js',
            'excanvas'                 => $sStaticPath.'assets/js/excanvas.min.js',

            'video' => $sStaticPath.'assets/images/video/vcastr2/swfobject.js',

            //活动相关
            //辛运猫-Date:2014-11-18
            'jquery.kxbdMarquee'       => $sStaticPath.'events/xinyunmao/images/jquery.kxbdMarquee.js',
            //预约成为总代
            'swfobject'                => $sStaticPath.'events/reserve_agent/images/vcastr2/swfobject.js',


            'game-table-base'           => $sStaticPath.'assets/js/game/bomao.TableGame.js',
            'game-table-dice'           => $sStaticPath.'assets/js/game/bomao.TableGame.dice.js',

            'game-lhd-init'             => $sStaticPath.'assets/js/game/game-lhd-init.js',

            'game-sports-init'             => $sStaticPath.'assets/js/game/game-sports-init.js',

            'base-all' => $sStaticPath.$aJsConfig['base_path'] . 'base-all' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'base-all' . $aJsConfig['suffix']),
            'game-all' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-all' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-all' . $aJsConfig['suffix']),


            //页面游戏初始化js
            'game-ssc-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-ssc-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-ssc-init' . $aJsConfig['suffix']),
            'game-n115-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-n115-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-n115-init' . $aJsConfig['suffix']),
            'game-3d-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-3d-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-3d-init' . $aJsConfig['suffix']),
            'game-p35-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-p35-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-p35-init' . $aJsConfig['suffix']),
            'game-k3-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-k3-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-k3-init' . $aJsConfig['suffix']),
            'game-dice-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-dice-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-dice-init' . $aJsConfig['suffix']),
            'game-lhd-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-lhd-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-lhd-init' . $aJsConfig['suffix']),
            'game-sports-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-sports-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-sports-init' . $aJsConfig['suffix']),
            'game-pk10-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-pk10-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-pk10-init' . $aJsConfig['suffix']),
            'game-bjl-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-bjl-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-bjl-init' . $aJsConfig['suffix']),
            //'game-pk10-init' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-pk10-init' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-pk10-init' . $aJsConfig['suffix']),
            'game-lucky28-all' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-lucky28-all' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-lucky28-all' . $aJsConfig['suffix']),
            'game-lhc-all' => $sStaticPath.$aJsConfig['base_path'] . 'game/game-lhc-all' . $aJsConfig['suffix'] . '?v=' . sha1_file('../userpublic' . $aJsConfig['base_path'] . 'game/game-lhc-all' . $aJsConfig['suffix']),

            'bootstrap'                => $sStaticPath.'assets/js/bootstrap.min.js',

        ),

    ),

);
