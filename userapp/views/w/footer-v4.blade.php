<div class="global-footer">
    <div class="container clearfix">
        <div class="cell">
            运营资质
        </div>
        <div class="cell cell-a">
            博彩责任
        </div>
        <div class="cell cell-b">
        </div>
        <div class="cont">
            <div class="foot-link">
                <a href="###" target="_blank">欧豹品牌</a>   /
                <a href="###" target="_blank">测速中心</a>   /
                <a href="###" target="_blank">手机客户端</a>   /
                <a href="/help">帮助中心</a>   /
                <a href="javascript:void(0);" onClick="openKF()">联系我们</a>  /
                <a href="/events/repairDNS/index.html" target="_blank">防劫持教程</a>
            </div>
            <div class="copy">© 2014 欧豹娱乐版权所有 <font>菲律宾政府合法博彩牌照认证</font></div>
            <div class="foot-warning">欧豹娱乐郑重提示：彩票有风险，投注需谨慎，不向未满18周岁的青少年出售彩票</div>
        </div>
    </div>
</div>


<script type="text/javascript">
    (function ($) {
        new bomao.Hover({
            triggers: '#J-footer-weichat',
            panels: '#J-footer-panel-weichat',
            currPanelClass: 'panel-weichat-hover'
        });

        //回到顶部
        var refer = $('#J-header-container'), dom, offset, win, timer;
        if (refer.size() < 1) {
            //首页应用
            $("#J-global-gototop").click(function (e) {
                e.preventDefault();
                $('html,body').animate({scrollTop: 0}, 400);
            });

            setTimeout(function () {
                $(window).scroll(function () {
                    clearTimeout(timer);
                    timer = setTimeout(function () {
                        if ($(window).scrollTop() > 200) {
                            $("#J-global-gototop").fadeIn(700);
                        } else {
                            $("#J-global-gototop").fadeOut(700);
                        }
                    }, 300);
                });
            });

            return;
        }
        win = $(window);
        offset = refer.offset();

        setTimeout(function () {
            dom = $('<a class="global-gototop" id="J-global-gototop" href="#">返回顶部</a>').appendTo('body');
            dom.css({'left': offset.left + refer.width() + 20});
            dom.click(function (e) {
                e.preventDefault();
                $('html,body').animate({scrollTop: 0}, 400);
            });
            win.scroll(function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    if (win.scrollTop() > 200) {
                        dom.fadeIn(700);
                    } else {
                        dom.fadeOut(700);
                    }
                }, 300);

            });
        }, 2000);



        //问题反馈
        var feedbackHandler = $('#J-global-panel-feedback');
        //当前积分
        var curscoreHandler = $('#J-global-panel-curscore');
        if (feedbackHandler.size() > 0) {
            $(document).on('keyup', '.feedback-textarea', function () {
                var v = this.value, len = v.length, maxlen = 1000;
                $('#J-text-feedback-length').text(len);
                if (len > maxlen) {
                    this.value = v.substr(0, maxlen);
                }
            });
            feedbackHandler.css({'left': offset.left + refer.width() + 10});
            curscoreHandler.css({'left': offset.left + refer.width() - 75});
            feedbackHandler.click(function () {
                var wd = bomao.Message.getInstance(), text = '';
                wd.show({
                    isShowMask: true,
                    confirmText: ' 提 交 ',
                    confirmIsShow: true,
                    title: '用户反馈',
                    content: $('#J-template-feedback-text').html(),
                    confirmFun: function () {
                        text = $('#J-text-feedback-value').val();
                        if ($.trim(text) == '') {
                            alert('内容不能为空');
                            $('#J-text-feedback-value').focus();
                            return;
                        }
                        $.ajax({
                            url: '/suggestions/create',
                            dataType: 'json',
                            method: 'post',
                            data: {'comment': text, '_token': $.trim($('#J-global-token-value').val())},
                            beforeSend: function () {
                                wd.hide();
                            },
                            success: function (data) {
                                if (Number(data['isSuccess']) == 1) {
                                    wd.show({
                                        isShowMask: true,
                                        confirmIsShow: true,
                                        title: '提示',
                                        content: '<div style="font-size:16px;">提交成功！</div>',
                                        confirmFun: function () {
                                            wd.hide();
                                        }
                                    });
                                } else {
                                    wd.show({
                                        isShowMask: true,
                                        confirmIsShow: true,
                                        confirmText: ' 关闭 ',
                                        title: '提示',
                                        content: '<div style="font-size:16px;">' + data['Msg'] + '</div>',
                                        confirmFun: function () {
                                            wd.hide();
                                        }
                                    });
                                }
                            },
                            error: function (xhr, type) {
                                alert('提交失败:' + type);
                            }
                        });
                    },
                    closeFun: function () {
                        wd.hide();
                    }
                });

            });
        }



    })(jQuery);
</script>