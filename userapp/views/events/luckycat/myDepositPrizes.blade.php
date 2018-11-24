
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>幸运博猫</title>

    @section ('styles')
      {{ style('eventLottery')}}
    @show
    <style>
        body {background:#FFF;}
        .noresult {padding:100px;line-height:30px;text-align:center;font-size:14px;}
        .mini-table {background:#FCF7E7;font-family:microsoft yahei;border-top:2px solid #3B2F49;}
        .mini-table th,.mini-table td {font-size:14px;color:#4a3e58;border:1px solid #C1BAAB;text-align:left;padding:10px;}
        .mini-table th {background:#A69C87;color:#FFF;}
        .mini-table td.first {border-left:0;}
        .mini-table td.last {border-right:0;}

    </style>
    @section('javascripts')
      {{ script('jquery-1.9.1') }}
    @show


</head>

<body>



<div class="mini-list">

    @if(empty($datas))
    <div class="noresult">
        您未参加首充返现活动<br />
        赶紧查看活动页面点击参与～
    </div>
    @elseif($datas['task_id'] == 3)
    <div class="cont">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mini-table">
            <tr>
                <th>首充1次返</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td>充值额度：@if($amount){{$amount}}@else N/a @endif 元</td>
                <td>返现额度：@if($amount){{$amount}}@else N/a @endif 元</td>
            </tr>
            <tr>
                <td>累计投注：@if(isset($backInfos[0])) {{$backInfos[0]['total_turnover']}} @else N/a @endif 元</td>
                <td>目标投注：@if($amount){{$amount*64}}@else N/a @endif 元</td>
            </tr>
            @if($datas['status'] == 1)
            <tr>
                <td>充值到账时间：@if($pay_time) {{$pay_time}}@else N/a @endif</td>
                <td>任务终止时间：@if($pay_time) {{$task->end_time}}@else N/a @endif</td>
            </tr>
            @endif
        </table>
    </div>
    @else
    <div class="cont">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="mini-table">
            <tr>
                <th>首充4次返</th>
                <th>&nbsp;</th>
            </tr>
            <tr>
                <td>充值额度：@if($amount){{$amount}} @else N/a @endif 元</td>
                <td>每周返现额度：@if($amount){{$amount / 4}} @else N/a @endif 元</td>
            </tr>
            @if($datas['status'] == 1)
                @foreach($backInfos as $backInfo)
            <tr>
                <td>统计日期：{{date("m.d", strtotime($backInfo['begin_date']))}} ～ {{date("m.d", strtotime($backInfo['end_date']))}}</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>累计投注：{{$backInfo['total_turnover']}} 元</td>
                <td>目标投注：{{$amount*16}} 元</td>
            </tr>
                @endforeach
            @endif
        </table>
    </div>
    @endif

</div>













</body>
</html>
