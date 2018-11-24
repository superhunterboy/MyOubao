<table width="100%" class="table">
    <thead>
    <tr>
        <th>用户名</th>
        <th>用户属性</th>
        <th>
            {{ custom_order_by('team_turnover', '投注总额', 'asc') }}
        </th>
        <th>
            {{ custom_order_by('team_prize', '派奖总额', 'asc') }}</th>

        <th>{{ custom_order_by('team_bet_commission', '投注返点', 'asc') }}</th>
        <th>
            {{ custom_order_by('team_profit', '游戏盈亏', 'asc') }}
        </th>
        <th>{{ custom_order_by('team_commission', '代理返点', 'asc') }}</th>
        <th>  {{ custom_order_by('team_dividend', '促销红利', 'asc') }}</th>
        <!--<th><a href="#" class="control-sort control-sort-up">净盈亏</a></th> -->
        <th>{{ custom_order_by('team_profit', '净盈亏', 'asc') }}</th>
    </tr>
    </thead>
    
    @if(isset($oAgentSumPerDay))
    <tbody>
        <tr>
            <td colspan="2">所选区间合计&gt;</td>
            <td><span>{{ $oAgentSumPerDay->team_turnover_formatted }}</span></td>
            <td><span>{{ number_format($oAgentSumPerDay->team_prize,4) }}</span></td>
            <td><span>{{ number_format($oAgentSumPerDay->team_bet_commission,4)}}</span></td>

            <?php $gameProfit = $oAgentSumPerDay->team_prize + $oAgentSumPerDay->team_bet_commission - $oAgentSumPerDay->team_turnover; ?>
            <td><span class="{{ $gameProfit < 0 ? 'c-red' : 'c-green' }}">{{ ($gameProfit > 0 ? '+' : '') }}{{ number_format($gameProfit, 4) }}</span></td>

            <td><span>{{ number_format($oAgentSumPerDay->team_commission,4)}}</span></td>
            <td><span>{{ number_format($oAgentSumPerDay->team_dividend, 4) }}</span></td>

            <td><span class="{{ $oAgentSumPerDay->team_profit < 0 ? 'c-red' : 'c-green' }}">{{ ($oAgentSumPerDay->team_profit > 0 ? '+' : '') }}{{ number_format($oAgentSumPerDay->team_profit,4) }}</span></td>

        </tr>
    </tbody>
    @endif
    @if (isset($oSelfProfit) && is_object($oSelfProfit) && isset($oSelfProfit->username))
    <tbody>
        <tr>
            <td>{{$oSelfProfit->username}}</td>
            <td >自己&gt;</td>
            <td><span>{{ $oSelfProfit->turnover_formatted }}</span></td>
            <td><span>{{number_format( $oSelfProfit->prize,4) }}</span></td>
            <td><span>{{ number_format($oSelfProfit->bet_commission,4)}}</span></td>

            <?php $gameProfit = $oSelfProfit->prize + $oSelfProfit->bet_commission - $oSelfProfit->turnover; ?>
            <td><span class="{{ $gameProfit < 0 ? 'c-red' : 'c-green' }}">{{ ($gameProfit > 0 ? '+' : '') }}{{ number_format($gameProfit, 4) }}</span></td>
            <td><span>{{ number_format($oSelfProfit->commission,4)}}</span></td>
            <td><span>{{ number_format($oSelfProfit->dividend,4) }}</span></td>

            <td><span class="{{ $oSelfProfit->profit < 0 ? 'c-red' : 'c-green' }}">{{ ($oSelfProfit->profit > 0 ? '+' : '') }}{{ number_format($oSelfProfit->profit,4) }}</span></td>

        </tr>
        <tr><td colspan='10'>直属下级盈亏明细</td></tr>
    </tbody>
    @endif
    <tbody>
    @foreach ($datas as $data)
        <tr>
            <td>{{ $data->username }}</td>
            <td>下级</td>
            <td><span>{{ $data->team_turnover_formatted }}</span></td>
            <td><span>{{ number_format($data->team_prize,4) }}</span></td>
            <td><span>{{ number_format($data->team_bet_commission,4) }}</span></td>

            <?php $gameProfit = $data->team_prize + $data->team_bet_commission - $data->team_turnover; ?>
            <td><span class="{{ $gameProfit < 0 ? 'c-red' : 'c-green' }}">{{ $gameProfit > 0 ? '+' : '' }}{{ number_format($gameProfit, 4) }}</span></td>
            <td><span>{{ number_format($data->team_commission,4) }}</span></td>
            <td><span>{{ number_format($data->team_dividend, 4) }}</span></td>

            <td><span class="{{ $data->team_profit < 0 ? 'c-red' : 'c-green' }}">{{ ($data->team_profit > 0 ? '+' : '') }}{{number_format( $data->team_profit,4) }}</span></td>
        </tr>
    @endforeach
    </tbody>
</table>

