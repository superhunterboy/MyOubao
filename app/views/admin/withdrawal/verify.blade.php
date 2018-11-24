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

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class=" panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_deposit.username') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control"  disabled/>{{ $oWithdrawal->username }}</span>@if(is_object($oRoleUser))<span style="color:red">不良用户</span>@endif

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.account_name') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control " data-copy disabled/>{{ $oWithdrawal->account_name }}</span>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"> {{ __('_withdrawal.account') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control " data-copy disabled/>{{ $oWithdrawal->account }}</span>@if($oBankCard->status==BankCard::STATUS_BLACK)<span style="color:red" title='{{$oBankCard->note}}'>不良记录银行卡</span>@endif
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.bank') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control " data-copy disabled/>{{ $oWithdrawal->bank }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.branch_address') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control"  disabled/>{{ $oWithdrawal->branch_address }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.amount') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control " data-copy disabled/>{{ $oWithdrawal->formatted_amount }}</span>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.verify_accepter') }}</label>
                    <div class="col-sm-8">
                        <span class="form-control"  disabled/>{{ $oWithdrawal->verify_accepter }}</span>

                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{ __('_withdrawal.verify_accepted_at') }}</label>
                    <div class="col-sm-5">
                        <span class="form-control"  disabled/>{{ $oWithdrawal->verify_accepted_at }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">

            <div class=" panel-body">
                <div class="alert alert-warning" role="alert">拒绝理由：{{$oWithdrawal->error_msg}}</div>
            </div>
            <div class="panel-footer" style=" position:relative;">
                <form action="{{route('withdrawals.refuse', $oWithdrawal->id)}}" method="get" >
                    <div class="form-group">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input type="text" name='error_msg' class="form-control" placeholder="请填写拒绝理由">
                    </div>
                    <button type="submit" class="btn btn-danger">拒绝</button>
                </form>
                <form action="{{route('withdrawals.verify', $oWithdrawal->id)}}" method="post" style="position: absolute;right: 15px;bottom: 15px;">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <input class="btn btn-success" type="submit" value="通过" />
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('end')
{{ script('ZeroClipboard')}}
@parent
@stop
