<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="description" content="">    <meta name="keywords" content=""/>     <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon"/>
        <title>
            新人首充
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
                <div class="product_newin_bd product_newin_bd_3">
                    <div class="p_left">
                        <a href="{{route('activity.dailybet')}}">每日投注</a>
                        <a href="{{route('activity.dailysignin')}}">每日签到</a>
                        <a href="{{route('activity.newcharge')}}" class="active">新人首充</a>
                        <a href="{{route('activity.dailycharge')}}">每日首充</a>
                    </div>
                    <div class="p_right">
                        <div class="p_r_c1 p_r_c1_3">
                            <span class="p_r_c1left">活动时间　2017年01月01日至2018年01月01日</span>
                            <div class="p_r_c1right">
                                <img src="/events/activity/images_v2/product/c_p_3.png" class="p_img1" style="left: -40px;">
                                <script language="javascript" type="text/javascript">

                                    var interval = 1000;
                                    function ShowCountDown(year, month, day, hor, mnt, scd, divName)
                                    {
                                        var cc = document.getElementById(divName);
                                        if (year == 0) {
                                            cc.innerHTML = '<b>无首充记录</b>';
                                            return true;
                                        }
                                        if (year == 1) {
                                            cc.innerHTML = '<b>活动结束</b>';
                                            return true;
                                        }
                                        var now = new Date();
                                        var endDate = new Date(year, month - 1, day, hor, mnt, scd);
                                        var leftTime = endDate.getTime() - now.getTime();
                                        var leftSecond = parseInt(leftTime / 1000);
                                        var hour = Math.floor((leftSecond) / 3600);
                                        var minute = Math.floor((leftSecond - hour * 3600) / 60);
                                        var second = Math.floor(leftSecond - hour * 3600 - minute * 60);
                                        if (leftSecond > 0) {
                                            cc.innerHTML = '<b>' + hour + '</b>' + '时' + '<b>' + minute + '</b>' + '分' + '<b>' + second + '</b>' + '秒';
                                        } else {
                                            cc.innerHTML = '<b>活动结束</b>';
                                        }

                                    }
                                        <?php
                                        if(is_object($oFirstDeposit)){
                                            $dt = Carbon::parse($oFirstDeposit->created_at);
                                            $dt->addDays(2);
                                            ?>
                                    window.setInterval(function () {
                                        ShowCountDown({{$dt->year}}, {{$dt->month}}, {{$dt->day}}, {{$dt->hour}}, {{$dt->minute}}, {{$dt->second}}, 'divDown');
                                    }, interval);
                                    <?php
                                    }else{
                                    ?>
                                                                            window.setInterval(function () {
                                        ShowCountDown(0, 0, 0, 0, 0, 0, 'divDown');
                                    }, interval);
                                    <?php 
                                    }
                                    if(is_object($oFirstDeposit)){
                                        $fAmount = formatNumber($oFirstDeposit->amount, 2);
                                    }else{
                                        $fAmount = 0;
                                    }
                                    
                                    ?>
                                </script>
                                <div id="divDown"></div>
                            </div>
                        </div>
                        <div class="p_r_c6">
                            <div class="p_r_c6tit">活动规则</div>
                            <div class="p_r_c6td">
                                一、新注册用户，首次充值满100元并达到相应的投注量，48小时内即可领取该奖金；<br>
                                二、用户需在充值后48小时内完成并领取相应奖金，未领则为放弃；<br>
                                三、注册日起7日内未充值的新用户为放弃此活动；
                            </div>
                        </div>
                        <div class="p_r_c7">
                            <ul>
                                @if(count($newChargePrize) <= 0)
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>500.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">8.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 8)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>1000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">18.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 18)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>2000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">38.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 38)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>5800.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">58.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 58)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>8000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">88.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 88)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>10000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">118.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 118)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>15000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">168.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 168)}}">点击领取</a>
                                </li>
                                <li>
                                    <div class="p_r_c7tit">新注册用户</div>
                                    <div class="p_r_c7td1">
                                        投注量：<span>{{$fTurnover}}</span><br>
                                        充值量：<span>{{$fAmount}}/100</span><br>
                                        要求量：<span>18000.00</span>
                                    </div>
                                    <div class="p_r_c7td1">
                                        奖　励：<strong style="font-size: 22px;color: #ff0202;margin-top: 5px">218.00</strong>
                                    </div>
                                    <a href="{{route('activity.newchargereward', 218)}}">点击领取</a>
                                </li>
                                @else
                                活动已完成
                                @endif
                            </ul>
                        </div>
                        <div class="p_r_c6 p_r_c8">
                            <div class="p_r_c6tit">活动条款</div>
                            <div class="p_r_c6td">
                                一、活动投注不得超过该玩法总投注数的70%，即定位胆玩法每个位置不能超过7注、二码玩法不能超过70注、三码玩法不能超过700注、四星<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;玩法不能超过7000注、五星玩法不能超过70000注、全包玩法不计入有效投注。如发现违规投注情况，均视作放弃本次活动。
                                <br><br>
                                二、活动存款及流水独立计算，参加其他活动存款及流水需另行计算。<br><br>
                                三、若发现同一用户利用多个账号（相同/相似IP地址，相同银行卡，相同银行账户，相同电脑等）重复领取、对打套利、恶意刷返点套利等任何<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;违规投注和对打情况，欧豹游戏将拒绝赠送活动奖金、没收优惠及其所有相关盈利，并冻结账号。
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
            $("body").css("background", "#ffefd2 url(/events/activity/images_v2/product/bg4_02.jpg) no-repeat center top 89px");
        })
    </script>

</html>
