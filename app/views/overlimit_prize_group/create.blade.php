@extends('...l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('...w.breadcrumb')
    @include('...w.notification')
    @include('...w._function_title')

<?php

//$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns , array_fill(0,count($aOriginalColumns),null));
//if (!$isEdit && $bTreeable){
//    $data->parent_id = $parent_id;
//}
if (!$isEdit){
    foreach($aInitAttributes as $sColumn => $mValue){
        $data->$sColumn = $mValue;
    }
}
$oFormHelper->setErrorObject($errors);
?>

{{ Form::open(['method' => 'post', 'class' => 'form-horizontal']) }}
@if ($isEdit)
<input type="hidden" name="_method" value="POST" />
@endif


<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_overlimitprizegroup.username') }}</label>
<div class="col-sm-6 form-inline">
{{$oUser->username}}
{{Form::hidden('top_agent_id',$oUser->id,['class'=>'form-control', 'style' => 'width:80px']); }}
{{Form::hidden('top_agent_name',$oUser->username,['class'=>'form-control', 'style' => 'width:80px']); }}
</div>
</div>

<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_overlimitprizegroup.classic_prize_group') }}</label>
<div class="col-sm-6 form-inline">
{{Form::select('classic_prize_group',$aAvilibalePrizeGroups,$data->classic_prize_group,['class'=>'form-control', 'style' => 'width:80px']); }}
</div>
</div>

<div class="form-group">
<label for="from" class="col-sm-2 control-label">*{{__('_overlimitprizegroup.limit_num') }}</label>
<div class="col-sm-6 form-inline">
{{Form::text('limit_num',$data->limit_num,['class'=>'form-control', 'style' => 'width:80px']); }}
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
