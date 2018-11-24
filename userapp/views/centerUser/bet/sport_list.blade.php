


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

        <th>游戏</th>
        <th>编号</th>
        <th>投注时间</th>
        <th>玩法</th>
        <th>类别</th>
        <th>发起人</th>
        <th>方案金额</th>
        <th>认购金额</th>
        <th>奖金</th>
        <th width="80">状态</th>
    </tr>
    </thead>
    <tbody>
    <?php $fTotalAmount = $fTotalPrize = $fTotalCommission = 0; ?>
    @if (count($datas))
        @foreach ($datas as $data)
            <tr>

                <td> {{ $aLotteries[$data->lottery_id] }} </td>
                <td>
                    @if ($data->group_id > 0)
                        <a class="view-detail" href="{{route('jc.follow', $data->group_id)}}">{{ $data->serial_number }}</a><textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                    @else
                        <a class="view-detail" href="{{route('jc.bet_view', $data->bet_id)}}">{{ $data->serial_number }}</a><textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>

                    @endif
                </td>
                <td>{{$data->created_at}}</td>
                <td> {{ $methods[$data->method_group_id]}} </td>
                <td>{{$sportType[$data->type]}}</td>
                <td> {{$data->author }} </td>
                <td>{{ number_format($data->total_amount, 2) }}</td>
                <td>{{ number_format($data->amount, 2) }}</td>
                <td> {{ number_format($data->prize, 2) }}</td>
                <td> {{$sportStatus[$data->status]}} </td>
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
            <td>&nbsp;</td>
            <td> {{ number_format($fTotalAmount, 2) }}</td>
            <td>{{ number_format($fTotalPrize,2)}}</td>
            <td> &nbsp;</td>

        </tr>
    @endif
    </tfoot>
</table>