@extends('l.home')

@section('title') 
    个人资料
@parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .layout-row {float: left;}
    </style>
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
    }
    </style>
@stop



@section ('container')
@include('w.header')
 <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>
<div class="page-content page-content-password">
        <div class="g_main clearfix">
            @include('w.manage-menu')
            
            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
                <div class="r-success">
                    <span>恭喜您，安全口令设置成功！</span>
                </div>
                
            </div>
        </div>
</div>


@include('w.footer')
@stop



@section('end')
@parent
@stop
