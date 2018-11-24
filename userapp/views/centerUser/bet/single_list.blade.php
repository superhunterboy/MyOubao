

<table width="100%" class="table" id="J-table">
    <thead>
    <tr>
        <th>用户名</th>
        <th>投注总额</th>
        <th>有效投注</th>
        <th>派奖总额</th>

        <th>游戏盈亏</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $susername }}</td>
        <td>{{ number_format($aSum['amount_sum'],4)}}</td>
        <td>{{ number_format($aSum['valid_amount_sum'],4)}}</td>
        <td>{{ number_format($aSum['prize_sum'],4)}}</td>
        <td><?php $profit = $aSum['prize_sum']-$aSum['valid_amount_sum']; ?>{{ $profit >= 0 ? '+' : ''}}{{ number_format($profit,4)}}</td>
    </tr>
    </tbody>
</table>



<?php
$input = '&';
foreach(Input::except('page' , 'sort_up' , 'sort_down', 'bet_status', 'status') as $key=>$val){
    $input .= $key.'='.$val.'&';
}
?>

<table width="100%" class="table" id="J-table">
    <thead>
    <tr>

        <th>游戏</th>
        <th>编号</th>
        <th>玩法</th>
        <th>投注内容</th>
        <th>投注额</th>
        <th>奖金</th>
        <th width="130">
            状态
        </th>
    </tr>
    </thead>
    <tbody>
    <?php $fTotalAmount = $fTotalPrize = 0; ?>
    @if (count($datas))
        @foreach ($datas as $data)
            <tr>

                <td> {{$data->game_title}} </td>
                <td>
                    <a class="view-detail" href="{{route('projects.view', $data->id)}}?mode=casino">{{ $data->serial_number_short }}</a><textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                </td>

                <td> {{ $data->method_title }} </td>
                <td>
                    {{ $data->way_title }}
                </td>
                <td> {{ number_format($data->amount,4)}} </td>
                <td> {{ number_format($data->prize,4)}}</td>

                <td> {{ $data->formatted_status }}</td>
            </tr>
            <?php if($data->status != BlackJackProjectDetail::STATUS_DROPED){$fTotalAmount += $data->amount;$fTotalPrize += $data->prize;} ?>
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
            <td> {{ number_format($fTotalAmount, 4) }}</td>
            <td>{{ number_format($fTotalPrize,4)}}</td>
            <td>&nbsp;</td>



        </tr>
    @endif
    </tfoot>
</table>