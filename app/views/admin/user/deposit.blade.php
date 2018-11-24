@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __(' ') }}
@stop


@section('container')

@include('w.breadcrumb')
@include('w.notification')


<div class="row">
    <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('_user.deposit_title') }}</div>
    </div>
    <div class="col-xs-8">
        <div class="pull-right">

        </div>
    </div>
</div>
<hr>
{{ Form::open(array('method' => 'post', 'class'=>'form form-horizontal', 'id' => 'depositForm') ) }}
<!-- CSRF Token -->
<input type="hidden" name="_token" value="{{ csrf_token() }}" />
<input type="hidden" name="_method" value="PUT" />
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_user.username') }}：</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" name="username" value="{{$oUser->username}}" readonly="true">
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_account.fund') }}：</label>
    <div class="col-sm-5">
        <table class="table table-bordered">
            <tr>
                <th width="100" class="success">{{ __('_account.balance') }}：</th>
                <td class="active">{{$oAccount->balance_formatted}}</td>
                <th width="100" class="success">{{ __('_account.frozen') }}：</th>
                <td class="active">{{$oAccount->frozen_formatted}}</td>
            </tr>
            <tr>
                <th class="success">{{ __('_account.available') }}：</th>
                <td class="active">{{$oAccount->available_formatted}}</td>
                <th class="success">{{ __('_account.withdrawable') }}：</th>
                <td class="active">{{$oAccount->withdrawable_formatted}}</td>
            </tr>
        </table>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_account.deposit_amount') }}（{{__('_account.figure')}}）：</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" id="j-numtochina"  name="amount" value="">
        <h3 class="j-china" style="color:red"></h3>
    </div>

</div>
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_account.transaction_type') }}：</label>
    <div class="col-sm-5">
        <input type="radio" name="transaction_type" checked value="18">人工充值
        <input type="radio" name="transaction_type" value="22">理赔充值
        <input type="radio" name="transaction_type" value="23">促销派奖
        <input type="radio" name="transaction_type" value="20">分红充值
    </div>

</div>
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_transaction.note') }}：</label>
    <div class="col-sm-5">
        <input type="text" class="form-control"  name="note" value="">
    </div>

</div>
<!--<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_transaction.type_id') }}：</label>
    <div class="col-sm-5">
        <label  style=" margin-right:10px; margin-top:3px;"><input name="type_id" type="radio" value="1" checked="true"> 人工充值 </label>
    </div>
</div>-->
<div class="form-group">
    <div class="col-sm-offset-3 col-sm-6">
        <button type="reset" class="btn btn-default">{{ __('Reset') }}</button>
        <button type="submit" class="btn btn-success" >{{ __('Submit') }}</button>
    </div>
</div>
{{Form::close()}}
{{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-sm">Small modal</button> --}}

<div class="modal fade bs-example-modal-sm" id="myModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
  <div class="modal-content">
   <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          <h4 class="modal-title" id="mySmallModalLabel">提示信息：</h4>
    </div>
    <div class="modal-body" id="alertContent">
    </div>
    </div>
  </div>
</div>



@stop
@section('end')
{{ script('bootstrap-switch') }}
{{ script('numtochinese') }}
@parent
<script>
    $(function () {
        $('#j-numtochina').bind('blur keyup', function () {
            var realNum = $(this).val().replace(/[，,]/g, '');
            if (isNaN(realNum)) {
                $('#j-numtochina').val('');
                // alert('充值金额只允许输入数字');
                $('#alertContent').text('充值金额只允许输入数字');
                $('#myModal').modal();
            } else {
                $(this).val(realNum);
                $('.j-china').text(numtochinese($(this).val()));
            }
        });
    })
</script>
@stop