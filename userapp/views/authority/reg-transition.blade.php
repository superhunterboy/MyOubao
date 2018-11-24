@extends('l.login')

@if(Session::get('is_client'))
    @include('authority.client.reg-transition')
@else

@section('title')
    注册跳转
@parent
@stop


@section ('styles')
@parent
    {{ style('reg-v2') }}
    <style type="text/css">
    body{
        background:url('/oubao/assets/images/login-v5/banner1.jpg') repeat scroll 0 0;
    }
    </style>
@stop




@section('container')
        @include('w.public-header')
 <div class="help-banner"></div>
<style type="text/css">
.global-top .logo{background: url(/assets/images/global-v4/v5/logo-v5.png) no-repeat scroll 0 10px;}
.reg-transition{margin:0 auto 240px;}
</style>

       <div class="reg-transition">
        <div class="trans-sum">恭喜,注册成功!</div>
        <div class="trans-text"><font>{{Session::get("username")}}</font>,恭喜您成为欧豹尊贵会员</div>
        <div class="trans-time">
            <label id="trans-time">3</label>秒后为您跳转至游戏平台
        </div>
        <button class="btn-trans" id="btn-trans">点击进入</button>
    </div>
    @include('w.footer')
@stop


@section('end')
@parent
    <script type="text/javascript">
    var time = 3;

    setInterval(function() {
        if (time > 1) {
            time--;
            document.getElementById("trans-time").innerHTML = time;
        } else {
            location.href = "/";
        }

    }, 1000);

    $("#btn-trans").click(function(){
        location.href = "/";
    })
    </script>
@stop

@endif
