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
        @include('w.list_item')
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

