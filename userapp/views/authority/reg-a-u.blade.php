<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="欧豹娱乐,欧豹彩票,欧豹游戏，欧豹娱乐平台,欧豹开户,欧豹注册">
    <meta name="description" content="欧豹游戏官网提供欧豹娱乐注册,欧豹平台开户,欧豹娱乐平台登录网址,欧豹娱乐平台客户端下载等服务!">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <title>会员注册 - 欧豹娱乐</title>
    <link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/bootstrap/bootstrap-v1.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="/assets/images/global-v4/global.css">
    <link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/outer/outer.css">
    <!--[if lt IE 9]>
    <script src="//cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<header class="navbar">
    <div class="container">
        <div class="navbar-header">
            <a href="/" class="navbar-brand"></a>
        </div>
        <nav class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a class="btn btn-quick" href="/" target="_blank">已有账号</a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<div class="container-fluid container-main container-main-a-u bcontainer-main-login">
    <div class="container">
        <div class="account-wrapper">

            <div class="account-body">
                <form id="J-form" class="form-horizontal account-form" method="POST" action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
	                <input type="hidden" name="_random" value="{{ createRandomStr() }}">
	                <h2>注册会员</h2>
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
	                <div class="form-group code">
	                    <input class="js-login-valCode" placeholder="输入验证码" maxlength="5" id="vcode" name="captcha">
	                    <img id="J-vcode-img" data-src="{{ URL::to('captcha?') }}" class="var-code" src="{{ URL::to('captcha?') }}" style="width: 70px; height: 35px; margin-top:0px; vertical-align: middle;">
	                    <span id="J-vcode-img-text" data-src="{{ URL::to('captcha?') }}" class="vcode-text-small" style="font-size:11px;">看不清? 换张图片</span>
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
        <p class="title-text">
            爱欧豹，纵享激情！<br />
            <span class="sm">权威认证购彩平台，100%资金兑现赔付</span>
        </p>

    </div>

</div>
@include('w.footer-v4')
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
    function openKF() {
    	var url = '{{SysConfig::readValue("KFURL")}}';  //转向网页的地址;
        var name = '';                            //网页名称，可为空;
        var iWidth = 750;                          //弹出窗口的宽度;
        var iHeight = 500;                         //弹出窗口的高度;
        //获得窗口的垂直位置
        var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
        //获得窗口的水平位置
        var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
        window.open(url, name, 'height=' + iHeight + ',,innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
    }

</script>


<script type="text/javascript">
    $(function($) {


        //回到顶部
        var refer = $('#J-header-container'),dom,offset,win,timer;
        if(refer.size() < 1){
            //首页应用
            $("#J-global-gototop").click(function(e){
                e.preventDefault();
                $('html,body').animate({scrollTop:0}, 400);
            });

            setTimeout(function(){
                $(window).scroll(function(){
                    clearTimeout(timer);
                    timer = setTimeout(function(){
                        if($(window).scrollTop() > 200){
                            $("#J-global-gototop").fadeIn(700);
                        }else{
                            $("#J-global-gototop").fadeOut(700);
                        }
                    }, 300);
                });
            });

            return;
        }
        win = $(window);
        offset = refer.offset();

        setTimeout(function(){
            dom = $('<a class="global-gototop" id="J-global-gototop" href="#">返回顶部</a>').appendTo('body');
            dom.css({'left':offset.left + refer.width() + 20});
            dom.click(function(e){
                e.preventDefault();
                $('html,body').animate({scrollTop:0}, 400);
            });
            win.scroll(function(){
                clearTimeout(timer);
                timer = setTimeout(function(){
                    if(win.scrollTop() > 200){
                        dom.fadeIn(700);
                    }else{
                        dom.fadeOut(700);
                    }
                }, 300);

            });
        }, 2000);
    });


</script>


<script type="text/javascript">
    $('.navbar-right>li').hover(function(){
        var curLeft = - $(this).find('.base-jmp').outerWidth()/2
        $(this).find('.base-jmp').show().css("margin-left",curLeft).addClass("fadeInUp");
    }, function(){
        $(this).find('.base-jmp').hide().removeClass("fadeInUp");
    });
</script>

</body>
</html>