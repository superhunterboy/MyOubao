@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    @include('ways.series-way-method.detailForm')

@stop

@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
