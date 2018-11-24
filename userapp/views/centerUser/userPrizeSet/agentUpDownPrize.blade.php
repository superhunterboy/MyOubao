@if(!$isUpRole || !$isDownRole)
<div class="table-proxy-upgrade">
    <table width="100%" class="table">
        <tr>
            @foreach($aTopAgentFloatInfo as $aFloatInfo)
            @foreach($aFloatInfo as $iDay =>$val)
            <th><span class="daynum">{{$iDay}}</span>天@if($val['isUp'])升级@else降级@endif考核周期</th>
            @endforeach
            @endforeach
        </tr>
        @foreach($aTopAgentFloatInfo as $aFloatInfo)
        @foreach($aFloatInfo as $iDay =>$val)
        <td><span class="field">起始时间</span>{{$val['beginDate']}}</td>
        @endforeach
        @endforeach
        </tr>
        <tr>
            @foreach($aTopAgentFloatInfo as $aFloatInfo)
            @foreach($aFloatInfo as $iDay =>$val)
            <td><span class="field">截止时间</span>{{$val['endDate']}}</td>
            @endforeach
            @endforeach
        </tr>
        <tr>
            @foreach($aTopAgentFloatInfo as $aFloatInfo)
            @foreach($aFloatInfo as $iDay =>$val)
            <td><span class="field">目前销量</span><span class="num">@if($val['turnover']!==null){{number_format($val['turnover'],2)}}</span> 元@else----</span>@endif</td>
            @endforeach
            @endforeach
        </tr>
    </table>
</div>


<div class="table-proxy-upgrade table-proxy-upgrade-2">
    <table width="100%">
        <tr>
            <th colspan="4">欧豹升点保点销量要求</th>
        </tr>
        <tr>
            <td class="bg-1">奖金组</td>
            @foreach($aFloatRule as $isUp => $val)
            @foreach($val as $iDay => $aTurnover)
            <td class="bg-2">{{$iDay}}天@if($isUp)升点@else降点@endif总销量（万）</td>
            @endforeach
            @endforeach
        </tr>
        @foreach($prizeset as $iPrizeGroup)
        <tr>
            <td class="bg-3">{{$iPrizeGroup}}</td>
            @foreach($aFloatRule as $isUp => $aVal)
            @foreach($aVal as $iDay => $aTurnover)
            <td>{{$aTurnover[$iPrizeGroup]}}</td>
            @endforeach
            @endforeach
        </tr>
        @endforeach
    </table>
</div>

@endif