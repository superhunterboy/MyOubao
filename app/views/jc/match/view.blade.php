@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

    @if(!empty($sParentTitle))

<!--        <tr>
            <th  class="text-right col-xs-2">{{ __('_basic.parent',null,2) }}</th>
            <td>{{ $sParentTitle }}</td>
        </tr>-->
    @endif
    <?php
//    pr($aColumnSettings);
    $i = 0;
            $th = '';
            $td = '';
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
                    $sDisplayValue = !is_null($data->$sColumn) ? ${$aSetting[ 'options' ]}[ $data->$sColumn ] : null;
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
        if(!in_array($sColumn,array('crs','had','hhad','hafu','ttg'))) {
            $td .= '<td>'.$sDisplayValue.'</td>';
            $th .= '<th>'.__($sLangPrev . $sColumn, null, 2).'</th>';
        }else{
            $$sColumn = $sDisplayValue;
        }
?>
<?php
}
?>
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
            <tr>
                {{$th}}
            </tr>
        </thead>
        <tbody>
        <tr>
            {{$td}}
        </tr>
        </tbody>
    </table>
</div>
@if(isset($had['key']))
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th colspan="{{count($had['key'])}}" class="text-center">
                {{__($sLangPrev . 'had', null, 2)}}
            </th>
        </tr>
        <tr>
            @foreach($had['key'] as $h)
            <td>{{$h}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($had['value'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
@endif
@if(isset($hhad['key']))
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th colspan="{{count($hhad['key'])}}" class="text-center">
                {{__($sLangPrev . 'hhad', null, 2)}}({{$hhad['fixedodds']}})
            </th>
        </tr>
        <tr>
            @foreach($hhad['key'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($hhad['value'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
@endif
@if(isset($crs['key']))
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th colspan="{{count($crs['key'])}}" class="text-center">
                {{__($sLangPrev . 'crs', null, 2)}}
            </th>
        </tr>
        <tr>
            @foreach($crs['key'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($crs['value'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
@endif
@if(isset($ttg['key']))
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th colspan="{{count($ttg['key'])}}" class="text-center">
                {{__($sLangPrev . 'ttg', null, 2)}}
            </th>
        </tr>
        <tr>
            @foreach($ttg['key'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($ttg['value'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
@endif
@if(isset($hafu['key']))
<div class="col-xs-12 J-tab-chart">
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th colspan="{{count($hafu['key'])}}" class="text-center">
                {{__($sLangPrev . 'hafu', null, 2)}}
            </th>
        </tr>
        <tr>
            @foreach($hafu['key'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </thead>
        <tbody>
        <tr>
            @foreach($hafu['value'] as $h)
                <td>{{$h}}</td>
            @endforeach
        </tr>
        </tbody>
    </table>
</div>
@endif
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
