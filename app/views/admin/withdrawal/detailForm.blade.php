<?php
$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns , array_fill(0,count($aOriginalColumns),null));

if (!$isEdit){
    foreach($aInitAttributes as $sColumn => $mValue){
        $data->$sColumn = $mValue;
    }
}

$oFormHelper->setErrorObject($errors);

?>
{{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal')) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
@endif
@foreach ($aHiddenColumns as $hiddenColumn)
{{ Form::hidden($hiddenColumn) }}
@endforeach
@foreach ($aAttributes as $sColumn => $sValue)
    <?php
    if ($sColumn == 'id') continue;
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
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <a class="btn btn-default" href="{{ route($resource.'.edit', $data->id) }}">{{ __('Reset') }}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}
