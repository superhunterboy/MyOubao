@extends('l.home')

@section('title')
    邮箱管理
@parent
@stop

@section ('main')
<div class="nav-bg">
    <div class="title-normal">邮箱管理</div>
</div>

<div class="content">
    <table width="100%" class="table-field">
        <tr>
            <td align="right"></td>
            <td>
                已经向您的邮箱：tere*******@qq.com发送了一封确认绑定邮件，请前往查看并按提示完成绑定！<br />
                <span class="ui-prompt">（您的激活链接在24小时内有效）</span>
            </td>
        </tr>
        <tr>
            <td align="right"></td>
            <td>
                <input type="button" value="没收到邮件？重新发送" class="btn">
            </td>
        </tr>
    </table>
    <table width="100%" class="table-field">
        <tr>
            <td align="right"></td>
            <td>
                已经向您的邮箱：tere*******@qq.com发送了一封确认绑定邮件，请前往查看并按提示完成绑定！<br />
                <span class="ui-prompt">（您的激活链接在24小时内有效）</span>
            </td>
        </tr>
        <tr>
            <td align="right"></td>
            <td>
                <input type="hidden" id="J-time-num" value="10" />
                <input id="J-button-disabled" type="button" value="59 秒后可重新发送" class="btn btn-disabled">
            </td>
        </tr>
    </table>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    var num = Number($('#J-time-num').val()),
        button = $('#J-button-disabled'),
        timer = setInterval(function(){
            if(num <= 0){
                clearInterval(timer);
                button.val('没收到邮件？重新发送');
                button.removeClass('btn-disabled');
                return;
            }
            num--;
            button.val(num + ' 秒后可重新发送');
        }, 1000);
})(jQuery);
</script>
@stop