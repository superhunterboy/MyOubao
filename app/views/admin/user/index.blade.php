@extends('l.admin', ['active' => $resource])

@section('title')
@parent
{{ $resourceName . __('Management') }}
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')

    @include('w._function_title')
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                @foreach( $aColumnForList as $sColumn )
                    <th>{{ __($sLangPrev . $sColumn) }}
                        @if (!in_array($sColumn, $aNoOrderByColumns))
                        {{ order_by($sColumn) }}
                        @endif</th>
                @endforeach
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <?php
                    foreach ($aColumnForList as $sColumn){
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
                                            $sDisplayValue = $data->$sColumn ? __('Yes') : __('No');
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
        </table>

        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>

<?php

$aBlockedTypes = array_slice($aBlockedTypes, 1);
$aBlockedTypeSelects = '';
foreach ($aBlockedTypes as $element => $value) {
    $aBlockedTypeSelects .= '<option value="' . ((int)$element + 1) . '">' . __('_user.'.$value) . '</option>';
}

$modalData['modal'] = [
    'id'      => 'myModal',
    'title'   => 'Action Confirmation',
    'message' => __('Confirm execute this action ?'),
    'footer'  =>
        Form::open(['id' => 'real-delete', 'method' => 'delete']) . '
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('Confirm Delete') . '</button>'.
        Form::close(),
];
$modalData2['modal'] = [
    'id'      => 'myModal2',
    'title'   => __('_user.block-user-title'),
    'action'  => 'block-user',
    'method'  => 'put',
    'message' =>
        join('', [
        // '<div class="form-group">',
        //     '<label for="is_include_children" class="col-sm-5 control-label">' . __('Blocked User') . '</label>',
        //     '<label class="col-sm-5 control-label" id="username">',
        //     '</label>',
        // '</div>',
        '<div class="form-group">',
            '<label for="blocked" class="col-sm-5 control-label">' . __('_user.block-type') . '</label>',
            '<div class="col-sm-5">',
                '<select class="form-control" name="blocked" id="blocked" >',
                    $aBlockedTypeSelects,
                '</select>',
            '</div>',
        '</div>',
        '<div class="form-group">',
            '<label for="is_include_children" class="col-sm-5 control-label">' . __('_user.include-sub-users') . '</label>',
            '<div class="col-sm-5">',
                '<div class="switch " data-on-label="' . __('Yes') . '"  data-off-label="' . __('No') . '">',
                    '<input type="checkbox" name="is_include_children" id="is_include_children" value="1">',
                '</div>',
            '</div>',
        '</div>',
        '<div class="form-group">',
            '<label for="comment" class="col-sm-5 control-label">' . __('_usermanagelog.comment') . '</label>',
            '<div class="col-sm-5">',
                '<textarea name="comment" cols="30" rows="2"></textarea>',
            '</div>',
        '</div>'
        ]),
    'footer'  =>
        '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('_function.cancel') . '</button>' .
        '<button type="submit" class="btn btn-sm btn-danger">' . __('_function.confirm') . '</button>'
];
$modalData3['modal'] = [
    'id'      => 'myModal3',
    'title'   => __('_user.unblock-user-title'),
    'action'  => 'unblock-user',
    'method'  => 'put',
    'message' =>
        join('', [
        // '<div class="form-group">',
        //     '<label for="is_include_children" class="col-sm-5 control-label">' . __('Blocked User') . '</label>',
        //     '<label class="col-sm-5 control-label" id="username">',
        //     '</label>',
        // '</div>',
        '<div class="form-group">',
            '<label for="is_include_children" class="col-sm-5 control-label">' . __('_user.include-sub-users') . '</label>',
            '<div class="col-sm-5">',
                '<div class="switch " data-on-label="' . __('Yes') . '"  data-off-label="' . __('No') . '">',
                    '<input type="checkbox" name="is_include_children" id="is_include_children" value="1">',
                '</div>',
            '</div>',
        '</div>',
        '<div class="form-group">',
            '<label for="comment" class="col-sm-5 control-label">' . __('_usermanagelog.comment') . '</label>',
            '<div class="col-sm-5">',
                '<textarea name="comment" cols="30" rows="2"></textarea>',
            '</div>',
        '</div>'
        ]),
    'footer'  =>
        '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('_function.cancel') . '</button>' .
        '<button type="submit" class="btn btn-sm btn-danger">' . __('_function.confirm') . '</button>'
];

$modalData4['modal'] = [
    
    'id'      => 'myModal4',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.enable withdrawal'),
    'action'  => 'add-user-to-withdraw-white-list',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.enable withdrawal'). '</button>',
];

$modalData5['modal'] = [
    
    'id'      => 'myModal5',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.block withdrawal'),
    'action'  => 'add-user-to-withdraw-black-list',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' . __('_function.block withdrawal' ). '</button>',
];
$modalData6['modal'] = [
    
    'id'      => 'myModal6',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.enable icbc deposit'),
    'action'  => 'icbc-recharge-whitelist-user',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' .__('_function.enable icbc deposit') . '</button>',
];

$modalData7['modal'] = [
    
    'id'      => 'myModal7',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.lock bankcards'),
    'action'  => 'lock-bankcards',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' .__('_function.lock bankcards') . '</button>',
];

$modalData8['modal'] = [
    
    'id'      => 'myModal8',
    'title'   => 'Action Confirmation',
    'message' =>__('_function.unlock bankcards'),
    'action'  => 'unlock-bankcards',
    'method'  => 'put',
    'footer'  =>' <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">' . __('Cancel') . '</button>
            <button type="submit" class="btn btn-sm btn-danger">' .__('_function.unlock bankcards') . '</button>',
];
?>
    @include('w.modal', $modalData)
    @include('w.formModal', $modalData2)
    @include('w.formModal', $modalData3)
    @include('w.formModal', $modalData4)
    @include('w.formModal', $modalData5)
    @include('w.formModal', $modalData6)
    @include('w.formModal', $modalData7)
    @include('w.formModal', $modalData8)

@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
        function setBlockedStatus(href, username)
        {
            $('#block-user').attr('action', href);
            // debugger;
            $('#myModal2').modal();
            // $('#username').text(username);
        }
        function setUnblockedStatus(href)
        {
            $('#unblock-user').attr('action', href);
            $('#myModal3').modal();
            // $('#username').text(username);
        }
        // jQuery(document).ready(function($) {
        //     setBlockedStatus('');
        // });
        
        
        //允许提现
        function addUserToWithdrawalWhiteList(href)
        {
            
            $('#add-user-to-withdraw-white-list').attr('action', href);
            $('#myModal4').modal();
        }
        //禁止提现
        function addUserToWithdrawalBlackList(href)
        {
            
            $('#add-user-to-withdraw-black-list').attr('action', href);
            $('#myModal5').modal();
        }
        //开启工行
        function addUserToICBCRechargeWhiteList(href){
            $('#icbc-recharge-whitelist-user').attr('action', href);
            $('#myModal6').modal();
        }
        //锁定银行卡
        function lockUserBankCards(href){
            $('#lock-bankcards').attr('action', href);
            $('#myModal7').modal();
        }
        //解锁银行卡
           function unlockUserBankCards(href){
            $('#unlock-bankcards').attr('action', href);
            $('#myModal8').modal();
        }

    </script>
@stop

