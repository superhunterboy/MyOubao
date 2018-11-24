@extends('l.ghome')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop


@section ('styles')
@parent
    {{ style('game-l115') }}
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

var global_game_config_ssc = {{ $sLotteryConfig }};

</script>

{{ script('game-n115-init') }}

@stop

















