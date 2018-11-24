<table width="100%" class="table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>{{ custom_order_by('deposit', '充值总额', 'asc') }}</th>
            <th>{{ custom_order_by('withdrawal', '提现总额', 'asc') }}</th>
            <th>{{ custom_order_by('turnover', '销售总额', 'asc') }}</th>
            <th>{{ custom_order_by('commission', '返点总额', 'asc') }}</th>
            <th>{{ custom_order_by('prize', '中奖总额', 'asc') }}</th>
            <th>  {{ custom_order_by('bonus', '活动奖金总额', 'asc') }}</th>
            <th>{{ custom_order_by('profit', '游戏总盈亏', 'asc') }}</th>
        </tr>
    </thead>

    <tbody>
        <?php
        $fTotalDeposit = 0;
        $fTotalWithdrawal = 0;
        $fTotalTurnover = 0;
        $fTotalCommission = 0;
        $fTotalPrize = 0;
        $fTotalBonus = 0;
        $fTotalProfit = 0;
        ?>
        <tr>
            <?php
            unset($search_params['parent_user']);
            $search_params['username'] = $aUserProfit['username'];
            ?>
            <td><a href="{{route('team-profits.index',$search_params) }}">{{ $aUserProfit['username'] }}</a></td>
            <td>{{ number_format($aUserProfit['total_deposit'],6) }}</td>
            <td>{{ number_format($aUserProfit['total_withdrawal'],6) }}</td>
            <td>{{ number_format($aUserProfit['total_turnover'],6) }}</td>
            <td>{{ number_format($aUserProfit['total_commission'],6) }}</td>
            <td>{{ number_format($aUserProfit['total_prize'],6) }}</td>
            <td>{{ number_format($aUserProfit['total_bonus'],6) }}</td>
            <td><span class="{{ $aUserProfit['total_profit'] > 0 ? 'c-red' : 'c-green' }}">{{ ($aUserProfit['total_profit'] < 0 ? '' : '+') }}{{ number_format($aUserProfit['total_profit'],6) }}</span></td>
        </tr>
        <tr>
            <td colspan="8">直属下级盈亏明细</td>
        </tr>
        <?php
        $fTotalDeposit += $aUserProfit['total_deposit'];
        $fTotalWithdrawal += $aUserProfit['total_withdrawal'];
        $fTotalTurnover += $aUserProfit['total_turnover'];
        $fTotalCommission += $aUserProfit['total_commission'];
        $fTotalPrize += $aUserProfit['total_prize'];
        $fTotalBonus += $aUserProfit['total_bonus'];
        $fTotalProfit += $aUserProfit['total_profit'];
        ?>
        @foreach ($datas as $data)
        <tr>
            <?php
            unset($search_params['parent_user']);
            $search_params['username'] = $data->username;
            ?>
            <td><a href="{{route('team-profits.index',$search_params) }}">{{ $data->username }}</a></td>
            <td>{{ $data->deposit }}</td>
            <td>{{ $data->withdrawal }}</td>
            <td>{{ $data->turnover_formatted }}</td>
            <td>{{ $data->commission_formatted }}</td>
            <td>{{ $data->prize_formatted }}</td>
            <td>{{ $data->bonus_formatted }}</td>
            <td><span class="{{ $data->profit > 0 ? 'c-red' : 'c-green' }}">{{ ($data->profit < 0 ? '' : '+') }}{{ $data->profit_formatted }}</span></td>
        </tr>
        <?php
        $fTotalDeposit += $data->deposit;
        $fTotalWithdrawal += $data->withdrawal;
        $fTotalTurnover += $data->turnover;
        $fTotalCommission += $data->commission;
        $fTotalPrize += $data->prize;
        $fTotalBonus += $data->bonus;
        $fTotalProfit += $data->profit;
        ?>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td>本页资本变动</td>
            <td>{{ number_format($fTotalDeposit,6) }}</td>
            <td>{{ number_format($fTotalWithdrawal,6) }}</td>
            <td>{{ number_format($fTotalTurnover,6) }}</td>
            <td>{{ number_format($fTotalCommission,6) }}</td>
            <td>{{ number_format($fTotalPrize,6) }}</td>
            <td>{{ number_format($fTotalBonus,6) }}</td>
            <td><span class="{{ $fTotalProfit > 0 ? 'c-red' : 'c-green' }}">{{ ($fTotalProfit < 0 ? '' : '+') }}{{ number_format($fTotalProfit,6) }}</span></td>
        </tr>
    </tfoot>
</table>
