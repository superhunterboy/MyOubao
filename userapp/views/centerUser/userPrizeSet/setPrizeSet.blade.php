@extends('l.home')

@section('title')
    奖金组
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
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
                    <li><a href="{{ route('users.index') }}">&lt;&lt; 返回团队管理</a></li>
                    <li class="active"><span class="top-bg"></span><a href="">奖金组设置</a></li>
                </ul>
            </div>




            <form action="{{ route('user-user-prize-sets.set-prize-set', [$iUserId]) }}" method="post" id="J-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <input type="hidden" name="_method" value="PUT" />
            <div class="page-content-inner">
                    <table width="100%" class="table" style="border:none;">
                        <thead>
                            <tr>
                                <th><span class="tip">用户名称</span></th>
                                <th><span class="tip">用户属性</span></th>
                                <th><span class="tip">奖金组</span></th>
                                @if($bIsOverLimitPrizeGroup)
                                <th><span class="tip">临时奖金组</span></th>
                                @endif
                                <th class="last"><span class="tip">下级人数</span></th>
                                <th><span class="tip">团队余额</span></th>
                                <th class="last"><span class="tip">在线</span></th>
                                <th class="last"><span class="tip">最近登录时间</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $oUser->username }}</td>
                                
                                <td>{{ $oUser->user_type_formatted }}</td>
                                <td>{{ $foreverPrize }}</td>

                                @if($bIsOverLimitPrizeGroup)
                                    <td>
                                    @if (!$isForever)
                                         {{ $oUser->prize_group }}
                                     @else
                                          --
                                    @endif
                                    </td>

                                @endif
                                <td>{{ $oUser->getAgentDirectChildrenNum()}} </td>
                                <td>{{ $oUser->getGroupAccountSum() }} 元</td>
                                <td>{{ $isOnline}} </td>
                                <td>{{Session::get('last_signin_at')}} </td>
                            </tr>
                        </tbody>
                    </table>
                    <br />
{{--                    <table width="100%" class="table" style="border:none;">
                        <tr>
                            <th>本月投注详情</th>
                            <th>彩票</th>
                            <th>体育</th>
                            <th>电子娱乐</th>
                        </tr>

                        <tr>
                            <td>投注额</td>
                            <td>{{$aLottery['team_turnover']}}</td>
                            <td>{{$aSport['team_turnover']}}</td>
                            <td>{{$aElectronic['team_turnover']}}</td>
                        </tr>

                        <tr>
                            <td>净盈亏</td>
                            <td>{{$aLottery['team_profit']}}</td>
                            <td>{{$aSport['team_profit']}}</td>
                            <td>{{$aElectronic['team_profit']}}</td>
                        </tr>
                    </table>
                    <br />--}}





                    <div class="row-mode mode-sport">
                        <div class="row-mode-title">
                            彩票奖金组设置
                        </div>
                        <div class="row row-set-prize clearfix" style="border-bottom:0;">

                                <input type="hidden" value="{{ $iMinPrizeGroup }}" id="J-input-prize-min" />
                                <input type="hidden" value="{{ $iMaxPrizeGroup }}" id="J-input-prize-max" />

                                <div style="text-align:center;">
                                    <input type="text" class="input w-1" id="J-input-prize" name="prize_group" value="{{ $sCurrentUserPrizeGroup }}" style="text-align:center;" />

                                    <input style="display:none;" type="button" value=" 重置 " id="J-button-reset" class="button" />
                                    &nbsp;&nbsp;
                                    <span class="tip-text">预计平均返点率</span>
                                    <span class="percentage" id="J-text-percentage">0.00%</span>
                                    <a id="J-ex-link" data-url="{{ route('user-user-prize-sets.prize-set-detail') }}" href="{{ route('user-user-prize-sets.prize-set-detail') }}/{{ $sCurrentUserPrizeGroup }}" class="ex_link" target="_blank">奖金详情</a>
                                </div>


            
                                @if(empty($hashOverLimits))
                                <div class="panel-progress" style="margin-left:190px;">
                                    <div class="bar" style="width:100%;" id="J-bar-middle">
                                        <div class="bar-ce"></div>
                                        <div class="sign-middle" id="J-prize-num-current">{{ $sCurrentUserPrizeGroup }}</div>
                                    </div>
                                    <div class="sign-start" id="J-prize-num-min">{{ $iMinPrizeGroup }}</div>
                                    <div class="sign-end" id="J-prize-num-max">{{ $iMaxPrizeGroup }}</div>
                                </div>
                                @endif



                                @if(!empty($hashOverLimits))
                                <div class="point-cont clearfix" id="J-point-item-cont" style="padding: 30px 0 0px 428px;font-size:0;text-align:left;">
                                    @foreach($aDiffPrizes as $iPrizeGroup=>$num)
                                    <?php if ($iPrizeGroup > Session::get('user_prize_group')) break;?>
                                        @if($oUser->prize_group == $iPrizeGroup)
                                        <div class="item item-active item-current item-select" data-point="{{$iPrizeGroup}}" id="J-item-{{$iPrizeGroup}}">
                                        @else
                                        <div class="item item-active" data-point="{{$iPrizeGroup}}" id="J-item-{{$iPrizeGroup}}">
                                        @endif
                                            <span class="point">{{$iPrizeGroup}}</span>
                                            @if($iPrizeGroup >=$agent_min_high_grize_group)
                                            <span class="num">{{$num}}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <div class="show-text-cont" style="text-align:center;display:none;">
                                    <span id="J-text-current-select" class="show-num">{{ $oUser->prize_group }}</span>
                                </div>
                                @endif


                        </div>
                    </div>




                @if (!empty($hashOverLimits ))
                @if($oUser->prize_group  < $agent_min_high_grize_group || $oUser->prize_group == $foreverPrize )
                <div id="J-check-type-cont" class="user-add-switch-type" style="display:none;">
                @else
                <div id="J-check-type-cont" class="user-add-switch-type">
                @endif


                    <ul class="field field-type-switch">
                        <li><a href="javascript:;">临时</a></li>
                        <li><a href="javascript:;">永久</a></li>
                    </ul>
                    <div class="tip">
                        ( 可升可降，最低可降至{{ $foreverPrize }} )
                    </div>
                    <div class="tip">
                        ( 一但提升，不可降 )
                    </div>

                    <input id="J-input-type-isforever" type="hidden" value="{{$isForever}}" name="is_forever" />
                    <!--
                    <div style="padding:20px 0 0 360px;">
                        <label for="J-checkbox-forever">
                            <input @if($isForever) checked="checked" @endif id="J-checkbox-forever" name="is_forever" value="1" type="radio" />
                             永久 (一但提升，不可降)
                        </label>
                    </div>
                    <div style="padding:20px 0 0 360px;">
                        <label for="J-checkbox-temp">
                            <input @if(!$isForever) checked="checked" @endif id="J-checkbox-temp" name="is_forever" value="0" type="radio" />
                             临时

                             (可升可降，最低可降至1952 <a href="#">详细说明</a>)
                        </label>
                    </div>
                    -->
                </div>
                @endif




                    <div class="row-mode mode-sport" style="display: none;">
                        <div class="row-mode-title">
                            竞彩返点设置
                        </div>
                        <div class="row row-set-prize clearfix">
                            <input type="hidden" value="{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}" id="J-input-single-commission-min" />
                            <input type="hidden" value="{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0 }}" id="J-input-single-commission-max" />
                            <label class="text-title">设置单关返点</label>

                            @foreach($oUserCommissions as $data)
                                @if($data->series_set_id == SeriesSet::ID_FOOTBALL_SINGLE)
                                    <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="{{$data->sub_commission_rate}}" style="text-align:center;" />
                                    %
                                    &nbsp;
                                    <span class="tip">最高可分配 <span class="max-point">{{$data->commission_rate}}</span> %</span>
                                    <input type="hidden" value="0.5" class="setp" />
                                    {{--  <span class="percentage" id="J-text-percentage-single">{{number_format($data->commission_rate, 2)}}%</span> --}}
                                @endif
                            @endforeach

                            {{--                        <a id="J-ex-link" data-url="{{ route('user-user-prize-sets.prize-set-detail') }}" href="{{ route('user-user-prize-sets.prize-set-detail') }}/1950" class="ex_link" target="_blank">奖金详情</a>--}}

                            <br />
                            <label class="text-title"></label>
                            <span id="J-angent-odd-tip2" class="tip" style="display:none;font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入整数比例</span>
                            <div class="panel-progress">
                                <div class="bar" style="width:100%;" id="J-bar-middle-single-commission">
                                    <div class="bar-ce"></div>
                                    <div class="sign-middle" id="J-single-commission-num-current">{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0 }}</div>
                                </div>

                                <div class="sign-start" id="J-single-commission-num-min">{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}</div>
                                <div class="sign-end" id="J-single-commission-num-max">{{ isset($iPlayerMaxJcSingleCommissionRate) ? $iPlayerMaxJcSingleCommissionRate : 0 }}</div>
                            </div>
                        </div>

                        <div class="row row-set-prize clearfix">
                            <input type="hidden" value="{{ isset($iPlayerMinJcMultipleCommissionRate) ? $iPlayerMinJcMultipleCommissionRate : 0 }}" id="J-input-multiple-commission-min" />
                            <input type="hidden" value="{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 0 }}" id="J-input-multiple-commission-max" />
                            <label class="text-title">设置串关返点</label>


                            @foreach($oUserCommissions as $data)
                                @if($data->series_set_id == SeriesSet::ID_FOOTBALL_MIX)
                                    <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="{{$data->sub_commission_rate}}" style="text-align:center;" />
                                    %
                                    &nbsp;
                                    <span class="tip">最高可分配 <span class="max-point">{{$data->commission_rate}}</span> %</span>
                                    <input type="hidden" value="0.5" class="setp" />
                                    {{--  <span class="percentage" id="J-text-percentage-multiple">{{number_format($data->commission_rate, 2)}}%</span> --}}
                                @endif
                            @endforeach

                            {{-- <a id="J-ex-link" data-url="{{ route('user-user-prize-sets.prize-set-detail') }}" href="{{ route('user-user-prize-sets.prize-set-detail') }}/1950" class="ex_link" target="_blank">奖金详情</a>--}}




                            <br />
                            <label class="text-title"></label>
                            <span id="J-angent-odd-tip3" class="tip" style="display:none;font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入整数比例</span>




                            <div class="panel-progress">
                                <div class="bar" style="width:100%;" id="J-bar-middle-multiple-commission">
                                    <div class="bar-ce"></div>
                                    <div class="sign-middle" id="J-multiple-commission-num-current">{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 0 }}</div>
                                </div>

                                <div class="sign-start" id="J-multiple-commission-num-min">{{ isset($iPlayerMinJcMultipleCommissionRate) ? $iPlayerMinJcMultipleCommissionRate : 0 }}</div>
                                <div class="sign-end" id="J-multiple-commission-num-max">{{ isset($iPlayerMaxJcMultipleCommissionRate) ? $iPlayerMaxJcMultipleCommissionRate : 0 }}</div>
                            </div>
                        </div>
                    </div>






                    <div class="row-mode mode-sport">
                        <div class="row-mode-title">
                            电子娱乐返点设置
                        </div>


                        @foreach($oUserCommissions as $data)
                            @if($data->type_id == SeriesSet::TYPE_ELECTRONIC)
                                <div class="row row-set-prize clearfix">
                                    <label class="text-title">{{$data->name}}</label>
                                    <input type="text" class="input ct-input-point w-1" value="{{$data->sub_commission_rate}}" name="commission_rate_{{$data->series_set_id}}" style="text-align:center;" />
                                    %
                                    &nbsp;
                                    <span class="tip">最高可分配 <span class="max-point">{{$data->commission_rate}}</span> %</span>
                                    <input type="hidden" value="0.1" class="setp" />
                                </div>
                            @endif
                        @endforeach

                    </div>




                
                
                <div class="row-control">
                    <input id="J-button-submit" class="btn btn-sbumit" type="submit" value=" 保存设置 " />
                </div>



            </div>
            </form>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
var global_high_point = [],
    global_high_hash = {};

@foreach($aDiffPrizes as $iPrizeGroup=>$num)
    global_high_point.push({group:{{$iPrizeGroup}},num:{{$num}}});
    global_high_hash[{{$iPrizeGroup}}] = {{$num}};
@endforeach

global_high_point.sort(function(a, b){
    return a['group'] - b['group'];
});
//console.log(global_high_hash);
</script>
<script>
(function(){
    var iCurrentPrizeGroup   = {{ $sCurrentUserPrizeGroup }},
        sCurrentUserPrizeGroup = {{ $sCurrentUserPrizeGroup }},
        iCurrentAgentPrizeGroup = {{ $sCurrentAgentPrizeGroup }},
        iMinPrizeGroup = {{ $foreverPrize }},
        iMaxPrizeGroup = {{ $iMaxPrizeGroup }},
        foreverPrize   = {{$foreverPrize}};

    var isForever = {{$isForever}};

    //奖金组范围
    var prizeBound = {'min':Number($('#J-input-prize-min').val()), 'max':Number($('#J-input-prize-max').val())};

    //Preload data
    updatePrize(iCurrentPrizeGroup);

    function updatePrize(num){
        //普通阶段只允许进行偶数调节
        if(num < foreverPrize){
            if(num%2 == 1){
                num = num - 1;
                $('#J-input-prize').val(num);
                select_help(num);

            }
        }
        select_help_none(num);
        prizeBound.min = Number($('#J-input-prize-min').val());
        prizeBound.max = Number($('#J-input-prize-max').val());
        var basePrize = 2000,
            rebate = (((iCurrentAgentPrizeGroup - num) / basePrize) * 100).toFixed(2),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = (100 - Number((num-prizeBound['min'])/bound*100)).toFixed(2),
            blnum = (100 - bl);

        $('#J-prize-num-current').text(num);
        $('#J-text-percentage').text(rebate + '%');
        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);


    }
    $('#J-input-prize').keyup(function(){
        var v = this.value.replace(/[^\d]/g, ''),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = 0,
            num = Number(v);
        if(num < 1000){
            return;
        }
        num = num > 1960 ? 1960 : num;
        if(num <= foreverPrize){
            num = foreverPrize;
        }else{
            if(!!!global_high_hash[''+num]){
                num = foreverPrize;
            }
        }

        this.value = num;
        updatePrize(num);

    }).blur(function(){
        var num = Number(this.value.replace(/[^\d]/g, '')),
            bound = prizeBound['max'] - prizeBound['min'];
        //num = num > prizeBound['max'] ? prizeBound['max'] : num;
        num = num < prizeBound['min'] ? prizeBound['min'] : num;
        this.value = num;
        updatePrize(num);
    });

    $('#J-por-high-cont').on('click', '.pro-set-fast', function(){
        var el = $(this),point = Number(el.attr('data-point'));
        $('#J-input-prize').val(point);
        updatePrize(point);
    });

    $('#J-button-reset').click(function(){
        $('#J-input-prize').val(sCurrentUserPrizeGroup);
        updatePrize(sCurrentUserPrizeGroup);
        select_help(sCurrentUserPrizeGroup);
    });

    //表单提交
    $('#J-button-submit').click(function(e){
        $('#J-form').submit();
    });




    $('#J-point-item-cont .item').click(function(e){
        var el = $(this),point = Number(el.attr('data-point'));
        if(!point){
            return;
        }
        select_help(point);
    });
    function select_help(point){
        var el = $('#J-item-' + point),items,CLS = 'item-select';
        if(el.size() < 1){
            return;
        }
        items = el.parent().find('.item');
        items.removeClass(CLS);
        el.addClass(CLS);
        $('#J-input-prize').val(point);
        $('#J-text-current-select').text(point);
        updatePrize(point);
        if(point >={{ $agent_min_high_grize_group}} && point != foreverPrize){
            $('#J-check-type-cont').show();
        }else{
            $('#J-check-type-cont').hide();
        }
        
    };
    function select_help_none(point){
        var el = $('#J-item-' + point),items,CLS = 'item-select';
        if(el.size() < 1){
            return;
        }
        items = el.parent().find('.item');
        items.removeClass(CLS);
        el.addClass(CLS);
        $('#J-input-prize').val(point);
        $('#J-text-current-select').text(point);
        if(point >= {{ $agent_min_high_grize_group}}  && point != foreverPrize){
            $('#J-check-type-cont').show();
        }else{
            $('#J-check-type-cont').hide();
        }
    }


    var pointTypeTab = new bomao.Tab({par:'#J-check-type-cont', triggers:'.field-type-switch li', panels:'.tip', eventType:'click', index:isForever});
    pointTypeTab.addEvent('afterSwitch', function(e, index){
        var me = this;
        $('#J-input-type-isforever').val(index);
    });

})();




    (function(){
        $('.ct-input-point').blur(function(){
            var el = $(this),
                min = 0,
                max = Number(el.parent().find('.max-point').text()),
                setp = Number(el.parent().find('.setp').val()),
                v = Number(this.value),
                varr,
                vpoint;
            v = isNaN(v) ? 0 : v;
            v = v.toFixed(6);
            vpoint = (v - Math.floor(v)).toFixed(2);
            //console.log(vpoint * 10 , setp * 10);
            if(vpoint != 0){
                if(vpoint * 100 < setp * 100){
                    v = Math.floor(v) + setp;
                }else if(vpoint * 100 > setp * 100){
                    if((vpoint * 100)%(setp * 100) != 0){
                        if(el.attr('name') == 'commission_rate_2' || el.attr('name') == 'commission_rate_3' || el.attr('name') == 'commission_rate_4'){
                            v = Math.floor(v) + Math.floor(vpoint * 10)/10;
                        }else{
                            v = Math.floor(v) + setp * 2;
                        }
                    }
                }
            }


            v = v < min ? min : v;
            v = v > max ? max : v;
            v = (Math.floor(Number(v)*10)/10).toFixed(1);
            this.value = v;
        });

    })();






</script>


@stop


