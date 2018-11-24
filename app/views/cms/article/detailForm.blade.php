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
// exit;
$oFormHelper->setErrorObject($errors);
// $sMethod = !$isEdit ? 'POST' : 'PUT';
//pr($aColumnSettings);
//exit;
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
switch ($aColumnSettings[$sColumn]['form_type']) {
    case 'text':
    case 'textarea':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control']);
        break;
    case 'bool':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn]);
        break;
    case 'select':
        $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => ${$aColumnSettings[$sColumn]['options']}]);
        break;
    case 'ignore':
        continue 2;
    default:
        $sHtml = Form::input('text', $sColumn, $sValue, ['class' => 'form-control']);
}
if ($sColumn == 'content') {
    ?>
    <div class="form-group">
        <label for="content" class="col-sm-2 control-label">*{{__( '_cmsarticle.content')}}</label>
        <div class="col-sm-6">
            <script id="editor" type="text/plain" name="content" style="width:100%;height:200px;">{{$sValue}}</script>
        </div>
        <div class="col-sm-4">
        </div>

    </div>
    <?php
} else if ($sColumn == 'add_user_id' || $sColumn == 'update_user_id') {
    $sHtml = '';
    ?>

    <?php
} else {
    ?>

    {{ $sHtml }}
<?php } ?>
@endforeach

<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        <a class="btn btn-default" href="{{ route($resource.'.edit', $data->id) }}">{{ __('Reset') }}</a>
        {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
<div style="height:10px;"></div>
{{Form::close()}}
