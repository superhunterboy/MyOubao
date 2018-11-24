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

    <div class="panel panel-default">
        <div class=" panel-body">
            <table class="table table-bordered table-striped">
                <tbody>
                    <?php
                        $i = 0;
                        foreach ($aColumnSettings as $sColumn => $aSetting){
                        $sClass= '';
                        if (in_array($sColumn,$aWeightFields)){
                            $sClass .= ' text-weight';
                        }
                        if (in_array($sColumn,$aClassGradeFields)){
                            $sClass .= ' ' .  ($data->$sColumn >= 0 ? 'text-red' : 'text-green');
                        }
                        if (isset($aViewColumnMaps[ $sColumn ])){
                                $sDisplayValue = $data->{$aViewColumnMaps[ $sColumn ]};
                        }if($sColumn=='transaction_pic_url'){
                            if(is_null($data->$sColumn)){
                                $sDisplayValue = '';
                            }else{
                                $sDisplayValue = '<a tabindex="0" class="btn btn-xs btn-danger" role="button" data-toggle="popover" data-trigger="focus" data-html="true" data-content="<img src='.route('deposits.load-img',[$data->id,$data->$sColumn]).' >">查看图片</a>';
                            }

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
