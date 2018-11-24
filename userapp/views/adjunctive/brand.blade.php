@extends('l.outer')
@section('title')
	欧豹品牌
@parent
@stop

@section('container')
	@include('authority.outerHeader')
	


	<div class="container-fluid container-brand">
		<div class="container" id="J-nav-tab">
			<ul class="nav nav-tabs">
			  <li role="presentation" class="active"><a href="#introduce">品牌介绍</a></li>
			  <li role="presentation"><a href="#media">媒体报道</a></li>
			  <li role="presentation"><a href="#concat">联系我们</a></li>
			</ul>
			<input class="pl" type="hidden" />
			<input class="pl" type="hidden" />
			<input class="pl" type="hidden" />
		</div>

	</div>

	<a name="introduce"></a>
	<div class="container-fluid brand-banner">
		<div class="container">

			<div class="title title-1">
				选欧豹 赢世界
				<p>王者雄心，智胜千里</p>
			</div>
			<div class="title title-2">
				我们是一家权威认证的购彩平台
			</div>
			<div class="title title-3">
				欧豹娱乐于2014年成功上线，拥有菲律宾政府FCLRC (First Cagayan Leisure and Resort Corporation) 颁发的博彩牌照并经第三方游戏平台 GLI (Gaming Laboratories International) 
权威认证，是一家<b style="color:#F7CD1F;">合法、安全、专业</b>的购彩平台，欧豹的主集团拥有12年行业经验。
			</div>

		</div>
	</div>

	<div class="container-fluid brand-part-2">
		
	</div>


	
	<div class="container-fluid brand-introduce">
		<div class="container">
			<div class="title">欧豹娱乐，一路有您，精彩纷呈</div>

			<div class="ptext ptext-1">
				<div class="sm-title">强大的资金实力，<span class="highlight">100%兑现赔付</span>，保您畅玩无忧</div>
				<div class="text">您可以选择其他超高奖金组平台，但欧豹会始终承诺给您最优奖金组和最强资金兑现力的双重保障！我们与主集团始终统一管理，只要您在获奖，无论多少，都100%即刻兑现，确保您的经济利益。</div>
			</div>
			<div class="ptext ptext-2">
				<div class="sm-title">12年健全资金风险管控力，为您<span class="highlight">挡风雨、稳发展</span></div>
				<div class="text">欧豹的主集团十二年成就行业龙头地位，与稳健的资金风险管控力密不可分。欧豹娱乐，拥有主集团的十二年稳健发展背景，风险管控能力远超行业平均水平，完全能为您撑起一把强有力的大伞来挡风遮雨。</div>
			</div>
			<div class="ptext ptext-3">
				<div class="sm-title">人才是第一生产力，精英欧豹，<span class="highlight">独一无二</span></div>
				<div class="text">自欧豹上线以来，无论是程序研发、交互体验、界面设计还是市场活动，都成为众多同行争相效仿的楷模。欧豹团队的每一位成员，都是从千万人中精挑细选出的各领域精英。集团化的科学管理，紧密而融洽的团队合作，欧豹期待与您一起见证下一个十年辉煌！</div>
			</div>


			<br />
			<br />
			<br />
			<br />
			<div class="title">倍受信任的购彩平台</div>
			<div class="centertext">
				欧豹为您提供<span class="highlight">彩票游戏、竞彩足球、电子娱乐场</span>的安全购彩服务。我们精选16款彩票游戏，奖金丰厚，公平公正；竞彩足球全面覆盖亚盘、欧盘热门赛事，提供最好的投注盘口和赔率；电子娱乐场的3款精品赌场游戏，45秒极速开奖，最优惠的丰富红利及最好的客户服务让您赢足百分百！
			</div>
			<div class="game-img">
				<div id="J-brand-slider" class="brand-slider">
					<div class="pl-list-cont">
						<a href="#"><img src="/assets/images/outer/brand/img-1.jpg" /></a>
						<a href="#"><img src="/assets/images/outer/brand/img-3.jpg" /></a>
						<a href="#"><img src="/assets/images/outer/brand/img-2.jpg" /></a>
					</div>
					<div class="triggers">
						<a href="#"></a>
						<a href="#"></a>
						<a href="#"></a>
					</div>
				</div>
			</div>

		</div>
	</div>
    
	<div class="container-fluid brand-video">
		<div class="container">
			<div class="title"></div>
			<div class="text">
				<div class="text-row"><span>我们的初衷：</span>凭借主集团12年成功运营经验与雄厚财富累积，创立新品牌欧豹，再攀行业巅峰！</div>
				<div class="text-row"><span>我们的愿景：</span>未来10年，成为最受尊敬的多元化游戏娱乐平台，让每位玩家都享受高品质生活！</div>
			</div>
			<div class="video-cont">
				@include('adTemp.21')
			</div>

		</div>

	</div>




	<a name="media"></a>
	<div class="container-fluid brand-media">
		<div class="container">
			<div class="title">媒体报道</div>
			<div class="sm-title">时代的革新-欧豹打造电子游戏新品牌</div>
			<div class="img-big">
				<a href="###" target="_blank">
					<img src="/assets/images/outer/brand/media-img.png" />
				</a>
			</div>
			<div class="text-p-row">更多国内知名媒体对欧豹品牌报道见下方</div>
			<div class="logos">
				<ul>
					<li><a target="_blank" href="http://digi.163.com/14/1112/14/AARVH2NS001618JV.html"></a></li>
					<li><a target="_blank" href="http://tech.hexun.com/2014-11-12/170317217.html "></a></li>
					<li><a target="_blank" href="http://it.msn.com.cn/563449/306337724856b.shtml "></a></li>
					<li><a target="_blank" href="http://digital.ynet.com/465522/714460635756b.shtml"></a></li>
					<li><a target="_blank" href="http://www.pcpop.com/view/1/1052/1052817.shtml?r=12141416"></a></li>
					<li><a target="_blank" href="http://sh.beareyes.com.cn/2/lib/201411/12/20141112207.htm"></a></li>
					<li class="last"><a target="_blank" href="http://news.csdn.net/article.html?arcid=15820772&preview=1"></a></li>
					<li><a target="_blank" href="http://roll.sohu.com/20141105/n405791537.shtml"></a></li>
					<li><a target="_blank" href="http://game.huanqiu.com/news/2014-11/5191589.html"></a></li>
					<li><a target="_blank" href="http://news.duowan.com/1411/279115466876.html"></a></li>
					<li><a target="_blank" href="http://game.21cn.com/online/c/a/2014/1105/10/28502546.shtml"></a></li>
					<li><a target="_blank" href="http://game.china.com/mobile/hardware/11106781/20141105/18929995.html"></a></li>
					<li><a target="_blank" href="http://news.766.com/dl/2014-11-05/2396330.shtml"></a></li>
					<li class="last"><a target="_blank" href="http://www.40407.com/news/201411/468703.html"></a></li>
					<li><a target="_blank" href="http://www.diankeji.com/net/15556.html"></a></li>
					<li><a target="_blank" href="http://tech.xinmin.cn/internet/2014/11/05/25855465.html"></a></li>
					<li><a target="_blank" href="http://news.uuu9.com/2014/201411/352218.shtml"></a></li>
					<li><a target="_blank" href="http://xin.52pk.com/list/201411/6223258.shtml"></a></li>
					<li><a target="_blank" href="http://xin.52pk.com/list/201411/6223258.shtml"></a></li>
					<li><a target="_blank" href="http://tech.91.com/content/141112/21761196.html"></a></li>
					<li class="last"><a target="_blank" href="http://news.hiapk.com/contribute/20141104/1559736.html"></a></li>
				</ul>
			</div>
		</div>
	</div>


	<a name="concat"></a>
	<div class="container-fluid brand-map">
		<div class="container">
			<div class="concat">
				<div class="title">
					<p class="bg">在线咨询</p> 
					<p class="sm">服务时间：00:00~24:00</p>
				</div>
				<a class="button" href="javascript:hj5107.openChat();">联系我们</a>
			</div>
		</div>
	</div>




	@include('w.footer')
@stop


@section('end')
@parent
<script>
(function($){
	new bomao.Tab({par:'#J-nav-tab',triggers:'.nav-tabs li',panels:'.pl',currClass:'active',eventType:'click'});
	//
    var bannerSlider = new bomao.Slider({
        par:'#J-brand-slider',
        triggers:'.triggers > a',
        panels:'.pl-list-cont a',
        sliderDirection:'left',
        sliderIsCarousel:true,
        autoPlay:3000,
        sliderDuration:500
    });

})(jQuery);
</script>
@stop








