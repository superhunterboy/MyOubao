@extends('l.base')

@section('title') 忘记密码 @parent @stop

@section ('styles')
    {{ style('findpassword')}}
@stop

@section('container')

<div class="reg-top">
    <div class="g_33 cont clearfix">
        <a href="#" class="logo">
            <img src="/assets/images/login/logo.png" width="160" height="50" alt="欧豹娱乐首页"  />
        </a>

    </div>
</div>
<div class="reg-banner">
</div>


<div class="reg-content">
    <div class="g_33 reg-content">

        <div class="reg-left" id="J-form-panel">
            <div class="reg-title">
                找回密码
            </div>
            <!-- 输入用户名部分开始 -->
            {{ Form::open(array('role' => 'form')) }}
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_random" value="{{ createRandomStr() }}">
                <input type="hidden" id="J-check-username-url" value="?"  />
                <input type="hidden" id="J-check-vcode-url" value="?"  />

                <div class="row">
                    <div class="row-title">
                        <span class="feild-title">欧豹用户名</span>
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                用户名格式不对，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild">
                        <input type="text" value="" class="reg-input reg-input-username" id="J-username" tabindex="1" />
                        <span class="feild-static-tip">
                            请输入您需要找回登录密码的用户名
                        </span>
                        <span class="ico-right"></span>
                    </div>
                </div>


                <div class="row">
                    <div class="row-title">
                        <span class="feild-title">验证码</span>
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                验证码不正确，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild row-feild-vcode">
                        <input type="text" value="" class="reg-input reg-input-vcode" id="J-vcode" tabindex="2"  />
                        <img id="J-img-vcode" src="{{ Captcha::img() }}" class="reg-img-vcode" title="{{ Lang::get('transfer.Captcha') }}" />
                        <span class="ico-right"></span>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" class="btn findpassword-submit" id="J-button-submit" tabindex="3" value=" 下一步 " />
                </div>
            {{ Form::close() }}

            @if( Session::get('nomail') )
                @include('authority.password.noMail')
            @elseif( Session::get('status') )
                @include('authority.password.rightMail')
            @endif


        </div>

    </div>
</div>

@stop

@section('end')
<script>
//填写用户名和验证码部分

(function($){
    var username = $('#J-username'),
        vcode = $('#J-vcode');

    $('#J-img-vcode').click(function(){
        $(this).attr('src', "{{ URL::to('captcha?') }}" + ((Math.random()*9 +1)*100000).toFixed(0));
    });


    username.blur(function(){
        var dom = username,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
        if(!(/^[a-zA-Z][a-zA-Z0-9]{5,15}$/).test(v)){
            tip.find('.feild-tip-text').html('用户名格式不对，请重新输入');
            tip.show();
            right.hide();
            return;
        }
        $.ajax({
            url:$.trim($('#J-check-username-url').val()),
            dataType:'json',
            method:'POST',
            data:{'username':v},
            success:function(data){
                if(Number(data['isSuccess']) == 1){
                    tip.hide();
                    right.show();
                }else{
                    tip.find('.feild-tip-text').html('用户名不存在');
                    tip.show();
                    right.hide();
                }
            },
            error:function(){
                tip.hide();
                right.show();
            }
        });
    });

    vcode.blur(function(){
        var dom = vcode,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
        if(!(/^[a-zA-Z0-9]{5}$/).test(v)){
            tip.show();
            return;
        }
        $.ajax({
            url:$.trim($('#J-check-vcode-url').val()),
            dataType:'json',
            method:'POST',
            data:{'vcode':v},
            success:function(data){
                if(Number(data['isSuccess']) == 1){
                    tip.hide();
                    right.show();
                }else{
                    tip.find('.feild-tip-text').html('您输入的验证码不正确');
                    tip.show();
                    right.hide();
                    $('#J-img-vcode').attr('src', $('#J-img-vcode').attr('data-src') + '?rd=' + Math.random());
                }
            }
        });
    });


    $('#J-button-submit').click(function(){
        if($('#J-form-panel').find('.ico-error:visible').size() > 0){
            return false;
        }
        if(username.val() == ''){
            username.parent().parent().find('.feild-tip').show();
            username.focus();
            return false;
        }
        if(vcode.val() == ''){
            vcode.parent().parent().find('.feild-tip').show();
            vcode.focus();
            return false;
        }

        $('#signupForm').submit();
        return true;

    });


    username.focus();
})(jQuery);
</script>
@stop
