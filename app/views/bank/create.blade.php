@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Create') . $resourceName }}
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



<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
    'footer'  =>
        Form::open().
            '<button class="btn btn-sm btn-default" type="submit">确认上传</button>'.
            '<button type="submit" class="btn btn-sm btn-danger">取消</button>'.
        Form::close(),
);
?>


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
