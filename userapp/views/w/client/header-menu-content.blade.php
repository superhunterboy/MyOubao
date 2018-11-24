
        <li class="cc">
            <a href="/">首页</a>
        </li>
        <li class="i"></li>
        <li class="gc cc">
            购彩大厅
            <i class="upDown"></i>
            <div class="gcdt">
                <div class="up"></div>
                <div class="main">
                    <div class="main-l">
                        <div class="zq">
                            <a href="{{ route('jc.match_list', ['football', 'hunhe']) }}">混合过关<i></i></a>
                            <a href="{{ route('jc.match_list', ['football', 'win-handicapWin']) }}">让球/胜平负<i></i></a>
                            <a href="{{ route('jc.match_list', ['football', 'haFu']) }}">半全场</a>
                            <a href="{{ route('jc.match_list', ['football', 'correctScore']) }}">比分</a>
                            <a href="{{ route('jc.match_list', ['football', 'totalGoals']) }}">总进球</a>
                            <a href="{{ route('jc.match_list', ['football', 'single']) }}">单关</a>
                        </div>
                        <div class="gp">
                            <a href="{{ route('bets.bet', 1) }}">重庆时时彩<i></i></a>
                            <a href="{{ route('bets.bet', 3) }}">黑龙江时时彩</a>
                            <a href="{{ route('bets.bet', 6) }}">新疆时时彩<i></i></a>
                            <a href="{{ route('bets.bet', 7) }}">天津时时彩</a>
                            <a href="{{ route('bets.bet', 2) }}">山东11选5</a>
                            <a href="{{ route('bets.bet', 8) }}">江西11选5</a>
                            <a href="{{ route('bets.bet', 9) }}">广东11选5</a>
                            <a href="{{ route('bets.bet', 21) }}">江苏快3<i></i></a>
                            <a href="{{ route('bets.bet', 22) }}">安徽快3</a>
                            <a href="{{ route('bets.bet', 53) }}">北京PK10</a>
                            <a href="{{ route('bets.bets', 20) }}">幸运28</a>
                             @if(!empty(Session::get('username')))
                            <a href="{{ route('bets.bet', 23) }}">博猫1分彩</a>
                            <a href="{{ route('bets.bet', 11) }}">博猫2分彩</a>
                            <a href="{{ route('bets.bet', 24) }}">博猫5分彩</a>
                            <a href="{{ route('bets.bet', 12) }}">博猫11选5</a>
                            <a href="{{ route('bets.bet', 20) }}">博猫快3</a>

                            @endif
                        </div>
                        <div class="sz">

                            <a href="{{ route('bets.bet', 13) }}">福彩3D</a>
                            <a href="{{ route('bets.bet', 14) }}">体彩P3/5</a>
                        </div>

                    </div>
                    @if(!empty(Session::get('username'))) 
                    <div class="main-r">
                        <div class="bjl">
                            <ul>
                                <li>娱乐场</li>
                                <li>普通场</li>
                                <li>高级场</li>
                            </ul>
                            <div class="a-2">
                                <a href="{{ route('bets.bet', 44) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 45) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 46) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 47) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 48) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 49) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 50) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 51) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 52) }}">75秒/期</a>
                            </div>
                        </div>
                        <div class="sb">
                            <ul>
                                <li>娱乐场</li>
                                <li>普通场</li>
                                <li>高级场</li>
                            </ul>
                            <div class="a-2">
                                <a href="{{ route('bets.bet', 34) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 35) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 37) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 38) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 39) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 40) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 41) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 42) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 43) }}">75秒/期</a>
                            </div>
                        </div>
                        <div class="lhd">
                            <ul>
                                <li>娱乐场</li>
                                <li>普通场</li>
                                <li>高级场</li>
                            </ul>
                            <div class="a-2">
                                <a href="{{ route('bets.bet', 25) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 26) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 27) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 28) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 29) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 30) }}">75秒/期</a>
                                <a href="{{ route('bets.bet', 31) }}">45秒/期</a>
                                <a href="{{ route('bets.bet', 32) }}">60秒/期</a>
                                <a href="{{ route('bets.bet', 33) }}">75秒/期</a>
                            </div>
                        </div>
                        <div class="d-21">
                            <ul>
                                <li></li>
                                <li>游戏厅</li>
                                <li></li>
                            </ul>
                            <div class="a-2">
                                <a href="/casino/bet/8001/1">娱乐场</a>
                                <a href="/casino/bet/8001/2">普通场</a>
                                <a href="/casino/bet/8001/3">高级场</a>
                            </div>


                        </div>
                    </div>
                    @endif

                </div>

            </div>
        </li>
        <li class="i"></li>
        <li class="cc"><a href="{{ route('jc.groupbuy', 'football') }}">合买大厅</a> </li>
        <li class="i"></li>
        <li class="cc zx">彩票资讯
            <div class="cpzx">
                <div class="up"></div>
                <div class="main">
                    <?php  $oLotteryInfoCate = LotteryCategory::getJcAndNumber(); ?>
                    @if(isset($oLotteryInfoCate) && $oLotteryInfoCate)
                    @foreach($oLotteryInfoCate as $k=>$v)
                    <a href="{{route('lotteryinformation.index', [$v->id, $v->name])}}"  target="_blank">{{$v->name}}</a>
                   @endforeach
                   @endif
                </div>
            </div>
        </li>
        <li class="i"></li>
        <li class="sj cc">数据图表
            <div class="sjtb">
                <div class="up"></div>
                <div class="main">
                    <a href="{{route('user-trends.trend-view',1)}}">重庆时时彩</a>
                    <a href="{{route('user-trends.trend-view',53)}}">北京PK10</a>
                    <a href="{{route('user-trends.trend-view',2)}}">山东11选5</a>
                    <a href="{{route('user-trends.trend-view',3)}}">黑龙江时时彩</a>
                    <a href="{{route('user-trends.trend-view',6)}}">新疆时时彩</a>
                    <a href="{{route('user-trends.trend-view',7)}}">天津时时彩</a>
                    <a href="{{route('user-trends.trend-view',8)}}">江西11选5</a>
                    <a href="{{route('user-trends.trend-view',9)}}">广东11选5</a>
                    <a href="{{route('user-trends.trend-view',22)}}">安徽快3</a>
                    <a href="{{route('user-trends.trend-view',21)}}">江苏快3</a>
                    <a href="{{route('user-trends.trend-view',13)}}">福彩3D</a>
                    <a href="{{route('user-trends.trend-view',14)}}">排列3/5</a>


                </div>

            </div>
        </li>
        <li class="i"></li>
        <li class="cc"><a href="{{route('issueannoucement.index')}}" target="_blank">开奖公告</a> </li>
        <li class="i"></li>
        <li class="cc"><a href="http://info.sporttery.cn/livescore/fb_livescore.html" target="_blank">比分直播</a></li>
        <li class="i"></li>
        <li class="kh cc">客户端
            <div class="khd">
                <div class="up"></div>
                <div class="main">
                    <a href="/mobile" target="_blank">手机端</a>
                    <a href="/pc-client/index.html" target="_blank">电脑端</a>
                </div>

            </div>
        </li>

        <!--<li class="i"></li>-->
        <!--<li class="cc last"><i class="gift"></i>优惠活动</li>-->