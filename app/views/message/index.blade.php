@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')

    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('_msguser.status') }}</th>
                    <th>{{ __('_msguser.msg_title') }} {{ order_by('msg_title') }}</th>
                    <th>{{ __('_msguser.type_id') }} {{ order_by('type_id') }}</th>
                    <th>{{ __('_msguser.sender') }} {{ order_by('sender') }}</th>
                    <th>{{ __('_msguser.receiver') }} {{ order_by('receiver') }}</th>
                    <th>{{ __('_msguser.created_at') }} {{ order_by('created_at') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td>{{ $aDeletedStatus[(int)!!$data->deleted_at] }}</td>
                    <td>{{ '[' . $aReadedStatus[(int)!!$data->readed_at] . ']' }} <a href="{{ route('msg-users.view', $data->id) }}">{{ $data->msg_title }}</a></td>
                    <td>{{ $aMsgTypes[$data->type_id] }}</td>
                    <td>{{ $data->sender }}</td>
                    <td>{{ $data->receiver }}</td>
                    <td>{{ $data->created_at }}</td>
                    <td>
                        @include('w.item_link')
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>

<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
    'footer'  =>
        Form::open(array('id' => 'real-delete', 'method' => 'delete')).'
            <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-sm btn-danger">确认删除</button>'.
        Form::close(),
);
?>
    @include('w.modal', $modalData)

@stop


@section('end')
@parent
<script>
   @if ($bNeedCalendar)
        $('.form_date').datetimepicker({
          language:  'zh-CN',
          weekStart: 1,
          todayBtn:  1,
          autoclose: 1,
          todayHighlight: 1,
          startView: 2,
          minView: 2,
          forceParse: 0,
          showMeridian: 1,
          pickerPosition: 'bottom-left'
        });
    @endif
    function modal(href)
    {
        $('#real-delete').attr('action', href);
        $('#myModal').modal();
    }
    </script>
@stop

@section('javascripts')
@parent
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@stop

