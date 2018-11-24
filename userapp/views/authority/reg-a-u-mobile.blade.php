<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta name="screen-orientation" content="portrait">
<meta name="x5-orientation" content="portrait">
<meta http-equiv="Cache-Control" content="no-siteapp">
<meta http-equiv="expires" content="0">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">

<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title">
<meta name="format-detection" content="telephone=no">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0,user-scalable=no">
<meta name="apple-touch-fullscreen" content="YES">
<meta name="msapplication-tap-highlight" content="no">
<title>会员注册</title>
<link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/bootstrap/bootstrap-v1.min.css">
<style type="text/css">
.container-main {
  margin-top:30px;
  text-align: center;
}
.account-body {
  position: relative;
  padding: 0 30px 20px 30px;
}
.account-body h2 {margin-top: 0;
  font-size: 20px;
  color: #655F76;
  padding-bottom: 0;
}
.form-control{width:100%;}
.account-body .form-group {text-align: left;}
.account-body .control-label {padding-right: 0;}
.account-body hr {margin-bottom: 20px}
.account-body .btn-form-submit {
    width: 100%;
    background-image: linear-gradient(-180deg, #F17272 0%, #DE4B4B 100%);
    border-radius: 3px;
    font-size: 20px;
    color: #FFFFFF;
    border-color: #EE6D6D;
}
.account-body .btn-form-submit:hover {
background-image: linear-gradient(-180deg, #F98585 0%, #DE4B4B 100%);
}
.account-body .btn-form-submit:active,.account-body .btn-form-submit:focus {
background-image: linear-gradient(-180deg, #D54646 0%, #DE4B4B 100%);
outline: none;
}
.account-body .vcode {cursor: pointer;}
.account-body .vcode-text-small {font-size: 11px;cursor: pointer;display: inline-block;padding-bottom: 10px;
  color: rgb(80, 171, 190);
  text-decoration: underline;
}
.account-body .text-tip {font-size: 12px;margin: 0;padding:2px 0;text-indent: -10000px;}
.account-body .has-error .text-tip {text-indent: 0;}

.account-body .feild-row {position: relative;text-align:left;}
.account-body .feild-row .ico-right {
  position: absolute;
  width: 17px;
  height: 17px;
  font-size: 0;
  overflow: hidden;
  background: url(ico-right.gif) no-repeat;
  right: 25px;
  top: 8px;
  display: none;
}
.account-body .has-right .ico-right {display: block;}
.account-body .has-error .ico-right {display: none;}
</style>
    
</head>
<body>

<div class="container-fluid container-main container-main-a-u bcontainer-main-login">
    <div class="container">
        <div class="account-wrapper">

            <div class="account-body">
                <form id="J-form" class="form-horizontal account-form" method="POST" action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
	                <input type="hidden" name="_random" value="{{ createRandomStr() }}">
	                <h2>会员注册</h2>
                    <hr />
                    <div class="form-group">
                        <label for="J-username" class="col-sm-2 control-label">用户名</label>
                        <div class="col-sm-10 feild-row">
                            <input name="" value="" type="text" class="form-control" id="J-username" tabindex="1" placeholder="6-16位字符，可使用字母或数字">
                            <p class="text-tip text-danger">用户名格式不对，请重新输入</p>
                            <span class="ico-right"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="J-password" class="col-sm-2 control-label">密码</label>
                        <div class="col-sm-10 feild-row">
                            <input name="" type="password" class="form-control" id="J-password" tabindex="2" placeholder="6-16位字符，可使用字母或数字">
                            <p class="text-tip text-danger">密码格式不对，请重新输入</p>
                            <span class="ico-right"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="J-password2" class="col-sm-2 control-label">确认密码</label>
                        <div class="col-sm-10 feild-row">
                            <input name="" type="password" class="form-control" id="J-password2" tabindex="3" placeholder="请再次输入密码">
                            <p class="text-tip text-danger">两次输入的密码不相同，请重新输入</p>
                            <span class="ico-right"></span>
                        </div>
                    </div>
	                @if (($iRegisterNum = UserUser::getRegisterNum(get_client_ip())) && $iRegisterNum > 1)
	                <div class="form-group" style="margin-bottom:20px;">
	                    <label for="J-password2" class="col-sm-2 control-label">验证码</label>
	                    <div class="col-sm-10 feild-row">
                            <input class="form-control" placeholder="输入验证码" maxlength="5" id="vcode" name="captcha" style="width: 60%;">
                            <img id="J-vcode-img" data-src="{{ URL::to('captcha?') }}" class="var-code" src="{{ URL::to('captcha?') }}" style="width:35%; height: 35px; margin-top:0px; vertical-align: middle;">
                        </div>
	                </div>
	                @endif
	                @if($errors->first('attempt'))
	                <div class="row-f login-error" style="color: #f00;text-align: center;line-height: 30px;height: 30px;">{{ $errors->first('attempt') }}</div>
	                @endif
	                <div class="form-group">
                        <div class="col-sm-12">
                            <button id="J-button-submit" type="button" class="btn btn-primary btn-form-submit">立即注册</button>
                        </div>
                    </div>

                    <input name="username" id="login-pass-username" type="hidden" required />
                    <input name="password" id="login-pass-password" type="hidden" required />
                    <input name="password_confirmation" id="login-pass-password-confirmation" type="hidden" required />
                </form>
            </div>

        </div>

    </div>

</div>
<script src="/oubao/assets/js/base-all.js"></script>
<script src="/oubao/assets/js/bootstrap-v1.min.js"></script>
<script src="/oubao/assets/js/md5.js"></script>
<script>
    (function($){
    	var global_isLoading = false;
        var username = $('#J-username'),
            password = $('#J-password'),
            password2 = $('#J-password2'),
            vcode = $('#J-vcode');

        function checkFormPass(){
            var arr = [username,password,password2,vcode],CLS = 'has-error',isPass = true;

            check_username();
            check_password();
            check_password2();
            check_vcode();

            $.each(arr, function(){
                if(this.parents('.form-group').hasClass(CLS)){
                    isPass = false;
                    return false;
                }
            });
            return isPass;
        }
        function check_username_service(v, callback){
            $.ajax({
                url:"/auth/check-username-is-exist?username=" + v,
                dataType:'json',
                beforeSend:function(){
                    global_isLoading = true;
                },
                success:function(data){
                    if(callback){
                        callback(data);
                    }
                },
                complete:function(){
                    global_isLoading = false;
                }
            });
        }
        function check_username(){
            var el = username,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
            par.find('.text-danger').text('用户名格式不对，请重新输入');
            if(!(/[A-Za-z0-9]{6,16}/).test(v)){
                par.addClass(CLS).removeClass(CLSR);
            }else{
                check_username_service(v, function(data){
                    if(Number(data['isSuccess']) == 1){
                        par.removeClass(CLS).addClass(CLSR);
                    }else{
                        par.addClass(CLS).removeClass(CLSR);
                        par.find('.text-danger').text('该用户名已被注册，请重新输入');
                    }
                });

            }
        }
        username.blur(check_username);
        function check_password(){
            var el = password,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
            if(!(/[A-Za-z0-9]{6,16}/).test(v)){
                par.find('.text-tip').text('密码格式不对，请重新输入');
                par.addClass(CLS).removeClass(CLSR);
            }else if($.trim(username.val()) == $.trim(password.val())){
                par.find('.text-tip').text('登录密码不能和用户名相同，请重新输入');
                par.addClass(CLS).removeClass(CLSR);
            }else{
                par.removeClass(CLS).addClass(CLSR);
            }
        }
        password.blur(check_password);
        function check_password2(){
            var el = password2,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
            if(!(/[A-Za-z0-9]{6,16}/).test(v) || $.trim(password.val()) != v){
                par.addClass(CLS).removeClass(CLSR);
            }else{
                par.removeClass(CLS).addClass(CLSR);
            }
        }
        password2.blur(check_password2);
        function check_vcode_service(v, callback){
            $.ajax({
                url:"/auth/check-captcha-error?captcha=" + v,
                dataType:'json',
                beforeSend:function(){
                    global_isLoading = true;
                },
                success:function(data){
                    if(callback){
                        callback(data);
                    }
                },
                complete:function(){
                    global_isLoading = false;
                }
            });
        }
        function check_vcode(){
        	@if ($iRegisterNum && $iRegisterNum > 1)
                
            var el = vcode,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
            par.find('.text-danger').text('验证码不正确，请重新输入');
            if(!(/[A-Za-z0-9]{5}/).test(v)){
                par.addClass(CLS).removeClass(CLSR);
            }else{
                check_vcode_service(v, function(data){
                    if(Number(data['isSuccess']) == 1){
                        par.removeClass(CLS).addClass(CLSR);
                    }else{
                        par.addClass(CLS).removeClass(CLSR);
                        par.find('.text-danger').text('验证码不正确，请重新输入');
                    }
                });
            }
            @endif
        }
        vcode.blur(check_vcode);

        $('#J-button-submit').click(function(){
            var el = $(this);
            if(global_isLoading || !checkFormPass()){
                return false;
            }
            el.attr('disabled', 'disabled');

            $('#login-pass-username').val(username.val());
            $('#login-pass-password').val(password.val());
            $('#login-pass-password-confirmation').val(password2.val());
            $('#J-form').submit();
        });

        //刷新验证码
        $('#J-vcode-img, #J-vcode-img-text').click(function(){
            var el = $(this),src = el.attr('data-src');
            $('#J-vcode-img').attr('src', src + '_='+ Math.random());
        });

    })(jQuery);

</script>

</body>
</html>