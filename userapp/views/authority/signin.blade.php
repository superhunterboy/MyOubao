<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"  />
    <meta name="keywords" content="欧豹娱乐,欧豹彩票,欧豹游戏，欧豹娱乐平台">
    <meta name="description" content="欧豹游戏官网提供欧豹娱乐注册,欧豹平台开户,欧豹娱乐平台客户端下载等服务!" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <title>登录 - 欧豹娱乐</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <link media="all" type="text/css" rel="stylesheet" href="/assets/images/global-v4/global.css">
    <link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/bootstrap/bootstrap-v1.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/login-v5/login.css">
    <link media="all" type="text/css" rel="stylesheet" href="/oubao/assets/images/fonts/iconfont.css">
</head>
<style type="text/css">
        .global-footer .container {
		    background: url("/assets/images/global-v4/bg-foot.gif") no-repeat scroll 0 -10px;
		    height: 115px;
		    position: relative;
		    z-index: 1;
		}
		.global-footer .cell {
		    float: left;
		    padding-top: 12px;
		    width: 167px;
		}
		.global-footer .cell-a {
		    padding-left: 67px;
		}
    </style>
    
<body>

<!--登录的内容-->
<div class="j-l-header" id="j-l-header">
    <div class="top-bar clearfix hidden-xs">
        <div class="container">
            <div class="fl bar-logo">
                <img src="/oubao/assets/images/login-v5/logo-new.png"  />
            </div>
            <ul class="fr">
                <li class="down-center">
                    下载中心
                    <div class="down-drop">
                        <div class="down-menu">
                            <a href="###" class="down-a">
                                <i class="iconfont icon-phone"></i><span>手机客户端</span>
                            </a>
                            <a href="###" class="down-a">
                                <i class="iconfont icon-send"></i><span>极速登录器</span>
                            </a>
                        </div>
                    </div>
                </li>
                <li class="down-center">
                    <a class="online-server">
                        在线客服
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="top-wrap @if ($errors->first('attempt')) top-wrap-c  @endif" id="top-wrap">
        <div class="form-pic"><img src="/oubao/assets/images/login-v5/login-header-logo.png" alt=""></div>
        {{ Form::open(array('class' => 'js-login-form login-form', 'role' => 'form', 'target' => '_top', 'name' => 'signinForm', 'id'=>'loginForm')) }}
            <input type="hidden" name="_random" value="{{ createRandomStr() }}" />
            <div class="form-login">
	            <div style="height: 46px;">
                    @if ($errors->first('attempt'))
                    <div class="remind remind-error">
                        <span>!</span><strong>{{ $errors->first('attempt') }}</strong>
                    </div>
                    @endif
                </div>
                <div class="form-item">
                    <input id="login-name" type="text" name="username" class="form-input form-user" placeholder="请输入用户名" value="" required autofocus>
                </div>
                <div class="form-item">
                    <input id="login-pass" type="password" name="loginPwd" class="form-input form-password" placeholder="请输入密码">
                    <input name="password" id="login-pass-real" type="hidden" required />
                </div>
                @if (Session::get('LOGIN_TIMES') >= 3)
	            <div class="form-item">
	                <input class="form-input form-code" name="captcha" placeholder="请输入验证码" type="text">
                    <img id="J-vcode-img" onclick="changeCaptcha();" src="{{ URL::to('captcha?') }}" class="code-pic" maxlength="5" width="110" height="36" title="看不清? 换张图片">
                </div>
                @endif
	            <div class="form-btn">
                    <button id="J-button-submit" type="button" class="common-btn">
                        <span class="btn-text">立即登录</span><i></i>
                        <!--登录中-->
                        <span class="btn-pic" style="display: none;"></span>
                    </button>
                </div>
                <div class="forget">
                    <a href='javascript:openKF();'>忘记密码</a>
                </div>
            </div>
        {{ Form::close() }}

    </div>

    <div class="top-slide  @if ($errors->first('attempt')) top-slide-c @endif" id="top-slide">
        <div id="myCarousel" class="carousel slide">
            <div class="carousel-inner">
                <div class="item active"><div class="item-a" style="background: url('/oubao/assets/images/login-v5/banner1.jpg') no-repeat center top;"></div></div>
            </div>
        </div>
        <div class="slide-text">
            <div class="text-pic"><img src="/oubao/assets/images/login-v5/login-slide-text.png" alt=""></div>
            <div class="enter">
                <button class="common-btn">
                    <span class="btn-text">进入</span><i></i>
                </button>
            </div>
        </div>
        <div class="arrow-d hidden-xs"><img src="/oubao/assets/images/login-v5/login-arrow-down.png" alt=""></div>
    </div>

    <div class="top-nav visible-xs-block">
        <div class="clearfix">
            <div class="nav-btn"><img src="/oubao/assets/images/login-v5/login-nav.png" alt=""></div>
        </div>
    </div>
    <div class="nav-con">
        <div class="nav-wrap">
            <div class="nav-header clearfix">
                <span class="nav-close"><i class="iconfont icon-close"></i></span>
            </div>
            <ul class="nav-menu">
                <li><a href="###">欧豹品牌</a></li>
                <li><a href="/help">帮助中心</a></li>
                <li><a href="###">联系我们</a></li>
                <li><a href="###" target="_blank">手机客户端</a></li>
                <li><a href="###" target="_blank">快速登录器</a></li>
                <li><a href="/events/repairDNS" target="_blank">防劫持教程</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="j-l-content" id="j-l-content">
    <div class="container login-game">
        <div class="title-wrap hidden-xs"><span class="l-title">简单，从来都不简单</span></div>
        <div class="row">
            <div class="col-sm-8">
                <div class="lottery-con">
                    <div class="lottery-sort"></div>
                    <div class="lottery-bg"></div>
                    <img data-src="/oubao/assets/images/login-v5/login-text-pic.png" class="lottery-pic" alt="">
                </div>
            </div>
            <div class="col-sm-4">
                <div class="lottery-play">
                    <div class="title-1">精选 20 款彩票游戏</div>
                    <div class="title-2">乐趣无穷，精彩无限！</div>
                    <ul class="sort-list">
                        <li>
                            <span class="name">时时彩系列</span><span class="line visible-lg-inline-block">|</span><span class="province">重庆、新疆、天津</span>
                        </li>
                        <li>
                            <span class="name">11选5系列</span><span class="line visible-lg-inline-block">|</span><span class="province">山东、江西、广东</span>
                        </li>
                        <li>
                            <span class="name">快三系列</span><span class="line visible-lg-inline-block">|</span><span class="province">江苏、安徽</span>
                        </li>
                        <li>
                            <span class="name">福彩3D、体彩P3/P5系列</span>
                        </li>
                        <li>
                            <span class="name">极速彩票</span><span class="line visible-lg-inline-block">|</span><span class="province">欧豹1分2分、5分彩、11选5以及快三</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="container no-padding physical-list">
        <div class="row">
            <div class="col-sm-4">
                <div class="physical-wrap physical-wrap-1">
                    <div class="physical">
                        <img data-src="/oubao/assets/images/login-v5/login-play-1.png" class="game-pic" alt="">
                        <div class="game-bg"></div>
                        <div class="game-text">
                            <div class="text-2">骰宝</div>
                            <div class="text-2">ONLINE GAMES</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="physical-wrap physical-wrap-2">
                    <div class="physical">
                        <img data-src="/oubao/assets/images/login-v5/login-play-2.png" class="game-pic" alt="">
                        <div class="game-bg"></div>
                        <div class="game-text">
                            <div class="text-2">百家乐</div>
                            <div class="text-2">ONLINE GAMES</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="physical-wrap physical-wrap-3">
                    <div class="physical">
                        <img data-src="/oubao/assets/images/login-v5/login-play-3.png" class="game-pic" alt="">
                        <div class="game-bg"></div>
                        <div class="game-text">
                            <div class="text-wrap">
                                <div class="text-2">龙虎斗</div>
                                <div class="text-2">ONLINE GAMES</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<div class="bg-lg-s3">
    <div class="container slide-content">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="slide-tit">资金雄厚</div>
                    <div class="slide-con">主集团12年稳健的资金管控能力，强大的现金流，100%兑现赔付，保您畅玩无忧。</div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-tit">充提快速</div>
                    <div class="slide-con">支持中国大陆15家主流银行转账充值，15家银行快捷充值，19家银行银联快捷充值，支付宝、微信支付、财付通充值等主流充值渠道，实现最为快捷、便利的充提体验。 </div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-tit">高额返奖</div>
                    <div class="slide-con">极具吸引力的高奖金组和最强资金兑现力的双重保障，返奖更高，赢利更多！</div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-tit">硬件开奖</div>
                    <div class="slide-con">自主彩使用瑞士超精密硬件Quantis Random Number Generator测量记录完全不可预测的光子自然衰变生成开奖数据，真正做到公平公正。</div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-tit">程序安全</div>
                    <div class="slide-con">100%自主研发，通过Global Trust国际安全认证，采用AES 256位加密，为您提供顶级的游戏体验。</div>
                </div>
            </div>
        </div>
        <div class="swiper-pagination visible-xs-block"></div>
        <div class="swiper-button-next hidden-xs"></div>
        <div class="swiper-button-prev hidden-xs"></div>
    </div>
</div>
</div>


<!--公用的页脚-->
@include('w.footer-v4')

<script src="/oubao/assets/js/base-all.js"></script>
<script>
function openKF() {
	var url = '{{SysConfig::readValue("KFURL")}}';  //转向网页的地址;
    var name = '';                            //网页名称，可为空;
    var iWidth = 750;                          //弹出窗口的宽度;
    var iHeight = 500;                         //弹出窗口的高度;
    //获得窗口的垂直位置
    var iTop = (window.screen.availHeight - 30 - iHeight) / 2;
    //获得窗口的水平位置
    var iLeft = (window.screen.availWidth - 10 - iWidth) / 2;
    window.open(url, name, 'height=' + iHeight + ',,innerHeight=' + iHeight + ',width=' + iWidth + ',innerWidth=' + iWidth + ',top=' + iTop + ',left=' + iLeft + ',status=no,toolbar=no,menubar=no,location=no,resizable=no,scrollbars=0,titlebar=no');
}
    //ie8以下浏览器隐藏的内容
    if (navigator.userAgent.indexOf("MSIE 8.0") > 0) {
        document.getElementById('j-l-content').style.display = 'none';
        document.getElementById('top-slide').style.display = 'none';
        document.getElementById('top-wrap').className += " top-wrap-c";
    }
</script>

<script src="/oubao/assets/js/md5.js"></script>

<script src="/oubao/assets/js/bootstrap-v1.min.js"></script>
<script src="/oubao/assets/js/placeholder.min.js"></script>
<script src="/oubao/assets/js/jquery.lazyload.js"></script>
<script src="/oubao/assets/js/login/login-v5.js"></script>

<script type="text/javascript">
    function changeCaptcha () {
        $('#J-vcode-img').attr('src', "/captcha?" + ((Math.random()*9 +1)*100000).toFixed(0));
    };

    $(function(){
        $('#J-button-submit').click(function (e) {
            $('#J-button-submit .btn-pic').show();

            var pwd = $('#login-pass').val();
            var username = ($('#login-name').val()).toLowerCase();
            $('#login-pass-real').val(md5(md5(md5(username + pwd))));
            $('form[name=signinForm]').submit();
        });

        $('form[name=signinForm]').keydown(function(event) {
            if (event.keyCode == 13) $('#J-button-submit').click();
        });

        $('body').keydown(function(event) {
            if (event.keyCode == 13) {
                $('.top-slide').addClass('top-slide-c');
                $('.top-wrap').addClass('top-wrap-c');
            }
        });

        $('.form-login input').bind('input propertychange', function() {
            if(!$('.remind').hasClass('hidden')){
                $('.remind').addClass('hidden');
            }
        });
    });

</script>

<!--左右切换轮播-->
<script>
    $(function () {
        if (navigator.userAgent.indexOf("MSIE 8.0") < 0) {        //如果为ie8就不能加载js
            $.getScript('/oubao/assets/js/swiper.min.js').then(function () {  //动态加载js文件
                var swiper = new Swiper('.swiper-container', {
                    pagination: '.swiper-pagination',
                    paginationClickable: true,
                    nextButton: '.swiper-button-next',
                    prevButton: '.swiper-button-prev',
                    slidesPerView: 3,
                    spaceBetween: 20,
                    breakpoints: {
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        640: {
                            slidesPerView: 1,
                            spaceBetween: 10
                        },
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 0
                        }
                    }
                });
            })
        }
    });
</script>

</body>
</html>