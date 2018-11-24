@extends('l.home')

@section('title')
    我的奖金
@parent
@stop

@section('main')
<div class="nav-bg">
    <div class="title-normal">
        我的奖金
    </div>
</div>

<div class="content">
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
    @include('centerUser.userPrizeSet.lotteryPrizeDetails')
</div>
@stop

@section('end')
@parent
<script>
    (function($) {

    })(jQuery);
</script>
@stop