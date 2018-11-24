@extends('l.home')

@section('title')
            银行转账--充值
@parent
@stop

@section('scripts')
@parent
    {{ script('tip')}}
    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
@stop

@section ('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal">
                充值
            </div>
            <ul class="tab-title">
                <li class="current"><a href="recharge-netbank.php"><span>银行转账</span></a></li>
                <li><a href="recharge-quick.php"><span>快捷充值</span></a></li>
            </ul>
        </div>

        <div class="content recharge-netbank">
            <div class="prompt">
                平台填写金额必须和银行转账金额一致（不包含手续费），否则充值无法到账。
            </div>

            <form action="recharge-comfirm.php" method="post" id="J-form">
            <table width="100%" class="table-field">
                <tr>
                    <td width="120" align="right" valign="top">选择充值银行：</td>
                    <td>

                                <div class="bank-more-content">
                                    <div class="bank-list" id="J-bank-list">
                                        <label class="img-bank" for="J-bank-name-CMB"><input data-id="1" name="bank[]" id="J-bank-name-CMB" type="radio" checked="checked" /><span class="ico-bank CMB"></span></label>
                                        <label class="img-bank" for="J-bank-name-ICBC"><input data-id="2" name="bank[]" id="J-bank-name-ICBC" type="radio" /><span class="ico-bank ICBC"></span></label>
                                        <label class="img-bank" for="J-bank-name-CIB"><input data-id="3" name="bank[]" id="J-bank-name-CIB" type="radio" /><span class="ico-bank CIB"></span></label>
                                        <label class="img-bank" for="J-bank-name-BOCO"><input disabled="disabled" data-id="4" name="bank[]" id="J-bank-name-BOCO" type="radio" /><span class="ico-bank BOCO"></span><span class="bank-mask"></span><a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a></label>
                                        <label class="img-bank" for="J-bank-name-CCB"><input disabled="disabled" data-id="5" name="bank[]" id="J-bank-name-CCB" type="radio" /><span class="ico-bank CCB"></span><span class="bank-mask"></span><a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a></label>
                                        <label class="img-bank" for="J-bank-name-ABC"><input disabled="disabled" data-id="6" name="bank[]" id="J-bank-name-ABC" type="radio" /><span class="ico-bank ABC"></span><span class="bank-mask"></span><a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a></label>
                                        <label class="img-bank" for="J-bank-name-CITIC"><input disabled="disabled" data-id="7" name="bank[]" id="J-bank-name-CITIC" type="radio" /><span class="ico-bank CITIC"></span><span class="bank-mask"></span><a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a></label>
                                        <label class="img-bank" for="J-bank-name-CMBC"><input disabled="disabled" data-id="8" name="bank[]" id="J-bank-name-CMBC" type="radio" /><span class="ico-bank CMBC"></span><span class="bank-mask"></span><a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a></label>
                                    </div>
                                </div>

                    </td>
                </tr>
                <tr>
                  <td align="right" valign="top">选择付款银行卡：</td>
                  <td>

                    <select id="J-select-search-type" style="display:none;">
                        <option value="" selected="selected">周笔畅 **** **** **** 6585</option>
                        <option value="1">赵本山 **** **** **** 3525</option>
                        <option value="2">李宇春 **** **** **** 3585</option>
                    </select>
                    <br />
                    <span class="tip">汇款方的卡号必须与平台绑定的中信卡一致，否则充值无法到帐。</span>
                  </td>
              </tr>
                <tr>
                  <td align="right" valign="top">充值金额：</td>
                  <td>
                        <input type="text" class="input w-2 input-ico-money" id="J-input-money" />
                        <br />
                        <span class="tip">充值额度限定：最低 <span id="J-money-min">20.00</span>,最高 <span id="J-money-max">30,000.00</span> 元</span>
                  </td>
              </tr>
                <tr>
                  <td align="right" valign="top">资金密码：</td>
                  <td>
                    <input type="password" maxlength="16" class="input w-2 input-ico-lock" id="J-input-password" />
                  </td>
              </tr>
                <tr>
                  <td align="right" valign="top">充值返送说明：</td>
                  <td>
                    <span class="prompt-text" id="J-bank-text">银行相关说明</span>
                  </td>
              </tr>
                <tr>
                  <td align="right" valign="top">&nbsp;</td>
                  <td><input id="J-submit" class="btn" type="submit" value="   下一步   " /></td>
              </tr>
            </table>
        </form>


        </div>
@stop

@section('end')
<script>
(function($){
    var banks = $('#J-bank-list').children(),inputs = banks.find('input'),currentBankId,bankCache = {},loadBankInfoById,buildingView,
        moneyInput = $('#J-input-money'),
        tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
        bankSelect = new bomao.Select({realDom:'#J-select-search-type',cls:'w-5'});

    banks.hover(function(){
        var el = $(this);
        el.addClass('current');
    },function(){
        var el = $(this);
        el.removeClass('current');
    });




    loadBankInfoById = function(id, callback){
        if(bankCache[id]){
            callback(bankCache[id]);
        }else{
            $.ajax({
                url:'../data/bank.php?action=getBankInfoById&id=' + id,
                timeout:30000,
                dataType:'json',
                beforeSend:function(){

                },
                success:function(data){
                    if(Number(data['isSuccess']) == 1){
                        bankCache[data['data']['id']] = data['data'];
                        callback(data['data']);
                    }else{
                        alert(data['msg']);
                    }
                },
                error:function(){
                    alert('网络请求失败，请稍后重试');
                }
            });
        }
    };
    buildingView = function(bankData){
        var list = bankData['userAccountList'],newList = [];
        $.each(list, function(i){
            newList[i] = {value:list[i]['id'], text:list[i]['name'] + ' ' + list[i]['number'], checked: list[i]['isdefault'] ? true : false};
        });
        bankSelect.reBuildSelect(newList);

        $('#J-money-min').text(bomao.util.formatMoney(Number(bankData['min'])));
        $('#J-money-max').text(bomao.util.formatMoney(Number(bankData['max'])));
        $('#J-bank-text').html(bankData['text']);

        $('#J-input-money').val('');
        $('#J-input-password').val('');
    };

    inputs.click(function(){
        var el = $(this),checked = this.checked,id = $.trim(el.attr('data-id'));
        if(checked){
            loadBankInfoById(id, buildingView);
        }
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
        var money = $('#J-input-money'),
            password = $('#J-input-password');
        if($.trim(money.val()) == ''){
            alert('金额不能为空');
            money.focus();
            return false;
        }
        if($.trim(password.val()) == ''){
            alert('资金密码不能为空');
            password.focus();
            return false;
        }
        return true;
    });

})(jQuery);
</script>
@stop