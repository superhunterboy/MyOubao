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

    @include('cms.lotteryinfo.detailForm')

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
    //add-AD-img
    var i=2;
    $('a[name=plus-img]').click(function(){

        var html = '<div class="file-img-box">'
                  +'<input type="file" class="form-control" style="padding:5px;" name="image'+i+'" >'
                  +'<span class="glyphicon glyphicon-remove form-control-feedback-img" onclick="removeDiv(this);"></span>'
                  +'<div>';

        $('div[name=file-img]').append(html);
        $('input[name=btnCount]').val(i);
        return i++;
    })

    function removeDiv(dome){
        $(dome).parent().remove();

    };
            UE.getEditor('editor');
</script>
@stop
