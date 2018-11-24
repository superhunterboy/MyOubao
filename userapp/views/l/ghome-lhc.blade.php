@extends('l.base')


@section ('styles')
@parent
{{ style('animate') }}
{{ style('lhc')}}
@stop

@section ('container')
@include('w.header')




<div class="r-main">
    <div class="obscured" id="obscured"></div>
    <div class="kuanian" id="kuanian"></div>
    <div class="r-body">
        <div class="betsvoice" id="betsvoice">
            <span class="open"></span>
            <h5>音效</h5>

        </div>
        <div class="body-1">
            <div class="logo-lottery">
                <img class="animated" src="">
            </div>
            <div class="deadline">
                <div class="deadline-text">第<strong id="J-header-currentNumber">---</strong>期<br>投注截止</div>
                <div class="deadline-number" id="J-deadline-panel">

                </div>
            </div>
            <div class="lottery">
                <div class="ry-1">
                    第<span>等待中...</span>期
                </div>
                <div class="ry-2">
                    <ul class="num" id="lottery">


                    </ul>
                    <ul class="jia">
                        <li>+</li>
                        <li>+</li>
                        <li>+</li>
                        <li>+</li>
                        <li>+</li>
                        <li>+</li>
                        <li>=</li>
                    </ul>
                    <ul class="text">


                    </ul>
                </div>

            </div>
            <ul class="r">
                <li><a target="_blank" href="/help/13#608">玩法说明</a> </li>
                <li><a target="_blank" id="zoushitu" href="/user-trends/trend-view/60">走势图</a></li>
            </ul>
        </div>
        <div class="body-2">
            <ul class="title" id="menu-title">

            </ul>
        </div>
        <div class="body-3">
            <div class="r-game animated">

            </div>
            <div class="dy-3-2">
                <ul class="a nav">
                    <li class="yx">游戏</li>
                    <li class="wf">玩法</li>
                    <li class="qh">期号</li>
                    <li class="kj">开奖号</li>
                    <li class="nr">投注内容</li>
                    <li class="je">投注金额</li>
                    <li class="jj">奖金</li>
                    <li class="fd">返点</li>
                    <li class="zt">状态</li>
                    <li class="cz">操作</li>
                </ul>
                <div class="b" id='history'>



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



