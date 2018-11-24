@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Group Info') }}
@stop


@section('container')

    @include('w.breadcrumb')
    @include('w.notification')


    <div class="row">
        <div class="col-xs-4">
            <div class="h1" style="line-height: 32px;">{{ __('Group Info') }} </div>
        </div>
        <div class="col-xs-8">
            <div class="pull-right">
                <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                    &laquo; {{ __('Return') .  $resourceName }}
                </a>
            </div>
        </div>
    </div>
    <hr>

    <table class="table table-bordered table-striped">
        <tbody>
            <tr>
                <th  class="text-right col-xs-2">{{ __('User Type') }}</th>
                <td>{{ $data->user_type_formatted }}</td>
            </tr>
            <tr>
                <th  class="text-right col-xs-2">{{ __('User Name') }}</th>
                <td>{{ $data->username }}</td>
            </tr>
            <tr>
                <th  class="text-right col-xs-2">{{ __('Nick Name') }}</th>
                <td>{{ $data->nickname }}</td>
            </tr>
            <tr>
                <th  class="text-right col-xs-2">{{ __('Created At') }}</th>
                <td>{{ $data->created_at }}</td>
            </tr>
            <tr>
                <th  class="text-right col-xs-2">{{ __('Signin At') }}</th>
                <td>{{ $data->signin_at }}</td>
            </tr>
            <tr>
                <th  class="text-right col-xs-2">{{ __('Group Account Sum') }}</th>
                <td>{{ number_format($data->group_account_sum, 4) }}</td>
            </tr>
        </tbody>
    </table>

@stop
@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop