<table width="100%" class="table" id="J-table">
    <thead>
        <tr>
            <th>编号</th>
            <th>时间</th>
            <th>账变类型</th>
            <th>游戏</th>
            <th>玩法</th>
            <th>模式</th>
            <th>变动金额</th>
            <th>余额</th>
        </tr>
    </thead>
    <tbody>
        <?php $fTotalAmount = 0; ?>
        @if (count($datas))
            @foreach ($datas as $data)
            <tr>
                <td>
                    <?php $link = $data->project_id ? route('projects.view',$data->project_id) : '#'; ?>
                    <a class="view-detail" href="{{ $link }}">{{ $data->serial_number_short }}</a>
                    <textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                </td>
                <td>
                    <?php $aCreatedAt = explode(' ', $data->created_at); ?>
                    {{ $aCreatedAt[0] }}
                    <br />
                    {{ $aCreatedAt[1] }}
                </td>
                <td>{{ $data->friendly_description }}</td>
                <td>{{ $aLotteries[$data->lottery_id] or null }}</td>
                <td>{{ $aSeriesWays[$data->way_id] or ''}}</td>
                <td>{{ $aCoefficients[$data->coefficient] }}</td>
                <td><span class="{{ $data->amount_formatted < 0 ? 'c-red' : 'c-green' }}">{{ $data->amount_formatted }}</span></td>
                <td>{{ $data->available_formatted }}</td>
            </tr>
            <?php $fTotalAmount += $data->direct_amount; ?>
            @endforeach
        @else
            <tr><td colspan="10">没有符合条件的记录，请更改查询条件</td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td>小结</td>
            <td>本页资金变动</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><span class="{{ $fTotalAmount < 0 ? 'c-green' : 'c-red' }}" id="fundChangeNum">{{ number_format($fTotalAmount,6) }}</span></td>
            <td></td>
        </tr>
    </tfoot>
</table>