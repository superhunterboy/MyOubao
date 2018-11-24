@extends('l.home')

@section('title')
我的充值
@parent
@stop





@section ('main')
<div class="nav-inner clearfix">

    
    @include('w.uc-menu-funds')


</div>

<div class="content">
    <div class="area-search">
        <form action="{{ route('user-transactions.mydeposit',Session::get('user_id')) }}" class="form-inline" method="get">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <p class="row">
                时间：
                <input id="J-date-start" class="input w-3" type="text" name="created_at_from" value="{{ Input::get('created_at_from') }}" />
                至
                <input id="J-date-end" class="input w-3" type="text" name="created_at_to" value="{{ Input::get('created_at_to') }}" />
            

<!--
                类型：
                <select id="J-select-recharge" style="display:none;" name="deposit_mode">
                    <option value>所有类型</option>
                    <option value="1" {{Input::get('deposit_mode') == 1 ? 'selected="selected"' : ''}}>银行转账</option>
                    <option value="2" {{Input::get('deposit_mode') == 2 ? 'selected="selected"' : ''}}>快捷充值</option>
                </select>
                &nbsp;-->
                <input class="btn" type="submit" value=" 搜 索 " />




            </p>

        </form>
    </div>

    <table width="100%" class="table">
        <thead>
            <tr>
                <th width="300">编号</th>
                <th width="200">时间</th>
                <th>金额</th>
            </tr>
        </thead>
        <tbody>
            <?php $fTotalAmount  = $fTotalFee = 0; ?>

            @if(count($datas))
                @foreach ($datas as $key => $data)
                <tr class="withdrawalRow">
                    <td>{{ $data->serial_number }}</td>
                    <td>
                        {{ $data->created_at }}
                    </td>
                    <td><span class="c-green amount">{{ $data->amount_formatted }}</span></td>
                </tr>
                <?php
                $fTotalAmount += $data->amount;
                $fTotalFee += $data->fee;
                ?>
                @endforeach

        @else
            <tr><td colspan="3">没有符合条件的记录，请更改查询条件</td></tr>
        @endif
        </tbody>
        <tfoot>
            <tr>
                <td>小结</td>
                <td>本页资金变动<br /></td>
                <td>{{  number_format($fTotalAmount, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
</div>
@stop

@section('end')
@parent
<script>
    (function ($) {

        //new bomao.Select({realDom: '#J-select-recharge', cls: 'w-2'});

        $('#J-date-start').focus(function () {
            (new bomao.DatePicker({input: '#J-date-start', isShowTime: true, startYear: 2013})).show();
        });
        $('#J-date-end').focus(function () {
            (new bomao.DatePicker({input: '#J-date-end', isShowTime: true, startYear: 2013})).show();
        });


    })(jQuery);
</script>
@stop