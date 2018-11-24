@extends('l.outer')
@section('title')
	注册
@parent
@stop

@section('container')
	@include('authority.outerHeader')
	

	
	@if (isset($oRegisterLink) && $oRegisterLink && $oRegisterLink->agent_qqs)
	<?php
		$aAgentQQs = explode(',', $oRegisterLink->agent_qqs);
	?>
	<div class="service">
		<h4>咨询上级代理</h4>
		@foreach($aAgentQQs as $key => $value)
		<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin={{ $value }}&site=qq&menu=yes"><img border="0" src="http://wpa.qq.com/pa?p=2:{{ $value }}:51" alt="点击这里给我发消息" title="点击这里给我发消息"/></a>
		@endforeach
	</div>
	@endif


	<div class="container-fluid container-main container-main-a-a bcontainer-main-login">
		<div class="container">
			<p class="title-text">
				我们是一家权威认证的购彩平台<br />
		    	<span class="sm">12年经验为您挡风雨、稳发展</span>
		    	<a style="margin-right: 110px;" href="{{ route('brand') }}" class="more" target="_blank">详情</a>
		    </p>



			<div class="account-wrapper">

			    <div class="account-body">
			      <form id="J-form" class="form-horizontal account-form" method="POST" action="{{ $sKeyword ? route('signup', ['prize' => $sKeyword]) : route('signup') }}">
			      <input type="hidden" name="_token" value="{{ csrf_token() }}">
			      <input type="hidden" name="_random" value="{{ createRandomStr() }}">

			      	<h2>注册代理</h2>

			      	<hr />

			        <div class="form-group">
			          <label for="J-username" class="col-sm-2 control-label">用户名</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="username" value="{{ Input::get('username') }}" type="text" class="form-control" id="J-username" tabindex="1" placeholder="6-16位字符，可使用字母或数字">
			          	<p class="text-tip text-danger">用户名格式不对，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->

			        <div class="form-group">
			          <label for="J-password" class="col-sm-2 control-label">密码</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="password" type="password" class="form-control" id="J-password" tabindex="2" placeholder="6-16位字符，可使用字母或数字">
			          	<p class="text-tip text-danger">密码格式不对，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->

			        <div class="form-group">
			          <label for="J-password2" class="col-sm-2 control-label">确认密码</label>
			          <div class="col-sm-10 feild-row">
			          	<input name="password_confirmation" type="password" class="form-control" id="J-password2" tabindex="3" placeholder="请再次输入密码">
			          	<p class="text-tip text-danger">两次输入的密码不相同，请重新输入</p>
			          	<span class="ico-right"></span>
			          </div>
			        </div> <!-- /.form-group -->



					@if (($iRegisterNum = UserUser::getRegisterNum(get_client_ip())) && $iRegisterNum > 1)
					<div class="form-group">
						  <label for="J-vcode" class="col-sm-2 control-label">验证码</label>
						  <div class="col-sm-6 feild-row">
						  	<input style="margin-top:2px;" name="captcha" type="text" maxlength="5" class="form-control" id="J-vcode" tabindex="4"  placeholder="不区分大小写">
						  	<p class="text-tip text-danger">验证码输入有误</p>
						  	<span class="ico-right"></span>
						  </div>
						  <div class="col-sm-4">
						  	<img id="J-vcode-img" data-src="{{ URL::to('captcha?') }}" src="{{ URL::to('captcha?') }}" class="vcode" width="100%" height="35" />
						  	<span id="J-vcode-img-text" data-src="{{ URL::to('captcha?') }}" class="vcode-text-small" style="font-size:11px;">看不清楚? 换张图片</span>
						  </div>
					</div>
					@endif
					<!-- /.form-group -->
					
			        



			        <div class="form-group">
			          <div class="col-sm-12">
			          	<button id="J-button-submit" type="submit" class="btn btn-primary btn-form-submit">立即注册</button>
			          </div>
			        </div> <!-- /.form-group -->

			        


			      </form>
			    </div> <!-- /.account-body -->

			  </div>


			  <div class="bottom-text">
			  	博猫平台优势
			  </div>


		</div>

	</div>



	<div class="container-fluid container-text-list">
		<div class="container">
			
			<div class="col col-1 col-sm-4">
				<div class="title">100%兑现赔付</div>
				<div class="text">与主集团统一管理，奖金100%即刻兑现，保您畅玩无忧！</div>
			</div>
			<div class="col col-2 col-sm-4">
				<div class="title">12年行业龙头</div>
				<div class="text">主集团12年稳健经营，资金雄厚，管控健全，挡风雨稳发展！</div>
			</div>
			<div class="col col-3 col-sm-4">
				<div class="title">3分钟提款到账</div>
				<div class="text">100元起提，每日提款最高可达100万元，充值30秒到账，0手续费！</div>
			</div>
			<div class="col col-4 col-sm-4">
				<div class="title">19款精品游戏</div>
				<div class="text">玩法齐全，支持元角分，自主彩瑞士硬件开奖公正公平！</div>
			</div>
			<div class="col col-5 col-sm-4">
				<div class="title">24小时优质服务</div>
				<div class="text">专业客服全年24小时在线，热情细致，解决所有问题！</div>
			</div>
			<div class="col col-6 col-sm-4">
				<div class="title">100%安心娱乐</div>
				<div class="text">全球顶级硬件安全设备保护资金信息安全，平台快速稳定！</div>
			</div>


		</div>
	</div>







	@include('w.common-footer')
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
            url:"/authority/check-username-is-exist?username=" + v,
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


})(jQuery);
</script>
@stop








