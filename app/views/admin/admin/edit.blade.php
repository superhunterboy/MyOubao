@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop


@section('container')

    @include('w.breadcrumb')
    @include('w.notification')


   <div class="row">
      <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('Edit') . $resourceName }} </div>
      </div>
      <div class="col-xs-8">
        <div class="pull-right">
            <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                &laquo; {{ __('Return') .  $resourceName }}
            </a>
        </div>
      </div>
    </div>
    <hr>

    <form class="form-horizontal" method="post" action="{{ route($resource.'.edit', $data->id) }}" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_method" value="PUT" />

        @include('admin.admin.detailForm')

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
              <button type="reset" class="btn btn-default" >{{ __('Reset') }}</button>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>

@stop
@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop