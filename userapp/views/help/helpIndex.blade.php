@extends('l.base')

@section('title')
帮助中心
@parent
@stop

@section ('styles')
@parent
    {{ style('help')}}
@stop

@section('container')
    @include('w.public-header')
 <div class="help-banner"></div>
<style type="text/css">
.global-top{background: rgba(0, 0, 0, 0) none repeat scroll 0 0;}
.global-top .logo{background: rgba(0, 0, 0, 0) url("/fastlogin/images/logo.png") no-repeat scroll 0 0;}
</style>


<div class="help-content">
    <div class="g_33 clearfix">

       @include('w.help-sider')
        <div class="help-main">
            <div class="help-main-inner">
                @if (count($datas))
                @foreach($datas as $data)
                <div class="row">
                    <h2 id="{{$data['id']}}">{{ $data['title'] }}</h2>
                    <div class="row-text">{{ $data['content'] }}</div>
                </div>
               @endforeach
               @endif
            </div>
        </div>

    </div>
</div>
    @include('w.footer')
@stop

@section('end')
<script>
(function($){
    var dom = $('#J-help-sider'),as = dom.find('.ul-first > li > a'),CLS = 'open',CLSA = 'current';
    as.click(function(e){
        var li = $(this).parent();
        if(li.hasClass(CLS)){
            li.removeClass(CLS);
        }else{
            li.addClass(CLS);
        }
        e.preventDefault();
    });
   dom.find('.ul-second > li >a').click(function(e){
        var li = $(this);
        if(li.hasClass(CLSA)){
            li.removeClass(CLSA);
        }else{
            dom.find('.ul-second > li>a').removeClass(CLSA);
            li.addClass(CLSA);
        }
    })
})(jQuery);
</script>
@parent
@stop


