<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>游戏手机客户端 - 博狼娱乐</title>

<link type="text/css" rel="stylesheet" href="/events/mobile/images/mobile.css" />
<link type="text/css" rel="stylesheet" href="/events/mobile/images/animate.css" />


</head>

<body>



<div class="banner-bg">

</div>



<div class="top">
	<div class="g_33">
		<a href="{{ route('home') }}" class="logo" title="博猫首页">博猫首页</a>
		<a href="{{ route('home') }}" class="tohome" title="博猫官网" target="_blank">博猫官网</a>
	</div>
</div>


<div class="content">
	<div class="g_33" id="J-main-cont">
<!-- 
		<div class="animated bounceInUp float agent-tip">
			本软件专为博猫玩家设计，代理请从电脑登录享受更多服务
		</div> -->

		<div class="animated zoomIn float phone"></div>
		<div class="animated zoomIn float iphone"></div>

		<div class="animated bounceInDown float title"></div>

		{{--
		<a href="/mobile-download" target="_blank" class="animated bounceInRight float download download-a"></a>
		
		<a href="#" class="animated zoomIn float download download-b"></a>
		--}}

		<div class="animated bounceInUp float text-code">
			<a class="text-guider" href="/mobile/help">请点击这里</a>
		</div>


		{{--
		<div id="J-cont-line" class="animated zoomIn float line"></div>


		
		<div class="animated bounceInUp float install install-a" id="J-install-a">
			<ul class="tab-title clearfix">
				<li class="current">方法1 手机安装</li>
				<li>方法2 电脑安装</li>
			</ul>
			<div class="tab-content tab-content-1 panel-current">
				<div class="tab-cont-inner">
					<div class="float ico"></div>
				</div>
			</div>
			<div class="tab-content tab-content-2">
				<div class="tab-cont-inner">
					<a href="/mobile-download" target="_blank;" class="down-url" title="下载IOS安装包"></a>
					<a href="http://zs.91.com/baidu91/pc1" target="_blank;" class="link-1" title="91助手"></a>
					<a href="http://pro.25pp.com/" target="_blank;" class="link-2" title="PP助手"></a>
				</div>
			</div>
		</div>



		<div class="animated bounceInUp float install install-b" id="J-install-b" style="display:none;">
			<ul class="tab-title clearfix">
				<li class="current">方法1 手机安装</li>
				<li>方法2 电脑安装</li>
			</ul>
			<div class="tab-content tab-content-1 panel-current">
				<div class="tab-cont-inner">
					<div class="float ico"></div>
				</div>
			</div>
			<div class="tab-content tab-content-2">
				<div class="tab-cont-inner">
					<a href="#" target="_blank;" class="down-url" title="下载安卓安装包"></a>
					<a href="#" target="_blank;" class="link-1" title="91助手"></a>
					<a href="#" target="_blank;" class="link-2" title="PP助手"></a>
				</div>
			</div>
		</div>
		--}}



	</div>
</div>




<div class="part-2" id="J-html-cont-part-2">

</div>
<script type="text/template" id="J-tpl-part2">
	<div class="g_33">
		<div class="animated slideInLeft float text"></div>
		<div class="animated slideInRight float img"></div>
	</div>
</script>




<div class="part-3" id="J-html-cont-part-3">

</div>
<script type="text/template" id="J-tpl-part3">
	<div class="animated bounceInUp inner"></div>
	<div class="g_33">
		<div class="animated bounceInDown text"></div>
	</div>
</script>




<div class="part-4" id="J-html-cont-part-4">
</div>
<script type="text/template" id="J-tpl-part4">
	<div class="g_33">
		<div class="animated slideInLeft float text"></div>
		<div class="animated slideInRight float img"></div>
	</div>
</script>




<div class="part-5" id="J-html-cont-part-5">
</div>
<script type="text/template" id="J-tpl-part5">
	<div class="g_33">
		<div class="animated slideInLeft float text"></div>
		<div class="animated slideInRight float img"></div>
	</div>
</script>




<div class="footer">
	博猫彩票郑重提示：彩票有风险，投注需谨慎 不向未满18周岁的青少年出售彩票
</div>





<script type="text/javascript" src="/events/mobile/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/events/mobile/js/bomao.base.js"></script>
<script type="text/javascript" src="/events/mobile/js/bomao.Tab.js"></script>
<script>
(function($){
	var panel = $('#J-main-cont'),downloads  = panel.find('.download');

	new bomao.Tab({par:'#J-install-a', triggers:'.tab-title li', panels:'.tab-content'});
	new bomao.Tab({par:'#J-install-b', triggers:'.tab-title li', panels:'.tab-content'});

	/**
	downloads.click(function(e){
		var el = $(this),CLS = ' download-a-current',domids = ['#J-install-a', '#J-install-b'],index = downloads.index(this);
		CLS = index == 0 ? 'download-b-current' : ' download-a-current';
		downloads.removeClass(CLS);
		CLS = CLS == 'download-b-current' ? 'download-a-current' : 'download-b-current';
		el.addClass(CLS);
		$.each(domids, function(){
			$(''+this).hide();
		});
		$(domids[index]).show();
		if(index == 1){
			$('#J-cont-line').addClass('line-2');
		}else{
			$('#J-cont-line').removeClass('line-2');
		}

		$(this).show();
		$('#J-cont-line').show();

		e.preventDefault();
	});
	**/



	var win = $(window),cached = {},scrollTimer;
	win.scroll(function(e){
		var top = win.scrollTop();
			if(top > 0){
				if(!cached['part2']){
					$('#J-html-cont-part-2').html($('#J-tpl-part2').html());
					cached['part2'] = true;
				}
			}
			if(top > 530){
				if(!cached['part3']){
					$('#J-html-cont-part-3').html($('#J-tpl-part3').html());
					cached['part3'] = true;
				}
			}
			if(top > 1010){
				if(!cached['part4']){
					$('#J-html-cont-part-4').html($('#J-tpl-part4').html());
					cached['part4'] = true;
				}
			}
			if(top > 1200){
				if(!cached['part5']){
					$('#J-html-cont-part-5').html($('#J-tpl-part5').html());
					cached['part5'] = true;
				}
			}
	});


})(jQuery);
</script>










</body>
</html>
