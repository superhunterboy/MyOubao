@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    <!--@include('w.page_link', ['id' => $data->id , 'parent_id' => $data->parent_id])-->
    @include('w.notification')
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

    <table class="table table-bordered table-striped">
        <tbody>
    @if(!empty($sParentTitle))
                  <tr>
            <th  class="text-right col-xs-2">{{ __('Parent') }}</th>
            <td>{{ $sParentTitle }}</td>
        </tr>
    @endif
    <?php $i = 0;
    foreach($aColumnSettings as $sColumn => $aSetting){
        if (isset($aSetting['type'])){
            switch($aSetting['type']){
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
                    $aColumn = explode(',', $data->$sColumn);
                    $sDisplayValue = "";
                    foreach ($aColumn as $val) {
                        $sDisplayValue .= ${$aColumnSettings[$sColumn]['options']}[$val] . ",";
                    }
                    if (strlen($sDisplayValue) > 0) {
                        $sDisplayValue = substr($sDisplayValue, 0, strlen($sDisplayValue) - 1);
                    }
                    break;
//                    $sDisplayValue = $data->$sColumn ? ${$aSetting['options']}[$data->$sColumn] : null;
//                    break;
                case 'numeric':
                case 'date':
                default:
                    $sDisplayValue = $data->$sColumn;
            }
        }
        else{
            $sDisplayValue = $data->$sColumn;
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
