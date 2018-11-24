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
{{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal','id'=>'refund_form')) }}
@if ($isEdit)
<input type="hidden" name="_method" value="PUT" />
<input type="hidden" id="refund_step" name="step" value="" />
@endif
@foreach ($aAttributes as $sColumn => $sValue)
<?php
if ($sColumn == 'amount' || $sColumn == 'pay_card_name' || $sColumn == 'pay_card_num' || $sColumn == 'issue_bank_id' || $sColumn == 'issue_bank_address'|| $sColumn == 'account' || $sColumn == 'remark') {
    switch ($aColumnSettings[$sColumn]['form_type']) {
        case 'text':
        case 'textarea':
            $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control']);
            break;
        case 'bool':
            $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn]);
            break;
        case 'select':
            $sHtml = $oFormHelper->input($sColumn, null, ['id' => $sColumn, 'class' => 'form-control', 'options' => ${$aColumnSettings[$sColumn]['options']}, 'empty' => true]);
            break;
        case 'ignore':
            continue 2;
        default:
            $sHtml = Form::input('text', $sColumn, $sValue, ['class' => 'form-control']);
    }
} else {
    continue;
}
?>

{{ $sHtml }}

@endforeach
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
        <a class="btn btn-default" href="javascript:void();" id="mownecum_withdraw">{{ __('_exceptiondeposit.mownecum-withdraw') }}</a>
        <a class="btn btn-default" href="javascript:void();" id="offline_deposit">{{ __('_exceptiondeposit.offline-deposit') }}</a>
    </div>
</div>
{{Form::close()}}
