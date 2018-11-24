@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.notification')
    @include('w._function_title')
    <div class="tab-content" style="margin-top: 20px;">

    <form class="form-horizontal" method="post" action="{{ route($resource.'.config-settings') }}" autocomplete="off">

  

    <div class="form-group">
        <label for="sdpay_deposit_enable" class="col-sm-3 control-label">抢红包活动开关</label>
        <div class="col-sm-3">
            <div class="switch " data-on-label="{{ __('Yes') }}"  data-off-label="{{ __('No') }}">
                <input type="checkbox" name="active_red_envelopes_status" id="sdpay_deposit_enable" value="{{ $active_red_envelopes_status }}"
                        {{ $active_red_envelopes_status == 1 ? 'checked': '' }}>
            </div>
        </div>
    </div>
        <div class="form-group">
        <label  class="col-sm-3 control-label">活动名称</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="active_red_envelopes_name"  value="{{ Input::old('active_red_envelopes_name') ? Input::old('active_red_envelopes_name') : $active_red_envelopes_name }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('active_red_envelopes_name', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>
    

    <div class="form-group">
        <label for="active_red_envelopes_start_time" class="col-sm-3 control-label">抢红包活动开始时间</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="active_red_envelopes_start_time" id="active_red_envelopes_start_time" value="{{ Input::old('active_red_envelopes_start_time') ? Input::old('active_red_envelopes_start_time') : $active_red_envelopes_start_time }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('active_red_envelopes_start_time', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="active_red_envelopes_end_time" class="col-sm-3 control-label">抢红包活动结束时间</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="active_red_envelopes_end_time" id="active_red_envelopes_end_time" value="{{ Input::old('active_red_envelopes_end_time') ? Input::old('active_red_envelopes_end_time') : $active_red_envelopes_end_time }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('active_red_envelopes_end_time', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="active_red_envelopes_mins" class="col-sm-3 control-label">抢红包活动每期分钟数</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="active_red_envelopes_mins" id="active_red_envelopes_mins" value="{{ Input::old('active_red_envelopes_mins') ? Input::old('active_red_envelopes_mins') : $active_red_envelopes_mins }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('active_red_envelopes_mins', '<label class="text-danger control-label">:message</label>') }}
        </div>
    </div>

    <div class="form-group">
        <label for="active_red_envelopes_amount" class="col-sm-3 control-label">每期活动奖金</label>
        <div class="col-sm-3">
            <input class="form-control" type="text" name="active_red_envelopes_amount" id="deposit_fee" value="{{ Input::old('active_red_envelopes_amount') ? Input::old('active_red_envelopes_amount') : $active_red_envelopes_amount }}">
        </div>
        <div class="col-sm-3">
            {{ $errors->first('active_red_envelopes_amount', '<label class="text-danger control-label">:message</label>') }}
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

    
@stop
