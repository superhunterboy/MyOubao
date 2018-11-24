@extends('...l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('...w.breadcrumb')
    @include('...w.notification')
    @include('...w._function_title')

@if($data->user_id)
{{ Form::open(['method' => 'PUT', 'class' => 'form-horizontal']) }}
@else
{{ Form::open(['method' => 'POST', 'class' => 'form-horizontal']) }}
@endif
<!--<input type="hidden" name="method" value="PUT" />-->


<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_userbonusaidcycles.username') }}</label>
<div class="col-sm-6 form-inline">
{{Form::text('username',$data->username?$data->username:$oUsers->username,['class'=>'form-control','disabled'=>'disabled', 'style' => 'width:200px']); }}
{{Form::hidden('user_id',$data->user_id?$data->user_id:$oUsers->id,['class'=>'form-control', 'style' => 'width:80px']); }}
{{Form::hidden('username',$data->username?$data->username:$oUsers->username,['class'=>'form-control', 'style' => 'width:80px']); }}
</div>
</div>

<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_userbonusaidcycles.cycles') }}</label>
<div class="col-sm-6 form-inline">
{{Form::text('cycles',$data->cycles,['class'=>'form-control', 'style' => 'width:200px']); }}
</div>
</div>





<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      {{ Form::submit(__('_basic.submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}

@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
