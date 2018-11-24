@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner">
            <div class="line-list-top"></div>

            <div class="nav-cont">{{ $oJcLottery->name}}{{ $oMethodGroup->name }} 第{{ date('Ymd', strtotime($oGroupBuy->created_at)) }}期合买方案</div>
            
            <div class="groupby-info clearfix">
                <div class="cell cell-1">
                    <p class="row row-no1">
                        发起人：<span class="f-username">{{ $oGroupBuy->display_nickname }}</span> 
                        <a class="ct-rank-detail" href="{{ route('jc.zj', [$oJcLottery->identifier, $oGroupBuy->user_id]) }}">@include('jc.groupbuy.star', ['oUserGrowth' => $oUserExtra])</a>
                      </p>
                    {{-- <p class="row">总中奖次数：<span class="f-times">{{ $oUserExtra->won_count }}</span> 次 &nbsp;&nbsp; 总中奖金额：<span class="f-money">{{ number_format($oUserExtra->won_prize, 2) }}</span> 元</p> --}}
                </div>

                
                <div class="cell cell-2">
                    
                    <?php 
                    $cle = max(strtotime($oGroupBuy->end_time) - time(), 0);
                    $h = floor(($cle%(3600*24))/3600);
                    $m = floor(($cle%(3600*24))%3600/60);
                    $s = floor(($cle%(3600*24))%60);

                    $h = $h < 10 ? '0'.$h : $h;
                    $m = $m < 10 ? '0'.$m : $m;
                    $s = $s < 10 ? '0'.$s : $s;

                    $timeStrArr = str_split($h.$m.$s);

                    ?>
                    <input type="hidden" value="{{ $cle }}" id="J-groupbuy-time-left" />
                    <p class="row row-no1" id="J-groupbuy-countdown">
                        剩余时间：
                        <span class="count-time"><i class="num-{{ $timeStrArr[0] }}">{{ $timeStrArr[0] }}</i><i class="num-{{ $timeStrArr[1] }}">{{ $timeStrArr[1] }}</i></span>时
                        <span class="count-time"><i class="num-{{ $timeStrArr[2] }}">{{ $timeStrArr[2] }}</i><i class="num-{{ $timeStrArr[3] }}">{{ $timeStrArr[3] }}</i></span>分
                        <span class="count-time"><i class="num-{{ $timeStrArr[4] }}">{{ $timeStrArr[4] }}</i><i class="num-{{ $timeStrArr[5] }}">{{ $timeStrArr[5] }}</i></span>秒
                        {{--
                        <span class="count-time"><i>{{ sprintf('%02d', $h) }}</i></span>时
                        <span class="count-time"><i>{{ sprintf('%02d', $m) }}</i></span>分
                        <span class="count-time"><i>{{ sprintf('%02d', $s) }}</i></span>秒
                        --}}
                    </p>
                    {{--
                    <p class="row row-text">
                        方案编号：{{ $oGroupBuy->serial_number }}
                        发起时间：{{ $oGroupBuy->created_at }}
                        认购截止时间：{{ $oGroupBuy->end_time }}
                    </p>
                     --}}
                </div>
               


                <?php
                //print_r($oGroupBuy);exit;
                ?>
                <div class="cell cell-3">
                    {{--
                    <p class="row">
                        @if ($bDisplayBet && $oGroupBuy->bet_id > 0)
                        <a class="link" href="{{ route('jc.bet_detail', $oGroupBuy->bet_id) }}">查看方案明细</a>
                        @endif
                    </p>
                    --}}
                    <!--
                    <a class="link-float" href="#">复制方案地址</a>
                    -->
                </div>
            </div>

            <div class="bet-confirm">
                @include('jc.match', ['datas' => $datas, 'aWays' => $aWayList, 'bDisplayBet' => $bDisplayBet])

            <div class="form-control panel-group-status clearfix">
                <div class="title">
                    方案编号：{{ $oGroupBuy->serial_number }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    发起时间：{{ $oGroupBuy->created_at }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    认购截止时间：{{ $oGroupBuy->end_time }}



                    @if ($bDisplayBet && $oGroupBuy->bet_id > 0)
                    <a target="_blank" class="link" href="{{ route('jc.bet_detail', $oGroupBuy->bet_id) }}">查看方案明细</a>
                    @endif


                </div>


                <form action="{{ route('jc.follow', $oGroupBuy->id)}}" method="post" class="clearfix" id="J-form">
                    <input id="J-token-value" name="_token" type="hidden" value="{{ csrf_token() }}" />
                    <input name="group_id" type="hidden" value="{{ $oGroupBuy->id }}" />
                    <div class="bet-confirm">
                        <table class="table table-group">
                            <tr>
                                <th>本方案总金额</th>
                                <th>保底金额</th>
                                <th>发起人提成</th>
                                <th>方案进度</th>
                                <th>合买对象</th>
                                <th>显示设置</th>
                                <th>方案状态</th>
                                <th>操作</th>
                                {{--
                                <th>已认购</th>
                                <th>可认购</th>
                                --}}
                            </tr>
                            <tr>
                                <td><span id="J-money-groupbuy-total">{{ number_format($oGroupBuy->amount, 2) }}</span> 元</td>
                                <td>
                                    <p>{{ number_format($oGroupBuy->guarantee_amount, 2) }} 元 ( {{ $oGroupBuy->guarantee_percent }} )</p>
                                    @if (Session::get('user_id') == $oGroupBuy->user_id && $oGroupBuy->status == \JcModel\JcGroupBuy::STATUS_NORMAL && $oGroupBuy->guarantee_count < $iMaxGuaranteeCount && $oGroupBuy->guarantee_amount + $oGroupBuy->buy_amount  < $oGroupBuy->amount)
                                    <p><a class="link" href="{{ route('jc.append', $oGroupBuy->id) }}" id="J-button-addbasic" count="{{ $oGroupBuy->guarantee_count }}" total="{{  $oGroupBuy->guarantee_amount }}" programme="{{ $oGroupBuy->amount  }}">追加保底</a></p>
                                    @endif
                                </td>
                                <td>{{ $oGroupBuy->fee_rate * 100 }}%</td>
                                <td>
                                    <span class="bar">
                                        <span class="bar-inner" style="width:{{ $oGroupBuy->buy_percent }};"></span>
                                    </span>
                                    &nbsp;
                                    {{ $oGroupBuy->buy_percent }}
                                </td>
                                <td>
                                    {{ $oGroupBuy->formatted_allow_type }}
                                </td>
                                <td>
                                    {{ $oGroupBuy->formatted_show_type }}
                                </td>
                                <td>
                                    {{ $oGroupBuy->formatted_status }}
                                </td>
                                 <td>
                                    @if ($oGroupBuy->checkDrop())
                                    <a class="J-groupbuy-cancel link" href="{{ route('jc.drop', $oGroupBuy->id) }}">我要撤单</a>
                                    @endif
                                </td>
                                {{--
                                <td>{{ number_format($oGroupBuy->buy_amount, 2) }} 元</td>
                                <td>
                                    <span id="J-money-groupbuy-left">{{ number_format($oGroupBuy->amount - $oGroupBuy->buy_amount, 2) }}</span> 元
                                </td>
                                --}}
                            </tr>
                        </table>

                    </div>
                    @if ($oGroupBuy->status == \JcModel\JcUserGroupBuy::STATUS_NORMAL && $oGroupBuy->amount > $oGroupBuy->buy_amount)
                    <div class="right">
                        <p class="sub-row">

                            本方案还可以认购 <span class="c-red" style="font-size:16px;" id="J-money-groupbuy-left">{{ number_format($oGroupBuy->amount - $oGroupBuy->buy_amount, 2) }}</span> 元，我要认购
                            &nbsp;&nbsp; 
                           <input name="amount" class="input" style="width:80px;text-align:center;" type="text" value="" id="J-groupbuy-input-money" />
                           &nbsp;&nbsp;
                            元,
                            占 
                            <span id="J-groupbuy-input-pre">0.00</span>
                            {{-- <input class="input" style="width:80px;text-align:center;" type="text" value="" id="J-groupbuy-input-pre" /> --}}
                             %
                        </p>
                        <p class="sub-row">
                            <input type="button" class="btn-submit" value=" 确定认购 " id="J-button-submit" style="margin-bottom:0px;" />
                        </p>
                        <p class="sub-row f-12">
                            可用余额: {{ number_format($oAccount->available, 2) }} 元
                        </p>
                    </div>
                    @else
                        {{--<span class="status-groupbuy-full">{{ $oGroupBuy->formatted_status }},不可购买</span>--}}
                    @endif
                </form>


                @if ($oGroupBuy->status == \JcModel\JcUserGroupBuy::STATUS_PRIZE_SENT)
                <div class="status-result">
                    <span class="ico-prize-yellow"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                    总奖金：
                    <span class="c-yellow">{{ number_format($oGroupBuy->prize, 4) }}</span>
                     元，发起人提成 
                    <span class="c-yellow">{{ number_format($oGroupBuy->fee_amount, 4) }}</span> 元，剩余分配奖金：
                    <span class="c-yellow">{{ number_format($oGroupBuy->prize - $oGroupBuy->fee_amount - $oGroupBuy->system_prize, 4) }}</span>
                     元

                     &nbsp;&nbsp;
                     <i id="J-groupbuy-info-prize-tip" style="font-size:16px;cursor: pointer;" class="fa fa-info-circle c-yellow"></i>
                     
                </div>
                @endif




        </div>
    </div>



    <div class="panel-list-group-history" id="J-panel-list-group-history">
        <div class="bet-confirm">
            <div class="records-inner">
                <div class="records-tab">
                    <div class="t clearfix">
                        <a href="javascript:void(0)" data-url="{{ route('jc.follow_list', $oGroupBuy->id) }}" class="trigger trigger-active">所有参与用户</a>
                        <a href="javascript:void(0)" data-url="{{ route('jc.follow_list', [$oGroupBuy->id, 'user_id' => Session::get('user_id')]) }}" class="trigger">我的认购记录</a>
                    </div>
                </div>
                <div id="J-panel-group-data-list">
                    @include('jc.groupbuy.follow_list')
                </div>
            </div>
        </div>
    </div>


    <script type="text/template" id="J-template-addbasic">
        <span>追加保底:</span>
        <input id="J-input-val" type="text" class="input" overdrawn={{ number_format($fAvailable, 2) }}></input>
        <span>元,共</span><span id="J-total"></span><span>元(</span><span id="J-percentage"></span><span>%)</span>
    </script>

</div>



@include('jc.rank-detail')

@stop








@section('end')
@parent
<script type="text/javascript">
(function($, host){
    var iptMoney = $('#J-groupbuy-input-money'),
        iptPre = $('#J-groupbuy-input-pre'),
        textMoneyTotal = $('#J-money-groupbuy-total'),
        textMoney = $('#J-money-groupbuy-left'),
        TIP = host.Tip.getInstance();
    var checkInputFn = function(e){
        var v = Number(this.value.replace(/[^\d]/g, '')),
            totalmoney = Number(textMoneyTotal.text().replace(/[^\d|^.]/g, '')),
            money = Number(textMoney.text().replace(/[^\d|^.]/g, '')),
            usedmoney = totalmoney - money,
            pre = 0;
        if($.trim(this.value) == ''){
            return;
        }
        if(isNaN(v)){
            v = 1;
        }
        v = v > money ? money : v;
        this.value = v;

        pre = (v/totalmoney*100).toFixed(2);
        iptPre.text(pre);

    };

    iptMoney.blur(checkInputFn);
    iptMoney.keyup(checkInputFn);


    $('#J-groupbuy-info-prize-tip').hover(function(){
        TIP.setText('奖金分配规则：<br />享受系统保底的方案，方案总奖金扣除<br />系统保底部分的奖金和发起人的提成后，<br />剩余的奖金按照合买比例进行重新分配<br />您所得的奖金计算公式:（方案奖金 - 系<br />统奖金 - 奖金提成）×（您的投注金额<br />÷（方案金额－系统保底金额））');
        TIP.show(25, TIP.dom.height()/2*-1, this);
    },function(){
        TIP.hide();
    });


})(jQuery, bomao);







(function($, host){

    var is_group_buy = true,
        button = $('#J-button-submit'),
        form = $('#J-form');
    var MASK = host.Mask.getInstance(),
        MSG = host.Message.getInstance();

    var get_my_bet_data = function(){
        var ordermoney = Number($('#J-order-amount').text().replace(/[^\d|\.]/g, '')),
            result = {};
        result['ordermoney'] = ordermoney;
        if(is_group_buy){
            result['betmoney'] = Number($('#J-groupbuy-input-money').val());
            result['betnum'] = Number($('#J-order-betnum').text());
            result['multiple'] = Number($('#J-order-multiple').text());
        }
        return result;
    };
    var get_msg_template = function(data){
        if(is_group_buy){
            return '<div>您的认购金额为<span class="c-yellow">'+ data['betmoney'] +'</span>元,是否确定投注?</div><div>注数: <span class="c-red">'+ data['betnum'] +'</span>注，倍数: <span class="c-red">'+ data['multiple'] +'</span>倍，方案金额:<span class="c-yellow">' + data['ordermoney'] + '</span>元</div>';
        }else{
            return '<div>您的投注金额为<span class="c-yellow">'+ data['ordermoney'] +'</span>元,是否确定投注?</div>';
        }
    };

    button.click(function(){
        var iptdom = $('#J-groupbuy-input-money'),
            money = Number(iptdom.val());
        if(money < 1){
            
            MSG.show({
                content:'认购金额输入不正确',
                confirmIsShow:true,
                confirmText:'确定',
                confirmFun:function(){
                    MSG.hide();
                    iptdom.focus();
                    MASK.hide();
                }
            });
            setTimeout(function(){
                MASK.show();
            }, 10);
            return;
        }

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
    });

    var submitData = function(){
        var data = form.serialize();
        $.ajax({
            url:'?',
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
                        closeIsShow:true,
                        isShowMask:true,
                        content: !!is_group_buy ? '认购成功' : '购买成功',
                        closeFun:function(){
                            location.href = location.href;
                            MSG.hide();
                        },
                        normalCloseFun:function(){
                            location.href = location.href;
                        }
                    });

                }else{
                    MSG.show({
                        title:'系统提示',
                        content:data['Msg'],
                        closeIsShow:true,
                        isShowMask:true,
                        closeFun:function(){
                            MSG.hide();
                        }
                    });
                    setTimeout(function(){
                        MASK.show();
                    }, 100);
                }
            },
            complete:function(){
                MASK.hide();
            },
            error:function(xhr, type){
                alert('数据提交失败:' + type);
                MASK.hide();
            }
        });
    };




    $(document).on('click', '.J-groupbuy-cancel', function(e){
        var el = $(this),
            url = el.attr('href');
        e.preventDefault();

        /*
        if(!confirm('是否确定撤销该方案?')){
            return;
        }
        */

        MSG.show({
            title:'系统提示',
            content:'是否确定撤销该方案',
            confirmIsShow:true,
            confirmText:'确认撤销',
            closeText:'取消',
            closeIsShow:true,
            isShowMask:true,
            closeFun:function(){
                MSG.hide();
            },
            confirmFun:function(){
                MSG.hide();
                MASK.hide();
                $.ajax({
                    url:url,
                    dataType:'json',
                    beforeSend:function(){
                        MASK.show();
                    },
                    success:function(data){
                        if(Number(data['isSuccess']) == 1){
                            MSG.show({
                                content:'撤销成功',
                                confirmIsShow:true,
                                confirmText:'确定',
                                confirmFun:function(){
                                    location.href = location.href;
                                    MSG.hide();
                                }
                            });
                        }else{
                            alert(data['Msg']);
                        }
                    },
                    error:function(xhr, type){
                        alert('数据提交是失败:' + type);
                    },
                    complete:function(){
                        MASK.hide();
                    }
                });
            
            }
        });
        setTimeout(function(){
            MASK.show();
        }, 100);





    });

})(jQuery, bomao);






(function($){
    var panel = $('#J-panel-list-group-history'),
        contlist = $('#J-panel-group-data-list'),
        triggers = panel.find('.trigger'),
        currentType = 0,
        typeHash = {
            '0':0,
            '1':1
        };

    panel.on('click', '.trigger', function(e){
        var el = $(this),
            index = triggers.index(this);
        e.preventDefault();
        currentType = index;
        triggers.removeClass('trigger-active');
        el.addClass('trigger-active');
        if (el.attr('data-url')){
            get_history_data(el.attr('data-url'));
        }
    });


    var get_history_data = function(url){
        var type = typeHash[''+currentType];
        $.ajax({
            url:url,
            beforeSend:function(){

            },
            success:function(data){
                contlist.html(data);
            },
            error:function(xhr, type){
                alert('数据请求失败:' + type);
            }
        });
    };


    panel.find('.page').on('click', 'a', function(e){
        var el = $(this),
            url = el.attr('href');
        if(url.indexOf('javascript') != -1){
            return;
        }
        get_history_data(url);
    });

})(jQuery);





(function($){
    var time = Number($('#J-groupbuy-time-left').val()),
        timeDoms = $('#J-groupbuy-countdown').find('.count-time'),
        timepage = new Date(),
        oldnums = 0,
        nums = 0,
        timer;
    if(isNaN(time) || time < 1){
        return;
    }
    timer = setInterval(function(){
        nums = time - Math.ceil((new Date() - timepage)/1000);
        if(nums <= 0){
            clearInterval(timer);
            location.href = location.href;
            return;
        }
        if(oldnums != nums){
            updateTimeView(nums);
        }
        oldnums = nums;
    }, 1000);


    var updateTimeView = function(second){
        var h = Math.floor(second/3600),
            m = Math.floor(second%3600/60),
            s = second%60,
            arr = [],
            strArr = [];
        h = h < 10 ? '0' + h : ''+h;
        m = m < 10 ? '0' + m : ''+m;
        s = s < 10 ? '0' + s : ''+s;
        arr = [h.split(''), m.split(''), s.split('')];
        timeDoms.each(function(i){
            strArr = [];
            $.each(arr[i], function(j){
                strArr.push('<i class="num-'+arr[i][j]+'">'+arr[i][j]+'</i>');
            });
            this.innerHTML = strArr.join('');
        });
    };

})(jQuery);


(function($, host){
    var button = $('#J-button-addbasic'),
        addvalue = $("#J-input-val"),
        MASK = host.Mask.getInstance(),
        MSG_2 = new host.Message({cls:'on-ico-warning'}),
        MSG_3 = new host.Message();
        form = $('#J-form');
    var appendCount = Number($('#J-button-addbasic').attr("count"));
        
    var get_msg_template = function(data){
        return host.util.template($("#J-template-addbasic").html(), data);
    };

    //动态更新保底信息
    var updateBasicInfo = function(){
        $("#J-input-val").val($("#J-input-val").val().replace(/[^\d]/g, ''));
        var init_total = Number(button.attr("total"));
        var val = Number($("#J-input-val").val()),
            total = 0,
            temp = 0,
            per = 0;
            limit_max = 0;
        
        limit_max = Number(button.attr("programme"))-init_total;
        if(val>limit_max){
            val = $("#J-input-val").val(limit_max);
            total = Number(button.attr("programme")); 
            temp = 100;
        }else{
            val = $("#J-input-val").val();
            total = Number(val) + Number(init_total); 
            temp = Number(total / Number(button.attr("programme")) * 100);
        }

        if(init_total == 0){
            var limit_min = Math.ceil(Number(button.attr("programme"))/5);
            $("#J-input-val").blur(function(e){
                if($("#J-input-val").val()<limit_min){
                    $("#J-input-val").val(limit_min);
                    total = limit_min; 
                    temp = Number(total / limit_max * 100);
                    $("#J-total").html(total);
                    per = parseInt(temp) == temp ? temp : temp.toFixed(2);
                    $("#J-percentage").html(per);
                }
            });
        }

         per = parseInt(temp) == temp ? temp : temp.toFixed(2);
        
        $("#J-total").html(total);
        $("#J-percentage").html(per);
    };

   var rmoney=function(str){
        return parseFloat(str.replace(/[^\d\.-]/g, ""));
    } 

    var get_comfirm_value_massage = function(data){
        return '<span>您的保底金额为<span class="c-yellow">'+data+'</span>元，是否确定保底？<span>';
    };
    
    button.click(function(e){
        e.preventDefault();

        MSG_2.show({
            'isShowMask':true,
            'confirmIsShow':true,
            'confirmText':'确认',
            'content':get_msg_template(),
            confirmFun:function(){
                if($("#J-input-val").val() != "" && $("#J-input-val").val() != "0"){
                    //金额判断
                    if(Number($("#J-input-val").val()) > rmoney($("#J-input-val").attr('overdrawn'))){
                        var confirmMSG = new host.Message();
                            confirmMSG.show({
                                'isShowMask':true,
                                'confirmIsShow':true,
                                'content':'您的账户余额不足，请重新输入',
                                'confirmText':'确认',
                                'hideClose':true,
                                confirmFun:function(){
                                    confirmMSG.hide();
                                    MASK.show();
                                }
                            });
                    }else{
                        //保底金额提示框
                        MSG_3.show({
                            isShowMask:true,
                            confirmIsShow:true,
                            confirmText:'确认',
                            content:get_comfirm_value_massage($("#J-input-val").val()),
                            confirmFun:function(){
                                MSG_3.hide();
                                submitData();
                                MSG_2.hide();
                            },
                            normalCloseFun:function(){
                                MSG_3.hide();

                                 setTimeout(function(){
                                    MASK.show();
                                }, 100);
                            }
                        });
                    }
                }else{
                    var confirmMSG = new host.Message();
                    confirmMSG.show({
                        'isShowMask':true,
                        'confirmIsShow':true,
                        'content':'请输入保底金额',
                        'confirmText':'确认',
                        hideClose:true,
                        confirmFun:function(){
                            confirmMSG.hide();
                            $("#J-input-val").focus();
                            MASK.show();
                        }
                    });
                }
            }
        });

         $("#J-input-val").focus();

        //有保底值，进行追加保底时，默认值为1
        if(!$("#J-input-val").val()){
            $("#J-input-val").val(1);
        }

       updateBasicInfo();
       if(Number(button.attr("total"))==0){
            $("#J-input-val").val(Math.ceil(Number(button.attr("programme"))/5));
            $("#J-total").html(Math.ceil(Number(button.attr("programme"))/5));
            var temp = Math.ceil(Number(button.attr("programme"))/5) / Math.ceil(Number(button.attr("programme"))) * 100;
            var per = parseInt(temp) == temp ? temp : temp.toFixed(2);
            $("#J-percentage").html(per);
       }

        $("#J-input-val").bind('input propertychange', function() {
            updateBasicInfo();  
        });

        var submitData = function(){
            $.ajax({
                url:button.attr('href'),
                dataType:'json',
                method:'POST',
                data:{_token:$("#J-token-value").val() , guarantee_amount:$("#J-input-val").val()},
                beforeSend:function(){
                    MASK.show();
                },
                success:function(data){
                    if(Number(data['isSuccess']) == 1){
                        button.hide();

                        setTimeout(function(){
                            MASK.show();
                        }, 100);
                        
                        MSG_2.show({
                            closeIsShow:true,
                            isShowMask:true,
                            content: '成功追加保底',
                            closeFun:function(){
                                location.href = location.href;
                                MSG_2.hide();
                            },
                            normalCloseFun:function(){
                                location.href = location.href;
                            }
                        });

                    }else{
                        MSG_2.show({
                            title:'系统提示',
                            content:data['Msg'],
                            closeIsShow:true,
                            isShowMask:true,
                            closeFun:function(){
                                MSG_2.hide();
                            }
                        });
                        setTimeout(function(){
                            MASK.show();
                        }, 100);
                    }
                },
                complete:function(){
                    MASK.hide();
                },
                error:function(xhr, type){
                    alert('数据提交失败:' + type);
                    MASK.hide();
                }
            });

        };

    });


})(jQuery, bomao);



</script>
@stop

















