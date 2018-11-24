@extends('l.home')

@section('title') 
    验证老银行卡
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
                            <tr>
                                <td class="current"><div class="con"><i>1</i>验证老银行卡</div></td>
                                <td><div class="tri"><div class="con"><i>2</i>输入银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                                <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php (isset($iCardId) && $iCardId) ? $url = route('bank-cards.modify-card', [0, $iCardId]) : $url = route('bank-cards.bind-card', 0); ?>
                <form action="{{ $url }}" method="post" id="J-form" autocomplete="off">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <table width="100%" class="table-field" style="margin-bottom:60px;">
                        @if(isset($iCardId) && $iCardId)
                        <tr>
                            <td align="right" width="460">卡号：</td>
                            <td>
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                {{ $data->account_hidden }}
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td align="right" width="330">选择验证银行卡：</td>
                            <td>
                                <select id="J-select-bank-card" style="display:none;" name="id">
                                    <option value="">请选择你要验证的银行卡</option>
                                    @foreach ($aBindedCards as $key => $oCard)
                                        <option value="{{ $oCard->id }}" {{ $oCard->id == Input::get('id') ? 'selected' : '' }}>{{ $oCard->account_hidden }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td align="right">开户人姓名：</td>
                            <td>
                                <input type="text" class="input w-4" id="J-input-name" name="account_name" value="{{ Input::get('account_name') }}">
                                <span class="ui-text-prompt">请输入旧的银行卡开户人姓名</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">银行账号：</td>
                            <td>
                                <input type="hidden" name="account" value="{{ Input::get('account') }}">
                                <input type="text" class="input w-4" id="J-input-card-number" value="{{ Input::get('account') }}">
                                <span class="ui-text-prompt">请输入旧的银行卡卡号</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">资金密码：</td>
                            <td>
                                <input type="password" class="input w-4" id="J-input-password" name="fund_password"  placeholder="为了您的资金安全请使用虚拟键盘">
                                <span class="key-logo"></span>
                                <span class="ui-text-prompt">请输入您的资金密码</span>
                            </td>
                        </tr>
                        <tr>
                            <td align="right"></td>
                            <td>
                                <a class="btn btn-normal" href="{{ route('bank-cards.index') }}">返回</a>
                                <input type="submit" value="下一步" class="btn" id="J-submit">
                                <!-- <a class="btn" href="{{-- route('bank-cards.create') --}}">下一步</a> -->
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
        var tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
            cardInput = $('#J-input-card-number'),
            makeBigNumber;
        if ($('#J-select-bank-card').length)
            new bomao.Select({realDom:'#J-select-bank-card',cls:'w-4'});

        cardInput.keyup(function(e){
            $('input[name=account]').val(this.value.replace(/\s+/g,''));
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
            var bankCard = $('#J-select-bank-card'),
                name = $('#J-input-name'),
                password = $('#J-input-password');
            if ($('#J-select-bank-card').length) {
                if(!$.trim(bankCard.val())){
                    alert('请选择需要进行验证的银行卡');
                    return false;
                }
            }

            if($.trim(name.val()) == ''){
                alert('请填写开户人姓名');
                name.focus();
                return false;
            }
            if($.trim(cardInput.val()) == ''){
                alert('请填写银行账号');
                cardInput.focus();
                return false;
            }
            if($.trim(password.val()) == ''){
                alert('请填写资金密码');
                password.focus();
                return false;
            }
            return true;
        });


        //键盘单例
        var keyboard = new bomao.Keyboard({'inputTag':$('.key-logo').prev() , 'isQueue':false});
        keyboard.show(30,-20,$('.key-logo'));
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
                keyboard.show(30,-20,$(this));
            }
        });

    })(jQuery);
    </script>
@stop


