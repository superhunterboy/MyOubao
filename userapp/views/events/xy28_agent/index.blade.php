@extends('l.base-v4')

@section('title')
幸运28闪亮上线-博狼娱乐
@stop

@section ('styles')
@parent
{{ style('font-awesome')}}
{{ style('ucenter') }}
{{ style('proxy') }}
{{ style('lucky28-ad') }}
@stop


@section ('container')
@include('w.header')
<div class="r-main">


    <div class="a">
        <div class="a1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>
    <div class="b">
        <div class="b1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>
    <div class="c">
        <div class="c1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>
    <div class="d">
        <div class="d1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>
    <div class="e">
        <div class="e1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>
    <div class="f"></div>
    <div class="g">
        <div class="g1">
            <a href='/bets/bets/20'></a>
        </div>
    </div>

</div>

@stop

@section('end')

@parent

@stop