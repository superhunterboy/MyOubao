@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')

{{ Form::model($datas, ['method' => 'post', 'class' => 'form-horizontal']) }}

<input type="hidden" name="_method" value="PUT" />

<div class="panel panel-default">
    <div class="panel-body">
        
            <label for=" " class="control-label " style="text-align: center; width:120px; float:left;"> 启用通道</label>
        @foreach ($datas as $sColumn => $sValue)
        <div class="form-group">
            <label for=" " class="control-label " style="text-align: center; width:120px; float:left;"> {{$sValue->channel}}</label>
            <div style=" width:80px; float:left;">
                {{Form::checkbox("data[$sValue->id][status]", '1', $sValue->status==1?'true':'');}}
            </div>
        </div>
        @endforeach
    </div>

</div>
</div>


<div class="form-group">
    <label for=" " class="control-label " style="text-align: center; width:120px; float:left;">金额上限&排序</label>
    @foreach ($datas as $sColumn => $sValue)
    <div style=" width:80px; float:left;">
        {{ Form::input('text','upper_limit_amount',$sValue->upper_limit_amount,['class' => 'form-control'])}}
    </div>
</div>
@endforeach




<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        <a class="btn btn-default" href="{{ route($resource. '.index') }}">{{ __('Reset') }}</a>
        {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}




@stop

@section('end')
{{ script('bootstrap-switch') }}
@parent

<script>
    function modal(href)
    {
        $('#real-delete').attr('action', href);
        $('#myModal').modal();
    }
</script>
@stop