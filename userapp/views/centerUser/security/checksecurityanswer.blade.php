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
    .layout-row {float: left;}
    </style>
    <style type="text/css">
    .page-content .row {
        padding: 20px 0 10px 0;
        margin: 0;
    }
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border:0px solid #CCC;
        border-top: 0;
    }
    </style>
@stop



@section ('container')
@include('w.header')
<div class="page-content page-content-password">
    <div class="yz-main">
        <div class="safe-yz">
            <span class="title">为了您的资金安全，请验证您的口令</span>
            <form method="post" action="{{route('security-questions.checksecurityanswer')}}">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" class="question" name="question" value="{{$oQuestion->content}}" readonly="readonly"/>
                <ul>

                    <li>问题：{{$oQuestion->content}}</li>
                    <li class="yz-error">答案：<span><input type="text" class="answer" name="answer"></span><span class="r">连续输错超过<span class="r-b">6</span>次系统将强制登出并冻结帐号</span></li>
                    <li><input type="submit" class="btn qd" value="确定" /></li>
                    <li><span id="notice"></span></li>
                </ul>
            </form>
        </div>
    </div>

</div>


@include('w.footer')
@stop



@section('end')
@parent
@stop

