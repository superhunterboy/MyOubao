<?php

$SeriesPrizeGroup = Series::find($oLottery->series_id)->min_commission_prize_group;
if($SeriesPrizeGroup >= $iCurrentPrizeGroup){
    $commissionPrizeGroup = 0;
}else{
    $commissionPrizeGroup = $iCurrentPrizeGroup - $SeriesPrizeGroup;
}

foreach ( $aLotteriesPrizeSetsTable as $Key => $oWayGroup )
{
    foreach ( $oWayGroup->children as $key1 => $oWay )
    {
        foreach ( $oWay->children as $key2 => $oMethod )
        {
            $oSeriesWay = SeriesWay::find($oMethod->series_way_id);
            $oPrizeLevels = PrizeLevel::where('basic_method_id','=', $oSeriesWay->basic_methods)->get(['id','level','rule', 'fixed_prize']);

            $oMethod->prize_count = $oPrizeLevels->count();

            $oCommission = CommissionConfig::where('way_id', '=', $oMethod->series_way_id)->orderBy('commission_rate', 'desc')->first(['commission_rate']);

            if($oCommission && $oCommission->commission_rate <= 0)
            {
                $oMethod->commission_rate = number_format(0,2);
            }else{
                $oMethod->commission_rate = number_format($commissionPrizeGroup/2000*100, 2);
            }

            foreach($oPrizeLevels as $oPrizeLevel)
            {
                if($oPrizeLevel->rule){
                    $oMethod->prize_level[] = $oPrizeLevel->rule.' => '. '1:'.($oPrizeLevel->fixed_prize -2)/2;
                }else{
                    $oMethod->prize_level[] = '1:'.($oPrizeLevel->fixed_prize -2)/2;
                }
            }
        }
    }
}

?>

<table class="table table-rowspan" width="100%" style="margin-top:10px;">
    <thead>
    <tr>
        <th>玩法群</th>
        <th>玩法组</th>
        <th>玩法</th>
        <th>奖级</th>
        <th>奖金</th>
        {{--<th>返点</th>--}}
    </tr>
    </thead>
    <tbody>
    @if (isset($aLotteriesPrizeSetsTable))
        @foreach ( $aLotteriesPrizeSetsTable as $oWayGroup )
            @foreach ( $oWayGroup->children as $key1 => $oWay )
                @foreach ( $oWay->children as $key2 => $oMethod )
                    <?php $aPrizes = explode(',', $oMethod->prize); rsort($aPrizes); ?>
                    @foreach ( $aPrizes as $key3 => $fPrizeLevel )
                        <tr>
                            @if ($key1 == 0 && $key2 == 0 && $key3 == 0)
                                <td rowspan="{{ $oWayGroup->count }}">{{ $oWayGroup->name_cn }}</td>
                            @endif
                            @if ($key2 == 0 && $key3 == 0)
                                <td rowspan="{{ $oWay->count }}">{{ $oWay->name_cn }}</td>
                            @endif
                            @if ($key3 == 0)
                                <td rowspan="{{ $oMethod->count }}">{{ $oMethod->name_cn }}</td>
                            @endif

                            <td>
                            @if(count($oMethod->prize_level) > 1)
                            @foreach ( $oMethod->prize_level as $key4 => $fPrizeLevel_1 )
                                    <span>{{ $fPrizeLevel_1 }}</span><br>
                            @endforeach
                            @endif
                            </td>

                            <td>{{ number_format($fPrizeLevel, 2) }}</td>
                            {{--<td>{{$oMethod->commission_rate}}% </td>--}}
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>