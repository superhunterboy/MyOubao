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
    <?php
    //    die($sSetOrderRoute); ?>
    @if ($bSequencable)
    {{ Form::open(['action' => $sSetOrderRoute ]) }}
    @endif

    <div class="col-xs-12 J-tab-chart">

        <table class="table table-striped table-hover table-bordered text-center">
            <thead>
            <tr>
                @if ($bCheckboxenable)
                <th class="text-center"><input type="checkbox" id="allCheckbox" name="allCheckbox"></th>
                @endif
                @foreach( $aColumnForList as $sColumn )
                <th class="text-center">{{ (__($sLangPrev . $sColumn, null, 3)) }}
                    @if (!in_array($sColumn, $aNoOrderByColumns))
                    {{ order_by($sColumn) }}
                    @endif
                </th>
                <?php
                if (isset($aTotalColumns)){
                in_array($sColumn, $aTotalColumns) or $aTotalColumnMap[$sColumn] = null;
                }
                ?>
                @endforeach
                <th class=" text-center">{{ __('_basic.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($datas as $data)
                <tr>
                    @if ($bCheckboxenable)
                        <td ><input type="checkbox" name="selectFlag" value="{{$data->id}}">
                    @endif
                    <?php

                    foreach ($aColumnForList as $sColumn) {
                    if (isset($aTotalColumns)) {
                    if (!is_null($aTotalColumnMap[$sColumn])) {
                    $fResult = $data->is_income===0 ? -$data->$sColumn : $data->$sColumn;
                    $aTotals[$aTotalColumnMap[$sColumn]] += $fResult;
                    }
                    }
                    //    $sDisplayColumn = isset($aColumnDisplayMaps[ $sColumn ]) ? $aColumnDisplayMaps[ $sColumn ] : $sColumn;
                    $sClass = '';
                    if (isset($aListColumnMaps[ $sColumn ])){
                    $sDisplayValue = $data->{$aListColumnMaps[ $sColumn ]};
                    }
                    else{

                    if ($sColumn == 'sequence'){
                    $sDisplayValue = Form::text('sequence[' . $data->id . ']',$data->sequence,['class' => 'form-control','style' => 'width:70px;text-align:right']);
                    }else{
                    if (isset($aColumnSettings[ $sColumn ][ 'type' ])){
                    $sDisplayValue = $sColumn . $aColumnSettings[ $sColumn ][ 'type' ];
                    switch ($aColumnSettings[ $sColumn ][ 'type' ]){
                    case 'bool':
                    $sDisplayValue = !is_null($data->$sColumn) ? ($data->$sColumn ? __('Yes') : __('No')) : null;
                    break;
                    case 'select':
                    //                                        $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                    $sDisplayValue = !is_null($data->$sColumn) ? ${$aColumnSettings[ $sColumn ][ 'options' ]}[ $data->$sColumn ] : null;
                    break;
                    case 'numeric':
                    $sClass = 'text-right';
                    if (!isset($aNumberColumns[$sColumn])){
                    $aNumberColumns[$sColumn] = $iDefaultAccuracy;
                    }
                    default:
                    $sDisplayValue = is_array($data->$sColumn) ? implode(',',$data->$sColumn) : $data->$sColumn;
                    }
                    }
                    else{
                    $sDisplayValue = $data->$sColumn;
                    }

                    if (array_key_exists($sColumn,$aNumberColumns)){
                    $sDisplayValue = number_format($sDisplayValue,$aNumberColumns[ $sColumn ]);
                    }
                    }
                    }
                    echo "<td class='$sClass'>$sDisplayValue</td>";
                    }
                    ?>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
            @endforeach
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

    @if($isShowGraph)
        <div id="J-chart" class="col-xs-12 J-tab-chart" >
            <div id="container" style="min-width: 310px; min-height: 450px; margin: 0 auto"></div>
        </div>
    @endif

    <div class="form-group">



        <div class="col-sm-12">
            @include('w.page_batch_link')
            @if ($bSequencable)
                {{ Form::submit(__('_basic.set-order',null,2), ['class' => 'btn btn-success']) }}
            @endif

            {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}

        </div>

    </div>

    @if ($bSequencable)
        {{ Form::close() }}
    @endif
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

