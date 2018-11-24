@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')


<?php
$oFormHelper->setErrorObject($errors);
$oFormHelper->setModel(new ManIssue);
//pr($aColumnSettings);
//exit;
?>
{{ Form::open(array('url' => route('active-red-envelopes.batch-delete'), 'method' => 'POST', 'class' => 'form-horizontal', 'onsubmit'=>'return confirmDelete();')) }}

{{$oFormHelper->input('begin_id',null,['id' => 'begin_id', 'class' => 'form-control','type'=>'text']);}}
{{$oFormHelper->input('end_id',null,['id' => 'end_id', 'class' => 'form-control','type'=>'text']);}}

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}




@stop
@section('end')
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
{{ script('bootstrap-switch') }}
<script>
    function confirmDelete() {
        if (confirm("确认要删除？")) {
            return true;
        } else {
            return false;
        }
    }
    $(function () {
        //时间控件
        $('.form_date').datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            showMeridian: 1,
            pickerPosition: 'bottom-left'
        });
    });

</script>
@parent

@stop
