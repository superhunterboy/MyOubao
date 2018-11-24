@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section ('styles')
    @parent
    {{ style('ueditor') }}
@stop


@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    @include('bank.detailForm')

@stop

@section('javascripts')
    @parent
    {{ script('ueditor.config') }}
    {{ script('ueditor.min') }}
    {{ script('zh-cn') }}
@stop

@section('end')
     {{ script('bootstrap-switch') }}
    @parent

    <script>
      
       UE.getEditor('editorBank');
    </script>
@stop
