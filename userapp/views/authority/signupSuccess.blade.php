@extends('l.login', array('active' => 'signin'))

@section('title') 注册成功 @parent @stop

@section ('styles')
    @parent
    {{ style('reg') }}
    <style type="text/css">
        .center
        {
            text-align: center;
        }
    </style>

@stop

@section('container')
    @include('authority.signupHeader')
<div class="reg-content">
    <div class="g_33">

        <div class="alert alert-success">
            <i></i>
            <div class="txt">
                <h4>恭喜您，注册成功!</h4>
                <!-- <p>我们已向您填写的绑定邮箱{{-- $sRegisterMail --}}发送了一封邮件，请按提示信息完成验证。验证邮箱能帮您找回密码，及时了解会员优惠活动和博猫的最新动态。<a class="btn btn-small" target="_blank" href="http://{{-- $sRegisterMail --}}">立即验证邮箱</a></p> -->
                <a href="{{ route('home') }}" class="btn-back"></a>
            </div>
        </div>


        <ul class="menu clerafix">
            <li class="quick">
                <div class="pic">
                    <img src="/assets/images/reg/bg-quick.png" alt="" />
                </div>
                <div class="text">
                    <h4>PC客户端</h4>
                    <p>为方便您随时快速访问博猫，并强化您的资金信息安全，我们强烈建议您立即下载快速登录器！</p>
                    <a href="/pc-client/index.html" target="_blank" class="btn">点击下载</a>
                </div>
            </li>
            <li class="wechat">
                <div class="pic">
                    <img src="/assets/images/reg/phone-code.png" width="125" alt="" />
                </div>
                <div class="text">
                    <h4>关注博猫微信</h4>
                    <p>扫一扫，精彩全知道。优惠活动、中奖喜讯等从此不再错过!</p>
                </div>
            </li>
            <li class="wechat">
                <div class="pic">
                    <img src="/assets/images/reg/phone-ico-img.png" width="125" alt="" />
                </div>
                <div class="text">
                    <h4>手机客户端</h4>
                    <p>手机玩博猫，最新玩法轻松掌控，随时随地，尽享自由自在！</p>
                    <a href="/mobile" target="_blank" class="btn">点击下载</a>
                </div>
            </li>

        </ul>




        <div style="height:100px;"></div>
    </div>
    </div>
</div>

    @include('w.footer')
@stop


@section('end')
@parent
<script type="text/javascript">
    (function(){
                    /* jmpinfo */
            var jmpTimer = null;
            var menuLi = $(".menu li");
            var baseJmp = $(".base-jmp");

            menuLi.hover(function(){
                var curLeft = - $(this).find('.base-jmp').outerWidth()/2
                $(this).find('.base-jmp').show().css("margin-left",curLeft).addClass("fadeInUp");
            }, function(){
                $(this).find('.base-jmp').hide().removeClass("fadeInUp");
            });
    })();
</script>
@stop



