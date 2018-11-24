@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $resourceName . __('Management') }}
@stop
@section('container')

  @include('w.breadcrumb')
  @include('w.notification')
  <div class="row">
      <div class="col-xs-4">
          <div class="h1" style="line-height: 32px;">{{ __('Generate ') . $resourceName }}</div>
      </div>
      <div class="col-xs-8">
          <div class="pull-right">
              <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                  &laquo; {{ __('Return') . $resourceName . __('List') }}
              </a>
          </div>
      </div>
  </div>
  <hr>
  <?php
    $oFormHelper->setErrorObject($errors);
    $oFormHelper->setModel(new ManIssue);
?>
<div class="col-md-12 clearfix" style=" margin-bottom:20px;">

  <div class="col-md-6">
    {{ Form::open(array( 'class' => 'form-horizontal')) }}
    {{ Form::hidden('step',1) }}
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Game</label>
        <div class="col-sm-6">
          <label class="control-label text-success">{{$sLotteryName}}</label>
        </div>
    </div>
    <input type="hidden" name="lottery_id" value="{{$lottery_id}}" />
    <input type="hidden" name="last_issue" value="{{$sLastIssue}}" />
    {{$oFormHelper->input('begin_date',$dBeginDate,['id' => 'begin_date', 'type' => 'date', 'class' => 'form-control']);}}
    {{$oFormHelper->input('end_date',null,['id' => 'end_date', 'type' => 'date', 'class' => 'form-control']);}}
    @if ($bNeedBeginIssue) 
    {{$oFormHelper->input('begin_issue',null,['id' => 'begin_issue', 'type' => 'text', 'class' => 'form-control']);}}
    @endif
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Bonus Date</label>
        <div class="col-sm-6">
          <?php
            foreach($aBonusDays as $iWeekDay)
            {
                  echo '<code style="float:left;margin:1px;">' . __($weeks[$iWeekDay]) . '</code>';
            }
          ?>
        </div>
    </div>
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Issue Format</label>
      <div class="col-sm-6">
        <label class="control-label text-success">{{$sIssueFormat}}</label>
      </div>
    </div>
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Last Draw</label>
      <div class="col-sm-6">
        <label class="control-label text-success">{{$sLastIssue}}</label>
      </div>
    </div>
    <div class="form-group">
      <label for="name" class="col-sm-2 control-label">Begin Date</label>
      <div class="col-sm-6">
        <label class="control-label text-success">{{$dBeginDate}}</label>
      </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-6">
            {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
        </div>
    </div>
  {{ Form::close() }}
  </div>

  <div class="col-md-6" style="background:#f2f2f2;">
    <?php
      if (isset($aIssueRules)):
      foreach($aIssueRules as $aInfo):
    ?>
        <table class="table table-bordered" style="background: #fff;margin-top: 15px;">
            <tr>
                <td><?php echo __('Begin Time') ?></td>
                <td><?php echo $aInfo->begin_time; ?></td>
                <td><?php echo __('End Time') ?></td>
                <td><?php echo $aInfo->end_time; ?></td>
            </tr>
            <tr>
                <td><?php echo __('Cycle') ?></td>
                <td><?php echo $aInfo->cycle ?></td>
                <td><?php echo __('First Issue End Time') ?></td>
                <td><?php echo $aInfo->first_time; ?></td>
            </tr>
            <tr>
                <td><?php echo __('Stop Adjst Time') ?></td>
                <td><?php echo $aInfo->stop_adjust_time; ?></td>
                <td><?php echo __('Encode Time') ?></td>
                <td><?php echo $aInfo->encode_time; ?></td>
            </tr>
        </table>

    <?php
      endforeach;
    endif;
    ?>
  </div>


</div>



@stop

@section('javascripts')
@parent
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@stop

@section('end')
@parent
  <script>
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
  </script>
@stop
