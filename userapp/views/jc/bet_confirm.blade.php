@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner">
            <div class="line-list-top"></div>


            <div class="page-nav">
                <span class="title">请确认您的投注信息</div>
            </div>



            <div class="bet-confirm">
                @include('jc.match', ['datas' => $datas])
                <div class="list-bg"></div>

                <div class="form-control clearfix">


                    <form action="{{{ route('jc.bet') }}}" method="post" id="J-form">
                        @if (empty($oGroupBuy) && $is_group_buy)
                        <div class="groupbuy-cont">
                            <table class="table form-groupbuy">
                                <tr>
                                    <td class="feild" width="120" rowspan="4">认购设置</td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="title">我要认购:</label>
                                        <input id="J-input-amount" name="buy_amount" class="input" type="text" value="0" /> 元
                                        (<span id="J-text-amount-pre">0.00</span>%)
                                        &nbsp;&nbsp;<span class="tip">最低认购5%</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="title">中奖提成:</label>
                                        <select name="fee_rate" id="J-select-fee-rate">
                                            @for ($i=0;$i<=10;$i++)
                                            <option value="{{{ $i/100 }}}">{{{ $i }}}%</option>
                                            @endfor
                                        </select>
                                        <span class="tip">
                                            &nbsp;&nbsp;获取提成的条件：（方案奖金-提成金额）>方案金额，不盈利则没有提成
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
                                        <span id="J-panel-guarantee-all" style="display:none;">

                                        </span>
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
                                    <td class="feild" rowspan="2">方案设置</td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="title">显示设置:</label>
                                        @foreach($aShowType as $iShowType => $sShowType)
                                        <label class="checkbox-item">
                                            <input name="show_type" type="radio" value="{{{ $iShowType }}}" @if($iShowType==\JcModel\JcUserGroupBuy::SHOW_TYPE_PUBLIC_CODE)checked="checked"@endif /> {{{ __($sShowType) }}}
                                        </label>
                                        @endforeach
                                    </td>
                                </tr>
                                <tr>
                                    <td class="feild" rowspan="2">合买对象</td>
                                </tr>
                                <tr>
                                    <td>
                                        <label class="checkbox-item">
                                            <input name="buy_user_type" type="radio" value="0" checked="checked" /> 全体成员可参与
                                        </label>
                                         <label class="checkbox-item">
                                            <input name="buy_user_type" type="radio" value="1" /> 仅限我的直属下级
                                        </label>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        @endif



                        <div class="button">
                             <input name="_token" type="hidden" value="{{{ csrf_token() }}}" />
                            @foreach($aInputData as $key => $val)
                                <input name="{{{ $key }}}" type="hidden" value="{{{ $val }}}" />
                            @endforeach
                            @if (isset($oGroupBuy))
                                <input name="group_id" type="hidden" value="{{{ $oGroupBuy->id}}}" />
                                <input type="button" class="btn-submit" value=" 提 交 " id="J-button-submit" />
                            @elseif ($is_group_buy)
                                <input type="button" class="btn-submit" value=" 发起合买 " id="J-button-submit" />
                                <input name="group_id" type="hidden" value="-1" />
                            @else
                                <input type="button" class="btn-submit" value=" 提 交 " id="J-button-submit" />
                            @endif
                            <a href="javascript:history.back(-1);" class="goback">重选赛事</a>

                            @if (empty($is_group_buy))
                            <?php if(empty($oUser)){ $oUser = \User::find(Session::get('user_id')); } ?>
                            @if ($oUser->isEnableVoucher())
                            <span class="use-cash-gift">
                                <input type="checkbox" id="J-checkbox-use-cash-gift" name="use_voucher" value="1" @if (empty($bAllowVoucher)) disabled="disabled" @else  checked="checked" @endif />
                                <label for="J-checkbox-use-cash-gift">使用礼金（可用礼金：{{{ number_format($oUser->voucher_amount, 2) }}}元）</label>
                            </span>
                            @endif
                            @endif
                        </div>
                    </form>




                </div>
            </div>









        </div>
    </div>
</div>
@include('w.footer')
@stop




@section('end')




<script type="text/javascript">
(function($, host){
    var is_group_buy = {{!$is_group_buy ? 'false' : 'true'}},
        button = $('#J-button-submit'),
        form = $('#J-form');
    var MASK = host.Mask.getInstance(),
        MSG = host.Message.getInstance();

    var get_my_bet_data = function(){
        var ordermoney = Number($('#J-order-amount').text().replace(/[^\d|\.]/g, '')),
            result = {};
        result['ordermoney'] = ordermoney;
        if(is_group_buy){
            result['betmoney'] = Number($('#J-input-amount').val());

            result['betnum'] = Number($('#J-order-betnum').text());
            result['multiple'] = Number($('#J-order-multiple').text());
        }
        return result;
    };
    var get_msg_template = function(data){
        if(is_group_buy){
            return '<div>您的认购金额为'+ data['betmoney'] +'元,是否确定投注?</div><div>注数: '+ data['betnum'] +'注，倍数: '+ data['multiple'] +'倍，方案金额:' + data['ordermoney'] + '元</div>';
        }else{
            return '<div>您的投注金额为'+ data['ordermoney'] +'元,是否确定投注?</div>';
        }
    };

    button.click(function(){
        if(is_group_buy){
            MSG.show({
                isShowMask:true,
                confirmIsShow:true,
                confirmText:'确认投注',
                content:get_msg_template(get_my_bet_data()),
                confirmFun:function(){
                    MSG.hide();
                    submitData();
                }
            });
        }else{
            submitData();
        }
        
    });

    var submitData = function(){
        var data = form.serialize();
        $.ajax({
            url:'{{ route('jc.bet') }}',
            dataType:'json',
            method:'POST',
            data:data,
            beforeSend:function(){
                MASK.show();
            },
            success:function(data){
                if(Number(data['isSuccess']) == 1){
                    button.hide();

                    setTimeout(function(){
                        MASK.show();
                    }, 100);


                    MSG.show({
                        cancelIsShow:true,
                        closeIsShow:true,
                        cancelText:'查看注单',
                        closeText:'继续投注',
                        content: !!is_group_buy ? '认购成功' : '购买成功',
                        cancelFun:function(){
                            location.href = data['data']['RedirectUrl'];
                        },
                        closeFun:function(){
                            location.href = '/jc/football';
                        },
                        normalCloseFun:function(){
                            location.href = data['data']['RedirectUrl'];
                        }
                    });

                }else{
                    setTimeout(function(){
                        MASK.show();
                    }, 100);
                    MSG.show({
                        title:'系统提示',
                        closeIsShow:true,
                        content:data['Msg'],
                        closeFun:function(){
                            MASK.hide();
                            MSG.hide();
                        }
                    });
                }
            },
            complete:function(){
                //MASK.hide();
            },
            error:function(xhr, type){
                alert('数据提交失败:' + type);
                MASK.hide();
            }
        });
    };




})(jQuery, bomao);
</script>





@if ($is_group_buy)
<script type="text/javascript">
(function($, host){
    var orderAmountDom = $('#J-order-amount'),
        userMoneyDom = $('#J-text-user-money');
    //自动生成最小认购金额
    var inputDom = $('#J-input-amount'),
        preDom = $('#J-text-amount-pre');
    var updateInputAmount = function(amount){
        var orderAmount = Number(orderAmountDom.text().replace(',', '')),
            userMoney = Number(userMoneyDom.text().replace(',', '')),
            mustMinMoney = orderAmount * 0.05,
            amount = amount || mustMinMoney;
        amount = amount < mustMinMoney ? mustMinMoney : amount;
        amount = amount > orderAmount ? orderAmount : amount;
        amount = amount > userMoney ? userMoney : amount;
        amount = amount < 1 ? 1 : Math.ceil(amount);

        inputDom.val(amount);
        preDom.text((amount/orderAmount*100).toFixed(2));
    };
    inputDom.blur(function(){
        var v = Number(this.value),
            gallmoney = Number(orderAmountDom.text().replace(',', '')) - v;
        if(isNaN(v)){
            updateInputAmount();
        }else{
            updateInputAmount(v);
        }
        setGuaranteeView(gallmoney);
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


    var selectFeeRate = new host.Select({realDom:'#J-select-fee-rate', cls:'w-2'}),
        selectGuarantee = new host.Select({realDom:'#J-select-guarantee', cls:'w-2'});
    selectGuarantee.addEvent('change', function(e, v){
        var v = Number(this.getValue()),
            gallmoney = Number(orderAmountDom.text().replace(',', '')) - Number($('#J-input-amount').val());
        if(v == 2){
            $('#J-panel-guarantee').show();
            updateInputGuarantee();
        }else{
            $('#J-panel-guarantee').hide();
        }
        if(v == 3){
            setGuaranteeView(gallmoney);
            $('#J-panel-guarantee-all').show();
        }else{
            $('#J-panel-guarantee-all').hide();
        }
    });

    var setGuaranteeView = function(num){
        $('#J-panel-guarantee-all').html('&nbsp;&nbsp;(金额: ' + num.toFixed(2) + ') ');
    };




})(jQuery, bomao);

</script>
@endif






@stop
