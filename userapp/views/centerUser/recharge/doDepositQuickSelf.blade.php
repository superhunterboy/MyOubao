@extends('l.home')

@section('title')
支付宝充值 - 充值
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
    .tip-hide-text {
        display: none;
    }
</style>
@stop


@section ('main')
<div class="nav-inner nav-bg-tab">
    <div class="title-normal">
        充值
    </div>
</div>

<div class="content page-content-inner recharge-netbank">

    <div class="prompt" style="color:#f00;font-size:15px;text-align: center;">
        <?php if(date('H:i:s') >= '23:30:00' || date('H:i:s') <= '00:30:00')
        echo "鉴于多数银行会在凌晨进行业务结算，转账处理可能会有一定延迟，影响到用户在平台的加币，请耐心等待！<br>"
        ?>
        支付宝转账银行卡成功后，请务必发送附言内容和转账金额给客服，否则游戏币无法正常到账!
    </div>
<?php $oBankcard = PaymentBankCard::getBankcardForDeposit(1);?>
    <table width="100%" class="table-field">
        <tr>
            <td align="right" style="width:480px;"><span class="field-name">充值方式：</span></td>
            <td valign="bottom">
                &nbsp;&nbsp;
                @if($oPayment->identifier == 'SELFZFB')
                <span class="bank-alipay" title="{{ $oPaymentAccount->platform }}" id="J-alipay-ico">
                    @else
                    <span class="bank-weixinpay" title="{{ $oPaymentAccount->platform }}" id="J-weixin-ico">
                        @endif
                    </span>
                    <br>
					<span class="tip f14" style="font-size:15px;">
                    您目前选择的是 <span class="c-red">支付宝</span>
                    向 <span class="c-red">{{ $oBankcard->bank }}</span>充值服务</span>
            </td>
        </tr>
<!--        <tr>
            <td align="right" valign="top"><span class="field-name">收款账号：</span></td>
            <td><span class="field-value-width data-copy">{{ $oPaymentAccount->account}}</span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-postscript"  data-clipboard-text="{{$oPaymentAccount->account}}"/>
            </td>
        </tr>-->
        <tr>
          <td align="right" valign="top">收款账户名：</td>
          <td>
            <span class="field-value-width data-copy" style="padding-left:20px;font-size:15px;">{{ $oBankcard->owner }}</span>
             <input type="button" class="btn btn-small" value="点击复制" id="J-button-name"  data-clipboard-text="{{ $oBankcard->owner }}"/>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">收款账号：</td>
          <td>
                <span class="field-value-width data-copy" style="padding-left:20px;font-size:15px;">
                    {{ $oBankcard->account_no }}
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-card" data-clipboard-text="{{ $oBankcard->account_no }}"/>
          </td>
      </tr>
<!--      @if( isset( $oDeposit->account_no ) )
        <tr>
            <td align="right" valign="top">二维码：</td>
            <td>
                <img src="/assets/images/deposits/{{$oDeposit->account_no}}.png?t=<?php echo rand();?>" alt="">
            </td>
        </tr>
        @endif
-->        <tr>
            <td align="right" valign="top">订单金额：</td>
            <td>
                <span class="field-value-width data-copy" style="padding-left:20px;font-size:15px;">{{$oDeposit->amount}}</span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-money"  data-clipboard-text="{{$oDeposit->amount}}"/>
            </td>
        </tr> 
        <tr>
            <td align="right" valign="top"><span class="field-name">附言：</span></td>
            <td>
                <span class="field-value-width data-copy" style="padding-left:20px;font-size:15px;">
                    <span class="c-red data-copy">{{$oDeposit->postscript}}</span>
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-postscript"  data-clipboard-text="{{$oDeposit->postscript}}"/>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top"></td>
            <td>
                <span class="f12">您也可以复制打开链接：<a class="link-url" href="{{$oPayment->load_url}}">{{$oPayment->load_url}}</a></span>
            </td>
        </tr>
        <tr>
            <td align="right" valign="top">&nbsp;</td>
            <td>
                <a target="_blank" href="{{$oPayment->load_url}}" class="btn">点击充值</a>
            </td>
        </tr>
    </table>
</div>
@stop
<script src="/assets/third/clipboard.js/clipboard.min.js"></script>
@section('end')
@parent
<script>
(function ($) {

    var clipboards = new Clipboard('[data-clipboard-text]');

    clipboards.on('success', function (e) {
        alert('复制成功!');
    });

    clipboards.on('error', function (e) {
        alert('您的浏览器暂不支持，请手动复制!');
    });
    var moneyDom = $('#J-input-money'),
            feeDom = $('#J-bank-sdpay-fee'),
            min = Number($('#J-money-min').text().replace(',', '')),
            max = Number($('#J-money-max').text().replace(',', '')),
            fee = Number($('#J-bank-sdpay-fee-value').val()) / 100;

    moneyDom.keyup(function (e) {
        var v = $.trim(this.value), arr = [], code = e.keyCode;
        if (code == 37 || code == 39) {
            return;
        }
        v = v.replace(/[^\d|^\.]/g, '');
        arr = v.split('.');
        if (arr.length > 2) {
            v = '' + arr[0] + '.' + arr[1];
        }
        arr = v.split('.');
        if (arr.length > 1) {
            arr[1] = arr[1].substring(0, 2);
            v = arr.join('.');
        }
        this.value = v;
    });
    moneyDom.blur(function (e) {
        var v = $.trim(this.value), arr = [], code = e.keyCode;
        if (code == 37 || code == 39) {
            return;
        }
        v = v.replace(/[^\d|^\.]/g, '');
        arr = v.split('.');
        if (arr.length > 2) {
            v = '' + arr[0] + '.' + arr[1];
        }
        arr = v.split('.');
        if (arr.length > 1) {
            arr[1] = arr[1].substring(0, 2);
            v = arr.join('.');
        }
        this.value = v;
    });
    moneyDom.blur(function () {
        var v = Number(this.value), minNum = Number($('#J-money-min').text().replace(/,/g, '')), maxNum = Number($('#J-money-max').text().replace(/,/g, ''));
        v = v < minNum ? minNum : v;
        v = v > maxNum ? maxNum : v;
        this.value = bomao.util.formatMoney(v).replace(/,/g, '');
    });



})(jQuery);




(function ($, host) {
    var tip = host.Tip.getInstance();

    var inputUserName = $('#J-input-alipay-username');
    inputUserName.focus(function () {
        var el = $(this);
        tip.setText($('#J-tip-text-1').html());
        tip.show(el.width() + 140, -30, this);
    });
    inputUserName.blur(function () {
        tip.hide();
    });


    var inputAccount = $('#J-input-alipay-account');
    inputAccount.focus(function () {
        var el = $(this);
        tip.setText($('#J-tip-text-2').html());
        tip.show(el.width() + 140, -50, this);
    });
    inputAccount.blur(function () {
        tip.hide();
    });



})(jQuery, bomao);
</script>

@stop