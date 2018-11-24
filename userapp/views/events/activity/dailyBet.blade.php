<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content="">    <meta name="keywords" content=""/>     <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        <title>
            每日投注
        </title>
        <script src="//cdn.bootcss.com/jquery/1.12.3/jquery.js?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224"></script>
        {{ script('base-all') }}
        {{ script('jquery.easing.1.3') }}
        <link media="all" type="text/css" rel="stylesheet" href="/events/activity/images_v2/zhengdian.css">
        <link media="all" type="text/css" rel="stylesheet" href="/events/activity/images_v2/layui.css?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224">
    </head>
    <body style="background:#e6dfd8 url(/events/activity/images_v2/bg_02.jpg) repeat-x">
        @include('w.notification')
        <div class="head">
            <div class="header_con">
                <div class="log_main">
                    <a href="/"><img src="/dist/images/logo.png" class="l_logo" width="170" height="47"></a>
                    <div class="log_right1 log_rig1">
                    </div>
                </div>
            </div>
        </div>
        <div class="content_all">
            <div class="main">
                <div class="product_newin_bd">
                    <div class="p_left">
                        <a href="{{route('activity.dailybet')}}" class="active">每日投注</a>
                        <a href="{{route('activity.dailysignin')}}">每日签到</a>
                        <a href="{{route('activity.newcharge')}}">新人首充</a>
                        <a href="{{route('activity.dailycharge')}}">每日首充</a>
                    </div>
                    <div class="p_right">
                        <div class="p_r_c1">
                            <span class="p_r_c1left">活动时间　2017年01月01日至2018年01月01日</span>
                            <div class="p_r_c1right">
                                <img src="/events/activity/images_v2/product/p_img3.png" class="p_img1">
                                <span><strong>{{number_format($fTodayTurnover, 2)}}</strong>元</span>
                                今日有效投注
                            </div>
                        </div>
                        <div class="p_r_c2">
                            <div class="p_r_c2tit" style="text-align: center;">活动规则</div>
                            <div class="p_r_c2td">一、您昨日有效投注金额为<span>【{{number_format($fYesterdayTurnover, 2)}}】</span>元，可领取<span>【{{number_format($fReward, 2)}}】</span>元<br>
                                二、昨日投注奖金需在次日23:59:59前领取（1次），未领则为放弃；</div>
                        </div>
                        <div class="p_r_c3">
                            <ul>
                                <li>
                                    <div class="p_r_c3_c">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥500.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>3.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 3)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_ca">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥1000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>5.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 5)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cb">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥2000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>8.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 8)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="li-r">
                                    <div class="p_r_c3_c p_r_c3_cc">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥3000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>10.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 10)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cd">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥5000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>20.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 20)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_ce">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥10000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>30.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 30)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cf">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥30000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>90.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 90)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="li-r">
                                    <div class="p_r_c3_c p_r_c3_cg">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥50000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>150.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 150)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cc">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥100000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>300.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 300)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cb">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥300000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>1000.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 1000)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="p_r_c3_c p_r_c3_cd">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥600000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>2000.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 2000)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                                <li class="li-r">
                                    <div class="p_r_c3_c p_r_c3_ce">
                                        <div class="p_r_c3_c1">
                                            <span class="p_r_c3_c1r"><span>￥1000000.00</span>元</span>投注金额
                                        </div>
                                        <div class="p_r_c3_c1 p_r_c3_c2">
                                            <span class="p_r_c3_c1r"><span>￥</span><strong>3000.00</strong>元</span>奖励
                                        </div>
                                        <a href="{{route('activity.dailybetreward', 3000)}}">点击领取</a>
                                        <div class="p_r_c3_c3">
                                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="p_r_c4">
                            <div class="p_r_c2tit" style="text-align: center;">活动条款</div>
                            <div class="p_r_c2td">
                                一、活动投注不得超过该玩法总投注数的70%，即定位胆玩法每个位置不能超过7注、二码玩法不能超过70注、三码玩法不能超过700注、四星玩法<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;不能超过7000注、五星玩法不能超过70000注、全包玩法不计入有效投注。如发现违规投注情况，均视作放弃本次活动。
                                <br><br>
                                二、活动存款及流水独立计算，参加其他活动存款及流水需另行计算。<br><br>
                                三、若发现同一用户利用多个账号（相同/相似IP地址，相同银行卡，相同银行账户，相同电脑等）重复领取、对打套利、恶意刷返点套利等任何违<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;规投注和对打情况，欧豹游戏将拒绝赠送活动奖金、没收优惠及其所有相关盈利，并冻结账号。
                                <br><br>
                                四、本活动最终解释权和裁决权归欧豹游戏所有，欧豹游戏保留修改、暂停、终止该优惠活动等所有权利。<br><br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        @include("w.footer")
    </body>





    <script>
        $(function () {

            $("body").css("background", "#ffe4b6 url(/events/activity/images_v2/product/bg2_02.jpg) no-repeat center top 89px");
        })

    </script>

</html>
