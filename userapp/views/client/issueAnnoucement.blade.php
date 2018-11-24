@extends('l.client.base')

@section('title')
开奖公告
@parent
@stop


@section ('styles')
@parent
{{ style('indexClient')}}
@stop

@section ('container')

@include('w.client.header')

<div class="r-main">
    <div class="gonggao">
        <div class="a">
            <i></i>
            <h4>开奖公告</h4>
            <h5>福彩、体彩、最快的开奖公告，第一时间为您提供准确，全面的开奖信息！</h5>
        </div>
        <div class="b">
            <ul class="title">
                <li class="b1">彩种名</li>
                <li class="i"></li>
                <li class="b2">期号</li>
                <li class="i"></li>
                <li class="b3">开奖时间</li>
                <li class="i"></li>
                <li class="b4">开奖号码</li>
                <li class="i"></li>
                <li class="b5">期数/每天</li>
                <li class="i"></li>
                <li class="b6">开奖频率</li>
                <li class="i"></li>
                <li class="b7">开奖详情</li>
                <li class="i"></li>
                <li class="b8">走势</li>
            </ul>
            <?php $i=1; ?>
            @foreach($aIssue as $k=>$v)
            <?php if($i % 2){$style = "even";}else{$style = "odd";} ?>
            @if('江苏快3' == $v['name'])
            <ul class="{{$style}} ball-1">
                <li class="b1">{{$v['name']}}</li>
                <li class="b2">{{$v['number']}}期</li>
                <li class="b3">{{$v['time']}}</li>
                <li class="b4">

                    @foreach($v['code'] as $j=>$c)
                    @foreach($c as $m=>$n)
                    <span class="num{{$n}}"></span>
                    @endforeach
                    @endforeach
                </li>
                <li class="b5">{{$v['count_issue_day']}}期</li>
                <li class="b6">{{$v['encode_time']}}</li>
                <li class="b7"><a href="{{ route('bets.bet', $v['id']) }}"></a></li>
                <li class="b8"><a href="{{route('user-trends.trend-view',$v['id'])}}">走势图</a></li>
            </ul>
            @elseif('安徽快3' == $v['name'])
            <ul class="{{$style}} ball-1">
                <li class="b1">{{$v['name']}}</li>
                <li class="b2">{{$v['number']}}期</li>
                <li class="b3">{{$v['time']}}</li>
                <li class="b4">
                    @foreach($v['code'] as $j=>$c)
                    @foreach($c as $m=>$n)
                    <span class="num{{$n}}"></span>
                    @endforeach
                    @endforeach
                </li>
                <li class="b5">{{$v['count_issue_day']}}期</li>
                <li class="b6">{{$v['encode_time']}}</li>
                <li class="b7"><a href="{{ route('bets.bet', $v['id']) }}"></a></li>
                <li class="b8"><a href="{{route('user-trends.trend-view',$v['id'])}}">走势图</a></li>
            </ul>
            @elseif('北京PK10' == $v['name'])
            <ul class="{{$style}} ball-3">
                <li class="b1">{{$v['name']}}</li>
                <li class="b2">{{$v['number']}}期</li>
                <li class="b3">{{$v['time']}}</li>
                <li class="b4">
                    @foreach($v['code'] as $j=>$c)
                    @foreach($c as $m=>$n)
                    <span>{{$n}}</span>
                    @endforeach
                    @endforeach
                </li>
                <li class="b5">{{$v['count_issue_day']}}期</li>
                <li class="b6">{{$v['encode_time']}}</li>
                <li class="b7"><a href="{{ route('bets.bet', $v['id']) }}"></a></li>
                <li class="b8"><a href="{{route('user-trends.trend-view',$v['id'])}}">走势图</a></li>
            </ul>
            @else
            <ul class="{{$style}} ball-2">
                <li class="b1">{{$v['name']}}</li>
                <li class="b2">{{$v['number']}}期</li>
                <li class="b3">{{$v['time']}}</li>
                <li class="b4">
                    @foreach($v['code'] as $j=>$c)
                    @foreach($c as $m=>$n)
                    <span>{{$n}}</span>
                    @endforeach
                    @endforeach
                </li>
                <li class="b5">{{$v['count_issue_day']}}期</li>
                <li class="b6">{{$v['encode_time']}}</li>
                <li class="b7"><a href="{{ route('bets.bet', $v['id']) }}"></a></li>
                <li class="b8"><a href="{{route('user-trends.trend-view',$v['id'])}}">走势图</a></li>
            </ul>
            @endif
            <?php ++$i; ?>
            @endforeach
            <?php $style = $style == 'odd' ? "even" : "odd"; ?>
            <ul class="{{$style}} jincai">
                <li class="b1">竞彩足球</li>
                <li class="b2">不定期</li>
                <li class="b3">每天开奖</li>
                <li class="b4">
                    @foreach($aMethodList as $oMethod)
                    @if(!empty(Session::get('username')) && !empty($oMethod->name))
                    <a href="{{{ route('jc.result', ['football', $oMethod->identifier]) }}}">{{$oMethod->name }}<a>
                        @else
                        <a href="">{{$oMethod->name}}<a>
                            @endif
                            @endforeach
                </li>
                <!--<li class="b5">85期</li>-->
                <!--<li class="b6">10分钟</li>-->
                <!--<li class="b7"><a href="/jc/football"></a></li>-->
                <!--<li class="b8"><a href="/jc/football">走势图</a></li>-->
            </ul>



        </div>
    </div>
</div>






@include('w.client.footer')



{{--
@if ($bFirstLogin)
<script type="text/javascript">
    (function(){
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
        popWindowNew.show(data);
    })();
</script>
@endif
--}}
@stop
