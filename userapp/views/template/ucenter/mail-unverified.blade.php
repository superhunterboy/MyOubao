@extends('l.home')

@section('title')
    邮箱管理
@parent
@stop

@section('main')
<div class="nav-bg">
    <div class="title-normal">邮箱管理</div>
</div>

<div class="content">
    <form action="mail-verified.php" id="J-form">
    <table width="100%" class="table-field">
        <tr>
            <td align="right">您的邮箱：</td>
            <td>
                <input type="text" class="input w-4" id="J-input-mail">
                <span class="ui-text-prompt">示例：abc@163.com</span>
            </td>
        </tr>
        <tr>
            <td align="right"></td>
            <td>
                <input type="submit" value="立即验证" class="btn" id="J-submit">
            </td>
        </tr>
    </table>
    </form>
</div>
@stop

@section('end')
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