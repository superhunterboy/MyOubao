@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop
@section('container')
<div class="col-md-12">
@include('w.breadcrumb')
<div style="float:right;">
    <label for="J-checkbox-autorefresh"><input id="J-checkbox-autorefresh" type="checkbox" value="1"> 自动刷新</label>
</div>
@include('w._function_title')
@include('w.notification')


<?php
if (isset($aTotalColumns)){
    $aTotals = array_fill(0, count($aColumnForList),null);
    $aTotalColumnMap = array_flip($aColumnForList);
}
?>
@foreach($aWidgets as $sWidget)
@include($sWidget)
@endforeach
<?php //    die($sSetOrderRoute); ?>
@if ($bSequencable)
{{ Form::open(['action' => $sSetOrderRoute ]) }}
@endif

<div class="panel panel-default">
<table class="table table-striped table-hover table-bordered">
    <thead class="thead-mini  thead-inverse">
        <tr style="border-bottom: 1px solid #fff;">
            <th class="text-center" colspan="12">充值平台信息</th>
            <th class="text-center" colspan="6">渠道查询信息</th>
        </tr>
        <tr>
            @foreach( $aColumnForList as $sColumn )
            <th>{{ (__($sLangPrev . $sColumn, null, 3)) }}</th>
            <?php
                if (isset($aTotalColumns)){
                    in_array($sColumn, $aTotalColumns) or $aTotalColumnMap[$sColumn] = null;
                }
            ?>
            @endforeach
            <th>{{ __('_basic.actions') }}</th>
        </tr>
    </thead>
    <tbody>
<?php $iHighlight=0;?>
        @foreach ($datas as $data)
            <?php
            $bHighlight = (time() - strtotime($data->created_at) > 120) && ($data->status == Deposit::DEPOSIT_STATUS_RECEIVED);
            if ($bHighlight) $iHighlight++;
            ?>
            <tr @if($bHighlight)style='color:red'@endif>
            @foreach ($aColumnForList as $sColumn)
            <?php
                if (isset($aTotalColumns)){
                    is_null($aTotalColumnMap[$sColumn]) or $aTotals[$aTotalColumnMap[$sColumn]] += $data->$sColumn;
                }
//    $sDisplayColumn = isset($aColumnDisplayMaps[ $sColumn ]) ? $aColumnDisplayMaps[ $sColumn ] : $sColumn;
                if (isset($aListColumnMaps[$sColumn])) {
                    $sDisplayValue = $data->{$aListColumnMaps[$sColumn]};
                } else {
                    if ($sColumn == 'sequence') {
                        $sDisplayValue = Form::text('sequence[' . $data->id . ']', $data->sequence, ['class' => 'form-control', 'style' => 'width:70px;text-align:right']);
                    } else {
                        if (isset($aColumnSettings[$sColumn]['type'])) {
                            $sDisplayValue = $sColumn . $aColumnSettings[$sColumn]['type'];
                            switch ($aColumnSettings[$sColumn]['type']) {
                                case 'bool':
                                    $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
                                    break;
                                case 'select':
                                    //                                        $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                                    $sDisplayValue = !is_null($data->$sColumn) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                                    break;
                                default:
                                    $sDisplayValue = is_array($data->$sColumn) ? implode(',', $data->$sColumn) : $data->$sColumn;
                            }
                            if ($sColumn == 'updated_at') {
                                if($data->status==Deposit::DEPOSIT_STATUS_SUCCESS){
                                        $sDisplayValue = $data->updated_at;
                                    }else{
                                        $sDisplayValue = '';
                                    }
                            }
                        }
                        if (array_key_exists($sColumn, $aNumberColumns)) {
                            $sDisplayValue = number_format($sDisplayValue, $aNumberColumns[$sColumn]);
                        }
                    }
                }
                ?>
                <td>{{$sDisplayValue}}</td>
           @endforeach
            <td>
                @include('w.item_link')
            </td>
        </tr>
        @endforeach
    </tbody>
        @if (isset($aTotalColumns))
        <tfoot>
            <tr>
                <td>{{ __('grand-total-per-page') }}</td>
                @for($i = 1; $i < count($aColumnForList); $i++)
                <td class="">{{  is_null($aTotals[$i]) ? ' ' : number_format($aTotals[$i], (array_key_exists($aColumnForList[$i], $aNumberColumns) ? $aNumberColumns[$aColumnForList[$i]] : 2)) }}</td>
                @endfor
                <td></td>
            </tr>
        </tfoot>
        @endif

</table>
</div>

<div class="pull-left">
    @if ($bSequencable)
        {{ Form::submit(__('_basic.set-order',null,2), ['class' => 'btn btn-success btn-b btn-xs']) }}
    @endif
    @include('w.page_batch_link')
</div>
{{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}

@if ($bSequencable)
{{ Form::close() }}
@endif
<?php
//pr($aLangVars);
//exit;
$modalData['modal'] = array(
    'id' => 'myModal',
    'title' => '系统提示',
    'message' => __('_basic.delete-confirm', $aLangVars) . '？',
    'footer' =>
    Form::open(['id' => 'real-delete', 'method' => 'delete']) . '
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-sm btn-danger">确认删除</button>' .
    Form::close(),
);
?>
@include('w.modal', $modalData)
</div>
@stop


@section('javascripts')
@parent
{{ script('jquery.cookie')}}
@stop

@section('end')
@parent
<script>
(function($){
	var COOKIE_NAME = 'autorefresh';
	var freshTime = 10 * 1000;
	$('#J-checkbox-autorefresh').click(function(){
	    if(this.checked){
	        $.cookie(COOKIE_NAME, '1');
	        setTimeout('location.reload()',freshTime);
	    }else{
	        $.cookie(COOKIE_NAME, '', { expires: -1 }); 
	    }
	});
	if($.cookie(COOKIE_NAME) && $.cookie(COOKIE_NAME) == '1'){
	    $('#J-checkbox-autorefresh').get(0).checked = true;
		setTimeout('location.reload()',freshTime);
	}else{
	    $('#J-checkbox-autorefresh').get(0).checked = false;
	}
    
})(jQuery);

    function modal(href)
    {
    	$('#real-delete').attr('action', href);
        $('#myModal').modal();
    }

</script>
@stop
