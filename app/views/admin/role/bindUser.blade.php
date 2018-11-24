@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Admin User Binding') }}
@stop



@section('container')
    @include('w.breadcrumb')
    @include('w.notification')

    <div class="row">
        <div class="col-xs-3">
            <div class="h2" style="line-height: 32px;">{{ __('Admin User Binding') }} </div>
        </div>
        <div class="col-xs-9">
            <div class="pull-right">
                <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                    &laquo; {{ __('Return') . $resourceName . __('List') }}
                </a>
            </div>
        </div>
    </div>
    <hr>
    {{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;')) }}
        <input type="hidden" name="is_search_form" value="1">
        {{ Form::label('role_id', 'Role Type') }}
        <div class="form-group">
            <select name="role_id" id="role_id" class="form-control">
                <option value="" >All</option>
                @foreach ($aRoles as $key => $sRoleName)
                <option value="{{ $key }}" {{ (isset($role_id) ? $role_id : Input::get('role_id')) == $key ? 'selected' : '' }}>{{ $sRoleName }}</option>
                @endforeach
            </select>
        </div>
        {{ Form::label('is_agent', 'User Type') }}
        <div class="form-group">
            <select name="is_agent" id="is_agent" class="form-control">
                <option value="" >All</option>
                <option value="0" {{ Input::get('is_agent') == '0' }}>Player</option>
                <option value="1" {{ Input::get('is_agent') == 1 }}>Agent</option>
            </select>
        </div>
        {{ Form::label('username', ('User Name')) }}
        <div class="form-group">

            {{ Form::text('username', Input::get('username'), ['class' => 'form-control']) }}
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
    <hr>
    @if (isset($datas))
    <div class="col-xs-12">
        <form name="userBindingForm" method="post" action="{{ route($resource . '.bind-user', isset($role_id) && $role_id ? $role_id : null) }}" autocomplete="off">
            <!-- <div class="clearfix">
                    <a href="" class="btn btn-default">{{ __('Reset') }}</a>
                    <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
            <hr> -->
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="removedFromCheckedUsers" value="" />
            <input type="hidden" name="newUsers" value="" />
            <div class="col-xs-12">
                @foreach ($datas as $data)
                <div class="col-md-3">
                    <label class="checkbox"  for="{{ 'User_' . ($data->id) }}">

                    <input type="checkbox" data-toggle="checkbox" name="user_id[]" id="{{  'User_' . ($data->id) }}" value="{{ $data->id }}" />
                    {{ $data->username }}
                    </label>

                </div>
                @endforeach
            </div>
            {{ pagination($datas->appends(Input::except('page')), 'p.slider-3') }}
            <hr>
            <div class="clearfix">
                <a href="" class="btn btn-default">{{ __('Reset') }}</a>
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
            <div class="clearfix visible-xs"></div>
        </form>
    </div>
    @endif

@stop

@section('end')
{{ script('ui-checkbox') }}
    @parent

    <?php
        // $checkedUsers = json_encode($checked);
        // print("<script language=\"javascript\">var checkedUsers = $checkedUsers;</script>\n");
    ?>
    <script>
        jQuery(document).ready(function($) {
            $(':checkbox').checkbox();
            // var removedFromCheckedUsers = [],
            //     newUsers = [];
            // $(':checkbox').change(function(event) {
            //     var node = $(this).val(), checked = $(this).attr('checked'), isOldChecked = $(this).attr('isOldChecked');
            //     if (+isOldChecked) {
            //         var index = $.inArray(node, removedFromCheckedUsers);
            //         checked ? removedFromCheckedUsers.splice(index, 1) : removedFromCheckedUsers.push(node);
            //     } else {
            //         var index = $.inArray(node, newUsers);
            //         checked ? newUsers.splice(index, 1) : newUsers.push(node);
            //     }
            // });
            // $('form[name=userBindingForm]').submit(function(event) {
            //     $('input[name=removedFromCheckedUsers]').val(removedFromCheckedUsers.join(','));
            //     $('input[name=newUsers]').val(newUsers.join(','));
            //     return true;
            // });
        });
    </script>
@stop