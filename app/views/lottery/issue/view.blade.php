@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ __('View') . $resourceName }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.page_link', ['id' => $data->id , 'parent_id' => $data->parent_id])
    @include('w.notification')

     <div class="row">
        <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('View') . $resourceName }} </div>
      </div>
    </div>
    <hr>
    <table class="table table-bordered table-striped">
        <tbody>
    @if(!empty($sParentTitle))
                  <tr>
            <th  class="text-right col-xs-2">{{ __('Parent') }}</th>
            <td>{{ $sParentTitle }}</td>
        </tr>
    @endif
    <?php $i = 0;
//    pr($aIssueRules);exit;
//    pr($aColumnSettings);exit;
    foreach($aColumnSettings as $sColumn => $aSetting){
            switch($sColumn){
                case 'begin_time':
                case 'end_time':
                case 'allow_encode_time':
                    $sDisplayValue = date('Y-m-d H:i:s',$data->$sColumn);
                    break;
                case 'lottery_id':
                    $sDisplayValue = $aLotteries[$data->$sColumn];
                    break;
                case 'issue_rule_id':
                    $sDisplayValue = $aIssueRules[$data->$sColumn];
                    break;
                case 'status':
                    $sDisplayValue = $aIssueStatus[$data->$sColumn];
                    break;
                case 'numeric':
                case 'date':
                default:
                    $sDisplayValue = $data->$sColumn;
            }

        ?>
         <tr>
            <th  class="text-right col-xs-2">{{ __($sColumn) }}</th>
            <td>{{{ $sDisplayValue }}}</td>
        </tr>
<?php
    }
    ?>
    </tbody>
    </table>

@stop

