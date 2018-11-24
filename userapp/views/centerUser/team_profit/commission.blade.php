@extends('l.home')

@section('title')
            代理返点报表
@parent
@stop




@section ('main')
@include('w.report-nav')

    <div class="content">
        @include('centerUser.profit._agent_commission_search')
        @include('centerUser.profit._agent_commission_table')

        {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
    </div>
@stop

@section('end')
@parent
<script>
(function($){
    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:false, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:false, startYear:2013})).show();
    });

   new bomao.Select({realDom:'#J-select-user-groups',cls:'w-2'});

})(jQuery);
</script>
@stop