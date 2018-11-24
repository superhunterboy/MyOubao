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
$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns , array_fill(0,count($aOriginalColumns),null));
//if (!$isEdit && $bTreeable){
//    $data->parent_id = $parent_id;
//}
if (!$isEdit){
    foreach($aInitAttributes as $sColumn => $mValue){
        $data->$sColumn = $mValue;
    }
}
//pr($aInitAttributes);
//pr($functionality_id);
// pr($aColumnSettings);
// exit;
$oFormHelper->setErrorObject($errors);
// $sMethod = !$isEdit ? 'POST' : 'PUT';
//pr($aColumnSettings);
//exit;
?>

{{ Form::open(['method' => 'post', 'class' => 'form-horizontal']) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
@endif

    {{ $oFormHelper->input('series_id', null, ['id' => 'series_id', 'class' => 'form-control', 'options' => $aSeries, 'empty' => true]); }}
  
<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_prizegroup.area') }}</label>
<div class="col-sm-6 form-inline">
    {{ Form::text('from',null,['class'=>'form-control', 'style' => 'width:80px']); }}
    <label for="to" class="control-label">{{ __('_basic.to') }}</label>
    {{ Form::text('to',null,['class'=>'form-control', 'style' => 'width:80px']); }}
</div>
</div>

<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_prizegroup.normalize') }}</label>
<div class="col-sm-6">
    <label  class="control-label text-success">{{ __('_prizegroup.round') }}</label>
</div>
</div>
<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_basic.step') }}</label>
    <div class="col-sm-6">
       <label  class="control-label text-success">1 {{ __('_basic.yuan') }}</label>
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <!--<a class="btn btn-default" href="{{ route($resource.'.edit', $data->id) }}">{{ __('Reset') }}</a>-->
      {{ Form::reset(__('_basic.reset'), ['class' => 'btn btn-default']) }}
      {{ Form::submit(__('_basic.submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}

@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
