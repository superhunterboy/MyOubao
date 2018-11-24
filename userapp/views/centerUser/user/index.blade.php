@extends('l.home')

@section('title') 
    管理团队
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



            <div class="page-content-inner page-content-inner-nobg">

                @include('centerUser.user.index_search')

                @include('centerUser.user._list')

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}

                
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){
    new bomao.Select({realDom:'#J-select-series-type',cls:'w-2'});
    var TIP = bomao.Tip.getInstance();
    //new bomao.Select({realDom:'#J-select-user-groups',cls:'w-2'});

    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:false, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:false, startYear:2013})).show();
    });
    

    $('#J-table-users .ct-username').hover(function(){
        var el = $(this),
            date_reg = el.attr('data-reg'),
            date_login = el.attr('data-login'),
            html = [];
        html.push('<p style="font-size:12px;">注册时间:'+ date_reg +'</p>');
        html.push('<p style="font-size:12px;">最新登录时间:'+ date_login +'</p>');
        TIP.setText(html.join(''));
        TIP.show(el.width()+10, el.height()/2-TIP.dom.height()/2 - 5, this);
    }, function(){
        TIP.hide();
    });


})(jQuery);
</script>
@stop


