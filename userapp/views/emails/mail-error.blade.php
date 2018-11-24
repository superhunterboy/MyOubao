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
            <div class="alert alert-error">
                <i></i>
                <div class="txt">
                    <h4>验证失败，邮件激活链接已过期。</h4>
                    <div><a class="btn btn-small" href="#">重新绑定</a></div>
                </div>
            </div>
            <div class="alert alert-error">
                <i></i>
                <div class="txt">
                    <h4>您已经激活成功过了，无需重复激活。</h4>
                    <div><a class="btn btn-small" href="#">重新绑定</a></div>
                </div>
            </div>
            <div class="alert alert-error">
                <i></i>
                <div class="txt">
                    <h4>验证失败，请重试。</h4>
                    <div><a class="btn btn-small" href="#">重新绑定</a></div>
                </div>
            </div>
        </div>
@stop