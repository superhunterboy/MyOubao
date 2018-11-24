@extends('l.home')

@section('title')
            汇款确认--充值
@parent
@stop



@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}

@stop







@section ('main')

	    @include ('centerUser.recharge._sdpay_confirm')

@stop



@section('scripts')
@parent
    {{ script('ZeroClipboard')}}
@stop