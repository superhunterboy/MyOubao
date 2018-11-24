@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')

{{ Form::open(['route' => ($resource.'.batch-deposit'), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) }}
<div class="pic-box">
    <div class="form-group ">
        <label class="col-sm-2 control-label">{{ __('_manualDeposit.excel_file') }}</label>
        <div class="col-sm-6">
            <input name="deposit_file" type="file" class="form-control" style="padding:5px;">
        </div>
    </div>
</div>
<div class="form-group">
<label for="sequence" class="col-sm-2 control-label">批量充值模板</label>
<div class="col-sm-6">
    <a href="/batch_deposit.xls">批量充值模板.xls</a></div>


<div class="col-sm-4">
</div>

</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-9">
        <a class="btn btn-default" href="{{ route($resource.'.batch-deposit') }}">{{ __('Reset') }}</a>
        <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
    </div>
</div>

{{ Form::close() }}

@stop

@section('end')
{{ script('bootstrap-switch') }}
@parent

@stop

