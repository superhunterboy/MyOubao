@extends('l.sports')


@section ('container')
@include('jc.header')

@if (isset($sJsonAllMatches))
<script>
    var all_matches = {{ $sJsonAllMatches }};
</script>
@endif

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


            <div class="game-cont clearfix">
                <div class="game-match game-match-{{$sTabKey}}" id="J-game-match">
                    {{--
                    <div class="row row-filter">
                        <span class="ft"><label><input type="checkbox" />热门</label></span>
                    </div>
                    --}}
                    <input type="hidden" value="{{$sTabKey}}" id="J-match-method-type-value" />


                    @include('adTemp.29')

                    <div class="row row-date-select">
                        <span class="tip-information">竞猜对象为全场90分钟(含伤停补时)的比分结果，不含加时赛及点球大战</span>
                        <div class="finished-match">
                            @if(isset($dBetDate))
                                <input class="finish-box" type="checkbox" checked="true" />
                            @else
                                <input class="finish-box" type="checkbox" />
                            @endif
                            <span>显示已截止比赛(<span>{{{ $iCountEndMatch }}}</span>)</span>
                        </div>
                        <div class="date-select-box">
                            <span class="current-date">
                                @if(isset($dBetDate))
                                {{{ $dBetDate }}}
                                @else
                                当前日期
                                @endif
                            </span>
                            <span class="arrow-box">
                                <span class="fa fa-sort-desc"></span>
                            </span>
                        </div>
                        <ul class="date-list close-list">
                            <li><a href='{{{ route('jc.match_list', [$oJcLottery->identifier, $sTabKey]) }}}'>当前日期</a></li>
                            <?php
                            $dCurrentDate = date('Y-m-d');
                            $iDateNum = 15;
                            $aDateList = [];
                            for($i=1;$i<=$iDateNum;$i++){
                                $aDateList[] = date('Y-m-d', strtotime("-{$i} days"));
                            }
                            ?>
                            @foreach($aDateList as $dDate)
                            <li><a href='{{{ route('jc.match_list', [$oJcLottery->identifier, $sTabKey, 'betDate' => $dDate]) }}}'>{{{ $dDate }}}</a></li>
                            @endforeach
                        </ul>
                    </div>

                    @include('jc.bettype.'.$sTabKey)




                    <div class="info-inner">
                        <div class="title"><i class="fa fa-info-circle"></i> 投注提示：</div>
                        <div class="text">
                            <ol>
                                <li>让球数：“+”号为客队让主队，“-”为主队让客队。</li>
                                <li>竞彩官方奖金计算：先计算单注奖金，再乘以倍数，得出最终的奖金。</li>
                                <li>投注选项中的数值为官方提供的实时投注奖金，仅供参考，奖金计算以投注时的奖金为准。</li>
                                <li>单场投注，最高奖金限额20万元；2-3场过关投注，最高奖金限额20万元；4-5场过关投注，最高奖金限额20万元；6场及以上过关投注，最高奖金限额40万元。以上奖金限额均包含倍数。</li>
                                <li>竞彩足球彩果，以比赛90分钟内比分（含伤停补时）结果为准。其中投注赛事取消、中断或改期，官方比赛彩果公布或确认取消将延后36小时，对应场次奖金派发或退款将同步延后处理；取消比赛的任何结果都算对，固定奖金按照1计算。</li>
                            </ol>
                        </div>
                    </div>
                </div>




                <div class="game-side game-side-{{$sTabKey}}" id="J-panel-main-side">
                    <div class="inner">
                        <div class="orders">
                            <div class="title">
                                <i class="fa fa-futbol-o"></i>比赛
                                <a id="J-button-clearall" class="ct-clearall ct-clearall-disabled" href="#">清空</a>
                            </div>
                            <div class="list" id="J-order-list-outer">
                                <script type="text/template" id="J-orders-row-tpl">
                                    <div class="o-match o-match-<#=matchid#> clearfix">
                                        <div class="o-title">
                                            <span class="time"><#=time#></span>
                                            <span class="vs"><#=team1#> - <#=team2#></span>
                                            <span class="dan <#=dancls#>" data-matchid="<#=matchid#>">胆</span>
                                        </div>
                                        <div class="its">
                                            <#=items#>
                                        </div>
                                        <span class="o-delete">
                                            <i class="fa fa-times-circle" data-matchid="<#=matchid#>"></i>
                                        </span>
                                    </div>
                                </script>
                                <div class="list-inner" id="J-order-list-cont">



                                </div>
                            </div>
                        </div>

                        <div class="order-type" id="J-order-type">
                            <div class="title"><i class="fa fa-building-o"></i>过关方式</div>
                            <ul class="tab clearfix">
                                <li class="active">自由过关</li>
                                <li>组合过关</li>
                            </ul>
                            <div class="list list-free">

                            </div>
                            <div class="list list-group">

                            </div>
                        </div>

                        <div class="order-confirm">
                            <div class="title"><i class="fa fa-check-square"></i>确认投注</div>
                            <div class="conf-inner">
                                <p class="p-row p-row-statics">
                                    场数：<span id="J-times-match">0</span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;
                                    注数：<span id="J-bets-num">0</span>
                                </p>
                                <p class="p-row p-row-multiple">购买 
                                    <span class="multiple-cont">
                                        <span class="multiple-ct-reduce" id="J-ct-multiple-reduce">-</span>
                                        <input id="J-input-multiple" class="input-num" type="text" value="1" />
                                        <span class="multiple-ct-add" id="J-ct-multiple-add">+</span>
                                    </span>
                                    
                                 倍 </p>

                                <p class="p-row p-row-statics">
                                    <span class="num" id="J-money-num">0.00</span> 元 
                                </p>
                                <p style="display:none">
                                    可用余额: {{ number_format($oAccount->available, 2) }} 元
                                </p>
                                <p class="p-row p-row-info">
                                    理论最高奖金: <span id="J-prize-max">0.00</span> 元
                                    <a class="link-detail">查看明细</a>
                                </p>


                                <form action="{{ route('jc.confirm') }}" method="post" id="J-form" >
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}" />
                                    <input name="gameId" type="hidden" value="{{ $oJcLottery->id }}" />
                                    <input name="gameData" type="hidden" value="" />
                                    <input name="gameExtra" type="hidden" value="" />
                                    <input name="betTimes" type="hidden" value="1" />
                                    @if (isset($oMethodGroup))
                                    <input name="methodGroupId" type="hidden" value="{{ $oMethodGroup->id }}" />
                                    @endif
                                    <input name="is_group_buy" type="hidden" value="0" />
                                    <input name="bind_group_id" type="hidden" value="{{ Input::get('bind_group_id') }}" />
                                    <p class="p-row p-row-button">
                                        <input type="button" class="btn-submit" id="J-button-submit" value=" 立即投注 " />
                                        <input type="button" class="btn-submit" id="J-button-submit-group" value=" 发起合买 " />
                                    </p>
                                </form>

                                <br />
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@include('w.footer')
@stop



@section('end')
    <script type="text/javascript">
    var global_sports_config = {};
    var global_sports_config_max = {{ json_encode($aGameConfigs) }};
    (function(h){
        var c = [];
        @foreach ($aWayList as $way)
            c.push(['{{ $way->identifier }}','{{ $way->name }}'@if ($way->rule),{{ $way->rule }}@endif]);            
        @endforeach
        h['option'] = c;
    })(global_sports_config);
    </script>

    
    {{ script('game-sports-init') }}

@stop









