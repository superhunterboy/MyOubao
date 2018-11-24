@extends('l.table')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop



@section ('styles')
@parent
	{{ style('game-lhd') }}
    {{ style('game-poker') }}
@stop




@section ('container')
	@include('w.header')
    <audio src="" controls="controls" hidden="true" style="display:none" id="dice-tips-audio"> </audio>
    <div class="main-content" id="J-page-container-main">
        <div class="loading-mask">
            <div class="load-img">
                <div class="load-bar">
                    <span class="load-bar-unfill">
                        <span class="load-bar-fill"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="main-content-body">
            <div class="table-notice2" id="J-panel-notice2">
                <div class="table-notice2-content"></div>
                <a class="close-notice2" href="javascript:;">关闭</a>
            </div>

            <div class="table-result" id="J-panel-result"></div>
            <div id="J-table-mask-lock" class="table-mask-lock" style="display:none">
               

            </div>

            <div class="body-title">
            <div class="banker"></div>
                <div class="game-level">
                    <span class="table-level"><span id="table-name"></span><span id="table-num"></span></span> <span class="table-limit">限赔<span id="max-prize"></span>万</span>
                </div>
                <div class="game-help btn-help">
                    操作限赔说明
                </div>
                <div class="count-down">
                    <!--
                    <div class="count-down-text">
                        距离本期结束
                    </div>
                    -->
                    <div id="J-clock-number" class="count-down-time">
                        <!-- <span class="num">00</span>: -->
                        <span class="num">00</span>
                    </div>
                </div>
            </div>
            <div id="J-desktop" class="body-content">
                <div class="poker-long"></div>
                <div class="poker-hu"></div>
            </div>
        </div>
        <div class="betTool">
            <div class="balance">
                <span class="balance-txt">桌面金额：</span><span id="J-money-bet" class="money">0元</span>
            </div>
            <div id="J-chip-group-cont" class="main-content-chips"></div>
            <div class="money-bet">
                <span class="money-txt">账户余额：</span><span id="J-money-user-balance" class="money J-text-money-value">{{ number_format($fAvailable, 2) }}</span><span class="yuan">元</span>
            </div>
        </div>

        <div class="buttons">
            <div id="J-button-clearall" class="btn-clear"></div>
            <div id="J-button-submit" class="btn-submit"></div>
            <div id="J-button-rebet" class="btn-rebet"></div>
            <div id="J-button-double" class="btn-double"></div>
        </div>



        <div class="main-content-records">
        <div class="pagination">
            <div class="pg-last"></div>
            <div class="pg-curr"></div>
            <div class="pg-next"></div>
        </div>
            <div class="tabs">
                <!-- <div class="tabs-title">
                    <div id="simple-title" tab="simple" class="tab-title simple-title active-title">
                    简洁版
                    </div>
                    <div class="tab-title-seperator"></div>
                    <div id="pro-title" tab="pro" class="tab-title prox-title">
                    专业版
                    </div>
                </div> -->
                <div class="tabs-content">
                    <div id="simple-content" tab="simple" class="tab-content simple-content active-content">
                        <div class="content-container simple-content-container">
                            <div class="normal-tabs">
                                <div class="normal-tabs-title">
                                    <div class="normal-title longhu-title active-tab-title" tab="longhu">龙虎路</div>
                                    <div class="normal-title longdanshuang-title" tab="longdanshuang">龙单双路</div>
                                    <div class="normal-title hudanshuang-title" tab="hudanshuang">虎单双路</div>
                                    <div class="normal-title longhonghei-title" tab="longhonghei">龙红黑路</div>
                                    <div class="normal-title huhonghei-title" tab="huhonghei">虎红黑路</div>
                                </div>
                                <div class="normal-tabs-content">
                                    <div class="normal-content longhu-content active-tab-content" tab="longhu">
                                        <div class="sequential-trands-container normal-sequential-longhu">
                                        </div>
<!--                                         <div class="turnover-trands-container normal-turnover-longhu">
                                        </div> -->
                                        <!-- <div class="longhu-summary"></div> -->
                                    </div>
                                    <div class="normal-content longdanshuang-content" tab="longdanshuang">
                                        <div class="sequential-trands-container normal-sequential-longdanshuang">
                                            
                                        </div>
<!--                                         <div class="turnover-trands-container normal-turnover-longdanshuang">
                                            
                                        </div> -->
                                        <!-- <div class="longdanshuang-summary"></div> -->
                                    </div>
                                    <div class="normal-content hudanshuang-content" tab="hudanshuang">
                                        <div class="sequential-trands-container normal-sequential-hudanshuang">
                                                                                
                                        </div>
<!--                                         <div class="turnover-trands-container normal-turnover-hudanshuang">
                                            
                                        </div> -->
                                        <!-- <div class="hudanshuang-summary"></div> -->
                                    </div>
                                    <div class="normal-content longhonghei-content" tab="longhonghei">
                                        <div class="sequential-trands-container normal-sequential-longhonghei">
                                            
                                        </div>
<!--                                         <div class="turnover-trands-container normal-turnover-longhonghei">
                                            
                                        </div> -->
                                        <!-- <div class="longhonghei-summary"></div> -->
                                    </div>
                                    <div class="normal-content huhonghei-content" tab="huhonghei">
                                        <div class="sequential-trands-container normal-sequential-huhonghei">
                                        
                                        </div>
<!--                                         <div class="turnover-trands-container normal-turnover-huhonghei">
                                            
                                        </div> -->
                                        <!-- <div class="huhonghei-summary"></div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                </div>
            </div>
        </div>
    </div>

	@include('w.footer-v4')
@stop


@section('end')
@parent

<script type="text/javascript">
    var global_game_config = {{ $sLotteryConfig }};
    var global_last_bet_history = {{ $sLastBetHistory }};
    var global_balance = Math.floor({{ $fAvailable }});
</script>

<script type="text/javascript">
    //进入游戏时，增加遮罩进度条
    setTimeout(function(){
        $(".loading-mask").hide();
    },2000);
</script>

{{ script('game-lhd-init') }}

<script type="text/javascript">

// 简洁版和专业版切换
    $(".tab-title").on("click",function(){
        $(".tab-title").removeClass("active-title");
        $(this).addClass("active-title");

        $(".tab-content").removeClass("active-content");
        var level = $(this).attr("tab");
        $(".tab-content[tab="+level+"]").addClass("active-content");
    })
// 简洁版中的五种小玩法切换
    $(".normal-title").on("click",function(){
        $(".normal-title").removeClass("active-tab-title");
        $(this).addClass("active-tab-title");

        $(".normal-content").removeClass("active-tab-content");
        $(".normal-content[tab="+$(this).attr("tab")+"]").addClass("active-tab-content");
    })

// 专业版中的五种小玩法切换
    $(".pro-title").on("click",function(){
        $(".pro-title").removeClass("active-tab-title");
        $(this).addClass("active-tab-title");

        $(".pro-content").removeClass("active-tab-content");
        $(".pro-content[tab="+$(this).attr("tab")+"]").addClass("active-tab-content");
    });

    


    
</script>
<script type="text/javascript">
    
    $(".btn-help").click(function(){
        $("#J-panel-notice2").toggle();
        $(".table-notice2-content").html($("#J-script-play-mothed").text());
    })

    $(".close-notice2").click(function(){
        $("#J-panel-notice2").css("display",'none');
    })

</script>
<script type="text/javascript">
    // 上一页
    $(".pg-last").on("click",function(){
        var currIndex = Number($(".sequentail-trands-pane:visible").attr('index')),
            lastIndex = currIndex-1;
        if(lastIndex<1){
            return;
        }else{
            $(".sequentail-trands-pane").hide();
            $(".sequentail-trands-pane[index="+lastIndex+"]").show();
        }
    })

    // 当前页
    $(".pg-curr").on("click",function(){
        $(".sequentail-trands-pane").hide();
        var currIndex = Number($(".sequentail-trands-pane").last().attr('index'));
        $(".sequentail-trands-pane[index="+currIndex+"]").show();
    })

    // 下一页
    $(".pg-next").on("click",function(){

        var currIndex = Number($(".sequentail-trands-pane:visible").attr('index')),
            total = Number($(".sequentail-trands-pane").last().attr('index')),
            nextIndex = currIndex+1;
        if(nextIndex>total){
            return;
        }else{
            $(".sequentail-trands-pane").hide();
            $(".sequentail-trands-pane[index="+nextIndex+"]").show();
        }
    })

</script>
<script type="text/template" id="J-script-play-mothed">
    <div class="play-method">
        <div class="play-summary-title">
            游戏说明
        </div>
        <div>
            <div class="play-summary-content">
                龙虎斗为扑克斗大游戏，以牌面大小来决定输赢；牌面大小不比花色，只比点数，K为最大，A为最小。注：玩家投注龙或虎，若开和，则仅输一半。
            </div>
            <div class="play-schedule-title">
                操作说明
            </div>
            <div class="play-schedule-content">
                <div class="first">
                    1.投注流程：
                    <div class="item">
                        ->选择筹码
                    </div>
                    <div class="item">
                        ->点击投注区域进行投注
                    </div>
                    <div class="item">
                        ->确认完所有投注后，点击“确认投注“等待开骰，等待期间不可再继续投注
                    </div>
                    <div class="item">
                        ->显示开骰结果，进入下一期继续投注
                    </div>
                </div>
                <div class="second">
                    2.桌面按钮:
                    <div class="item">
                        - 清桌：撤销当前桌面的所有投注筹码
                    </div>
                    <div class="item">
                        - 重押：恢复上期成功投注的投注桌面
                    </div>
                    <div class="item">
                        - 翻倍：当前桌面的所有投注筹码乘以2
                    </div>
                </div>
                <div class="third">
                    3.右键(桌面投注区域)
                    <div class="item">
                        - 撤销：撤销所选投注区域顶面的投注筹码
                    </div>
                    <div class="item">
                        - 清空：撤销所选投注区域所有的投注筹码
                    </div>
                    <div class="item">
                        - 翻倍：所选投注区域的筹码乘以2
                    </div>
                    <div class="item">
                        - All In：下押所有的账号余额
                    </div>
                </div>
                <div class="fifth">
                    <font class="limit">限赔</font>
                    <div class="item">限赔：单人单期最高赔付总奖金。举例说明：在高级场的某期内，某玩家押注了12万龙，结果开奖扑克分别为K和J，则根据赔率1:1计算共返奖24万，但由于高级场限赔上限是20万，因此实际返奖仅为20万。</div>
                </div>
            </div>
</script>


@stop

















