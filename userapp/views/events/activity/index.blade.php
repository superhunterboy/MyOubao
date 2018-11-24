@extends('l.home')

@section('title') 
优惠活动
@parent
@stop


@section ('styles')
@parent
<link media="all" type="text/css" rel="stylesheet" href="/assets/images/events/activity/main.css">
@stop



@section ('container')

@include('w.header')

<div class="page-content">
    <div class="g_main clearfix">

            <div class="product_newin_c">
                <ul>
                    <li>
                        <a href="{{route('activity.dailybet')}}" target="_blank"><img src="/events/activity/images_v2/product/p_img2_07.jpg"></a>
                        <div class="product_newin_c_tit">
                            <a href="{{route('activity.dailybet')}}" target="_blank">查看详情</a>
                            <span>每日投注，奖金嗨翻天</span><br>活动时间　2017-01-01至2018-01-01					</div>
                        </li>
                        <li>
                            <a href="{{route('activity.newcharge')}}" target="_blank"><img src="/events/activity/images_v2/product/p_img3_07.jpg"></a>
                            <div class="product_newin_c_tit">
                            <a href="{{route('activity.newcharge')}}" target="_blank">查看详情</a>
                            <span>新人首充大礼包 </span><br>活动时间　2017-01-01至2018-01-01					</div>
                    </li>
                    <li>
                        <a href="{{route('activity.dailysignin')}}" target="_blank"><img src="/events/activity/images_v2/product/p_img4_07.jpg"></a>
                        <div class="product_newin_c_tit">
                            <a href="{{route('activity.dailysignin')}}" target="_blank">查看详情</a>
                            <span>每日欢乐签到送！</span><br>活动时间　2017-01-01至2018-01-01					</div>
                    </li>
                    <li>
                        <a href="{{route('activity.dailycharge')}}" target="_blank"><img src="/events/activity/images_v2/product/p_img5_07.jpg"></a>
                        <div class="product_newin_c_tit">
                            <a href="{{route('activity.dailycharge')}}" target="_blank">查看详情</a>
                            <span>每日首充有惊喜</span><br>活动时间　2017-01-01至2018-01-01					</div>
                    </li>
                </ul>
            </div>
    </div>
</div>



@include('w.footer')
@stop



@section('end')
@parent
@stop


