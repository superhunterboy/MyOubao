@extends('l.base-v4')

@section('title')
    抽奖活动
@stop

@section ('styles')
@parent
	{{ style('win-prize-activety')}}
@stop


@section ('container')
    @include('w.header')
	<input type="hidden" name="_token" id="J-global-token-value-activity" value="{{ csrf_token() }}">
	<div class="main">
		<!--积分信息-->
		<div class="score-area">
			<span class="score-area-lab">我的积分</span>
			<span class="my-score"></span>
			<span class="score-area-lab ask-lab">如何获得积分？</span>
		</div>
		<!--抽奖区-->
		<div>
			<div class="mask">
				<div class="numbers-area">
					<div class="numbers-mask">
						<div class="right-num"></div>
					</div>
				</div>
			</div>
			<div class="opention-area">

                @if(count($aAllHand[0]) > 0)
                @foreach($aAllHand as $k=>$oData)
                @if("银手柄" == $oData->name)
				<div id="silver-bt" class="score-img" select="true" value="{{{$oData->money}}}">
					<span class="score-lab">{{{$oData->money}}}</span>
					<span class="score-lab score-lab-name">积分</span>
				</div>
                @endif
                @if("金手柄" == $oData->name)
				<div id="gold-bt" class="score-img" select="false" value="{{{$oData->money}}}">
					<span class="score-lab">{{{$oData->money}}}</span>
					<span class="score-lab score-lab-name">积分</span>
				</div>
                @endif
                @if("猫爪" == $oData->name)
				<div id="cat-bt" class="score-img" select="false" value="{{{$oData->money}}}">
					<span class="score-lab">{{{$oData->money}}}</span>
					<span class="score-lab score-lab-name">积分</span>
				</div>
                @endif
                @endforeach
                @endif
				<div id="start-bt"></div>
				<div id="history-bt">
					<span class="history-logo"></span>
					<span class="history-lab">中奖记录</span>
				</div>
			</div>
		</div>
		<!--奖品展示区-->
		<div class="info-area">
			<div class="prize-list-area">
				<div class="prize-title">本期奖品详情</div>
                @if(count($aAllHand) > 0)
                @foreach($aAllHand as $k=>$data)
				<div class="pirze-list">
					<div class="pirze-list-title">{{$data->name}}</div>
					<hr class="line" />
					<ul>
                        @if(count($data->rule) > 0)					<!--此处开始循环生成奖品-->
                        @foreach($data->rule as $m=>$rule)
						<li class="prize-show">
							<span class="prize-index rank-{{$rule->id}}"></span>
							<div class="prize-img">
								<img style="width:100%;height:100%" src="/assets/images/win-prize-activity/product/product-{{$rule->id}}.png" />
							</div>
							<ul class="prize-detail">
								<li class="prize-lab">
									<span class="prize-name-lab">{{$rule->name}}</span>
								</li>
								<li class="prize-lab">
									价值:&nbsp;<span class="prize-value-lab">{{$rule->price}}</span>&nbsp;元
								</li>
								<li class="prize-lab">
									<span class="prize-disc-lab">{{$rule->content}}</span>
								</li>
							</ul>
						</li>
                        @endforeach
                        @endif					<!--此处结束循环生成奖品-->
					</ul>
				</div>
                @endforeach
                @endif
			</div>
		</div>
		
		<!--中奖提示-->
		<div class="winner-prize">
			<div class="prize-info">
				<div class="winner-prize-img">
					<img class="winner-img" style="width:100%;height:100%" src="" />
				</div>

				<ul class="prize-detail">
					<li class="prize-lab">
						<span id="winner-name" class="prize-name-lab"></span>
					</li>
					<li class="prize-lab">
						<span id="winner-disc" class="prize-disc-lab"></span>
					</li>
				</ul>
			</div>
			<div class="winner-prize-bt">
				<a class="server-bt" href="javascript:;"></a>
			</div>
		</div>
		
		<!--历史记录-->
		<div class="history-box">
			
		</div>
	</div>

	<audio id="winnerAudio" src="assets/images/win-prize-activity/winner.mp3" type="audio/mpeg"></audio>

	@include('w.footer')
@stop

@section('end')
@parent

{{ script('jquery.easing') }}

<script type="text/javascript">
	//重写animate动画，兼容firefox
	(function($) {
	    $.fx.step["backgroundPosition"] = function(fx) {
	        if (typeof fx.end == 'string') {
	            fx.start = getBgPos(fx.elem);
	            //fx.end原本是一个string，这里将它转换成数组，就不会再进入这个if，也方便我们下面的计算
	            //例 "0px -21px"
	            fx.end = [parseFloat(fx.end.split(" ")[0]), parseFloat(fx.end.split(" ")[1])];
	        }
	        //这里fx.pos是根据传入的时间参数，从0到1变化的浮点数
	        var nowPosX = ((fx.end[0] - fx.start[0]) * fx.pos) + fx.start[0] + fx.unit;
	        var nowPosY = ((fx.end[1] - fx.start[1]) * fx.pos) + fx.start[1] + fx.unit;
	        fx.elem.style.backgroundPosition = nowPosX + ' ' + nowPosY;
	         
	        /**
	         * 获取backgroundPosition数组[top, left]，没有单位
	         */
	        function getBgPos(elem) {
	            var top  = 0.0;
	            var left = 0.0;
	            if ($(elem).css("backgroundPosition")) {
	                //例 "0px -21px"
	                top  = parseFloat($(elem).css("backgroundPosition").split(" ")[0]);
	                left = parseFloat($(elem).css("backgroundPosition").split(" ")[1]);
	            }else{
	                top  = parseFloat($(elem).css("backgroundPositionX"));
	                left = parseFloat($(elem).css("backgroundPositionY"));
	            }
	            return [top, left];
	        }
	    }
	})(jQuery);
</script>

<script type="text/javascript">
	//锁
	var isBegin = false;
	var numbers_area = $(".numbers-area");
	var start_bt = $("#start-bt");
	//图片数组
	var numbers_arr = [0,1,7,0,5,2,4,0,6,3,8,11,0,10,9];
	//未中奖位置
	var no_num = [0,3,7,12];
	//积分ID
	var scoreIndex = $('#silver-bt').attr('value');
	//中奖音效
	var myAudio = $("#winnerAudio");
	//开奖结果
	var result;
	//我的积分
	var my_score = {{{$sumLs}}};
	//中奖信息
	var prize_data = null;

	var tip = new bomao.Tip({cls:'j-ui-tip-l j-ui-tip-info tip-style'});
	//非正常状态的错误提示
	var popWindow = new bomao.Message();

	//初始化积分
	$(function(){
		updateScore(my_score);
	});

	//更新积分DOM
	function updateScore(score){
		var score_str = score.toString();
		var len = score.toString().length;
		$(".my-score").html("");
        for(var i=0 ; i<len ; i++){
        	var num = score_str.substr(i,1);
        	var score_num = "<span class='score-num score-num-"+num+"'></span>";
        	$(".my-score").append(score_num);
        }
	}

	//发送请求
	function submitFunction(callback){
		var url = "/mdactivity/reward";
		var data = {id: scoreIndex , _token: "{{ csrf_token() }}"};
		$.ajax({
			type: "post",
			url: url,
			data: data,
			dataType: "json",
			success:function(data){
				if($.isFunction(callback)){
					callback.call(this,data);
				}
			},
			error:function(data){
				// console.log(data);
			}
		});
	}

	//历史请求
	function getHistoryFunction(callback){
		var url = "/mdactivity/history";
		$.ajax({
			type: "get",
			url: url,
			dataType: "html",
			success:function(data){
				if($.isFunction(callback)){
					callback.call(this,data);
				}
			},
			error:function(data){
				// console.log(data);
			}
		});
	}

	var mask_bg = new bomao.Mask();
	//显示中奖详情
	function showPrizeDetail(){
		mask_bg.show();
		$(".winner-prize").show();
		//写入中奖信息
		if(parseInt(prize_data['level']) != 0){
			//中奖显示
			$(".winner-prize").css('background-position', "0px 0px");

			var img_ads = 'assets/images/win-prize-activity/product/product-b-'+prize_data['level']+'.png';
			$(".winner-img").attr('src', img_ads);

			$("#winner-name").html(prize_data['name']);
			$("#winner-disc").html(prize_data['content']);
			$(".winner-prize-bt").removeClass('prize-bt2');

			//自动派送显示返回按钮，非自动派送显示联系客服
			if(prize_data['is_sent']){
				//描述改完自动充值
				$("#winner-disc").html("系统已自动充值到账户！");

				$(".server-bt").text('确认');
				$(".server-bt").attr('href', 'javascript:;');
			}else{
				$(".server-bt").text('联系客服领取');
				$(".server-bt").attr('href', 'javascript:javascript:hj5107.openChat();');
			}
			
			$(".prize-info").show();
		}else{
			$(".prize-info").hide();
			$(".winner-prize-bt").addClass('prize-bt2');

			$(".server-bt").text('确认');
			$(".server-bt").attr('href', 'javascript:;');
			$(".winner-prize").css('background-position', "0px -672px");
		}
	}
	//关闭中奖详情
	function hidePrizeDetail(){
		mask_bg.hide();
		$(".winner-prize").hide();

		$("#winner-name").html("");
		$("#winner-value").html("");
		$("#winner-disc").html("");

		//按钮样式恢复
		isBegin = false;
		start_bt.removeClass('gray');
		popWindow.hide();
	}

	//抽奖动画
	function prizeAnimation(data){
		var typeFlag = data['error'];

		//正常状态
		if(data['isSuccess'] == undefined){
			if(typeFlag == undefined){
				//结果
				result = parseInt(data['data']['level']);
				//数字宽度
				var u = 106;

		    	//中奖号所做位置
		    	var r ;
		    	if(result!=0){
		    		r = numbers_arr.indexOf(result);
		    	}else{
		    		//如果未中奖，随机一个0的位置
		    		var n = parseInt(Math.random() * 4);
		    		r = no_num[n];
		    	}
		    	prize_data = data['data'];

		    	//15个数组长度 + 旋转7圈 + 位置调整
		    	numbers_area.animate({
		            backgroundPosition: -(u*15*7 + u*(r-7)) + "px 0px"
		        }, {
		            duration: 5000,
		            easing: "easeInOutQuad",
		            complete: function() {
		            	//显示中奖信息
		                showPrizeDetail();

		                isBegin = false;
		                start_bt.removeClass('gray');
		                if(result!=0){
		                	myAudio.get(0).play();
		                }
		            }
		        });

		    	//更新积分
		        updateScore(data['data']['integral']);
			}else{
				//非正常状态的错误提示
				var data = {
					title : '提示',
					content : data['Msg'],
					isShowMask : true,
					closeIsShow : true,
					closeButtonText: '关闭',
					closeFun : function() {
						this.hide();
						isBegin = false;
						start_bt.removeClass('gray');
					}
				};
				popWindow.hideClose();
				popWindow.show(data);
			}
		}else{
			//非正常状态的错误提示
			var data = {
				title : '提示',
				content : data['Msg'],
				isShowMask : true,
				closeIsShow : true,
				closeButtonText: '关闭',
				closeFun : function() {
					this.hide();
					isBegin = false;
					start_bt.removeClass('gray');
				}
			};
			popWindow.hideClose();
			popWindow.show(data);
		}
		
	}

	//显示中奖记录
	function showPrizeHistory(data){
		// console.log(data);
		// console.log(data , data["isSuccess"] , data.isSuccess);
		if(data['isSuccess']==undefined){
			mask_bg.show();
			//写入记录
			$(".history-box").html(data);
			$(".history-box").show();
		}else{
			//非正常状态的错误提示
			var data = {
				title : '提示',
				content : data['Msg'],
				isShowMask : true,
				closeIsShow : true,
				closeButtonText: '关闭',
				closeFun : function() {
					this.hide();
				}
			};
			popWindow.hideClose();
			popWindow.show(data);
		}
	}

	//关闭中奖记录
	function hidePrizeHistory(){
		mask_bg.hide();
		$(".history-box").hide();
	}

	$(function (){
		//银 金 猫爪 点击效果
		$(".score-img").click(function(event) {
			scoreIndex = $(this).attr('value');
			//还原
			$(".score-img").css('background-position', "0px 0px");
			$(".score-img").css('font-size', "16px");

			//选中设置
			$(".score-img").attr('select' , 'false');
			$(this).attr('select' , 'true');
			$(this).css('background-position', "0px -125px");
			$(this).css('font-size', "18px");
		}).mouseenter(function(event) {
			$(this).css('background-position', "0px -125px");
			$(this).css('font-size', "18px");
		}).mouseleave(function(event) {
			if($(this).attr('select')=='false'){
				$(this).css('background-position', "0px 0px");
				$(this).css('font-size', "16px");
			}
		});

		//play按钮
		start_bt.click(function(event) {
			if (isBegin) return false;
        	isBegin = true;

        	numbers_area.css('background-position', "0px 0px");
	    	start_bt.addClass('gray');
        	//发送请求
        	submitFunction(prizeAnimation);
		});

		//确认 或 派发奖品按钮
		$(".server-bt").click(function(event) {
			hidePrizeDetail();
		});

		//点击mask，进行关闭中奖结果
		$(".j-ui-mask").click(function(event) {
			hidePrizeDetail();
			hidePrizeHistory();
		});

		$(".ask-lab").mouseenter(function(event) {
			tip.setText("2016年9月28日至10月8日期间，在欧豹娱乐投注的金额会自动以1:1兑换成抽奖积分。投注越多，积分越多，就有可能抽取更多好礼！");
			tip.show(160,10,this);
		}).mouseleave(function(event) {
			tip.hide();
		});

		//抽奖记录
		$("#history-bt").click(function(event) {
			//开始抽奖时 点击记录无响应
			if (isBegin) return false;
			getHistoryFunction(showPrizeHistory);
		});

	})

</script>

@stop