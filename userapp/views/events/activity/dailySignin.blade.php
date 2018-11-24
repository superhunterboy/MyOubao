<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content="">    <meta name="keywords" content=""/>     <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        <title>
            每日签到
        </title>
        <script src="//cdn.bootcss.com/jquery/1.12.3/jquery.js?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224"></script>
        {{ script('base-all') }}
        {{ script('jquery.easing.1.3') }}
        <link media="all" type="text/css" rel="stylesheet" href="/events/activity/images_v2/zhengdian.css?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224">
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
                <div class="product_newin_bd product_newin_bd1">
                    <div class="p_left">
                        <a href="{{route('activity.dailybet')}}">每日投注</a>
                        <a href="{{route('activity.dailysignin')}}" class="active">每日签到</a>
                        <a href="{{route('activity.newcharge')}}">新人首充</a>
                        <a href="{{route('activity.dailycharge')}}">每日首充</a>
                    </div>
                    <div class="p_right">
                        <div class="p_r_c1 p_r_c1_2">
                            <span class="p_r_c1left">活动时间：2017年01月01日至2018年01月01日</span>
                            <div class="p_r_c1right">
                                <img src="/events/activity/images_v2/product/p_img5.png" class="p_img1">
                                <span><strong>{{number_format($fTodayTurnover, 2)}}</strong>元</span>
                                今日有效投注
                            </div>
                        </div>
                        <div class="p_r_c5">
                            <div class="p_r_c5_c1">
                                <div class="p_r_c5_c1tit">活动规则</div>
                                <div class="p_r_c5_c1bd">
                                    一、最少消费额度：玩家必须在平台消费1000元后才能进行签到；<br>
                                    二、签到天数：玩家必须连续签到7天方可进行领奖，领取奖金后次日可重新参与，签到中断需从第一天重新开始签到；<br>
                                    三、每日只能签到一次（如果玩家消费1000元时进行签到，当日后期消费到10000时也不可改签）<br>
                                    四、奖金领取规则：领取奖金=签到消费奖金总额 x 返奖率，（如：每日消费5000元 x 连续7天 x 奖率0.5%=奖金5000元 x 7 x 0.5%=175）<br>
                                    五、最高奖金1888元<br>
                                    六、签到领奖时将会取整如最终奖励888.88元则取整为888元<br>
                                </div>
                            </div>
                            <div class="p_r_c5_c2">
                                <ul>
                                    <li>
                                        <div class="p_r_c5_c2a">第一天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(1, $aDailyData))<a href="{{route('activity.punchin', 1)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第二天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(2, $aDailyData))<a href="{{route('activity.punchin', 2)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第三天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(3, $aDailyData))<a href="{{route('activity.punchin', 3)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第四天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(4, $aDailyData))<a href="{{route('activity.punchin', 4)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第五天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(5, $aDailyData))<a href="{{route('activity.punchin', 5)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第六天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(6, $aDailyData))<a href="{{route('activity.punchin', 6)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                    <li>
                                        <div class="p_r_c5_c2a">第七天</div>
                                        <img src="/events/activity/images_v2/product/p_img9_13.jpg">
                                        @if(!key_exists(7, $aDailyData))<a href="{{route('activity.punchin', 7)}}">点击签到</a>
                                        @else<a>已签到</a>
                                        @endif
                                    </li>
                                </ul>

                                <div class="p_r_c1right" style="background: #f5f6e6; float: left; width: 230px;">
                                    <span><strong>{{number_format($fTotalTurnover, 2)}}</strong>元</span>
                                    累计签到金额
                                </div>
                            </div>
                            <div class="p_r_c5_c1" style="margin-bottom: 0">
                                <div class="p_r_c5_c1tit">活动条款</div>
                                <div class="p_r_c5_c1bd">
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
        </div>

        @include("w.footer")

    </body>


    <script>
$(function () {
    $("body").css("background", "#ffe4b6 url(/events/activity/images_v2/product/bg3_02.jpg) no-repeat center top 89px");
})

    </script>

</html>
