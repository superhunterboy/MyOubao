@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop
@section('container')

<div class="col-md-12">
@include('w._function_title')
@include('w.breadcrumb')
@include('w.notification')

	<div class="panel panel-default">
		<div class=" panel-body">

		{{ Form::open(array('url' => route('deposits.set-wait-verify', ['id' => $oDeposit->id]), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal')) }}
			<input type="hidden" name="id" value="{{ $oDeposit->id }}" />
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.id') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control "  disabled>{{ $oDeposit->id }}</span>

			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.username') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->username }}</span>

			    </div>
			</div>
                                                       @if($oDeposit->deposit_mode=='1')
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.accept_card_num') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->accept_email ? $oDeposit->accept_email : $oDeposit->accept_card_num }}</span>

			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.accept_acc_name') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->accept_acc_name }}</span>
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.postscript') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->postscript }}</span>
			    </div>
			</div>
                                                       @endif
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.created_at') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->created_at }}</span>

			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.accepter') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->accepter }}</span>

			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.accepted_at') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->accepted_at }}</span>
			    </div>
			</div>
                                                       @if($oDeposit->deposit_mode=='1')
			<div class="form-group">
			    <label class="col-sm-3 control-label" for="service_bank_seq_no">{{ __('_deposit.service_bank_seq_no') }}</label>
			    <div class="col-sm-5">
			        <input class="form-control" type="text" id="service_bank_seq_no" name="service_bank_seq_no" />
			    </div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label" for="amount">{{ __('_deposit.amount') }}</label>
			    <div class="col-sm-5">
			        <input class="form-control" type="text" id="amount" name="amount" value="{{ $oDeposit->amount }}" />
			    </div>
			</div>
                                                       @endif
                                                       @if($oDeposit->deposit_mode=='2')
                                                        <div class="form-group">
			    <label class="col-sm-3 control-label">{{ __('_deposit.amount') }}</label>
			    <div class="col-sm-5">
			        <span class="form-control"  disabled>{{ $oDeposit->amount }}</span>
			    </div>
                                                        </div>
                                                       @endif
			<div class="form-group">
			    <div class="col-sm-offset-3 col-sm-5">
			      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
			    </div>
			</div>

		{{ Form::close() }}

		</div>
	</div>
</div>
@stop
@section('end')
@parent
@stop
