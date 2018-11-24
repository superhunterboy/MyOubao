@extends('l.admin')

@section('title')
@parent

@stop

@section('javascripts')
@parent
{{ script('bootstrap-3-switch') }}
@stop

@section('container')

@include('w.breadcrumb')
@include('w.notification')

<div class="row">
    <div class="col-xs-3">
        <div class="h2" style="line-height: 32px;">{{ __($sLangKey,['resource' => $resourceName]) }}</div>
    </div>
    <div class="col-xs-9">
        <div class="form-inline pull-right">

        </div>
    </div>
</div>
<hr>


{{ Form::open(array('method' => 'post', 'class'=>'form form-horizontal', 'action'=>'prize-set-float-rules.update') ) }}
<div  class="form-inline" style="background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;"> 
    <div class="col-xs-5">
        <label for="controller" class="">总开关：</label>
        <div class="form-group">
            <div class="switch ">
                <input type="checkbox" name='float_enabled' @if($floatEnabled==1)checked@endif data-toggle="switch" />
            </div>
        </div>
    </div>
    <div class="col-xs-7 text-right">
        <label style=" padding:2px 10px;"><input name="lottery_series_ssc" type="checkbox" @if(str_contains($floatSeries,'1'))checked@endif/> 时时彩</label>
        <label style=" padding:2px 10px;"><input name="lottery_series_11y" type="checkbox" @if(str_contains($floatSeries,'2'))checked@endif/> 11选5</label>
    </div>
    <div class="clearfix"></div>
</div>



<div class="col-xs-12">
    <table class="table table-hover table-bordered">
        <thead>
            <tr id="j-tr-dome">
                <th>目标奖金组</th>
                <th>周期</th>
                @foreach($aTopAgentPrizeGroups as $val)
                <th>{{$val}}</th>
                @endforeach
                <th>{{ __('_basic.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            <?php $iUp = $iDown = 1; ?>
            @foreach($aLiftRules as $bUp => $aTurnovers)
            @foreach($aTurnovers as $iDays => $aTurnover)
        <input type="hidden" name="liftType[]" value="{{$bUp}}" disabled />
        <td class="j-type">@if($bUp==1)升点条件{{$iUp++}}@elseif($bUp==0)保点条件{{$iDown++}}@endif</td>
        <td class="j-date"><input type="text" name="day[]" class="form-control td-input input-sm" value="{{$iDays}}" readonly/>天 </td>
        @foreach($aTopAgentPrizeGroups as $val)
        <td class="j-f">
            <input type="text" name="turnover{{$val}}[]" class="form-control td-input input-sm" value="{{@$aTurnover[$val]}}" disabled/> 万 
        </td>
        @endforeach
        <td>
            <a href="javascript:void(0);" id="cancle" class="btn btn-embossed  btn-danger j-delete" onclick="modal('{{route('prize-set-float-rules.delete')}}?day={{$iDays}}&is_up={{$bUp}}');">删除</a>
        </td>
        </tr>
        @endforeach
        @endforeach

        </tbody>
    </table>
    <div class="row" style=" margin-top:-10px;">
        <div class="col-xs-3">
            <span id="j-add-btn" class="btn btn-embossed btn-default">增加条件</span>
        </div>
        <div class="col-xs-9 ">
            <div class="pull-right">
                <span id="revise" class="btn btn-embossed btn-danger">修改</span>
                <input type="submit" class="btn btn-embossed btn-success" value="保存设置" />
                <a href="{{route('prize-set-float-rules.index')}}" id="cancle" class="btn btn-embossed btn-default">取消</a>
            </div>
        </div>
    </div>
</div>
{{form::close()}}

<?php
//pr($aLangVars);
//exit;
$modalData['modal'] = array(
    'id' => 'myModal',
    'title' => '系统提示',
    'message' => __('_basic.delete-confirm', $aLangVars) . '？',
    'footer' =>
    Form::open(['id' => 'real-delete', 'method' => 'delete']) . '
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-sm btn-danger">确认删除</button>' .
    Form::close(),
);
?>
@include('w.modal', $modalData)


@stop

@section('end')
{{ script('bootstrap-switch') }}
@parent
<script type="text/javascript">
    $(function () {

        function dHtml(i) {
            return '<td class="j-f">'
                    + '<input type="text" name="turnover' + i + '[]" class="form-control td-input input-sm" value=""/> 万'
                    + '</td>';
        }
        var tdType = '<td class="j-type">'
                + '<select class="form-control select-sm" name="liftType[]">'
                + '<option value="1" selected="selected">升点条件</option>'
                + '<option value="0" selected="selected">保点条件</option>'
                + '</select>'
                + '</td>'
                + '<td class="j-date"><input name="day[]" type="text" class="form-control td-input input-sm" value=""/>天</td>';
        var delBtn = '<td >'
                + '<span class="btn btn-embossed  btn-danger j-delete" onclick="removeDiv(this);">删除</span>'
                + '</td>';
        //html结构
        function html(n) {
            var tHtml = [];
            tHtml.push(tdType);
            for (var i = 2; i < n; i++) {
                tHtml.push(dHtml(1954 + i));
            }
            tHtml.join(',');
            return '<tr class="j-tr warning">' + tHtml + delBtn + '</tr>';
        }
        ;

        $('#j-add-btn').click(function () {
            var thLength = $('#j-tr-dome').find('th').length;
            $('tbody').append(html(thLength - 1));
            $('input').attr("disabled", false);
        });
        $('#revise').click(function () {
            $('input').attr("disabled", false);
        });

    });
    function removeDiv(dome) {
        $(dome).parent().parent('tr.j-tr').remove();
    }

    function modal(href)
    {
        $('#real-delete').attr('action', href);
        $('#myModal').modal();
    }
</script>

@stop
