@extends('l.home')

@section('title')
    微信充值 - 充值
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
    <?php
        $bIsEnableTHK = SysConfig::readValue('is_enable_tonghuika');
        $sActionUrl = $bIsEnableTHK ? route('user-recharges.confirmWeiXin') : route('user-recharges.confirm');
    ?>
    <div class="content page-content-inner recharge-netbank">
      <div class="prompt">
          请在新打开的微信支付页面进行充值操作。<br/>
          如果您的浏览器未弹出新的支付页面，请取消浏览器对弹出页面的阻拦，并选择允许（信任）<br/>
      </div>

        <form action="{{$sActionUrl}}" method="post" id="J-form" target="_blank">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="bank_code" value="WEIXIN" />
            <input type="hidden" name="deposit_mode" value="{{ UserDeposit::DEPOSIT_MODE_THIRD_PART }}" />
            <input data-id="{{ $oBank->id }}" name="bank" value="{{ $oBank->id }}" id="J-bank-name-{{ $oBank->identifier }}" type="hidden" />

            <table width="100%" class="table-field">
                <tr>
                    <td align="right" style="width:480px;"><span class="field-name">充值方式：</span></td>
                    <td valign="bottom">
                        <span class="bank-weixinpay" title="微信支付" id="J-alipay-ico">
                        </span>
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top">充值金额：</td>
                    <td>
                        <input type="text" class="input w-2 input-ico-money" id="J-input-money" name="amount" value="" />
                        <br />
                        <span class="tip">充值额度限定：最低 <span id="J-money-min">{{ $fMinLoad }}</span>元,最高 <span id="J-money-max">{{ $fMaxLoad }}</span>元<br/>
                            请在5分钟内完成转账，单日无次数上限。
                        </span>
                    </td>
                </tr>
                <tr>
                    <td align="right" valign="top">&nbsp;</td>
                    <td><input id="J-submit" class="btn" type="submit" value="   下一步   " />
                </tr>
                @if($bIsEnableTHK)
                <tr>
                     <td colspan="2" align="center"><font color="red" size="4">温馨提示：您即将进入扫码支付页面，支付成功后，窗口将自动关闭，请勿手动关闭窗口，以免影响充值进度。</font></td>
                </tr>
                @endif
            </table>
        </form>
    </div>
@stop

@section('end')
    @parent
    <script>
        (function ($) {
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
    </script>

@stop
