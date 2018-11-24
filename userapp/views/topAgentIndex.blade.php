@extends('l.base')

@section('title') 首页-浪里淘金 年年有鱼@stop

@section ('styles')
@parent
{{ style('index') }}
@stop

@section ('container')
@include('w.header')
<div class="g_33 clearfix">
    <div class="main-content">
        @include('adTemp.3')

        <ul class="link-quick link-quick-proxy">
            <li><a href="{{ route('users.index') }}" class="record-user">用户管理</a></li>
            <li><a href="{{ route('user-profits.commission') }}" class="record-rebate">代理返点</a></li>
            <li><a href="{{ route('user-bonuses.index') }}" class="record-dividend">代理分红</a></li>
            <li class="last"><a href="{{ route('user-profits.index') }}" class="record-profit">代理盈亏</a></li>
        </ul>

        <div class="proxy-img">
            <ul class="clearfix">
                <li>@include('adTemp.17')</a></li>
                <li class="last">@include('adTemp.18')</li>
            </ul>
        </div>


       <div class="proxy-statics">
            <ul>
                <li>
                    <div class="title">
                        <div class="text">下级总人数</div>
                        <div class="num">{{ $iTeamUserNumTotal }}</div>
                        <div class="unit">人</div>
                    </div>
                    <div class="cont-img-ico cont-img-ico-1"></div>
                    <!-- <div class="cont">
                        <div class="row">玩家：{{-- $iTeamUserNumPlayer --}}人</div>
                        <div class="row">一代：{{-- $iTeamUserNumAgent --}}人</div>
                        <a href="{{-- route('users.index') --}}" class="btn">查看完整报表</a>
                    </div> -->
                </li>

                <li>
                    <div class="title">
                        <div class="text">团队余额</div>
                        <div class="num">{{ number_format($iTeamAccountCount, 2) }}</div>
                        <div class="unit">元</div>
                    </div>
                    <div class="cont-img-ico cont-img-ico-2"></div>
                    <!-- <div class="cont">
                        <div class="row">昨日：820,291.00 元</div>
                        <div class="row">前日：820,291.00 元</div>
                        <a href="#" class="btn">查看完整报表</a>
                    </div> -->
                </li>

                <li class="last">
                    <div class="title">
                        <div class="text">昨日销量</div>
                        <div class="num">{{ number_format($fTeamTurnOver, 2) }}</div>
                        <div class="unit">元</div>
                    </div>
                    <div class="cont-img-ico cont-img-ico-3"></div>
                    <!-- <div class="cont">
                        <div class="row">玩家销量：820,291.00 元</div>
                        <div class="row">一代销量：820,291.00 元</div>
                        <a href="#" class="btn">查看完整报表</a>
                    </div> -->
                </li>

            </ul>
        </div>


    </div>
    <div class="main-sider">

        <div class="user-info-proxy">
            <div class="info">
                <a href="{{ route('users.personal') }}" class="user-info-img-big">
                    <img src="/assets/images/index/user-img-big.png" width="66" height="66" />
                </a>
                <p class="row sayhello">{{ Session::get('nickname') }}</p>
                <p class="row tips">幸运，从博狼开始～</p>
            </div>
            <div class="money">
                <?php
                // $iShowMoney = Session::get('is_agent') ? $fTeamTurnOver : $fAvailable;
                // // TODO 因为页面上无法显示完整的千万以上资金, 暂时做此处理
                // $iShowMoney <= 9999999.9 or $iShowMoney = 9999999.9;
                ?>
                {{ number_format($fAvailable, 2) }}
            </div>
            <ul class="link-big">
                <li><a href="{{ route('users.accurate-create') }}">开户</a></li>
                <li><a class="last" href="{{ route('user-withdrawals.withdraw') }}">提现</a></li>
            </ul>
        </div>

        <div class="user-info" >
            <div class="proxy-cont" id="J-panel-proxy">
                <div class="title">
                    我的奖金组
                </div>
                <div class="proxy-inner">
                    <div class="proxy-group">
                        <div class="group-inner">当前奖金组 <span class="num" id="J-proxy-current-num">{{$currentPrizeSet}}</span></div>
                    </div>
                    <div class="proxy-main">
                        <span class="text-num-min" id="J-proxy-min">
                            {{ $topAgentMinPrizeSet > ($currentPrizeSet - 1) ? $topAgentMinPrizeSet : $currentPrizeSet - 1 }}
                        </span>
                        <span class="text-num-max"  id="J-proxy-max">
                            {{ $topAgentMaxPrizeSet < ($currentPrizeSet + 1) ? $topAgentMaxPrizeSet : $currentPrizeSet + 1 }}
                        </span>

                        @if(!$isDownRole)
                            @if(isset($aRuleData['down']['prizeset'][$currentPrizeSet]))
                                <span class="text-name-down">保级差额</span>
                                <span class="num-down">
                                    {{ number_format(($aRuleData['down']['prizeset'][$currentPrizeSet]*PrizeSetFloatRule::NUMBER_WAN - $fDownTotalTurnoverBewteenDays), 2) }}
                                </span>
                            @else
                                <span class="text-name-down">保级差额</span>
                                <span class="num-down">
                                    0.00
                                    <span class="proxy-ico-down"></span>
                                </span>
                            @endif
                        @endif
                        @if(!$isUpRole)
                        @if(isset($aRuleData['up']['prizeset'][$currentPrizeSet+1]))
                        <span class="text-name-up">晋升差额</span>
                        <span class="num-up">
                            {{ number_format($aRuleData['up']['prizeset'][$currentPrizeSet+1]*PrizeSetFloatRule::NUMBER_WAN - $fUpTotalTurnoverBewteenDays, 2) }}
                            <span class="proxy-ico-up"></span>
                        </span>
                        @else
                        <span class="text-name-up">晋升差额</span>
                        <span class="num-up">
                            0.00
                            <span class="proxy-ico-up"></span>
                        </span>
                        @endif
                        @endif

                        <span class="num-current" id="J-proxy-current">
                            <span class="num">{{$currentPrizeSet}}</span>
                        </span>

                    </div>
                    <ul class="proxy-text">
                        @if(!$isUpRole)
                        <li><span class="up">升点</span>考核日期:<span class="down">{{$sUpDate}}</span>,距离考核还有<span class="day">{{$iUpDay}}</span>天</li>
                        @endif
                        @if(!$isDownRole)
                        <li><span class="down">保点</span>考核日期:<span class="down">{{$sDownDate}}</span>,距离考核还有<span class="day">{{$iDownDay}}</span>天</li>
                        @endif
                    </ul>
                </div>
            </div>



            <div class="news">
                <div class="title clearfix">
                    <div class="left">平台公告</div>
                    <div class="right">
                        <a href="{{ route('announcements.index') }}">更多</a>
                    </div>
                </div>
                <ul class="list">
                <?php $iCountAnnouncements = count($aLatestAnnouncements); ?>
                    @foreach ($aLatestAnnouncements as $key => $oAnnouncement)
                    <li class="{{ $key+1 == $iCountAnnouncements ? 'last' : '' }}">
                        <a href="{{ route('announcements.view', $oAnnouncement->id) }}">{{ $oAnnouncement->title }}</a>
                    </li>
                    @endforeach
                </ul>
            </div>


        </div>

    </div>
</div>

@include('w.footer-v3')
@stop


@section('scripts')
@parent
{{ script('bomao.Tab')}}
{{ script('bomao.Slider')}}
@stop

@section('end')
@parent
<script>
    (function ($) {
        @if ($bFirstLogin)
            // debugger;
            var popWindowNew = new bomao.Message();
            var data = {
                title          : '提示',
                content        : "<i class=\"ico-waring\"></i><p class=\"pop-text\">{{ __('_basic.first-login-tip') }}</p>",
                isShowMask     : true,
                closeIsShow    : true,
                closeButtonText: '关闭',
                closeFun       : function() {
                    this.hide();
                }
            };
            // debugger;
            popWindowNew.show(data);
            // alert('您的登录密码是由您的上级设置的，请修改为您自己的登录密码!');
        @endif
        var isAgent = {{ Session::get('is_agent') ? 1 : 0 }};
        //banner滚动图
        var slider = new bomao.Slider({par: '#J-slider', triggers: '.slider-num li', panels: '.slider-pic li', sliderDirection: 'left', sliderIsCarousel: true});

        //金额滚动
        var ernie = new bomao.Ernie({'dom': $('#J-money-nums li'), 'height': 50, 'length': 10, 'callback': function () {
            }});

        //奖金组
        (function () {
            var proxyDom = $('#J-panel-proxy'), currDom, currNumDom, current = 0, minv,maxv,
                left = 0,top = 0;
            if (proxyDom.size() > 0) {
                currDom = $('#J-proxy-current');
                current = Number($('#J-proxy-current-num').text());
                minv = Number($('#J-proxy-min').text());
                maxv = Number($('#J-proxy-max').text());
                currNumDom = currDom.find('.num');
                if(current == minv){
                    left = 8;
                    top = 101;
                    $('#J-proxy-min').hide();
                }else if(current == maxv){
                    left = 229;
                    top = 3;
                    $('#J-proxy-max').hide();
                }else{
                    left = 120;
                    top = 50;
                }
                currDom.animate({left: left, top: top}, {easing: 'easeOutQuart', duration: 2500});

            }
        })();



    })(jQuery);
</script>
@stop

