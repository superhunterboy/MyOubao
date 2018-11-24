@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    <form class="form-horizontal" method="post" action="{{ route($resource . '.create') }}" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

        @include('admin.user.topAgentForm')

        <!-- Form actions -->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="reset" class="btn btn-default">{{ __('Reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>

@stop
@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop
