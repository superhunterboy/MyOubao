</table><table width="100%" class="table">
    <thead>
        <tr>
            <th width="300">编号</th>
            <th width="200">时间</th>
            <th>账户</th>
            <th>申请金额</th>
            <th>实际提现</th>
            
            <th>状态</th>
        </tr>
    </thead>
    <tbody>
        <?php $fTotalAmount = $fTotalTransAmount = $fTotalCharge = 0; ?>




        @if(count($datas))

            @foreach ($datas as $key => $data)
            <tr class="withdrawalRow">
                <td>{{ $data->serial_number }}</td>
                <td>
                    {{ $data->request_time }}
                </td>
                <td>{{ $data->account_name }}</td>
                <td><span class="c-green amount"> {{ $data->formatted_amount }}</span></td>
                <td><span class="c-green transaction_amount"> {{ $data->formatted_transaction_amount }}</span></td>
                 <td> {{ $data->formatted_status }}</td>
            </tr>
            <?php
                $fTotalAmount      += $data->amount;
                $fTotalTransAmount += $data->transaction_amount;
            ?>
            @endforeach


        @else
            <tr><td colspan="6">没有符合条件的记录，请更改查询条件</td></tr>
        @endif


    </tbody>
    <tfoot>
        <tr>
            <td>小结</td>
            <td>本页资金变动</td>
            <td></td>
            <td>{{  number_format($fTotalAmount, 2) }}</td>
            <td>{{  number_format($fTotalTransAmount, 2) }}</td>
            <td></td>
        </tr>
    </tfoot>
</table>