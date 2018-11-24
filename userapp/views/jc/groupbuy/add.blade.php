@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner">
            <div class="line-list-top"></div>


            {{--
            <div class="control">
                <a href="/jc/football">过关投注</a>
                <a href="/jc/yutou/football">发起预投</a>
                <a href="/jc/groupbuy/football">参与合买</a>
            </div>
            --}}




            <div class="bet-confirm">

                <div class="form-control clearfix" style="margin:0;">


                    <form action="{{ route('jc.bet') }}" method="post">

                    <div class="groupbuy-cont">
                        <table class="table form-groupbuy">
                            <tr>
                                <td class="feild" width="120">预投金额</td>
                                <td>
                                    <label class="title">预投金额:</label>
                                    <input id="J-money-pre" class="input" type="text" value="2" /> 元
                                    <span class="tip">［注］实际上传方案金额可上下浮动20%，金额必须为2的整数倍</span>
                                </td>
                                </td>
                            </tr>
                            <tr>
                                <td class="feild" width="120" rowspan="5">认购设置</td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="title">我要认购:</label>
                                    <input id="J-input-amount" name="buy_amount" class="input" type="text" value="1" /> 元
                                    (<span id="J-text-amount-pre">0.00</span>%) 
                                    &nbsp;&nbsp;<span class="tip">最低认购5%</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="title">中奖提成:</label>
                                    <select name="fee_rate">
                                        @for ($i=0;$i<=10;$i++)
                                        <option value="{{ $i/100 }}">{{ $i }}%</option>
                                        @endfor
                                    </select>
                                    <span class="tip">
                                        &nbsp;&nbsp;不盈利没有提成
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="title">保底设置:</label>
                                    <select name="guarantee" id="J-select-guarantee">
                                        <option value="1">不保底</option>
                                        <option value="2">保底</option>
                                        <option value="3">全保</option>
                                    </select>
                                    
                                    <span id="J-panel-guarantee" style="display:none;">
                                        <input id="J-input-guarantee" class="input" name="guarantee_amount" type="text" value="0" /> 元 
                                        (<span id="J-text-guarantee-pre">0</span>%)
                                        <span class="tip">
                                            (保底最少20%，截止后系统用保底金额认购以促进方案成功，多则返还) 
                                        </span>
                                    </span>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="title">可用余额:</label>
                                    <span id="J-text-user-money">10,0000.00</span> 元
                                </td>
                            </tr>
                            <tr>
                                <td class="feild" rowspan="2">方案设置</td>
                            </tr>
                            <tr>
                                <td>
                                    <label class="title">显示设置:</label>
                                    <label class="checkbox-item">
                                        <input name="show_type" type="radio" value="1" checked="checked" /> 不公开
                                    </label>
                                    <label class="checkbox-item">
                                        <input name="show_type" type="radio" value="2" /> 跟单即公开
                                    </label class="checkbox-item">
                                    <label class="checkbox-item">
                                        <input name="show_type" type="radio" value="3" /> 截止后公开
                                    </label>
                                    <label class="checkbox-item">
                                        <input name="show_type" type="radio" value="4" /> 公开
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>

                   
                    <div class="button">
                         <input name="_token" type="hidden" value="{{ csrf_token() }}" />
                        <input type="submit" class="btn-submit" value=" 提 交 " />
                    </div>
                    </form>


                </div>
            </div>









        </div>
    </div>
</div>
@stop




@section('end')



<script type="text/javascript">
(function($){
    var orderAmountDom = $('#J-money-pre'),
        userMoneyDom = $('#J-text-user-money');
    //自动生成最小认购金额
    var inputDom = $('#J-input-amount'),
        preDom = $('#J-text-amount-pre');
    var updateInputAmount = function(amount){
        var orderAmount = Number(orderAmountDom.val().replace(',', '')),
            userMoney = Number(userMoneyDom.text().replace(',', '')),
            mustMinMoney = orderAmount * 0.05,
            amount = amount || mustMinMoney;
        amount = amount < mustMinMoney ? mustMinMoney : amount;
        amount = amount < 1 ? 1 : Math.ceil(amount);
        amount = amount > orderAmount ? orderAmount : amount;
        amount = amount > userMoney ? userMoney : amount;

        inputDom.val(amount);
        if(amount > 0){
            preDom.text(Math.round(amount/orderAmount*100));
        }
    };
    inputDom.blur(function(){
        var v = Number(this.value);
        if(isNaN(v)){
            updateInputAmount();
        }else{
            updateInputAmount(v);
        }
    });
    updateInputAmount();



    //设置保底
    var guaranteeInput = $('#J-input-guarantee'),
        guaranteePreDom = $('#J-text-guarantee-pre');
    var updateInputGuarantee = function(money){
        var orderAmount = Number(orderAmountDom.text().replace(',', '')),
            userMoney = Number(userMoneyDom.text().replace(',', '')),
            guaranteePre = Number(guaranteePreDom.text()),
            mustMinMoney = orderAmount * 0.2,
            money = money || mustMinMoney;
        money = money < mustMinMoney ? mustMinMoney : money;
        money = money < 1 ? 1 : Math.ceil(money);
        money = money > orderAmount ? orderAmount : money;
        money = money > userMoney ? userMoney : money;

        guaranteeInput.val(money);
        guaranteePreDom.text(Math.round(money/orderAmount*100));
    };
    guaranteeInput.blur(function(){
        var v = Number(this.value);
        if(isNaN(v)){
            updateInputGuarantee();
        }else{
            updateInputGuarantee(v);
        }
    });
    $('#J-select-guarantee').change(function(){
        var v = Number(this.value);
        if(v == 2){
            $('#J-panel-guarantee').show();
            updateInputGuarantee();
        }else{
            $('#J-panel-guarantee').hide();
        }
    });



    //预投金额必须为2的倍数
    $('#J-money-pre').blur(function(){
        var v = Number(this.value);
        v = Math.ceil(v);
        if(v%2 == 1){
            v += 1;
        }
        if(v < 2){
            v = 2;
        }
        this.value = v;

        updateInputAmount();
    }).keyup(function(){
        var v = this.value.replace(/[^\d]/g, '');
        v = Number(v);
        if(v < 2){
            v = 2;
        }
        this.value = v;

        updateInputAmount();
    });

    

})(jQuery);

</script>



@stop






