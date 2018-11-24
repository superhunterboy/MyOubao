@extends('l.home')

@section('title')
    我的奖金
    @parent
@stop

{{ style('ucenter')}}
@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border: 0px solid #CCC;
        background-color: #FFF;
    }
    .bonusgroup-title {
        border: none;
    }
    .table td {
        border-right: 1px solid #E6E6E6;
    }
    .table tbody tr:hover td {
        background: #FFF;
    }
    </style>
@stop




@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>



    <div class="page-content">
        <div class="g_main clearfix">

            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
            </div>


        
            <form action="" method="post" id="J-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="_method" value="PUT" />
            <div class="page-content-inner">
                 {{--
                <div class="bonusgroup-title">
                   
                    <table width="100%">
                        <tr>
                            <td>{{ $aUserPrizeSet['username'] }}<br /><span class="tip">用户名称</span></td>
                            <td>{{ $aUserPrizeSet['nickname'] }}<br /><span class="tip">用户昵称</span></td>
                            <td>{{ $aUserPrizeSet['is_agent_formatted'] }}<br /><span class="tip">用户类型</span></td>
                            <td>{{ $aUserPrizeSet['available_formatted'] }} 元<br /><span class="tip">可用余额</span></td>
                            @if ($aUserPrizeSet['is_agent'] == 0)
                            <td class="last">{{ $aUserPrizeSet['bet_max_prize'] }} 元<br /><span class="tip">奖金限额</span></td>
                            @endif
                        </tr>
                    </table>

                </div>
                 --}}
                {{--<div class="row-title">--}}
                    {{--奖金组--}}
                {{--</div>--}}
                {{--<div class="bonusgroup-game-type">--}}
                    {{--<ul class="clearfix">--}}
                        {{--@if (isset($aSeriesLotteries))--}}
                            {{--<ul class="clearfix gametype-row">--}}
                                {{--@foreach($aSeriesLotteries as $oSeriesLottery)--}}
                                    {{--{{pr($oSeriesLottery)}}--}}
                                    {{--<li class="name {{ ( isset($iCurrentLotteryId) and $iCurrentLotteryId == $oSeriesLottery['children'][0]['id'] ) ? 'current' : '' }}">--}}
                                        {{--<a href="{{ route('user-user-prize-sets.game-prize-set', $oSeriesLottery['children'][0]['id']) }}">--}}
                                            {{--<span class="name">{{$oSeriesLottery['friendly_name']}}</span>--}}
                                            {{--@if($oSeriesLottery['children'][0]['id'] == 2)--}}
                                                {{--<span class="group">{{ $iCurrentPrizeGroup-20 }}</span>--}}
{{--                                            @elseif($oSeriesLottery['children'][0]['id'] == 3 || $oSeriesLottery['children'][0]['id'] == 13 || $oSeriesLottery['children'][0]['id'] == 22)--}}
                                                {{--<span class="group">{{ $iCurrentPrizeGroup-30 }}</span>--}}
                                            {{--@endif--}}
                                            {{--@elseif($oSeriesLottery['children'][0]['id'] == 3 || $oSeriesLottery['children'][0]['id'] == 13)--}}
                                                {{--<span class="group">{{ $iCurrentPrizeGroup-30 }}</span>--}}
                                            {{--@else--}}
                                                {{--<span class="group">{{ $iCurrentPrizeGroup }}</span>--}}
                                            {{--@endif--}}
                                        {{--</a>--}}
                                    {{--</li>--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}
                        {{--@endif--}}
                    {{--</ul>--}}
                {{--</div>--}}
                <div class="clearfix">

                        <div class="bonus-current-cont">
                            <div class="inner">
                                @if($iCurrentLotteryId == 2)
                                    <div class="num">{{ $iCurrentPrizeGroup-20 }}</div>
{{--                                @elseif($iCurrentLotteryId == 13 || $iCurrentLotteryId == 22)--}}
                                @elseif($iCurrentLotteryId == 13 || in_array($iCurrentLotteryId,array(13)))
                                    <div class="num">{{ $iCurrentPrizeGroup-30 }}</div>
                                @else
                                    <div class="num">{{ $iCurrentPrizeGroup }}</div>
                                @endif
                                <div>当前奖金组</div>
                            </div>
                        </div>

                </div>
                @if($oLottery->is_trace_issue || $oLottery->id == 22)
                    @include('centerUser.userPrizeSet.SeriesPrizeSet')
                @else
                    @include('centerUser.userPrizeSet.lotteryPrizeSet')
                @endif


            </div>
            </form>
            <br />
            <br />
        </div>
    </div>



    @include('w.footer')
@stop






