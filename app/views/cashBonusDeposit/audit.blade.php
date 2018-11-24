@extends('l.admin', array('active' => $resource))

@section('title')
    @parent
    {{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')



    <?php
    $oFormHelper->setErrorObject($errors);
    ?>
    {{ Form::model($data, array('method' => 'post', 'class' => 'form-horizontal')) }}
    <table class="table table-bordered table-striped">
        <?php
        foreach ($aColumnSettings as $sColumn => $aSetting){
        if (isset($aViewColumnMaps[ $sColumn ])){
            $sDisplayValue = $data->{$aViewColumnMaps[ $sColumn ]};
        }else{
            if (isset($aSetting[ 'type' ])){
                switch ($aSetting[ 'type' ]){
                    case 'ignore':
                        continue 2;
                        break;
                    case 'bool':
                        $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                        break;
                    case 'text':
                        if(isset($ableEdit[$sColumn]))
                            $sDisplayValue = '<input type="'.$ableEdit[$sColumn].'"  name="'.$sColumn.'" class="form-control id="'.$sColumn.'" value="'.$oBonus->$sColumn.'">';
                        else
                            $sDisplayValue = nl2br($data->$sColumn);
                        break;
                    case 'select':
                        $sDisplayValue = null;
                        if (!is_null($data->$sColumn) && isset(${$aSetting[ 'options' ]}[ $data->$sColumn ])){
                            $sDisplayValue = ${$aSetting[ 'options' ]}[ $data->$sColumn ];
                        }
                        break;
                    case 'numeric':
                    case 'date':
                    default:
                        $sDisplayValue = $data->$sColumn;
                }
            }
            else{
                $sDisplayValue = $data->$sColumn;
            }
        }
        ?>
        <tr>
            <th  class="text-right col-xs-2">{{ __($sLangPrev . $sColumn, null, 2) }}</th>
            <td>{{ $sDisplayValue }}</td>
        </tr>
        <?php  }
        ?>
        <tr><th></th><td><input type="radio" name="step" value="2" id="step_access"  checked="1">审核通过&nbsp;<input type="radio" name="step" value="3">拒绝通过</td></tr>
    </table>







    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            {{ Form::submit('审核', ['class' => 'btn btn-success','id'=>'access_btn']) }}
        </div>
    </div>
    {{Form::close()}}








@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
