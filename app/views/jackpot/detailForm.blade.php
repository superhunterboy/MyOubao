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
            break;
        default:
            $sHtml = Form::input('text',$sColumn,$sValue,['class' => 'form-control']);
            break;
    }

    if($sColumn == 'lotteries_limit'){
        echo '<div class="form-group">';
        echo '<label for="" class="col-sm-2 control-label">请选择彩种</label>';
        echo '<div class="col-sm-6">';
        foreach ($aLotteries as $oLottery) {
            if($oLottery->id == $sValue)
                echo '<label for="'.$oLottery->name.'"><input name="lotteries_limit" type="radio" value="'.$oLottery->id.'" id="'.$oLottery->name.'" checked="checked"/>'.__('_jackpots.'.$oLottery->name).' </label>';
            else
                echo '<label for="'.$oLottery->name.'"><input name="lotteries_limit" type="radio" value="'.$oLottery->id.'" id="'.$oLottery->name.'" />'.__('_jackpots.'.$oLottery->name).' </label>';
        }
        echo '</div>';
        echo '<div class="col-sm-4"></div>';
        echo '</div>';

    }else{
        echo $sHtml;
    }
    ?>



@endforeach
<div class="form-group">
    <div class="col-sm-offset-2 col-sm-6">
      <a class="btn btn-default" href="{{ route($resource. ($isEdit ? '.edit' : '.create'), $data->id) }}">{{ __('Reset') }}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </div>
</div>
{{Form::close()}}
