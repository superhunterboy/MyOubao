<ul class="tab-title tab-title-small clearfix">
    <li class="current"><a href="javascript:void(0);"><span>选择奖金组套餐</span></a></li>
    <li><a href="javascript:void(0);"><span>自定义奖金组</span></a></li>
</ul>
<ul class="tab-panels">
    <li class="tab-panel-li panel-current">
        <div class="bonus-group">
            <ul class="clearfix" id="J-panel-group">
                @foreach ($oPossiblePrizeGroups as $oPrizeGroup)
                <li class="">
                    <div class="bonus"><strong class="data-bonus">{{ $oPrizeGroup->classic_prize }}</strong>当前奖金</div>
                    <div class="rebate"><strong class="data-feedback"> {{number_format(($currentUserPrizeGroup-$oPrizeGroup->classic_prize)/2000*100,2).'%'}}</strong>预计平均返点率</div>
                    <a target="_blank" href="{{ route('user-user-prize-sets.prize-set-detail', $oPrizeGroup->classic_prize) }}">查看奖金组详情</a>
                    <input type="button" class="btn button-selectGroup" value="选 择" data-groupid="{{ $oPrizeGroup->id }}" />
                </li>
                @endforeach
            </ul>
            <ul class="clearfix" id="J-panel-group-agent" style='display: none'>
                @foreach ($oPossibleAgentPrizeGroups as $oPrizeGroup)
                <li class="">
                    <div class="bonus"><strong class="data-bonus">{{ $oPrizeGroup->classic_prize }}</strong>当前奖金</div>
                    <div class="rebate"><strong class="data-feedback">  {{number_format(($currentUserPrizeGroup-$oPrizeGroup->classic_prize)/2000*100,2).'%'}}</strong>预计平均返点率</div>
                    <a target="_blank" href="{{ route('user-user-prize-sets.prize-set-detail', $oPrizeGroup->classic_prize) }}">查看奖金组详情</a>
                    <input type="button" class="btn button-selectGroup" value="选 择" data-groupid="{{ $oPrizeGroup->id }}" />
                </li>
                @endforeach
            </ul>
        </div>
    </li>


    <li class="tab-panel-li">
        <input type="hidden" name="series_id" id="J-input-custom-type" value="{{ Input::old('series_id') }}" />
        <input type="hidden" name="lottery_id" id="J-input-custom-id" value="{{ Input::old('lottery_id') }}" />
        <div class="bonusgroup-game-type">
            <div id="J-group-gametype-panel">
                <ul class="clearfix gametype-row">
                    <li class="item-all"     style="display: none;">
                        <a class="item-game" data-id="all_lotteries" data-itemType="all" href="javascript:void(0);" id="all_lotteries"><span class="name">全部彩种</span></a>
                    </li>
                </ul>
                @if (isset($aLotteriesPrizeSets))
                @foreach ($aLotteriesPrizeSets as $aSeries)
                <ul class="clearfix gametype-row">
                    <li class="item-all-series {{ ( isset($iCurrentLotteryId) and $iCurrentLotteryId == $aSeries['id'] ) ? 'current' : '' }}" >
                        <a class="item-game" data-id="{{ $aSeries['id'] }}" data-itemType="all" href="javascript:void(0);"><span class="name">{{ '全部' . $aSeries['friendly_name'] }}</span></a>
                    </li>
                    @foreach ($aSeries['children'] as $key => $aLotteryPrizeSet)
                    <li class="item-lottery {{ ( isset($iCurrentLotteryId) and $iCurrentLotteryId == $aLotteryPrizeSet['id'] ) ? 'current' : '' }}" >
                        <a class="item-game" data-id="{{ $aLotteryPrizeSet['id'] }}" data-itemType="game" href="javascript:void(0);"><span class="name">{{ $aLotteryPrizeSet['name'] }}</span><span class="group">{{$iPlayerMinPrizeGroup}}</span></a>
                    </li>
                    @endforeach
                </ul>
                @endforeach
                @endif
            </div>
        </div>
        <input type="hidden" id="J-input-bonusgroup-gameid" value="" />
        <input type="hidden" id="J-input-lottery-json" name="lottery_prize_group_json" />
        <input type="hidden" id="J-input-series-json" name="series_prize_group_json" />
        <div class="bonusgroup-title">
            <table width="100%">
                <tr>
                    <td class="last">
                        <div class="bonus-setup">
                            <div class="bonus-setup-title">
                                <strong>设置奖金</strong>
                                <span class="tip">奖金组一旦上调后则无法降低，请谨慎操作。</span>
                            </div>
                            <div class="bonus-setup-content">
                                <div class="slider-range" onselectstart="return false;">

                                    <div class="slider-range-sub" id="J-slider-minDom"></div>
                                    <div class="slider-range-add" id="J-slider-maxDom"></div>

                                    <div class="slider-range-wrapper" id="J-slider-cont">
                                        <div class="slider-range-inner" style="width:0;" id="J-slider-innerbg"></div>
                                        <div class="slider-range-btn" style="left:0;" id="J-slider-handle"></div>
                                    </div>
                                    <div class="slider-range-scale">
                                        <span class="small-number" id="J-slider-num-min">1800</span>
                                        <span class="big-number" id="J-slider-num-max">1960</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td><input type="text" class="input w-1" style="text-align:center;" value="" id="J-input-custom-bonus-value" />
                        <br><span class="tip">&nbsp;&nbsp;&nbsp;<a href="#" target="_blank" data-path="{{ route('user-user-prize-sets.prize-set-detail') }}" id="J-link-bonusgroup-detail">查看详情</a>&nbsp;&nbsp;&nbsp;</span>
                    </td>
                    <td class="last"><strong id="J-custom-feedback-value">--</strong><br><span class="tip">预计平均返点率</span></td>
                </tr>
            </table>
        </div>
    </li>
</ul>