<div class="nav-bg">
      <div class="title-normal">
          汇款确认
      </div>
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
                <label class="img-bank" for="J-bank-name-{{ $oCollectionBank->identifier }}" style="cursor:default;">
                    <input name="bank[]" id="J-bank-name-{{ $oCollectionBank->identifier }}" type="radio" style="visibility:hidden;" />
                    <span class="ico-bank {{ $oCollectionBank->identifier }}">{{$oCollectionBank->name}}</span>
                </label>
                <br />
                <span class="tip f14">
                    您目前选择的是 <span class="c-red">{{ $oApplyBank->name }}</span>
                    @if($oApplyBank->id != $oCollectionBank->id)
                    跨行汇款到 <span class="c-red">{{ $oCollectionBank->name }}</span>
                    @endif
                    充值服务
                </span>
            </td>
        </tr>
        <tr>
          <td align="right" valign="top">收款账户名：</td>
          <td>
            <span class="field-value-width data-copy">{{ $oUserDeposit->accept_acc_name }}</span>
             <input type="button" class="btn btn-small" value="点击复制" id="J-button-name" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">收款账号：</td>
          <td>
                <span class="field-value-width data-copy">
                    @if($oUserDeposit->mode == 1)
                    {{$oUserDeposit->accept_card_num}}
                    @elseif($oUserDeposit->mode == 2)
                    {{$oUserDeposit->accept_email}}
                    @endif
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-card" />
          </td>
      </tr>
      @if ($oUserDeposit->bank_id != 25)
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
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-money" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">附言(充值订单号)：</td>
          <td>
            <span class="field-value-width">
                <span class="c-red data-copy">{{$oUserDeposit->note}}</span>
            </span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-msg" />
            <span class="ui-text-prompt">({{ $oApplyBank->name }}附言区分大小写，请正确复制)</span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">充值说明：</td>
          <td>
            <div class="prompt-text">
                {{ $oApplyBank->deposit_notice }}
            </div>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top"></td>
          <td>
            <span class="f12">您也可以复制打开链接：<a class="link-url" href="{{$oApplyBank->url}}">{{$oApplyBank->url}}</a></span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">&nbsp;</td>
          <td>

            <a target="_blank" href="{{$oApplyBank->url}}" class="btn">点击充值</a>

          </td>
      </tr>
    </table>
</div>
@stop

@section('end')
@parent
<script>
(function($){
  ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');

  var clip_name = new ZeroClipboard.Client(),
    clip_card = new ZeroClipboard.Client(),
    clip_money = new ZeroClipboard.Client(),
    clip_msg = new ZeroClipboard.Client(),
    table = $('#J-table'),
    fn = function(client){
      var el = $(client.domElement),value = $.trim(el.parent().find('.data-copy').text());
      client.setText(value);
      alert('复制成功:\n\n' + value);
    };

  clip_name.setCSSEffects( true );
  clip_card.setCSSEffects( true );
  clip_money.setCSSEffects( true );
  clip_msg.setCSSEffects( true );

  clip_name.addEventListener( "mouseUp", fn);
  clip_card.addEventListener( "mouseUp", fn);
  clip_money.addEventListener( "mouseUp", fn);
  clip_msg.addEventListener( "mouseUp", fn);

  clip_name.glue('J-button-name');
  clip_card.glue('J-button-card');
  clip_money.glue('J-button-money');
  clip_msg.glue('J-button-msg');


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