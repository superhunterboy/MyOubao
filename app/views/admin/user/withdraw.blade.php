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
        <div class="h1" style="line-height: 32px;">{{ __('_user.withdraw_title') }}</div>
    </div>
    <div class="col-xs-8">
        <div class="pull-right">

        </div>
    </div>
</div>
<hr>
{{ Form::open(array('method' => 'post', 'class'=>'form form-horizontal') ) }}
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
    <label class="col-sm-3 control-label ">{{ __('_account.withdrawal_amount') }}（{{__('_account.figure')}}）：</label>
    <div class="col-sm-5">
        <input type="text" class="form-control" id="j-numtochina"  name="amount">
        <h3 class="j-china" style="color:red"></h3>
    </div>

</div>
<!--<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_transaction.type_id') }}：</label>
    <div class="col-sm-5">
        <label  style=" margin-right:10px; margin-top:3px;"><input name="type_id" type="radio" value="1" checked="true"> 提取现金 </label>
    </div>
</div>-->
<div class="form-group">
    <label class="col-sm-3 control-label ">{{ __('_transaction.note') }}：</label>
    <div class="col-sm-5">
        <input name="note"  class="form-control"  type="text"> 
    </div>
</div>
<div class="form-group">
    <div class="col-sm-offset-3 col-sm-6">
        <button type="reset" class="btn btn-default">{{ __('Reset') }}</button>
        <button type="submit" class="btn btn-success" >{{ __('Submit') }}</button>
    </div>
</div>
{{Form::close()}}
@stop
@section('end')
{{ script('bootstrap-switch') }}
{{ script('numtochinese') }}
@parent
<script>
    $(function () {

        $('#j-numtochina').keyup(function () {
            $(this).val($(this).val().replace(/[^0-9.]/g, ''));
            $('.j-china').text(numtochinese($(this).val()));
        });
    })
</script>
@stop