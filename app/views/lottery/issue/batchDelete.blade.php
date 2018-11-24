@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')


<!--    <form class="form-horizontal" method="post" action="{{ route($resource.'.create') }}" autocomplete="off">

        <input type="hidden" name="_token" value="{{ csrf_token() }}" />-->

<?php
$oFormHelper->setErrorObject($errors);
$oFormHelper->setModel(new ManIssue);
//pr($aColumnSettings);
//exit;
?>
{{ Form::open(array('url' => route('issues.batch-delete'), 'method' => 'POST', 'class' => 'form-horizontal', 'onsubmit'=>'return confirmDelete();')) }}
{{$oFormHelper->input('lottery_id', $lottery_id, ['id' => 'lottery_id', 'class' => 'form-control', 'options' => ${$aColumnSettings['lottery_id']['options']},'type'=>'select', 'empty' => true]);}}
{{$oFormHelper->input('begin_time',null,['id' => 'begin_time', 'class' => 'form-control','type'=>'date']);}}
{{$oFormHelper->input('end_time',null,['id' => 'end_time', 'class' => 'form-control','type'=>'date']);}}
{{$oFormHelper->input('begin_issue',null,['id' => 'begin_issue', 'class' => 'form-control','type'=>'text']);}}
{{$oFormHelper->input('end_issue',null,['id' => 'end_issue', 'class' => 'form-control','type'=>'text']);}}

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}



<!--        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
              <a class="btn btn-default" href="{{ route($resource.'.create') }}">{{ __('Reset') }}</a>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>-->

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
