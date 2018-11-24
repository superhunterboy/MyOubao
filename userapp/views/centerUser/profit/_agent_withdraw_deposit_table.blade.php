<table width="100%" class="table">
    <thead>
    <tr>
        <th>用户名</th>
        <th>用户属性</th>
        <th>团队人数</th>
        <th>{{ custom_order_by('team_deposit', '充值总额', 'asc') }}</th>
        <th>
            {{ custom_order_by('team_withdrawal', '提现总额', 'asc') }}</th>
        <th>可用余额</th>
    </tr>
    </thead>
    
    @if(isset($oAgentSumPerDay))
    <tbody>
        <tr>
            <td colspan="2">所选区间合计&gt;</td>
            <td><span>{{ $oAgentSumPerDay->team_numbers}}</span></td>
            <td><span>{{ $oAgentSumPerDay->team_deposit_formatted}}</span></td>
            <td><span>{{ $oAgentSumPerDay->team_withdrawal_formatted}}</span></td>
            <td><span>{{ $oAgentSumPerDay->available_formatted}}</span></td>

        </tr>
    </tbody>
    @endif
    @if (isset($oSelfProfit) && is_object($oSelfProfit) && isset($oSelfProfit->username))
    <tbody>
        <tr>
            <td>{{$oSelfProfit->username}}</td>
            <td >自己&gt;</td>
            <td><span>{{ $oSelfProfit->team_numbers}}</span></td>
            <td><span>{{ $oSelfProfit->deposit_formatted }}</span></td>
            <td><span>{{ $oSelfProfit->withdrawal_formatted}}</span></td>
            <td><span>{{ $oSelfProfit->available_formatted}}</span></td>

        </tr>
        <tr><td colspan='10'>直属下级盈亏明细</td></tr>
    </tbody>
    @endif
    <tbody>
    @foreach ($datas as $data)
        <tr>
            <td>{{ $data->username }}</td>
            <td>下级</td>
            <td><span>{{ $data->team_numbers}}</span></td>
            <td><span>{{ $data->team_deposit_formatted}}</span></td>
            <td><span>{{ $data->team_withdrawal_formatted}}</span></td>
            <td><span>{{ $data->available_formatted}}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>

