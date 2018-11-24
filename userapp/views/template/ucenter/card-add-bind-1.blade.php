@extends('l.home')

@section('title')
    验证老银行卡 -- 增加绑定
@parent
@stop

@section('scripts')
@parent

    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
    {{ script('tip')}}
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
                    <td class="current"><div class="con"><i>1</i>验证老银行卡</div></td>
                    <td><div class="tri"><div class="con"><i>2</i>输入新银行卡信息</div></div></td>
                    <td><div class="tri"><div class="con"><i>3</i>确认银行卡信息</div></div></td>
                    <td><div class="tri"><div class="con"><i>4</i>绑定结果</div></div></td>
                </tr>
            </tbody>
        </table>
    </div>
    <form action="card-add-bind-2.php" method="post" id="J-form">
    <table width="100%" class="table-field">
        <tr>
            <td align="right">选择验证银行卡：</td>
            <td>
                <select id="J-select-bank-card" style="display:none;">
                    <option value="0" selected="selected">请选择你要验证的银行卡</option>
                    <option value="1">**** **** **** 9988</option>
                    <option value="2">**** **** **** 5484</option>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">开户人姓名：</td>
            <td>
                <input type="text" class="input w-4" id="J-input-name">
                <span class="ui-text-prompt">请输入旧的银行卡开户人姓名</span>
            </td>
        </tr>
        <tr>
            <td align="right">银行账号：</td>
            <td>
                <input type="text" class="input w-4" id="J-input-card-number">
                <span class="ui-text-prompt">请输入旧的银行卡卡号</span>
            </td>
        </tr>
        <tr>
            <td align="right">资金密码：</td>
            <td>
                <input type="password" class="input w-4" id="J-input-password">
                <span class="ui-text-prompt">请输入您的资金密码</span>
            </td>
        </tr>
        <tr>
            <td align="right"></td>
            <td>
                <input type="submit" value="下一步" class="btn" id="J-submit">
            </td>
        </tr>
    </table>
    </form>


</div>
@stop

@section('end')
<script>
    (function($){
        var tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'}),
            cardInput = $('#J-input-card-number'),
            makeBigNumber;

        new bomao.Select({realDom:'#J-select-bank-card',cls:'w-4'});

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
            if($.trim(bankCard.val()) == '0'){
                alert('请选择需要进行验证的银行卡');
                return false;
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


    })(jQuery);
    </script>
@stop