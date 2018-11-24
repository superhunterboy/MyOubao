@extends('l.home')

@section('title')
    邮箱管理
@parent
@stop


@section('main')
    <div class="nav-bg">
        <div class="title-normal">邮箱管理</div>
    </div>

    <div class="content">
        <div class="alert {{ $msg[$state]['class'] }}">
            <i></i>
            <div class="txt">
                <h4>{{ $msg[$state]['msg'] }}</h4>
                <div><a class="btn btn-small" href="{{ $msg[$state]['backUrl'] }}">{{ $msg[$state]['backMsg'] }}</a></div>
            </div>
        </div>

    </div>
@stop