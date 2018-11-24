@extends('l.home')

@section('title') 
    添加银行卡
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
        border-top: 0;
    }
    </style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')


            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
            </div>


        
            <div class="page-content-inner page-content-inner-bg">


                <div class="step">
                    <table class="step-table">
                        <tbody>
                        @if (isset($bIsFirst) && $bIsFirst)
                            <tr>
                                <td class="current"><div class="con"><i>1</i>输入银行卡信息</div></td>
                                <td><div class="tri"><div class="con"><i>2</i>确认银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>3</i>绑定结果</div></div></td>
                            </tr>
                        @else
                            <tr>
                                <td class="clicked"><div class="con"><i>1</i>验证老银行卡</div></td>
                                <td class="current"><div class="tri"><div class="con"><i>2</i>输入银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>

                <form action="{{ route('bank-cards.bind-card', 2) }}" method="post" id="J-form" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <!-- <input type="hidden" name="method" value="PUT" /> -->
                    <input type="hidden" name="bank" id="J-input-bank-name" value="{{ Input::old('bank') }}" />
                    <!-- <input type="hidden" name="province" id="J-input-province-name" value="" />
                    <input type="hidden" name="city" id="J-input-city-name" value="" /> -->
                    <table width="100%" class="table-field">
                        <tr>
                            <td width="300" align="right">开户银行：</td>
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
                                <?php
                                    if (Input::old('province_id')) {
                                        $aSelectorData['sSelectedFirst'] = Input::old('province_id');
                                    }
                                    if (Input::old('city_id')) {
                                        $aSelectorData['sSelectedSecond'] = Input::old('city_id');
                                    }
                                ?>
                                @include('widgets.widget', ['aSelectorData' => $aSelectorData])
                            </td>
                        </tr>
                        <tr>
                            <td align="right">支行名称：</td>
                            <td>
                                <input type="text" class="input w-3" id="J-input-bankname" name="branch" value="{{ Input::old('branch') }}">
                                <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">开户人姓名：</td>
                            <td>
                                <input type="text" class="input w-3" id="J-input-name" name="account_name" value="{{ Input::old('account_name') }}">
                                <span class="ui-text-prompt">由1至20个字符或汉字组成，不能使用特殊字符</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">银行账号：</td>
                            <td>
                                <input type="text" class="input w-3" id="J-input-card-number" name="account" value="{{ Input::old('account') }}">
                                <span class="ui-text-prompt">银行卡卡号由16位或19位数字组成</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">确认银行账号：</td>
                            <td>
                                <input type="text" class="input w-3" id="J-input-card-number2" name="account_confirmation" value="{{ Input::old('account_confirmation') }}">
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
        </div>
    </div>

    @include('w.footer')
@stop

@section('end')
@parent
    <script>
    (function($){
        var selectedBank = "{{ Input::old('bank_id') }}";
        $('#J-select-banks').css('display', 'none').val(selectedBank);
        var
            tip             = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
            cardInput       = $('#J-input-card-number, #J-input-card-number2'),
            bankNameInput   = $('#J-input-bank-name'),

            bankSelect      = new bomao.Select({realDom:'#J-select-banks',cls:'w-3'}),
            makeBigNumber;
        bankSelect.addEvent('change', function(e, value, text) {
            bankNameInput.val(text);
        });



        cardInput.keyup(function(e){
            var el = $(this),v = this.value.replace(/^\s*/g, ''),arr = [],code = e.keyCode;
            if(code == 37 || code == 39){
                return;
            }
            v = v.replace(/[^\d|\s]/g, '').replace(/\s{2}/g, ' ');
            this.value = v;
            if(v == ''){
                v = '&nbsp;';
            }else{
                v = makeBigNumber(v);
                v = v.substr(0, 23);
                this.value = v;
            }
            tip.setText(v);
            tip.getDom().css({left:el.offset().left + el.width()/2 - tip.getDom().width()/2});
            if(v != '&nbsp;'){
                tip.show(el.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
            }else{
                tip.hide();
            }
        });
        cardInput.focus(function(){
            var el = $(this),v = $.trim(this.value);
            if(v == ''){
                v = '&nbsp;';
            }else{
                v = makeBigNumber(v);
            }
            tip.setText(v);
            if(v != '&nbsp;'){
                tip.show(el.width()/2 - tip.getDom().width()/2, tip.getDom().height() * -1 - 20, this);
            }else{
                tip.hide();
            }
        });
        cardInput.blur(function(){
            this.value = makeBigNumber(this.value);
            tip.hide();
        });
        cardInput.keydown(function(e){
            if(e.ctrlKey && e.keyCode == 86){
                return false;
            }
        });
        cardInput.bind("contextmenu",function(e){
            return false;
        });
        //每4位数字增加一个空格显示
        makeBigNumber = function(str){
            var str = str.replace(/\s/g, '').split(''),len = str.length,i = 0,newArr = [];
            for(;i < len;i++){
                if(i%4 == 0 && i != 0){
                    newArr.push(' ');
                    newArr.push(str[i]);
                }else{
                    newArr.push(str[i]);
                }
            }
            return newArr.join('');
        };


        $('#J-submit').click(function(){
            var bank = $('#J-select-banks'),
                province = $('#J-select-1'),
                city = $('#J-select-2'),
                bankname = $('#J-input-bankname'),
                name = $('#J-input-name'),
                cardnumber = $('#J-input-card-number'),
                cardnumber2 = $('#J-input-card-number2');

            if(!+$.trim(bank.val())){
                alert('请选择开户银行');
                return false;
            }
            if(!+$.trim(province.val())){
                alert('请选择开户银行省份');
                return false;
            }
            if(!+$.trim(city.val())){
                alert('请选择开户银行城市');
                return false;
            }
            if($.trim(bankname.val()) == ''){
                alert('请填写支行名称');
                bankname.focus();
                return false;
            }
            if($.trim(name.val()) == ''){
                alert('请填写开户人姓名');
                name.focus();
                return false;
            }
            if($.trim(cardnumber.val()) == ''){
                alert('请填写银行账号');
                cardnumber.focus();
                return false;
            }
            if($.trim(cardnumber2.val()) == ''){
                alert('请填写确认银行账号');
                cardnumber2.focus();
                return false;
            }
            if($.trim(cardnumber.val()) != $.trim(cardnumber2.val())){
                alert('两次填写的银行账号不一致');
                cardnumber2.focus();
                return false;
            }

            return true;
        });

    })(jQuery);
    </script>
@stop


