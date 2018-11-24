@extends('l.home')

@section('title')
代理盈亏报表
@parent
@stop


@section ('styles')
@parent
{{ style('proxy-global') }}
{{ style('proxy') }}
<style type="text/css">
    .page-content .row {
        padding: 0 0 10px 0;
        margin: 10px 0 0 0;
    }
    .page-content .row-nav {
        padding: 0 35px;
        margin-bottom: 10px;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
    }
    .page-content .row-nav ul{
        width: 264px;
        height: 38px;
        border-radius: 4px;
        background-color: #31CEAC;
        padding: 5px 10px;
        font-size: 13px;
    }
</style>
@stop




@section ('container')

@include('w.header')


<div class="banner">
    <img src="assets/images/proxy/banner.jpg" width="100%" />
</div>




<div class="page-content">
    <div class="g_main clearfix">
        @include('w.manage-menu')

        <div class="nav-inner clearfix">
            @include('w.uc-menu-proxy')
        </div>




        <div class="page-content-inner">
            @include('centerUser.team_profit._agent_search')
            @include('centerUser.team_profit._agent_table')

            {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}


        </div>
    </div>
</div>



@include('w.footer')
@stop



@section('end')
@parent
<script>
    (function ($) {
        $('#J-date-start').focus(function () {
            (new bomao.DatePicker({input: '#J-date-start', isShowTime: false, startYear: 2013})).show();
        });
        $('#J-date-end').focus(function () {
            (new bomao.DatePicker({input: '#J-date-end', isShowTime: false, startYear: 2013})).show();
        });

        new bomao.Select({realDom: '#J-select-user-groups', cls: 'w-2'});


    })(jQuery);
</script>
@stop


