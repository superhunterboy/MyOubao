@extends('l.home')

@section('title')
    提现申请
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
                @include('w.uc-menu-funds')
            </div>


            <div class="page-content-inner page-content-inner-nobg">

                @include('centerUser.withdrawal._search')
                @include('centerUser.withdrawal._list')
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
    new bomao.Select({realDom:'#J-select-status',cls:'w-2'});
    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });

})(jQuery);
</script>
@stop


