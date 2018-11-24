@extends('l.thome')

@section('title')
    {{ $sLotteryName . '-' . $sTrendTypeName }}-走势图
@parent
@stop


@section ('container')
    @include('centerTrend.ssc.ssc-ways')
    <!-- Star 后三 -->
    <div class="chart-section chart-container-main" id="J-chart-area">
        <table class="chart-table" id="J-chart-table">
            <thead class="thead">
                <tr class="title-text">
                    <th rowspan="2" colspan="3" class="border-bottom border-right">期号</th>
                    <th colspan="3" class="border-right">开奖号码</th>
                    <th colspan="12" class="border-right">千位</th>
                    <th colspan="12" class="border-right">百位</th>
                    <th colspan="12" class="border-right">十位</th>
                    <th colspan="12" class="border-right">号码分布</th>
                    <th rowspan="2" class="border-bottom border-right">大小形态</th>
                    <th rowspan="2" class="border-bottom border-right">单双形态</th>
                    <th rowspan="2" class="border-bottom border-right">质合形态</th>
                    <th rowspan="2" class="border-bottom border-right">012形态</th>
                    <th rowspan="2" class="border-bottom border-right">豹子</th>
                    <th rowspan="2" class="border-bottom border-right">组三</th>
                    <th rowspan="2" class="border-bottom border-right">组六</th>
                    <th rowspan="2" class="border-bottom border-right">跨度</th>
                    <th rowspan="2" class="border-bottom border-right">直选和值</th>
                    <th rowspan="2" class="border-bottom">和值尾数</th>
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
                    <th class="ball-none border-bottom-header td-bg"></th>
                </tr>
            </thead>
            <tbody id="J-chart-content" class="chart table-guides tbody">
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
                    <td rowspan="2" class="border-right border-bottom">大小形态</td>
                    <td rowspan="2" class="border-right border-bottom">单双形态</td>
                    <td rowspan="2" class="border-right border-bottom">质合形态</td>
                    <td rowspan="2" class="border-right border-bottom">012形态</td>
                    <td rowspan="2" class="border-right border-bottom">豹子</td>
                    <td rowspan="2" class="border-right border-bottom">组三</td>
                    <td rowspan="2" class="border-right border-bottom">组六</td>
                    <td rowspan="2" class="border-right border-bottom">跨度</td>
                    <td rowspan="2" class="border-right border-bottom">直选和值</td>
                    <td class="border-bottom" rowspan="2">和值尾数</td>

                </tr>
                <tr class="auxiliary-area title-text">
                    <td colspan="12" class="border-right border-bottom">千位</td>
                    <td colspan="12" class="border-right border-bottom">百位</td>
                    <td colspan="12" class="border-right border-bottom">十位</td>
                    <td colspan="12" class="border-right border-bottom">号码分布</td>
                </tr>

            </tbody>


        </table>


    </div>
    <!-- End 后三 -->
@stop

@section('end')
    @parent
    <script>
        var CHART;
        (function($){

            var sysConfig = {{ $sConfigs }};

            CHART = new bomao.GameCharts({currentGameType:'cqssc', currentGameMethod: 'Housan', expands:{
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
                        loseFlag = new Array(30);
                        tempArr = [],
                        i2 = 0,
                        i3 = 0;

                    $.each([0, 1, 2], function(i){
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
                            if(i2 > 1 && i2 < 5){
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
                },
                cqsscHousanRender:function(data, currentNum){
                    var td,
                        current,
                        me = this,
                        styleName = currentNum % me.getSeparateNum() == 0 ? ' border-bottom' : '',
                        htmlTextArr = new Array(),
                        allowCount = me.getRenderLength(),
                        parentDom = document.createElement('tr');

                    td = document.createElement('td');
                    td.className = "ball-none " + styleName;
                    parentDom.appendChild(td);

                    //期号
                    td = document.createElement('td');
                    td.className = "issue-numbers " + styleName;
                    td.innerHTML = data[0];
                    parentDom.appendChild(td);

                    td = document.createElement('td');
                    td.className = "ball-none border-right" + styleName;
                    parentDom.appendChild(td);

                    td = document.createElement('td');
                    td.className = "ball-none " + styleName;
                    parentDom.appendChild(td);

                    //开奖号码
                    td = document.createElement('td');
                    td.className = styleName;
                    td.innerHTML = '<span class="lottery-numbers">' + data[1] + '</span>';
                    parentDom.appendChild(td);

                    td = document.createElement('td');
                    td.className = 'ball-none border-right' + styleName;
                    parentDom.appendChild(td);

                    //百位
                    parentDom.appendChild(me.singleLotteryBall(data[2], styleName));
                    //十位
                    parentDom.appendChild(me.singleLotteryBall(data[3], styleName));
                    //个位
                    parentDom.appendChild(me.singleLotteryBall(data[4], styleName));
                    //号码分布
                    parentDom.appendChild(me.layoutLotteryBall(data[5], styleName));


                    td = document.createElement('td');
                    td.className = "ball-none border-right " + styleName;
                    parentDom.appendChild(td);


                    //大小形态
                    td = document.createElement('td');
                    td.className = 'bg-blue border-right ' + styleName;
                    td.innerHTML = (data[6]).join('').replace(/0/g, '小').replace(/1/g, '大');
                    parentDom.appendChild(td);

                    //单双形态
                    td = document.createElement('td');
                    td.className = 'bg-green border-right ' + styleName;
                    td.innerHTML = (data[7]).join('').replace(/0/g, '双').replace(/1/g, '单');
                    parentDom.appendChild(td);

                    //质合形态
                    td = document.createElement('td');
                    td.className = 'bg-blue border-right ' + styleName;
                    td.innerHTML = (data[8]).join('').replace(/0/g, '合').replace(/1/g, '质');
                    parentDom.appendChild(td);

                    //012形态
                    td = document.createElement('td');
                    td.className = 'bg-green border-right ' + styleName;
                    td.innerHTML = (data[9]).join('');
                    parentDom.appendChild(td);

                    //豹子
                    td = document.createElement('td');
                    td.className = 'border-right ' + styleName;
                    td.innerHTML = data[10][0] == 0 ? '<i class="group-current"></i>' : data[10][0];
                    parentDom.appendChild(td);

                    //组三
                    td = document.createElement('td');
                    td.className = 'border-right ' + styleName;
                    td.innerHTML = data[11][0] == 0 ? '<i class="group-current"></i>' : data[11][0];
                    parentDom.appendChild(td);

                    //组六
                    td = document.createElement('td');
                    td.className = 'border-right ' + styleName;
                    td.innerHTML = data[12][0] == 0 ? '<i class="group-current"></i>' : data[12][0];
                    parentDom.appendChild(td);

                    //跨度
                    td = document.createElement('td');
                    td.className = 'bg-blue border-right ' + styleName;
                    td.innerHTML = data[13];
                    parentDom.appendChild(td);

                    //直选和值
                    td = document.createElement('td');
                    td.className = 'bg-red border-right ' + styleName;
                    td.innerHTML = data[14];
                    parentDom.appendChild(td);

                    //和值尾数
                    td = document.createElement('td');
                    td.className = 'border-right ' + styleName;
                    td.innerHTML = data[15];
                    parentDom.appendChild(td);



                    //返回完整的单行TR结构
                    return parentDom;
                },
                cqsscHousanRenderStatistics: function(data){
                    var me = this,
                        index = 0,
                        i = 0,
                        len = 0,
                        j = 0,
                        len2 = 0,
                        n = 0,
                        tdstr = '<td class="ball-none border-right"></td><td class="ball-none"></td>',
                        frame1 = [],
                        frame2 = [],
                        frame3 = [],
                        frame4 = [];
                    frame1.push('<tr class="auxiliary-area"><td class="ball-none border-bottom"></td><td class="border-bottom border-top">出现总次数</td><td class="ball-none border-right border-bottom"></td><td class="ball-none  border-bottom"></td><td class="border-bottom"></td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td>');
                    frame2.push('<tr class="auxiliary-area"><td class="ball-none border-bottom"></td><td class="border-bottom">平均遗漏值</td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td><td class="border-bottom"></td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td>');
                    frame3.push('<tr class="auxiliary-area"><td class="ball-none border-bottom"></td><td class="border-bottom">最大遗漏值</td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td><td class="border-bottom"></td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td>');
                    frame4.push('<tr class="auxiliary-area"><td class="ball-none border-bottom"></td><td class="border-bottom">最大连出值</td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td><td class="border-bottom"></td><td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td>');


                    for(i = 0, len = 40; i < len; i++){
                        tdstr = ((i+1)%10 == 0) ? '<td class="ball-none border-right border-bottom"></td><td class="ball-none border-bottom"></td>' : '';
                        tdstr = (i == (len - 1)) ? '<td class="border-right border-bottom"></td>' : tdstr;

                        frame1.push('<td class="border-bottom"><i class="ball-noraml">'+ data[0][i] +'</i></td>' + tdstr);
                        frame2.push('<td class="border-bottom"><i class="ball-noraml">'+ data[1][i] +'</i></td>' + tdstr);
                        frame3.push('<td class="border-bottom"><i class="ball-noraml">'+ data[2][i] +'</i></td>' + tdstr);
                        frame4.push('<td class="border-bottom"><i class="ball-noraml">'+ data[3][i] +'</i></td>' + tdstr);
                    }

                    for(i = 0; i < 10; i++){
                        //形态
                        if(i > 3 && i < 7){
                            frame1.push('<td class="border-bottom border-right"><i class="ball-noraml">'+ data[0][40 + i - 4] +'</i></td>');
                            frame2.push('<td class="border-bottom border-right"><i class="ball-noraml">'+ data[1][40 + i - 4] +'</i></td>');
                            frame3.push('<td class="border-bottom border-right"><i class="ball-noraml">'+ data[2][40 + i - 4] +'</i></td>');
                            frame4.push('<td class="border-bottom border-right"><i class="ball-noraml">'+ data[3][40 + i - 4] +'</i></td>');
                        }else{
                            frame1.push('<td class="border-right border-bottom"></td>');
                            frame2.push('<td class="border-right border-bottom"></td>');
                            frame3.push('<td class="border-right border-bottom"></td>');
                            frame4.push('<td class="border-right border-bottom"></td>');
                        }
                    }

                    frame1.push('</tr>');
                    frame2.push('</tr>');
                    frame3.push('</tr>');
                    frame4.push('</tr>');
                    $(me.getBallContainer()).append($(frame1.join(''))).append($(frame2.join(''))).append($(frame3.join(''))).append($(frame4.join('')));
                },
                cqsscHousanTrendCanvas:function(dom, data){
                    var positionCount = 0,
                        me = this,
                        currentBallLeft = 0,
                        currentBallTop = 0,
                        chartTrendPosition = me.getChartTrendPosition();


                    //遍历分段渲染数据
                    for (var i = 0, current; i < data.length; i++) {
                        current = data[i];

                        for (var k = 0; k < current.length; k++) {
                            //选球区域
                            if (k > 1 && k < 5) {
                                for (var j = 0; j < current[k].length; j++) {

                                    if(j == 0)  {
                                        var currentDom = dom.getElementsByTagName('i')[positionCount].parentNode,
                                            unitSize = me.getUnitSize(currentDom),
                                            top = unitSize.topNum,
                                            left = unitSize.leftNum,
                                            width = unitSize.widthNum,
                                            height = unitSize.heightNum;
                                    }

                                    //当前位置球
                                    positionCount ++;

                                    //当前号码
                                    if (current[k][j][0] == 0) {
                                        //第一排渲染
                                        if (typeof chartTrendPosition[k] == 'undefined') {

                                            //当前球的坐标
                                            currentBallLeft = left + (j + 1) * width - width / 2;
                                            currentBallTop = top + height / 2;

                                            chartTrendPosition[k] = {};
                                            chartTrendPosition[k]['top'] = currentBallTop;
                                            chartTrendPosition[k]['left'] = currentBallLeft;
                                        } else {

                                            //当前球的坐标
                                            currentBallLeft = left + (j + 1) * width - width / 2;
                                            currentBallTop = chartTrendPosition[k]['top'] + height;

                                            //绘制画布
                                            //绘制走势图线
                                            me.draw.setOption({
                                                parentContainer: $('#J-chart-area')[0]
                                            });
                                            me.draw.line(chartTrendPosition[k]['top'], chartTrendPosition[k]['left'], currentBallTop, currentBallLeft);

                                            chartTrendPosition[k]['top'] = currentBallTop;
                                            chartTrendPosition[k]['left'] = currentBallLeft;
                                        }
                                    }
                                };
                            }
                        };

                        positionCount = 0;
                    };
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