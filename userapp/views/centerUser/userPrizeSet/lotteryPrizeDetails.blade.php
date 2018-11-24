<div class="row-title">
    奖金组
</div>
<div class="bonusgroup-game-type">
    <ul class="clearfix">
        @if (isset($oLotteriesPrizeSets))
        <ul class="clearfix gametype-row">
            @foreach ($oLotteriesPrizeSets as $key => $oLotteryPrizeSet)
            <li class="{{ ( isset($iCurrentLotteryId) and $iCurrentLotteryId == $oLotteryPrizeSet->lottery_id ) ? 'current' : '' }}">
                <a href="{{ route('user-user-prize-sets.game-prize-set', $oLotteryPrizeSet->lottery_id) }}"><span class="name">{{ $aLotteries[$oLotteryPrizeSet->lottery_id] }}</span><span class="group">{{ $oLotteryPrizeSet->prize_group }}</span></a>
            </li>
            @endforeach
        </ul>
        @endif
    </ul>
</div>
<div class="bonus-current-cont">
    <div class="inner">
        <div class="num">{{ $iCurrentPrizeGroup }}</div>
        <div>当前奖金组</div>
    </div>
</div>
@include('centerUser.userPrizeSet.lotteryPrizeSet')