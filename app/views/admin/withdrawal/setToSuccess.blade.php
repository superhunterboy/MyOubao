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
  <a href="#" id="copy_all"  style="font-size: 20px;">一键复制</a>
    <div class="panel panel-default">
        <div class=" panel-body">
            <?php 
                $sAccountInfo = $oWithdrawal->account_name.','.$oWithdrawal->account.','.$oWithdrawal->bank.','.$oWithdrawal->amount;
            ?>
            {{ Form::open(array('url' => route('withdrawals.submit-document', ['id' => $oWithdrawal->id]), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal')) }}
            <input type="hidden" name="id" value="{{ $oWithdrawal->id }}" />
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_deposit.username') }}</label>
                <div class="col-sm-5">
                    <span class="form-control"  disabled/>{{ $oWithdrawal->username }}</span>@if(is_object($oRoleUser))<span style="color:red">不良用户</span>@endif

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.account_name') }}</label>
                <div class="col-sm-5">
                    <span class="form-control " data-copy disabled/>{{ $oWithdrawal->account_name }}</span>

                </div>
                <div class="col-sm-3">
                    <input type="button" class="btn btn-default" id="account_name" value="点击复制--{{ __('_withdrawal.account_name') }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label"> {{ __('_withdrawal.account') }}</label>
                <div class="col-sm-5">
                    <span class="form-control " data-copy disabled/>{{ $oWithdrawal->account }}</span>@if($oBankCard->status==BankCard::STATUS_BLACK)<span style="color:red" title='{{$oBankCard->note}}'>不良记录银行卡</span>@endif
                </div>
                <div class="col-sm-3">
                    <input type="button" class="btn  btn-default" id="account" value="点击复制--{{ __('_withdrawal.account') }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.bank') }}</label>
                <div class="col-sm-5">
                    <span class="form-control " data-copy disabled/>{{ $oWithdrawal->bank }}</span>
                </div>
                <div class="col-sm-3">
                    <input type="button" class="btn  btn-default" id="bank" value="点击复制--{{ __('_withdrawal.bank') }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.branch_address') }}</label>
                <div class="col-sm-5">
                    <span class="form-control"  disabled/>{{ $oWithdrawal->branch_address }}</span>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.amount') }}</label>
                <div class="col-sm-5">
                    <span class="form-control " data-copy disabled/>{{ $oWithdrawal->formatted_amount }}</span>
                </div>
                <div class="col-sm-3">
                    <input type="button" class="btn  btn-default" id="amount" value="点击复制--{{ __('_withdrawal.amount') }}" />
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.auditor') }}</label>
                <div class="col-sm-5">
                    <span class="form-control"  disabled/>{{ $oWithdrawal->auditor }}</span>

                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">{{ __('_withdrawal.verified_time') }}</label>
                <div class="col-sm-5">
                    <span class="form-control"  disabled/>{{ $oWithdrawal->verified_time }}</span>
                </div>
            </div>
<!--            <div class="form-group">
                <label class="col-sm-3 control-label" for="transaction_pic">{{ __('_withdrawal.transaction_pic') }}</label>
                <div class="col-sm-5">
                    <input class="form-control" name="transaction_pic" id="transaction_pic" type="file" />
                </div>
            </div>-->
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
{{ script('ZeroClipboard')}}
    @parent
<script>
    $(function(){
        //载入复制
        ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');

        var clip_name = new ZeroClipboard.Client(),
            clip_card = new ZeroClipboard.Client(),
            clip_money = new ZeroClipboard.Client(),
            clip_msg = new ZeroClipboard.Client(),
            clip_all = new ZeroClipboard.Client(),
            fn = function(client){
              var el = $(client.domElement),value = $.trim(el.parent().parent().find('[data-copy]').text());
              client.setText(value);
              alert('复制成功:\n\n' + value);
            },
              copyAll = function(client){
              var value = '{{$sAccountInfo}}';
              client.setText(value);
              alert('复制成功:\n\n' + value);
            };

          clip_name.setCSSEffects( true );
          clip_card.setCSSEffects( true );
          clip_money.setCSSEffects( true );
          clip_msg.setCSSEffects( true );
          clip_all.setCSSEffects( true );

          clip_name.addEventListener( "mouseUp", fn);
          clip_card.addEventListener( "mouseUp", fn);
          clip_money.addEventListener( "mouseUp", fn);
          clip_msg.addEventListener( "mouseUp", fn);
          clip_all.addEventListener( "mouseUp", copyAll);

          clip_name.glue('amount');
          clip_card.glue('bank');
          clip_money.glue('account');
          clip_msg.glue('account_name');
          clip_all.glue('copy_all');
    })
</script>
@stop
