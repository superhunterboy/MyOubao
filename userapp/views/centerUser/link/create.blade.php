@extends('l.home')

@section('title')
    链接开户
@stop

@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content .row {
        padding: 0 0 10px 0;
        margin: 10px 0 0 0;
    }
    .page-content .row-nav {
        padding: 0 35px;
        margin-bottom: 10px;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #E6E6E6;
    }
    .page-content .row-nav ul{
        width: 176px;
        height: 38px;
        border-radius: 4px;
        background-color: #31CEAC;
        padding: 5px 10px;
        font-size: 13px;
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
                @include('w.uc-menu-proxy')
            </div>


            <div class="page-content-inner page-content-inner-bg">
                <form action="{{ route('user-links.create') }}" method="post" id="J-form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="_random" value="{{ createRandomStr() }}" />
                    <input type="hidden" name="is_agent" id="J-input-userType" value="{{ Input::old('is_agent', 1) }}" />
                    <div class="row row-nav clearfix">
                        {{-- <label class="text-title">开户方式</label> --}}
                        <ul class="field field-type-switch">
                            <li><a href="{{ route('users.accurate-create') }}">人工开户</a></li>
                            <li class="current"><a href="{{ route('user-links.create') }}">链接开户</a></li>
                        </ul>
                    </div>

                    <div class="row clearfix" id="J-user-type-tab" style="display:none;">
                        <label class="text-title">选择账户类型</label>
                        <ul class="field field-type-switch field-user-type">
                            <li class="current"><a class="item-player" href="javascript:;">玩家账号</a></li>
                            <li><a class="item-agent" href="javascript:;">代理账号</a></li>
                        </ul>
                        <input class="user-type-cont" type="hidden" />
                        <input class="user-type-cont" type="hidden" />
                    </div>


                    <div class="row clearfix">
                        <label class="text-title">链接有效期</label>
                        <select id="J-select-link-valid" style="display:none;" name="valid_days">
                             <option value="">请选择</option>
                             <option value="1" {{ Input::old('valid_days') == 1 ? 'selected' : '' }}>1天</option>
                            <option value="7" {{ Input::old('valid_days') == 7 ? 'selected' : '' }}>7天</option>
                             <option value="30" {{ Input::old('valid_days') == 30 ? 'selected' : '' }}>30天</option>
                             <option value="90" {{ Input::old('valid_days') == 90 ? 'selected' : '' }}>90天</option>
                            <option value="0" {{ Input::old('valid_days') === '0' ? 'selected' : '' }} selected="selected">永久有效</option>
                        </select>
                    </div>



                    <div class="row clearfix">
                        <label class="text-title">推广渠道</label>
                        <input type="text" class="input w-3" value="" id="J-input-custom" name="channel" placeholder="如:QQ群推广" />
                    </div>


                    <div class="row clearfix">
                        <label class="text-title">客服QQ</label>
                        <input type="text" name="agent_qqs[]" class="input w-2 agentQQ" value="{{ isset($aAgentQQ[0]) ? $aAgentQQ[0] : '' }}" placeholder="QQ1" />
                        <input type="text" name="agent_qqs[]" class="input w-2 agentQQ" value="{{ isset($aAgentQQ[1]) ? $aAgentQQ[1] : '' }}" placeholder="QQ2" />
                        <input type="text" name="agent_qqs[]" class="input w-2 agentQQ" value="{{ isset($aAgentQQ[2]) ? $aAgentQQ[2] : '' }}" placeholder="QQ3" />
                        <input type="text" name="agent_qqs[]" class="input w-2 agentQQ" value="{{ isset($aAgentQQ[3]) ? $aAgentQQ[3] : '' }}" placeholder="QQ4" />
                        <br />
                        <div class="row-row-tip" style="padding-left:420px;">为方便客户与您联系，建议您填写真实的推广QQ并开通临时会话功能（此QQ会显示在该链接开户页面）。</div>
                    </div>




                <div class="row-mode mode-lottery">
                    <div class="row-mode-title">
                        彩票奖金组设置
                    </div>
                    <div class="row row-set-prize clearfix">
                        <input type="hidden" value="{{ $iAgentMinPrizeGroup }}" id="J-input-prize-min" />
                        <input type="hidden" value="{{ $iAgentMaxPrizeGroup }}" id="J-input-prize-max" />
                        <label class="text-title">设置奖金组</label>
                        <input type="text" class="input w-1" id="J-input-prize" name="prize_group" value="{{ $iAgentMaxPrizeGroup }}" style="text-align:center;" />
                        &nbsp;&nbsp;
                        <span class="tip-text">预计平均返点率</span>
                        <span class="percentage" id="J-text-percentage">0.00%</span>
                        <a id="J-ex-link" data-url="{{ route('user-user-prize-sets.prize-set-detail') }}" href="{{ route('user-user-prize-sets.prize-set-detail') }}/1950" class="ex_link" target="_blank">奖金详情</a>
                        <br />
                        <label class="text-title"></label>
                        <span id="J-angent-odd-tip" class="tip" style="display:none;font-size: 12px;padding-top:5px;color: #A3A3A3;">请输入偶数奖金组</span>
                        <div class="panel-progress">
                            <div class="bar" style="width:100%;" id="J-bar-middle">
                                <div class="bar-ce"></div>
                                <div class="sign-middle" id="J-prize-num-current">{{ $iAgentMaxPrizeGroup }}</div>
                            </div>

                            <div class="sign-start" id="J-prize-num-min">{{ $iAgentMinPrizeGroup }}</div>
                            <div class="sign-end" id="J-prize-num-max">{{ $iAgentMaxPrizeGroup }}</div>
                        </div>
                    </div>
            </div>






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
                                <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="{{ isset($iPlayerMinJcSingleCommissionRate) ? $iPlayerMinJcSingleCommissionRate : 0 }}" style="text-align:center;" />
                                %
                                &nbsp;
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
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
                                <input type="text" class="input ct-input-point w-1" name="commission_rate_{{$data->series_set_id}}" value="{{ isset($iPlayerMinJcMultipleCommissionRate) ? $iPlayerMinJcMultipleCommissionRate : 0 }}" style="text-align:center;" />
                                %
                                &nbsp;
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
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
                                <input type="text" class="input ct-input-point w-1" value="0" name="commission_rate_{{$data->series_set_id}}" style="text-align:center;" />
                                %
                                &nbsp;
                                <span class="tip">最高可分配 <span class="max-point">{{number_format($data->commission_rate, 2)}}</span> %</span>
                                <input type="hidden" value="0.1" class="setp" />
                            </div>
                        @endif
                    @endforeach

                </div>






                    <div class="row-control">
                        <input id="J-button-submit" class="btn btn-sbumit" type="submit" value=" 生成链接 " />
                    </div>
                </form>






            </div>
        </div>


        <div class="g_main" style="min-height: 250px;">
                <a name="list"></a>
                <table width="100%" class="table table-toggle link-table" id="J-table">
                    <thead>
                        <tr>
                            <th>投放渠道</th>
                            <th>开户类型</th>
                            <th>奖金组</th>
                            <th>注册人数</th>
                            <th>有效期</th>
                            <th>复制链接</th>
                            <th>生成时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                        <tr>
                            <td>{{ $data->channel }}</td>
                            <td>{{ $data->{$aListColumnMaps['is_agent']} }}</td>
                            <td>{{ $data->link_prize_group }}</td>
                            <td><a href="{{ route('user-link-users.index', ['register_link_id' => $data->id]) }}">{{ $data->created_count }}</a></td>
                            <td>{{ $data->friendly_expired_at }}</td>
                            <td style="padding-left:80px;" width="350">
                                <input value="{{ $data->url }}" type="text" class="input" />
                                <a href="#" class="tb-inner-btn tb-copy">复制</a>
                            </td>
                            <td>
                                {{ $data->created_at }}
                            </td>
                            <td style="text-align:left">

                                <a class="tb-inner-btn" href="{{ route('user-links.view', $data->id) }}">详情</a>
                                @if($data->status == 0)
                                <a class="tb-inner-btn confirmDelete" href="javascript:void(0);" url="{{ route('user-links.destroy', $data->id) }}">删除</a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>



                    {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
        </div>



    </div>


    @include('w.footer')
@stop



@section('end')
@parent
{{ script('ZeroClipboard')}}
<script>
(function(){
    //有效期
    var linkValid = new bomao.Select({realDom:'#J-select-link-valid',cls:'w-2'});
    var iCurrentPrizeGroup   = {{ $iCurrentUserPrizeGroup }};
        iPlayerMinPrizeGroup = {{ $iPlayerMinPrizeGroup }};
        iPlayerMaxPrizeGroup = {{ $iPlayerMaxPrizeGroup }};
        iAgentMinPrizeGroup  = {{ $iAgentMinPrizeGroup }};
        iAgentMaxPrizeGroup  = {{ $iAgentMaxPrizeGroup }};

    /*================JC=================*/
    var iCurrentSingleJcCommission   = {{ isset($iCurrentUserSingleJcCommission) ? $iCurrentUserSingleJcCommission : 0 }};
        iPlayerMinSingleJcCommission = {{ isset($iPlayerMinSingleJcCommission) ? $iPlayerMinSingleJcCommission : 0 }};
        iPlayerMaxSingleJcCommission = {{ isset($iPlayerMaxSingleJcCommission) ? $iPlayerMaxSingleJcCommission : 0 }};
        iAgentMinSingleJcCommission  = {{ isset($iAgentMinSingleJcCommission) ?  $iAgentMinSingleJcCommission : 0}};
        iAgentMaxSingleJcCommission  = {{ isset($iAgentMaxSingleJcCommission) ? $iAgentMaxSingleJcCommission : 0 }};
    var iCurrentMultipleJcCommission   = {{ isset($iCurrentUserMultipleJcCommission) ?  $iCurrentUserMultipleJcCommission : 0}};
        iPlayerMinMultipleJcCommission = {{ isset($iPlayerMinMultipleJcCommission) ? $iPlayerMinMultipleJcCommission : 0}};
        iPlayerMaxMultipleJcCommission = {{ isset($iPlayerMaxMultipleJcCommission) ?  $iPlayerMaxMultipleJcCommission : 0}};
        iAgentMinMultipleJcCommission  = {{ isset($iAgentMinMultipleJcCommission) ?  $iPlayerMaxMultipleJcCommission : 0}};
        iAgentMaxMultipleJcCommission  = {{ isset($iAgentMaxMultipleJcCommission) ? $iAgentMaxMultipleJcCommission : 0 }};
    /*================JC=================*/

    var isAddAgent = false;

    //QQ号码
    $('.agentQQ').keyup(function(event) {
        var v = this.value;
        v = v.replace(/[^\d]/g, '');
        this.value = v.substr(0, 20);
    });



    //账户类型切换
    var typeTab = new bomao.Tab({par:'#J-user-type-tab', triggers:'.field-user-type li', panels:'.user-type-cont', eventType:'click'});
    typeTab.addEvent('afterSwitch', function(e, index){
        $('#J-input-userType').val(index);
        isAddAgent = index == 0 ? false : true;
        switch(index)
        {
            case 0: //player
                $("#J-input-prize-min").val(iPlayerMinPrizeGroup);
                $("#J-input-prize-max").val(iPlayerMaxPrizeGroup);
                $("#J-prize-num-min").html(iPlayerMinPrizeGroup);
                $("#J-prize-num-max").html(iPlayerMaxPrizeGroup);
                updatePrize(iPlayerMaxPrizeGroup);
                $('#J-input-prize').val(iPlayerMaxPrizeGroup);

                $('#J-angent-odd-tip').css('display', 'none');

                /*================JC=================*/
                $("#J-input-single-commission-min").val(iPlayerMinSingleJcCommission);
                $("#J-input-single-commission-max").val(iPlayerMaxSingleJcCommission);
                $("#J-single-commission-num-min").html(iPlayerMinSingleJcCommission);
                $("#J-single-commission-num-max").html(iPlayerMaxSingleJcCommission);

                $("#J-input-multiple-commission-min").val(iPlayerMinMultipleJcCommission);
                $("#J-input-multiple-commission-max").val(iPlayerMaxMultipleJcCommission);
                $("#J-multiple-commission-num-min").html(iPlayerMinMultipleJcCommission);
                $("#J-multiple-commission-num-max").html(iPlayerMaxMultipleJcCommission);
                updateSingleCommission(iPlayerMaxSingleJcCommission);
                updateMultipleCommission(iPlayerMaxMultipleJcCommission);
                $('#J-input-single-commission').val(iPlayerMaxSingleJcCommission);
                $('#J-input-multiple-commission').val(iPlayerMaxMultipleJcCommission);
                $('#J-angent-odd-tip2').css('display', 'none');
                $('#J-angent-odd-tip3').css('display', 'none');

                /*================JC=================*/
            break;

            case 1: //agent
                $("#J-input-prize-min").val(iAgentMinPrizeGroup);
                $("#J-input-prize-max").val(iAgentMaxPrizeGroup);
                $("#J-prize-num-min").html(iAgentMinPrizeGroup);
                $("#J-prize-num-max").html(iAgentMaxPrizeGroup);
                updatePrize(iAgentMaxPrizeGroup);
                $('#J-input-prize').val(iAgentMaxPrizeGroup);

                $('#J-angent-odd-tip').css('display', 'block');

                /*================JC=================*/
                $("#J-input-single-commission-min").val(iPlayerMinSingleJcCommission);
                $("#J-input-single-commission-max").val(iPlayerMaxSingleJcCommission);
                $("#J-single-commission-num-min").html(iPlayerMinSingleJcCommission);
                $("#J-single-commission-num-max").html(iPlayerMaxSingleJcCommission);

                $("#J-input-multiple-commission-min").val(iPlayerMinMultipleJcCommission);
                $("#J-input-multiple-commission-max").val(iPlayerMaxMultipleJcCommission);
                $("#J-multiple-commission-num-min").html(iPlayerMinMultipleJcCommission);
                $("#J-multiple-commission-num-max").html(iPlayerMaxMultipleJcCommission);
                updateSingleCommission(iPlayerMaxSingleJcCommission);
                updateMultipleCommission(iPlayerMaxMultipleJcCommission);
                $('#J-input-single-commission').val(iPlayerMaxSingleJcCommission);
                $('#J-input-multiple-commission').val(iPlayerMaxMultipleJcCommission);
                $('#J-angent-odd-tip2').css('display', 'block');
                $('#J-angent-odd-tip3').css('display', 'block');
                /*================JC=================*/
            break;
        }
    });

    //奖金组范围
    var prizeBound = {'min':Number($('#J-input-prize-min').val()), 'max':Number($('#J-input-prize-max').val())};
    
    updatePrize(iAgentMaxPrizeGroup);

    /*================JC=================*/
    var singleCommissionBound = {'min':Number($('#J-input-single-commission-min').val()), 'max':Number($('#J-input-single-commission-max').val())};
    var multipleCommissionBound = {'min':Number($('#J-input-multiple-commission-min').val()), 'max':Number($('#J-input-multiple-commission-max').val())};

    updateSingleCommission(iPlayerMaxSingleJcCommission);
    updateMultipleCommission(iPlayerMaxMultipleJcCommission);
    /*================JC=================*/

    function updatePrize(num){
        prizeBound.min = Number($('#J-input-prize-min').val());
        prizeBound.max = Number($('#J-input-prize-max').val());
        var basePrize = 2000,
            rebate = (((iCurrentPrizeGroup - num) / basePrize) * 100).toFixed(2),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = (100 - Number((num-prizeBound['min'])/bound*100)).toFixed(2);
        $('#J-bar-middle').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-prize-num-current').css('right', -30);
        }else{
            $('#J-prize-num-current').css('right', 0);
        }
        $('#J-prize-num-current').text(num);
        $('#J-text-percentage').text(rebate + '%');
        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }

    /*================JC=================*/
    function updateSingleCommission(num){
        singleCommissionBound.min = Number($('#J-input-single-commission-min').val());
        singleCommissionBound.max = Number($('#J-input-single-commission-max').val());
        var rebate = Number(num).toFixed(2),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'],
            bl = (100 - Number(num/bound*100)).toFixed(2);

        $('#J-bar-middle-single-commission').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-single-commission-num-current').css('right', -30);
        }else{
            $('#J-single-commission-num-current').css('right', 0);
        }
        $('#J-single-commission-num-current').text(num);
        $('#J-text-percentage-single').text(rebate + '%');
//        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }

    function updateMultipleCommission(num){
        multipleCommissionBound.min = Number($('#J-input-multiple-commission-min').val());
        multipleCommissionBound.max = Number($('#J-input-multiple-commission-max').val());
        var rebate = Number(num).toFixed(2),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'],
            bl = (100 - Number(num/bound*100)).toFixed(2);
        $('#J-bar-middle-multiple-commission').css('width',  (100 - bl) + '%');
        if((100 - bl) <= 0){
            $('#J-multiple-commission-num-current').css('right', -30);
        }else{
            $('#J-multiple-commission-num-current').css('right', 0);
        }
        $('#J-multiple-commission-num-current').text(num);
        $('#J-text-percentage-multiple').text(rebate + '%');
//        $('#J-ex-link').attr('href', $('#J-ex-link').attr('data-url') + '/' + num);
    }
    /*================JC=================*/
    $('#J-input-prize').keyup(function(){
        var v = this.value.replace(/[^\d]/g, ''),
            bound = prizeBound['max'] - prizeBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = prizeBound['max'];
        }


        if(isAddAgent && num > 1000){
            if(num%2 == 1){
                num = num - 1;
            }
            if(num < prizeBound['min']){
                num = prizeBound['min'];
            }
        }


        if(num > prizeBound['max']){
            num = prizeBound['max'];
        }
        this.value = num;
        updatePrize(num);

    }).blur(function(){
        var num = Number(this.value.replace(/[^\d]/g, '')),
            bound = prizeBound['max'] - prizeBound['min'];
        num = num > prizeBound['max'] ? prizeBound['max'] : num;
        num = num < prizeBound['min'] ? prizeBound['min'] : num;
        this.value = num;
        updatePrize(num);
    });
    
    /*===========================JC===========================*/
    $('#J-input-single-commission').keyup(function(){
        var v = this.value.replace(/[^\d\.]/g, ''),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = singleCommissionBound['min'];
        }
        if(num <= singleCommissionBound['min']){
            num = singleCommissionBound['min'];
        }
        if(num >= singleCommissionBound['max']){
            num = singleCommissionBound['max'];
        }
        if (v.length > 0 && v.indexOf('.') == v.length - 1){
            this.value = v;
        }else{
            num = Math.round(num*10)/10;
            this.value = num;
        }
        updateSingleCommission(num);
    }).blur(function(){
        var num = Number(this.value.replace(/[^\d\.]/g, '')),
            bound = singleCommissionBound['max'] - singleCommissionBound['min'];
        num = num > singleCommissionBound['max'] ? singleCommissionBound['max'] : num;
        num = num < singleCommissionBound['min'] ? singleCommissionBound['min'] : num;
        num = Math.round(num*10)/10;
        this.value = num;
        updateSingleCommission(num);
    });;
    $('#J-input-multiple-commission').keyup(function(){
        var v = this.value.replace(/[^\d\.]/g, ''),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'],
            bl = 0,
            num = Number(v);
        if(!num){
            num = multipleCommissionBound['min'];
        }
        if(num <= multipleCommissionBound['min']){
            num = multipleCommissionBound['min'];
        }
        if(num >= multipleCommissionBound['max']){
            num = multipleCommissionBound['max'];
        }
        if (v.length > 0 && v.indexOf('.') == v.length - 1){
            this.value = v;
        }else{
            num = Math.round(num*10)/10;
            this.value = num;
        }
        updateMultipleCommission(Number(num));

    }).blur(function(){
        var num = Number(this.value.replace(/[^\d\.]/g, '')),
            bound = multipleCommissionBound['max'] - multipleCommissionBound['min'];
        num = num > multipleCommissionBound['max'] ? multipleCommissionBound['max'] : num;
        num = num < multipleCommissionBound['min'] ? multipleCommissionBound['min'] : num;
        num = Math.round(num*10)/10;
        updateMultipleCommission(num);
    });
    /*===========================JC===========================*/

    //表单提交
    $('#J-button-submit').click(function(e){
        if(linkValid.getValue() == ''){
            alert('请选择链接有效期');
            return false;
        }
        $('#J-form').submit();
    });



})();





//管理链接
(function($){
    var table = $('#J-table');
    table.find('.agent-link-name').click(function(e){
        var el = $(this),
            id = $.trim(el.attr('data-id')),
            ico = el.find('i');
        if(ico.hasClass('ico-fold')){
            table.find('.ico-unfold').removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-item').addClass('table-tr-hidden');
            table.find('.table-tr-pid-' + id).removeClass('table-tr-hidden');
            //table.find('.ico-fold').replaceClass('ico-unfold', 'ico-fold');
            ico.removeClass('ico-fold').addClass('ico-unfold');
        }else{
            ico.removeClass('ico-unfold').addClass('ico-fold');
            table.find('.table-tr-pid-' + id).addClass('table-tr-hidden');
        }
        e.preventDefault();
    });
    $('.confirmDelete').click(function(event) {
        var url = $(this).attr('url');
        if (confirm('确定关闭该开户链接？')) {
            location.href = url;
        }
    });
})(jQuery);




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






//复制链接
(function($){
  ZeroClipboard.setMoviePath('/assets/js/ZeroClipboard.swf');
  var tip = new bomao.Tip({cls:'j-ui-tip-r j-ui-tip-success'});
  var clip_link,table = $('#J-table'),timer,trs = table.find('tr'),
    fn = function(client){
      var el = $(client.domElement),input = el.parent().find('.input').eq(0),value = $.trim(input.val());
      client.setText(value);
      input.focus().select();
      tip.setText('已复制');
      tip.show(-70, -3, input);

      clearTimeout(timer);
      timer = setTimeout(function(){
        tip.hide();
      }, 2000);

      trs.removeClass('tr-selected');
      el.parents('tr').addClass('tr-selected');
    };


  table.find('.tb-copy').each(function(){
   clip_link = new ZeroClipboard.Client();
   clip_link.setCSSEffects( true );
   clip_link.addEventListener( "mouseUp", fn);
   clip_link.glue(this);
  });








})(jQuery);

</script>
@stop


