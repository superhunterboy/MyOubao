@extends('l.client.base')

@section('title')
博狼彩票
@stop


@section ('styles')
@parent
{{ style('indexClient')}}
{{ style('animate')}}
@stop

@section ('container')

@include('w.client.full-header')

<div class="r-main">
    <div class="mid">
        <div class="a">
            <div class="a1">
                <h3><i></i>热门彩种</h3>
                <div class="a1-1">
                    <ul class="a1-1-1">
                        <li>
                            <i class="l-a"></i>
                            <a class="r" href="{{ route('jc.match_list', ['football']) }}">
                                <h4>竞彩足球</h4>
                                <h5>赢长串，奖金高</h5>
                            </a>
                        </li>
                        <li>
                            <i class="l-b"></i>
                            <a class="r" href="{{ route('bets.bet', 1) }}">
                                <h4>重庆时时彩</h4>
                                <h5>好玩刺激派奖快</h5>
                            </a>
                        </li>
                        <li>
                            <i class="l-c"></i>
                            <a class="r" href="{{ route('bets.bet', 8) }}">
                                <h4>江西11选5</h4>
                                <h5>10分钟一期</h5>
                            </a>
                        </li>
                        <li>
                            <i class="l-d"></i>
                            <a class="r" href="{{ route('bets.bet', 13) }}">
                                <h4>福彩3D</h4>
                                <h5>天天开奖</h5>
                            </a>
                        </li>
                        <li>
                            <i class="l-e"></i>
                            <a class="r" href="{{ route('bets.bet', 21) }}">
                                <h4>江苏快3</h4>
                                <h5>最简单易中彩票</h5>
                            </a>
                        </li>
                    </ul>
                    <div class="a1-1-2">
                        <a href="{{ route('bets.bet', 3) }}">黑龙江时时彩</a>
                        <a href="{{ route('bets.bet', 6) }}">新疆时时彩</a>
                        <a href="{{ route('bets.bet', 7) }}">天津时时彩</a>
                        <a href="{{ route('bets.bet', 2) }}">山东11选5</a>
                        <a href="{{ route('bets.bet', 14) }}">排列三/五</a>
                        <a href="{{ route('bets.bet', 9) }}">广东11选5</a>
                        <a href="{{ route('bets.bet', 22) }}">安徽快3</a>
                        <a href="{{ route('bets.bet', 53) }}">北京PK10</a>
                        <a href="{{ route('bets.bets', 20) }}">幸运28</a>
                    </div>

                </div>
                <!--<div class="more">-->
                    <!--<h5>更多>></h5>-->
                    <!--<div class="more-cont">-->
                        <!--<a href="{{ route('bets.bets', 20) }}">幸运28</a>-->

                    <!--</div>-->
                <!--</div>-->
            </div>
            <div class="a2">
                <div class="a2-1">

                    @include("adTemp.31")
                </div>
                <div class="a2-2">
                    <div class="a2-2-1">
                        <h5>
                            <i></i>
                            热销彩种
                        </h5>
                        <ul id="rxcz">
                            <li class="zq active">竞彩足球</li>
                            <li class="cq">重庆时时彩</li>
                            <li class="fc">福彩3D</li>
                        </ul>
                    </div>
                    <div class="a2-2-2">
                        <div class="a2-main">
                            <div class="football">
                                @foreach($aRecommendMatches as $k=>$v)
                                <ul class="u1">
                                    <li class="l">
                                        <h1>{{$v['league']->short_name}}</h1>
                                        <h2>{{date('m-d H:i',strtotime($v['match_time']))}}</h2>
                                        <h3>{{$v['home_team']->short_name}}vs{{$v['away_team']->short_name}}</h3>
                                    </li>
                                    <li class="r"><a href="{{ route('jc.match_list', ['football']) }}">更多比赛</a> </li>
                                </ul>
                                <div class="u2">
                                    <a class="u2-1" href="{{ route('jc.match_list', ['football']) }}">
                                        <img src="{{{ $v['home_team']->icon_url }}}" />
                                        <h5>{{$v['home_team']->short_name}} {{$v['odds'][2]['status']}}</h5>
                                        <span>{{$v['odds'][2]['odd']}}</span>
                                        <!--<div class="gou"></div>-->
                                    </a>
                                    <a class="u2-2" href="{{ route('jc.match_list', ['football']) }}">
                                        <h5>{{$v['odds'][1]['status']}}局</h5>
                                        <span>{{$v['odds'][1]['odd']}}</span>
                                    </a>
                                    <a class="u2-3" href="{{ route('jc.match_list', ['football']) }}">
                                        <img src="{{{ $v['away_team']->icon_url }}}" />
                                        <h5>{{$v['away_team']->short_name}} 胜{{--{{$v['odds'][0]['status']}}--}}</h5>
                                        <span>{{$v['odds'][0]['odd']}}</span>
                                    </a>
                                </div>
                                @endforeach
                                <ul class="u3">
                                    <li class="l">
                                        当前
                                        <span>{{$totalMatch}}</span>
                                        场比赛可投注
                                    </li>
                                    <li class="r">
                                        <a href="{{ route('jc.match_list', ['football']) }}"> 立即投注</a>
                                    </li>
                                </ul>
                            </div>
                            @if ($oCqssc)
                            <div class="ssc">
                                <ul>
                                    <li class="l">第<span>{{$oCqssc->issue}}</span>期截止：{{date('m-d H:i', $oCqssc->end_time)}}</li>
                                    <li class="r"><a href="{{route('user-trends.trend-view', '1')}}">走势图</a> </li>
                                </ul>
                                <div class="hm">
                                    <i class="ssc-ico"></i>
                                    <!--<ul class="zoomIn animated">-->
                                    <ul class="">
                                    </ul>
                                </div>
                                <div class="foot">
                                    <h5>好玩刺激派奖快</h5>
                                    <div class="r">

                                        <div class="r-1 ssc-change">
                                            <i></i>
                                            <input type="button" value="换一注" data-num="5"  id="ssc-change"/>
                                        </div>
                                        <a href="{{ route('bets.bet', 1) }}">立即投注</a>

                                    </div>
                                </div>
                            </div>
                            @endif
                            @if ($oFd)
                            <div class="d-3">
                                <ul>
                                    <li class="l">第<span>{{$oFd->issue}}</span>期截止：{{date('m-d H:i', $oFd->end_time)}}</li>
                                    <li class="r"><a href="{{route('user-trends.trend-view', '13')}}">走势图</a> </li>
                                </ul>
                                <div class="hm">
                                    <i class="d3-ico"></i>
                                    <ul class="">
                                    </ul>
                                </div>
                                <div class="foot">
                                    <h5>每注<span>2</span>元 天天开奖</h5>
                                    <div class="r">

                                        <div class="r-1 ssc-change">
                                            <i></i>
                                            <input type="button" value="换一注"  data-num="3"  id="d3-change"/>
                                        </div>
                                        <a href="{{ route('bets.bet', 13) }}">立即投注</a>

                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>

                </div>
            </div>
            <div class="a3">
                @if(empty(Session::get('username')))
                <ul class="a3-1">
                    <li class="login">
                        <a href="/auth/signin">登录</a>
                    </li>
                    <li class="reg"><a href="/auth/signup">免费注册</a></li>
                </ul>
                @else
                <ul class="a3-1-suc">
                    <li class="l">
                        <i></i>
                        <span></span>
                        <h4>
                            欢迎您，<h5>{{{Session::get('username')}}}</h5>
                        </h4>
                    </li>
                    <li class="r"><a href="/auth/logout">退出</a></li>
                </ul>
                @endif
                <ul class="a3-2">
                    <li class="title">
                        <i></i>
                        <h5>网站公告</h5>
                        <a href="/announcements">更多</a>
                    </li>
                    @foreach($oCmsArticle as $k=>$v)
                    <li class="cont">
                        <a title="{{$v->title}}" href="{{ route('announcements.view', $v->id) }}">{{$v->title}}</a>
                        <span>{{date('m-d',strtotime($v->created_at))}}</span>
                    </li>
                    @endforeach

                </ul>
                <div class="a3-2-1">
                    <h4>本站累计中奖</h4>
                    <div class="ljzj">
                        <?php
                        $sLeftNum = sprintf('%02d', intval($iHistoryPrize / 10000));
                        $sRightNum = sprintf('%04d', intval($iHistoryPrize % 10000));
                        ?>
                        <ul>
                            @for($i=0;$i<strlen($sLeftNum);$i++)
                            <li>{{{ $sLeftNum[$i] }}}</li>
                            @endfor
                        </ul>
                        <h5>亿</h5>
                        <ul>
                            @for($i=0;$i<strlen($sRightNum);$i++)
                            <li>{{{ $sRightNum[$i] }}}</li>
                            @endfor
                        </ul>
                        <h5>万</h5>
                    </div>
                </div>
                <div class="a3-3"></div>
            </div>
        </div>
        <div class="b">
            @include("adTemp.30")
        </div>

        <div class="c">
            <div class="c1">
                <h1>开奖公告</h1>
                @foreach($aIssue as $k=>$v)
                <div class="c1-1">
                    <h5>{{$v['name']}}
                        <span>{{$v['number']}}</span>
                        期
                    </h5>
                    <ul>
                        @foreach($v['code'] as $code)
                        <li>{{$code}}</li>
                        @endforeach
                    </ul>
                    <a href="{{route('user-trends.trend-view',$v['id'])}}">走势</a>
                    <i></i>
                    <a href="{{ route('bets.bet', $v['id']) }}">我要投注</a>
                </div>
                @endforeach

            </div>
            <div class="c2">
                <div class="c2-1">
                    <h1>彩票资讯</h1>
                    <div class="l">
                        <span>
                            <h4>竞技彩</h4>
                            @if(count($oJcTopLottery))
                            <a class="l-top" target="_blank" href="{{route('lotteryinformation.view', $oJcTopLottery[0]->id)}}">{{$oJcTopLottery[0]->title}}</a>
                            @endif
                        </span>
                        <div class="l2">
                            @if(count($oJcLottery))
                            @foreach($oJcLottery as $k=>$data)
                            <a target="_blank" href="{{route('lotteryinformation.view', $data->id)}}">{{$data->title}}</a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="r">
                        <span>
                             <h4>数字彩</h4>
                            @if(count($oNumberTopLottery))
                             <a class="l-top" target="_blank" href="{{route('lotteryinformation.view', $oNumberTopLottery[0]->id)}}">{{$oNumberTopLottery[0]->title}}</a>
                            @endif
                        </span>
                        <div class="l2">
                            @if(count($oNumberLottery))
                            @foreach($oNumberLottery as $k=>$data)
                            <a target="_blank" href="{{route('lotteryinformation.view', $data->id)}}">{{$data->title}}</a>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
                <div class="c2-2">
                    <h4>合买召集</h4>
                    <div class="c2-2-1">
                        <ul class="title">
                            <li class="e1">发起人</li>
                            <li class="i"></li>
                            <li class="e2">战绩</li>
                            <li class="i"></li>
                            <li class="e3">方案进度</li>
                            <li class="i"></li>
                            <li class="e4">方案金额</li>
                            <li class="i"></li>
                            <li class="e5">剩余金额</li>
                            <li class="i"></li>
                            <li class="e6">截止时间</li>
                            <li class="i"></li>
                            <li class="e7">参与</li>
                        </ul>


                        @foreach($aRecommendGroupBuys as $k=>$oGroupBuy)
                        <ul class="cont">
                            <li class="e1">{{ $oGroupBuy->display_nickname }}</li>
                            <li class="e2">
                                @include('jc.groupbuy.star', ['oUserGrowth' => $oGroupBuy->user_extra])

                            </li>
                            <li class="e3"> {{ $oGroupBuy->buy_percent }}</li>
                            <li class="e4">{{ number_format($oGroupBuy->amount, 2) }}</li>
                            <li class="e5">{{ number_format($oGroupBuy->amount - $oGroupBuy->buy_amount, 2) }}</li>
                            <li class="e6">{{ date('m-d H:i' ,strtotime($oGroupBuy->end_time)) }}</li>
                            <li class="e7"><a class="link" href="{{ route('jc.follow', $oGroupBuy->id) }}">认购</a></li>
                        </ul>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="c3">
                <div class="c3-1">
                    <ul class="c3-1-1">
                        <li class="l">昨日赛果</li>
                        <li class="r">
                            <a href="{{{ route('jc.result', 'football') }}}">更多...</a></li>
                    </ul>
                    <ul class="c3-1-2">
                        @foreach($oLastMatchInfo as $k=>$v)
                        <li>
                            <div class="z1">
                                <img src="{{$aTeamList[$v->home_id]->icon_url}}"/>
                                <h5>{{$aTeamList[$v->home_id]->short_name}}</h5>
                            </div>
                            <div class="z2">
                                <h3>{{date('m-d H:i', strtotime($v->match_time))}}</h3>
                                <h4>{{$v->score}}</h4>
                            </div>
                            <div class="z3">
                                <img src="{{$aTeamList[$v->away_id]->icon_url}}"/>
                                <h5>{{$aTeamList[$v->away_id]->short_name}}</h5>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="c3-2">
                    <a class="c3-2-1" href="/mobile"></a>
                    <a href="/mobile"></a>
                </div>
            </div>
        </div>
        <div class="ad">
            <a class="cc" href="http://digi.163.com/14/1112/14/AARVH2NS001618JV.html" target="_blank"></a>
            <a class="cc" href="http://tech.hexun.com/2014-11-12/170317217.html" target="_blank"></a>
            <a class="cc" href="http://tech.91.com/content/141112/21761196.html" target="_blank"></a>
            <a class="cc" href="http://xin.52pk.com/list/201411/6223258.shtml" target="_blank"></a>
            <a class="cc" href="http://news.uuu9.com/2014/201411/352218.shtml" target="_blank"></a>
            <a class="cc" href="http://it.msn.com.cn/563449/306337724856b.shtml" target="_blank"></a>
            <a class="cc" href="http://xin.52pk.com/list/201411/6223258.shtml" target="_blank"></a>
            <a class="cc" href="http://tech.xinmin.cn/internet/2014/11/05/25855465.html" target="_blank"></a>
            <a class="cc" href="http://www.diankeji.com/net/15556.html" target="_blank"></a>
            <a class="cc" href="http://game.huanqiu.com/news/2014-11/5191589.html" target="_blank"></a>
            <a class="cc" href="http://www.pcpop.com/view/1/1052/1052817.shtml?r=12141416" target="_blank"></a>
            <a class="cc" href="http://digital.ynet.com/465522/714460635756b.shtml" target="_blank"></a>
            <a class="cc" href="http://sh.beareyes.com.cn/2/lib/201411/12/20141112207.htm" target="_blank"></a>
            <a class="cc" href="http://news.csdn.net/article.html?arcid=15820772&preview=1" target="_blank"></a>
            <a class="cc" href="http://www.40407.com/news/201411/468703.html" target="_blank"></a>
            <a class="cc" href="http://roll.sohu.com/20141105/n405791537.shtml" target="_blank"></a>
            <a class="cc" href="http://news.766.com/dl/2014-11-05/2396330.shtml" target="_blank"></a>
            <a class="cc" href="http://game.china.com/mobile/hardware/11106781/20141105/18929995.html" target="_blank"></a>
            <a class="cc" href="http://game.21cn.com/online/c/a/2014/1105/10/28502546.shtml" target="_blank"></a>
            <a class="cc" href="http://news.duowan.com/1411/279115466876.html" target="_blank"></a>
        </div>
    </div>
</div>
@include('w.client.footer')



{{--
@if ($bFirstLogin)
<script type="text/javascript">
    (function(){
        var popWindowNew = new bomao.Message();
        var data = {
            title          : '提示',
            content        : "<i class=\"ico-waring\"></i><p class=\"pop-text\">{{ __('_basic.first-login-tip') }}</p>",
            isShowMask     : true,
            closeIsShow    : true,
            closeButtonText: '关闭',
            closeFun       : function() {
                this.hide();
            }
        };
        popWindowNew.show(data);
    })();
</script>
@endif
--}}
<script>

    //            $(function(){
    //                SyntaxHighlighter.all();
    //            });
    $(window).load(function(){

        $('.a2-1 .slider-pic:eq(0)').addClass('slides');

        $('.a2-1').flexslider({
            animation: "fade",
            slideshow: true,
            animationLoop: true,
            slideshowSpeed: 3000,
            pauseOnHover: true
        });

        var oA={
            hotCz:function () {
                var _this = this;


                $('#rxcz li').on('mouseover',function () {
                    var _a = ['20px','-671px','-1361px'],
                            _b = $(this).index();
                    $(this).siblings('li').removeClass('active');
                    $(this).addClass('active');
                    $('.a2-main').animate({
                        'left':_a[_b]
                    },300,'easeOutBounce')

                });


                var _ssc = _this.creatLi('#ssc-change');
                var _d3 = _this.creatLi('#d3-change');
                $('#ssc-change').parent().parent().parent().siblings('.hm').find('ul').empty().append(_ssc);
                $('#d3-change').parent().parent().parent().siblings('.hm').find('ul').empty().append(_d3);

                $('.ssc-change input').on('click',function () {
                    var _input = $(this),
                            _ls = _this.creatLi(_input),

                            _parent=_input.parent().parent().parent().siblings('.hm').find('ul');


                    _input.attr('disabled','disabled').val('祝您好运');
                    _parent.empty().append(_ls).find('li').addClass('rotate');

                    _this.num_rotate(_parent,_input);



                });

                $('.mid .a .a2 .a2-1').hover(function () {
                    $(this).find('.pre').fadeIn(300);
                    $(this).find('.next').fadeIn(300);
                },function () {

                    $(this).find('.pre').fadeOut(300);
                    $(this).find('.next').fadeOut(300);
                })
            },
            changeNum:function (a) {      //生成a位数,数组；
                var _a = '';
                for(var i = 0; i < Number(a); i++){
                    _a += Math.floor(Math.random() * 10);
                };
                _a=_a.split('');
                return _a;
            },
            creatLi:function (a) {   //a,class类名  生成LI
                var _this = this,
                        _a = $(a).attr('data-num'),  //获取位数
                        _b = _this.changeNum(_a),   //生成数组
                        _c='';
                for(var i=0;i<_b.length;i++){
                    _c += '<li>'+_b[i]+'</li>'
                }

                return _c;

            },
            num_rotate:function (a,b) { //_a父类class,b当前点击的按钮
                var _a, // 定时器
                        _num = 0;

                _a=setInterval(function () {

                    var _length = a.find('li').length;


                    a.find('li:eq('+_num+')').removeClass('rotate');

                    _num +=1;

                    if(_num>=_length){
                        clearInterval(_a);
                        b.removeAttr('disabled').val('换一注');
                    }
                },500);
            }
        };
        oA.hotCz();

    });


</script>

@stop
















