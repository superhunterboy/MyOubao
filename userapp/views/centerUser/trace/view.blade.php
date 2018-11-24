@extends('l.home')

@section('title')
            追号记录 -- 详情
@parent
@stop


@section ('styles')
@parent
    <style type="text/css">
    .detail-row-cont {
        padding: 10px 0;
        text-align: center;
        
    }
    </style>
@stop





@section ('main')
<div class="nav-inner clearfix">
    <ul class="list">
        <li><a href="{{ route('traces.index') }}">返回列表</a></li>
        <li class="active"><a href="">追号详情</a></li>
    </ul>
    
</div>

<div class="content">
    <div class="area-search" style="background:#FFF;">
        <table width="100%" class="table-detail">
            <tr>
                <td width="400" align="right">游戏：</td>
                <td width="200">{{ $aLotteries[$data->lottery_id] }}</td>
                <td width="120" align="right">追号编号：</td>
                <td>{{ $data->serial_number }}</td>
            </tr>
            <tr>
                <td align="right">玩法：</td>
                <td>{{ $data->title }}</td>
                <td align="right">追号时间：</td>
                <td>{{ $data->bought_at }}</td>
            </tr>
            <tr>
                <td align="right">开始期号：</td>
                <td>{{ $data->start_issue }}</td>
                <td align="right">追号期数：</td>
                <td>{{ $data->total_issues }}期</td>
            </tr>
            <tr>
                <td align="right">完成期数：</td>
                <td>{{ $data->finished_issues }}期</td>
                <td align="right">取消期数：</td>
                <td>{{ $data->canceled_issues }}期</td>
            </tr>
            <tr>
                <td align="right">追号金额：</td>
                <td>{{ $data->amount_formatted }}</td>
                <td align="right">完成金额：</td>
                <td>{{ $data->finished_amount_formatted }}</td>
            </tr>
            <tr>
                <td align="right">取消金额：</td>
                <td>{{ $data->canceled_amount_formatted }}</td>
                <td align="right">中奖金额：</td>
                <td>{{ $data->prize }}</td>
            </tr>
            <tr>
                <td align="right">追号状态：</td>
                <td>{{ $data->formatted_status }}</td>
                <td align="right">模式：</td>
                <td>{{ $aCoefficients[$data->coefficient] }}</td>
            </tr>
            <tr>
                <td align="right">追号奖金组：</td>
                <td>{{ $data->prize_group }}</td>
                <td align="right">中奖后终止任务：</td>
                <td>{{ $data->formatted_stop_on_won }}</td>

            </tr>
        </table>
        <div class="detail-row-cont">
            <div class="title">追号内容</div>
            <textarea disabled="disabled" class="textarea-lotterys-detail input">{{ $data->display_bet_number }}</textarea>
        </div>

        @if ($data->status == Trace::STATUS_RUNNING)
        <div class="detail-row-cont">
            <a class="btn" href="{{ route('traces.stop', $data->id) }}">终止追号</a>
        </div>
        @endif
        <table class="table-info table-toggle" align="center">
            <thead>
                <tr>
                    <th>奖期</th>
                    <th>追号内容</th>
                    <th>追号倍数</th>
                    <th>投注金额</th>
                    <th>追号状态</th>
                    <th>中奖</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($aTraceDetailList as $oTraceDetail)
                <tr>
                    <td>{{ $oTraceDetail->issue }}</td>
                    <td>{{ $data->display_bet_number }}</td>
                    <td>{{ $oTraceDetail->multiple }}倍</td>
                    <td>{{ $oTraceDetail->amount  }}</td>
                    <td>{{ $oTraceDetail->formatted_status }}</td>
                    <td>
                        @if ((int)$oTraceDetail->project_id)
                            @if($oTraceDetail->project->status == Project::STATUS_WON)
                                {{$oTraceDetail->project->prize}}
                            @else
                                {{$oTraceDetail->project->formatted_status}}
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($oTraceDetail->status == TraceDetail::STATUS_WAITING)
                        <a style="text-decoration: underline;" href="{{ route('traces.cancel', [$oTraceDetail->trace_id, $oTraceDetail->id]) }}">取消本期追号</a>
                        @endif
                        @if ((int)$oTraceDetail->project_id)
                        &nbsp;&nbsp;
                        <a href="{{ route('projects.view', $oTraceDetail->project_id) }}">详情</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
    {{ pagination($aTraceDetailList->appends(Input::except('page')), 'w.pages') }}
</div>
@stop

@section('end')
@parent
<script>
(function($){
  // $('#J-button-goback').click(function(e){
  //   history.back(-1);
  //   e.preventDefault();
  // });
})(jQuery);
</script>

@stop