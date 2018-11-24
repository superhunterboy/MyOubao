@extends('l.home')

@section('title')
    转账
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .field-user-input {
        padding: 0;
        float: left;
    }
    .page-content .row .field-user-input li input {
        padding: 2px 10px;
        font-size: 12px;
    }
    .main-content .content {
        padding-top: 10px;
    }
    .main {padding: 0;margin-top: 0}
    .layout-row {float: left;}
    .page-content .row {
        padding: 20px 0 0 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
        background: #f0f2f3;
    }
    .money-num-index {font-size: 20px;color: #F60;}
    .page-content .row .text-title {
        width: 84px;
        text-align: right;
        padding: 5px 10px 0 0;
    }
    .page-content .field-user-input li {width: auto;}
    .tip {
        font-size: 12px;
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
                <ul class="list">
                    <li><a href="{{ route('user-withdrawals.withdraw')}}">提 款</a></li>
                    <li class="active"><span class="top-bg"></span><a href="{{ route('user-transfers.index')}}">转 账</a></li>
                </ul>
            </div>




            <div class="page-content-inner">

                <div class="tikuan">

                    <form action="{{route('user-transfers.transfer-to-sub')}}" method="post" id="J-form">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
     
                        <div class="a">
                            <span class="a1">转账申请</span>
                            <span class="a2">用户向欧豹平台发起转账申请，将账户资金转入指定的下级账号。</span>
                        </div>
                        
                        <div class="pay-bg">
                            <div class="row clearfix">
                                <label class="text-title">账户余额：</label>
                                <ul class="field field-user-input">
                                    <li>
                                        <span id="acount_money" class="money-num-index">{{ $oAccount->available }}</span> 元
                                    </li>
                                </ul>
                            </div>
                            <div class="row clearfix">
                                <label class="text-title">收款账号：</label>
                                <ul class="field field-user-input">
                                    <li>
                                        <input name="username" value="{{$sUsername}}" id="J-input-username" type="text" value="" class="input w-3" placeholder="收款账号" />
                                        &nbsp;
                                        <span class="tip tip-error"></span>
                                        <span class="tip"><i></i>收款人仅限于您的直属下级</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="row clearfix">
                                <label class="text-title">转账金额：</label>
                                <ul class="field field-user-input">
                                    <li>
                                        <input name="amount" id="J-input-amount" type="text" value="" class="input w-2" placeholder="0.00" />
                                        &nbsp;
                                        元
                                    </li>
                                </ul>
                            </div>
                            <div class="row clearfix">
                                <label class="text-title">资金密码：</label>
                                <ul class="field field-user-input">
                                    <li>
                                        <input name="fund_password" id="J-input-safepassword" type="password" value="" class="input w-4" placeholder="为了您的资金安全请使用虚拟键盘"/>
                                        <span class="key-logo"></span>
                                    </li>
                                </ul>
                            </div>
                            <div class="row clearfix">
                                <label class="text-title">安全验证：</label>
                                <ul class="field field-user-input">
                                    <li>

                                        <select id="J-select-bank" name="card_id">
                                            <option value="0" selected="selected">-- 请选择转帐银行卡 --</option>
                                            @foreach($aBankCards as $oBankCard)
                                                <option value="{{ $oBankCard->id }}">{{ $oBankCard->account_name . ' ' . $oBankCard->account_hidden . ' [' . $oBankCard->bank . ']' }}</option>
                                            @endforeach
                                        </select>

                                     <span class="tip">&nbsp;&nbsp;<i></i>请选择要验证的银行卡，以完成身份核实</span>
                                     
                                     <div style="padding-top:20px;">
                                        <input type="text" id="J-input-cardnumber" name="card_number" class="input w-4" />
                                        <span class="tip">&nbsp;&nbsp;<i></i>请输入完整卡号</span>
                                     </div>
                                    </li>
                                </ul>
                            </div>
                            

                        </div>

                        <div class="row-control" style="padding: 20px 0 50px 95px;text-align:left;">
                            <input id="J-button-submit" class="btn btn-sbumit" type="submit" value=" 确认转账 " />
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){
    var bankSelect = new bomao.Select({realDom:'#J-select-bank', cls:'w-7'});
    var acount_money = Number($('#acount_money').text());
    $('#acount_money').text(bomao.util.formatMoney(acount_money));

    var uname = $('#J-input-username'),
        amount = $('#J-input-amount'),
        spassword = $('#J-input-safepassword'),
        bank = $('#J-select-bank'),
        cardnumber = $('#J-input-cardnumber');

        //每4位数字增加一个空格显示
    function makeBigNumber(str){
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


    uname.blur(function(e){
        var el = $(this),username = $.trim(el.val());
        $.ajax({
            url:'?',
            success:function(data){
                var tips = el.find('.tip');
                if(Number(data['isSuccess']) == 1){
                    tips.show().filter('.tip-error').hide();
                }else{
                    tips.hide().filter('.tip-error').show().text(data['msg']);
                }
            }
        });
    });
    amount.keyup(function(e){
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
    }).blur(function(e){
        var v = Number(this.value.replace(/[^\d|^\.]/g, ''));
        v = v < 0 ? 0 : (v > acount_money ? acount_money : v);
        this.value = bomao.util.formatMoney(v);
    });
    cardnumber.keyup(function(e){
        var v = this.value.replace(/^\s*/g, ''),arr = [],code = e.keyCode;
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
    });
    $('#J-button-submit').click(function(){

        if($.trim(uname.val()) == ""){
            alert('转账用户名不能为空');
            uname.focus();
            return false;
        }
        if($.trim(amount.val()) == ""){
            alert('转账金额不能为空');
            amount.focus();
            return false;
        }
        if($.trim(spassword.val()) == ""){
            alert('安全密码不能为空');
            spassword.focus();
            return false;
        }
        if(cardnumber.size() > 0){
            if($.trim(bank.val()) == ""){
                alert('请选择要验证的银行卡');
                bank.focus();
                return false;
            }
            if($.trim(cardnumber.val()) == ""){
                alert('银行卡卡号不能为空');
                cardnumber.focus();
                return false;
            }
        }
        amount.val(Number(amount.val().replace(',', '')));

        // $(this).attr('disabled', 'disabled');

        return true;
    });
    setTimeout(function(){
        //uname.val('');
        amount.val('');
        spassword.val('');
        cardnumber.val('');
    }, 300);



    //键盘单例
    var keyboard = new bomao.Keyboard({'inputTag':$('.key-logo').prev() , 'isQueue':false});
    keyboard.show(-230,25,$('.key-logo'));
    $('.key-logo').addClass('key-active');

    $('.key-logo').click(function(){
        //获取将要输入的INPUT元素
        keyboard.inputTag = $(this).prev();
        $(this).prev().focus();
        //键盘显示
        if($(this).hasClass('key-active')){
            keyboard.hide();
            $(this).removeClass('key-active');
        }else{
            $('.key-logo').removeClass('key-active');
            $(this).addClass('key-active');
            keyboard.show(-230,25,$(this));
        }
    });




})(jQuery);
</script>
@stop

