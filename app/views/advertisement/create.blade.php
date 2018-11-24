@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Create') . $resourceName }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')


    {{ Form::open(['route' => ($resource.'.create'), 'method' => 'post', 'files' => true, 'class' => 'form-horizontal']) }}
        @include('advertisement.detailForm')
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-9">
              <a class="btn btn-default" href="{{ route($resource.'.create') }}">{{ __('Reset') }}</a>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>

            </div>
        </div>
    {{ Form::close() }}


<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
    'footer'  =>
        Form::open().
            // '<button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
            // <button type="submit" class="btn btn-sm btn-danger">确认删除</button>'.
            '<button class="btn btn-sm btn-default" type="submit">确认上传</button>'.
            '<button type="submit" class="btn btn-sm btn-danger">取消</button>'.
        Form::close(),
);
?>


@stop



@section('end')
    {{ script('bootstrap-switch') }}
    @parent

    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }

        $('#ad_location_id').change(function(){
            var valData = $(':selected').attr('aType');
            if( (/^marquee/i).exec(valData) ){
                $('.pics-box').show();
                $('.pic-box,.text-box').hide();
            }else if( (/^pic/i).exec(valData)){
                $('.pic-box,.text-box').show();
                $('.pics-box').hide();
            }else{
                $('.pic-box,.pics-box').hide();
                $('.text-box').show();
            }
        });



        //add-AD-img
        $('a[name=plus-img]').click(function(){
            var html =  '<hr/><div class="row">'+
                            '<div class="col-sm-10"><div class="row">'+
                        '<div class="col-sm-6" style="margin-bottom:5px;"><input type="file" name="portrait[]" class="form-control" style="padding:5px;"></div>'+
                        '<div class="col-sm-6"><input class="form-control" name="ad_url[]" type="text" placeholder="{{ __('AD URL') }}"  value="" /></div>'+
                         '<div class="col-sm-12"><input class="form-control" type="text"   placeholder="*{{ __('Content') }}" name="content[]" id="content" value="" /></div>'+
                          '</div></div><div class="col-sm-2">'+
                        '<span class="btn btn-danger btn-block" name="plus-img"   onclick="removeDiv(this);"><i class="glyphicon glyphicon-remove"></i></span>'+
                        '</div></div>';

            $('div[name=file-img]').append(html);
        })

        function removeDiv(dome){
            $(dome).parent().parent().remove();
        };

    </script>
@stop
