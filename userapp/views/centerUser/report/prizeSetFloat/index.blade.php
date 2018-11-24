@extends('l.home')

@section('title')
代理升降点报表
@parent
@stop



@section ('main')

@include('w.report-nav')

<div class="content">
    <div class="area-search">
        <form action="{{ route('user-prizeset-float-reports.index') }}" class="form-inline" method="get">
        <p class="row">
            查询日期 从<span class="ui-prompt"></span>
            <input type="text" class="input w-3" name='updated_at_from' value="{{ Input::get('updated_at_from') }}" id="J-date-start-from">
            &nbsp;
            到 <span class="ui-prompt"></span>
            <input type="text" value="{{ Input::get('updated_at_to') }}" class="input w-3" name='updated_at_to' id="J-date-start-to">
            &nbsp;
            升降点类型：
            <select  id="J-select-custom-type" name='is_up'>
                <option value='-1'>全部类型</option>
                <option value="1" @if(Input::get('is_up')==1) selected@endif>升点</option>
                <option value="0" @if(Input::get('is_up')==0) selected@endif>降点</option>
            </select>&nbsp;&nbsp;
            <input type="submit" class="btn" value="搜 索" />
        </p>
        </form>
    </div>

    <table width="100%" class="table">
        <thead>
            <tr>
                <th>升降点日期</th>
                <th>升降点类型</th>
                <th>变化前奖金组</th>
                <th>变化后奖金组</th>
                <th>升降点要求销量</th>
                <th>当前销量</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                <td>{{ $data->updated_at }}</td>
                <td>{{$data->day}}天@if($data->is_up==1)升点@else降点@endif</td>
                <td>{{ $data->old_prize_group }}</td>
                <td>{{ $data->new_prize_group }}</td>
                <td>{{ number_format($data->standard_turnover, 4) }}</td>
                <td>{{ number_format($data->total_team_turnover, 4) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
</div>
@stop

@section('end')
@parent
<script>
    (function ($) {
        $('#J-date-start-from').focus(function () {
            (new bomao.DatePicker({input: '#J-date-start-from', isShowTime: false, startYear: 2013})).show();
        });
        $('#J-date-start-to').focus(function () {
            (new bomao.DatePicker({input: '#J-date-start-to', isShowTime: false, startYear: 2013})).show();
        });
        new bomao.Select({realDom: '#J-select-custom-type', cls: 'w-2'});
    })(jQuery);
</script>
@stop