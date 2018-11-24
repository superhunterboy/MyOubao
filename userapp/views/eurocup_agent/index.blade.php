@extends('l.base-v4')

@section('title')
    2016法国欧洲杯-博狼娱乐
@stop

@section ('styles')
@parent
    {{ style('font-awesome')}}
    {{ style('ucenter') }}
    {{ style('proxy') }}
    {{ style('eurocup') }}
@stop


@section ('container')
    @include('w.header')
        <div class="page-content">
            <div class="container main clearfix">

                <div class="main-content">
                <form method="post" action="{{ route('eurocups.index') }}" id="coldSession">
    <div id="test">

        <div id="top" class="banner">
            <div class="container">
                <div class="row">
                    <div class="bonus">
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <a target="_blank" href="/user-recharges/netbank">
                            <span class="btn btn-deposit">
                                <span id="spnDeposit">
                                    <span>立即存款</span>
                                </span>
                            </span>
                        </a>
                        <a target="_blank" href="/jc/football ">
                            <span class="btn btn-bet">
                                <span id="spnSportbook">体育投注</span>
                            </span>
                        </a>
                        <div class="timerbg">
                        <span id="clockSpan" style="transform: skew(30deg); position: absolute; left: 23px; padding: 6px 0 0; font-size: 20px;">
                            <span id="clockSpandays"></span>天
                            <span id="clockSpanhours"></span>小时
                            <span id="clockSpanminutes"></span>分钟
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="main-menu" class="main-menu">
            <div class="container">
                <div class="row">
                    <ul class="lavaLampNoImage" id="menu">
                        <li><a class="current" href="/eurocups">2016法国欧洲杯</a>
                        </li>
                        <li><a>竞猜活动</a></li>
                        <li><a>赛事资料</a></li>

                    </ul>
                </div>
            </div>
        </div>

        <!-- About -->
        <div id="r-main" class="r-main">
        <section id="about" class="about">
            <div class="container">
                <div class="aboutbg">
                    <div class="intro">
                        <div class="content">
                            <div class="left"><h1><span class="orange">介绍</span></h1></div>
                            <div class="center"><p>
                                2016年法国欧洲杯是第十五届欧洲足球锦标赛。比赛由欧洲足球协会联盟管理，于2016年6月10日至7月10日在法国境内9座城市的12座球场内举行。这是继1960年和1984年后法国第三次举办这一赛事。</p>
                            </div>
                            <div class="right"><p>
                                比赛共有24支球队参赛，除东道主法国自动获得参赛资格以外，其他23支球队需要通过参加预选赛获得参赛资格；此届比赛也是欧足联决定将参赛名额由16队扩充至24队之后的首届欧洲杯。法国欧洲杯期间，总共在法国境内举办51场比赛角逐出冠军。</p>
                            </div>
                        </div>
                    </div>

                    <div style="clear:both"></div>

                    <div class="col-lg-12">
                        <div class="content">
                            <div class="stadium">
                                <div class="left">
                                    <h1><span class="orange">主办城市场地</span></h1>
                                    <p>
                                        2010年法国击败了意大利、土耳其等强劲对手获得了2016年欧洲杯的举办权后，北京时间2011年5月21日凌晨，欧足联官方网站公布了2016年法国欧洲杯（欧锦赛）的9个举办城市，
                                        包括：波尔多、朗斯、里尔、里昂、马赛、南锡、尼斯、巴黎和圣丹尼（巴黎郊区的城镇，法兰西大球场所在地）。而圣埃蒂安和图卢兹则被法足协联邦委员会列为后备城市。值得一提的
                                        是被确定的9个城市中，有很多耳熟能详的名字，包括波尔多和里昂等法国足坛有名的劲旅。</p>
                                </div>
                                <div class="right">
                                    <img src="/events/eurocup/images/stadium.png">
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div>

    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
</form>

                </div>
            </div>
        </div>


 @include('w.footer')



@stop

@section('end')
 <script type="text/javascript">
/*以下是欧洲杯活动页面*/
    $(function () {
        $("#menu").lavalamp({
            easing: "easeOutBack",
            speed: 200
        });
    });

    function getTimeRemaining(endtime) {
        var t = Date.parse(endtime) - Date.parse(new Date());
        var minutes = Math.floor((t / 1000 / 60) % 60);
        var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        var days = Math.floor(t / (1000 * 60 * 60 * 24));
        return {
            'total': t,
            'days': days,
            'hours': hours,
            'minutes': minutes,
        };
    }

    function initializeClock(id, endtime) {
        var daysSpan = document.getElementById('clockSpandays');
        var hoursSpan = document.getElementById('clockSpanhours');
        var minutesSpan = document.getElementById('clockSpanminutes');

        function updateClock() {
            var t = getTimeRemaining(endtime);
            daysSpan.innerHTML = t.days;
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);

            if (t.total <= 0) {
                clearInterval(timeinterval);
            }
        }

        updateClock();
        var timeinterval = setInterval(updateClock, 1000);
    }

    var deadline = 'Jun 10 23:59:59 UTC+0800 2016';
    initializeClock('clockSpan', deadline);

    $('#menu li:eq(1)').click(function () {
        $('#r-main').load('/events/eurocup/quiz_1.html');

    });
    $('#menu li:eq(2)').click(function () {
        $('#r-main').load('/events/eurocup/match_1.html');
    });

    //check form
     $(document).on('click',"#r-tj",function () {
         // alert(1);
            var options = {
                url: "{{ route('eurocups.index') }}",
                type: 'post',
                dataType: 'text',
                data: $("#coldSession").serialize(),
                success: function (data) {
                    if (data.length > 0){
                        var a = JSON.parse(data); 
                        switch (a.status) {
                            case '0':
                                window.location.href="/jc/football/hunhe";
                                break;
                        }
                        alert(a.msg);
                    }
                }
            };
            $.ajax(options);
            
        });
</script>
@parent

@stop


