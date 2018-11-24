@section('title')
登录
@parent
@stop


@section ('styles')
@parent
{{ style('login-v4') }}
{{ style('animate') }}
@stop


@section('container')


@include('w.client.auth-header')

<div class="reg">
    {{ Form::open(array('class' => 'login-form', 'role' => 'form', 'target' => '_top', 'name' => 'signinForm')) }}
    <input type="hidden" name="_random" value="{{ createRandomStr() }}" />
    <div class="a">


        <div class="l">

        </div>
        <ul class="r">
            <li class="r1">用户登录</li>
            <li class="r2">
                <span>*</span>
                <h5>用户名：</h5>
                <input id="login-name" name="username" type="text" value="{{ Input::old('username') }}" placeholder="{{ __('_user.username') }}"   required autofocus/>
            </li>

            <li class="r3">
                <span>*</span>
                <h5>密码：</h5>
                <input id='login-pass' name="" type="password"  placeholder="{{ __('_user.password') }}" required/>
                <input name="password" id="login-pass-real" type="hidden" required>
            </li>

            @if ($bCapcha = Session::get('LOGIN_TIMES') && Session::get('LOGIN_TIMES') > 2)
            <li class="vcode">
                <span>*</span>
                <h5>验证码：</h5>
                <input name="captcha"  placeholder="{{ __('_basic.captcha') }}"/>
                <img width="110" height="47" class="vcode-img" id="J-vcode-img" onclick="changeCaptcha();" src="{{ Captcha::img() }}">
            </li>
            @endif


            <li class="r4">
                <span class="s1 {{ $errors->first('attempt')?'':'hidden'; }}">
                     {{ $errors->first('attempt') }}
                </span>
                            <span class="s2">
                                没有账号？
                                <a href="/auth/signup">免费注册</a>
                            </span>
            </li>
            <li class="r5">
                <input type="submit" value="登 录" id="J-button-submit"/>
            </li>
        </ul>
    </div>
    {{ Form::close() }}

</div>
@include('w.client.footer')
@stop





@section('end')
@parent
{{ script('md5') }}
<script type="text/javascript">
    function changeCaptcha () {
        $('.vcode-img').attr('src', "{{ URL::to('captcha?') }}" + ((Math.random()*9 +1)*100000).toFixed(0));
    };

    $(function(){
        $('#J-button-submit').click(function (e) {

            var pwd = $('#login-pass').val();
            var username = ($('#login-name').val()).toLowerCase();
            $('#login-pass-real').val(md5(md5(md5(username + pwd))));
            $('form[name=signinForm]').submit();
        });
        $('form[name=signinForm]').keydown(function(event) {
            if (event.keyCode == 13) $('#loginButton').click();
        });
    });



    $('#J-video-cover').click(function(){
        var el = $(this);


        var so = new SWFObject("/assets/images/video/vcastr2/vcastr2.swf","ply","950","460","9","#000000");
        so.addParam("allowfullscreen","true");
        so.addParam("allowscriptaccess","always");
        so.addParam("wmode","opaque");
        so.addParam("quality","high");
        so.addParam("salign","lt");
        so.addVariable("vcastr_config", "1|0|100|0|0|0x000033|60|0x66ff00|0xffffff|0xffffff||/assets/images/global-v4/logo.png||1");
        so.addVariable("vcastr_file","/assets/images/video/bomao640-480.flv");
        //console.log(so.getSWFHTML());
        //so.write("J-video-flash");
        $('#J-video-flash').html(so.getSWFHTML());

        setTimeout(function(){
            el.fadeOut();
        }, 400);

    });


    setTimeout(function(){
        $('.s-1 .line').fadeIn();
    }, 1000);

</script>






@stop