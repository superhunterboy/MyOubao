@extends('l.base-v4')

@section('title')
竞足攻略
@parent
@stop

@section('styles')
@parent
<style type="text/css">
* {
    margin: 0;
    padding: 0;
}
body {
	background: url(/assets/images/sports/help/b/bg.jpg) 0 20px;
}
.p {
    width: 1080px;
    margin: 0 auto;
    height: 800px;
    position: relative;
}


.p-1 {background: url(/assets/images/sports/help/f/1.jpg) no-repeat;}
.p-2 {background: url(/assets/images/sports/help/f/2.jpg) no-repeat;}
.p-3 {background: url(/assets/images/sports/help/f/3.jpg) no-repeat;}
.p-4 {height:284px;background: url(/assets/images/sports/help/f/4.jpg) no-repeat;}

.blank {
    height: 100px;
    background: #91CA25;
}
.p-1 .gobet {
    position: absolute;
    width: 200px;
    height: 54px;
    left: 828px;
    top: 225px;
}







.float {
    position: fixed;
    width: 181px;
    background: url(/assets/images/sports/help/float.png) no-repeat;
    top: 100px;
    right: 0;
    top: 310px;
    padding-top: 91px;
}
.float ul {
    padding: 0 30px;
}
.float a {
    display: block;
    text-align: center;
    padding: 10px 0;
    color: #FFF;
    background: #509c14;
    margin-bottom: 1px;
}
.float a:hover {
    background: #61b121;
}

</style>
@stop

@section('scripts')
    {{ script('base-all') }}
@stop

@section('container')

@include('w.header')


<div class="float" id="J-side-float">
    <ul>
        <li><a href="#" data-top="0">返回顶部</a></li>
        <li><a href="#" data-top="456">选玩法</a></li>
        <li><a href="#" data-top="920">选赛事</a></li>
        <li><a href="#" data-top="1336">选难度</a></li>
        <li><a href="#" data-top="1780">选球队</a></li>
        <li><a href="#" data-top="1924">选过关</a></li>
    </ul>
</div>


<div class="c-1">
    <div class="p p-1">
        <a class="gobet" href="/jc/football"></a>
    </div>
</div>

<div class="c-2">
    <div class="p p-2"></div>
</div>

<div class="c-3">
    <div class="p p-3"></div>
</div>

<div class="c-4">
    <div class="p p-4"></div>
</div>




@include('w.footer')
@stop

@section('end')

<script type="text/javascript">
(function($){
    var panel = $('#J-side-float');
    var header = $('#J-header-container');
    var win = $(window);
    if(header.size() < 1){
        return;
    }
    var offset = header.offset();
    panel.css({
        left: offset.left + header.width() + 20
    });

    panel.on('click', 'a', function(e){
        e.preventDefault();
        var top = Number($(this).attr('data-top'));
        //console.log(win.scrollTop());
        $('html, body').animate({scrollTop:top}, 500, 'easeOutSine');
    });



})(jQuery);
</script>

@stop