@extends('l.admin', ['active' => $resource])
@section('title')
    @parent
    {{ $sPageTitle }}
@stop
@section('container')

    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    <?php
    //pr($aColumnSettings);
    // pr($aWidgets);
    // exit;
    ?>
    <?php
    if (isset($aTotalColumns)){
        $aTotals = array_fill(0, count($aColumnForList),null);
        $aTotalColumnMap = array_flip($aColumnForList);
    }
    ?>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach

    <div class="col-xs-12 J-tab-chart">

        <table class="table table-striped table-hover table-bordered text-center">
            <thead>
            <tr>
                <th class="text-center">用户ID</th>
                <th class="text-center">用户名</th>
                <th class="text-center">层级</th>
                <th class="text-center">
                    @if($commission_type == 1){{'充值'}}
                    @elseif($commission_type == 2){{'消费'}}
                    @elseif($commission_type == 3){{'亏损'}}
                    @endif
                </th>
                @if($commission_type == 1)
                    <th class="text-center">
                        投注额
                    </th>
                @endif
                <th class="text-center">佣金</th>
                <th class=" text-center">{{ __('_basic.actions') }}</th>
            </tr>
            </thead>
            <tbody>

            <?php $sClass = 'text-right'; $amountTotal =$turnOverTotal = $commissionTotal = 0;?>
            @foreach ($datas as $data)
                <?php $amountTotal += $data['amount']; $commissionTotal += $data['commission']; if(isset($data['turnover'])) $turnOverTotal += $data['turnover']; ?>
                <tr>
                    <td >{{$data['user_id']}}</td>
                    <td>{{$data['username']}}</td>
                    <td>
                        @if($data['sub_level'] == 1){{'下级'}}
                        @elseif($data['sub_level'] == 2){{'下下级'}}
                        @elseif($data['sub_level'] == 3){{'下下下级'}}
                        @endif
                    </td>
                    <td>{{$data['amount']}}</td>
                    @if($commission_type == 1)
                        <td>{{$data['turnover']}}</td>
                    @endif
                    <td>{{$data['commission']}}</td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
            @endforeach
                <tr>
                    <td colspan="3">总计：</td>
                    <td>{{$amountTotal}}</td>
                    @if($commission_type == 1)
                        <td>{{$turnOverTotal}}</td>
                    @endif
                    <td>{{$commissionTotal}}</td>
                    <td></td>
                </tr>
            </tbody>
            <tfoot>
            @if (isset($aTotalColumns))
                <tr>
                    <td>{{ __('grand-total-per-page') }}</td>
                    @for($i = 1; $i < count($aColumnForList); $i++)
                        <td class="{{ is_numeric($aTotals[$i]) ? 'text-right' : '' }}">{{  is_null($aTotals[$i]) ? ' ' : number_format($aTotals[$i], (array_key_exists($aColumnForList[$i], $aNumberColumns) ? $aNumberColumns[$aColumnForList[$i]] : 2)) }}</td>
                    @endfor
                    <td></td>
                </tr>

            @endif
            @if (isset($aTotalColumnsAllPages))
                <tr>
                    <td>{{ __('grand-total') }}</td>
                    @for($i = 1; $i < count($aColumnForList); $i++)
                        <td class="{{ is_numeric($aTotalsAllPages[$aColumnForList[$i]."_sum"]) ? 'text-right' : '' }}">{{  is_null($aTotalsAllPages[$aColumnForList[$i]."_sum"]) ? ' ' : number_format($aTotalsAllPages[$aColumnForList[$i]."_sum"], (array_key_exists($aColumnForList[$i], $aNumberColumns) ? $aNumberColumns[$aColumnForList[$i]] : 2)) }}</td>
                    @endfor
                    <td></td>
                </tr>
            @endif
            </tfoot>
        </table>
    </div>


    <?php
    //pr($aLangVars);
    //exit;
    $modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => __('_basic.delete-confirm',$aLangVars) . '？',
    'footer'  =>
    Form::open(['id' => 'real-delete', 'method' => 'delete']).'
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-sm btn-danger">确认删除</button>'.
    Form::close(),
    );
    ?>
    @include('w.modal', $modalData)

@stop

@section('javascripts')
    @parent
    {{ script('datetimepicker') }}
    {{ script('datetimepicker-zh-CN')}}
    @if($isShowGraph)
        {{ script('highcharts')}}
    @endif
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }


        @if($isShowGraph)

        $(function () {
            var d = {{ $graphdatas }};

            var numData = [];
            for( var n in d.y){
                var m = d.y[n];
                for (var i = 0, l = m['data'].length; i < l; i++) {
                    m['data'][i] = parseFloat(m['data'][i]);
                }
                numData.push(m);
            };


            $('#container').highcharts({
                chart: {
                    type: 'spline'
                },
                title: {
                    text: ' ',
                    x: -20
                },
                xAxis: {
                    categories: d.x,
                    type: 'category',
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: ''
                    },
                    plotLines: [{
                        value: 0,
                        width: 2,
                        color: '#f00'
                    }]
                },
                tooltip: {
                    crosshairs: true,
                    shared: true
                },
                legend: {

                },
                series:numData
            });
            $('#J-chart').hide();
            $('#J-btn').click(function() {
                var text = ($('#J-btn').val() =='查看数据表')? '查看统计图':'查看数据表'
                $('.J-tab-chart').toggle(0,function(){
                    $('#J-btn').val(text);
                });
            });

        });
        @endif

    </script>
@stop

