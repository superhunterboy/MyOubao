@extends('l.base-v4')

@section('title')
   彩票签到赢奖金,轻松到手388元! - 博狼娱乐
@stop

@section ('styles')
@parent
    <link media="all" type="text/css" rel="stylesheet" href="/assets/images/events/qiandao/qiandao.css">
@stop


@section ('container')
    @include('w.header')
    <div class="activity">
        <div class="activity-title"></div>
        <div class="activity-summary">
            <div class="real-summary">
                活动时间：2016年3月23日00:00:00-2016年4月22日23:59:59
            </div>
        </div>
        <div class="activity-content">
<!--             <table class="pay-get">
                <tr class="pay-get-head">
                    <td>每天流水（元）</td>
                    <td>奖金（元）</td>
                </tr>
                <tr class="pay-get-body">
                    <td>3,888</td>
                    <td>18</td>
                </tr>
                <tr class="pay-get-body">
                    <td>8,888</td>
                    <td>58</td>
                </tr>
                <tr class="pay-get-body">
                    <td>18,888</td>
                    <td>128</td>
                </tr>
                <tr class="pay-get-body">
                    <td>38,888</td>
                    <td>258</td>
                </tr>
                <tr class="pay-get-body">
                    <td>58,888</td>
                    <td>388</td>
                </tr>
            </table> -->
            <div class="shit">
                            <div class="activity-heart">
                <div class="heart-content">
                    <div class="heart-main">
                        <div class="value">
                            <div class="value-block-0">0</div>
                            <div class="value-block-3888">3,888</div>
                            <div class="value-block-8888">8,888</div>
                            <div class="value-block-18888">18,888</div>
                            <div class="value-block-38888">38,888</div>
                            <div class="value-block-58888">58,888</div>
                        </div>

                        <div class="displayer">
                            <div class="qiandao-popup">
                                <div class="bubble"></div>
                                <div class="triangle"></div>
                            </div>
                            <div class="block">
                                <div class="block-1"></div>
                            </div>
                            <div class="block-seperator"></div>
                            <div class="block">
                                <div class="block-2"></div>
                            </div>
                            <div class="block-seperator"></div>
                            <div class="block">
                                <div class="block-3"></div>
                            </div>
                            <div class="block-seperator"></div>
                            <div class="block">
                                <div class="block-4"></div>
                            </div>
                            <div class="block-seperator"></div>
                            <div class="block">
                                <div class="block-5"></div>
                            </div>
                            <div class="refresher" id="J-button-refresh"></div>
                        </div>
                    </div>
                    <div class="heart-button" id="J-button-receive">签到领奖</div>
                    <div class="heart-help">
                        点击进度条右顶端刷新按钮，投注额会推动进度条，进度条达到对应流水后，即可立即开始领奖哦！
                    </div>
                </div>
            </div>
            </div>

        </div>
        <div class="activity-detail">
            活动规则：
            <br/>
            <p>
                1．本活动仅限彩票投注，系统每天00:00:00起按当天实际已经开奖的彩票注单自动计算您当天的彩票流水（撤单、未开奖均不计算在内）。
            </p>
            <p>
                2．一旦符合奖金对应的流水要求，即可点击“签到领奖”领取奖金，每位用户每天只可领取一次奖金。当天23:59:59流水清零, 未领取者视为自动放弃。
            </p>
            <p>
                3．投注码量不能超过70%，即定位胆玩法不能超过7注、二码玩法不能超过70注、三码玩法不能超过700注、四星玩法不能超过7000注、五星玩法不能超过70000注，全包玩法不计入有效投注。
            </p>
            <p>
                4．本活动最终解释权归博狼娱乐所有，博狼娱乐保留暂停、终止、修改等所有权利。
            </p>

        </div>
    </div>

    <div class="qiandao-message">
        <div class="message-content"></div>
        <div class="close-message">确定</div>
    </div>

    <div class="clock-notice">
        <div class="clock-content"></div>
        <div class="close-clock">确定</div>
    </div>
    <div class="qiandao-prompt">
        <div class="prompt-content"></div>
        <div class="sure" id="J-button-sure">立即领取</div>
        <div class="wait">稍后领取</div>
    </div>

    @include('w.footer')
@stop





@section('end')
@parent

<script>
    setInterval(function(){
        var currTime =new Date();
        var hour = currTime.getHours();
        var min = currTime.getMinutes();
        var sec = currTime.getSeconds();
        if(hour=="23"&&min=="59"&&sec=="59"){
            location.reload();
        }
    },500)
    var infoUrl = "{{route('anniversary.get-datas')}}",
        resultUrl="{{route('anniversary.get-red-envelope')}}",
        countDownClosed = true,
        countDown;

    var showClockNotice = function(message){
        $(".clock-content").html(message);
        if(countDownClosed){
            $(".clock-notice").show();
        }
    };

    var hideClockNotice = function(){
        $(".clock-notice").hide();
        countDownClosed = false;
    };

    // 根据从后台获取的信息判断用户领奖是否成功
    var getInfo = function(data) {
        var isUserAvailable = data["isUserAvailable"],
            status = data["status"],
            // turnover用户本期投注额
            turnover = data["turnover"];
            currentEndTime = data["currentEndTime"],
            currentTime = data["currentTime"],
            leftTime = 0,
            currentEndTime = new Date(currentEndTime.split(" ")[0]+"T"+currentEndTime.split(" ")[1]+"Z"),
            currentTime = new Date(currentTime.split(" ")[0]+"T"+currentTime.split(" ")[1]+"Z");

        leftTime = currentEndTime - currentTime;

        // clearInterval(countDown);

        // 倒计时做两件事
        // 1、30秒时进行提醒
        // 2、0秒时刷新数据
        // countDown = setInterval(function(){
        //     var leftSec = leftTime/1000%60,
        //         leftHour = Math.floor(leftTime/1000/3600),
        //         leftMin = Math.floor(leftTime/1000/60)-leftHour*60;

        //     leftTime = leftTime-1000;

        //     if(leftTime <= 1000){
        //         console.log("清空")
        //         clearInterval(countDown);
        //         hideClockNotice();
        //          $.ajax({
        //             cache:false,
        //             url: infoUrl,
        //             dataType: "JSON",
        //             success: function(result) {
        //                 setTurnOver(result['turnover'],result['isUserAvailable']);
        //                 getInfo(result);
        //             },
        //             error: function() {
        //                 toggleMessage("请检查网络连接或跟客服联系",true);
        //             }
        //         })
        //     }
        //     if(leftTime <= 30000){
        //         showClockNotice("今天签到活动还有"+leftTime/1000+"秒结束");
        //     }

        // },1000);


        // 0：不可用，1：已抢了，2：流水不足
        var type = 0;

        // 0:不可用，1:可用
        if (status == 0) {
            type = 0;
            message = "本活动已结束，敬请期待更多精彩活动！";
        } else {
            // 1、已抢,0:未抢
            if (isUserAvailable == 1) {
                type = 1;
                message = "您已领过奖金，请明天再来签到！";
            } else {
                if(turnover < 3888){
                    type = 2;
                    message = "您今天的有效投注额不足，不能领取奖金！";
                }else{
                    // 跳出提示框，询问用户是否领取当前额度的奖金
                    type = 3;
                    message = "您当前可领取奖金"+getBonus(turnover)+"元，是否立即领取？";
                }
            }
        }
        return {
            type: type,
            message: message
        }
    };

    var getBonus = function(turnover){
        var bonus = 0;
        if (turnover >= 3888 && turnover < 8888) {
            bonus = 18;

        } else if (turnover >= 8888 && turnover < 18888) {
            bonus = 58;

        } else if (turnover >= 18888 && turnover < 38888) {
            bonus = 128;

        } else if (turnover >= 38888 && turnover < 58888) {
            bonus = 258;
        }else if(turnover >= 58888){
            bonus = 388;
        }else{
            bonus = 0;
        }
        return bonus;
    }

    var setTurnOver = function(turnover,isUserAvailable){
        if(turnover>58888){
            turnover=58888;
        }
        var blockAmount = 0,
            per = 0,
            cha = 0;
            x = 1;
        for(;x<=5;x++){
            $(".block-"+x).css("width","0");
        }
        if (turnover >= 0 && turnover <= 3888) {
            blockAmount = 1;
            per = turnover/3888*100;
            
        } else if (turnover > 3888 && turnover <= 8888) {
            blockAmount = 2;
            per = (turnover-3888)/(8888-3888)*100;

        } else if (turnover > 8888 && turnover <= 18888) {
            blockAmount = 3;
            per = (turnover-8888)/(18888-8888)*100;
        } else if (turnover > 18888 && turnover <= 38888) {
            blockAmount = 4;
            per = (turnover-18888)/(38888-18888)*100;
        } else if(turnover > 38888 && turnover <= 58888){
            blockAmount = 5;
            per = (turnover-38888)/(58888-38888)*100;
        }

        for(var i=1;i<blockAmount;i++){
            $(".block-"+i).css("width","100%");
        }
        $(".block-"+blockAmount).css("width",per+"%");


        if(blockAmount==1){
            left = (88*per/100-45)+"px";
            cha = 3888 - turnover;
            if(!cha){
                if(isUserAvailable==1){
                    $(".bubble").html("已领"+getBonus(turnover)+"元!");
                }else{
                    $(".bubble").html("可领"+getBonus(turnover)+"元!");
                }
            }else{
                $(".bubble").html("还差"+cha+"元!");
            }
            $(".qiandao-popup").css("left",left).show();
        }else{
            left = (88*per/100)+(blockAmount-1)*3+(blockAmount-1)*88-45+"px";
            if(isUserAvailable==1){
                $(".bubble").html("已领"+getBonus(turnover)+"元!");
            }else{
                $(".bubble").html("可领"+getBonus(turnover)+"元!");
            }
            $(".qiandao-popup").css("left",left).show();
        }
    }

    var toggleMessage = function(message,isShow){
        if(isShow){
            $(".qiandao-message").show();
            $(".message-content").html(message);
        }else{
            $(".qiandao-message").hide();
        }
    };

    var togglePrompt = function(message,isShow){
        if(isShow){
            $(".qiandao-prompt").show();
            $(".prompt-content").html(message);
        }else{
            $(".qiandao-prompt").hide();
        }
    };

    // 点击领取按钮
    $("#J-button-receive").on("click", function() {
        // 提示用户返回信息即可
        $.ajax({
            cache:false,
            url: infoUrl,
            dataType: "JSON",
            // async:false, 
            success: function(result) {
                var info = getInfo(result);
                if(info.type == 3){
                    togglePrompt(info.message,true);
                }else{
                    toggleMessage(info.message,true);
                }
                setTurnOver(result['turnover'],result['isUserAvailable']);
            },
            error: function() {
                toggleMessage("请检查网络连接或跟客服联系",true);
            }
        })
    });

    $("#J-button-sure").on('click',function(){
        togglePrompt("",false);
        var message = '';
        $.ajax({
            cache:false,
            url:resultUrl,
            dataType:"JSON",
            success:function(result){
               if(result.isSuccess==1){
                    var amount = result["data"]["amount"];
                        message = "恭喜您成功领取"+amount+"元奖金！<br/>奖金已自动派发至您的平台账户。";
                        toggleMessage(message,true);
                     $.ajax({
                        cache:false,
                        url: infoUrl,
                        dataType: "JSON",
                        // async:false, 
                        success: function(result) {
                            setTurnOver(result['turnover'],result['isUserAvailable']);
                            getInfo(result);
                        },
                        error: function() {
                            toggleMessage("请检查网络连接或跟客服联系",true);
                        }
                    });
               }else{
                    message = result['msg'];
                    toggleMessage(message,true);
               }
            },
            error:function(){
                toggleMessage("请检查网络连接或跟客服联系",true);
            }
        });
    });

    $(".close-message").on("click",function(){
        $(this).parent().hide();
    });

    $(".close-clock").on("click",function(){
        $(this).parent().hide();
        countDownClosed = false;
    })

    $(".wait").on("click",function(){
        $(this).parent().hide();
    });

    // 点击刷新按钮
    $("#J-button-refresh").on("click", function() {
        $.ajax({
            cache:false,
            url: infoUrl,
            dataType: "JSON",
            success: function(result) {
                setTurnOver(result['turnover'],result['isUserAvailable']);
            },
            error: function() {
                toggleMessage("请检查网络连接或跟客服联系",true);
            }
        })
    });

    // 页面加载的时候就往后台请求当前下注数据。
    $.ajax({
        cache:false,
        url: infoUrl,
        dataType: "JSON",
        // async:false, 
        success: function(result) {
            setTurnOver(result['turnover'],result['isUserAvailable']);
            getInfo(result);
        },
        error: function() {
            toggleMessage("请检查网络连接或跟客服联系",true);
        }
    })
</script>



@stop
