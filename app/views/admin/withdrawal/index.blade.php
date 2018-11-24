@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('styles')
@parent
<style type="text/css">

@-webkit-keyframes twinkling{ 
0%{ 
opacity: 0.5; 
} 
100%{ 
opacity: 1; 
} 
} 
@keyframes twinkling{ 
0%{ 
opacity: 0.5; 
} 
100%{ 
opacity: 1; 
} 
}
.table>tbody>.active>td,.table>tbody>.active:nth-child(odd)>td
{
-webkit-animation-duration: 1s; 
animation-duration: 1s; 
-webkit-animation-fill-mode: both; 
animation-fill-mode: both;

animation: twinkling 1s infinite;

background-color: #FFF379;
}
</style>
@stop


@section('container')

    @include('w.breadcrumb')
    @include('w.notification')
    
    <div style="text-align:right;">
        <label for="J-checkbox-autorefresh"><input id="J-checkbox-autorefresh" type="checkbox" value="1"> 自动刷新</label>
    </div>

    @include('w._function_title')

    


<?php
// pr($aWidgets);
// exit;
?>
<?php
if (isset($aTotalColumns)){
    $aTotals = array_fill(0, count($aColumnForList),null);
    $aTotalColumnMap = array_flip($aColumnForList);
}
?>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <?php // die($sSetOrderRoute); ?>
    @if ($bSequencable)
    {{ Form::open(['action' => $sSetOrderRoute ]) }}
    @endif
    <div class="col-xs-12">

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                @foreach( $aColumnForList as $sColumn )
                    <th>{{ (__($sLangPrev . $sColumn, null, 3)) }}
                        @if (!in_array($sColumn, $aNoOrderByColumns))
                        {{ order_by($sColumn) }}
                        @endif
                    </th>
                    <?php
                    if (isset($aTotalColumns)){
                        in_array($sColumn, $aTotalColumns) or $aTotalColumnMap[$sColumn] = null;
                    }
                    ?>
                @endforeach
                    <th>{{ __('_basic.actions') }}</th>
                </tr>
            </thead>
            <tbody id="J-tbody">
                @foreach ($datas as $data)
                <tr status='{{ $data->status }}'>
                    <?php
                    foreach ($aColumnForList as $sColumn){
                        if (isset($aTotalColumns)){
                            is_null($aTotalColumnMap[$sColumn]) or $aTotals[$aTotalColumnMap[$sColumn]] += $data->$sColumn;
                        }
                    //    $sDisplayColumn = isset($aColumnDisplayMaps[ $sColumn ]) ? $aColumnDisplayMaps[ $sColumn ] : $sColumn;
                        if (isset($aListColumnMaps[ $sColumn ])){
                            $sDisplayValue = $data->{$aListColumnMaps[ $sColumn ]};
                        }
                        else{
                            if ($sColumn == 'sequence'){
                                $sDisplayValue = Form::text('sequence[' . $data->id . ']',$data->sequence,['class' => 'form-control','style' => 'width:70px;text-align:right']);
                            }
                            else{
                                if (isset($aColumnSettings[ $sColumn ][ 'type' ])){
                                    $sDisplayValue = $sColumn . $aColumnSettings[ $sColumn ][ 'type' ];
                                    switch ($aColumnSettings[ $sColumn ][ 'type' ]){
                                        case 'bool':
                                            $sDisplayValue = !is_null($data->$sColumn) ? ($data->$sColumn ? __('Yes') : __('No')) : null;
                                            break;
                                        case 'select':
                                            //                                        $sDisplayValue = (isset($data->$sColumn) && !is_null($data->$sColumn)) ? ${$aColumnSettings[$sColumn]['options']}[$data->$sColumn] : null;
                                            $sDisplayValue = !is_null($data->$sColumn) ? ${$aColumnSettings[ $sColumn ][ 'options' ]}[ $data->$sColumn ] : null;
                                            break;
                                        default:
                                            $sDisplayValue = is_array($data->$sColumn) ? implode(',',$data->$sColumn) : $data->$sColumn;
                                    }
                                }
                                else{
                                    $sDisplayValue = $data->$sColumn;
                                }
                                if (array_key_exists($sColumn,$aNumberColumns)){
                                    $sDisplayValue = number_format($sDisplayValue,$aNumberColumns[ $sColumn ]);
                                }
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
    <div class="form-group">
        <?php $pageClass = $bSequencable ? 'col-sm-6' : 'col-sm-12'?>
        @if ($bSequencable)
        <div class="col-sm-6">
          {{ Form::submit(__('_basic.set-order',null,2), ['class' => 'btn btn-success']) }}
        </div>
        @endif
        <div class="{{ $pageClass }}">
        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
        </div>
    </div>
    @if ($bSequencable)
    {{ Form::close() }}
    @endif
    <?php
        $modalData['modal'] = array(
            'id'      => 'myModal',
            'title'   => '系统提示',
            'message' => '确认删除此'.$resourceName.'？',
            'footer'  =>
                Form::open(['id' => 'real-delete', 'method' => 'delete']).'
                    <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-sm btn-danger">确认删除</button>'.
                Form::close(),
        );
        $modalData2['modal'] = [
            'id'      => 'myModal2',
            'title'   => __('_withdrawal.refuse-withdrawal-request'),
            'action'  => 'refuse-withdrawal',
            'method'  => 'put',
            'message' =>
                join('',
                [
                '<div class="form-group">',
                    '<label for="error_msg" class="col-sm-3 control-label">' . __('_withdrawal.refuse-message') . '</label>',
                    '<div class="col-sm-5">',
                            '<textarea name="error_msg" id="error_msg"></textarea>',
                    '</div>',
                '</div>'
                ]),
            'footer'  =>
                '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('_function.Cancel') . '</button>' .
                '<button type="submit" class="btn btn-sm btn-danger">' . __('_function.Confirm') . '</button>'
        ];
        $modalData3['modal'] = [
            'id'      => 'myModal3',
            'title'   => __('_withdrawal.waiting-for-comfirmation'),
            'action'  => 'waiting-withdrawal',
            'method'  => 'put',
            'message' =>
                join('',
                [
                '<div class="form-group">',
                    '<label for="remark" class="col-sm-3 control-label">' . __('_withdrawal.remark-message') . '</label>',
                    '<div class="col-sm-5">',
                            '<textarea name="remark" id="remark"></textarea>',
                    '</div>',
                '</div>'
                ]),
            'footer'  =>
                '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('_function.Cancel') . '</button>' .
                '<button type="submit" class="btn btn-sm btn-danger">' . __('_function.Confirm') . '</button>'
        ];
         $modalData4['modal'] = [
            'id'      => 'myModal4',
            'title'   => __('_function.manual set success to withdrawal'),
    'message' =>__('_withdraw.confirm_set_sucess'),
            'action'  => 'manual-set-to-success',
            'method'  => 'get',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.Confirm' ). '</button>',
        ];  
         $modalData5['modal'] = [
            'id'      => 'myModal5',
            'title'   => __('_function.manual set failure to withdrawal'),
    'message' =>__('_withdraw.confirm_set_failure'),
            'action'  => 'manual-set-to-failure',
            'method'  => 'get',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.Confirm' ). '</button>',
        ];
    ?>
    @include('w.modal', $modalData)
    @include('w.formModal', $modalData2)
    @include('w.formModal', $modalData3)
    @include('w.formModal', $modalData4)
    @include('w.formModal', $modalData5)



<span id="J-notes-audio"></span>
@stop

@section('javascripts')
@parent
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
{{ script('jquery.cookie')}}
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            if (! href || href == 'javascript:void(0);') return false;
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
        function refuseWithdrawal(href)
        {
            if (! href || href == 'javascript:void(0);') return false;
            $('#refuse-withdrawal').attr('action', href);
            $('#myModal2').modal();
        }
        function manualSetToSuccess(href)
        {
            if (! href || href == 'javascript:void(0);') return false;
            $('#manual-set-to-success').attr('action', href);
            $('#myModal4').modal();
        }
         function manualSetToFailure(href)
        {
            if (! href || href == 'javascript:void(0);') return false;
            $('#manual-set-to-failure').attr('action', href);
            $('#myModal5').modal();
        }
        
        function waitingForConfirmation(href)
        {
            if (! href || href == 'javascript:void(0);') return false;
            $('#waiting-withdrawal').attr('action', href);
            $('#myModal3').modal();
        }
        @if ($bNeedCalendar)
        $('.form_date').datetimepicker({
          language:  'zh-CN',
          weekStart: 1,
          todayBtn:  1,
          autoclose: 1,
          todayHighlight: 1,
          startView: 2,
          minView: 2,
          forceParse: 0,
          showMeridian: 1,
          pickerPosition: 'bottom-left'
        });
        @endif
        function playWarning(){
        	if ($("#J-notes-audio").html() == ''){
        		var obj='<object type="application/x-shockwave-flash" data="/assets/img/dewplayer.swf" width="1" height="1" id="dewplayer" name="dewplayer">'+ 
        		'<param name="wmode" value="transparent" />'+
        		'<param name="movie" value="/assets/img/dewplayer.swf" />'+
        		'<param name="flashvars" value="mp3=/assets/img/mao.mp3&autostart=1&autoreplay=true&volume=100" />'+ 
        		'</object>';
        		$("#J-notes-audio").html(obj);
        	}
        } 


//检测是否有待审核的记录
(function($){
    var trs = $('#J-tbody > tr'),tds,CLS = 'active',num = 0,audio;
    var COOKIE_NAME = 'autorefresh';
    trs.each(function(i){
        if($(this).attr('status') == 1){
            trs.eq(i).addClass(CLS);
            num++;
        }
//        tds = this.getElementsByTagName('td');
//        if($.trim(tds[15].innerHTML) == '待审核'){
//            trs.eq(i).addClass(CLS);
//            num++;
//        }
    });
    if(num > 0){
    	playWarning();

        $('#J-checkbox-autorefresh').get(0).checked = false;
        $.cookie(COOKIE_NAME, '', { expires: -1 }); 
    }
    var refresh = function(){
        if(num > 0){
            return;
        }
        setTimeout(function(){
            location.href = location.href;
        }, 60 * 1000);
    };


    
    $('#J-checkbox-autorefresh').click(function(){
        if(this.checked){
            $.cookie(COOKIE_NAME, '1');
            refresh();
        }else{
            $.cookie(COOKIE_NAME, '', { expires: -1 }); 
        }
    });
    if($.cookie(COOKIE_NAME) && $.cookie(COOKIE_NAME) == '1'){
        refresh();
        $('#J-checkbox-autorefresh').get(0).checked = true;
    }else{
        $('#J-checkbox-autorefresh').get(0).checked = false;
    }



})(jQuery);



    </script>
@stop


