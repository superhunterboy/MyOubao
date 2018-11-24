@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    {{ Form::open(array( 'id' => 'create-2', 'class' => 'form-horizontal')) }}

    {{ Form::hidden('step',2) }}
    {{ Form::hidden('series_id',$iSeriesId) }}
    {{ Form::hidden('name',$sName) }}
    {{ Form::hidden('identifier',$sIdentifier) }}
    {{ Form::hidden('frequency',$iFrequency) }}
    {{ Form::hidden('digital_count',$iDigitalCount) }}
    {{ Form::hidden('issue_format',$sIssueFormat) }}

    <table class="table table-bordered table-striped">
        <tbody>
         <tr>
            <th  class="text-right col-xs-2">Series:</th>
            <td> {{ $sSeries }}</td>
        </tr>

 <tr>
            <th  class="text-right col-xs-2">Frequency:</th>
            <td>{{ $iFrequency }}</td>
        </tr>

 <tr>
            <th  class="text-right col-xs-2">Name:</th>
            <td>{{ $sName }}</td>
        </tr>
 <tr>
            <th  class="text-right col-xs-2">Identifier:</th>
            <td> {{ $sIdentifier }}</td>
        </tr>

 <tr>
            <th  class="text-right col-xs-2">Digital:</th>
            <td> {{ $iDigitalCount }}</td>
        </tr>

 <tr>
            <th  class="text-right col-xs-2">Issue Format:</th>
            <td> {{ $sIssueFormat }}</td>
        </tr>

 <tr>
            <th  class="text-right col-xs-2">&nbsp;</th>
            <td>
      <a class="btn btn-default" href="javascript:window.history.back()">{{__('Back')}}</a>
      {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
    </td>
        </tr>
            </tbody>
    </table>


    {{ Form::close() }}
    
@stop
