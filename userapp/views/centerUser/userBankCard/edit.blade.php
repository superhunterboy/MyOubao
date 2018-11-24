@extends('l.home')

@section('title')
修改银行卡信息
@parent
@stop


@section('main')
<div class="nav-bg">
    <div class="title-normal">
        银行卡绑定
    </div>
</div>

<div class="content">
    <div class="step">
        <table class="step-table">
            <tbody>
                <tr>
                    <td class="clicked"><div class="con"><i>1</i>验证老银行卡</div></td>
                    <td class="current"><div class="tri"><div class="con"><i>2</i>修改银行卡信息</div></div></td>
                    <td><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                    <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <form action="{{ route('bank-cards.modify-card', [2, $iCardId]) }}" method="post" id="J-form" autocomplete="off">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <!-- <input type="hidden" name="method" value="PUT" /> -->
        <input type="hidden" name="bank" id="J-input-bank-name" value="{{ $data->bank }}" />
       <!--  <input type="hidden" name="province" id="J-input-province-name" value="{{-- $data->province --}}" />
        <input type="hidden" name="city" id="J-input-city-name" value="{{-- $data->city --}}" /> -->
        <table width="100%" class="table-field">
            <tr>
                <td align="right">开户银行：</td>
                <td>
                    <select id="J-select-banks" name="bank_id">
                        <option value>请选择开户银行</option>
                        @foreach($aBanks as $key=>$val)
                        <option value="{{$val->id}}" >{{$val->name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td align="right">开户银行区域：</td>
                <td>
                    @include('widgets.widget', ['aSelectorData' => $aSelectorData])
                </td>
            </tr>
            <tr>
                <td align="right">支行名称：</td>
                <td>
                    <input type="text" class="input w-3" id="J-input-bankname" name="branch" value="{{ $data->branch }}">
                    <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                </td>
            </tr>
            <tr>
                <td align="right">开户人姓名：</td>
                <td>
                    <input type="text" class="input w-3" id="J-input-name" name="account_name" value="{{ $data->account_name }}">
                    <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                </td>
            </tr>
            <tr>
                <td align="right">银行账号：</td>
                <td>
                    <input type="text" class="input w-3" id="J-input-card-number" name="account" value="{{ $data->account }}">
                    <span class="ui-text-prompt">银行卡卡号由16位或19位数字组成</span>
                </td>
            </tr>
            <tr>
                <td align="right">确认银行账号：</td>
                <td>
                    <input type="text" class="input w-3" id="J-input-card-number2" name="account_confirmation">
                    <span class="ui-text-prompt">银行账号只能手动输入，不能粘贴</span>
                </td>
            </tr>
            <tr>
                <td align="right"></td>
                <td>
                    <input type="submit" value="下一步" class="btn" id="J-submit">
                    <!-- <a class="btn" href="{{-- route('bank-cards.confirm') --}}">下一步</a> -->

                </td>
            </tr>
        </table>
    </form>

</div>
@stop

@section('end')
@parent
<?php
// $cities = json_encode($aAllCities);
// print("<script language=\"javascript\">var provinceCities = $cities; </script>\n");
//
    // $selectedProvince = $data->province_id;
// $selectedCity     = $data->city_id;
// print("<script language=\"javascript\">var selectedProvince = $selectedProvince; var selectedCity = $selectedCity; </script>\n");
$selectedBank = $data->bank_id;
print("<script language=\"javascript\">var selectedBank = $selectedBank;</script>\n");
?>
<script>
    (function ($) {
        $('#J-select-banks').css('display', 'none').val(selectedBank);
        var tip = new bomao.Tip({cls: 'j-ui-tip-b j-ui-tip-input-floattip'}),
                cardInput = $('#J-input-card-number, #J-input-card-number2'),
                bankNameInput = $('#J-input-bank-name'),
                // provinceNameInput = $('#J-input-province-name'),
                // cityNameInput   = $('#J-input-city-name'),
                bankSelect = new bomao.Select({realDom: '#J-select-banks', cls: 'w-3'}),
                // provinceSelect  = new bomao.Select({realDom:'#J-select-province',cls:'w-3'}),
                // citySelect      = new bomao.Select({realDom:'#J-select-city',cls:'w-3', valueKey: 'id', textKey: 'name'}),
                makeBigNumber;
        bankSelect.addEvent('change', function (e, value, text) {
            bankNameInput.val(text);
        });

        // provinceSelect.addEvent('change', function(e, value, text){
        //     var id = $.trim(value);
        //     if(id == '0'){
        //         citySelect.reBuildSelect([{value:0, text:'请选择城市',checked:true}]);
        //         return;
        //     }
        //     if(provinceCities[id]['children']){
        //         citySelect.reBuildSelect(provinceCities[id]['children']);
        //         provinceNameInput.val(text);
        //     }
        // });
        // citySelect.addEvent('change', function(e, value, text) {
        //     var id = $.trim(value);
        //     if (id != 0)
        //         cityNameInput.val(text);
        // });


        cardInput.keyup(function (e) {
            var el = $(this), v = this.value.replace(/^\s*/g, ''), arr = [], code = e.keyCode;
            if (code == 37 || code == 39) {
                return;
            }
            v = v.replace(/[^\d|\s]/g, '').replace(/\s{2}/g, ' ');
            this.value = v;
            if (v == '') {
                v = '&nbsp;';
            } else {
                v = makeBigNumber(v);
                this.value = v;
            }
            tip.setText(v);
            tip.getDom().css({left: el.offset().left + el.width() / 2 - tip.getDom().width() / 2});
            if (v != '&nbsp;') {
                tip.show(el.width() / 2 - tip.getDom().width() / 2, tip.getDom().height() * -1 - 20, this);
            } else {
                tip.hide();
            }
        });
        cardInput.focus(function () {
            var el = $(this), v = $.trim(this.value);
            if (v == '') {
                v = '&nbsp;';
            } else {
                v = makeBigNumber(v);
            }
            tip.setText(v);
            if (v != '&nbsp;') {
                tip.show(el.width() / 2 - tip.getDom().width() / 2, tip.getDom().height() * -1 - 20, this);
            } else {
                tip.hide();
            }
        });
        cardInput.blur(function () {
            this.value = makeBigNumber(this.value);
            tip.hide();
        });
        cardInput.keydown(function (e) {
            if (e.ctrlKey && e.keyCode == 86) {
                return false;
            }
        });
        cardInput.bind("contextmenu", function (e) {
            return false;
        });
        //每4位数字增加一个空格显示
        makeBigNumber = function (str) {
            var str = str.replace(/\s/g, '').split(''), len = str.length, i = 0, newArr = [];
            for (; i < len; i++) {
                if (i % 4 == 0 && i != 0) {
                    newArr.push(' ');
                    newArr.push(str[i]);
                } else {
                    newArr.push(str[i]);
                }
            }
            return newArr.join('');
        };


        $('#J-submit').click(function () {
            var bank = $('#J-select-banks'),
                    province = $('#J-select-province'),
                    city = $('#J-select-city'),
                    bankname = $('#J-input-bankname'),
                    name = $('#J-input-name'),
                    cardnumber = $('#J-input-card-number'),
                    cardnumber2 = $('#J-input-card-number2');

            if ($.trim(bank.val()) == '') {
                alert('请选择开户银行');
                return false;
            }
            if ($.trim(province.val()) == '0') {
                alert('请选择开户银行省份');
                return false;
            }
            // if($.trim(city.val()) == '0'){
            //     alert('请选择开户银行城市');
            //     return false;
            // }
            if ($.trim(bankname.val()) == '') {
                alert('请填写支行名称');
                bankname.focus();
                return false;
            }
            if ($.trim(name.val()) == '') {
                alert('请填写开户人姓名');
                name.focus();
                return false;
            }
            if ($.trim(cardnumber.val()) == '') {
                alert('请填写银行账号');
                cardnumber.focus();
                return false;
            }
            if ($.trim(cardnumber2.val()) == '') {
                alert('请填写确认银行账号');
                cardnumber2.focus();
                return false;
            }
            if ($.trim(cardnumber.val()) != $.trim(cardnumber2.val())) {
                alert('两次填写的银行账号不一致');
                cardnumber2.focus();
                return false;
            }

            return true;
        });
        // setTimeout(function() {
        //     $('#J-select-province').val(selectedProvince);
        //     $('#J-select-city').val(selectedCity);
        // }, 1000);
        var objBankAccount = $('#J-input-card-number');
        if (objBankAccount.val() != '') {
            objBankAccount.val(makeBigNumber(objBankAccount.val()));
        }
    })(jQuery);
</script>
@stop