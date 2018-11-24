@extends('l.admin')

@section ('styles')
    @parent
    {{ style('ueditor') }}
@stop

@section('container')


<script id="editor" type="text/plain" style="width:1024px;height:500px;"></script>



@stop

@section('javascripts')
    @parent
    {{ script('ueditor.config') }}
    {{ script('ueditor.min') }}
    {{ script('zh-cn') }}
@stop

@section('end')
    <script type="text/javascript">

        UE.getEditor('editor');

    </script>
@stop