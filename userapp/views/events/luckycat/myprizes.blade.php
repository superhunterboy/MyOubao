<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>幸运博猫</title>

@section ('styles')
      {{ style('eventLottery')}}
		{{ style('global')  }}
@show
<style>
body {background:#FFF;}
.mini-list {width:760px;overflow-x:hidden;}
.mini-table {background:#FCF7E7;font-family:microsoft yahei;border-top:2px solid #3B2F49;}
.mini-table th,.mini-table td {font-size:14px;color:#4a3e58;text-align:center;border:1px solid #C1BAAB;}
.mini-table th {background:#A69C87;padding:10px 0;color:#FFF;}
.mini-table td {padding:10px 0;}
.mini-table td.first {border-left:0;}
.mini-table td.last {border-right:0;}

.mini-table .ticket {color:#FFF; cursor:pointer;display:inline-block;background:#716781;padding:2px 5px;
border-radius:2px;
}
.table-tip {margin:5px 0;}
.table-tip td {border:1px solid #C1BAAB;padding:10px;text-align:center;}

.control {padding:10px;margin-top:10px;color:#4a3e58;}
.control .total {}
.control .total .num {display:inline-block;padding:0 4px;color:#cb2e53;}
.control .pages {float:right;width:330px;}
.control .pages .total {float:left;}
.control .pages a {float:left;width:35px;height:35px;text-align:center;line-height:35px;border:1px solid #DADADA;margin-left:-1px;color:#888;}
.control .pages a.current {color:#F60;font-weight:bold;}
.control .pages .pre {background:url(images/page-pre.gif) no-repeat center center;}
.control .pages .next {background:url(images/page-next.gif) no-repeat center center;}

</style>
@section('javascripts')
  {{ script('jquery-1.9.1') }}
  {{ script('bomao.base') }}
  {{ script('bomao.Tip') }}
@show

</head>

<body>



<div class="mini-list">
	<table width="100%" class="mini-table" id="J-table-list">
		<tr>
			<th class="first">奖品名称</th>
			<th>奖品价值</th>
			<th class="last">中奖时间</th>
		</tr>

		<?php

			$i	= 0;
			foreach ($datas as $data) :
			$i ++;
			?>
		<tr>
			<td class="first">

				<?php
				if (in_array($data->prize_id, [10, 11])) :

				?>
					<span class="ticket">{{$data->prize_name}}</span>
					<div class="ticket-cont" style="display:none;">
						<table width="100%" class="table-tip">
							<tr>
								<td>第一天</td>
								<td>第二天</td>
								<td>返利金额</td>
							</tr>
							<tr>
								<td><?php if(date('Y-m-d') >= date('Y-m-d', strtotime($data->created_at . ' +1 day'))) : ?>{{$data->getTurnoverDay(1)}}<?php else : echo '统计中'; endif; ?></td>
								<td><?php if(date('Y-m-d') >= date('Y-m-d', strtotime($data->created_at . ' +2 day'))) : ?>{{$data->getTurnoverDay(2)}}<?php else : echo '统计中'; endif; ?></td>
								<td><?php if(date('Y-m-d') >= date('Y-m-d', strtotime($data->created_at . ' +2 day'))) : ?>{{$data->getMoneyback()}}<?php else : echo 'N/a'; endif; ?></td>
							</tr>
						</table>
					</div>
					<?php else : ?>
				{{$data->prize_name}}

				<?php endif; ?>
			</td>
			<td>{{$data->value}} 元</td>
			<td class="last">{{$data->created_at}}</td>
		</tr>

		<?php
			endforeach;
			?>
		<?php
			for (;$i<8;$i++) :
		?>

		<tr>
			<td class="first">&nbsp;&nbsp;</td>
			<td>&nbsp;&nbsp;</td>
			<td class="last">&nbsp;&nbsp;</td>
		</tr>

		<?php
			endfor;
		?>

	</table>

	<div class="control">
		{{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
	</div>
</div>



<script>
(function($){
	var table = $('#J-table-list'),tickets = table.find('.ticket'),tip = bomao.Tip.getInstance();
	tickets.hover(function(){
		var el = $(this),html = el.parent().find('.ticket-cont').html();
		tip.setText(html);
		tip.show(80, -40, el);
	},function(){
		tip.hide();
	});

})(jQuery);
</script>

</body>
</html>
