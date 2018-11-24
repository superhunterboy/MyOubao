@extends('l.outer-spo')
@section('title')
	竞彩开售！注册即享高返点
@stop





@section('container')

@section ('styles')
@parent
	{{ style('spo')}}
@stop

<style media="screen">
		body {
				background: url(/assets/images/spo/bg-dg.jpg) no-repeat center 0
		}
</style>


	<div class="container-fluid container-main container-main-spo container-main-spo-dg bcontainer-main-login">
		<div class="container">




			<div class="account-wrapper clearfix" id="J-spo-header">
				<div class="account-left">

				</div>


			    <div class="account-body">
			    	<div class="login-tip"></div>
			      <form id="J-form" class="form-horizontal account-form" method="POST" action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}">
			      <input type="hidden" name="_token" value="{{ csrf_token() }}">
			      <input type="hidden" name="_random" value="{{ createRandomStr() }}">

			        <div class="form-group">
			          <label for="J-username" class="col-sm-2 control-label"></label>
			          <div class="col-sm-10 feild-row">
			          		<span class="reg-title">用户注册</span>
			          		<div class="r-title">单关返点5%，串关7.5%</div>
			          </div>
			        </div> <!-- /.form-group -->

			        <div class="form-group">
			          <label for="J-username" class="col-sm-2 control-label">用户名</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="username" value="{{ Input::get('username') }}" type="text" class="form-control f-username" id="J-username" tabindex="1" placeholder="请输入您的QQ号作为用户名">
			          	<p class="text-tip text-danger">用户名格式不对，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->

			        <div class="form-group">
			          <label for="J-password" class="col-sm-2 control-label">密码</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="password" type="password" class="form-control f-password" id="J-password" tabindex="2" placeholder="6-16位字符，可使用字母或数字">
			          	<p class="text-tip text-danger">密码格式不对，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->

			        <div class="form-group">
			          <label for="J-password2" class="col-sm-2 control-label">确认密码</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="password_confirmation" type="password" class="form-control f-password2" id="J-password2" tabindex="3" placeholder="请再次输入密码">
			          	<p class="text-tip text-danger">两次输入的密码不相同，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->



					@if (($iRegisterNum = UserUser::getRegisterNum(get_client_ip())) && $iRegisterNum > 1)
					<div class="form-group">
						<label for="J-vcode" class="col-sm-2 control-label">验证码</label>
						<div class="col-sm-8 feild-row">
							<input name="captcha" type="text" maxlength="5" class="form-control f-vcode" id="J-vcode" tabindex="4" placeholder="不区分大小写">
							<p class="text-tip text-danger">验证码输入有误</p>
							<span class="ico-right"></span>
						</div>
						<div class="col-sm-4" style="padding-left:0;padding-right:20px;">
							<img id="J-vcode-img" data-src="{{ URL::to('captcha?') }}" src="{{ URL::to('captcha?') }}" class="vcode" width="100%" height="33" />
							<span style="display:none;" id="J-vcode-img-text" data-src="{{ URL::to('captcha?') }}" class="vcode-text-small" style="font-size:11px;">看不清楚? 换张图片</span>
						</div>
					</div> <!-- /.form-group -->
					@endif




			        <div class="form-group">
			          <div class="col-sm-12 cont-submit" style="position:relative;">
			          	<button id="J-button-submit" type="submit" class="btn btn-primary btn-form-submit">注册即享高返点</button>
			          	<a href="http://www.bomao.com/auth/signin" class="tologin">已有账号</a>
			          </div>
			        </div> <!-- /.form-group -->



			        <div class="form-group">
			          <label for="J-password2" class="col-sm-2 control-label">确认密码</label>
			          <div class="col-sm-10 feild-row">
			          	<p class="qq-panel">
			          		<a class="aa aa-1" href="http://wpa.qq.com/msgrd?v=3&uin=35347526&site=qq&menu=yes">客服QQ:35347526</a>
			          		<span class="aa aa-2">活动Q群: 437299178</span>
			          		<span class="aa aa-3">活动Q群: 517196163</span>
			          	</p>
			          </div>
			        </div> <!-- /.form-group -->



			      </form>
			    </div> <!-- /.account-body -->

			  </div>



			  <div class="spo-text-2">
				<div class="inner">
					<a class="cc" href="http://digi.163.com/14/1112/14/AARVH2NS001618JV.html" target="_blank"></a>
					<a class="cc" href="http://tech.hexun.com/2014-11-12/170317217.html" target="_blank"></a>
					<a class="cc" href="http://it.msn.com.cn/563449/306337724856b.shtml" target="_blank"></a>
					<a class="cc" href="http://digital.ynet.com/465522/714460635756b.shtml" target="_blank"></a>
					<a class="cc" href="http://www.pcpop.com/view/1/1052/1052817.shtml?r=12141416" target="_blank"></a>
					<a class="cc" href="http://news.csdn.net/article.html?arcid=15820772&preview=1" target="_blank"></a>
					<a class="cc" href="http://tech.xinmin.cn/internet/2014/11/05/25855465.html" target="_blank"></a>
					<a class="cc" href="http://news.uuu9.com/2014/201411/352218.shtml" target="_blank"></a>


					<a class="cc" href="http://roll.sohu.com/20141105/n405791537.shtml" target="_blank"></a>
					<a class="cc" href="http://game.huanqiu.com/news/2014-11/5191589.html" target="_blank"></a>
					<a class="cc" href="http://news.duowan.com/1411/279115466876.html" target="_blank"></a>
					<a class="cc" href="http://game.21cn.com/online/c/a/2014/1105/10/28502546.shtml" target="_blank"></a>
					<a class="cc" href="http://game.china.com/mobile/hardware/11106781/20141105/18929995.html" target="_blank"></a>
					<a class="cc" href="http://www.40407.com/news/201411/468703.html" target="_blank"></a>
					<a class="cc" href="http://xin.52pk.com/list/201411/6223258.shtml" target="_blank"></a>
					<a class="cc" href="http://tech.91.com/content/141112/21761196.html" target="_blank"></a>

			  	</div>
			  </div>

		</div>

	</div>


	<div class="c-2">

	</div>





@stop


@section('end')
@parent
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
            error:function(){
                alert('网络请求失败，请刷新页面重试');
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
            error:function(){
                alert('网络请求失败，请刷新页面重试');
            },
            complete:function(){
                global_isLoading = false;
            }
        });
	}
	function check_vcode(){
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
	}
	vcode.blur(check_vcode);


	$('#J-button-submit').click(function(){
		var el = $(this);
		if(global_isLoading || !checkFormPass()){
			return false;
		}
		el.attr('disabled', 'disabled');
		$('#J-form').submit();
	});


	//刷新验证码
	$('#J-vcode-img, #J-vcode-img-text').click(function(){
		var el = $(this),src = el.attr('data-src');
		$('#J-vcode-img').attr('src', src + '_='+ Math.random());
	});


	(function(){
		$('#J-float-chart').css('left', $('#J-spo-header').offset().left + $('#J-spo-header').width() + 10 - 200);
	})();


})(jQuery);
</script>

@if($need_cnzz)
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan style='display:none' id='cnzz_stat_icon_1259482517'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1259482517' type='text/javascript'%3E%3C/script%3E"));</script>
@endif
@stop
