@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner match-result">
            <div class="line-list-top"></div>
            <div class="nav-cont">竞彩 竞彩胜平负   奖金评测详情</div>
            <div class="bet-confirm">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="100">场次</th>
                            <th width="100">赛事</th>
                            <th width="150">比赛时间</th>
                            <th width="300">主队VS客队</th>
                            <th width="150">玩法</th>
                            <th>投注内容</th>
                            <th width="50">胆</th>
                        </tr>
                    </thead>
                    <tbody id="J-przie-match-body">
                        <script type="text/template" id="J-przie-match-row">
                            <tr class="przie-match-<#=match_id#>">
                            </tr>
                        </script>

                        <script type="text/template" id="J-prize-match-info-row">
                            <td rowspan="<#=row_num#>"><#=match_num#></td>
                            <td rowspan="<#=row_num#>"><#=match_name#></td>
                            <td rowspan="<#=row_num#>"><#=match_time#></td>
                            <td rowspan="<#=row_num#>">
                                <span class="team team-a"><#=match_team1#></span>
                                <span class="ico-match-ball">VS</span>
                                <span class="team team-a"><#=match_team2#></span>
                            </td>
                        </script>

                        <script type="text/template" id="J-prize-match-play-row">
                            <td><#=play#></td>
                            <td>
                                <div class="col-order-table-content">
                                </div>
                            </td>
                        </script>

                        <script type="text/template" id="J-prize-match-dan-row">
                            <td rowspan="<#=row_num#>"><#=match_dan#></td>
                        </script>
                    </tbody>
                </table>

                <div class="bet-statics">
                    过关方式:<b class="type c-red" id="J-detail-type"></b>&nbsp;&nbsp;&nbsp;&nbsp;
                    倍数:<span class="num" id="J-detail-multiple"></span>倍&nbsp;&nbsp;&nbsp;&nbsp;
                    注数:<span class="num" id="J-detail-betnum"></span>注&nbsp;&nbsp;&nbsp;&nbsp;
                    金额:<span class="num" id="J-detail-amount"></span>元
                </div>
                
                <div class="bet-detail-tip">
                    注：</br>
                    1、理论奖金是根据您选择的即时最小和最大参考赔率计算出大概奖金范围，仅供参考。</br>
                    2、最终实际奖金派发以票样的投注赔率计算为准。
                </div>

                <table class="table table-prize-list">
                    <thead>
                        <tr>
                            <th width="100">命中场数</th>
                            <th class="prize-bet-num">中奖注数</th>
                            <th width="100">倍数</th>
                            <th>奖金范围</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th class="multiple_th"></th>
                            <th>
                                <span class="prize-detail-value">最小奖金</span>
                                &nbsp;&nbsp;~&nbsp;&nbsp;
                                 <span class="prize-detail-value">最大奖金</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="J-prize-detail-body">
                        <script type="text/template" id="J-przie-detail-row">
                            <tr class="prize-detail-<#=right_num#>">
                                <td><#=right_num#></td>
                            </tr>
                        </script>

                        <script type="text/template" id="J-przie-detail-type-col">
                            <td><#=bet_num#>注</td>
                        </script>

                        <script type="text/template" id="J-przie-detail-multiple-col">
                            <td><#=multiple#></td>
                            <td>
                                <span class="prize-detail-value">
                                    <span class="prize-value"><#=prize_min#></span>元&nbsp;&nbsp;[<span class="detail-list-bt" data-value="min-<#=right_num#>">明细</span>]
                                </span>
                                <span>&nbsp;&nbsp;~&nbsp;&nbsp;</span>
                                <span class="prize-detail-value">
                                    <span class="prize-value"><#=prize_max#></span>元&nbsp;&nbsp;[<span class="detail-list-bt" data-value="max-<#=right_num#>">明细</span>]
                                </span>
                            </td>
                        </script>
                    </tbody>
                </table>

                <table class="table table-prize-detail" style="display:none">
                    <thead>
                        <tr>
                            <th width="100">过关方式</th>
                            <th width="100">中奖注数</th>
                            <th>中奖明细</th>
                        </tr>
                    </thead>
                    <script type="text/template" id="J-prize-detail-list-tr">
                        <tr class="prize-detail-type-<#=type_char#>">
                        </tr>
                    </script>

                    <script type="text/template" id="J-prize-detail-list-row">
                        <td rowspan="<#=bet_num#>"><#=type#></td>
                        <td rowspan="<#=bet_num#>"><#=bet_num#></td>
                    </script>

                    <script type="text/template" id="J-prize-detail-list-content">
                        <td><#=detail_content#></td>
                    </script>
                    <tbody id="J-prize-detail-list">

                    </tbody>
                </table>

            </div>
            <div class="list-tab">
            </div>

        </div>


    </div>
</div>
</div>
@include('w.footer-v4')
@stop




@section('end')
@parent
    <script type="text/javascript">
        (function($, host){
            var gameCase = new bomao.SportsGame();
            gameCase.initPrizeDetailPage();

            var prize_detail_value = $('.prize-detail-value');
            prize_detail_value.on('click', '.detail-list-bt', function(){
                var parameter_arr = $(this).attr('data-value').split('-');

                $('.detail-list-bt').html("明细");

                if($(this).hasClass('detail-bt-active')){
                    $(this).removeClass('detail-bt-active');
                    $(this).html("明细");
                    $('.table-prize-detail').hide();
                }else{
                    $('.detail-list-bt').removeClass('detail-bt-active');
                    $(this).addClass('detail-bt-active');
                    $(this).html("收起");
                    $('.table-prize-detail').show();
                }

                gameCase.getPrizeDetailTableList(Number(parameter_arr[1]) , parameter_arr[0]);
            });
        })(jQuery, bomao);
    </script>
@stop





















