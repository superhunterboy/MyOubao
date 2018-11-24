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

    <div  class="form-group">
        <label for="sdpay_deposit_enable" class="col-sm-3 control-label">收款类型：银联快捷支付</label>
    </div>

    <div class="form-group">
        <label for="sdpay_deposit_enable" class="col-sm-3 control-label">{{ __('_sdpaysetting.sdpay_deposit_enable') }}</label>
        <div class="col-sm-3">
            <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                <input type="checkbox" name="sdpay_deposit_enable" id="sdpay_deposit_enable" value="{{ $sdpay_deposit_enable }}"
                        {{ $sdpay_deposit_enable == 1 ? 'checked': '' }}>
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="deposit_amount_min" class="col-sm-3 control-label">{{ __('_sdpaysetting.deposit_amount_min') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="deposit_amount_min" id="deposit_amount_min" value="{{ Input::old('deposit_amount_min') ? Input::old('deposit_amount_min') : $deposit_amount_min }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('deposit_amount_min', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="single_deposit_amount_max" class="col-sm-3 control-label">{{ __('_sdpaysetting.single_deposit_amount_max') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="single_deposit_amount_max" id="single_deposit_amount_max" value="{{ Input::old('single_deposit_amount_max') ? Input::old('single_deposit_amount_max') : $single_deposit_amount_max }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('single_deposit_amount_max', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="day_deposit_amount_max" class="col-sm-3 control-label">{{ __('_sdpaysetting.day_deposit_amount_max') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="day_deposit_amount_max" id="day_deposit_amount_max" value="{{ Input::old('day_deposit_amount_max') ? Input::old('day_deposit_amount_max') : $day_deposit_amount_max }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('day_deposit_amount_max', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="deposit_fee" class="col-sm-3 control-label">{{ __('_sdpaysetting.deposit_fee') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="deposit_fee" id="deposit_fee" value="{{ Input::old('deposit_fee') ? Input::old('deposit_fee') : $deposit_fee }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('deposit_fee', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="total_deposit_fee" class="col-sm-3 control-label">{{ __('_sdpaysetting.total_deposit_fee') }}</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="total_deposit_fee" id="total_deposit_fee" value="{{ Input::old('total_deposit_fee') ? Input::old('total_deposit_fee') : $total_deposit_fee }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('total_deposit_fee', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="deposit_channel_description" class="col-sm-3 control-label">{{ __('_sdpaysetting.deposit_channel_description') }}</label>
        <div class="col-sm-3">
            <textarea class="form-control" type="text" name="deposit_channel_description" id="deposit_channel_description">{{ Input::old('deposit_channel_description') ? Input::old('deposit_channel_description') : $deposit_channel_description }}</textarea>
        </div>
        <div class="col-sm-3">
            {{ $errors->first('deposit_channel_description', '<label class="text-danger control-label">:message</label>') }}
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
