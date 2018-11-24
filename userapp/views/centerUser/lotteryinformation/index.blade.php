@extends('l.home')

@section('title') 
    彩票资讯
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
            {{-- 管理中心 侧边菜单栏与菜单层级显示 --}}
            <?php $aUserCenterNav = UserCenterMenu::getNav(); ?>
            <?php $aUserCenterMenu = UserCenterMenu::getMenu(); ?>
            <?php $i=1; $total = count($aUserCenterNav); ?>
            <div class="center-top-menu">
                <span class="menu-logo"></span>
               <span class="cpzx">
                   首页   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&gt;&nbsp;&nbsp;&nbsp;&nbsp;   彩票资讯
               </span>

            </div>

            <div class="center-left-menu">
                @foreach($aUserCenterMenu as $k=> $aMenu)
                <div class="logo-box">
                    <span class="logo-box-side"></span>
                    <span class="logo-img logo-img-{{$i}}"></span>
                    <div class="second-menu">
                        <div class="title">{{$aMenu['title']}}</div>

                        <ul>
                            @if( isset($aMenu['children']) && $aMenu['children'])
                            @foreach($aMenu['children'] as $j=>$c)
                            @if('站内信' == $c['title'])
                            <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}<span class="letter-num">{{$unreadMessagesNum}}</span></a></li>
                            @elseif('高点配额' == $c['title'])
                            @if(Session::get('show_overlimit'))
                            <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                            @endif
                            @elseif('分红报表' == $c['title'])
                            @if(Session::get('is_top_agent') )
                            <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                            @endif
                            @else
                            <li><a href="{{$c['url']}}"><span class="left-menu-item">&gt;&nbsp;&nbsp;</span>{{$c['title']}}</a></li>
                            @endif
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
                <?php $i++; ?>
                @endforeach

            </div>

            <div class="nav-inner clearfix">
                <ul class="list">
                    <li class="active">
                        <span class="top-bg"></span>
                        <a href="">{{$name}}</a>
                    </li>
                </ul>
            </div>



            <div class="page-content-inner">

  @if(count($datas) > 0)
                <table width="100%" class="table">
                    <tbody>
                      
                        @foreach ($datas as $data)
                            <tr>
                                <td>
                                    <div class="text-left" style="padding-left:20px;padding-top:10px;padding-bottom:10px;">
                                        <a style="font-size:14px;" href="{{route('lotteryinformation.view', $data->id)}}">{{ $data->title }}</a>
                                    </div>
                                </td>
                                <td class="text-right">{{ $data->created_at}}</td>
                            </tr>
                        @endforeach
                   
                    </tbody>
                </table>
                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}

     @endif

            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
@stop


