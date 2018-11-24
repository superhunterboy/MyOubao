@extends('l.home')

@section('title') 
    站内信详情
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
    .table tbody tr:hover td {
        background: #FFF;
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
                    <li><a href="{{ route('station-letters.index') }}">返回列表</a></li>
                    <li class="active"><span class="top-bg"></span><a href="">查看站内信</a></li>
                </ul>
            </div>




            <div class="page-content-inner">

                <table width="100%" class="table">
                    <tr>
                        <td>
                            <div class="article-page">
                                <div class="article-page-title">
                                    <p>{{ $data->msg_title }}</p>
                                    <p class="article-page-time">{{ $data->created_at }}</p>
                                </div>
                                <div class="article-page-content">
                                    {{ $data->msg_content }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>







            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
@stop


