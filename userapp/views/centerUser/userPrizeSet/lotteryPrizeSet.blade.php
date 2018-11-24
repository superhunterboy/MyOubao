<div class="row-title">
    玩法奖金详情
</div>

<table class="table table-rowspan" width="100%" style="margin-top:10px;">
    <thead>
        <tr>
            <th>玩法群</th>
            <th>玩法组</th>
            <th>玩法</th>
            <th>奖级</th>
            <th>奖金</th>
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
                        <td>{{ $oMethod->count > 1 ? $aPrizeLevel[$key3] : '' }}</td>
                        <td>{{ number_format($fPrizeLevel, 2) }}</td>
                    </tr>
                    @endforeach
                @endforeach
            @endforeach
        @endforeach
    @endif
    </tbody>
</table>