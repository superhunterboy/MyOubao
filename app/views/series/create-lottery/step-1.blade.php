@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')


        {{ Form::open(['id' => 'create-1', 'class' => 'form-horizontal']) }}

    {{ Form::hidden('step',1) }}
<div class="form-group">
        {{ $oFormHelper->makeLabel('series_id', 'Series', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::select('series_id', $aSeries,$iSeriesId,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
        {{ $oFormHelper->makeLabel('frequency', 'Frequency', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::select('frequency', [1=>'High',2=>'Low'],1,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
        {{ $oFormHelper->makeLabel('name', 'Name', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::input('text','name',null,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
        {{ $oFormHelper->makeLabel('identifier', 'Identifier', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::input('text','identifier',null,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
        {{ $oFormHelper->makeLabel('digital_count', 'Digital', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::input('text','digital_count',null,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
        {{ $oFormHelper->makeLabel('issue_format', 'Issue Format', false, 'col-sm-2 control-label') }}
    <div class="col-sm-6">
    {{ Form::input('text','issue_format',null,['class' => 'form-control']) }}
     </div>
    <div class="col-sm-4">
   </div>
</div>

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <a class="btn btn-default" href="javascript:window.history.back()">{{__('Back')}}</a>
    {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>

    {{ Form::close() }}
    
@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
