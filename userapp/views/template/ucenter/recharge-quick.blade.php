@extends('l.home')

@section('title')
            快速充值
@parent
@stop

@section('scripts')
@parent
    {{ script('tip')}}
@stop

@section ('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal">
                充值
            </div>
            <ul class="tab-title">
                <li><a href="recharge-netbank.php"><span>银行转账</span></a></li>
                <li class="current"><a href="recharge-quick.php"><span>快捷充值</span></a></li>
            </ul>
        </div>

        <div class="content recharge-netbank">
            <div class="prompt">
                充值额度为10至300元，给您带来的不便，敬请谅解。
            </div>


            <table width="100%" class="table-field">
                <tr>
                    <td width="120" align="right" valign="top"><span class="field-name">选择充值银行：</span></td>
                    <td>

                                <div class="bank-more-content">
                                    <div class="bank-list">
                                        <label class="img-bank" for="J-bank-name-CMB"><input name="bank[]" id="J-bank-name-CMB" type="radio" /><span class="ico-bank CMB"></span></label>
                                        <label class="img-bank" for="J-bank-name-ICBC"><input name="bank[]" id="J-bank-name-ICBC" type="radio" /><span class="ico-bank ICBC"></span></label>
                                        <label class="img-bank" for="J-bank-name-CIB"><input name="bank[]" id="J-bank-name-CIB" type="radio" /><span class="ico-bank CIB"></span></label>
                                        <label class="img-bank" for="J-bank-name-BOCO"><input name="bank[]" id="J-bank-name-BOCO" type="radio" /><span class="ico-bank BOCO"></span></label>
                                        <label class="img-bank" for="J-bank-name-CCB"><input name="bank[]" id="J-bank-name-CCB" type="radio" /><span class="ico-bank CCB"></span></label>
                                        <label class="img-bank" for="J-bank-name-ABC"><input name="bank[]" id="J-bank-name-ABC" type="radio" /><span class="ico-bank ABC"></span></label>
                                        <label class="img-bank" for="J-bank-name-CITIC"><input name="bank[]" id="J-bank-name-CITIC" type="radio" /><span class="ico-bank CITIC"></span></label>
                                        <label class="img-bank" for="J-bank-name-CMBC"><input name="bank[]" id="J-bank-name-CMBC" type="radio" /><span class="ico-bank CMBC"></span></label>
                                    </div>
                                </div>                  </td>
                </tr>
                <tr>
                  <td align="right" valign="top"><span class="field-name">充值金额：</span></td>
                  <td>
                        <input type="text" class="input w-2 input-ico-money" id="J-input-money" />&nbsp; 元
                        <br />
                        <span class="tip">充值额度限定：最低 <span id="J-money-min">20.00</span>,最高 <span id="J-money-max">30,000.00</span> 元</span>
                 </td>
              </tr>
                <tr>
                  <td align="right" valign="top">&nbsp;</td>
                  <td>
                    <input id="J-submit" class="btn" type="submit" value="立即充值" />
                    </td>
              </tr>
            </table>


        </div>
@stop

@section('end')
@parent
<script>
(function($){
    var moneyInput = $('#J-input-money'),
        tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'});

    moneyInput.keyup(function(e){
        var v = $.trim(this.value),arr = [],code = e.keyCode;
        if(code == 37 || code == 39){
            return;
        }
        v = v.replace(/[^\d|^\.]/g, '');
        arr = v.split('.');
        if(arr.length > 2){
            v = '' + arr[0] + '.' + arr[1];
        }
        arr = v.split('.');
        if(arr.length > 1){
            arr[1] = arr[1].substring(0, 2);
            v = arr.join('.');
        }
        this.value = v;
        v = v == '' ? '&nbsp;' : v;
        tip.setText(v);
        tip.getDom().css({left:moneyInput.offset().left + moneyInput.width()/2 - tip.getDom().width()/2});
    });
    moneyInput.focus(function(){
        var v = $.trim(this.value);
        if(v == ''){
            v = '&nbsp;';
        }
        tip.setText(v);
        tip.show(moneyInput.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
    });
    moneyInput.blur(function(){
        var v = Number(this.value),minNum = Number($('#J-money-min').text().replace(/,/g, '')),maxNum = Number($('#J-money-max').text().replace(/,/g, ''));
        v = v < minNum ? minNum : v;
        v = v > maxNum ? maxNum : v;
        this.value = bomao.util.formatMoney(v).replace(/,/g, '');
        tip.hide();
    });


    $('#J-submit').click(function(){
        var money = $('#J-input-money');
        if($.trim(money.val()) == ''){
            alert('金额不能为空');
            money.focus();
            return false;
        }
        return true;
    });

})(jQuery);
</script>
@stop