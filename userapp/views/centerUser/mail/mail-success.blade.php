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
        <div class="alert alert-success">
            <i></i>
            <div class="txt">
                <h4>恭喜您，邮箱验证成功。</h4>
                <div><a class="btn btn-small" href="#">返回首页</a></div>
            </div>
        </div>
    </div>
@stop