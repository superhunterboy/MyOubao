@extends('l.home')

@section('title') 
    提现确认
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .main-content .content {
        padding-top: 10px;
    }
    .main {padding: 0;margin-top: 0}
    .layout-row {float: left;}
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
        background: #FFF;
    }
    .table-field {
        margin-top: 0;
    }
    </style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_main clearfix">
            
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                <ul class="list">
                    <li class="active"><span class="top-bg"></span><a href="{{ route('user-withdrawals.withdraw')}}">提 款</a></li>
                    <li><a href="{{ route('user-transfers.index')}}">转 账</a></li>
                </ul>
            </div>



            <div class="page-content-inner"> 



            <form action="{{ route('user-withdrawals.withdraw', 1) }}" method="post" id="J-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="step" value="2" />
                <table width="100%" class="table-field">
                    <tr>
                        <td width="400" align="right" valign="top"><span class="field-name">用户名：</span></td>
                        <td>
                            <input type="hidden" name="id" value="{{ $oBankCard->id }}">
                            {{ $oBankCard->username }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">可提现金额：</span></td>
                        <td>
                             <input type="hidden" name="withdrawable" value="{{ $oAccount->withdrawable }}">
                            {{ $oAccount->withdrawable_formatted }} 元
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">提现金额：</span></td>
                        <td>
                            <input type="hidden" name="amount" value="{{ $aInputData['amount'] }}">
                            {{ number_format($aInputData['amount'],2) }} 元
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">开户银行名称：</span></td>
                        <td>
                            {{ $oBankCard->bank }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">开户城市：</span></td>
                        <td>
                            {{ $oBankCard->province . '  ' . $oBankCard->city }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">开户人姓名：</span></td>
                        <td>
                            {{ $oBankCard->account_name }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">个人银行账号：</span></td>
                        <td>
                            {{ $oBankCard->account_hidden }}
                        </td>
                    </tr>
                    <tr>
                        <td align="right" valign="top"><span class="field-name">确认资金密码：</span></td>
                        <td>
                            <input type="password" class="input w-4 input-ico-lock" id="J-input-passowrd" name="fund_password" placeholder="为了您的资金安全请使用虚拟键盘"/>
                            <span class="key-logo"></span>
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
        </div>
    </div>


    @include('w.footer')
@stop



@section('end')
@parent
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


    //键盘单例
    var keyboard = new bomao.Keyboard({'inputTag':$('.key-logo').prev() , 'isQueue':false});
    keyboard.show(35,-50,$('.key-logo'));
    $('.key-logo').addClass('key-active');

    $('.key-logo').click(function(){
        //获取将要输入的INPUT元素
        keyboard.inputTag = $(this).prev();
        $(this).prev().focus();
        //键盘显示
        if($(this).hasClass('key-active')){
            keyboard.hide();
            $(this).removeClass('key-active');
        }else{
            $('.key-logo').removeClass('key-active');
            $(this).addClass('key-active');
            keyboard.show(35,-50,$(this));
        }
    });

})(jQuery);
</script>
@stop


