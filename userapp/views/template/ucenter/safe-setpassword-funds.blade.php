@extends('l.home')

@section('title')
  设置资金密码
@parent
@stop



@section ('main')
<div class="nav-bg">
      <div class="title-normal">
        设置资金密码
      </div>
    </div>

    <div class="content">
      <div class="prompt">
        为了你的账户安全，请先设置资金密码，该密码用于验证您的资金操作。
      </div>
      <form action="?" method="post" id="J-form">
      <table width="100%" class="table-field">
        <tr>
          <td align="right">设置资金密码：</td>
            <td><input type="password" class="input w-3" id="J-input-passowrd" />
          <span class="ui-text-prompt-multiline">由字母和数字组成6-16个字符；且必须包含数字和字母，<br />不允许连续三位相同，不能和登录密码相同。</span>

          </td>
        </tr>
        <tr>
          <td align="right">确认资金密码：</td>
            <td><input type="password" class="input w-3"id="J-input-passowrd2"  /></td>
        </tr>
        <tr>
          <td align="right">&nbsp;</td>
            <td><input class="btn" type="submit" value=" 提 交 " id="J-submit" /></td>
        </tr>
      </table>
      </form>

    </div>
@stop

@section('end')

        <script>
(function($){
  var ipt1 = $('#J-input-passowrd'),
    ipt2 = $('#J-input-passowrd2');

  $('#J-submit').click(function(){
    var v1 = $.trim(ipt1.val()),
      v2 = $.trim(ipt2.val());
    if(v1 == ''){
      alert('资金密码不能为空');
      ipt1.focus();
      return false;
    }
    if(v2 == ''){
      alert('确认资金密码不能为空');
      ipt2.focus();
      return false;
    }
    if(v1 != v2){
      alert('两次输入的资金密码不一致');
      ipt2.focus();
      return false;
    }
    return true;
  });


})(jQuery);
</script>
@stop