<table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            <th>游戏</th>
            <th>玩法</th>
            <th>起始奖期</th>
            <th title="已追/总期数">追号进度</th>
            <th>总追号金额</th>
            <th>已中奖金额</th>
            <th>追中即停</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @if (count($datas))
            @foreach ($datas as $data)
            <tr>
                <td> {{ $aLotteries[$data->lottery_id] }} </td>
                <td>{{ $data->title }}</td>
                <td>{{ $data->start_issue }}</td>
                <td>{{ $data->finished_issues }} / {{ $data->total_issues }}</td>
                <td>{{ $data->amount_formatted }}</td>
                <td>{{ $data->prize }}</td>
                <td>{{ $data->formatted_stop_on_won }}</td>
                <td>{{ $data->formatted_status }}</td>
                <td><a href="{{ route('traces.view', $data->id) }}">详情</a></td>
            </tr>
            @endforeach
        @else
            <tr><td colspan="10">没有符合条件的记录，请更改查询条件</td></tr>
        @endif

    </tbody>
    <tfoot>
        <tr>
            @if (isset($bHasSumRow) && $bHasSumRow)
                <td>小结</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td> {{ number_format($aSum['amount_sum'], 2) }}</td>
                <td> {{ number_format($aSum['prize_sum'], 2) }}</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            @endif
        </tr>
    </tfoot>
</table>