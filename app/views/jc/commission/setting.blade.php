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

            <div class="form-group">
                <label for="single_rate" class="col-sm-3 control-label">{{ __('jc/_jccommissionsetting.single_rate') }}</label>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="single_rate" id="single_rate" value="{{ Input::old('single_rate') ? Input::old('single_rate') : $single_rate }}">
                </div>
            </div>

            <div class="form-group">
                <label for="multiple_rate" class="col-sm-3 control-label">{{ __('jc/_jccommissionsetting.multiple_rate') }}</label>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="multiple_rate" id="multiple_rate" value="{{ Input::old('multiple_rate') ? Input::old('multiple_rate') : $multiple_rate }}">
                </div>
            </div>

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
