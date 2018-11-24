<!DOCTYPE HTML>
<html lang="en-US">
<head>
<meta charset="UTF-8">
<title>奖金组详情</title>
{{ style('global')}}
{{ style('ucenter')}}
<style>
body {background-image:none;background-color: #EEE;}
.main-cont {width:980px;margin:10px auto;border:1px solid #EEE;background:#FFF;}
.table {width:80%;margin:20px auto;}
.table caption { text-align:center;font-size:14px;font-weight:bold;height:32px;line-height:32px;}
</style>
</head>
<body>



<div class="main-cont">


<div class="row-title">
    奖金组
</div>
<div class="bonusgroup-game-type">
    <ul class="clearfix">
        @if (isset($aSeriesLotteries))
            <ul class="clearfix gametype-row">
                @foreach($aSeriesLotteries as $oSeriesLottery)
                    {{--{{pr($oSeriesLottery)}}--}}
                    <li class="name {{ ( isset($iCurrentLotteryId) and $iCurrentLotteryId == $oSeriesLottery['children'][0]['id'] ) ? 'current' : '' }}">
                        <a href="{{ route('user-user-prize-sets.prize-set-detail', [$iCurrentPrizeGroup,$oSeriesLottery['children'][0]['id']]) }}">
                            <span class="name">{{$oSeriesLottery['friendly_name']}}</span>
                            @if($oSeriesLottery['children'][0]['id'] == 1)
                                <span class="group">{{ $iCurrentPrizeGroup }}</span>
                            @elseif($oSeriesLottery['children'][0]['id'] == 2)
                                <span class="group">{{ $iCurrentPrizeGroup-20 }}</span>
                            @elseif($oSeriesLottery['children'][0]['id'] == 3 || $oSeriesLottery['children'][0]['id'] == 13)
                                <span class="group">{{ $iCurrentPrizeGroup-30 }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </ul>
</div>
<div class="bonusgroup-title">
    <table width="100%">
        <tr>
            @if($iCurrentLotteryId == 1)
                <td>{{ $iCurrentPrizeGroup }}<br /><span class="tip">&nbsp;&nbsp;&nbsp;当前奖金&nbsp;&nbsp;&nbsp;</span></td>
            @elseif($iCurrentLotteryId == 2)
                <td>{{ $iCurrentPrizeGroup - 20 }}<br /><span class="tip">&nbsp;&nbsp;&nbsp;当前奖金&nbsp;&nbsp;&nbsp;</span></td>
            @elseif($iCurrentLotteryId == 13)
                <td>{{ $iCurrentPrizeGroup - 30 }}<br /><span class="tip">&nbsp;&nbsp;&nbsp;当前奖金&nbsp;&nbsp;&nbsp;</span></td>
            @endif
            @if (Session::get('is_agent'))
            <td class="last"> {{ $iWater.'%' }}<br /><span class="tip">预计平均返点率</span></td>
            @endif
        </tr>
    </table>
</div>
@include('centerUser.userPrizeSet.lotteryPrizeSet')


</div>

</body>
</html>