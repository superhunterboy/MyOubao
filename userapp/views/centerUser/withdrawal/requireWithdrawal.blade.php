@extends('l.home')

@section('title')
            提现
@parent
@stop








@section ('main')
<div class="nav-bg">
    <div class="title-normal">
        提现
    </div>
</div>

<div class="content recharge-confirm recharge-netbank">
    <div class="prompt">
        每天限提 {{ $iWithdrawLimitNum }} 次，今天您已经成功发起了 <span class="c-red">{{ $iWithdrawalNum }}</span> 次提现申请
    </div>
    <form action="{{ route('user-withdrawals.require-withdrawal', 1) }}" method="post" id="J-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <table width="100%" class="table-field">
            <tr>
                <td width="200" align="right" valign="top"><span class="field-name">用户名：</span></td>
                <td>
                    {{ $sUsername }}
                </td>
            </tr>
            <tr>
                <td align="right" valign="top"><span class="field-name">可提现余额：</span></td>
                <td>
                    <span id="J-money-withdrawable">{{ $oAccount->withdrawable_formatted }}</span> 元
                </td>
            </tr>
            <tr>
                <td align="right" valign="top"><span class="field-name">收款银行卡信息：</span></td>
                <td>
                <select id="J-select-bank" style="display:none;" name="id">
                    <option value="0" selected="selected">-- 请选择收款银行卡 --</option>
                    @foreach($aBankCards as $oBankCard)
                        <option value="{{ $oBankCard->id }}">{{ $oBankCard->account_name . ' ' . $oBankCard->account_hidden . ' [' . $oBankCard->bank . ']' }}</option>
                    @endforeach
                </select>
                </td>
            </tr>
            <tr>
              <td align="right" valign="top"><span class="field-name">提现金额：</span></td>
              <td>
                    <input id="J-input-money" type="text" class="input w-2 input-ico-money" name="amount" />&nbsp; 元
                    <br />
                    <span class="tip">单笔最低提现金额：<span id="J-money-min">100.00</span>元，最高<span id="J-money-max">1,000,000.00</span>元</span>

                </td>
          </tr>
            <tr>
              <td align="right" valign="top">&nbsp;</td>
              <td>
                <input type="submit" class="btn" value=" 下一步 " id="J-submit" />
                </td>
          </tr>
        </table>
    </form>
</div>
@stop

@section ('end')
@parent
<script>
(function($){

    var ipt1 = $('#J-input-money'),
        moneyInput = $('#J-input-money'),
        tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
        bankSelect = new bomao.Select({realDom:'#J-select-bank',cls:'w-5'});

    bankSelect.addEvent('change', function(e, value, text){
        // var id = $.trim(value);
        // if(id == '' || id == '0'){
        //     return;
        // }
        // $.ajax({
        //         url:'../data/bank.php?action=getBankInfoById&id=' + id,
        //         timeout:30000,
        //         dataType:'json',
        //         beforeSend:function(){

        //         },
        //         success:function(data){
        //             if(Number(data['isSuccess']) == 1){
        //                 $('#J-money-min').text(bomao.util.formatMoney(data['data']['min']));
        //                 $('#J-money-max').text(bomao.util.formatMoney(data['data']['max']));
        //             }else{
        //                 alert(data['msg']);
        //             }
        //         },
        //         error:function(){
        //             alert('网络请求失败，请稍后重试');
        //         }
        // });

    });


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
        if(!isNaN(Number(v))){
            v = bomao.util.formatMoney(v);
        }
        tip.setText(v);
        tip.getDom().css({left:moneyInput.offset().left + moneyInput.width()/2 - tip.getDom().width()/2});
    });
    moneyInput.focus(function(){
        var v = $.trim(this.value);
        if(v == ''){
            v = '&nbsp;';
        }
        if(!isNaN(Number(v))){
            v = bomao.util.formatMoney(v);
        }
        tip.setText(v);
        tip.show(moneyInput.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
    });
    moneyInput.blur(function(){
        var v = Number(this.value),minNum = Number($('#J-money-min').text().replace(/,/g, '')),maxNum = Number($('#J-money-max').text().replace(/,/g, '')),withdrawable = Number($('#J-money-withdrawable').text().replace(/,/g, ''));
        v = v < minNum ? minNum : v;
        v = v > maxNum ? maxNum : v;
        v = v > withdrawable ? withdrawable : v;
        this.value = bomao.util.formatMoney(v).replace(/,/g, '');
        tip.hide();
    });




    $('#J-submit').click(function(){
        var v1 = $.trim(ipt1.val());
        if(v1 == ''){
            alert('提款金额不能为空');
            ipt1.focus();
            return false;
        }
        return true;
    });






})(jQuery);
</script>
@stop








