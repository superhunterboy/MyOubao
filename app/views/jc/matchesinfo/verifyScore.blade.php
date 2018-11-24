@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.notification')
    @include('w._function_title')
    <div class="tab-content" style="margin-top: 20px;">

    <form class="form-horizontal" method="post" action="{{ route($resource.'.verifyScore', isset($iReceiverType) ? $iReceiverType : 1) }}" autocomplete="off">

    <div  class="form-group">
        <label class="col-sm-3 control-label">审核赛果</label>
    </div>

    <div class="form-group">
        <label for="half_score" class="col-sm-3 control-label">{{ __('_manjcmatchesinfo.half_score') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="half_score" id="half_score" value="{{ Input::old('half_score') ? Input::old('half_score') : $half_score }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('half_score', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="score" class="col-sm-3 control-label">{{ __('_manjcmatchesinfo.score') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="score" id="score" value="{{ Input::old('score') ? Input::old('score') : $score }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('score', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>


        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="id" value="{{$id}}">

    <div class="form-group">
        <div class="col-sm-offset-3 col-sm-9">
            <button type="reset" class="btn btn-default">{{ __('Reset') }}</button>
            <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
        </div>
    </div>
    </form>
    </div>
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
