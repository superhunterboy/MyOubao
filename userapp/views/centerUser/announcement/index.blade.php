@extends('l.home')

@section('title') 
    平台公告
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
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
                    @if(Input::get('category_id') == 2 || empty(Input::get('category_id')))
                    <li class="active"><span class="top-bg"></span><a href="{{ route('announcements.index') }}?category_id=2">平台公告</a></li>
                    @elseif(Input::get('category_id') == 11)
                    <li class="active"><span class="top-bg"></span><a href="{{ route('announcements.index') }}?category_id=11">优惠活动</a></li>
                    @endif
                </ul>
            </div>



            <div class="page-content-inner">


                <table width="100%" class="table">
                    <tbody>
                        @foreach ($datas as $data)
                            <tr>
                                <td>
                                    <div class="text-left" style="padding-left:20px;padding-top:10px;padding-bottom:10px;">
                                        <a style="font-size:14px;" href="{{route('announcements.view', $data->id)}}?category_id=<?php echo empty(Input::get('category_id')) ? 2 : Input::get('category_id');?>">{{ $data->title_formatted }}</a>
                                    </div>
                                </td>
                                <td class="text-right">{{ $data->created_at}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}



            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
@stop


