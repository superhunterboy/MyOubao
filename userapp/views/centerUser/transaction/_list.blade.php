<table width="100%" class="table" id="J-table">
    <thead>
    <tr>
        <th width="300">编号</th>
        <th width="200">时间</th>
        <th>账变类型</th>
        @if($related_user)
         <th>转出用户</th>
         <th>转入用户</th>
        @else
        <th>游戏</th>
        <th>玩法</th>
        @endif
        {{-- <th>投注金额</th> --}}
        <th>变动金额</th>
        <th width="90">余额</th>
    </tr>
    </thead>
    <tbody>
    <?php $fTotalAmount = 0; ?>
    @if (count($datas))
        @foreach ($datas as $data)
            <tr>
            <td>
                    <a class="view-detail" href="{{ $data->link }}"> {{ $data->serial_number }}</a>
                    <textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                </td>
                <td>
                    <?php $aCreatedAt = explode(' ', $data->created_at); ?>
                    {{ $aCreatedAt[0] }}
                    {{ $aCreatedAt[1] }}
                </td>
                <td>{{ $data->friendly_description }}</td>
                @if($related_user)
                <td>{{ $data->from_user_name }}</td>
                <td>{{ $data->to_user_name }}</td>
                 @else
                <td>{{ $aLotteries[$data->lottery_id] or null }}</td>
                <td>{{ $aSeriesWays[$data->way_id] or ''}}</td>
               
                @endif
                
                 <td><span class="{{ $data->amount_formatted < 0 ? 'c-red' : 'c-green' }}">{{ $data->amount_formatted }}</span></td>
                <td>{{ $data->available_formatted }}</td>
            </tr>
            <?php $fTotalAmount += $data->direct_amount; ?>
        @endforeach
    @else
        <tr><td colspan="
                 @if(!$related_user) 9 @else 8 @endif
                ">没有符合条件的记录，请更改查询条件</td></tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td>小结</td>
        <td>本页变动</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td><span class="{{ $fTotalAmount < 0 ? 'c-green' : 'c-red' }}" id="fundChangeNum">{{ number_format($fTotalAmount,2) }}</span></td>
        <td></td>
    </tr>
    </tfoot>
</table>