@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li class="{{ $iReceiverType == 1 ? 'active' : '' }}"><a href="{{ route('msg-messages.create', 1) }}">指定用户</a></li>
      <li class="{{ $iReceiverType == 2 ? 'active' : '' }}"><a href="{{ route('msg-messages.create', 2) }}">发给代理</a></li>
      <li class="{{ $iReceiverType == 3 ? 'active' : '' }}"><a href="{{ route('msg-messages.create', 3) }}">发给所有人</a></li>
    </ul>
    <div class="tab-content" style="margin-top: 20px;">
    <form class="form-horizontal" method="post" action="{{ route($resource.'.create', isset($iReceiverType) ? $iReceiverType : 1) }}" autocomplete="off">
        <!-- CSRF Token -->
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

            @include('message.detailForm')

        <!-- Form actions -->
        <div class="form-group">
            <div class="col-sm-offset-3 col-sm-9">
                <button type="reset" class="btn btn-default">{{ __('Reset') }}</button>
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
    </form>
    </div>
@stop
@section('end')
    {{ script('bootstrap-switch') }}
    <script type="text/javascript">
    function showReceiverList (source) {
        if (! source.files) {
            alert("请使用chrome或firefox最新版本的浏览器");
            return false;
        }
        var file = source.files[0];
        // alert(file.type);
        if (!/text\/\w+/.test(file.type)){
            alert("请确保文件为txt类型");
            return false;
        }
        if (file.size / 1024 > 200) {
            alert("文件大小不能超过200k");
            return false;
        }
        if (window.FileReader) {
            var fr = new FileReader();
            fr.onloadend = function (e) {
                if (!e.target.result) {
                    alert('读取名单失败，请尝试手动填写！');
                } else {
                    $('#receiver').val(e.target.result);
                }
            }
            fr.readAsText(file);
        } else {

        }
    }
    </script>
    @parent

@stop
