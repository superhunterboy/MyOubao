@extends('l.home')

@section('title')
    银联快捷充值 - 充值
@parent
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
    .table-field td {
        padding: 5px 0;
    }
    </style>
@stop


@section ('main')
<div class="nav-inner nav-bg-tab">
    <div class="title-normal">
        充值
    </div>
    @include ('centerUser.recharge_agent._bank_tab')
</div>

<div class="content page-content-inner recharge-netbank">
    <div class="prompt">
        中国银联快捷支付，只需要输入手机号码即可快速到账。单笔充值限额 {{$fMaxLoad}} 元，每日限额 {{$fDayMaxLoad}} 元。
    </div>
    <!--<div class="r-jd">-->
        <!--<div  class="r-gc"></div>-->
    <!--</div>-->

    <form action="{{ route('user-recharges.confirm') }}" method="post" id="J-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="deposit_mode" value="{{ UserDeposit::DEPOSIT_MODE_THIRD_PART }}" />
        <input type="hidden" name="bank" value="47" />

        <table width="100%" class="table-field">

            <tr>
                <td align="right" style="width:480px;"><span class="field-name">充值方式：</span></td>
                <td valign="bottom">
                    <span class="bank-sdpay" title="银联支付">
                    </span>
                    <span class="bank-sdpay-tip">
                        支持19家主流银行，<br />查看 <a href="/help/5%23272" target="_blank">如何使用银联快捷充值?</a>
                    </span>
                </td>
            </tr>
            <tr>
                <td align="right" valign="top">充值金额：</td>
                <td>
                    <input type="text" class="input w-2 input-ico-money" id="J-input-money" name="amount" value="" /> 元
                    &nbsp;&nbsp;
                    <input type="hidden" value="{{$iDepositFee}}" id="J-bank-sdpay-fee-value" />
                    <span class="bank-sdpay-fee-tip">扣除手续费后实际到账 <span class="num" id="J-bank-sdpay-fee"></span> 元</span>
                    <br />
                    <span class="tip" id="J-money-tip-row">充值额度限定：最低 
                        <span id="J-money-min">{{$fMinLoad}}</span>,最高 
                        <span id="J-money-max">{{$fMaxLoad}}</span> 元
                    </span>
                </td>
            </tr>


            <tr>
                <td align="right" valign="top">&nbsp;</td>
                <td><input id="J-submit" class="btn" type="submit" value="   下一步   " />
            </tr>
        </table>
    </form>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    var moneyDom = $('#J-input-money'),
        feeDom = $('#J-bank-sdpay-fee'),
        min = Number($('#J-money-min').text().replace(',', '')),
        max = Number($('#J-money-max').text().replace(',', '')),
        fee = Number($('#J-bank-sdpay-fee-value').val())/100;

    moneyDom.keyup(function(){
        var v = this.value.replace(/[^\d]/g,'');
        if($.trim(v) == ''){
            return;
        }
        v = Number(v);
        feeDom.text(bomao.util.formatMoney(v - fee * v));
        this.value = v;
        
    });
    moneyDom.blur(function(){
        var v = Number(this.value.replace(/[^\d]/g,''));

        v = v < min ? min : v;
        v = v > max ? max : v;

        feeDom.text(bomao.util.formatMoney(v - fee * v));
        this.value = v; 
    });


})(jQuery);
</script>

@stop