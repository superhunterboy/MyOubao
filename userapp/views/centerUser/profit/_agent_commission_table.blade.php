<table width="100%" class="table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>所属组</th>
            <th>日期</th>
            <th>有效投注金额</th>
            <th>返点总额</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($oAgentSumPerDay) && $oAgentSumPerDay)
        <!-- current agent -->
        <tr>
            <td>{{ $oAgentSumPerDay->username }}</td>
            <td>{{ $oAgentSumPerDay->user_type_formatted }}</td>
            <td>{{ $oAgentSumPerDay->date }}</td>
            <td>{{ number_format($oAgentSumPerDay->team_turnover, 4) }}</td>
            <td>{{ number_format($oAgentSumPerDay->commission, 4) }}</td>
        </tr>
        @endif
        <!-- current agent -->
        @foreach ($datas as $data)
        <?php
            // $fTotalDepositNum  = $data->is_agent ? $data->team_deposit : $data->deposit;
            // $fTotalWithdrawNum = $data->is_agent ? $data->team_withdrawal : $data->withdrawal;
            $fTotalTurnOverNum   = $data->is_agent ? $data->team_turnover   : $data->turnover;
            $fTotalPrizeNum      = $data->is_agent ? $data->team_prize      : $data->prize;
            $fTotalProfitNum     = $data->is_agent ? $data->team_profit     : $data->profit;
            $fTotalCommissionNum = $data->is_agent ? $data->team_commission : $data->commission;
        ?>
    </tbody>
    <tfoot>
        <tr>
            <td>{{ $data->username }}</td>
            <td>{{ $data->user_type_formatted }}</td>
            <td>{{ $data->date }}</td>
            <td>{{ number_format($fTotalTurnOverNum, 4) }}</td>
            <td>{{ number_format($fTotalCommissionNum, 4) }}</td>
        </tr>
        @endforeach
    </tfoot>
</table>

