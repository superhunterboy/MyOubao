@extends('l.home')

@section('title')
    代理盈亏报表
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
@stop




@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_main clearfix">
        @include('w.manage-menu')
        
        <div class="nav-inner clearfix">
            @include('w.uc-menu-proxy')
        </div>


            <div class="page-content-inner">

                @include('centerUser.profit._agent_withdraw_deposit_search')
                @include('centerUser.profit._agent_withdraw_deposit_table')

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}


            </div>
        </div>
    </div>



    @include('w.footer')
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


