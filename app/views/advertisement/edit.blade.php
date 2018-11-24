@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Edit') . $resourceName }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')


    <form class="form-horizontal" method="post" enctype="multipart/form-data" action="{{  route($resource.'.edit', $data->id) }}"  autocomplete="off">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="_method" value="PUT" />
        @include('advertisement.editForm')


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <a class="btn btn-default" href="{{  route($resource.'.edit', $data->id) }}">{{ __('Reset') }}</a>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>

<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
    'footer'  =>
        Form::open(['id' => 'real-delete', 'method' => 'delete']).
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
        };

        var valData =  $('#ad_location_id').attr('adId');
            if( valData == 1 ){
                $('.portrait').hide();
            }
    </script>
@stop
