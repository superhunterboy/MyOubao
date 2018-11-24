<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>预约成为博猫总代</title>

@section ('styles')
	  {{ style('global')}}
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
<!--baidu&google-->
        <script>
            var _hmt = _hmt || [];
            (function() {
              var hm = document.createElement("script");
              hm.src = "//hm.baidu.com/hm.js?f9023a76b39fb6f4ce37024fcef1542a";
              var s = document.getElementsByTagName("script")[0];
              s.parentNode.insertBefore(hm, s);
            })();
        </script>

        <script>
              (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
              (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
              m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
              })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

              ga('create', 'UA-57340364-1', 'auto');
              ga('require', 'displayfeatures');
              ga('send', 'pageview');

        </script>
</head>

<body>


<div class="top">
	<div class="warper" id="J-warper">
		<div class="left">
			<a href="/" class="logo"></a>
		</div>
		<div class="right">
			<a href="/" class="toindex"></a>
		</div>


		<div class="float text1"></div>
		<div class="float text2"></div>
		<div class="float text3"></div>
		<div class="float text4"></div>

		<div class="float it-1"></div>
		<div class="float it-2"></div>
		<div class="float it-3"></div>
		<div class="float it-4"></div>
		<div class="float it-5"></div>
		<div class="float it-6"></div>
		<div class="float it-7"></div>


		<a href="#" class="float button button-showForm"></a>

		<div class="float video-inner">
			<div class="float video" id="J-video-cont">
				<!-- <div  id="J-video-first"></div> -->
				<embed src="http://player.56.com/v_MTMzMjQwNTY3.swf" type="application/x-shockwave-flash" width="800" height="450" allowfullscreen="true" allownetworking="all" allowscriptaccess="always"></embed>
			</div>
		</div>



	</div>
</div>





<div class="part-2">
	<div class="warper">

		<div class="float text1"></div>
		<div class="float text2"></div>
		<div class="float text3"></div>
		<div class="float text4"></div>
		<div class="float text5"></div>
		<div class="float gg"></div>
		<div class="float text6"></div>

	</div>
</div>






<div class="part-3">
	<div class="warper">

		<div class="float text1"></div>
		<div class="float gg"></div>
		<a href="#" class="float button button-showForm" ></a>
		<div class="float chart"></div>
		<div class="float table"></div>
		<div class="float text2"></div>

		<a href="#" class="float button button-2 button-showForm"></a>

		<div class="float pillar pillar-bomao">
			<div class="float inner" style="height:35%" id="J-mask-bomao">
				<div class="title"></div>
				<div class="flag"></div>
				<div class="money">
					<span id="J-money-bomao">0.00</span> 元<br />
					收入金额
				</div>
				<div class="float arrow arrup">扶持增收</div>
			</div>
		</div>
		<div class="float pillar pillar-other">
			<div class="float inner" style="height:35%" id="J-mask-other">
				<div class="title"></div>
				<div class="money">
					<span id="J-money-other">0.00</span> 元<br />
					收入金额
				</div>
				<div class="float info-text">
					代理其他平台比在博猫
					<div class="small"><span id="J-less-num" class="less-num">少赚 0.00</span>元</div>
				</div>
				<div class="float arrow arrdown" style="display:none;"></div>
			</div>
		</div>

		<div class="float text-tip text-bomao" id="J-text-bomao">在博猫<span class="gametype">时时彩</span><span class="num">1960</span>奖金组收入</div>
		<div class="float text-tip text-other" id="J-text-other">在其他平台<span class="gametype">时时彩</span><span class="num">1950</span>奖金组收入</div>


		<div class="float panel">
			<div class="row">
				<label>选择彩种</label>
				<select id="J-select-gametype" style="display:none;">
					<option value="ssc" selected="selected">时时彩</option>
					<option value="l115">11选5</option>
				</select>
			</div>
			<div class="row">
				<label>您的奖金组</label>
				<select id="J-select-group" style="display:none;">
					<option selected="selected">1950</option>
					<option>1940</option>
					<option>1930</option>
					<option>1920</option>
					<option>1800</option>
				</select>
			</div>
			<div class="row">
				<label>每月销售总额</label>
				<input type="text" value="100" id="J-num-sale" />
				万
			</div>
			<div class="row">
				<label>分红比例</label>
				<input type="text" value="1" id="J-num-profit" />
				%
			</div>
			<div class="row">
				<label>最小点差</label>
				<input type="text" value="0.25" id="J-num-point" />
				%
			</div>
			<div class="row">
				<label>每月输额</label>
				<input type="text" value="100" id="J-num-lost" />
				万
			</div>
			<a href="#" class="panel-button" id="J-button-calc"></a>
		</div>

	</div>
</div>





<div class="part-4">
	<div class="warper" id="J-tab-p4">
		<div class="float text1"></div>

		<div class="float tab-t">
			<ul>
				<li class="current first">优厚政策有保障</li>
				<li>总代迎新翻倍赚</li>
				<li>超级大奖天天抽</li>
				<li>千万现金日日送</li>
				<li>品牌海量大曝光</li>
				<li class="last">安全防护立体化</li>
			</ul>
		</div>

		<div class="float infoText">

		</div>

		<div class="float tab-c">
			<div class="tab-c-inner">
				<ul>
					<li><img src="/events/reserve_agent/images/4/1.jpg" width="930" height="414" /></li>
					<li><img src="/events/reserve_agent/images/4/2.jpg" width="930" height="414" /></li>
					<li><img src="/events/reserve_agent/images/4/3.jpg" width="930" height="414" /></li>
					<li><img src="/events/reserve_agent/images/4/4.jpg" width="930" height="414" /></li>
					<li><img src="/events/reserve_agent/images/4/5.jpg" width="930" height="414" /></li>
					<li><img src="/events/reserve_agent/images/4/6.jpg" width="930" height="414" /></li>
				</ul>
			</div>
			<div class="float ctrol toleft"></div>
			<div class="float ctrol toright"></div>
		</div>

		<div class="float tiptext">
			温馨提示：成为总代后，即可劲享业界最强的精彩促销活动！
		</div>

		<div class="float text2"></div>

		<div class="float video2">
			<!-- <div id="J-video-p4"></div> -->
				<embed src="http://player.56.com/v_MTMzMjQxNTY3.swf" type="application/x-shockwave-flash" width="800" height="450" allowfullscreen="true" allownetworking="all" allowscriptaccess="always"></embed>

		</div>

		<div class="float text3"></div>

		<a href="#" class="float button button-showForm"></a>

	</div>
</div>







<div class="part-5">
	<div class="warper">
		<div class="float land"></div>
		<div class="float text1"></div>
		<div class="float text2"></div>

	</div>
</div>






<div style="background: #fff;">
@include('w.big-footer')
</div>

<div class="side" id="J-side">
	<a href="javascript:hj5107.openChat();" class="button-1"></a>
	<a href="#" class="button-2" id="J-button-gotop"></a>
</div>



<div class="form" id="J-panel-form" style="display:none;" >
	<a href="#" class="close"></a>
	<div class="inner">
	<iframe width="100%;" height="420px;" style=" border: 0;" src="{{ route('reserve-agent.form')  }}"></iframe>
	</div>

</div>


<script>

//part-1
(function($){
	var mask = bomao.Mask.getInstance();
	//视频播放

	// var player = new SWFObject("/events/reserve_agent/images/vcastr2/vcastr2.swf","ply","800","450","9","#000000");
	// player.addParam("allowfullscreen","true");
	// player.addParam("allowscriptaccess","always");
	// player.addParam("wmode","opaque");
	// player.addParam("quality","high");
	// player.addParam("salign","lt");
	// player.addVariable("vcastr_file","http://192.168.62.104/images/video/zhaoshang.flv");
	// player.write("J-video-first");

	//视频触发
	var videoCont = $('#J-video-cont');
	videoCont.hover(function(){
		videoCont.css('z-index', 100);
		videoCont.animate({'top':243}, 500);
	},function(){
		videoCont.css('z-index', 100);
		videoCont.animate({'top':603}, 500);
	});

	//右侧漂浮
	var side = $('#J-side'),warper = $('#J-warper'),sideLeft = warper.offset().left + warper.width() + 40,fullWidth = warper.offset().left + warper.width() + side.width() + 20;
	if(fullWidth >= $(window).width()){
		sideLeft -= (fullWidth - $(window).width() + 10);
	}
	side.css('left', sideLeft);
	$('#J-button-gotop').click(function(e){
		e.preventDefault();
		$(window).scrollTop(0);
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

	$('body').on('click', '.button-showForm', function(e){
		if(e && e.preventDefault){
			//e.preventDefault();
		}
		showForm();
	});


})(jQuery);




//part-3
//计算器
(function($){
	var data = [{'name':'ssc', 'cnname':'时时彩', 'list':[1950, 1940, 1930, 1920, 1800]}, {'name':'l115', 'cnname':'11选5', 'list':[1890, 1880, 1870, 1860, 1782]}];
	var getDataByName = function(name){
		var i = 0,len = data.length;
		for(;i < len;i++){
			if(data[i]['name'] == name){
				return data[i];
			}
		}
	};
	var gametype = $('#J-select-gametype'),group = $('#J-select-group'),
		textTip1 = $('#J-text-bomao'),textTip2 = $('#J-text-other');
	var sale = $('#J-num-sale'),profit = $('#J-num-profit'),point = $('#J-num-point'),lost = $('#J-num-lost'),
		button = $('#J-button-calc');
	var getCalculate = function(){
		//每月销售总额X最小点差+每月输额X分红比例
		var numSale = Number(sale.val()),
			numProfit = Number(profit.val())/100,
			numPoint = Number(point.val())/100,
			numLost = Number(lost.val()),
			other = (numSale * numPoint) + (numLost * numProfit),
			bound = [[100, 200, 0.3103], [200, 400, 0.3112], [400, 600, 0.3131], [600, 800, 0.3141], [800, 1000, 0.3172], [1000, 1200, 0.3213], [1200, 1400, 0.3223], [1400, 1600, 0.3232], [1600, 1800, 0.3241], [1800, 2000, 0.3253], [2000, 4000, 0.3272], [4000, 6000, 0.3282], [6000, 8000, 0.3293], [8000, 10000, 0.3312], [10000, 99999, 0.3320]],
			len = bound.length,
			i = 0,
			add = 0;
		for(;i < len;i++){
			if(numSale >= bound[i][0] && numSale <  bound[i][1]){
				add = bound[i][2];
				break;
			}
		}
		var bomao = other * (1 + add);
		return {'bomao':Number(bomao).toFixed(2), 'other':Number(other).toFixed(2)};
	};
	var maskBomao = $('#J-mask-bomao'),maskOther = $('#J-mask-other'),timer1,timer2,moneyBomao = $('#J-money-bomao'),moneyOther = $('#J-money-other'),less = $('#J-less-num'),isRunning = false;
	var showView = function(numBomao, numOther){
		//69%~70%
		var blBomao = parseInt(Math.random()*(70-69+1) + 69)/100,top = numBomao/blBomao,
			blOther = numOther/top;
		clearTimeout(timer1);
		clearTimeout(timer2);
		moneyBomao.text(bomao.util.formatMoney(numBomao * 10000));
		moneyOther.text(bomao.util.formatMoney(numOther * 10000));
		less.text('少赚 ' + bomao.util.formatMoney((numBomao - numOther) * 10000));
		maskBomao.stop(true).css('height', '25%').animate({'height':blOther*100 + '%'}, 1000).delay(500).animate({'height':(blBomao)*100 + '%'}, 1000).delay(500);
		maskOther.stop(true).css('height', '25%').animate({'height':blOther*100 + '%'}, 1000);
		timer1 = setTimeout(function(){
			isRunning = true;
			maskBomao.addClass('blink');
			timer2 = setTimeout(function(){
				maskBomao.removeClass('blink');
				isRunning = false;
			}, 600);
		}, 3000);
	};
	button.click(function(e){
		var result = getCalculate();
		e.preventDefault();
		if(isRunning){
			return false;
		}
		if(Number(sale.val()) < Number(lost.val())){
			alert('每月输额不能大于每月销售额');
			lost.focus();
			return false;
		}
		showView(result['bomao'], result['other']);
	});

	//选择彩种
	var selectType = new bomao.Select({realDom:'#J-select-gametype'});
	var sleectGroup = new bomao.Select({realDom:'#J-select-group'});
	selectType.addEvent('change', function(e, value, text){
		var dt = getDataByName($.trim(value)),i = 0,len = dt['list'].length,numValue = '',buildData = [];
		for(;i < len;i++){
			buildData.push({value:dt['list'][i],text:dt['list'][i],checked: i == 0 ? true : false});
		}
		sleectGroup.reBuildSelect(buildData);
		numValue = group.val();
		textTip1.find('.gametype').text(dt['cnname']);
		textTip2.find('.gametype').text(dt['cnname']);
		textTip1.find('.num').text(dt['name'] == 'ssc' ? 1960 : 1920);
		textTip2.find('.num').text(numValue);
	});
	sleectGroup.addEvent('change', function(e, value, text){
		var numValue = value;
		//textTip1.find('.num').text(numValue);
		textTip2.find('.num').text(numValue);
	});
	sale.keyup(function(){
		var num = Number(this.value.replace(/[^\d]/g, ''));
		this.value = num;
	});
	sale.blur(function(){
		var num = Number(this.value);
		num = num < 100 ? 100 : num;
		num = num > 10000 ? 10000 : num;
		this.value = num;
	});
	profit.keyup(function(){
		var float = this.value.replace(/[^\d|^\.]/g, '');
		if(isNaN(Number(float))){
			this.value = 0;
		}else{
			this.value = float;
		}
	});
	point.keyup(function(){
		var float = this.value.replace(/[^\d|^\.]/g, '');
		if(isNaN(Number(float))){
			this.value = 0;
		}else{
			this.value = float;
		}
	});
	lost.keyup(function(){
		this.value = Number(this.value.replace(/[^\d]/g, ''));
	});


})(jQuery);




//part-4
//视频播放
(function($){
	// var player = new SWFObject("/events/reserve_agent/images/vcastr2/vcastr2.swf","ply","800","450","9","#000000");
	// player.addParam("allowfullscreen","true");
	// player.addParam("allowscriptaccess","always");
	// player.addParam("wmode","opaque");
	// player.addParam("quality","high");
	// player.addParam("salign","lt");
	// player.addVariable("vcastr_file","http://192.168.62.104/images/video/bomao.flv");
	// player.write("J-video-p4");

	//轮播图
	var sliderPar = $('#J-tab-p4');
	var slider = new bomao.Slider({par:sliderPar, triggers:'.tab-t li', panels:'.tab-c-inner li', sliderDirection:'left', sliderIsCarousel:true});
	sliderPar.find('.toleft').click(function(e){
		//e.preventDefault();
		slider.controlPre();
	});
	sliderPar.find('.toright').click(function(e){
		//e.preventDefault();
		slider.controlNext();
	});



})(jQuery);

</script>


</body>
</html>

