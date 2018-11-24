@extends('l.home')

@section('title')
            提现确认
@parent
@stop



@section ('main')
<div class="nav-bg">
            <div class="title-normal">
                提现确认
            </div>
        </div>

        <div class="content recharge-confirm recharge-netbank">
            <form action="?" method="post" id="J-form">
            <table width="100%" class="table-field">
                <tr>
                    <td width="200" align="right" valign="top"><span class="field-name">用户名：</span></td>
                    <td>
                        wahaha
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">可提现金额：</span></td>
                    <td>
                        1200.00 元
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">提现金额：</span></td>
                    <td>
                        1200.00 元
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">开户银行名称：</span></td>
                    <td>
                        中国工商银行
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">开户城市：</span></td>
                    <td>
                        广东 广州
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">开户人姓名：</span></td>
                    <td>
                        张振兴
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">个人银行账号：</span></td>
                    <td>
                        **** **** **** 1448
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top"><span class="field-name">确认资金密码：</span></td>
                    <td>
                        <input type="password" class="input w-2 input-ico-lock" id="J-input-passowrd" />
                    </td>
                </tr>
                <tr>
                  <td align="right" valign="top">&nbsp;</td>
                  <td>
                    <input type="submit" class="btn" value=" 确认提现 " id="J-submit" />
                    </td>
              </tr>
            </table>
        </form>

        </div>
@stop
@section ('end')
<script>
(function($){
    var ipt1 = $('#J-input-passowrd');
    $('#J-submit').click(function(){
        var v1 = $.trim(ipt1.val());
        if(v1 == ''){
            alert('资金密码不能为空');
            ipt1.focus();
            return false;
        }
        return true;
    });
})(jQuery);
</script>
@stop