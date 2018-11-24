@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

    <table class="table table-bordered table-striped">
        <tbody>

    @if(!empty($sParentTitle))

<!--        <tr>
            <th  class="text-right col-xs-2">{{ __('_basic.parent',null,2) }}</th>
            <td>{{ $sParentTitle }}</td>
        </tr>-->
    @endif
    <?php
//    pr($aColumnSettings);
    $i = 0;
    foreach ($aColumnSettings as $sColumn => $aSetting){
    if (isset($aViewColumnMaps[ $sColumn ])){
            $sDisplayValue = $data->{$aViewColumnMaps[ $sColumn ]};
    }
    else{
        if (isset($aSetting[ 'type' ])){
            switch ($aSetting[ 'type' ]){
                case 'ignore':
                    continue 2;
                    break;
                case 'bool':
                    $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                    break;
                case 'text':
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
            <td>{{{ $sDisplayValue }}}</td>
        </tr>
<?php
}
?>
    </tbody>
    </table>
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop
