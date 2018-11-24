@extends('l.table')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop



@section ('styles')
@parent
	{{ style('game-table') }}
@stop




@section ('container')
	@include('w.header')



	<audio src="" controls="controls" hidden="true" style="display:none" id="dice-tips-audio"> </audio>
	<div class="table-layout">
		<div class="loading-mask">
            <div class="load-img">
	            <div class="load-bar">
	                <span class="load-bar-unfill">
	                    <span class="load-bar-fill"></span>
	                </span>
	            </div>
            </div>
        </div>
		<div class="container top">
			<div class="empty-bg">
				<button class="btn-help">操作和限赔说明</button>
			</div>
			<span class="logo"></span>
			<span class="mm"></span>

			<div class="history">
				<div class="history-container">
					<ul class="his-list">
					</ul>
				</div>
				<div class="pagination">
					<a class="page last-page" href="javascript:;"></a>
					<a class="page next-page" href="javascript:;"></a>
					<a class="page curr-page" href="javascript:;"></a>
				</div>
				<div class="time">
					<div class="title">上期中奖骰子</div>
					<div class="balls">
						<!-- <i class="dice dice-1"></i>
						<i class="dice dice-2"></i>
						<i class="dice dice-3"></i> -->
					</div>
					<div class="number-time">
						<div class="left">
							第<span class="hl" id="J-current-number">150904357</span>期
							<br />
							投注截止
						</div>
						<div class="right" id="J-clock-number">
							<!-- <span class="num">00</span>: -->
							<span class="num">00</span> : <span class="num">00</span>
						</div>
					</div>
				</div>
			</div>
		</div>








		<div class="container">
			<div class="table-main" id="J-desktop">
				<div class="table-mask-lock" id="J-table-mask-lock"></div>
				<div class="money-quota"><font id="table-name">娱乐场</font><font id="table-num">1</font>桌<br />限赔<font class="compensation-limit"> <font id="max-prize">5</font>万</font></div>



				<div class="tagle-game-cup" id="J-tagle-game-cup">
					<img width="100%" src="/assets/images/game/table/dice/cup.png" />
					<div class="dices">
						<i class="dice dice-1"><img width="100%" src="/assets/images/game/table/dice/cup-dice-1.png" /></i>
						<i class="dice dice-2"><img width="100%" src="/assets/images/game/table/dice/cup-dice-2.png" /></i>
						<i class="dice dice-3"><img width="100%" src="/assets/images/game/table/dice/cup-dice-3.png" /></i>
					</div>
				</div>

				<div class="table-game-sandglass">
					<div class="table-game-sand"></div>
				</div>

				<div class="table-hot-cold">
					<div class="hot-area">
						冷
					</div>
					<div class="cold-area">
						热
					</div>
				</div>
		
				<input class="table-hot-cold-txt" id="hot-cold-txt" placeholder="30">

				<div class="table-result" id="J-panel-result">
				</div>
				<div class="table-notice2" id="J-panel-notice2">
					<div class="table-notice2-content"></div>
					<a class="close-notice2" href="javascript:;">关闭</a>
				</div>
			</div>


			<div class="table-bar">
				<div class="chips-cont" id="J-chip-group-cont">

				</div>


				<div class="buttons">
					<button id="J-button-clearall" class="btn-clear"></button>
					<button id="J-button-submit" class="btn-submit"></button>
					<button id="J-button-rebet" class="btn-rebet"></button>
					<button id="J-button-double" class="btn-double"></button>
				</div>




				<div class="balance">
					<span class="balance-bg"></span><span class="balance-txt">桌面金额：</span><span id="J-money-bet" class="money">0元</span>
				</div>
				<div class="money-bet">
					<span class="money-bg"></span><span class="money-txt">账户余额：</span><span id="J-money-user-balance" class="money">{{ number_format($fAvailable, 2) }}元</span>
				</div>
				<div class="bet-bonus">
					+ <span class="bonus"></span>
				</div>
				<div class="win-bonus-container">
					+ <span class="win-bonus-txt"></span>
				</div>
			</div>

			<div class="table-how-to">


			</div>

		</div>



	<div class="container bottom">
		<div class="bet-records">
			
			<table class="tb-bet-records">
				<thead>
					<tr><th colspan="9"><div class="btn-toggle-records up"></div></th></tr>
					<tr class="tb-header">
						<th><div class="th-text">期号</div><div class="th-seperator"></div></th>
						<th><div class="th-text">投注时间</div><div class="th-seperator"></div></th>
						<th><div class="th-text">玩法</div><div class="th-seperator"></div></th>
						<th><div class="th-text">投注内容</div><div class="th-seperator"></div></th>
						<th><div class="th-text">中奖骰号</div><div class="th-seperator"></div></th>
						<th><div class="th-text">投注金额</div><div class="th-seperator"></div></th>
						<th><div class="th-text">中奖金额</div><div class="th-seperator"></div></th>
						<th><div class="th-text">状态</div><div class="th-seperator"></div></th>
						<th><div class="th-text">操作</div></th>
					</tr>
				</thead>
				<tbody class="body-bet-records">
				</tbody>
			</table>
		</div>
	</div>






	@include('w.footer-v4')
@stop







@section('end')
@parent
<script type="text/javascript">

var global_game_config = {{ $sLotteryConfig }};
var global_last_bet_history = {{ $sLastBetHistory }};
var global_balance = Math.floor({{ $fAvailable }});

</script>

<script type="text/javascript">
    //进入游戏时，增加遮罩进度条
    setTimeout(function(){
        $(".loading-mask").hide();
    },2000);
</script>

{{ script('game-dice-init') }}


<script type="text/javascript">
	// 展开和折叠投注记录
	$(".btn-toggle-records").click(function(){
		$(".table-layout .bottom .tb-bet-records tbody").toggle();
		$(this).toggleClass("down").toggleClass("up");
	})
</script>

<script type="text/javascript">
	
	// 开奖历史记录翻页 
	(function(){
		
		var itemsPerPage = 20,
			liLen = $(".his-list > li").length,
			pageCount = Math.ceil(liLen/itemsPerPage),
			currPage = 1;

		var paginate =  function(currPage){

			var ltIndexShow = currPage * itemsPerPage,
				gtIndexShow = (currPage - 1)*itemsPerPage -1,

				ltIndexHide = (currPage - 1)*itemsPerPage,
				gtIndexHide = currPage * itemsPerPage-1;


			$.each($(".his-list > li:lt("+ltIndexShow+")"),function(i){
				$(this).show();
			});
			$.each($(".his-list > li:gt("+gtIndexShow+")"),function(i){
				$(this).show();
			});
			$.each($(".his-list > li:lt("+ltIndexHide+")"),function(){
				$(this).hide();
			});
			$.each($(".his-list > li:gt("+gtIndexHide+")"),function(){
				$(this).hide();
			});
		};

		


		$(".last-page").click(function(){
			
			currPage = currPage+1;
			if(currPage > pageCount){
				currPage = pageCount;
			}
			paginate(currPage)
		});
		$(".next-page").click(function(){
			currPage = currPage-1;
			if(currPage <= 0){
				currPage = 1;
			}
			paginate(currPage);
		});
		$(".curr-page").click(function(){
			paginate(1);
		});

		paginate(currPage);
	})()



	$(".btn-help").click(function(){
		$("#J-panel-notice2").toggle();
		$(".table-notice2-content").html($("#J-script-play-mothed").text());
	})

	$(".close-notice2").click(function(){
		$("#J-panel-notice2").css("display",'none');
	});



</script>
<script type="text/template" id="J-script-play-mothed">
	<div class="play-method">
        <div class="play-summary-title">
            游戏说明
        </div>
        <div>
            <div class="play-summary-content">骰宝源于中国古老的骰子游戏，至今已风靡席卷全球。玩家可选择多种不同赔率的玩法同时下注，包括大小单双、对子、豹子、和值、双骰和单骰，各玩法中奖说明可点击其左上角？号查看。
            </div>
            <div class="play-schedule-title">
                操作说明
            </div>
            <div class="play-schedule-content">
                <div class="first">
                    1.投注流程：
                    <div class="item">
                        ->选择筹码
                    </div>
                    <div class="item">
                        ->点击投注区域进行投注
                    </div>
                    <div class="item">
                        ->确认完所有投注后，点击“确认投注“等待开骰，等待期间不可再继续投注
                    </div>
                    <div class="item">
                        ->显示开骰结果，进入下一期继续投注
                    </div>
                </div>
                <div class="second">
                    2.桌面按钮:
                    <div class="item">
                        - 清桌：撤销当前桌面的所有投注筹码
                    </div>
                    <div class="item">
                        - 重押：恢复上期成功投注的投注桌面
                    </div>
                    <div class="item">
                        - 翻倍：当前桌面的所有投注筹码乘以2
                    </div>
                </div>
                <div class="third">
                    3.右键(桌面投注区域)
                    <div class="item">
                        - 撤销：撤销所选投注区域顶面的投注筹码
                    </div>
                    <div class="item">
                        - 清空：撤销所选投注区域所有的投注筹码
                    </div>
                    <div class="item">
                        - 翻倍：所选投注区域的筹码乘以2
                    </div>
                    <div class="item">
                        - All In：下押所有的账号余额
                    </div>
                </div>
                <div class="fourth">
                4.冷热：
                	 <div class="item">将鼠标移至桌面右侧的冷热按钮上1至3秒，则可显示最近N期的冷热号，默认为30期</div>
                </div>
                <div class="fifth">
                	<font class="limit">限赔</font>
                    <div class="item">单人单期最高赔付总奖金。举例说明：在高级场的某期内，某玩家押注了6万“大”，6万“单”，结果开奖骰子为5,5,7，则根据赔率1:1计算共返奖24万，但由于高级场限赔上限是20万，因此实际返奖仅为20万。</div>
                </div>
            </div>
</script>

@stop

















