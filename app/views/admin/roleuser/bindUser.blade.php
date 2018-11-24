@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Create') . $resourceName }}
@stop



@section('container')
@include('w.breadcrumb')
@include('w.notification')
@include('w._function_title')
<form method = "POST" action = "{{route('role-users.create')}}" accept-charset = "UTF-8" class = "form-horizontal">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input name='step' value='step2' type='hidden'/>
    <input name='role_id' value='{{$role_id}}' type='hidden'/>
    <div class = "form-group">
        <label for = "role_name" class = "col-sm-2 control-label">角色</label>
        <div class="col-sm-6 control-label" style="text-align: left;">
            {{$aUserRoles[$role_id]}}
        </div>

    </div>
    <div class="form-group">
        <label for="expire_date" class="col-sm-2 control-label">开始时间</label>
        <div class="col-sm-2">
            <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd">
                <input id="expire_date" class="form-control" size="16" type="text" name="add_date" value="{{@$aSearchFields['created_at'][0]}}" >
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>
        <div class="col-sm-4">
        </div>

    </div>
    <div class="form-group">
        <label for="expire_date" class="col-sm-2 control-label">过期时间</label>
        <div class="col-sm-2">
            <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd">
                <input id="expire_date" class="form-control" size="16" type="text" name="expire_date" value="{{@$aSearchFields['created_at'][0]}}" >
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
        </div>



        <div class="col-sm-4">
        </div>

    </div>
    @if (isset($aUsers))
    <div class = "form-group">
        <div class="col-sm-offset-2 col-sm-6">
            <!-- CSRF Token -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <div class="row">
                @foreach ($aUsers as $key => $val)
                <div class="col-md-3">
                    <input type="checkbox"  @if(in_array($key, $aRoleUsers)) checked@endif name="user_id[]" value="{{ $key }}" />{{$val}}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <div class = "form-group">
        <div class = "col-sm-offset-2 col-sm-6">
            <a class = "btn btn-default" href = "javascript:void();" onclick="history.go(-1)">返回</a>
            <input class = "btn btn-success" type = "submit" value = "创建"> </div>
    </div>
</form>

@stop

@section('javascripts')
@parent
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@stop

@section('end')

@parent
<script type="text/javascript">
    $(function () {
        //时间控件
        $('.form_date').datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            minView: 2,
            forceParse: 0,
            showMeridian: 1,
            pickerPosition: 'bottom-left'
        });
    });
</script>
@stop


