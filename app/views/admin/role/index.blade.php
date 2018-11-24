@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Admin') . $resourceName . __('Management') }}
@stop

@section('container')

    @include('w.breadcrumb')
    @include('w.notification')

    <div class="row">
      <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('Admin') . $resourceName . __('Management') }} </div>
      </div>
      <div class="col-xs-8">
        {{ Form::open(array('method' => 'get', 'class' => 'form-inline pull-right')) }}
            <div class="form-group">
                {{
                    Form::select(
                        'target',
                        array('name' => __('Role Name'), 'description' => __('Description')),
                        Input::get('target', 'name'),
                        array('class' => 'form-control')
                    )
                }}
            </div>
            <div class="form-group">
              <input type="text" class="form-control" name="like" placeholder="请输入搜索条件" value="{{ Input::get('like') }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn  btn-primary"><i class="glyphicon glyphicon-search"></i>{{ __('Search') }}</button>
            </div>
             @foreach ($buttons['pageButtons'] as $element)
            <div class="form-group">
               <a  href="{{ $element->route_name ? route($element->route_name) : 'javascript:void(0);' }}" class="btn btn-info"><i class="glyphicon glyphicon-plus"></i> {{ __($element->label) }}</a>
            </div>
            @endforeach
        {{ Form::close() }}

      </div>
    </div>
    <hr>

    <div class="col-xs-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{{ __('Role Name') }} {{ order_by('name') }}</th>
                    <th>{{ __('Description') }} {{ order_by('description') }}</th>
                    <th>{{ __('Priority') }} {{ order_by('priority') }}</th>
                    <th>{{ __('Is System') }} {{ order_by('is_system') }}</th>
                    <th>{{ __('Right Settable') }} {{ order_by('right_settable') }}</th>
                    <th>{{ __('User Settable') }} {{ order_by('user_settable') }}</th>
                    <th>{{ __('Disabled') }} {{ order_by('disabled') }}</th>
                    <th>{{ __('Sequence') }} {{ order_by('sequence', 'asc') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                <tr>
                    <td>{{ $data->name }}</td>
                    <td>{{ __( $data->description) }}</td>
                    <td>{{ $data->priority }}</td>
                    <td>{{ yes_no($data->is_system) }}</td>
                    <td>{{ yes_no($data->right_settable) }}</td>
                    <td>{{ yes_no($data->user_settable) }}</td>
                    <td>{{ yes_no($data->disabled) }}</td>
                    <td>{{ $data->sequence }}</td>
                    <td>
                        @foreach ($buttons['itemButtons'] as $element)
                            @if ($element->btn_type == 1)
                            <a href="javascript:void(0)" class="btn btn-xs btn-embossed btn-danger"
                             onclick="modal('{{ $element->route_name ? route($element->route_name, $data->id) : 'javascript:void(0);' }}')">{{ __( $element->label) }}</a>
                            @elseif ($element->btn_type == 2)
                            <a href="{{ $element->route_name ? route($element->route_name, $data->id) : 'javascript:void(0);' }}" class="btn btn-xs btn-embossed btn-success">{{ __( $element->label) }}</a>
                            @else
                            <a  href="{{ $element->route_name ? route($element->route_name, str_contains($element->route_name, 'index') ? ['id' => $data->id] : $data->id) : 'javascript:void(0);' }}" class="btn btn-xs btn-embossed btn-default" > {{ __( $element->label) }}</a>
                            @endif
                        @endforeach
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
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
    </script>
@stop

