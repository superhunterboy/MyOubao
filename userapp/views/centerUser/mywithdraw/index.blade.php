@extends('l.home')

@section('title')
    提现记录
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

                @include('centerUser.mycommission._search')
                @include('centerUser.mycommission._list')

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
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });

    try{
        new bomao.Select({realDom:'#J-select-bill-type',cls:'w-3'});
        new bomao.Select({realDom:'#J-select-issue',cls:'w-2'});
        new bomao.Select({realDom:'#J-select-game-mode',cls:'w-2'});
    }catch(e){
        
    }

    $('#J-button-showdetail').click(function(e){
        var panel = $('#J-panel-search-ad');
        if(panel.css('display') == 'none'){
            $(this).text('基本搜索');
        }else{
            $(this).text('高级搜索');
        }
        panel.toggle();
    });

})(jQuery);
</script>
@stop


