@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.notification')
    @include('w._function_title')
    <div class="tab-content" style="margin-top: 20px;">

    <form class="form-horizontal" method="post" action="{{ route($resource.'.index', isset($iReceiverType) ? $iReceiverType : 1) }}" autocomplete="off">
        <div class="form-group">
            <label for="tonghuika_enable" class="col-sm-3 control-label">是否开启通汇卡</label>
            <div class="col-sm-3">
                <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                    <input type="checkbox" name="tonghuika_enable" id="tonghuika_enable" value="{{ $tonghuika_enable }}"
                            {{ $tonghuika_enable == 1 ? 'checked': '' }}>
                </div>
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
