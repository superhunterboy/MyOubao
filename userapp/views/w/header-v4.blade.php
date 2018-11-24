
@include('w.top')



<div class="header-v5">
    <div class="container clearfix" id="J-header-container">
        <div class="left">
            <a href="{{ route('home') }}" class="logo"></a>
        </div>

        <div class="right">
            <ul class="menu">
                <li class="it"><a class="mu-big" href="{{ route('home') }}">首页</a></li>
                <li class="it it-lottery">
                    <a class="mu-big" href="#">彩票</a>
                    <div class="panel-menu">
                        <span class="p-sj"></span>
                        <div class="row">
                            <div class="menu-table">
                                <div class="menu-row">
                                    <div class="menu-cell">
                                        <div class="sprite sprite-ssc"></div>
                                    </div>
                                    <div class="menu-cell">
                                        <ul class="j-list clearfix">
                                            <li><a href="{{ route('bets.bet', 62) }}">腾讯分分彩<i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 23) }}">欧豹分分彩<i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 11) }}">欧豹2分彩<i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 24) }}">欧豹5分彩<i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 1) }}">重庆时时彩 <i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 6) }}">新疆时时彩</a></li>
                                            <li><a href="{{ route('bets.bet', 7) }}">天津时时彩</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-row">
                                    <div class="menu-cell">
                                        <div class="sprite sprite-11x5"></div>
                                    </div>
                                    <div class="menu-cell">
                                        <ul class="j-list clearfix">
                                            <li><a href="{{ route('bets.bet', 12) }}">欧豹11选5<i class="ico ico-new"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 2) }}">山东11选5</a></li>
                                            <li><a href="{{ route('bets.bet', 8) }}">江西11选5</a></li>
                                            <li><a href="{{ route('bets.bet', 9) }}">广东11选5</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-row">
                                    <div class="menu-cell">
                                        <div class="sprite sprite-k3"></div>
                                    </div>
                                    <div class="menu-cell">
                                        <ul class="j-list clearfix">
                                            <li><a href="{{ route('bets.bet', 20) }}">欧豹快3<i class="ico ico-hot"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 21) }}">江苏快3</a></li>
                                            <li><a href="{{ route('bets.bet', 22) }}">安徽快3</a></li>
                                            <li><a href="{{ route('bets.bet', 63) }}">广西快3<i class="ico ico-new"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-row">
                                    <div class="menu-cell">
                                        <div class="sprite sprite-pk10"></div>
                                    </div>
                                    <div class="menu-cell">
                                        <ul class="j-list clearfix">
                                            <li><a href="{{ route('bets.bet', 53) }}">北京PK10 <i class="ico ico-new"></i></a></li>
                                            <li><a href="{{ route('bets.bet', 60) }}">PK10分分彩<i class="ico ico-new"></i></a></li> 
                                            <li><a href="javascript:;" title="敬请期待" class="no-link">幸运飞艇PK10</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="menu-row">
                                    <div class="menu-cell">
                                        <div class="sprite sprite-qt"></div>
                                    </div>
                                    <div class="menu-cell">
                                        <ul class="j-list clearfix">
                                            <li><a href="{{ route('bets.bet', 13) }}">福彩3D</a></li>
                                            <li><a href="{{ route('bets.bet', 14) }}">体彩P3/5</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="it it-casino">
                    <a class="mu-big" href="#">电子娱乐</a>
                    <div class="panel-menu">
                        <span class="p-sj"></span>
                        <div class="row">
                            <div class="title">百家乐 传统经典 奖金高</div>
                            <div class="cont clearfix">
                                <div class="cell cell-">
                                    <div class="t">娱乐场 <span class="sm">单期限赔5万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 44) }}">娱乐1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 45) }}" class="last">娱乐2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 46) }}" class="last">娱乐3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-2">
                                    <div class="t">普通场 <span class="sm">单期限赔10万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 47) }}">普通1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 48) }}" class="last">普通2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 49) }}" class="last">普通3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-3">
                                    <div class="t">高级场 <span class="sm">单期限赔20万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 50) }}">高级1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 51) }}" class="last">高级2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 52) }}" class="last">高级3桌75秒/期</a>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="title">骰宝专区</div>
                            <div class="cont clearfix">
                                <div class="cell cell-1">
                                    <div class="t">娱乐场 <span class="sm">单期限赔5万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 25) }}">娱乐1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 26) }}" class="last">娱乐2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 27) }}" class="last">娱乐3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-2">
                                    <div class="t">普通场 <span class="sm">单期限赔10万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 28) }}">普通1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 29) }}" class="last">普通2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 30) }}" class="last">普通3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-3">
                                    <div class="t">高级场 <span class="sm">单期限赔20万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 31) }}">高级1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 32) }}" class="last">高级2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 33) }}" class="last">高级3桌75秒/期</a>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="title">龙虎之斗,谁与争锋</div>
                            <div class="cont clearfix">
                                <div class="cell cell-1">
                                    <div class="t">娱乐场 <span class="sm">单期限赔5万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 34) }}">娱乐1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 35) }}" class="last">娱乐2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 37) }}" class="last">娱乐3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-2">
                                    <div class="t">普通场 <span class="sm">单期限赔10万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 38) }}">普通1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 39) }}" class="last">普通2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 40) }}" class="last">普通3桌75秒/期</a>-->
                                    </div>
                                </div>
                                <div class="cell cell-3">
                                    <div class="t">高级场 <span class="sm">单期限赔20万</span></div>
                                    <div class="link">
                                        <!--<a href="{{ route('bets.bet', 41) }}">高级1桌45秒/期</a>-->
                                        <a href="{{ route('bets.bet', 42) }}" class="last">高级2桌60秒/期</a>
                                        <!--<a href="{{ route('bets.bet', 43) }}" class="last">高级3桌75秒/期</a>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="it it-lottery">
                    <a class="mu-big" href="{{route('activity.index')}}?category_id=11">优惠活动</a>
                </li>
                <li class="it it-lottery">
                    <a class="mu-big" href="{{route('announcements.index')}}?category_id=2">平台公告</a>
                </li>
            </ul>
        </div>
    </div>
</div>

@section('end')
@parent
<script type="text/javascript">
    (function () {
        var button = $('.J-button-money-update'), locked = false, CLS = 'ico-update-animation';
        button.click(function () {
            if (locked) {
                return;
            }
            $.ajax({
                url: '/users/user-monetary-info',
                dataType: 'json',
                beforeSend: function () {
                    locked = true;
                    button.addClass(CLS);
                },
                success: function (data) {
                    if (Number(data['isSuccess']) == 1) {
                        var monetary = bomao.util.formatMoney(Number(data['data']['available']));
                        $('.J-text-money-value').text(monetary);
                    }
                },
                complete: function () {
                    locked = false;
                    button.removeClass(CLS);
                }
            });
        });
    })();


    //点击隐藏余额
    $('.J-button-control-hidden').click(function (e) {
        e.preventDefault();
        $('.J-button-control-hidden').each(function () {
            var el = $(this), par = el.parent(), CLS = 'menu-user-control-hidden', allItems = $('.panel-text-user-balance');
            if (par.hasClass(CLS)) {
                par.removeClass(CLS);
                el.text('隐藏');
                allItems.show();
                $.removeCookie('user-balance-ishidden');
            } else {
                par.addClass(CLS);
                el.text('显示');
                allItems.hide();
                $.cookie('user-balance-ishidden', 1);
            }
        });

    });
    (function () {
        var button = $('.J-button-control-hidden'), par = button.parent(), CLS = 'menu-user-control-hidden', allItems = $('.panel-text-user-balance');
        if ($.cookie('user-balance-ishidden')) {
            par.addClass(CLS);
            button.text('显示');
            allItems.hide();
        } else {
            par.removeClass(CLS);
            button.text('隐藏');
            allItems.show();
        }
    })();


    setTimeout(function () {
        var dom = $('#J-header-container').find('.lantern');
        dom.show().fadeIn(function () {
            dom.removeClass().addClass('lantern animated swing');
        });
    }, 1200);


</script>
@stop






