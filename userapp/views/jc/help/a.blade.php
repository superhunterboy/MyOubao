@extends('l.base-v4')

@section('title')
竞彩新手指南
@parent
@stop

@section('styles')
@parent
<style type="text/css">
* {
    margin: 0;
    padding: 0;
}
.p {
    width: 1080px;
    margin: 0 auto;
}
.c-1 {background:#373737;}
.c-2 {background: #F0F0F0;}
.c-3 {background: #373737;}
.c-4 {background: #91CA25;}
.c-5 {background: #91CA25;}

.p-1 {height:80px;background: url(/assets/images/sports/help/a/1.jpg) no-repeat;}
.p-2 {height:382px;background: url(/assets/images/sports/help/a/2.jpg) no-repeat;}
.p-3 {height:80px;background: url(/assets/images/sports/help/a/3.jpg) no-repeat;}
.p-4 {height:781px;background: url(/assets/images/sports/help/a/4.jpg) no-repeat;}
.p-5 {height:254px;background: url(/assets/images/sports/help/a/5.jpg) no-repeat;}

.blank {
    height: 100px;
    background: #91CA25;
}



.p-4 {
    position: relative;
}
.p-4 .cont  {
    position: absolute;
    width: 290px;
    height: 100px;
    right: 20px;
    top: 70px;
}
.p-4 .tobet {
    position: absolute;
    width: 190px;
    height: 50px;
    left: 467px;
    top: 715px;
}
.p-4 .tobet:hover {
    background: rgba(255, 255, 255, 0.15);
}


.hanlder {
    position: absolute;
    width: 174px;
    left: 584px;
    top: 70px;
}
.hanlder a {
    float: left;
    width: 57px;
    height: 36px;
    margin-right: 1px;
    margin-top: 1px;
    text-align: center;
    color: #FFF;
    line-height: 36px;
}
.hanlder a:hover,
.hanlder a.active {
    background: #2fab1e;
}


.tb-cont {
    position: absolute;
    left: 770px;
    top: 70px;
}
.tb {
    border-collapse: separate;
    border-left: 1px solid #FFF;;
    border-bottom: 1px solid #FFF;;
}
.tb td {
    border: 1px solid #FFF;
    padding: 10px 5px;
    border-left: 0;
    border-bottom: 0;
    text-align: center;
    font-size: 14px;
    background: #B7DC71;
    color: #333;
}
.tb td.active {
    cursor: pointer;
}
.tb td.no-bg {
    cursor: default;
    background: none;
}
.tb td.highlight {
    background: #26AD04;
}
.tb td .text {
    display: none;
}
.tb td.active .t {
    display: none;
}
.tb td.active .text {
    display: block;
}

.c-red {
    color: #F00;
}



</style>
@stop

@section('scripts')
    {{ script('base-all') }}
@stop


@section('container')

@include('w.header')

<div class="c-1">
    <div class="p p-1"></div>
</div>

<div class="c-2">
    <div class="p p-2"></div>
</div>

<div class="c-3">
    <div class="p p-3"></div>
</div>

<div class="c-4">
    <div class="p p-4">

        <div class="hanlder">
            <a data-no="1-1" data-index="1" href="#" class="active">2.45</a>
            <a data-no="1-2" data-index="2" href="#">3.10</a>
            <a data-no="1-3" data-index="3" href="#">2.55</a>
            <a data-no="2-1" data-index="1" href="#">5.60</a>
            <a data-no="2-2" data-index="2" href="#">4.15</a>
            <a data-no="2-3" data-index="3" href="#">1.41</a>
            <a data-no="1-1" data-index="1" href="#">1.66</a>
            <a data-no="1-2" data-index="2" href="#">3.90</a>
            <a data-no="1-3" data-index="3" href="#">3.70</a>
            <a data-no="2-1" data-index="1" href="#">3.15</a>
            <a data-no="2-2" data-index="2" href="#">3.40</a>
            <a data-no="2-3" data-index="3" href="#">1.95</a>
        </div>

        <div class="tb-cont">
            <table class="tb" id="J-table">
                <tr>
                    <td class="no-bg"></td>
                    <td class="no-bg">胜</td>
                    <td class="no-bg">平</td>
                    <td class="no-bg">负</td>
                </tr>
                <tr>
                    <td class="cell-1 no-bg">让球0</td>
                    <td class="cell-2 it highlight" data-cell="1">
                        <div class="t">主队胜2.45</div>
                        <div class="text show-1-1" >
                            主队胜<br />
                            如：<span class="c-red">1:0，2:0，2:1</span>等比赛结果为主队胜
                            <span class="c-red">2.45</span>与<span class="c-red">1.66</span>为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                    <td class="cell-3 it" data-cell="2">
                        <div class="t">主队平3.10</div>
                        <div class="text show-1-2">
主队平<br />
如：<span class="c-red">0:0，1:1，2:2</span>等赛果为主队平
3.10与3.90为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                    <td class="cell-4 it" data-cell="3">
                        <div class="t">主队负2.55</div>
                        <div class="text show-1-3">
主队负<br />
如：<span class="c-red">0:1，0:2，1:2</span>等赛果为主队负
2.55与3.70为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="cell-1 no-bg">让球<br />-1</td>
                    <td class="cell-2 it" data-cell="1">
                        <div class="t">主队让客队主队胜5.60</div>
                        <div class="text show-2-1">
                            主队让客队1球后主队胜<br />
                            如：<span class="c-red">2:0，3:0，3:1</span>等比赛结果为主队胜
                            <span class="c-red">5.60</span>与<span class="c-red">3.15</span>为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                    <td class="cell-3 it" data-cell="2">
                        <div class="t">主队让客队主队平4.15</div>
                        <div class="text show-2-2">
主队让客队1球后主队平<br />
如：<span class="c-red">1:0，2:1，3:2</span>等赛果为主队平
4.15与3.40为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                    <td class="cell-4 it" data-cell="3">
                        <div class="t">主队让客队主队负1.41</div>
                        <div class="text show-2-3">
主队让客队1球后主队负<br />
如：<span class="c-red">1:1，0:1，0:2</span>等赛果为主队负
1.41与1.95为该选项的赔率值，中奖后会按该选项出票时的赔率值进行赔付。
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="no-bg">
                        备注：“-”表示主队让客队，“+”表示客队让主队
                    </td>
                </tr>
            </table>
        </div>


        <a class="tobet" href="/jc/football"></a>

    </div>
</div>
<div class="c-5">
    <div class="p p-5"></div>
</div>


<div class="blank"></div>




@include('w.footer')
@stop

@section('end')

<script type="text/javascript">
(function($){
    var table = $('#J-table');
    var setCurr = function(index){
        table.find('td').removeClass('active');
        table.find('[data-cell="'+index+'"]').addClass('active');
    };
    var highlight = function(el){
        var hlcls = 'highlight';
        table.find('.'+hlcls).removeClass(hlcls);
        $(el).addClass(hlcls);
    };
    table.on('mouseover', '.it', function(){
        var el = $(this),
            index = el.attr('data-cell');
        if(!!index){
            setCurr(index);
        }
        highlight(this);
    });
    setCurr(1);


    $('.hanlder').on('mouseover', 'a', function(e){
        e.preventDefault();
        var el = $(this),
            index = el.attr('data-index'),
            no = el.attr('data-no');
        $('.hanlder a').removeClass('active');
        el.addClass('active');
        setCurr(index);
        highlight(table.find('.show-'+no).parent());
    });



})(jQuery);
</script>

@stop



