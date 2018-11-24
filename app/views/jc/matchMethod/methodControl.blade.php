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
            @foreach($aMatchMethod as $oaMatchMethod)
            <div class="form-group">
                <label for="is_enable:{{$oaMatchMethod->id}}" class="col-sm-3 control-label">{{ $oaMatchMethod->method }}</label>
                <div class="col-sm-1">
                    <div class="switch" data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                        <input type="checkbox" name="is_enable:{{$oaMatchMethod->id}}" id="is_enable:{{$oaMatchMethod->id}}" value="1"
                                {{ $oaMatchMethod->is_enable == 1 ? 'checked': '' }}>
                    </div>
                </div>
                <label for="is_single:{{$oaMatchMethod->id}}" class="col-sm-1 control-label">单关</label>
                <div class="col-sm-1">
                    <div class="switch" data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                        <input type="checkbox" name="is_single:{{$oaMatchMethod->id}}" id="is_single:{{$oaMatchMethod->id}}" value="1"
                                {{ $oaMatchMethod->is_single == 1 ? 'checked': '' }}>
                    </div>
                </div>
            </div>
            @endforeach

            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-9">
                    {{--<button type="reset" class="btn btn-default">{{ __('Reset') }}</button>--}}
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
