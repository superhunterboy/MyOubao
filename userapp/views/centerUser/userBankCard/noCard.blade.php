@extends('l.home')

@section('title') 
    银行卡管理
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
        border-top: 0;
    }
    </style>
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
                @include('w.uc-menu-user')
            </div>


        
            <div class="page-content-inner page-content-inner-bg">

                <div style="padding:10px;">
    				<div class="row-tip" style="margin:0;text-align:center;padding:20px 0;">
    				    您还没有绑定银行卡， <a href="{{ Route('bank-cards.bind-card', 1) }}" class="btn btn-sbumit">立即绑定</a>
    				</div>
                </div>

            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){



})(jQuery);
</script>
@stop


