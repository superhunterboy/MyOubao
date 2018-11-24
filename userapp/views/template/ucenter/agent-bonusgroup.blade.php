@extends('l.home')

@section('title')
   我的奖金
@parent
@stop

@section('scripts')
@parent
    {{ script('jscroll')}}
    {{ script('tab')}}
    {{ script('sliderBar')}}
    {{ script('datePicker')}}
    {{ script('U-groupgame')}}
@stop

@section('main')
		<div class="nav-bg">
			<div class="title-normal">奖金组管理</div>
		</div>


		<input type="hidden" value="<?=$root_path?>/data/groupbonus.php" id="J-loadGroupData-url" />
		<form action="?" method="post" id="J-form">
			<input type="hidden" id="J-input-custom-type" value="" />
			<input type="hidden" id="J-input-custom-id" value="" />

		<div class="content" id="J-panel-cont">
			<div class="bonusgroup-title">
				<table width="100%">
					<tr>
						<td>Terence2014<br /><span class="tip">用户名称</span></td>
						<td>特伦苏<br /><span class="tip">用户昵称</span></td>
						<td>代理<br /><span class="tip">用户类型</span></td>
						<td>66,888,888.00 元<br /><span class="tip">可用余额</span></td>
						<td class="last">66,888,888.00 元<br /><span class="tip">奖金限额</span></td>
					</tr>
				</table>
			</div>

			<div class="row-title">奖金组设置</div>

						<script type="text/template" id="J-template-group">
							<li>
								<div class="bonus"><strong class="data-bonus"><#=bonus#></strong>当前奖金</div>
								<div class="rebate"><strong class="data-feedback"><#=feedback#>%</strong>预计平均返点率</div>
								<a href="#">查看奖金组详情</a>
								<input type="button" class="btn button-selectGroup" value="选 择" data-groupid="<#=id#>" />
							</li>
						</script>
			<div class="bonusgroup-game-type">
						<script type="text/template" id="J-template-gametype">
							<ul class="clearfix gametype-row">
								<#=listloop#>
							</ul>
						</script>
						<script type="text/template" id="J-template-gamesitem">
							<li>
								<a href="#" class="item-game" data-id="<#=id#>" data-itemType="game"><span class="name"><#=name#></span><span class="group"><#=bonus#></span></a>
							</li>
						</script>
						<div id="J-group-gametype-panel">
						</div>

					</div>



				<div class="bonusgroup-title">
						<table width="100%">
							<tr>
								<td class="last">
									<div class="bonus-setup">
										<div class="bonus-setup-title">
											<strong>设置奖金</strong>
											<span class="tip">奖金组一旦上调后则无法降低，请谨慎操作。</span>
										</div>

										<div class="bonus-setup-content">
											<div class="slider-range" onselectstart="return false;">

												<div class="slider-range-sub" id="J-slider-minDom"></div>
												<div class="slider-range-add" id="J-slider-maxDom"></div>

												<div class="slider-range-wrapper" id="J-slider-cont">
													<div class="slider-range-inner" style="width:0;" id="J-slider-innerbg"></div>
													<div class="slider-range-btn" style="left:0;" id="J-slider-handle"></div>
												</div>
												<div class="slider-range-scale">
													<span class="small-number" id="J-slider-num-min">1800</span>
													<span class="big-number" id="J-slider-num-max">1960</span>
												</div>
											</div>
										</div>
									</div>
								</td>

								<td>
									<input type="text" class="input w-1" style="text-align:center;" value="1955" id="J-input-custom-bonus-value" />
									<br><span class="tip">&nbsp;&nbsp;&nbsp;<a href="#">查看详情</a>&nbsp;&nbsp;&nbsp;</span>
								</td>
								<td class="last"><strong id="J-custom-feedback-value">4.5%</strong><br><span class="tip">预计平均返点率</span></td>
							</tr>
						</table>
			</div>

			<div class="row-lastsubmit">
				<input type="submit" class="btn" value="保存奖金组设置" id="J-button-submit">
			</div>




		</div>
		</form>
@stop

@section('end')
@parent
<script type="text/javascript" src="../js/bomao.ucenter.groupgame.js"></script>

<script>
(function(){



	//表单提交
	$('#J-button-submit').click(function(){
		if($.trim($('#J-input-custom-type').val()) == ''){
			alert('请选择一个游戏或者彩种进行设置');
			return false;
		}
		return true;
	});

})();
</script>
@stop