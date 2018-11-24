@extends('l.home')

@section('title')
银行卡充值
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
</style>
@stop


@section ('main')
<div class="nav-inner nav-bg-tab">
    <div class="title-normal">
        汇款确认
    </div>
    @include ('centerUser.recharge._bank_tab')
</div>
<div class="content recharge-confirm">
    <div class="prompt">
        <?php $iDepositValidTime = Sysconfig::readValue('deposit_valid_time');
            if(!is_numeric($iDepositValidTime)){
                $iDepositValidTime = 30;
            }
        ?>
        此次充值申请的有效时间为{{$iDepositValidTime}}分钟，为保障充值成功，请在{{$iDepositValidTime}}分钟之内完成充值。返回首页：倒计时<span class="c-red" id="J-time-dom">00:00</span>
        <input type="hidden" value="{{$iDepositValidTime*60}}" id="J-time-second" />
    </div>


    <table width="100%" class="table-field" id="J-table">
        <tr>
            <td width="150" align="right" valign="top">收款银行：</td>
            <td>
                <label class="img-bank" for="J-bank-name-{{ $oBank->identifier }}" style="cursor:default;">
                    <input name="bank[]" id="J-bank-name-{{ $oBank->identifier }}" type="radio" style="visibility:hidden;" />
                    <span class="ico-bank {{ $oBank->identifier }}"></span>
                </label>
                <br />
                <span class="tip f14">
                    您目前选择的是 <span class="c-red">{{ $oBank->name }}</span>
                    @if($oBank->id == $oBankcard->bank_Id)
                    充值服务
                    @else
                    向 <span class="c-red">{{ $oBankcard->bank }}</span>充值服务
                    @endif
                </span>
            </td>
        </tr>
        <tr>
          <td align="right" valign="top">收款账户名：</td>
          <td>
            <span class="field-value-width data-copy">{{ $oUserDeposit->accept_acc_name }}</span>
             <input type="button" class="btn btn-small" value="点击复制" id="J-button-name"  data-clipboard-text="{{ $oUserDeposit->accept_acc_name }}"/>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">收款账号：</td>
          <td>
                <span class="field-value-width data-copy">
                    {{ $oUserDeposit->accept_email ? $oUserDeposit->accept_email : $oUserDeposit->accept_card_num }}
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-card" data-clipboard-text="{{ $oUserDeposit->accept_email ? $oUserDeposit->accept_email : $oUserDeposit->accept_card_num }}"/>
          </td>
      </tr>
      @if ($oUserDeposit->bank_id != 25&& $oUserDeposit->bank_id != 44)
        <tr>
          <td align="right" valign="top">开户城市：</td>
          <td>
            <span class="field-value-width data-copy">{{$oUserDeposit->accept_bank_address}}</span>
          </td>
      </tr>
      @endif
        <tr>
          <td align="right" valign="top">订单金额：</td>
          <td>
            <span class="field-value-width data-copy">{{$oUserDeposit->amount}}</span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-money"  data-clipboard-text="{{ $oUserDeposit->amount }}"/>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">附言(充值订单号)：</td>
          <td>
            <span class="field-value-width">
                <span class="c-red data-copy">{{$oUserDeposit->postscript}}</span>
            </span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-msg"  data-clipboard-text="{{$oUserDeposit->postscript}}"/>
            <span class="ui-text-prompt">({{ $oBank ->name }}附言区分大小写，请正确复制)</span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">充值说明：</td>
          <td>
            <div class="prompt-text">
                {{ $oBank->deposit_notice }}
            </div>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top"></td>
          <td>
            <span class="f12">您也可以复制打开链接：<a class="link-url" href="{{$oBank->url}}">{{$oBank->url}}</a></span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">&nbsp;</td>
          <td>

            <a target="_blank" href="{{$oBank->url}}" class="btn">点击充值</a>

          </td>
      </tr>
    </table>
</div>
@stop
<script src="/assets/third/clipboard.js/clipboard.min.js"></script>
@section('end')
@parent
<script>
(function($){
//  ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');
//
//  var clip_name = new ZeroClipboard.Client(),
//    clip_card = new ZeroClipboard.Client(),
//    clip_money = new ZeroClipboard.Client(),
//    clip_msg = new ZeroClipboard.Client(),
//    table = $('#J-table'),
//    fn = function(client){
//      var el = $(client.domElement),value = $.trim(el.parent().find('.data-copy').text());
//      client.setText(value);
//      alert('复制成功:\n\n' + value);
//    };
//
//  clip_name.setCSSEffects( true );
//  clip_card.setCSSEffects( true );
//  clip_money.setCSSEffects( true );
//  clip_msg.setCSSEffects( true );
//
//  clip_name.addEventListener( "mouseUp", fn);
//  clip_card.addEventListener( "mouseUp", fn);
//  clip_money.addEventListener( "mouseUp", fn);
//  clip_msg.addEventListener( "mouseUp", fn);
//
//  clip_name.glue('J-button-name');
//  clip_card.glue('J-button-card');
//  clip_money.glue('J-button-money');
//  clip_msg.glue('J-button-msg');
  var clipboards = new Clipboard('[data-clipboard-text]');

  clipboards.on('success', function(e) {
    alert('复制成功!');
  });

  clipboards.on('error', function(e) {
    alert('您的浏览器暂不支持，请手动复制!');
  });

  var timeDom = $('#J-time-dom'),
    timeNum = Number($('#J-time-second').val()),
    timer = setInterval(function(){
      var m = Math.floor(timeNum/60),
        s = timeNum%60;
      m = m < 10 ? '0' + m : m;
      s = s < 10 ? '0' + s : s;
      timeDom.text(m + ':' + s);
      timeNum--;
      if(timeNum < 0){
        clearInterval(timer);
        showTimeout();
      }
    }, 1000);



  var showTimeout = function(){
    location.href = '/';
    /**
    var win = bomao.Message.getInstance();
    win.show({
      content:'<div class="pop-title"><i class="ico-waring"></i><h4 class="pop-text">该订单已失效，请重新发起</h4></div>',
      confirmIsShow:true,
      confirmFun:function(){
        this.hide();
      },
      closeIsShow:true,
      closeFun:function(){
        this.hide();
      },
      mask:true
    });
    **/
  };


})(jQuery);
</script>
@stop
