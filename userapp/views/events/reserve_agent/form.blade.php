<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>预约成为博猫总代</title>

@section ('styles')
      {{ style('merchants')}}
@show

@section('javascripts')
  {{ script('jquery-1.9.1') }}
  {{ script('jquery.easing.1.3') }}
  {{ script('jquery.mousewheel') }}
  {{ script('bomao.base') }}
  {{ script('bomao.Tab') }}
  {{ script('bomao.Slider') }}
  {{ script('bomao.Select') }}
  {{ script('bomao.Mask') }}
  {{ script('bomao.MiniWindow') }}
  {{ script('swfobject') }}
@show


</head>

<body style=" background:none; ">
{{ Form::open(['route' => ('reserve-agent.reserve'), 'method' => 'post', 'files' => true, 'class' => 'formbox' , 'id' => 'form-1']) }}
 @if ( !Session::get('success')&&!Session::get('error'))
		<table width="100%" cellpadding="0" cellspacing="20" id="J-form-table">
			<tr>
				<td align="right" width="100">您的QQ</td>
				<td><input name="qq" class="input long" type="text" placeholder="您的qq号码" id="J-form-qq" /></td>
			</tr>
			<tr>
			  <td align="right">&nbsp;</td>
			  <td><span class="tip">为方便我们与您联系，建议您开通此QQ的临时会话功能</span></td>
		  </tr>
			<tr>
			  <td align="right">您所代理的平台</td>
			  <td><input class="input long" name="platform"  type="text" placeholder="如：大发娱乐，明升" id="J-form-platform" /></td>
		  </tr>
			<tr>
			  <td align="right">&nbsp;</td>
			  <td>&nbsp;</td>
		  </tr>
			<tr>
			  <td align="right">您的日均销售额</td>
			  <td>
			  	<select name="sale" style="float:left;" id="J-reg-sale" style="display:none;">
					<option selected="selected" value="0">小于10万</option>
					<option value="1">10万-30万</option>
					<option value="2">30万-50万</option>
					<option value="3">50万-70万</option>
					<option value="4">70万-100万</option>
					<option value="5">大于100万</option>
				</select>
			  	<input class="input long" type="text" style="float:left;display:none;" />
				<input type="button" class="input-file-cover" />
				<input type="file" name="portrait" class="input-file" id="J-form-file" title="图片限1M内，仅支持JPG/PNG/GIF格式"/>
				<span class="file-tip" title="图片限1M内，仅支持JPG/PNG/GIF格式">上传截图会优先与您联系</span>
			</td>
		  </tr>
			<tr>
			  <td align="right">&nbsp;</td>
			  <td>&nbsp;</td>
		  </tr>
			<tr>
			  <td align="right">验证码</td>
			  <td>
			  	<input class="input" name="vcode" type="text" id="J-form-vcode" />
			  	<img  class="img-code" id="captchaImg" width="100" height="35" src="{{ Captcha::img() }}"/>
				<a href="javascript:changeCaptcha();" title="{{ Lang::get('transfer.Captcha') }}" class="img-code-change">换一张</a>
			</td>
		  </tr>
			<tr>
			  <td align="right">&nbsp;</td>
			  <td>&nbsp;</td>
		  </tr>
			<tr>
			  <td align="right">&nbsp;</td>
			  <td>

                <input id="J-form-submit" class="button-submit" type="submit" style=" border: 0;" value=" ">
			  </td>
		  </tr>
	  </table>

        @endif


 @if ($message = Session::get('success'))
        <div class="tip-success" style="" id="J-form-success"></div>
        @endif
        @if ($message = Session::get('error'))
         <div class="tip-fail" style="" id="J-form-fail">
            <div class="tip-fail-pic"></div>
            <div class="tip-fail-error" id="J-form-fail-error-text">{{ $message }}</div>
        	<span class="button-return"  id="J-button-fial-back"></span>
        	</div>
        </div>
        @endif

{{Form::close() }}



<script type="text/javascript">
function changeCaptcha () {
        // debugger;
        captchaImg.src = {{ '"'.URL::to('captcha?').'"' }} + ((Math.random()*9 +1)*100000).toFixed(0);
    }
    //返回按钮
	$('#J-button-fial-back').click(function(e){

		window.location.href='{{ route('reserve-agent.form')  }} ';
	});
(function($){
//注册弹窗相关 ========================================================
	//注册弹窗下拉框
	var showFormSuccess = function(){
		$('#J-form-table').hide();
		$('#J-form-fail').hide();
		$('#J-form-success').show();
	};
	var showFormFail = function(msg){
		$('#J-form-table').hide();
		$('#J-form-success').hide();
		$('#J-form-fail').show();
		$('#J-form-fail-error-text').html(msg);
	};
	var refreshCode = function(){
		var img = $('#J-form-code-img');
		img.attr('src', img.attr('data-path') + '?' + Math.random());
	};
	new bomao.Select({realDom:'#J-reg-sale'});
	$('#J-form-submit').click(function(e){
//		e.preventDefault();
		var qq = $('#J-form-qq'),
			platform = $('#J-form-platform'),
			sale = $('#J-reg-sale'),
			vcode = $('#J-form-vcode');

		if(!(/^\d{5,20}$/).test($.trim(qq.val()))){
			alert('您的qq号填写不正确，请重新填写');
			qq.focus();
			return false;
		}
		if($.trim(platform.val())!='' && !(/^.{2,20}$/).test($.trim(platform.val()))){
			alert('当前所在平台名称填写不正确，请重新填写');
			platform.focus();
			return false;
		}
		if(!(/^[a-zA-Z0-9]{5}$/).test($.trim(vcode.val()))){
			alert('验证码填写不正确，请重新填写');
			vcode.focus();
			return false;
		}

//		$.ajax({
//			url:$('#J-hidden-ajaxpath').val(),
//			cache:false,
//			method:'post',
//			dataType:'json',
//
//			data:{'qq':$.trim(qq.val()), 'platform':$.trim(platform.val()), 'sale':sale.val(), 'vcode':$.trim(vcode.val())},
//			success:function(data){
//				if(Number(data['isSuccess']) == 1){
//					showFormSuccess();
//				}else{
//					showFormFail(data['msg']);
//				}
//			}
//		});
	});

	$('#J-form-code-change').click(function(e){
		e.preventDefault();
		refreshCode();
	});
	$('#J-form-code-img').click(function(){
		refreshCode();
	});

	var formDom = $('#J-panel-form');
	var showForm = function(){
		mask.show();
		formDom.show();
	};
	var hideForm = function(){
		formDom.hide();
		mask.hide();
	};
	formDom.find('.close').click(function(e){
		e.preventDefault();
		hideForm();
	});

})(jQuery);
</script>

</body>
</html>