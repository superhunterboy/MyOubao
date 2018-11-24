@extends('l.base-v4')

@section ('styles')
@parent
    {{ style('ucenter') }}
    {{ style('proxy') }}
    {{ style('proxy-global') }}
@stop


@section ('container')
    @include('w.header')
        <div class="page-content">
            <div class="container main clearfix">
                @include('w.manage-menu')
                <div class="main-content">
                    @section ('main')
                    @show
                </div>
            </div>
        </div>
    @include('w.footer')
@stop

@section('end')
@parent

@stop
