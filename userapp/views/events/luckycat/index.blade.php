<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>幸运博狼</title>

@section ('styles')

      {{ style('global')}}
      {{ style('eventLottery')}}
@show
@section('javascripts')
  {{ script('jquery-1.9.1') }}
  <script type="text/javascript" src="/events/xinyunmao/js/jquery.kxbdMarquee.js"></script>
  {{ script('bomao.base') }}
  {{ script('bomao.Mask') }}
  {{ script('bomao.Message') }}
@show

</head>

<body>
<input id="J-value-ajaxpath" type="hidden" value="{{route("luckycat.winprize")}}" />
<input id="J-value-ajaxpath-recharge" type="hidden" value=@if($isApply)"1"@else"0"@endif />
<input id="J-isPhysical" type="hidden" value="1" />
<input id="notifyMsg" type="hidden" value="{{json_encode($notify)}}">
<input id="J-token" type="hidden" value="{{$token}}">

<div class="top-header">
	<div class="warper clearfix" id="J-warper">
		<div class="left">
			<a target="_blank" href="{{ route('home') }}" class="logo"><img src="/events/xinyunmao/images/logo.png" width="180" height="50" /></a>
		</div>
		<div class="right">
			<div class="user">
				<span class="user-menu" id="J-top-user-menu">
					<a href="#" class="user-ico"></a>
					<i class="sj-ico"></i>
					<div class="user-info">

						<div>
							<div class="title">
								<div class="tip">欢迎您，<b>{{ Session::get('nickname') }}</b>，祝您好运连连~</div>
							</div>
							<div class="title-text">
								<span class="text1">账户余额：</span>
								<span class="text2">参加首充就送100%礼金，更划算哦～</span>
							</div>
							<div class="acount">
								<span class="num">{{ $avaiableBalance }}</span> <span class="unit">元</span>
								<a target="_blank" href="{{ route('user-recharges.netbank') }}" class="button-recharge">去充值</a>
							 </div>
							<div class="times">
								<div class="text">剩余抽奖次数：</div>
								<span class="times-num">
									<span class="num" id="J-top-lefttimes">{{$left_prize_count}}</span>
									<span class="text1">次</span>
								</span>
								<a target="_blank" href="{{ route('bets.bet', '1') }}" class="button-getmore">投注获得更多抽奖机会</a>
							</div>
						</div>



						<span class="sj-deep"></span>
					</div>
				</span>
				<a id="J-item-gift" href="javascript:;" class="user-gift"></a>
			</div>
		</div>
	</div>
</div>



<div class="part-1">
	<div class="part-1-2">
	<div class="part-1-3">
		<div class="warper">
			<div class="pan-light"></div>

			<div class="pan-sj"></div>
			<div class="p1-anim-text"></div>
			<div class="p1-anim-line"></div>
			<div class="p1-anim-c p1-anim-c-1"></div>
			<div class="p1-anim-c p1-anim-c-2"></div>
			<div class="p1-anim-c p1-anim-c-3"></div>
			<div class="p1-anim-c p1-anim-c-4"></div>
			<div class="p1-anim-c p1-anim-c-5"></div>

			<div class="list-record">
				<div class="inner" id="J-list-record">
					<ul>
					@foreach($rolling_prize_list as $p)
						<li>恭喜{{$p['username']}}中奖啦！ 奖品：{{$p['prize_name']}}</li>
				    @endforeach
					</ul>
				</div>
			</div>
			<div class="panel-times">
				<span class="times">
					<input id="J-hidden-times" type="hidden" value="{{$left_prize_count}}" />
					<span class="num" id="J-lefttimes">&nbsp;</span>
					<span class="text">次</span>
					<span class="anim-num-toup">+1</span>
					<span class="anim-num-todown">-1</span>
				</span>
			</div>


			<div class="items">
				<ul class="list" id="J-item-list">
					<li>

						<div class="tip">
							<div class="title">
								<div class="text">特斯拉MODELS60</div>
								<div class="info">价值：648000元</div>
								<div class="info">来自未来的车</div>
							</div>
							<div class="infotext">
								Tesla (特斯拉) Model S 可以实现瞬间加速，只需 5.6 秒 Model S 85kWh就可达到 100 公里时速 ，不拖泥带水，不烧一滴汽油。
							</div>
							<div class="sj"></div>
						</div>

					</li>
					<li>

						<div class="tip">
							<div class="title">
								<div class="text">10000元欧洲游</div>
								<div class="info">价值：10000元</div>
								<div class="info">奢华浪漫之旅</div>
							</div>
							<div class="infotext">
								奢华浪漫的西欧，既童真又深沉的中欧，沧桑雍容的东欧，热情烂漫的南欧，自然沉静的北欧……这里能满足你对旅行的所有想象，一个个响亮的名字高悬在每个人周游世界的愿望清单之上。
							</div>
							<div class="sj"></div>
						</div>

					</li>
					<li>
						<div class="tip">
							<div class="title">
								<div class="text">金条20g</div>
								<div class="info">价值：5500元</div>
								<div class="info">中国黄金 万足金Au9999薄片投资金条20g</div>
							</div>
							<div class="infotext">
								中金黄金和世界黄金协会信誉推荐，国际标准金锭中国版，尊贵大方值得收藏，可随时在中国黄金旗舰店或指定金店兑换现金。
							</div>
							<div class="sj"></div>
						</div>
					</li>
					<li>
						<div class="tip">
							<div class="title">
								<div class="text">iphone6 64G iphone6 plus 64G</div>
								<div class="info">价值：6088、6888元</div>
								<div class="info">比更大还要大</div>
							</div>
							<div class="infotext">
								64位苹果 A8+M8协处理器，像素密度326ppi，LTE网络，后置摄像头800万，前置摄像头120万，OIS光学防抖功能。
							</div>
							<div class="sj"></div>
						</div>
					</li>
					<li>
						<div class="tip">
							<div class="title">
								<div class="text">Galaxy Note4</div>
								<div class="info">价值：5399元</div>
								<div class="info">最强劲的移动处理器</div>
							</div>
							<div class="infotext">
								Android OS 4.4，高通 骁龙805，前置摄像头370万像素，后置摄像头1600万像素。
							</div>
							<div class="sj"></div>
						</div>
					</li>
					<li>
						<div class="tip">
							<div class="title">
								<div class="text">现金奖</div>
								<div class="info">2元、5元、10元、100元、1000元</div>
							</div>
							<div class="infotext">
								获得现金券后，系统将自动派发奖金至平台账户，请注意查看“我的奖品”。
							</div>
							<div class="sj"></div>
						</div>
					</li>
					<li class="last">
						<div class="tip">
							<div class="title">
								<div class="text">返利券</div>
								<div class="info">1%返利券 2%返利券</div>
							</div>
							<div class="infotext">
								返利券规则：获得返利券后，即日起00:00开始计算随后48小时的投注额度的1%，即为返利金额，返利金将在随后的5个工作日内发放至平台账户。
							</div>
							<div class="sj"></div>
						</div>
					</li>
				</ul>
			</div>

		</div>
	</div>
	</div>
</div>

<div id="J-fl-warper" class="fl-warper">

			  <object id="lotteryFlash" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="690" height="690">
                  <param name="movie" value="/events/xinyunmao/images/swf/lottery.swf?v=201412191" />
				  <param name="FlashVars" value="R=345&ang=30&imgSrc=/events/xinyunmao/images/swf/images/pan.png?v=9&isShowGray=1&grayR=105&grayAlpha=0.1&colorArrStr=0xEEEEEE|0x27AEAD&hightLightColor=0xFF6600" />
				  <param name="wmode" value="transparent" />
                  <param name="quality" value="high" />
				  <param name="allowScriptAccess" value="sameDomain" />
				  <embed FlashVars="R=345&ang=30&imgSrc=/events/xinyunmao/images/swf/images/pan.png?v=9&isShowGray=1&grayR=105&grayAlpha=0.1&colorArrStr=0xEEEEEE|0x27AEAD&hightLightColor=0xFF6600" wmode="transparent" name="lotteryFlash" src="/events/xinyunmao/images/swf/lottery.swf?v=201412191" quality="high" allowScriptAccess="sameDomain" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="690" height="690"></embed>
			  </object>
	<div class="fl-button-cont">
		<a id="J-flButton" href="#" class="fl-button fl-button-disable" onfocus="this.blur();"></a>
	</div>
</div>





<div class="module part-2">
	<script type="text/template">
		<style>
			.part-2 {background:url(/events/xinyunmao/images/2.png) center 0 no-repeat;}
		</style>
		<div class="warper">
			<div class="@if(isset($finish_conditions[3]))step step3 @elseif(isset($finish_conditions[1]))step step1 @endif"></div>
			<div class="number"></div>
			<div class="text1"></div>
			<div class="text2"></div>
			@if(isset($finish_tasks[1]))
				<div class="finish"></div>
				<a href="javascript:;" class="button button-disable"></a>
			@else
				<a target="_blank"  href="{{ route('bank-cards.index') }}" class="button "></a>
			@endif

		</div>
	</script>
</div>




<div class="module part-3">
	<script type="text/template">
		<style>
			.part-3 {background:url(/events/xinyunmao/images/3.jpg) center 0 no-repeat;}
		</style>
		<div class="warper">
			<div class="text"></div>
			<div class="mm"></div>
			<div class="ask"></div>
			<div class="info"></div>
			<a target="_blank" href="{{ route('bets.bet', '1') }}" class="button"></a>
		</div>
	</script>
</div>


<div class="module part-5" id="J-part-5">
	<script type="text/template">
		<style>
			.part-5 {background:url(/events/xinyunmao/images/5.jpg) center 0 no-repeat;}
		</style>
		<div class="half left" id="J-p5-left"></div>
		<div class="half right" id="J-p5-right"></div>

		<div class="warper" id="J-p5-warper">
			<div class="titleimg"></div>

			<div class="text1 gray1"></div>
			<div class="text1 wt1"></div>

			<div class="text2 gray2"></div>
			<div class="text2 wt2"></div>

			<div class="mm1"></div>
			<div class="mm2"></div>


			<div class="float text-long-1">
				<ul>
					<li>
						选择“一次返”就不能参加“多次返”了哦；
					</li>
					<li>
						首次充值达1000元及以上，即一次性赠送100%礼金。只要满足活动期间完成“充值金额+礼金金额”的32倍流水，即可获得100%礼金，最高上限19万元；
						<br />
						举例：用户参与“一次返“活动，充值1000元，流水金额（1000+1000）*32=64000元，即可获得1000元礼金，需在活动周期内（即2015年1月16日）完成，方能获得；<br />
					</li>
					<li>
						礼金金额是以用户在报名后第一次向平台充值的金额计算，第二次及以后的充值将不算计算入礼金金额内；
					</li>
					<li>
						该优惠每位玩家，相同支付方式(相同借记卡/银行账户姓名及号码)、一次活动只能享有一次优惠；
					</li>
					<li>
						点击参加“一次返”活动后，用户需要在最长1小时内充值到账，1小时后此活动入口失效；
					</li>
					<li>
						若玩家同一时间参加两种，或统一活动进行了多次充值，则按照最先到账的金额为主。
					</li>
				</ul>

			</div>



			<div class="float text-long-2">
				<ul>
					<li>
						参加“多次返”就不能参加“一次返”了哦；
					</li>
					<li>
						首次充值达100元及以上，即送分期100%礼金。只要满足每周内“充值金额+礼金金额”的8倍流水，即获得本周25%的赠送礼金，所有礼金4周送完；<br />
						举例：用户参加“多次返“活动，充值500元，每周流水倍数为（500+500）*8=8000元，即可获得本周125元礼金，后续3周延续；
					</li>
					<li>
						若首次充值金额在本次活动的最后一周，则剩余3周仍然可以完成每周的流水要求；
					</li>
					<li>
						“多次返”奖金统计将在以下固定时间内完成，具体时间：
						<table border="1" cellspacing="0" cellpadding="0" width="450" style="margin:5px 0;">
						  <tr>
						    <td width="89"><p align="center"><strong>活动期数 </strong></p></td>
						    <td width="302"><p align="center"><strong>多次返 “充值投注”周期 </strong></p></td>
						    <td width="152"><p align="center"><strong>奖金统计日期 </strong></p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">活动第1周</p></td>
						    <td width="302"><p align="center">2014/12/22－2014/12/28 </p></td>
						    <td width="152"><p align="center">2014/12/29 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">活动第2周</p></td>
						    <td width="302"><p align="center">2014/12/29－2015/1/4 </p></td>
						    <td width="152"><p align="center">2015/1/5 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">活动第3周</p></td>
						    <td width="302"><p align="center">2015/1/5－2015/1/11 </p></td>
						    <td width="152"><p align="center">2015/1/12 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">活动第4周</p></td>
						    <td width="302"><p align="center">2015/1/12－2015/1/18 </p></td>
						    <td width="152"><p align="center">2015/1/19 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">结束第1周</p></td>
						    <td width="302"><p align="center">2015/1/19－2015/1/25 </p></td>
						    <td width="152"><p align="center">2015/1/26 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">结束第2周</p></td>
						    <td width="302"><p align="center">2015/1/26－2015/2/1 </p></td>
						    <td width="152"><p align="center">2015/2/2 </p></td>
						  </tr>
						  <tr>
						    <td width="89"><p align="center">结束第3周</p></td>
						    <td width="302"><p align="center">2015/2/2－2015/2/8</p></td>
						    <td width="152"><p align="center">2015/2/9</p></td>
						  </tr>
						</table>
						举例：a用户12月28日上午9:00参加“多次返”活动，其奖金统计时间为12月29日。若完成充值金额的周8倍流水，即可通过审核，未完成则本周权利失效，但第二周的资格仍存在，只要完成第二周8倍流水要求，仍然可以获得第二周礼金；
					</li>
					<li>
						礼金金额是以用户在报名后第一次向平台充值的金额计算，第二次及以后的充值将不算计算入礼金金额内；
					</li>
					<li>
						该优惠每位玩家，相同支付方式(相同借记卡/银行账户姓名及号码)、一次活动只能享有一次优惠；
					</li>
					<li>
						点击参加“多次返”活动后，用户需要在最长1小时内充值到账，1小时后此活动入口失效；
					</li>

				</ul>
			</div>




			<div class="finish"></div>

			<a target="_blank" href="{{ route('user-recharges.netbank') }}" id="J-button-p5-100" data-value="100" class="button button1"></a>
			<a target="_blank" href="{{ route('user-recharges.netbank') }}" id="J-button-p5-1000" data-value="1000" class="button button2"></a>
		</script>
	</div>
</div>
<script>
function initPart5(){
	var left = $('#J-p5-left'),right = $('#J-p5-right'),warper = $('#J-p5-warper');
	left.hover(function(){
		warper.removeClass('warper-right').addClass('warper-left');
		left.css({width:'65%', zIndex:30});
	},function(e){
		var target = $(e.relatedTarget);
		if(target.hasClass('button1')){
			return false;
		}
		warper.removeClass('warper-left');
		left.css({width:'50%', zIndex:9});
	});
	right.hover(function(){
		warper.removeClass('warper-left').addClass('warper-right');
		right.css({width:'65%', zIndex:30});
	},function(e){
		var target = $(e.relatedTarget);
		if(target.hasClass('button2')){
			return false;
		}
		warper.removeClass('warper-right');
		right.css({width:'50%', zIndex:9});
	});

		var dom = $(this),cont = $('#J-part-5');
		var un =  $('#J-value-ajaxpath-recharge').val();

		//弹窗内容
		var windowFun = function(d , f){
			var popWindow = new bomao.Message();
	            mask = new bomao.Mask();
	        var html = '';
	            if(d.status == '0'){
	            	html = '您已经参加'
	            }else{
	            	html = (f == '100')? '您已选择“首充就送100%”【多次返】优惠，只要完成“本金+礼金”的周8倍流水，即可分四周获得100%的充值礼金，奖金将于5个工作日内发出。'
	            	:'您已选择“首充就送100%”【一次返】优惠，只要完成“本金+礼金”的32倍流水，即可一次性获得100%的充值礼金，奖金将于5个工作日内发出。'
	            };
	        var data = {
	            title          : '提示',
	            content        : html,
	            isShowMask     : true,
	            closeIsShow    : true,
	            closeButtonText: '关闭',
	            closeFun       : function() {
	                this.hide();
	            }
	        };
	        popWindow.show(data);
		};

		if( un === "1" || un == true){
			cont.addClass('part-5-finish');
		}else{
			$('#J-button-p5-100,#J-button-p5-1000').click(function(e){

	         	var b10 = $(this).attr('data-value');
		        $.ajax({
						url:(b10 == '100') ? "{{route('luckycat.sicifan')}}":"{{route('luckycat.yicifan')}}",
					})
		        .done(function(data) {
		        	var d = JSON.parse(data);
		        	if(d['status'] == '0'){
		        		cont.addClass('part-5-finish');
		        		windowFun(d );
		        	}else{
		        		cont.addClass('part-5-finish');
		        		windowFun(d , b10);
		        	}
				})
				.fail(function(data) {
					windowFun(JSON.parse(data));
				});
	        });
		}

}
</script>




<div class="module part-4" id="J-part-4">
	<script type="text/template">
		<style>
			.part-4 {background:url(/events/xinyunmao/images/4.jpg) center 0 no-repeat;}
		</style>
		<div class="warper">
			<div class="light"></div>
			<div class="text1"></div>
			<div class="text2"></div>
			<div class="text3"></div>
			<div class="text4"></div>
			<a target="_blank" href="{{ route('user-withdrawals.withdraw', 0) }}" class="button"></a>
			@if(isset($finish_tasks[4]))
			<div class="finish"></div>
			@else
			@endif
		</div>
	</script>
</div>



<div class="module part-6">
	<script type="text/template">
		<style>
			.part-6 {background:url(/events/xinyunmao/images/6.jpg) center 0 no-repeat;}
		</style>
		<div class="warper">
			<div class="light"></div>
			<div class="text1"></div>
			<div class="text2"></div>

		</div>
	</script>
</div>


<div class="module part-7">
	<script type="text/template">
		<style>
			.part-7 {background:url(/events/xinyunmao/images/7.png) center 0 no-repeat;}
		</style>
		<div class="warper">

		</div>
	</script>
</div>




<div style="background: #fff;">
		@include('w.big-footer')
</div>



<div class="sider-menu" id="J-sider-menu">
	<div class="inner">
		<a href="javascript:hj5107.openChat();" class="service"></a>
		<ul class="first" id="J-side-menuA">
			<li><a href="#">抽奖转盘</a></li>
		</ul>
		<ul class="other" id="J-side-menuB">

			<li @if(isset($finish_tasks[1])) class="finish" @endif><span>1</span>完成新手任务就抽奖</li>
			<li @if(isset($finish_tasks[6])) class="finish" @endif><span>2</span>每满99抽奖1次</li>
			<li @if(isset($finish_tasks[2]) || isset($finish_tasks[3])) class="finish" @endif><span>3</span>首充就送100%</li>
			<li @if(isset($finish_tasks[4])) class="finish" @endif><span>4</span>你提款我送钱</li>
			<li><span>5</span>转运金</li>

		</ul>
		<span class="num">{{$all_finish_task}}</span>
		<!-- <a href="#" class="download"></a> -->
		<a id="J-gotop" href="#" class="gotop"></a>
	</div>
</div>


<div class="pop-itemlist" id="J-itemwd">
	<div class="title">
		<a href="#" class="close"></a>
	</div>
	<div class="content">
		<div class="tabTitle">
			<ul class="clearfix">
				<li class="current">全部奖品</li>
				<li>实物类奖品</li>
				<li>首充就送100%</li>
			</ul>
			<div id="J-item-contcat" class="item-contcat">实物奖品的收货信息及送奖进度，请<a class="button-contcat" href="javascript:hj5107.openChat();">联系客服</a></div>
		</div>
		<div class="tabContent">
			<iframe id="J-item-iframe" src="about:blank" frameborder="0" scrolling="auto" width="770" height="493"></iframe>
		</div>
	</div>
</div>


<div class="wd-result wd-result-3" id="J-wd-result-3">
	<div class="wd-result-img wd-result-img-3-6"></div>
	<div class="wd-result-text">
		恭喜您战胜了<span class="wd-result-prob"></span>的玩家，抽中了<span class="item-reuslt-title"></span>！
		<p>快点联系在线客服，提供收奖信息吧！</p>
		<p>这么好的运气，再试试还能抽到什么吧！</p>
	</div>
	<div class="wd-result-button">
		<a href="javascript:hj5107.openChat();" class="button"></a>
	</div>
	<a href="#" class="close"></a>
</div>



<div class="wd-result wd-result-2" id="J-wd-result-2">
	<div class="wd-result-img wd-result-img-1-1"></div>
	<div class="wd-result-text">
		恭喜您战胜了<span class="wd-result-prob"></span>的玩家，抽中了<span class="item-reuslt-title"></span>！
		<p>运气不错嘛，再试试还能抽到什么吧！</p>
	</div>
	<div class="wd-result-button">
		<a href="#" class="button button-close button-comfirm"></a>
	</div>
	<a href="#" class="close"></a>
</div>



<div class="wd-result wd-result-0" id="J-wd-result-0">
	<div class="wd-result-img wd-result-img-0"></div>
	<div class="wd-result-text">
		哎呀，幸运之神离你还差0.001mm的距离！
		<p>不要气馁，继续试试手气吧！说不定就中了呢！</p>
	</div>
	<div class="wd-result-button">
		<a href="#" class="button button-close"></a>
	</div>
	<a href="#" class="close"></a>
</div>



<div class="wd-result wd-msg-expired" id="J-msg-expired">
	<div class="wd-result-img"></div>
	<div class="wd-result-text">

	</div>
	<div class="wd-result-button">
		<a href="#" class="button button-close"></a>
	</div>
	<a href="#" class="close"></a>
</div>



<div class="wd-result wd-msg-notimes" id="J-msg-notimes">
	<div class="wd-result-img"></div>
	<div class="wd-result-text">

	</div>
	<div class="wd-result-button">
		<a target="_blank" href="{{ route('bets.bet', '1') }}" class="button "></a>
	</div>
	<a href="#" class="close"></a>
</div>

<script>
//顶部 ====================================================================
(function(){
	var timer;
	$('#J-top-user-menu').hover(function(){
		clearTimeout(timer);
		$(this).find('.user-info').fadeIn(300);
	},function(){
		var me = $(this);
		timer = setTimeout(function(){
			me.find('.user-info').fadeOut(300);
		}, 200);

	});
})();


//抽奖部分 =================================================================
(function($){
	var button = $('#J-flButton'),disCls = 'fl-button-disable',msgWd,mask = bomao.Mask.getInstance(),valToken=$('#J-token');
	button.click(function(e){
		e.preventDefault();
		if(button.hasClass(disCls)){
			return false;
		}
		$.ajax({
			url:$('#J-value-ajaxpath').val()+'?token='+valToken.val(),
			dataType:'json',
			beforeSend:function(){
				button.addClass(disCls);
			},
			success:function(data){
				if(Number(data['isSuccess']) == 1){
					var timesAnim = $('#J-lefttimes').parent().find('.anim-num-todown'),num = Number(data['data']['num']),type = Number(data['data']['type']),title = data['data']['title'],value = data['data']['value'],userwin = data['data']['userwin'];
					BomaoLottery.flash.sendMsgToFlash('run', num + '|' + type + '|' + title + '|' + value + '|' + userwin);
					$('#J-lefttimes').text(data['data']['times']);
					$('#J-top-lefttimes').text(data['data']['times']);
					timesAnim.addClass('anim-num-todown-show');
					setTimeout(function(){
						timesAnim.removeClass('anim-num-todown-show');
					}, 1000);
					if(type == 3){
						$('#J-item-contcat').show();
					}
					//更新 token
					valToken.val(data['token']);
				}else{

					switch($.trim(data['type'])){
						case 'expired':
							msgWd = $('#J-msg-expired');
							msgWd.css('margin-top', msgWd.height()/2*-1);
							mask.show();
							msgWd.show();
						break;
						case 'notimes':
							msgWd = $('#J-msg-notimes');
							msgWd.css('margin-top', msgWd.height()/2*-1);
							mask.show();
							msgWd.show();
						break;
						default:
								alert(data['msg']);
								location.href = location.href;
						break;
					}

				}
			},
			error:function(){
				alert('网络请求失败，请刷新页面重试');
				location.href = location.href;
			}
		});
		$('#J-msg-expired .close, #J-msg-notimes .close').click(function(e){
			e.preventDefault();
			if(msgWd){
				mask.hide();
				msgWd.hide();
			}
		});
	});

	//任务弹窗，完成新手任务
	var newFieish = {{json_encode($notify)}};
	var win = function(newFieish){
			var popWindow = new bomao.Message();
	            //mask = new bomao.Mask();
	        var html = newFieish;
	        var data = {
	            title          : '提示',
	            content        : html,
	            isShowMask     : false,
	            closeIsShow    : true,
	            closeButtonText: '知道了',
	            closeFun       : function() {
	                this.hide();
	            }
	        };
	        popWindow.show(data);

	};
	for(var i=0; i<newFieish.length;i++){
		win(newFieish[i]);
	};
})(jQuery);

var onFlashMsg;
(function($, window, document){
	var button = $('#J-flButton'),disCls = 'fl-button-disable',wd = new bomao.MiniWindow({cls:'pop'}),mask = bomao.Mask.getInstance(),isFlashLoaded = false;
	wd.addEvent('beforeShow', function(){
		mask.show();
	});
	wd.addEvent('afterHide', function(){
		mask.hide();
		BomaoLottery.flash.sendMsgToFlash('startFree');
	});
	var startReady = function(){
		var timerTime = document.all ? 1000 : 4000;
		setTimeout(function(){
			if(!isFlashLoaded){
				try{
					BomaoLottery.flash.sendMsgToFlash('startFree');
					isFlashLoaded = true;
				}catch(e){
				}
				var timesDom = $('#J-lefttimes'),times = Number($('#J-hidden-times').val()),timer,i = 0;
				$('#J-list-record').kxbdMarquee({direction:'up', scrollDelay:50});
				timesDom.text(times);
				button.removeClass('fl-button-disable');
				/**
				if(times > 0){
					timesDom.parent().find('.anim-num-toup').show();
					timer = setInterval(function(){
						if(i >= times){
							clearInterval(timer);
							timesDom.parent().find('.anim-num-toup').hide();
							button.removeClass('fl-button-disable');
							return;
						}
						timesDom.text(i++);
					}, 50);
				}
				 **/
			}
		}, timerTime);
	};
	var BomaoLottery = {
		flash:{
			flashFnName:'onJsFn',
			getFlash:function(name){
				var me = this;
				return me.flash || (me.flash = navigator.appName.indexOf("Microsoft") != -1 ? window[name] : document[name]);
			},
			sendMsgToFlash:function(msg, param){
				var me = this;
				me.getFlash("lotteryFlash")[me.flashFnName](msg, param);
			},
			onFlashMsg:function(fnName, param){
				//alert("命令：" + fnName + '\n' + '参数：' + param);
				switch(fnName){
					case 'loadComplete':
						button.css('display', 'inline-block');
						if(Number($('#J-lefttimes').text()) < 1){
							button.addClass('fl-button-disable');
						}
						startReady();
						break;
					case 'finish':
						setTimeout(function(){
							var arr = param.split('|'),
								type = Number(arr[0]),
								num = Number(arr[1]),
								title = arr[2],
								value = arr[3],
								userwin = arr[4];
							showLotteryResult(type, num, title, value, userwin);
						}, 1000);
						break;
					default:
						break;
				}
			}
		}
	};
	onFlashMsg = BomaoLottery.flash.onFlashMsg;
	window.BomaoLottery = BomaoLottery;


	//中奖消息提示
	//num 奖品编号
	//type 类型
	var currentWd;
	var showLotteryResult = function(type, num, title, value, userwin){
		var type2 = type == 1 ? 2 : type,wd = $('#J-wd-result-' + type2),cls = 'wd-result-img  wd-result-img-',marginTop = 0;
		currentWd = wd;
		if(type == 1 || type == 2){
			cls += type + '-' + value;
		}else{
			cls += type + '-' + num;
		}
		wd.find('.wd-result-prob').text('' + userwin );
		wd.find('.wd-result-img').removeClass().addClass(cls);
		marginTop = wd.height()/2*-1;
		if(type == 3){
			marginTop -= 120;
		}

		wd.css('margin-top', marginTop);
		wd.find('.item-reuslt-title').text(title);
		mask.show();
		wd.show();
	};
	$('.wd-result').find('.close , .button-close').click(function(e){
		e.preventDefault();
		var times = 0;
		if(currentWd){
			times = Number($('#J-lefttimes').text());
			currentWd.hide();
			mask.hide();
			if(times > 0){
				button.removeClass(disCls);
			}
		}
	});



	startReady();

})(jQuery, window, document);



//第一屏 =======================================================================
(function(){
	var lis = $('#J-item-list > li'),itemwd = $('#J-itemwd'),itemTriggers = itemwd.find('.tabTitle li'),iframe = $('#J-item-iframe'),
		srcs = ['{{ route("luckycat.myprizes") .("?type=0")}}', '{{ route("luckycat.myprizes") .("?type=3")}}', '{{ route("luckycat.myDepositPrizes")}}'],
		CLS = 'current',
		button = $('#J-item-gift'),
		mask = bomao.Mask.getInstance();
	var hide = function(){
		mask.hide();
		itemwd.hide();
	};
	var show = function(){
		var index = itemTriggers.index(itemwd.find('.tabTitle li.current').get(0));
		iframe.attr('src', srcs[index]);
		itemwd.css({top:$(window).height()/2 - itemwd.height()/2});
		mask.show();
		itemwd.show();
	};
	lis.hover(function(){
		$(this).find('.tip').show();
	},function(){
		$(this).find('.tip').hide();
	});
	button.click(function(e){
		e.preventDefault();
		show();
	});
	itemwd.find('.close').click(function(e){
		e.preventDefault();
		hide();
	});
	itemTriggers.click(function(){
		itemTriggers.removeClass(CLS);
		$(this).addClass(CLS);
		iframe.attr('src', srcs[itemTriggers.index(this)]);
	});

	if(Number($('#J-isPhysical').val() == 1)){
		$('#J-item-contcat').show();
	}


})();



//侧栏
(function(){
	var win = $(window),lisA = $('#J-side-menuA > li'),ulB = $('#J-side-menuB'),lisB = $('#J-side-menuB > li'),CLS = 'current',baseH = 4,bgH = 37,
		scrollList = [[0, 2299], [2299, 2945], [2945,3591], [3591,4237], [4237,4883], [4883,10000]];
		scrollSide = function(i){
			if(i == -1){
				lisA.addClass(CLS);
				lisB.removeClass(CLS);
				ulB.stop().css('backgroundPosition',  '0 -100px');
			}else{
				lisA.removeClass(CLS);
				lisB.removeClass(CLS);
				lisB.eq(i).addClass(CLS);
				ulB.stop().css('backgroundPosition', '0 ' + (baseH + bgH * i - 1) + 'px');
			}
		};
	var scrollCheckFn = function(){
		var top = win.scrollTop(),i = 0,len = scrollList.length,it;
		for(;i < len;i++){
			it = scrollList[i];
			if(top >= it[0] - 56 && top <= it[1] - 56){
				scrollSide(i - 1);
				break;
			}
		}
	};
	win.scroll(function(){
		scrollCheckFn();
	});
	lisB.click(function(e){
		var i = lisB.index(this);
		e.preventDefault();
		if(document.all){
			win.scrollTop(scrollList[i + 1][0] + 1 - 56);
		}else{
			$('html,body').animate({'scrollTop':scrollList[i + 1][0] + 1 - 56}, 300);
		}
	});
	lisA.click(function(e){
		e.preventDefault();
		if(document.all){
			win.scrollTop(0);
		}else{
			$('html,body').animate({'scrollTop':0}, 300);
		}
	});
	$('#J-gotop').click(function(e){
		e.preventDefault();
		scrollSide(-1);
		win.scrollTop(0);
	});
	setTimeout(function(){
		scrollCheckFn();
	}, 1000);

	//侧栏首次定位
	var side = $('#J-sider-menu'),warper = $('#J-warper'),sideLeft = warper.offset().left + warper.width() + 40,fullWidth = warper.offset().left + warper.width() + side.width() + 20;
	if(fullWidth >= $(window).width()){
		sideLeft -= (fullWidth - $(window).width() + 10);
	}
	side.css('left', sideLeft);

})();


</script>


<script>
//模块分屏加载
(function($){
	var modules = $('.module'),datas = [],win = $(window),checkLoadFn,view = [],bounds = [[2299, 2945], [2945, 3591], [3591, 4237], [4237, 4883], [4883, 5529], [5529, 6278]];
	var updateView = function(){
		view[0] = win.scrollTop();
		view[1] = view[0] + win.height();
	};
	var timer;
	win.load(function(){
		win.scroll(function(){
			var dom;
			updateView();
			clearTimeout(timer);
			timer = setTimeout(function(){
				checkLoadFn();
			}, 100);
		});
	});
	modules.each(function(i){
		datas.push({dom:modules.eq(i), loaded:false, callback: (i == 3) ? initPart5 : function(){} });
	});
	//console.log(datas);
	checkLoadFn = function(){
		var bound;
		$.each(datas, function(i){
			bound = bounds[i];
			if(!this['loaded'] && view[1] >= bound[0] && view[0] <= bound[1]){
				dom = this['dom'];
				dom.html(dom.find('script').html());
				this['loaded'] = true;
				this['callback']();
			}

		});
	};


})(jQuery);



  @if(Session::get('user_id'))
	//客服代码：
	//姓名|性别|固定电话|手机|邮箱|地址|公司名|MSN|QQ|会员ID|会员等级 |（此处按照上面约定字段直接传送；如未登陆，传空）会员等级（1:VIP会员 0:普通会员）
	var hjUserData="{{urlencode(Session::get('username'))}}|||||{{get_client_ip()}}||||{{Session::get('id')}}|0|";
	@endif
</script>




















</body>
</html>
