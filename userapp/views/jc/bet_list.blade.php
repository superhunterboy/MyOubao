@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner">

            <div class="line-list-top"></div>

            <div class="list-tab">
                <ul class="list clearfix">
                    <li @if (Input::get('type') == '')class="active"@endif><a href="{{ route('jc.bet_list', [$sLotteryKey]) }}">全部</a></li>
                    <li @if (Input::get('type') == \JcModel\JcProject::TYPE_SELF_BUY)class="active"@endif><a href="{{ route('jc.bet_list', [$sLotteryKey, 'type' => \JcModel\JcProject::TYPE_SELF_BUY]) }}">自购方案</a></li>
                    <li @if (Input::get('type') == \JcModel\JcProject::TYPE_GROUP_BUY)class="active"@endif><a href="{{ route('jc.bet_list', [$sLotteryKey, 'type' => \JcModel\JcProject::TYPE_GROUP_BUY]) }}">合买方案</a></li>
                </ul>
            </div>


            <div class="bet-confirm">
                <form>
                <div class="search">
                    日期：
                    <input class="input input-date" name="searchDate" id="J-date-start" value="{{{ isset($searchDate) ? $searchDate : '' }}}" />
                    &nbsp;
                    状态：
                    <select name="status" id="J-select-filter-status">
                        <option value="">全部</option>
                        @foreach($aStatus as $iStatus => $sStatus)
                        <option @if(Input::get('status') !== null && Input::get('status') !== '' && Input::get('status') == $iStatus)selected="selected" @endif value='{{{ $iStatus }}}'>{{{ $sStatus }}}</option>
                        @endforeach
                    </select>
                     &nbsp;
                    <input name="type" type="hidden" value="{{{ Input::get('type') }}}" />
                    <input type="hidden" value="{{{ csrf_token() }}} " />
                    <input type="submit" value=" 搜索 " class="button" />


                </div>
                </form>
                <table class="table table-group">
                    <tr>
                        <th>方案编号</th>
                        <th>玩法</th>
                        <!--<th>编号</th>-->
                        <th>类别</th>
                        <th>发起人</th>
                        <th>方案金额</th>
                        <th>认购金额</th>
                        <th>进度</th>
                        <th>奖金</th>
                        <th>投注时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    @foreach($datas as $data)
                    <tr class="tr-status-{{{ $data->status }}}">
                        <td>{{{ $data->bet_serial_number }}}</td>
                        <td>
                            @if(isset($aMethodGroup[$data->method_group_id]->name))
                            {{{ $aMethodGroup[$data->method_group_id]->name }}}
                            @endif
                        </td>
                        <!--<td>{{{ $data->serial_number }}}</td>-->
                        <td>{{{ $data->group_id > 0 ? '合买' : '自购' }}}</td>
                        <td>{{{ $data->author }}}</td>
                        <td>
                            <span class="c-yellow">{{{ number_format($data->total_amount, 2) }}}</span>
                        </td>
                        <td>
                            <span class="c-yellow">{{{ number_format($data->amount, 2) }}}</span>
                        </td>
                        <td>
                            {{{ $data->total_buy_percent }}}
                        </td>
                        <td>
                             <span class="c-yellow">{{{ $data->prize > 0 ? number_format($data->prize, 4) : '' }}}</span>
                        </td>
                        <td>{{{ $data->created_at }}}</td>
                        <td>{{{ $data->formatted_status }}}</td>
                        <td class="cell-ct">
                            @if ($data->group_id > 0)
                            <a target="_blank" class="ct-link" href="{{{ route('jc.follow', $data->group_id) }}}">查看详细</a>
                            @else
                            <a target="_blank" class="ct-link" href="{{{ route('jc.bet_view', $data->bet_id) }}}">查看详细</a>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
                
            </div>




        </div>
    </div>
</div>
@include('w.footer')
@stop


@section('end')
@parent
<script>
(function($, host){
    var selectStatus = new host.Select({realDom:'#J-select-filter-status', cls:'w-2'});

    $('#J-date-start').focus(function(){
        var dateStart = new host.DatePicker({input:'#J-date-start', startYear:2013});
        dateStart.show();
    });
    $('#J-date-end').focus(function(){
        var dateStart = new host.DatePicker({input:'#J-date-end', startYear:2013});
        dateStart.show();
    });
        

})(jQuery, bomao);
</script>
@stop


