@extends('l.thome')

@section('title')
    {{ $sLotteryName . '-' . $sTrendTypeName }}-走势图
@parent
@stop


@section ('container')
    @include('centerTrend.ssc.ssc-ways')

    <!-- Star 五星 -->
    <div class="chart-section chart-container-main" id="J-chart-area">
        <table class="chart-table" id="J-chart-table">
            <thead class="thead">
                <tr class="title-text">
                    <th rowspan="2" colspan="3" class="border-bottom border-right">期号</th>
                    <th colspan="3" class="border-right">开奖号码</th>
                    <th colspan="12" class="border-right border-bottom">万位</th>
                    <th colspan="12" class="border-right border-bottom">千位</th>
                    <th colspan="12" class="border-right border-bottom">百位</th>
                    <th colspan="12" class="border-right border-bottom">十位</th>
                    <th colspan="12" class="border-right border-bottom">个位</th>
                    <th colspan="12" class="border-bottom">号码分布</th>
                </tr>
                <tr class="title-number">
                    <th class="ball-none border-bottom-header"></th>
                    <th class="border-bottom-header"><label class="label"><input type="checkbox" class="checkbox">全部</label></th>
                    <th class="ball-none border-bottom-header border-right"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header border-right td-bg"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header border-right td-bg"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header border-right td-bg"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header border-right td-bg"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header border-right td-bg"></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">0</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">1</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">2</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">3</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">4</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">5</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">6</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">7</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">8</i></th>
                    <th class="border-bottom-header td-bg"><i class="ball-noraml">9</i></th>
                    <th class="ball-none border-bottom-header td-bg"></th>
                </tr>
            </thead>
            <tbody id="J-chart-content" class="chart table-guides">
                <tr></tr>
            </tbody>

            <tbody id="J-ball-content" class="tbody">

            </tbody>

            <tbody class="tbody tbody-footer-header">
                <tr class="auxiliary-area title-number">
                    <td rowspan="2" colspan="3" class="border-right border-bottom">期号</td>
                    <td rowspan="2" colspan="3" class="border-right border-bottom" >开奖号码</td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-right border-bottom td-bg"></td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-right border-bottom td-bg"></td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-right border-bottom td-bg"></td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-right border-bottom td-bg"></td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-right border-bottom td-bg"></td>
                    <td class="ball-none border-bottom td-bg"></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">0</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">1</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">2</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">3</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">4</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">5</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">6</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">7</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">8</i></td>
                    <td class="border-bottom td-bg"><i class="ball-noraml">9</i></td>
                    <td class="ball-none border-bottom td-bg"></td>
                </tr>
                <tr class="auxiliary-area title-text">
                    <td colspan="12" class="border-right border-bottom">万位</td>
                    <td colspan="12" class="border-right border-bottom">千位</td>
                    <td colspan="12" class="border-right border-bottom">百位</td>
                    <td colspan="12" class="border-right border-bottom">十位</td>
                    <td colspan="12" class="border-right border-bottom">个位</td>
                    <td colspan="12" class="border-bottom">号码分布</td>
                </tr>

            </tbody>


        </table>


    </div>
    <!-- End 五星 -->
@stop

@section('end')
    @parent
   <script>
        var CHART;
        (function($){

            var sysConfig = {{ $sConfigs }};

            CHART = new bomao.GameCharts({currentGameType:'cqssc', currentGameMethod: 'Wuxing', expands:{
                getDataUrl:function(){
                    var dt1 = Date.parse(new Date($('#J-date-star').val())),
                        dt2 = Date.parse(new Date($('#J-date-end').val()));
                    dt1 = isNaN(dt1) ? '' : dt1/1000;
                    dt2 = isNaN(dt2) ? '' : dt2/1000;
                    var param = {
                        'lottery_id':sysConfig.lotteryId,
                        'num_type':sysConfig.wayId,
                        'count':30,
                        'begin_time':dt1,
                        'end_time':dt2
                    },
                    arr = [sysConfig.queryBaseUrl + '?'],
                    p;

                    for(p in param){
                        if(param.hasOwnProperty(p)){
                            arr.push(p + '=' + param[p]);
                        }
                    }
                    return arr.join('&');
                },
                reBuildData:function(data){
                    var arrMain = [],
                        newArr = [],
                        timesData = data['statistics'][0],
                        ballData = data['data'],
                        loseBar = data['omissionBarStatus'],
                        loseFlag = new Array(50);
                        tempArr = [],
                        i2 = 0,
                        i3 = 0;

                    $.each([0, 1, 2, 3, 4], function(i){
                        arrMain[i] = [];
                        $.each([0,1,2,3,4,5,6,7,8,9], function(j){
                            // [times, index]
                            arrMain[i][j] = [timesData[i*10+j], j];
                        });
                        arrMain[i].sort(function(a, b){
                            return b[0] - a[0];
                        });
                        $.each(arrMain[i], function(k){
                            //cold 1
                            //hot 3
                            //other 2
                            if(k < 3){
                                newArr[i*10+arrMain[i][k][1]] = 3;
                            }else if(k > 6){
                                newArr[i*10+arrMain[i][k][1]] = 1;
                            }else{
                                newArr[i*10+arrMain[i][k][1]] = 2;
                            }
                        });

                    });
                    $.each(ballData, function(i){
                        i2 = 0;
                        $.each(this, function(j){
                            i3 = 0;
                            if(i2 > 1 && i2 < 7){
                                $.each(this, function(){
                                    if(this[0] == 0){
                                        this[2] = newArr[(i2 - 2)*10 + i3];
                                    }
                                    //loseBar
                                    if(loseBar[(i2 - 2)*10 + i3] < 0){
                                        loseFlag[(i2 - 2)*10 + i3] = true;
                                    }
                                    if(!loseFlag[(i2 - 2)*10 + i3]){
                                        this[3] = 0;
                                    }else{
                                        this[3] = 1;
                                    }
                                    if(loseBar[(i2 - 2)*10 + i3] == i){
                                        loseFlag[(i2 - 2)*10 + i3] = true;
                                    }
                                    i3++;
                                });
                            }
                            i2++;
                        });
                    });
                    return data;
                }
            }});
            CHART.addEvent('afterRenderChartHtml', function(){
                var me = this,tds = $(me.getContainer()).find('tr:last').children();
                tds.addClass('border-bottom');
            });
            CHART.show();

            CHART.sysConfig = sysConfig;


        })(jQuery);
    </script>

@stop