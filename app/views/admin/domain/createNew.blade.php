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


        <div class="form-group">
            <label for="domain" class="col-sm-3 control-label">{{ __('_agentdomaingroup.user_id') }}</label>
            <div class="col-sm-5">
                <?php if($oUser){?>
                    <input type="text" class="form-control" id="user_id" name="user_id" value="{{$oUser->id}}" readonly/>
                <?php }else{?>
                <input type="text" class="form-control" id="user_id" name="user_id" value=""/>
                <?php }?>
            </div>
            <div class="col-sm-4">
                {{ $errors->first('domain', '<label class="text-danger control-label">:message</label>') }}
            </div>
        </div>
        <div class="form-group">
            <label for="status" class="col-sm-3 control-label">{{ __('_agentdomaingroup.group_name') }}</label>
            <div class="col-sm-5">
                <select class="form-control" name="group_id" id="group_id" >
                    <option value="" ></option>
                    @foreach ($aDomainGroup as $key => $value)
                        <option value="{{ $key }}" >{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                {{ $errors->first('status', '<label class="text-danger control-label">:message</label>') }}
            </div>
        </div>





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
