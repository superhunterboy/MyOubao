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
//pr($aCategories);
//exit;
?>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                @foreach( $aColumnForList as $sColumn )
                    <th>{{ ucwords(__($sLangPrev . $sColumn)) }} {{ order_by($sColumn) }}</th>
                @endforeach
                    <th>{{ __('_basic.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <?php
                    // pr($aColumnSettings);exit;
                    foreach ($aColumnForList as $sColumn){
                        if (isset($aColumnSettings[$sColumn]['type'])){
                            $sDisplayValue = $sColumn . $aColumnSettings[$sColumn]['type'];
                            switch($aColumnSettings[$sColumn]['type']){
                                case 'bool':
                                    $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                                    break;
                                case 'select':
                                    $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                                    break;
                                default:
                                    $sDisplayValue = $data->$sColumn;
                            }
                        }
                        else{
                            $sDisplayValue = $data->$sColumn;
                        }
                        echo "<td>$sDisplayValue</td>";
                    }
                    ?>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>

<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
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
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
@if ($bNeedCalendar)
        $('.form_date').datetimepicker({
          language:  'zh-CN',
          weekStart: 1,
          todayBtn:  1,
          autoclose: 1,
          todayHighlight: 1,
          startView: 2,
          minView: 2,
          forceParse: 0,
          showMeridian: 1,
          pickerPosition: 'bottom-left'
        });
@endif
    </script>
@stop

