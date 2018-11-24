


<table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>投注总额</th>
            <th>有效投注</th>
            <th>派奖总额</th>
            <th>投注返点</th>
            <th>游戏盈亏</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $susername }}</td>
            <td>{{ number_format($aSum['amount_sum'],4)}}</td>
            <td>{{ number_format($aSum['valid_amount_sum'],4)}}</td>
            <td>{{ number_format($aSum['prize_sum'],4)}}</td>
            <td>{{ number_format($aSum['commission_sum'],4)}}</td>
            <td><?php $profit = $aSum['prize_sum']-$aSum['valid_amount_sum']+$aSum['commission_sum']; ?>{{ $profit >= 0 ? '+' : ''}}{{ number_format($profit,4)}}</td>
        </tr>
    </tbody>
</table>



<?php
$input = '&';
foreach(Input::except('page' , 'sort_up' , 'sort_down', 'bet_status', 'status') as $key=>$val){
    $input .= $key.'='.$val.'&';
}
?>

<table width="100%" class="table table-game-detail" id="J-table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>游戏</th>
            <th>编号</th>
            <th>奖期</th>
            <th>玩法</th>
            <th>投注内容</th>
            <th>投注额</th>
            <th>奖金</th>
            <th>奖金组-返点</th>
            <th width="80">
                状态
            </th>
        </tr>
    </thead>
    <tbody>
        <?php $fTotalAmount = $fTotalPrize = $fTotalCommission = 0; ?>
        @if (count($datas))
            @foreach ($datas as $data)
            <tr>
                <td> {{$data->username }} </td>
                <td> {{ $aLotteries[$data->lottery_id] }} </td>
                <td>
                    <a class="view-detail" href="{{route('projects.view', $data->id)}}">{{ $data->serial_number_short }}</a><textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                </td>
                <td> {{ $data->issue }} </td>
                <td> {{ $data->title }} </td>
                <td>
                    @if ( strlen( $data->display_bet_number) > 5 )
                        <a class="view-detail" href="{{route('projects.view', $data->id)}}" target="_blank">详细号码</a><textarea class="data-textarea" style="display:none;">{{ $data->display_bet_number }} </textarea>
                    @else
                        {{ $data->display_bet_number }}
                    @endif
                </td>
                <td> {{ $data->amount_formatted }} </td>
                <td> {{ $data->prize_formatted ? $data->prize_formatted : 0.00}} @if($data->is_overprize) (奖金超限) @endif </td>
                <td>{{ $data->prize_group_real.'-'.$data->commission_formatted.'%' }} </td>
                <td> {{ $data->formatted_status }} </td>
            </tr>
            <?php if($data->status != Project::STATUS_DROPED){$fTotalAmount += $data->amount;$fTotalPrize += $data->prize; $fTotalCommission += $data->commission;} ?>
            @endforeach
        @else
            <tr><td colspan="12">没有符合条件的记录，请更改查询条件</td></tr>
        @endif
    </tbody>
    <tfoot>
        @if (isset($bHasSumRow) && $bHasSumRow)
        <tr>
            <td>本页小结</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td> {{ number_format($fTotalAmount, 2) }}</td>
            <td>{{ number_format($fTotalPrize,2)}}</td>
            <td> &nbsp;</td>
            <td> &nbsp;</td>
        </tr>
        @endif
    </tfoot>
</table>