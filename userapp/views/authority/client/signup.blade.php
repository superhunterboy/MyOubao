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

@include('w.client.auth-header')


    <div class="reg">

        <form id="J-form" class="form-horizontal account-form" method="POST" action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="_random" value="{{ createRandomStr() }}">
            <input type="hidden" id="password_confirmation_id" name="password_confirmation" value="">
            <input type="hidden" name="prize" value="b420deb3" />

        <div class="c">
            <div class="l">
                <a class="l-1" href="/lotteryinformation/1/view">新用户注册领18元彩金</a>
                <a class="l-2" href="/lotteryinformation/88/view">奖金最高多送80%</a>
                <a class="l-3" href="/lotteryinformation/2/view">新注册用户首次充值多送30%</a>
            </div>
            <ul class="r">
                <li class="r1">用户注册</li>
                <li class="r2">
                    <span>*</span>
                    <h5>用户名：</h5>
                    <input name="username" value="{{ Input::get('username') }}" id='J-username' type="text" placeholder="建议使用QQ/邮箱注册"/>
                </li>

                <li class="username-notice"></li>
                <li class="r3">
                    <span>*</span>
                    <h5>密码：</h5>
                    <input  name="password" id='J-password' type="password" placeholder="请输入6-16位字母或数字作为密码"/>
                </li>

                <li class="password-notice"></li>
                <li class="sj">
                    <span>*</span>
                    <h5>手机号：</h5>
                    <input name="phone" id='J-password2' type="text" placeholder="请输入11位正确手机号以便通知您领取大奖"/>
                </li>

                <li class="sj-notice"></li>


                @if (($iRegisterNum = UserUser::getRegisterNum(get_client_ip())) && $iRegisterNum > 1)
                <li class="vcode">
                    <span>*</span>
                    <h5>验证码：</h5>
                    <input name="captcha" id="J-vcode" placeholder="{{ __('_basic.captcha') }}"/>
                    <img width="110" height="47" class="vcode-img" id="J-vcode-img"  data-src="{{ URL::to('captcha?') }}" src="{{ URL::to('captcha?') }}">
                    <!--<img width="110" height="47" class="vcode-img" id="J-vcode-img" onclick="changeCaptcha();" data-src="{{ URL::to('captcha?') }}" src="{{ URL::to('captcha?') }}">-->
                </li>
                <li class="vcode-notice"></li>
                @endif




                <li class="login-r5">
                    <input type="checkbox" checked/>
                    <h5>我已年满十八周岁已阅读并同意接受<a target="_blank" href="/client/agreement/index.html">《服务协议》</a></h5>
                </li>
                <li class="r5-notice">

                </li>
                <li class="r6">
                    <input type="button" value="提交注册"/>
                </li>
            </ul>
            </div>
    </form>
</div>


@include('w.client.footer')
@stop


@section('end')
 @parent
<script>
    (function($){
        var username_ok = false,
                password_ok = false,
                password2_ok =false,
                vcode_ok = true;

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


//            $.each(arr, function(){
//                if(this.parents('.form-group').hasClass(CLS)){
//                    isPass = false;
//                    return false;
//                }
//            });
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
//                    global_isLoading = true;
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
//                    global_isLoading = false;
                }
            });
        }
        function check_username(){
            var el = username,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';

            if(!(/[A-Za-z0-9]{6,16}/).test(v)){
                $('.username-notice').text('用户名格式不对，请重新输入');
                username_ok=false;
            }else{
                check_username_service(v, function(data){
                    if(Number(data['isSuccess']) == 1){
                        $('.username-notice').text('');
                        username_ok=true;
//                        console.log('username_ok_is'+username_ok)
                    }else{

                        $('.username-notice').text('该用户名已被注册，请重新输入');
                        username_ok=false;
//                        console.log('username-no')
//                        par.find('.text-danger').text('该用户名已被注册，请重新输入');
                    }
                });

            }
        }

        username.blur(check_username);

        function check_password(){
            var el = password,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
            if(!(/[A-Za-z0-9]{6,16}/).test(v)){
                $('.password-notice').text('密码格式不对，请重新输入');
                password_ok=false;

            }else if($.trim(username.val()) == $.trim(password.val())){
                $('.password-notice').text('登录密码不能和用户名相同，请重新输入');
                password_ok=false;

            }else{
                $('.password-notice').text('');
                password_ok=true;
            }

//            console.log('userpassword_ok_is'+password_ok)
        }
        password.blur(check_password);
        function check_password2(){
            var el = password2,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';

//            这是验证密码
//            if(!(/[A-Za-z0-9]{6,16}/).test(v) || $.trim(password.val()) != v){
//                par.addClass(CLS).removeClass(CLSR);
//            }else{
//                par.removeClass(CLS).addClass(CLSR);
//            }

            //下方验证手机号
            if(!(/^1[3|4|5|7|8]\d{9}$/.test(v))){
//                alert(1);
                password2_ok=false;
                $('.sj-notice').text('手机号有误，请重新输入');
            }else{
//                alert(2);
                $('.sj-notice').text('');
                password2_ok=true;
//                $('.sj-notice').text('');

            }
//            console.log('shouji_ok_is'+password2_ok)

        }
        password2.blur(check_password2);

        function check_vcode_service(v, callback){
            $.ajax({
                url:"/auth/check-captcha-error?captcha=" + v,
                dataType:'json',
                beforeSend:function(){
//                    global_isLoading = true;
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
//                    global_isLoading = false;
                }
            });
        }
        function check_vcode(){
            var el = vcode,v = $.trim(el.val()),par = el.parents('.form-group'),CLS = 'has-error',CLSR = 'has-right';
//            par.find('.vcode-notice').text('');

            if($('.vcode').length>0){
                if(!(/[A-Za-z0-9]{5}/).test(v)){
                    $('.vcode-notice').text('请输入完整')
                    vcode_ok=false;
                }else{
                    check_vcode_service(v, function(data){
                        if(Number(data['isSuccess']) == 1){
                            $('.vcode-notice').text('')
                            vcode_ok=true;
                        }else{
                            vcode_ok=false;
                            $('.vcode-notice').text('验证码不正确，请重新输入')

                        }
                    });
                }
            }


//            console.log('yzm'+vcode_ok)

        }
        vcode.blur(check_vcode);



        //刷新验证码
        $('#J-vcode-img, #J-vcode-img-text').click(function(){
            check_vcode();
            var el = $(this),src = el.attr('data-src');
            $('#J-vcode-img').attr('src', src + '_='+ Math.random());
        });




        $('.r6 input').click(function () {
            var a = false;
            check_username();
            check_password();
            check_password2();
            check_vcode();

            if($('.login-r5 input').is(':checked')){
                a=true;
                $('.r5-notice').text('');
            }else {
                a=false;
                $('.r5-notice').text('请勾选服务协议后再次点击注册按钮')
            }


            if(vcode_ok&&a&&username_ok&&password_ok&&password2_ok){
//            if(a&&username_ok&&password_ok&&password2_ok){
                console.log('全部通过')
                $('#password_confirmation_id').val($('#J-password').val());
                $('#J-form').submit();

            }else {

                return false;
            }


        });
        $(document).keypress(function(e) {
            // 回车键事件
            if(e.which == 13) {
                jQuery(".r6 input").click();
            }
        });


//        (function(){
//            $('#J-float-chart').css('left', $('#J-spo-header').offset().left + $('#J-spo-header').width() + 10 - 200);
//        })();



    })(jQuery);
</script>

@stop