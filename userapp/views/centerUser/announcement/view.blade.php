@extends('l.home')

@section('title') 
    公告详情
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .page-content-inner {
        box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
        border: 0px solid #CCC;
        background-color: #FFF;
    }
    </style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                <ul class="list">
                    <li><a href="{{ route('announcements.index') }}?category_id={{Input::get('category_id')}}">返回列表</a></li>
                    <li class="active"><span class="top-bg"></span><a href="">公告详情</a></li>
                </ul>
            </div>




            <div class="page-content-inner">
                <br />
                <br />

                <div class="article-page">
                    <div class="article-page-title">
                        <p>{{ $data->title }}</p>
                        <p class="article-page-time">{{ $data->created_at}}</p>
                    </div>
                    <div class="article-page-content">
                        {{ nl2br($data->content) }}
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


