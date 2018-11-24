<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content="">    <meta name="keywords" content=""/>     <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        <title>
            每日首充
        </title>
        <script src="//cdn.bootcss.com/jquery/1.12.3/jquery.js?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224"></script>
        {{ script('base-all') }}
        {{ script('jquery.easing.1.3') }}
        <link media="all" type="text/css" rel="stylesheet" href="/events/activity/images_v2/zhengdian.css?v=zd-a149bb98e9f149e2aed7f2c158ed6cad-20170224">

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
                <div class="product_newin_bd product_newin_bd_4">
                    <div class="p_left">
                        <a href="{{route('activity.dailybet')}}">每日投注</a>
                        <a href="{{route('activity.dailysignin')}}">每日签到</a>
                        <a href="{{route('activity.newcharge')}}">新人首充</a>
                        <a href="{{route('activity.dailycharge')}}" class="active">每日首充</a>
                    </div>
                    <div class="p_right">
                        <div class="p_r_c1 p_r_c1_4">
                            <span class="p_r_c1left">活动时间　2017年01月01日至2018年01月01日</span>
                            <div class="p_r_c1right">
                                <img src="/events/activity/images_v2/product/p_img9.png" class="p_img1">
                                <span><strong>{{number_format($firstDeposit, 2)}}</strong>元</span>
                                今日首次充值金额

                            </div>
                        </div>
                        <div class="p_r_c2 p_r_c9">
                            <div class="p_r_c2tit">活动规则</div>
                            <div class="p_r_c2td" style="color: #efd0cd">一、每日首次充值相应金额并消费活动金额的30%即可领取奖金；<br>
                                二、奖金需在当日23:59:59前完成并领取，未领则为放弃；</div>
                        </div>
                        <div class="p_r_c10">
                            <ul>
                                <li>
                                    <img src="/events/activity/images_v2/product/p_img13_07.jpg">
                                    <div class="p_r_c10c">
                                        充值满 3888元 领取 <span>8元</span>
                                    </div>
                                    <a href="{{route('activity.dailychargereward', 8)}}">马上领取</a>
                                </li>
                                <li>
                                    <img src="/events/activity/images_v2/product/p_img14_07.jpg">
                                    <div class="p_r_c10c">
                                        充值满 18888元 领取 <span>38元</span>
                                    </div>
                                    <a href="{{route('activity.dailychargereward', 38)}}">马上领取</a>
                                </li>
                                <li class="li-r">
                                    <img src="/events/activity/images_v2/product/p_img15_07.jpg">
                                    <div class="p_r_c10c">
                                        充值满 38888元 领取 <span>88元</span>
                                    </div>
                                    <a href="{{route('activity.dailychargereward', 88)}}">马上领取</a>
                                </li>
                            </ul>
                        </div>
                        <div class="p_r_c2 p_r_c9">
                            <div class="p_r_c2tit">活动条款</div>
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

            $("body").css("background", "#1f0e2a url(/events/activity/images_v2/product/bg5_02.jpg) no-repeat center top 89px");
        })

    </script>

</html>
