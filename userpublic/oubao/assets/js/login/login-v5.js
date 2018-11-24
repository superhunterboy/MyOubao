$(function () {
    sizeEnter();
    downMenu();
    develop();
    slider();
    $('img').lazyload(); //分页加载js
});

//登录第一页
function sizeEnter() {
    var oBody = $('body');
    var oHeader = $('#j-l-header');
    var oEnter = $('.enter .common-btn');
    var oArrow = $('.arrow-d');
    var oNav = $('.top-nav');
    var oBtn = $('.nav-btn');
    var oCon = $('.nav-con');
    var oWrap = $('.nav-wrap');
    var oClose = $('.nav-close');
    var emailBT = $('.email-btn');
    var oHeight = document.documentElement.clientHeight;
    // var h = document.body.clientHeight;
    $(window).resize(function () {
        oHeight = document.documentElement.clientHeight;
    });
    //点击向下的箭头
    oArrow.click(function () {
        $('html,body').animate({'scrollTop': oHeight}, 400);
    });
    //点击进入按钮
    oEnter.click(function () {
        $('.top-slide').addClass('top-slide-c');
        $('.top-wrap').addClass('top-wrap-c');
    });
    //点击左上角的按钮，让phone-con出来
    oBtn.click(function () {
        oCon.fadeIn(200);
        oWrap.addClass('nav-wrap-c');
        oNav.addClass('top-nav-c');
    });
    //点击关闭按钮，让phone-con关闭
    oClose.click(function () {
        oCon.fadeOut(200);
        oWrap.removeClass('nav-wrap-c');
        oNav.removeClass('top-nav-c');
    });
    //订阅按钮
    emailBT.click(function(){
        var reg_email = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
        var email_text = $('.order-input').val();

        if(email_text == ''){
            $('#email-remind-error strong').text('请输入您的E-Mail电子邮箱地址');
            $('#email-remind-error').addClass('remind-error');
        }else{
            if(!reg_email.test(email_text)){
                $('#email-remind-error strong').text('邮箱格式不正确，请您重新输入');
                $('#email-remind-error').addClass('remind-error');
            }else{
                //提交订阅邮箱
                var url = '/auth/saveemail';
                $.ajax({
                    url: url,
                    dataType: 'JSON',
                    method: 'POST',
                    data: {email : email_text},
                    success: function(data) {
                        if(data){
                            switch(data['status']){
                                case 'success' : 
                                    $("#order-email-box").hide();
                                    $("#order-email-success").show();
                                    break;
                                default:
                                    $('#email-remind-error strong').text('邮箱只可订阅一次，请勿重复提交，如有疑问请联系在线客服');
                                    $('#email-remind-error').addClass('remind-error');
                                    break;
                            }
                        }
                    }
                });
            }
        }
    });
    $('.order-input').bind('input propertychange',function(){
        $('#email-remind-error').removeClass('remind-error');
    });
    //页面往下滚动，头部颜色变黑
    $(window).scroll(function () {
        var top = oBody.scrollTop();
        if(top < 140){
            $('.top-nav').css('background','rgba(0,0,0,'+top/150+')');
        }
    });
}
//首页右上角下拉效果
function downMenu() {
    $('.down-center').hover(function () {
        $(this).find('.down-drop').stop(true, false).fadeIn(400);
        $(this).find('.down-menu').addClass('down-menu-hov');
    }, function () {
        $(this).find('.down-drop').stop(true, false).fadeOut(400);
        $(this).find('.down-menu').removeClass('down-menu-hov');
    })
}
// 设置自动轮播和左右触屏滑动js
function slider() {
    $('.carousel').carousel({
        interval:4000
    }); //自动轮播
    var isTouch = ('ontouchstart' in window);
    if (isTouch) {
        $(".carousel").on('touchstart', touchMove);
    }
}
//手指左右滑动js
function touchMove(e) {
    var that = $(this);
    var touch = e.originalEvent.changedTouches[0];
    var startX = touch.pageX;
    var startY = touch.pageY;
    $(document).on('touchmove', function (e) {
        touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
        var endX = touch.pageX - startX;
        var endY = touch.pageY - startY;
        if (Math.abs(endY) < Math.abs(endX)) {
            if (endX > 40) {
                $(this).off('touchmove');
                that.carousel('prev');
            } else if (endX < -40) {
                $(this).off('touchmove');
                that.carousel('next');
            }
            return false;
        }
    });
    $(document).on('touchend', function () {
        $(this).off('touchmove');
    })
}
// 发展历程的移动效果
function develop() {

    var oWrap = $('.move-wrap'),
        oYear = oWrap.find('.move-year'),
        oEvent = oWrap.find('.move-event'),
        widths = oEvent.eq(0).width(),
        t = 400;

    oEvent.click(function () {
        var i = $('.move-wrap .move-li').index($(this).parent('.move-li'));
        var n = $(this).parents('.move-list').find('.move-li').index($(this).parent('.move-li'));
        var thisYear = $(this).parents('.move-list').prev('.move-year');
        var prevYear = $(this).parents('.move-date').prevAll().find('.move-year');
        var nextYear = $(this).parents('.move-date').nextAll().find('.move-year');

        oEvent.parent().removeClass('move-active');
        $(this).parent().addClass('move-active');

        oYear.removeClass('move-cur');
        thisYear.addClass('move-cur');

        oWrap.delay(100).animate({'marginLeft': -i * widths}, t);
        thisYear.delay(100).animate({'left': n * widths}, t);

        prevYear.delay(100).animate({'left': '50%'}, t);
        nextYear.delay(100).animate({'left': '50%'}, t);
    });
}
