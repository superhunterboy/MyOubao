@extends('l.login', array('active' => 'signin'))

@section('title') 登录 @parent @stop


@section('body')
<div class="login">
    <div class="login-box">
        <div class="login-screen">
            <div class="login-icon">
                <img class="img-circle" src="../assets/img/logo.jpg" alt="Welcome！">
                <h4>欢迎登陆<small>管理平台</small></h4>
            </div>
            {{ Form::open(array('class' => 'form-signin', 'role' => 'form', 'target' => '_top')) }}

            <div class="login-form">
                <div class="control-group">
                    <input name="username" id="login-name" value="{{ Input::old('username') }}" type="text" class="form-control  login-field" placeholder="{{ __('User Name') }}" required autofocus>
                    <label class="login-field-icon glyphicon glyphicon-user" for="login-name"></label>
                </div>

                <div class="control-group">
                    <input name="password" id="login-pass" type="password" class="form-control login-field" placeholder="{{ __('Passwrod') }}" required>
                    <label class="login-field-icon  glyphicon glyphicon-lock" for="login-pass"></label>
                </div>
                @if ($bSecureCard)
                <div class="control-group">
                    <input name="secure_password" id="securd-password" type="password" class="form-control login-field" placeholder="{{ __('Secure Password') }}" required>
                    <label class="login-field-icon  glyphicon glyphicon-sound-5-1" for="securd-password"></label>
                </div>
                @elseif ($bCaptcha)
                <div class="control-group">
                    <input name="captcha" type="text" class="form-control login-field" placeholder="{{ __('Captcha') }}" required >
                    <a href="javascript:changeCaptcha();" class="login-captcha" title="{{ Lang::get('transfer.Captcha') }}"><img id="captchaImg" src="{{ Captcha::img() }}" alt=""></a>
                </div>
                @endif

                <label class="checkbox">
                    <!-- <input type="checkbox" name="remember-me" value="1" data-toggle="checkbox"> 记住我 -->
                    <!-- <a href="{{-- route('forgotPassword') --}}" style="float:right;">忘记密码 &gt;&gt;&gt;</a> -->
                </label>


                <button class="btn btn-primary btn-large btn-block login-btn" type="submit">{{ Lang::get('transfer.Login') }}</button>
                <!-- <a class="login-link" href="#">Lost your password?</a> -->
            </div>
            <div class="alert alert-warning alert-dismissable {{ $errors->first('attempt')?'':'hidden'; }}">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>{{ $errors->first('attempt') }}</strong>
            </div>
            {{ Form::close() }}
        </div>
    </div>

  </div>
@stop
@section('end')
<script>
    function changeCaptcha () {
        // debugger;
        captchaImg.src = {{ '"'.URL::to('captcha?').'"' }} + ((Math.random()*9 +1)*100000).toFixed(0);
    }
    // TODO 调整开户流程中，调整完成后打开注释以便实现Username+password登录
    $('#login-pass').blur(function(event) {
        debugger;
        var pwd = $(this).val();
        var username = $('#login-name').val();
        $(this).val(md5(md5(md5(username + pwd))));
    });
</script>
@parent
@stop


