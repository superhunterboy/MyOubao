<div class="bottom">

    <ul>
        <li>
            <a href="/brand">博猫品牌</a>   /
            <a href="/help">帮助中心</a>   /
            <a href="/brand#concat">联系我们</a>  /
            <a href="/mobile" target="_blank">手机客户端</a>   /
            <a href="/pc-client/index.html" target="_blank">PC客户端</a>   /
            <a href="/fastlogin/index.html" target="_blank">快速登录器</a> /
            客服热线:400-600-1238
        </li>
        <li>Copyright © 2014 - 2016 博猫彩票版权所有All Rights Reserved &nbsp;&nbsp;博猫网络技术有限公司&nbsp;&nbsp;粤公网安备 44030502000178号  ICP证：粤B2-20140796号-1
        </li>

        <li>博猫彩票郑重提示：彩票有风险，投注需谨慎。不向未满18周岁的青少年出售彩票</li>
    </ul>



</div>

@include('w.service')
<script>
    window.onload=function () {
        (function($){
            //回到顶部
            var refer = $('.top .c2'),dom,offset,win,timer;
            if(refer.size() < 1){
                return;
            }
            win = $(window);
            offset = {
                left:-300,
                top:-40
            };

            setTimeout(function(){

                dom = $('<a class="global-gototop" id="J-global-gototop" href="#">返回顶部</a>').appendTo('body');
                dom.css({'left':offset.left + refer.width() + 20});
                dom.click(function(e){
                    e.preventDefault();
                    $('html,body').animate({scrollTop:0}, 400);
                });
                win.scroll(function(){
                    clearTimeout(timer);
                    timer = setTimeout(function(){
                        if(win.scrollTop() > 200){
                            dom.fadeIn(700);
                        }else{
                            dom.fadeOut(700);
                        }
                    }, 300);

                });
            }, 2000);



            //问题反馈
            var feedbackHandler = $('#J-global-panel-feedback');
            //当前积分
            var curscoreHandler = $('#J-global-panel-curscore');

            if(feedbackHandler.size() > 0){
                $(document).on('keyup', '.feedback-textarea', function(){
                    var v = this.value,len = v.length,maxlen = 1000;
                    $('#J-text-feedback-length').text(len);
                    if(len > maxlen){
                        this.value = v.substr(0, maxlen);
                    }
                });
                feedbackHandler.css({'left':offset.left + refer.width() + 10});
                curscoreHandler.css({'left':offset.left + refer.width() - 75});
                feedbackHandler.click(function(){
                    var wd = bomao.Message.getInstance(),text = '';
                    wd.show({
                        isShowMask: true,
                        confirmText:' 提 交 ',
                        confirmIsShow: true,
                        title:'用户反馈',
                        content:$('#J-template-feedback-text').html(),
                        confirmFun:function(){
                            text = $('#J-text-feedback-value').val();
                            if($.trim(text) == ''){
                                alert('内容不能为空');
                                $('#J-text-feedback-value').focus();
                                return;
                            }
                            $.ajax({
                                url:'/suggestions/create',
                                dataType:'json',
                                method:'post',
                                data:{'comment':text,'_token':$.trim($('#J-global-token-value').val())},
                                beforeSend:function(){
                                    wd.hide();
                                },
                                success:function(data){
                                    if(Number(data['isSuccess']) == 1){
                                        wd.show({
                                            isShowMask: true,
                                            confirmIsShow: true,
                                            title:'提示',
                                            content:'<div style="font-size:16px;">提交成功！</div>',
                                            confirmFun:function(){
                                                wd.hide();
                                            }
                                        });
                                    }else{
                                        wd.show({
                                            isShowMask: true,
                                            confirmIsShow: true,
                                            confirmText:' 关闭 ',
                                            title:'提示',
                                            content:'<div style="font-size:16px;">'+ data['Msg'] +'</div>',
                                            confirmFun:function(){
                                                wd.hide();
                                            }
                                        });
                                    }
                                },
                                error:function(xhr, type){
                                    alert('提交失败:' + type);
                                }
                            });
                        },
                        closeFun:function(){
                            wd.hide();
                        }
                    });

                });
            }



        })(jQuery);
    };

</script>