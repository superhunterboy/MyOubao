<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <title>史上最佳代理扶持 - 博狼娱乐</title>
    <link href="/assets/images/global/global.css" rel="stylesheet" type="text/css" media="all">
    <link rel="stylesheet" href="events/fuchi-z/style/style.css"/>

</head>
<body>
	<div class="global-top">
	<div class="g_33 cont clearfix">
		<a title="博狼娱乐首页" class="logo" href="/">博狼娱乐</a>
		
		<a href="/" class="link link-home" target="_blank"><span>博猫主站</span></a>
		<a href="/pc-client/index.html" class="link link-fastlogin" target="_blank"><span>PC客户端</span></a>
		<a href="/mobile" class="link link-mobile" target="_blank"><span>手机客户端</span></a>
	</div>
</div>
	<div class="reg-banner"></div>
	<div class="part1">
		<div class="slider slider1">
			<div class="slider-inner">
				<ul class="slider-pic clearfix">
					<li class="slider-pic1 current"><img src="/events/fuchi-z/images/brand.jpg" alt="" /></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="part2">
		<div class="warper">
			<div class="text text-baidu">提取码：52s4</div>
			<div class="text text-360">提取码：j13c</div>
			<div class="text text-baidu text-baidu-2">提取码：a093</div>
			<div class="text text-360 text-360-2">提取码：c311</div>

			<a href="http://pan.baidu.com/s/18OkgA" target="_blank" class="button button-baidu">百度云盘下载</a>
			<a href="http://yunpan.cn/cAn5RXQbBm4Kw" target="_blank"  class="button button-baidu-2">360云盘下载</a>
			<a href="http://pan.baidu.com/s/1dDu4QEd" target="_blank" class="button button-360">百度云盘下载</a>
			<a href="http://yunpan.cn/cAn5kFz9Sxyk4" target="_blank" class="button button-360-2">360云盘下载</a>
		</div>
	</div>
	<div class="part3" id="tab-slider">
		<div class="tab-title clearfix">
			<a href="javascript:void(0);" class="current">代理活动</a>
			<a href="javascript:void(0);">玩家活动</a>
		</div>
		<div class="slider slider2">
			<div class="slider-inner">
                @include('adTemp.9')
			</div>
			<a href="javascript:void(0);" class="slider-prev"></a>
			<a href="javascript:void(0);" class="slider-next"></a>
		</div>
		<div class="slider slider3">
			<div class="slider-inner">
                @include('adTemp.8')
			</div>
			<a href="javascript:void(0);" class="slider-prev"></a>
			<a href="javascript:void(0);" class="slider-next"></a>
		</div>
	</div>
	<div class="part4"></div>
	<div class="footer clearfix">
		<div class="g_33">
			<div class="footer-cont">
				<div class="foot-link">
                    <a href="/brand">博猫品牌</a>   /
                    <a href="/help">帮助中心</a>   /
                    <a href="/introduce#contact">联系我们</a>   /
                    <a href="/mobile" target="_blank">手机客户端</a>   /
                    <a href="/pc-client/index.html" target="_blank">PC客户端</a>
				</div>
                <div class="copy">&copy; 2014 博狼娱乐版权所有 <font>尊享欧亚双重博彩牌照认证</font></div>
                <div class="foot-warning">博狼娱乐郑重提示：彩票有风险，投注需谨慎，不向未满18周岁的青少年出售彩票</div>
			</div>
		</div>
	</div>

</body>
<script type="text/javascript" src="events/fuchi-z/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="events/fuchi-z/js/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="events/fuchi-z/js/bomao.base.js"></script>
<script type="text/javascript" src="events/fuchi-z/js/bomao.Tab.js"></script>
<script type="text/javascript" src="events/fuchi-z/js/bomao.Slider.js"></script>
<script>
(function($){
	new bomao.Tab({par:'#tab-slider', triggers:'.tab-title a', panels:'.slider', eventType:'click'});


	var slider2 = new bomao.Slider({par:'.slider2', triggers:'.slider-num li', panels:'.slider-pic li', sliderDirection:'left', sliderIsCarousel:true});
	$('.slider2').find('.slider-prev').click(function(e){
		e.preventDefault();
		slider2.controlPre();
	});
	$('.slider2').find('.slider-next').click(function(e){
		e.preventDefault();
		slider2.controlNext();
	});


	var slider3 = new bomao.Slider({par:'.slider3', triggers:'.slider-num li', panels:' .slider-pic li', sliderDirection:'left', sliderIsCarousel:true});
	$('.slider3').find('.slider-prev').click(function(e){
		e.preventDefault();
		slider3.controlPre();
	});
	$('.slider3').find('.slider-next').click(function(e){
		e.preventDefault();
		slider3.controlNext();
	});


})(jQuery);
</script>

</html>