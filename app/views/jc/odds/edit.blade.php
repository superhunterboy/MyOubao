@extends('l.admin', array('active' => $resource))

@section('title')
    @parent
    {{ $sPageTitle }}
@stop

@section('container')
    @include('w.notification')
    @include('w._function_title')
    <div class="tab-content" style="margin-top: 20px;">

        <form class="form-horizontal" method="post" action="" autocomplete="off">
            @foreach($aOddses as $k => $aOdds)
                <div  class="form-group">
                    <label for="method" class="col-sm-3 control-label">{{$k}}</label>
                </div>
                @foreach($aOdds as $oOdds)
                <div class="form-group">
                <label for="{{$oOdds->id}}" class="col-sm-3 control-label">{{$oOdds->name}}</label>
                <div class="col-sm-3">
                <input class="form-control" type="text" name="{{$oOdds->id}}" id="{{$oOdds->id}}" value="{{ Input::old($oOdds->id) ? Input::old($oOdds->id) : $oOdds->odds }}">
                </div>
                <div class="col-sm-3">
                {{ $errors->first($oOdds->id, '<label class="text-danger control-label">:message</label>') }}
                </div>
                </div>
                @endforeach
            @endforeach

            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

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
