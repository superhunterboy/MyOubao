@extends('l.admin', array('active' => 'admin'))


@section('container')
    <div class="page-header">
      <h2>{{ __('_basic.welcome') }}{{ __('_basic.app-name') }} </h2>
    </div>
    <div class="row clearfix">
        <div class="col-xs-3">
            <div class="callout callout-default">
              <h4>{{ __('APP') . __('Version') }}</h4>
              <p>{{ $sysInfo['app_version'] }}</p>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="callout callout-danger">
              <h4>PHP {{ __('Version') }}</h4>
              <p>{{ $sysInfo['php_version'] }}</p>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="callout callout-warning">
              <h4>{{ __('Server') . __('OS') }}</h4>
              <p>{{ $sysInfo['os'] }}</p>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="callout callout-info">
              <h4>Web {{ __('Server') }}</h4>
              <p>{{ $sysInfo['web_server'] }}</p>
            </div>
        </div>
    </div>

@stop


