@extends('l.base')

@section('title') 密码重置 @parent @stop

@section ('styles')
    {{ style('findpassword')}}
@stop

@section('container')

<div class="reg-top">
    <div class="g_33 cont clearfix">
        <a href="#" class="logo">
            <img src="images/reg/logo.png" width="160" height="50" alt="欧豹娱乐首页"  />
        </a>

    </div>
</div>
<div class="reg-banner">
</div>


<div class="reg-content">
    <div class="g_33 reg-content">

        <div class="reg-left" id="J-form-panel">
            <div class="reg-title">
                密码重置
            </div>



        <form action="?" method="post" id="J-resend-form">
            <input type="hidden" id="J-check-username-url" value="?"  />
            <input type="hidden" id="J-check-vcode-url" value="?"  />
            <div class="row">
                <div class="row-title">
                    <span class="feild-title">重置登录密码</span>
                    <span class="feild-tip">
                        <i class="ico-error"></i>
                        <span class="feild-tip-text">
                            密码格式不对，请重新输入
                        </span>
                    </span>
                </div>
                <div class="row-feild">
                    <input type="password" value="" class="reg-input reg-input-password" id="J-password" tabindex="1" />
                    <span class="feild-static-tip">
                        第一个字母必须为字母，由0-9，a-z，A-Z组成的6-16个字符
                    </span>
                    <span class="ico-right"></span>
                </div>
            </div>
            <div class="row">
                <div class="row-title">
                    <span class="feild-title">确认登录密码</span>
                    <span class="feild-tip">
                        <i class="ico-error"></i>
                        <span class="feild-tip-text">
                            密码格式不对，请重新输入
                        </span>
                    </span>
                </div>
                <div class="row-feild">
                    <input type="password" value="" class="reg-input reg-input-password2" id="J-password2" tabindex="2" />
                    <span class="feild-static-tip">

                    </span>
                    <span class="ico-right"></span>
                </div>
            </div>
            <div class="row">
                <input type="submit" class="btn findpassword-submit" id="J-button-save" tabindex="3" value=" 保 存 " />
            </div>
            </form>

@stop

@section('end')
    @parent
    <!-- 输入新密码部分开始 -->
<script>
(function($){
    var password = $('#J-password'),
        password2 = $('#J-password2');

    password.blur(function(){
        var dom = password,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right'),v2;
        if(!(/^.{6,16}$/).test(v)){
            tip.show();
            right.hide();
            return;
        }
        if(!(/\d/g).test(v) || !(/[a-zA-Z]/g).test(v) || (/(.)\1{2}/g).test(v)){
            tip.show();
            return;
        }
        tip.hide();
        right.show();

        v2 = $.trim(password2.val());
        if(v2 != ''){
            if(v != v2){
                password2.parent().parent().find('.feild-tip').show();
                password2.parent().parent().find('.ico-right').hide();
            }else{
                password2.parent().parent().find('.feild-tip').hide();
                password2.parent().parent().find('.ico-right').show();
            }
        }
    });
    password2.blur(function(){
        var dom = password2,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
        if(v != $.trim(password.val())){
            tip.find('.feild-tip-text').html('两次输入的密码不相同，请重新输入');
            tip.show();
            right.hide();
            return;
        }
        if(v != ''){
            tip.hide();
            right.show();
        }
    });


    $('#J-button-save').click(function(){
        if($('#J-resend-form').find('.ico-error:visible').size() > 0){
            return false;
        }
        if(password.val() == ''){
            password.parent().parent().find('.feild-tip').show();
            password.focus();
            return false;
        }
        if(password2.val() == ''){
            password2.parent().parent().find('.feild-tip').show();
            password2.focus();
            return false;
        }
        return true;
    });

})(jQuery);
</script>
@stop
