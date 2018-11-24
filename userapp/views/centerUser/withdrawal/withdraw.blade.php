@extends('l.home')

@section('title')
提款
@parent
@stop


@section ('styles')
@parent
{{ style('proxy-global') }}
{{ style('proxy') }}
<style type="text/css">
    .main-content .content {
        padding-top: 10px;
    }
    .main {padding: 0;margin-top: 0}
    .layout-row {float: left;}
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
        background: #FFF;
    }
    .table-field {
        margin-top: 0;
    }
    .money-num {
        font-size: 20px;
        color: #F60;
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
                <li class="active"><span class="top-bg"></span><a href="{{ route('user-withdrawals.withdraw')}}">提 款</a></li>
                <li><a href="{{ route('user-transfers.index')}}">转 账</a></li>
            </ul>
        </div>

        <div class="page-content-inner">
            <div class="tikuan">
                <form action="{{ route('user-withdrawals.withdraw', 1) }}" method="post" id="J-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="step" value="1"/>
                    <div class="a">
                        <span class="a1">提款申请</span>
                        <span class="a2">用户向欧豹平台发起提取现金的申请，待平台审核通过后向用户指定的银行账号汇入资金。</span>
                    </div>
                    <!--<div class="b"></div>-->
                    <ul class="c">
                        <li class="c1">
                            <i></i>
                            @if ($iWithdrawLimitNum)
                            <h5>注意：每天限制提款{{ $iWithdrawLimitNum }}次，今天您已经成功发起了 {{ $iWithdrawalNum }} 次提现申请</h5>
                            @endif
                        </li>
                        <li class="c2">
                            <span class="l">用户名：</span>
                            <span class="r">{{ $sUsername }}</span>
                        </li>
                        <li class="c3">
                            <span class="l">可提现金额：</span>
                        <span class="r" id="J-money-withdrawable">
                            {{ number_format($oAccount->available,2) }}  元
                        </span>
                        </li>
                        <li class="c4">
                            <span class="l">提现金额：</span>
                            <input id="J-input-money" name="amount" type="text" placeholder="提现金额" />
                            <i></i>
                            <span class="r">单笔最低提现金额：<span id="J-money-min">{{ $iMinWithdrawAmount ? number_format($iMinWithdrawAmount,2) : number_format("100.00",2) }}</span>元，最高<span id="J-money-max">{{ $iMaxWithdrawAmount ? number_format($iMaxWithdrawAmount,2) : number_format("500000.00",2)}}</span>元</span>

                        </li>

                    </ul>
                    <div class="d">
                        <div class="d1">
                            <h5>提现银行卡:</h5>
                            <ul id="d1">
                                @foreach($aBankCards as $oBankCard)
                                <li class="{{ $aBanks[$oBankCard->bank_id] }}" value="{{ $oBankCard->id }}">
                                    <span>{{$oBankCard->account_hidden}}</span>
                                    @if($oBankCard->islock)
                                    <div class="locked">
                                        <i></i>
                                        <h4>已锁定</h4>
                                        <span>联系客服解除银行卡锁定</span>
                                    </div>
                                    @endif

                                </li>

                                @endforeach
                                <input id='d1-bankid' type="hidden" name="id"/>
                            </ul>
                        </div>
                        <div class="d2">
                            <input type="submit" class="btn" value=" 下一步 " id="J-submit" />
                        </div>
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
<script type="text/javascript">
    (function($){

        var ipt1 = $('#J-input-money'),
                moneyInput = $('#J-input-money'),
                tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-input-floattip'});
//                bankSelect = new bomao.Select({realDom:'#J-select-bank',cls:'w-6'});

//        bankSelect.addEvent('change', function(e, value, text){
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

//        });
        $('#d1 li').on('click',function () {
            var _b = $('#d1-bankid'),
                    _a = $(this).attr('value');
            _b.val(_a);
            $(this).siblings('li').find('.check').remove();

            if(!($(this).find('.check').length>0)){
                $(this).append('<div class="check"></div>');
            }

            console.log(_b.val());

        })

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


