@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.notification')
    @include('w._function_title')

    <div class="tab-content" style="margin-top: 20px;">

        <form class="form-horizontal" method="post" action="{{ route($resource.'.settings', isset($iReceiverType) ? $iReceiverType : 1) }}" autocomplete="off">

            <div class="form-group">
                <label for="sdpay_enable" class="col-sm-3 control-label">{{ __('_withdrawalchannelsetting.sdpay_enable') }}</label>
                <div class="col-sm-3">
                    <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                        <input type="checkbox" name="sdpay_enable" id="sdpay_enable" value="{{ $sdpay_enable }}"
                                {{ $sdpay_enable == 1 ? 'checked': '' }}>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="dashpay_enable" class="col-sm-3 control-label">{{ __('_withdrawalchannelsetting.dashpay_enable') }}</label>
                <div class="col-sm-3">
                    <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                        <input type="checkbox" name="dashpay_enable" id="dashpay_enable" value="{{ $dashpay_enable }}"
                                {{ $dashpay_enable == 1 ? 'checked': '' }}>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="sdpay_amount_max" class="col-sm-3 control-label">{{ __('_withdrawalchannelsetting.sdpay_payment_amount_setting') }}</label>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="sdpay_amount_max" id="sdpay_amount_max" value="{{ Input::old('sdpay_amount_max') ? Input::old('sdpay_amount_max') : $sdpay_amount_max }}">
                </div>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="sdpay_sort" id="sdpay_sort" value="{{ Input::old('sdpay_sort') ? Input::old('sdpay_sort') : $sdpay_sort }}">
                </div>
            </div>

            <div class="form-group">
                <label for="dashpay_amount_max" class="col-sm-3 control-label">{{ __('_withdrawalchannelsetting.dashpay_payment_amount_setting') }}</label>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="dashpay_amount_max" id="dashpay_amount_max" value="{{ Input::old('dashpay_amount_max') ? Input::old('dashpay_amount_max') : $dashpay_amount_max }}">
                </div>
                <div class="col-sm-1">
                    <input class="form-control" type="text" name="dashpay_sort" id="dashpay_sort" value="{{ Input::old('dashpay_sort') ? Input::old('dashpay_sort') : $dashpay_sort }}">
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
