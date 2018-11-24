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
        
        $modalData2['modal'] = [
    
    'id'      => 'myModal2',
    'title'   => '系统提示',
    'message' =>__('_function.prohibited-withdraw'),
    'action'  => 'prohibited-withdraw',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.prohibited-withdraw'). '</button>',
];
        ?>
    @include('w.modal', $modalData)

    @include('w.formModal', $modalData2)
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

 function prohibitedWithdraw(href)
        {
            
            $('#prohibited-withdraw').attr('action', href);
            $('#myModal2').modal();
        }

    </script>
@stop

