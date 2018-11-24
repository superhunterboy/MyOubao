@extends('l.home')

@section('title')
            汇款确认--充值
@parent
@stop

@section('scripts')
@parent
    {{ script('ZeroCLipboard')}}
    {{ script('mask')}}
    {{ script('message')}}
@stop

@section ('main')
<div class="nav-bg">
    <div class="title-normal">
        汇款确认
    </div>
</div>

<div class="content recharge-confirm">
    <div class="prompt">
        此次充值申请的有效时间为30分钟，为保障充值成功，请在30分钟之内完成充值： 倒计时<span class="c-red" id="J-time-dom">00:00</span>
        <input type="hidden" value="600" id="J-time-second" />
    </div>


    <table width="100%" class="table-field" id="J-table">
        <tr>
            <td width="150" align="right" valign="top">收款银行：</td>
            <td>
                <label class="img-bank" for="J-bank-name-CMB" style="cursor:default;"><input name="bank[]" id="J-bank-name-CMB" type="radio" style="visibility:hidden;" /><span class="ico-bank CMB"></span></label>
                <br />
                <span class="tip f14">
                    您目前选择的是 <span class="c-red">中信银行</span> 跨行汇款到 <span class="c-red">招商银行</span> 充值服务
                </span>
            </td>
        </tr>
        <tr>
          <td align="right" valign="top">收款账户名：</td>
          <td>
            <span class="field-value-width data-copy">
             张振兴
             </span>
             <input type="button" class="btn btn-small" value="点击复制" id="J-button-name" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">收款账号：</td>
          <td>
                <span class="field-value-width data-copy">6225 8820 1946 1448
                </span>
                <input type="button" class="btn btn-small" value="点击复制" id="J-button-card" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">开户城市：</td>
          <td>
            <span class="field-value-width data-copy">广州分行东风支行</span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">订单金额：</td>
          <td>
            <span class="field-value-width data-copy">
            5,475.00
            </span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-money" />
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">附言(充值订单号)：</td>
          <td>
            <span class="field-value-width">
                <span class="c-red data-copy">I9AH聚美优品</span>
            </span>
            <input type="button" class="btn btn-small" value="点击复制" id="J-button-msg" />
            <span class="ui-text-prompt">(中信跨行附言区分大小写，请正确复制)</span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">充值说明：</td>
          <td>
            <div class="prompt-text">
                注意事项：<br />
                1、请点击中信银行页面“转账支付”—“跨行转账”。<br />
                2、在汇款页面中，“收款银行开户行名称”请选择“招商银行股份有限公司”。<br />
                3、请务必复制“充值订单号”到中信银行汇款页面的“摘要”栏中进行粘帖。 (建议采取键盘复制功能 CTRL+V) ，否则充值将无法到账。<br />
                4、充值订单号由系统随机生成，一个订单号只能充值一次，请您在申请到充值信息的15分钟内进行充值操作。超过15分钟或重复使用充值信息将无法到账。<br />
                5、收款账户名和账号会不定期更换，请在获取最新信息后充值，如果充值到旧卡号，可能会造成您的损失。<br />
                6、“订单金额”与网银转账金额不符，充值将无法到账。<br />
                7、充值金额为100元，充值金额小于规定金额，充值将无法到账。
            </div>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top"></td>
          <td>
            <span class="f12">您也可以复制打开链接：<a class="link-url" href="#">https://e.bank.ecitic.com/perbank5/signIn.do</a></span>
          </td>
      </tr>
        <tr>
          <td align="right" valign="top">&nbsp;</td>
          <td>

            <a href="#" class="btn">点击充值</a>

          </td>
      </tr>
    </table>
</div>
@stop

@section('end')
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
    location.href = 'recharge-netbank.php';
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

