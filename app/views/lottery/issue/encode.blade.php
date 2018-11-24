@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')
    @include('w._function_title')


    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach

    @include('lottery.issue.encodeForm')
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                @foreach( $aColumnForList as $sColumn )
                <th>{{ (__($sLangPrev . $sColumn, null, 3)) }} {{ order_by($sColumn) }}</th>
                @endforeach
                @if ($buttons['itemButtons'])
                <th>{{ __('Actions') }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <tr>
                                <?php
                foreach ($aColumnForList as $sColumn) {
    if (isset($aListColumnMaps[ $sColumn ])){
        $sDisplayValue = $data->{$aListColumnMaps[ $sColumn ]};
    }
    else{
        switch ($sColumn){
            case 'begin_time':
            case 'end_time':
            case 'allow_encode_time':
            case 'encode_time':
                $sDisplayValue = $sDisplayValue ? date('Y-m-d H:i:s',$data->$sColumn) : null;
                break;
            case 'lottery_id':
                $sDisplayValue = $aLotteries[ $data->$sColumn ];
                break;
            case 'issue_rule_id':
                $sDisplayValue = $aIssueRules[ $data->$sColumn ];
                break;
            case 'status':
                $sDisplayValue = $aWnNumberStatus[ $data->$sColumn ];
                break;
            case 'numeric':
            case 'date':
            default:
                $sDisplayValue = $data->$sColumn;
        }
    }
    echo "<td>$sDisplayValue</td>";
}
?>
                <td>
                    @include('w.item_link')
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@stop



