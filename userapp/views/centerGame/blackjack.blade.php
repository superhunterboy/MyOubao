@extends('l.ghome')

@section('title')
	{{ __($sLotteryName) }}
@parent
@stop
<style>
	html, body { height:100%;  overflow:hidden;  }
	body { margin:0;}
</style>

@section ('container')
	@include('w.header')
<div id="gameView" width="100%">
	<div id="altContent">
		<h1>BlackJack2D</h1>
		<p><a href="http://www.adobe.com/go/getflashplayer">Get Adobe Flash player</a></p>
	</div>
</div>

@stop
@section('end')
@parent
<script src="/assets/js/swfobject.js"></script>
	<script>
		var global_game_config_ssc = {{ $sLotteryConfig }};
		
		var log = function(info){
			console.log(info);
		};
		var updateBalance = function(num){
			bomao.Games.updateUserAccount(num);
		};
        var changeViewScale = function(value){
			document.body.style.zoom = value/100;
		};
		
		document.getElementById("gameView").style.height = (window.innerHeight - 125)+"px";
		window.onresize = function(){
			document.getElementById("gameView").style.height = (window.innerHeight - 125)+"px";
		}
		var scaleMode = window.innerHeight > 825 ? "noscale" : "noborder";//"showall";
		var flashvars = {
			resRoot:'/assets/images/game/table/blackjack/',
			gameId : global_game_config_ssc['gameId'],
			lotery:1,
			stage:1,
			submitUrl : global_game_config_ssc['submitUrl'],
			loaddataUrl : global_game_config_ssc['loaddataUrl'],
			pollUserAccountUrl : global_game_config_ssc['pollUserAccountUrl'],
			rechargeUrl:global_game_config_ssc['rechargeUrl'],
			currentTime : 1469505655,
			_token : global_game_config_ssc['_token'],
			is_agent : global_game_config_ssc['is_agent'],
			is_encode:global_game_config_ssc['is_encode'],
			table:global_game_config_ssc['table'],
			env:global_game_config_ssc['env']
		};
		var params = {
			menu: "false",
			scale: scaleMode,
			allowFullscreen: "true",
			allowScriptAccess: "always",
			bgcolor: "0x0c151e",
			//align:"middle",
			wmode: "direct" // can cause issues with FP settings & webcam
		};
		var attributes = {
			id:"BlackJack2D"
		};
		swfobject.embedSWF(
			"/assets/images/game/table/blackjack/BlackJack2D.swf",
			"altContent", "100%", "100%", "10.0.0",
			"/assets/images/game/table/blackjack/expressInstall.swf",
			flashvars, params, attributes);
			
	</script>






@stop




