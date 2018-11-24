<table width="100%" class="table" id="J-table">
    <thead>
    <tr>
        <th>编号</th>
        <th width="130">时间</th>
        <th>账变类型</th>


        <th>游戏</th>
        <th>玩法</th>

 
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
            <td>{{ $data->serial_number }}
                    <textarea class="data-textarea" style="display:none;">{{ $data->serial_number }} </textarea>
                </td>
                <td>
                    <?php $aCreatedAt = explode(' ', $data->created_at); ?>
                    {{ $aCreatedAt[0] }}
                    {{ $aCreatedAt[1] }}
                </td>
                <td>{{ $aTansanctiongType[$data->type_id] }}</td>

                
 
               

                
                 <td><span class="{{ $data->amount_formatted < 0 ? 'c-green' : 'c-red' }}"></span>{{ $data->amount_formatted }}</td>
                <td>{{ $data->available_formatted }}</td>
            </tr>
            <?php $fTotalAmount += $data->direct_amount; ?>
        @endforeach
    @else
        <tr><td colspan="9">没有符合条件的记录，请更改查询条件</td></tr>
    @endif
    </tbody>
    <tfoot>
    <tr>
        <td>小结</td>
        <td>本页变动</td>
        <td></td>
        <td></td>

       

        <td><span class="{{ $fTotalAmount < 0 ? 'c-green' : 'c-red' }}" id="fundChangeNum"></span>{{ number_format($fTotalAmount,2) }}</td>
        <td></td>
    </tr>
    </tfoot>
</table>