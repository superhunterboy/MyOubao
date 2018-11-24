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
//pr($aWidgets);
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
                foreach ($aColumnForList as $sColumn) {
                    if (isset($aColumnSettings[$sColumn]['type'])) {
                        $sDisplayValue = $sColumn . $aColumnSettings[$sColumn]['type'];
                        switch ($aColumnSettings[$sColumn]['type']) {
                            case 'bool':
                                $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                                break;
                            case 'select':
                                $aColumn = explode(',', $data->$sColumn);
                                $sDisplayValue = "";
                                foreach ($aColumn as $val) {
                                    $sDisplayValue .= ${$aColumnSettings[$sColumn]['options']}[$val] . ",";
                                }
                                if (strlen($sDisplayValue) > 0) {
                                    $sDisplayValue = substr($sDisplayValue, 0, strlen($sDisplayValue) - 1);
                                }
                                break;
                            default:
                                $sDisplayValue = $data->$sColumn;
                        }
                    } else {
                        $sDisplayValue = $data->$sColumn;
                    }
                    if (array_key_exists($sColumn, $aNumberColumns)) {
                        $sDisplayValue = number_format($sDisplayValue, $aNumberColumns[$sColumn]);
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
//pr($aLangVars);
//exit;
$modalData['modal'] = array(
    'id' => 'myModal',
    'title' => '系统提示',
    'message' => __('_basic.delete-confirm', $aLangVars) . '？',
    'footer' =>
    Form::open(['id' => 'real-delete', 'method' => 'delete']) . '
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-sm btn-danger">确认删除</button>' .
    Form::close(),
);
?>
@include('w.modal', $modalData)

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

@section('javascripts')
@parent
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@stop

