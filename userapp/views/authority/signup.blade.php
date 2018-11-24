@extends('l.login', array('active' => 'signin'))

@if(Session::get('is_client'))
    @include('authority.client.signup')
@else

@section('title') 注册会员 @parent @stop

@section ('styles')
    @parent
    {{ style('reg') }}
@stop

@section('scripts')
@parent
    {{ script('video') }}
@stop

@section('container')
    

    @include('authority.signupHeader')


<div id="xmSlide" class="xmSlide">
    @include('adTemp.11')
</div>
<div class="reg-content">
    <div class="g_33 reg-content">

<!--             @if (! $sKeyword)
        <div class="reg-tip">
            此注册为您提供的是1500奖金组（三星直选单注奖金1500元），仅供游戏体验。如需更高的游戏奖金组，请您通过百度、QQ寻找博猫的代理开户。
        </div>
        @endif -->


        <div class="reg-left" id="J-form-panel">
            <form action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}" method="post" id="signupForm">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_random" value="{{ createRandomStr() }}">



                <div class="title">
                    用户注册
                </div>

                <div class="row">
                    <div class="row-title">
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                用户名格式不对，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild">
                        <input type="text" value="" placeholder="用户名" class="input reg-input reg-input-username" id="J-username" name="username" value="{{ Input::get('username') }}" />
                        <span class="ico-right"></span>
                    </div>
                </div>


                <div class="row row-password">
                    <div class="row-title">
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                密码格式不正确，请重新输入
                            </span>
                        </span>

                    </div>
                    <div class="row-feild row-feild-password">
                        <input type="text" value="" placeholder="密码" class="input reg-input reg-input-password" id="J-password" name="password" />
                        <input type="text" value="" class="reg-input-password-hidden" id="J-password-hidden" />
                        <label class="checkbox-show-pas" for="J-checkbox-showpas">
                            <input type="checkbox" id="J-checkbox-showpas" /> 显示
                        </label>
                        <span class="ico-right"></span>
                    </div>
                </div>

                <div class="row">
                    <div class="row-title">
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                两次输入的密码不相同，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild">
                        <input type="text" value="" placeholder="确认密码" class="input reg-input reg-input-password2" id="J-password2" name="password_confirmation" />
                        <span class="ico-right"></span>
                    </div>
                </div>


                {{--
                <div class="row row-mail">
                    <div class="row-title">
                        <span class="feild-title">邮箱地址</span>
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                邮箱地址输入不正确，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild">
                        <input type="text" value="" class="input reg-input reg-input-email" id="J-email"  name="email" value="{{ Input::get('email') }}" />
                        <span class="ico-right"></span>
                    </div>
                </div>
                --}}

                @if (($iRegisterNum = UserUser::getRegisterNum(get_client_ip())) && $iRegisterNum > 1)
                
                <div id="J-row-vocde" class="row" style="display:block;">
                    <div class="row-title">
                        <span class="feild-tip">
                            <i class="ico-error"></i>
                            <span class="feild-tip-text">
                                验证码不正确，请重新输入
                            </span>
                        </span>
                    </div>
                    <div class="row-feild row-feild-vcode">
                        <input type="text" name="captcha" value="" class="input reg-input reg-input-vcode" id="J-vcode"  placeholder="不区分大小写" />
                        <a class="verify reg-img-vcode" href="javascript:changeCaptcha();" title="{{ Lang::get('transfer.Captcha') }}">
                            <img id="captchaImg"  src="{{ Captcha::img() }}"/>
                            &nbsp;
                            <span id="J-vcode-img-text" data-src="{{ URL::to('captcha?') }}" class="vcode-text-small" style="font-size:11px;color:#908267;text-decoration: underline;">看不清楚? 换张图片</span>
                        </a>

                        <span class="ico-right"></span>
                    </div>
                </div>
                @endif
                



                <div class="row">
                    <button class="reg-button-submit" id="J-button-submit">立即注册</button>

                </div>

                <div class="row row-text">
                    已有账号？<a href="{{ route('signin') }}">立即登录</a>
                </div>


            </form>
        </div>

        <div class="dy-superiority">
            <p class="superiority-title">
                欧豹娱乐-给梦想一个机会
            </p>
            <div class="superiority-content">
                <span>
                    <div class="superiority-pic" id="games"></div>
                    <p class="title">
                        12款精品游戏
                    </p>
                    <p class="explain">
                        玩法齐全，支持元角分，自主彩，瑞士硬件，开奖公正公平，7X24小时优质客服！
                    </p>
                </span>
                <div class="seperator"></div>
                <span>
                    <div class="superiority-pic" id="clock"></div>
                    <p class="title">
                        3分钟提款到账
                    </p>
                    <p class="explain">
                        15家合作银行，100元起提，单笔可达50万，3分钟到账，0手续费！
                    </p>
                </span>
                <div class="seperator"></div>
                <span>
                    <div class="superiority-pic" id="rocket"></div>
                    <p class="title">
                        100%速度激情
                    </p>
                    <p class="explain">
                        单式十万注，秒投无压力，万人不卡，光速体验，超越极限，自由随心！
                    </p>
                </span>
                <div class="seperator"></div>
                <span>
                    <div class="superiority-pic" id="security"></div>
                    <p class="title">
                        100%安心娱乐
                    </p>
                    <p class="explain">
                        256位AES加密和DSA数字签名，高防云防御防火墙，CDN多线加速，安全稳定高速！
                    </p>
                </span>
            </div>
        </div>
    </div>
</div>


    @include('w.footer-v3')
@stop


@section('end')
 @parent
    <script>
        var global_isLoading = false;
        function isNeedVcode(){
            return !($('#J-row-vocde').css('display') == 'none');
        }
        function changeCaptcha(){
            // debugger;
            captchaImg.src = "{{ URL::to('captcha?') }}" + ((Math.random()*9 +1)*100000).toFixed(0);
        };
        //远程校验用户名
        function checkUserNameService(username, callback){
            $.ajax({
                url:"/auth/check-username-is-exist?username=" + username,
                dataType:'json',
                beforeSend:function(){
                    global_isLoading = true;
                },
                success:function(data){
                    if(callback){
                        callback(data);
                    }
                },
                error:function(){
                    alert('网络请求失败，请刷新页面重试');
                },
                complete:function(){
                    global_isLoading = false;
                }
            });
        }
        function checkVcodeService(v, callback){
            $.ajax({
                url:"/authority/check-captcha-error?captcha=" + v,
                dataType:'json',
                beforeSend:function(){
                    global_isLoading = true;
                },
                success:function(data){
                    if(callback){
                        callback(data);
                    }
                },
                error:function(){
                    alert('网络请求失败，请刷新页面重试');
                },
                complete:function(){
                    global_isLoading = false;
                }
            });
        }
        (function($){

            var username = $('#J-username'),
                password = $('#J-password'),
                passwordHidden = $('#J-password-hidden'),
                password2 = $('#J-password2'),
                email = $('#J-email'),
                vcode = $('#J-vcode'),
                showPass = $('#J-checkbox-showpas');

            setTimeout(function(){
                password.attr('type', 'password');
                password2.attr('type', 'password');
            }, 500);

            username.blur(function(){
                var dom = username,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
                if(!(/^[a-zA-Z0-9]{6,16}$/).test(v)){
                    tip.find('.feild-tip-text').html('用户名格式不对，请重新输入');
                    tip.show();
                    right.hide();
                    return;
                }
                checkUserNameService(v, function(data){
                    if(Number(data['isSuccess']) == 1){
                        tip.hide();
                        right.show();
                    }else{
                        tip.find('.feild-tip-text').html('该用户名已被注册，请重新输入');
                        tip.show();
                        right.hide();
                    }
                });
            });
            password.blur(function(){
                var dom = password,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right'),v2;
                var tiptext = '密码格式不正确，请重新输入';
                tip.find('.feild-tip-text').text(tiptext);

                //if(!(/^(?=.*\d+)(?=.*[a-zA-Z]+)(?!.*?([a-zA-Z0-9]{1})\1\1).{6,16}$/).test(v)){
                if(!(/^[a-zA-Z0-9]{6,16}$/).test(v)){
                    tip.show();
                    right.hide();
                    return;
                }
                if(v == $.trim(username.val())){
                    tiptext = '登录密码不能和用户名相同，请重新输入';
                    tip.find('.feild-tip-text').text(tiptext);
                    tip.show();
                    right.hide();
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
            }).keyup(function(){
                passwordHidden.val(this.value);
            });
            passwordHidden.keyup(function(){
                password.val(this.value);
            });
            passwordHidden.blur(function(){
                var dom = passwordHidden,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right'),v2;
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
                if(v2 != '' && v != v2){
                    password2.parent().parent().find('.feild-tip').show();
                    password2.parent().parent().find('.ico-right').hide();
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
            email.blur(function(){
                var dom = email,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
                if(v != '' && !(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/).test(v)){
                    tip.show();
                    return;
                }
                tip.hide();
                if(v !=  ''){
                    right.show();
                }
            });
            vcode.blur(function(){
                var dom = vcode,v = $.trim(dom.val()),tip = dom.parent().parent().find('.feild-tip'),right = dom.parent().parent().find('.ico-right');
                if(!(/^[a-zA-Z0-9]{5}$/).test(v)){
                    tip.show();
                    return;
                }
                tip.hide();

                checkVcodeService(v , function(data){
                    if(Number(data['isSuccess']) == 1){
                        tip.hide();
                        right.show();
                    }else{
                        tip.find('.feild-tip-text').html('验证码不正确，请重新输入');
                        tip.show();
                        right.hide();
                        //changeCaptcha();
                    }
                });

                // $.ajax({
                //     url:$.trim($('#J-check-vcode-url').val()),
                //     dataType:'json',
                //     method:'POST',
                //     data:{'vcode':v},
                //     success:function(data){
                //         if(Number(data['isSuccess']) == 1){
                //             tip.hide();
                //             right.show();
                //         }else{
                //             tip.find('.feild-tip-text').html('您输入的验证码不正确');
                //             tip.show();
                //             right.hide();
                //             $('#J-img-vcode').attr('src', $('#J-img-vcode').attr('data-src') + '?rd=' + Math.random());
                //         }
                //     }
                // });
            });
            showPass.click(function(){
                if(this.checked){
                    passwordHidden.show();
                }else{
                    passwordHidden.hide();
                }
            });


            $('#J-button-submit').click(function(){
                if(global_isLoading){
                    return false;
                }
                if($('#J-form-panel').find('.ico-error:visible').size() > 0){
                    return false;
                }
                if(username.val() == ''){
                    username.parent().parent().find('.feild-tip').show();
                    username.focus();
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
                if(isNeedVcode() && vcode.val() == ''){
                    vcode.parent().parent().find('.feild-tip').show();
                    vcode.focus();
                    return false;
                }
                $('#signupForm').submit();
                return false; // TIP return false可以去除button type = image时, form提交出现的button的x,y座标值

            });


            showPass.get(0).checked = false;
            username.focus();

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
        })(jQuery);

        /**
        if ($('#popWindow').length) {
            // $('#myModal').modal();
            var popWindow = new bomao.Message();
            var data = {
                title          : '提示',
                content        : $('#popWindow').find('.pop-bd > .pop-content').html(),
                closeIsShow    : true,
                closeButtonText: '关闭',
                closeFun       : function() {
                    this.hide();
                }
            };
            popWindow.show(data);
        }
        **/


// //视频播放
// (function($){
//     var player = new SWFObject("/assets/images/video/vcastr2/vcastr2.swf","ply","270","152","9","#000000");
//     player.addParam("allowfullscreen","true");
//     player.addParam("allowscriptaccess","always");
//     player.addParam("wmode","opaque");
//     player.addParam("quality","high");
//     player.addParam("salign","lt");
//     player.addVariable("vcastr_file","/assets/images/video/bomao320-240.flv");
//     player.write("J-video");

// })(jQuery);


    </script>

@stop

@endif