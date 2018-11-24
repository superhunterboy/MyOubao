@extends('l.home')

@section('title')
    提现申请
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

            {{--<div class="nav-inner clearfix">--}}
                {{--@include('w.uc-menu-funds')--}}
            {{--</div>--}}


            <div class="page-content-inner page-content-inner-nobg">
                <div class="txcg">
                    <h4>账户申请提现成功！ </h4>
                    <ul>
                        <li><a href="/user-withdrawals/withdraw">还要提现</a></li>
                        <li><a href="/user-withdrawals">查看提现进度</a></li>
                    </ul>
                    <h5>8秒后，系统将自动跳转至提现申请记录详情。</h5>
                </div>
                {{--@include('centerUser.withdrawal_agent._search')--}}
                {{--@include('centerUser.withdrawal_agent._list')--}}
                {{--{{ pagination($datas->appends(Input::except('page')), 'w.pages') }}--}}

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
    setTimeout(function () {
        location.href='/user-withdrawals';
    },8000)
})(jQuery);
</script>
@stop


