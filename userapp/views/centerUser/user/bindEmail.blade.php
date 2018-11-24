@extends('l.home')

@section('title')
    邮箱激活
@parent
@stop

@section('main')
<div class="nav-bg">
    <div class="title-normal">邮箱激活</div>
</div>

<?php
$bBinded = $data->isActivated();
$sReadonly = $bBinded ? 'readonly' : '';
$sCmdTitle = $bBinded ? "您已绑定" : "立即验证" ;
$sDisabled = $bBinded ? 'disabled' : '';
?>
<div class="content">
    <form action="{{ route('users.bind-email') }}" method="post" id="J-form">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input type="hidden" name="_method" value="PUT" />
    <table width="100%" class="table-field">
        <tr>
            <td align="right">您的邮箱：</td>
            <td>
                <input type="text" class="input w-4" id="J-input-mail" {{ $sReadonly }} name="email" value="{{ $data->email }}">
                <span class="ui-text-prompt">示例：abc@163.com</span>
            </td>
        </tr>
        <tr>
            <td align="right"></td>
            <td>
                <input type="submit" value="{{ $sCmdTitle }}" {{ $sDisabled }} class="btn" id="J-submit">
            </td>
        </tr>
    </table>
    </form>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    var mail = $('#J-input-mail');

    $('#J-submit').click(function(){
        var mailv = $.trim(mail.val());
        if(!(/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/).test(mailv)){
            alert('邮箱格式填写不正确');
            mail.focus();
            return false;
        }
        return true;
    });


})(jQuery);
</script>
@stop