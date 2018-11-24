@extends('l.gxy28')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop




@section ('centerGame')
	<div class="happy-panel" oncontextmenu="return false" onselectstart="return false">
		<div class="lucky-main-panel">
			@foreach($aLotteryCodes as $iLotteryId => $sLotteryCode)
				<div id="{{$sLotteryCode}}" class="game"></div>
			@endforeach
			<div id="orderWindow"></div>	
		</div>
		
		<div id="clockList">
			<div class="clock-slider">
				<div class="logo"></div>
				@foreach($aLotteryCodes as $iLotteryId => $sLotteryCode)
					<div id="{{$sLotteryCode}}-clock" class="clock"></div>
				@endforeach
			</div>
		</div>

		<div class="informationList">
			<div class="resultButton">
				<span class="result-logo"></span>
				<span class="result-lab">开奖结果</span>
			</div>
			<div class="historyButton">
				<span class="history-logo"></span>
				<span class="history-lab">历史记录</span>
			</div>
			<div class="introduce">
				<span class="introduce-logo"></span>
				<span class="introduce-lab">彩票介绍</span>
			</div>
		</div>

		<div class="record-panel"></div>
	</div>

	<div class="ie-tips">
		<div class="up-ie-panel">
			<div class="browser-box">
				<ul>
					<li>
						<span class="browser-lab" param="0">谷歌浏览器2016(Chrome) v51.0.2704.106 官方正式版</span>
					</li>
					<li>
						<span class="browser-lab" param="1">360安全浏览器2016 V7.1.1.814 官方正式版</span>
					</li>
					<li>
						<span class="browser-lab" param="2">火狐firefox V46.0.1 官方简体中文版</span>
					</li>
					<li>
						<span class="browser-lab" param="3">百度浏览器 8.4.100.3593 官方正式版</span>
					</li>
					<li>
						<span class="browser-lab" param="4">傲游(maxthon) 4.9.3.1000 官方最新版</span>
					</li>
				</ul>
			</div>
		</div>
		<div class="down-ie-panel"></div>
	</div>
	
	<input id="new-service-time" type="hidden">
@stop







@section('end')
@parent

<script type="text/javascript">
	var global_game_config_lucky28 = {{ $sLotteryConfig }};
	var global_game_init_data_lucky28 = {{$sLastIssues}};
	var history_game_lucky28 = {{ $sFinishedIssues }};
	var help = {{ $sCmsArticle }};

	setInterval(function(){
		$('#new-service-time').val(new Date().getTime());
	} , 500);

</script>

{{ script('game-lucky28-all') }}


@stop

















