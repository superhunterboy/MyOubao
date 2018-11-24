@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Edit') . $resourceName }}
@stop


@section('container')

    @include('w.breadcrumb')
    @include('w.notification')


   <div class="row">
      <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('_function.admin reset password') }} </div>
      </div>
      <div class="col-xs-8">
        <div class="pull-right">
            <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                {{__('_function.admin user list') }}
            </a>
        </div>
      </div>
    </div>
    <hr>

    <form class="form-horizontal" method="post" action="{{ route($resource.'.reset-password', $data->id) }}" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="_method" value="PUT" />

        <div class="form-group">
            <label class="col-sm-3 control-label">{{ __('_adminuser.username') }}</label>
            <label class="col-sm-3 control-label">{{ $data->username }}</label>
        </div>
        <div class="form-group">
            <label for="password"  class="col-sm-3 control-label">*{{ __('_adminuser.new-password') }}</label>

            <div class="col-sm-5">
                <input class="form-control" type="password" name="password" id="password" value="" />
            </div>
            <div class="col-sm-4">
                {{ $errors->first('password', '<label class="text-danger control-label">:message</label>') }}
            </div>

        </div>

        <div class="form-group">
            <label for="password_confirmation"  class="col-sm-3 control-label">*{{ __('_adminuser.new-password-confirm') }}</label>

            <div class="col-sm-5">
                <input class="form-control" type="password" name="password_confirmation" id="password_confirmation" value="" />
            </div>
            <!-- <div class="col-sm-4">
                {{ $errors->first('password', '<label class="text-danger">:message</label>') }}
            </div> -->
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-6">
              <button type="reset" class="btn btn-default" >{{ __('Reset') }}</button>
              <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>

@stop
@section('end')
    {{ script('bootstrap-switch') }}
    @parent

@stop