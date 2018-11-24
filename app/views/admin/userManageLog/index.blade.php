@extends('l.admin', ['active' => $resource])

@section('title')
@parent
{{ $sPageTitle }}
@stop
@section('container')

    @include('w.breadcrumb')

    @include('w.notification')

    @include('w._function_title')

    <hr>
    @foreach($aWidgets as $sWidget)
        @include($sWidget)
    @endforeach
    <div class="col-xs-12">
        <form action="{{ route('user-manage-logs.update-comments') }}" name="updateComments" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <!-- <th><input type="checkbox" name="checkAll"></th> -->
                        @foreach( $aColumnForList as $sColumn )
                        <th>{{ __('_usermanagelog.' . strtolower($sColumn)) }} {{ order_by($sColumn) }}</th>
                        @endforeach
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $data)
                    <tr>
                        <!-- <td><input type="checkbox" name="row_{{-- $data->id --}}"></td> -->
                        <td>{{ __('_function.' . strtolower($data->functionality)) }}</td>
                        <td>{{ $data->created_at }}</td>
                        <td>{{ $data->admin }}</td>
                        <td>
                            @if (isset($data->comment) && $data->comment)
                            {{ $data->comment }}
                            @else
                            <textarea name="comment[{{ $data->id }}]" cols="30" rows="2">{{ $data->comment }}</textarea>
                            @endif
                        </td>
                        <td>
                            @include('w.item_link')
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ Form::submit(__('Submit'), ['class' => 'btn btn-success']) }}
        </form>
        {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
    </div>

<?php
$modalData['modal'] = array(
    'id'      => 'myModal',
    'title'   => '系统提示',
    'message' => '确认删除此'.$resourceName.'？',
    'footer'  =>
        Form::open(['id' => 'real-delete', 'method' => 'delete']).'
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
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop

