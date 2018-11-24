@extends('l.home')

@section('title')
个人资料
@parent
@stop


@section ('styles')
@parent
{{ style('proxy-global') }}
{{ style('proxy') }}
<style type="text/css">
    .layout-row {
        float: left;
    }
</style>
<style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }

    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border: 0px solid #CCC;
        border-top: 0;
    }
</style>
@stop


@section ('container')
@include('w.header')
<div class="banner">
    <img src="/assets/images/proxy/banner.jpg" width="100%"/>
</div>
<div class="page-content page-content-password">
    <div class="g_main clearfix">
        @include('w.manage-menu')

        <div class="nav-inner clearfix">
            @include('w.uc-menu-user')
            <div class="confirm-main">
                <div class="safe-confirm">
                    <span>
                        <div style="font-size:14px">以下是您刚刚设置的安全口令问题，请确认问题答案。</div>
                        <div>
                            <span style="color:red;">安全口令设置后不可更改，请谨慎确认</span>
                        </div>
                    </span>
                    <div>
                        <form action="{{route('security-questions.savedata')}}" method="post">
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                            <ul class="r-1">


                                @foreach($data as $k=>$aData)
                                <li>问题：<span>{{$aData['content']}}</span>
                                    <div>
                                        <input type="hidden" name="id[]" value="{{$aData['id']}}"/>
                                        答案：<span><input type="text" name="answer[]" value="{{$aData['answer']}}"
                                                        readonly="readonly"/></span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <a href="javascript:void(0)" class="btn" onclick="javascript:history.go(-1)">上一步</a>
                            <input type="submit" class="btn" value="确认">
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@include('w.footer')
@stop


@section('end')
@parent
@stop
