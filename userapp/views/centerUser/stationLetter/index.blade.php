@extends('l.home')

@section('title') 
    站内信
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
                @include('w.uc-menu-user')
            </div>



            <div class="page-content-inner">


                <table width="100%" class="table">
                    <tbody>
                        @if(count($datas) > 0)
                            @foreach ($datas as $data)
                                <!-- TODO 判断已读和未读状态, 根据消息记录中的某个字段 -->
                                <tr>
                                    <td>
                                        <div class="text-left" style="padding-left:10px;">
                                            <i class="ico {{ !!$data->readed_at ? 'ico-mail-read' : 'ico-mail' }}"></i>
                                            <a href="{{ route('station-letters.view', $data->id) }}">
                                            @if(!!$data->readed_at)
                                            {{ $data->msg_title }}
                                            @else
                                                <b>{{ $data->msg_title }}</b>
                                            @endif
                                            </a>
                                        </div>
                                    </td>
                                    <td>{{ $aMsgTypes[$data->type_id] }}</td>
                                    <td>{{ $data->created_at }}</td>
                                    <td>
                                        <a class="tb-inner-btn" href="{{ route('station-letters.view', $data->id) }}">阅读</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td>暂无信件</td>
                            </tr>
                        @endif

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


