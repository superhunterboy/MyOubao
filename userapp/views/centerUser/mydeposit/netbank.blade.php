@extends('l.home')

@section('title')
    银行转账-充值
@parent
@stop



@section ('main')
<div class="nav-bg nav-bg-tab">
    <div class="title-normal">
        充值
    </div>
    <ul class="tab-title">
        <li class="current"><a href="{{ route('user-recharges.netbank') }}"><span>银行转账</span></a></li>
        <li><a href="{{ route('user-recharges.quick') }}"><span>快捷充值</span></a></li>
    </ul>
</div>

<div class="content recharge-netbank">
    <div class="prompt">
        平台填写金额必须和银行转账金额一致（不包含手续费），否则充值无法到账。
    </div>

    <form action="{{ route('user-recharges.confirm') }}" method="post" id="J-form">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="deposit_mode" value="{{ UserDeposit::DEPOSIT_MODE_BANK_CARD }}" />
        <table width="100%" class="table-field">
            <tr>
                <td width="120" align="right" valign="top">选择充值银行：</td>
                <td>
                    <div class="bank-more-content">
                        <div class="bank-list" id="J-bank-list">
                            @foreach($oAllBanks as $oBank)
                            <label class="img-bank" for="J-bank-name-{{ $oBank->identifier }}">
                                <input data-id="{{ $oBank->id }}" name="bank" value="{{ $oBank->id }}" id="J-bank-name-{{ $oBank->identifier }}" type="radio"
                                @if (!$oBank->is_band_card)
                                       disabled="disabled"/>
                                 <span class="bank-mask"></span>
                                <a href="#" class="bank-tip-text">尚未绑定，请先绑卡</a>
                                @else
                                />
                                @endif
                                <span class="ico-bank {{ $oBank->identifier }}">{{-- $oBank->name --}}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </td>
            </tr>
            @if($bCheckUserBankCard)
            <tr>
                <td align="right" valign="top">选择付款银行卡：</td>
                <td>
                    <select id="J-select-search-type" style="" name="bankcard">
                        <option>请选择充值银行</option>
                    </select>
                    <br />
                    <span class="tip">汇款方的卡号必须与平台绑定的中信卡一致，否则充值无法到帐。</span>
                </td>
            </tr>
            @endif
            <tr>
                <td align="right" valign="top">充值金额：</td>
                <td>
                    <input type="text" class="input w-2 input-ico-money" id="J-input-money" name="amount" /> &nbsp;元
                    <br />
                    <span class="tip" style="display:none;" id="J-money-tip-row">充值额度限定：最低 <span id="J-money-min"></span>,最高 <span id="J-money-max"></span> 元</span>
                </td>
            </tr>
            @if($bCheckFundPassword)
            <tr>
                <td align="right" valign="top">资金密码：</td>
                <td>
                    <input type="password" maxlength="16" class="input w-2 input-ico-lock" id="J-input-password" name="fund_password" />
                </td>
            </tr>
            @endif
            <tr>
                <td align="right" valign="top">充值返送说明：</td>
                <td>
                    <span style="width:180px;" class="prompt-text" id="J-bank-text">银行相关说明</span>
                </td>
            </tr>
            <tr>
                <td align="right" valign="top">&nbsp;</td>
                <td><input style="padding:10px 30px;height:auto;font-size:20px;" id="J-submit" class="btn" type="submit" value="   下一步   " />
            </tr>
        </table>
    </form>
</div>
@stop

@section('end')
@parent
<script>
(function($){
    {{-- 未设置资金密码 --}}
    @if(!$bSetFundPassword)
    var msg = bomao.Message.getInstance();
    msg.show({
        content:"<div style='padding-bottom:10px;font-size:14px;'>使用充值前需设置资金密码，是否现在进行设置？</div>",
        confirmIsShow:true,
        cancelIsShow:true,
        isShowMask:true,
        confirmFun:function(){
            location.href = "{{ route('users.safe-reset-fund-password') }}";
        },
        cancelFun:function(){
            msg.hide();
        }
    });
    /**
    if(confirm("使用充值前需设置资金密码，是否现在进行设置？")) {
        location.href = "{{ route('users.safe-reset-fund-password') }}";
    } else {
        location.href = "/";
    }
    **/
    @endif
    var banks = $('#J-bank-list').children(),inputs = banks.find('input'),currentBankId,
                {{-- 银行及用户银行卡JS数据接口 --}}
                bankCache = {{$sAllBanksJs}},
                loadBankInfoById,buildingView,
        moneyInput = $('#J-input-money'),
        tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
        @if($bCheckUserBankCard)
        bankSelect = new bomao.Select({realDom:'#J-select-search-type',cls:'w-5'});
        @else
        bankSelect = null;
        @endif

    banks.hover(function(){
        var el = $(this);
        el.addClass('current');
    },function(){
        var el = $(this);
        el.removeClass('current');
    });




    loadBankInfoById = function(id, callback){
        var data = bankCache[id];
        callback(data);
    };
    buildingView = function(bankData){
        var list = bankData['userAccountList'],newList = [];
        $.each(list, function(i){
            newList.push({value:list[i]['id'], text:list[i]['name'] + ' ' + list[i]['number'], checked: list[i]['isdefault'] ? true : false});
        });
        bankSelect && bankSelect.reBuildSelect(newList);

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
            $('#J-money-tip-row').show();
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
            password = $('#J-input-password'),
            banks = $('input:radio[name="bank"]:checked').val(),
            bankCard = $('.choose-input-disabled');
        //if没有开启银行卡判断
        @if($bCheckUserBankCard)
        if(bankSelect && ($.trim(bankCard.val()) == ''|| $.trim(bankCard.val()) == '请选择充值银行')){
            alert('请选择充值银行信息');
            bankCard.focus();
            return false;
        }
        @else
        if(banks == undefined || banks == ''){
            alert('请选择充值银行');

            return false;
        }
        @endif

        if($.trim(money.val()) == ''){
            alert('金额不能为空');
            money.focus();
            return false;
        }
        @if($bCheckFundPassword)
        if($.trim(password.val()) == ''){
            alert('资金密码不能为空');
            password.focus();
            return false;
        }
        @endif
        return true;
    });

})(jQuery);
</script>

@stop