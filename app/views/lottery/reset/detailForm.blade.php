<?php
$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns, array_fill(0, count($aOriginalColumns), null));
//if (!$isEdit && $bTreeable){
//    $data->parent_id = $parent_id;
//}
if (!$isEdit) {
    foreach ($aInitAttributes as $sColumn => $mValue) {
        $data->$sColumn = $mValue;
    }
}
//pr($aInitAttributes);
//pr($functionality_id);
// pr($aColumnSettings);
$oFormHelper->setErrorObject($errors);
$sMethod = !$isEdit ? 'POST' : 'PUT';
?>
{{ Form::model($data, array('method' => $sMethod, 'class' => 'form-horizontal')) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
@endif
@foreach ($aAttributes as $sColumn => $sValue)
<?php
if ($sColumn == 'id' || !isset($aColumnSettings[$sColumn])) {
    continue;
}
switch ($aColumnSettings[$sColumn]['form_type']) {
    case 'text':
    case 'textarea':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control']);
        break;
    case 'bool':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn]);
        break;
    default:
        $sHtml = Form::input('text', $sColumn, $sValue, ['class' => 'form-control']);
}
switch ($sColumn) {
    case 'start_date':
    case 'end_date':
        $sHtml = $oFormHelper->input($sColumn, $data->$sColumn, ['id' => $sColumn, 'type' => 'date', 'class' => 'form-control']);
        break;
    case 'close_type':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => ${$aColumnSettings[$sColumn]['options']}]);
        break;
    case 'lottery_id':
        $sHtml = $oFormHelper->input($sColumn, $lottery_id, ['id' => $sColumn, 'class' => 'form-control', 'options' => ${$aColumnSettings[$sColumn]['options']}, 'empty' => true]);
        break;
    case 'week':
        $aWeek = ['1' => '周一', '2' => '周二', '3' => '周三', '4' => '周四', '5' => '周五', '6' => '周六', '0' => '周日'];
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => $aWeek, 'type'=>'select','multiple'=>true]);
        break;
}
?>

{{ $sHtml }}

@endforeach
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        <!--<a class="btn btn-default" href="{{ route($resource. ($isEdit ? '.edit' : '.create'), $data->id) }}">{{ __('Reset') }}</a>-->
        {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}

@section('end')
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@parent
<script type="text/javascript">
    $(function () {

        $('#close_type').bind('change', function () {
            optionVal = $(this).val();
            if (optionVal == 1) {
                $('.form-group:eq(3)').show();
                $('.form-group:eq(4)').show();
                $('.form-group:eq(5)').hide();
                $('.form-group:eq(7)').hide();
                $('.form-group:eq(6)').hide();
            } else if (optionVal == 2) {
                $('.form-group:eq(3)').hide();
                $('.form-group:eq(4)').hide();
                $('.form-group:eq(5)').show();
                $('.form-group:eq(7)').show();
                $('.form-group:eq(6)').show();
            }
        });
        $('#close_type').trigger("change")
        //时间控件
        $('.form_date').datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            showMeridian: 1,
            pickerPosition: 'bottom-left'
        });
    });
</script>

@stop