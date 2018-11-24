@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
<div class="col-md-12">
@include('w.breadcrumb')
    @include('w._function_title', ['id' => $data->id , 'parent_id' => $data->parent_id])

    @include('w.notification')
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class=" panel-body">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <?php
                            $i = 0;
                            $id = null;
                            $excludeFields = ['deposit_mode', 'created_at','error_msg','status_commission','commission_sent_at','accept_bank_address','updated_at','service_order_no','service_time','service_order_status','service_bank_seq_no','notify_type','notify_data','collection_bank_id','real_amount','pay_time','accepter_id','id', 'forfather_ids','user_id', 'top_agent_id', 'top_agent', 'user_forefather_ids','web_url','fee','ip','commission','platform','platform_id','platform_identifier','user_parent'];
                            foreach ($aColumnSettings as $sColumn => $aSetting){
                                if($sColumn == 'id'){
                                    $id = $data->id;
                                }
                                if(in_array($sColumn, $excludeFields)){
                                    continue;
                                }
                            $sClass= '';
                            if (in_array($sColumn,$aWeightFields)){
                                $sClass .= ' text-weight';
                            }
                            if (in_array($sColumn,$aClassGradeFields)){
                                $sClass .= ' ' .  ($data->$sColumn >= 0 ? 'text-red' : 'text-green');
                            }
                            if (isset($aViewColumnMaps[ $sColumn ])){
                                    $sDisplayValue = $data->{$aViewColumnMaps[ $sColumn ]};
                            }else if($sColumn=='transaction_pic_url'){
                                // $sDisplayValue = '<a href="'.route('deposits.load-img',[$data->id,$data->$sColumn]).'">查看图片</a>';
                                // $sDisplayValue = '<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="right" title="<img src='.route('deposits.load-img',[$data->id,$data->$sColumn]).' ">查看图片</button>';

                                $sDisplayValue = '';

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
                                }else{
                                    $sDisplayValue = $data->$sColumn;
                                }
                            }
                            if (array_key_exists($sColumn,$aNumberColumns) && !array_key_exists($sColumn, $aViewColumnMaps)){
                                $sDisplayValue = number_format($sDisplayValue,$aNumberColumns[ $sColumn ]);
                            }
                        ?>
                        <tr>
                            <th  class="text-right col-xs-2">{{ __($sLangPrev . $sColumn, null, 2) }}</th>
                            <td class="{{ $sClass }}">{{ $sDisplayValue }}</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="panel panel-default">

            <div class=" panel-body">
                <div class="alert alert-warning" role="alert">拒绝理由：{{$data->note}}</div>
            </div>
            <div class="panel-footer" style=" position:relative;">
                <form action="{{route('deposits.reject', $id)}}" method="get" >
                    <div class="form-group">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <input type="text" name='note' class="form-control" placeholder="请填写拒绝理由">
                    </div>
                    <button type="submit" class="btn btn-danger">拒绝</button>
                </form>
                <form action="{{route('deposits.set-wait-load', $id)}}" method="post" style="position: absolute;right: 15px;bottom: 15px;">
                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                        <input class="btn btn-success" type="submit" value="通过" />
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('end')
    @parent
    <script>
    $('[data-toggle="popover"]').popover()
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop
