@extends('l.home')

@section('title')
	汇款确认--充值
	@parent
@stop

@section('scripts')
	@parent
	{{ script('ZeroClipboard')}}
@stop

@section ('main')
	@if($oUserDeposit->bank_id == 48)
		@include ('centerUser.recharge._weixin_confirm')
	@elseif($iDepositMode == UserDeposit::DEPOSIT_MODE_BANK_CARD)
	    @include ('centerUser.recharge._netbank_confirm')
	@elseif($iDepositMode == UserDeposit::DEPOSIT_MODE_THIRD_PART)
		@include ('centerUser.recharge._3rdpart_confirm')
	@elseif($iDepositMode == UserDeposit::DEPOSIT_MODE_SDPAY)
		@include ('centerUser.recharge._sdpay_confirm')
	@endif

@stop
