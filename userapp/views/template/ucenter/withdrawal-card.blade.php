@extends('l.home')

@section('title')
            提现
@parent
@stop



@section ('main')
<div class="nav-bg">
    <div class="title-normal">
        提现
    </div>
</div>

<div class="content">
    <div class="prompt" style="background-position:170px center;padding:60px 0;padding-left:200px;">
        您还没有绑定银行卡， <a href="#" class="btn">立即绑定</a>
    </div>
</div>
@stop