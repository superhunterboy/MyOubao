<div class="global-top">
    <div class="g_main clearfix">
        <a title="博狼娱乐首页" class="logo" href="{{ route('home') }}">博狼娱乐</a>


        <div id="J-global-top-menu-games" class="menu-games">
            <span class="menu-games-text">全部游戏</span>
            <i class="menu-games-bg"></i>

            
            <div class="menu-games-panel">

                <?php $iCountSeries = count($aSeriesLotteries); ?>
                @foreach ($aSeriesLotteries as $key => $oSeriesLotteries)
                <div class="cell {{ 'cell-' . ( ($key + 1) == $iCountSeries ? 'last' : ($key + 1) ) }}">
                        <div class="title">{{ $oSeriesLotteries['friendly_name'] }}</div>
                        <ul>
                        @if (isset($oSeriesLotteries['children']))
                            @foreach ($oSeriesLotteries['children'] as $oLottery)
                            <li><a href="{{ route('bets.bet', $oLottery['id']) }}">{{ $oLottery['name'] }}</a></li>
                            @endforeach
                        @endif
                        </ul>
                </div>
                @endforeach
            </div>
            
            {{--
            <div class="cell cell-1">
                    <div class="title">时时彩</div>
                    <ul>
                        <li><a href="http://www.bomao.com/bets/bet/1">重庆时时彩</a></li>
                        <li><a href="http://www.bomao.com/bets/bet/3">黑龙江时时彩</a></li>
                        <li><a href="http://www.bomao.com/bets/bet/5">江西时时彩</a></li>
                        <li><a href="http://www.bomao.com/bets/bet/6">新疆时时彩</a></li>
                        <li><a href="http://www.bomao.com/bets/bet/7">天津时时彩</a></li>
                        <li><a href="http://www.bomao.com/bets/bet/11">博猫时时彩</a></li>
                    </ul>
            </div>
            <div class="cell cell-last">
                    <div class="title">11选5</div>
                        <ul>
                            <li><a href="http://www.bomao.com/bets/bet/2">山东11选5</a></li>
                            <li><a href="http://www.bomao.com/bets/bet/8">江西11选5</a></li>
                            <li><a href="http://www.bomao.com/bets/bet/9">广东11选5</a></li>
                            <li><a href="http://www.bomao.com/bets/bet/10">重庆11选5</a></li>
                            <li><a href="http://www.bomao.com/bets/bet/12">博猫11选5</a></li>
                        </ul>
                    </div>
            </div>
            --}}
            

        </div>



        <ul class="menu">
            <li class="active"><a title="代理中心" href="{{ route('home') }}">代理中心</a></li>
        </ul>


        <div class="buttons">
            <a class="btn" id="livechatbutton" href="javascript:hj5107.openChat();"><span title="联系客服" class="ico ico-service">客服</span></a>
            <a class="btn" href="{{ route('users.password-management')}}" id="J-button-user-info"><span title="个人信息" class="ico ico-user"></span></a>
            <a class="btn btn-msg" href="{{ route('station-letters.index') }}"><span title="消息" class="ico ico-msg"></span><i class="num">{{ $unreadMessagesNum }}</i></a>
            <div class="user-info" id="J-panel-user-info">
                <div class="row">{{ Session::get('nickname') }}</div>
                <div class="row row-group">
                    <span class="num">{{ Session::get('user_prize_group') }}</span><br />
                    @if(Session::get('user_forever_prize_group') == Session::get('user_prize_group'))永久@else临时@endif奖金组
                </div>
                <div class="row row-gray">最新登陆时间 <br /> {{ Session::get('signin_at') }}</div>
                <div class="row">
                    <a href="{{ route('logout') }}" class="btn"> 退出登录 </a>
                </div>
                <div class="sj"></div>
            </div>
        </div>
    </div>
</div>

@section('end')
@parent
        <!--[if lt IE 6]>
            <script>
            (function($){
                var dom = $('#J-global-top-menu-games');
                dom.hover(function(){
                    dom.addClass('menu-games-hover');
                },function(){
                    dom.removeClass('menu-games-hover');
                });
            })(jQuery);
            </script>
        <![endif]-->
<script>
@if(Session::get('user_id'))
//客服代码：
//姓名|性别|固定电话|手机|邮箱|地址|公司名|MSN|QQ|会员ID|会员等级 |（此处按照上面约定字段直接传送；如未登陆，传空）会员等级（1:VIP会员 0:普通会员）
var hjUserData="{{urlencode(Session::get('username'))}}|||||{{get_client_ip()}}||||{{Session::get('id')}}|0|";
@endif

(function($){
    //头部用户下拉面板
    var userinfoTimer;
    $('#J-button-user-info').hover(function(){
        clearTimeout(userinfoTimer);
        $('#J-panel-user-info').show();
    },function(){
        clearTimeout(userinfoTimer);
        userinfoTimer = setTimeout(function(){
            $('#J-panel-user-info').hide();
        }, 300);
    });
    $('#J-panel-user-info').hover(function(){
        clearTimeout(userinfoTimer);
    },function(){
        $('#J-panel-user-info').hide();
    });

})(jQuery);
</script>

@stop

