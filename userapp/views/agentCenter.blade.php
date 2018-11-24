@extends('l.home')

@section('title')
    代理首页
@stop

@section ('styles')
@parent
    
@stop

@section ('container')

@include('w.header')




<div class="main">
    <div class="g_main clearfix">



    <div class="main-chart clearfix">
            <div class="chart-cont">
                <div class="title">团队投注额走势图</div>
                <div class="chart-cont-inner">
                    <div class="chart-table" id="J-chart-table">
                </div>
                </div>
            </div>

            <div class="info-cont">
                <div class="inner">
                    <div class="title clearfix">
                        <span class="text">本月收入</span>
                        <span class="tip">（更新自{{ $aCommissionAndProfit['cached_before_minutes'] }} 分钟前）</span>
                        <a href="{{ route('user-profits.index') }}" class="btn">盈亏</a>
                        <a href="{{ route('user-transactions.index') }}" class="btn">报表</a>
                    </div>
                    <ul class="list clearfix">
                        <li class="li-1">
                            <div class="li-inner clearfix">
                                <div class="text">团队总投注额</div>
                                <div class="money"><span class="num">{{$aCommissionAndProfit['team_turnover']}}</span> 元</div>
                            </div>
                        </li>
                        <li class="li-2">
                            <div class="li-inner clearfix">
                                <div class="text">
                                    团队净盈亏额
                                </div>
                                <div class="money">
                                    <span class="num">
                                        @if ($aCommissionAndProfit['team_profit'] >= 0)
                                            +
                                        @endif
                                        {{$aCommissionAndProfit['team_profit']}}
                                    </span> 元
                                </div>
                            </div>
                        </li>
                        <li class="li-3">
                            <div class="text">返点收入</div>

                            <div class="money"><span class="num">{{$aCommissionAndProfit['commission']}}</span> 元</div>
                        </li>
                        {{--
                        <li>
                            <div class="text">佣金收入</div>
                            <div class="money"><span class="num">{{$aCommissionAndProfit['commission']}}</span> 元</div>
                        </li>
                        --}}

                        @if ($agent_type == 2)
                        <li class="li-4">
                            <div class="text">
                                预计分红
                                <span class="progress"></span> {{$aCommissionAndProfit['bonus_percents']}}%
                            </div>

                            <div class="money"><span class="num">{{$aCommissionAndProfit['bonus']}}</span> 元</div>
                        </li>

                        @endif

                    </ul>




                    @if(Session::get('show_overlimit'))
                    <div class="limit-cont">
                        <div class="title-2">
                            可用高点限额
                        </div>
                        <div class="limit">
                            <ul class="clearfix">
                                @foreach ($aOverLimits as $prizeGroup => $aData)
                                <li>
                                    <span class="num">{{$prizeGroup}}</span>
                                    <p class="bl">{{$aData['used_num']}}/{{$aData['limit_num']}}</p>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif





                </div>





            </div>

        </div>




        <div class="main-content clearfix" style="padding-top: 10px;">
            <div class="layout-left" style="padding: 10px 0 0 20px;">


                <div class="ranking" id="J-ranking-tab">
                    <span class="hidden"></span>
                    <span class="hidden"></span>
                    <span class="hidden"></span>
                        <div class="title">
                            <span class="text">本月排名</span>
                            <span class="tab">
                                <span class="first current">投注额</span><span>开户数</span><span class="last">净盈亏</span>
                            </span>
                        </div>
                        <div class="table-cont">
                            <table width="100%" class="table-rank">
                                <thead>
                                    <tr>
                                        <th width="100">用户名</th>
                                        <th width="100">用户属性</th>
                                        <th width="100">奖金组</th>
                                        <th width="100">团队人数</th>
                                        <th width="100">团队余额</th>
                                        <th width="100"><span id="J-rank-data-title">投注额</span></th>
                                    </tr>
                                </thead>
                                <tbody id="J-tbody-rank">

                                </tbody>
                            </table>

                        </div>
                </div>








            {{--
            @if(Session::get('show_overlimit'))
                <div class="limit">
                    <div class="title">
                        <span class="text">可用高点限额</span>
                    </div>
                    <div class="cont">
                        <ul class="list">
                             @foreach ($aOverLimits as $prizeGroup => $aData)

                            @if($aData['used_num'] != 0)
                            <li class="empty color-{{$prizeGroup}}">
                            @else
                            <li class="color-{{$prizeGroup}}">
                            @endif
                                <a href="/my-overlimit-quotas/index?prize_group={{$prizeGroup}}">
                                <div class="num">
                                                    <span class="
                                                       @if($aData['used_num'] == $aData['limit_num'])
                                                        gray
                                                       @else
                                                        num
                                                       @endif


                                                       "><i class="fa fa-diamond"></i> {{$prizeGroup}}</span></div>
                                <div class="pre"><span class="
                                                       @if($aData['used_num'] == $aData['limit_num'])
                                                        gray
                                                       @else
                                                        red
                                                       @endif
                                                       ">
                                                       <span class="sm-num">{{$aData['used_num']}}</span>
                                                        / 
                                                        <span class="sm-num">{{$aData['limit_num']}}</span>
                                                        </div>
                                </a>
                            </li>
                            @endforeach

                        </ul>
                    </div>
                </div>
            @endif
            --}}




            </div>


            
            <div class="layout-right">







                <div class="team-my" id="J-team-my-tab">
                    <span class="hidden"></span>
                    <span class="hidden"></span>
                    <span class="hidden"></span>
                    <div class="title">
                        <span class="text">我的团队</span>
                        <span class="tab">
                            <span class="first current">今日</span><span>本周</span><span class="last">本月</span>
                        </span>
                    </div>
                    <div class="cont">
                        <ul class="list clearfix">
                            <li>
                                <div class="text">团队投注用户</div>
                                <div class="bor">
                                    <div class="num" id="J-team-my-bet-num">-</div>
                                </div>
                                <div class="button"><a href="{{ route('user-profits.index') }}">盈亏报表</a></div>
                            </li>
                            <li>
                                <div class="text">团队新增用户</div>
                                <div class="bor">
                                    <div class="num" id="J-team-my-reg-num">-</div>
                                </div>
                                <div class="button"><a href="{{ route('users.index') }}">团队管理</a></div>
                            </li>
                            <li>
                                <div class="text">新增直属玩家</div>
                                <div class="bor">
                                    <div class="num" id="J-team-my-reg-direct-num">-</div>
                                </div>
                                <div class="button"><a href="{{ route('users.accurate-create') }}">新增用户</a></div>
                            </li>
                        </ul>
                    </div>
                </div>


                <div class="team-chart">
                    
                    <input id="J-team-chart-sum-num" type="hidden" value="{{$iUserChildrenNum}}" />
                    <input id="J-team-chart-online-num" type="hidden" value="{{$iUserOnline}}" />
                    <input id="J-team-chart-online-agent-num" type="hidden" value="{{$iAgentCount}}" />
                    <input id="J-team-chart-online-user-num" type="hidden" value="{{$iPlayerCount}}" />


                    <div class="title">当前在线 {{$iUserOnline}} 人</div>
                    <div class="cont" id="J-chart-pie">

                    </div>
                </div>






         

                {{--
                <div class="news" id="J-tab-news">
                    <div class="title-inner clearfix">
                        <span class="title-text">我的信息</span>
                        <div class="title">
                            <ul>
                                <li class="first current">平台公告</li>
                                <li class="last">我的收件箱 [{{ $unreadMessagesNum }}]</li>
                            </ul>
                        </div>
                    </div>
                    <div class="cont panel-current">
                        <ul class="list">
                            <?php $iCountAnnouncements = count($aLatestAnnouncements); ?>
                            @foreach ($aLatestAnnouncements as $key => $oAnnouncement)
                            <li class="{{ $key+1 == $iCountAnnouncements ? 'last' : '' }}">
                                <a href="{{ route('announcements.view', $oAnnouncement->id) }}">{{ $oAnnouncement->title }}</a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="row-more"><a target="_blank" href="{{ route('announcements.index') }}"><i class="fa fa-info-circle"></i> 更多公告</a></div>
                    </div>
                    <div class="cont">
                        <ul class="list">
                            @foreach ($aStationLetters as $letter)
                            <li><a class="text" href="{{ route('station-letters.view', $letter['id']) }}">【{{ $letter['msg_type'] }}】{{ $letter['msg_title']}}</a> <span class="date">{{ $letter['updated_at'] }}</span></li>
                            @endforeach
                       </ul>
                       <div class="row-more"><a target="_blank" href="{{ route('station-letters.index') }}"><i class="fa fa-info-circle"></i> 更多信件</a></div>
                    </div>
                </div>


                <div class="ads-title">精彩纷呈</div>
                <div class="img-ad">
                    @include('adTemp.26')
                </div>
                --}}








            </div>






        </div>



    </div>
</div>
<div class="chart-tooltip" id="J-tooltip"></div>


@include('w.feedback')



@include('w.footer')
@stop


@section('scripts')
@parent
{{ script('jquery.flot')}}
{{ script('jquery.flot.crosshair') }}
{{ script('jquery.flot.pie') }}
{{ script('jquery.flot.categories') }}
@stop

@section('end')
@parent

<!--[if lte IE 8]>
<script language="javascript" type="text/javascript" src="/assets/js/excanvas.min.js"></script>
<![endif]-->
<script>
(function($){


    //团队销量图表
    var chartCont = $('#J-chart-cont'),
        //图表参数
        chartOptions = {
            lines: {
                show: true,
                lineWidth: 2
            },
            colors: ["#50E3C2"],
            points: {
                show: true
            },
            xaxis: {
                mode: "categories",
                tickDecimals: 0,
                color: '#666',
                fontSize: 12
            },
            yaxis: {
                tickDecimals: 0,
                color: '#FFF'
            },
            crosshair: {
                mode: "xy",
                color:'#666'
            },
            grid: {
                borderWidth: 1,
                color: '#666',
                hoverable: true,
                autoHighlight: true
            },
            legend: {
                color: '#000'
            }
        };
    var teamSales = {
        update:function(){
            $.ajax({
                url:'/get-month-team-turnover',
                dataType:'json',
                success:function(data){
                    var chartData = {
                            "label": '&nbsp;销量',
                            'data':[]
                        },
                        chartTable = $('#J-chart-table'),
                        tooltip = $('#J-tooltip'),
                        row,
                        list = [],
                        month = [];

                    $.each(data, function(i){
                        row = this;
                        //row[1] = Math.floor(Math.random()*(1000000 - 100000 + 1) + 1000);
                        list.push([Number(row[0].substr(8, 2)), Number(row[1])]);
                        month.push(row[0].substr(5, 2));
                    });

                    var plot = $.plot(chartTable, [list], chartOptions);
                    chartTable.bind("plothover", function (event, pos, item) {
                        if(item){
                            var index = item['dataIndex'],
                                x = month[index] + '月' + list[index][0] + '日',
                                y = '' + bomao.util.formatMoney(item.datapoint[1]) + ' 元';
                            tooltip.html(x + ' ' + y).css({top: item.pageY - tooltip.height() - 22, left: item.pageX - tooltip.outerWidth(true)/2}).fadeIn(200);
                        }else{
                            tooltip.hide();
                        }
                    });
                }
            });
        }
    };
    teamSales.update();






    //饼图
    var pieData = [],
        pie_online_num = Number($('#J-team-chart-online-num').val()),
        pie_user_sum = Number($('#J-team-chart-sum-num').val());

    pieData.push({label: '&nbsp;在线', data: pie_online_num/pie_user_sum, color: '#458DAC'});
    pieData.push({label: '&nbsp;下线', data: (pie_user_sum - pie_online_num)/pie_user_sum, color: '#8B8B8B'});
    var chartPie = $.plot('#J-chart-pie', pieData, {
        series: {
            pie: {
                show: true,
                innerRadius: .4
            }
        },
        legend: {
            show: false
        }
    });




    //我的团队tab
    var TeamMy = new bomao.Tab({
        par:'#J-team-my-tab',
        triggers:'.title .tab span',
        panels:'.hidden',
        eventType:'click'
    });
    TeamMy.addEvent('afterSwitch', function(e, i){
        var params = ['today', 'week', 'month'];
        this.update(params[i]);
    });
    TeamMy.update = function(param){
        var url = '/get-team-data/' + param;
        $.ajax({
            url:url,
            cache:false,
            dataType:'json',
            success:function(data){
                $('#J-team-my-bet-num').text(data['howmanypeoplebet']);
                $('#J-team-my-reg-num').text(data['howmanynewaccount']);
                $('#J-team-my-reg-direct-num').text(data['howmanynewplayer']);
            }
        });
    };
    TeamMy.update('today');





/**
[{
    "turnover": 0,//投注额
    "profit": 0,//盈亏
    "prize_group": "1955",//奖金组
    "username": "yidai444",//用户名
    "user_id": 441,
    "user_level": 1,
    "group_balance_sum": 0,//团队余额
    "direct_child_num": 1,//下级人数
    "user_level_txt": "\u4e00\u4ee3",//用户类型
    "new_create_account": 1//开户数
}]
**/
    //排行tab
    var Ranking = {
        hashTitle:{
            'sale':'投注额',
            'newaccount':'开户数',
            'profit':'净盈亏'
        },
        type:'sale',
        data:[],
        update:function(param){
            var me = this;
            me.type = param;
            $.ajax({
                url:'/get-agent-month-rank/' + param,
                cache:false,
                dataType:'json',
                success:function(data){
                    me.data = data;
                    me.updateView();
                }
            });
        },
        sort:function(type, asc){
            var me = this,
                ascFn = function(a, b){
                    return b[type] - a[type];
                },
                descFn = function(a, b){
                    return a[type] - b[type];
                },
                fn;
            fn = asc == 'asc' ? ascFn : descFn;
            me.data.sort(fn);
            me.updateView();
        },
        updateView:function(){
            var me = this,htmlArr = [],row;
            $.each(me.data, function(){
                row = this;
                htmlArr.push('<tr>');
                    htmlArr.push('<td>');
                        htmlArr.push(row['username']);
                    htmlArr.push('</td>');
                    htmlArr.push('<td>');
                        htmlArr.push('<span class="gray">'+ row['user_level_txt'] +'</span>');
                    htmlArr.push('</td>');
                    htmlArr.push('<td>');
                        htmlArr.push('<span class="red">'+ row['prize_group'] +'</span>');
                    htmlArr.push('</td>');
                    htmlArr.push('<td>');
                        htmlArr.push(row['direct_child_num']);
                    htmlArr.push('</td>');
                     htmlArr.push('<td>');
                        htmlArr.push(row['group_balance_sum']);
                    htmlArr.push('</td>');
                    htmlArr.push('<td>');
                        htmlArr.push(row['data']);
                    htmlArr.push('</td>');
                    /**
                    htmlArr.push('<td>');
                        htmlArr.push(row['new_create_account']);
                    htmlArr.push('</td>');
                    htmlArr.push('<td>');
                        htmlArr.push('<span class="red">'+ row['profit'] +'</span>');
                    htmlArr.push('</td>');
                    **/
                htmlArr.push('</tr>');
            });
            $('#J-tbody-rank').html(htmlArr.join(''));
            $('#J-rank-data-title').text(me.hashTitle[me.type]);
        }
    };

    var rankTab = new bomao.Tab({
        par:'#J-ranking-tab',
        triggers:'.title .tab span',
        panels:'.hidden',
        eventType:'click'
    });
    rankTab.addEvent('afterSwitch', function(e, i){
        var params = ['sale', 'newaccount', 'profit'];
        Ranking.update(params[i]);
    });
    $('#J-ranking-tab').on('click', '.sj', function(){
        var el = $(this),type = el.attr('data-type'),asc = '';
        asc = el.hasClass('sj-up') ? 'asc' : 'desc';
        Ranking.sort(type, asc);
        switch(asc){
            case 'asc':
                el.removeClass('sj-up');
            break;
            case 'desc':
                el.addClass('sj-up');
            break;
            default:
            break;
        }
    });
    Ranking.update('sale');





    //平台公告tab
    new bomao.Tab({
        par:'#J-tab-news',
        triggers:'.title li',
        panels:'.cont',
        eventType:'click'
    });



})(jQuery);
</script>

@stop