@extends('l.home')

@section('title')
    提现申请
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


            <div class="jindu">
                <div class="a">
                    <h4>提款进度</h4>
                    <h5>用户可以在这里查到自己或直属下级的提款进度。</h5>
                </div>


                <div class="b">

                    @foreach($aStepLab as $iStep => $sVal)
                    <div class="b{{$iStep}}">
                        @if(isset($aSteps[$iStep]['status']))
                        <h4>{{$aStepLab[$iStep]}}</h4>
                        <ul>
                            <li>{{{ $aSteps[$iStep]['time'] }}} {{{ $aStatus[$aSteps[$iStep]['status']]}}}</li>
                            @if(isset($aSteps[$iStep]['msg']))
                            <li class="last">{{ $aSteps[$iStep]['msg'] }}</li>
                            @endif
                        </ul>
                        @else
                        <h4 class="txtunactive">{{$aStepLab[$iStep]}}</h4>
                        @if(isset($aSteps[$iStep]['processing']))
                        <ul>
                            <li class="dcl">待处理</li>
                        </ul>
                        @endif
                        @endif
                    </div>
                    @endforeach

                </div>
                <div class="c">

                    <a class="l" href="/user-withdrawals/withdraw">我要提款</a>
                </div>
            </div>


        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){
    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });
//    $('.jindu .b ul').each(function () {
//        if($(this).find('li').hasClass('dcl')){
//            $(this).siblings('h4').addClass('txtunactive');
//            $(this).addClass('bgunactive');
//        }
//    });

})(jQuery);
</script>
@stop


