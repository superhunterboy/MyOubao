@extends('l.ghome')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop


@section('styles')
@parent
<style type="text/css">
.gametypes-menu-panel .gametypes-menu-erxing .types-node-zhixuan {height: auto;}
.gametypes-menu-panel .gametypes-menu-erxing .types-node-zuxuan {height: auto;}
.gametypes-menu-panel .gametypes-menu-budingwei .types-node-sanxingbudingwei {height: auto;}

.play-select-title .gametypes-menu-qiansan .content .types-node-zhixuan {height: auto;}
.play-select-title .gametypes-menu-erxing .content .types-node-zhixuan {height: auto;}
.play-select-title .gametypes-menu-erxing .content .types-node-zuxuan  {height: auto;}
.play-select-title .gametypes-menu-daxiaodanshuang .content dd.types-node {height: auto;}
.play-select-title .gametypes-menu-budingwei dd.types-node-sanxingbudingwei  {height: auto;}
.play-select-title .gametypes-menu-budingwei .content {left: 0;}
.play-select-title .gametypes-menu-erxing .content {left: 0;}
.play-select-title .gametypes-menu-budingwei .content .sj {left: 33px;}
</style>
@stop


@section ('centerGame')

		<div class="balls-float-panel">
			<div id="J-balls-main-panel" class="balls-main-panel">

			</div>
			@include('w.ball-statistics-panel')
		</div>
		<textarea id="J-textarea-historys-balls-data" style="display:none;">
			{{ $sFinishedIssues }}
		</textarea>

		<div class="list-historys" id="J-list-historys">
			<div class="method-current-prize">
				单注奖金:
				<span id="J-method-prize" class="prize-num"></span>
				元
			</div>
			<div class="inner">
				<div class="cont" id="J-minitrend-cont">
					<div class="more"><a target="_blank" href="{{ route('user-trends.trend-view', [$iLotteryId]) }}">查看完整走势</a></div>
				</div>

			</div>
		</div>

@stop







@section('end')
@parent
<script type="text/javascript">

var global_game_config_p35 = {{ $sLotteryConfig }};

</script>

{{ script('game-p35-init') }}

@stop

















