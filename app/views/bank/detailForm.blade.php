<?php
$aAttributes = $isEdit ? $data->getAttributes() : array_combine($aOriginalColumns , array_fill(0,count($aOriginalColumns),null));
if (!$isEdit){
    foreach($aInitAttributes as $sColumn => $mValue){
        $data->$sColumn = $mValue;
    }
}

$oFormHelper->setErrorObject($errors);
?>

{{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal', 'enctype'=>'multipart/form-data')) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
@endif

@foreach ($aAttributes as $sColumn => $sValue)
    <?php
    if ($sColumn == 'id' || !isset($aColumnSettings[$sColumn])) {
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
            $aNewArray = ${$aColumnSettings[$sColumn]['options']};
            if(is_array($aNewArray)){
                foreach($aNewArray as &$val){
                    $val = __('_bank.'.$val);
                }
            }
            $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => $aNewArray, 'empty' => true]);
            break;
        case 'ignore':
            continue 2;
        default:
            $sHtml = Form::input('text',$sColumn,$sValue,['class' => 'form-control']);
    }
 if($sColumn == 'deposit_notice') {
    ?>
    <div class="form-group">
        <label for="deposit_notice" class="col-sm-2 control-label">{{__('deposit_notice')}}</label>
        <div class="col-sm-6">
            <script id="editorBank" type="text/plain" name="deposit_notice" style="width:100%;height:200px;">{{$sValue}}</script>
        </div>
    </div>
    <?php 
    }else{
    ?>

            {{ $sHtml }}
<?php } ?>
@endforeach
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <a class="btn btn-default" href="{{ route($resource. ($isEdit ? '.edit' : '.create'), $data->id) }}">{{ __('Reset') }}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}
