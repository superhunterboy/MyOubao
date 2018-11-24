<?php
$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns , array_fill(0,count($aOriginalColumns),null));
//if (!$isEdit && $bTreeable){
//    $data->parent_id = $parent_id;
//}
if (!$isEdit){
    foreach($aInitAttributes as $sColumn => $mValue){
        $data->$sColumn = $mValue;
    }
}
//pr($aInitAttributes);
//pr($functionality_id);
// pr($aColumnSettings);
// exit;
$oFormHelper->setErrorObject($errors);
// $sMethod = !$isEdit ? 'POST' : 'PUT';
//pr($aColumnSettings);
//exit;
?>
{{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal')) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
@endif
@foreach ($aAttributes as $sColumn => $sValue)
    <?php
    if ($sColumn == 'id' || !isset($aColumnSettings[$sColumn])){
        continue;
    }
    switch($aColumnSettings[$sColumn]['form_type']){
        case 'text':
        case 'textarea':
            $sHtml = $oFormHelper->input($sColumn,null,['id' => $sColumn, 'class' => 'form-control']);
            break;
        case 'bool':
            $sHtml = $oFormHelper->input($sColumn,null,['id' => $sColumn]);
            break;
        case 'select':
            $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => ${$aColumnSettings[$sColumn]['options']}, 'empty' => true]);
            break;
        case 'ignore':
            continue 2;
        default:
            $sHtml = Form::input('text',$sColumn,$sValue,['class' => 'form-control']);
    }
    ?>

            {{ $sHtml }}

@endforeach
<table class='table table-striped table-hover table-bordered text-center'>
    <tr>
        <td colspan="2">
           额外分红标准
        </td>
    </tr>
    <tr>
        <td>实际净亏损</td>
        <td>额外比例</td>
    </tr>
<tbody id="J-tbody">
@foreach($data->extraBonusPolicy as $extra)
<tr> 
    <td>
        <input type='hidden' name='extra_id[]' value='{{$extra->id}}' />
        <input type='text' name='extra_loss[]' value='{{$extra->loss}}' /> </td>
     <td><input type='text' name='extra_rate[]' value='{{$extra->rate}}' /> </td>
      
</tr>
@endforeach
</tbody>
</table>


<script type="text/template" id="J-addrow-tpl">
<tr> 
    <td><input type="hidden" name="extra_id[]" value="0"> <input type="text" name="extra_loss[]" value=""> </td>
     <td><input type="text" name="extra_rate[]" value=""> </td>
     
</tr>
</script>

<div class="form-group">
    <div class="col-sm-1">
        <button type="button" class="btn btn-default" id="J-button-addrow">
            增 加
        </button>
    </div>

</div>
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <a class="btn btn-default" href="{{ route($resource. ($isEdit ? '.edit' : '.create'), $data->id) }}">{{ __('Reset') }}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}
