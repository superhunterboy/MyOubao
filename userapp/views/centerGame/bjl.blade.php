@extends('l.table')

@section('title')
    {{ __($sLotteryName) }}
@parent
@stop



@section ('styles')
@parent
	{{ style('game-bjl') }}
@stop




@section ('container')
	@include('w.header')
    <audio src="" controls="controls" hidden="true" style="display:none" id="dice-tips-audio"> </audio>
    <div class="main-content">
        <div class="loading-mask">
            <div class="load-img">
                <div class="load-bar">
                    <span class="load-bar-unfill">
                        <span class="load-bar-fill"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="banker"></div>
        <div id="J-desktop" class="main-content-body">
            <div class="notice" id="J-panel-notice">
                <div class="notice-content"></div>
            </div>
            <div class="win-notice" id="J-win-notice">
                <div class="win-notice-content"></div>
            </div>
            <div class="table-notice2" id="J-panel-notice2">
                <div class="table-notice2-content"></div>
                <a class="close-notice2" href="javascript:;">关闭</a>
            </div>

            <div id="J-table-mask-lock" class="table-mask-lock" style="display:none"></div>
            <div class="poker-locations">
            <div class="poker-sender">
            <div class="poker-sender-container"></div>
            <div class="poker-container">
                <div class="card">
                    <div class="poker poker-3">
                    </div>
                    <div class="poker back">
                    </div>
                </div>
            </div>
            <div class="poker-sender-cover"></div>
        </div>
                <div class="poker-container"></div>
                <div class="xian-holder-1"></div>
                <div class="xian-holder-2"></div>
                <div class="zhuang-holder-1"></div>
                <div class="zhuang-holder-2"></div>
                <div class="xian-value"></div>
                <div class="zhuang-value"></div>
            </div>
            <div class="game-level">
                <span class="table-level"><span id="table-name"></span><span id="table-num"></span></span> <span class="table-limit">限赔<span id="max-prize"></span>万</span>
            </div>
            <div class="game-help btn-help">
            </div>
            <div class="count-down">
                <div id="J-clock-number" class="count-down-time">
                    <!-- <span class="num">00</span>: -->
                    <span class="num">00</span>
                </div>
            </div>
        </div>
        <div id="J-chip-group-cont" class="main-content-chips">

        </div>
        <div class="betTool">
            <div class="balance">
               <span id="J-money-bet" class="money-balance">0.00</span>
            </div>
            
            <div id="J-button-clearall" class="btn-clear"></div>
            <div id="J-button-submit" class="btn-submit"></div>
            <div id="J-button-rebet" class="btn-rebet"></div>
            <div id="J-button-double" class="btn-double"></div>
            <div class="money-bet">
               <span id="J-money-user-balance" class="money J-text-money-value"></span>
            </div>
        </div>
        <div class="betTrand">
            <div class="trandController">
                <div class="paginate">
                    <div class="btn-last"></div>
                    <div class="btn-curr"></div>
                    <div class="btn-next"></div>
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
    var WEB_SOCKET_SERVER = "{{ $WEB_SOCKET_SERVER }}";
    var global_last_bet_history = {{ $sLastBetHistory }};
    var global_balance = Math.floor({{ $fAvailable }});
    var userId = "{{urlencode(Session::get('user_id'))}}";
</script>

<script type="text/javascript">
    //进入游戏时，增加遮罩进度条
    setTimeout(function(){
        $(".loading-mask").hide();
    },2000);
</script>


{{ script('game-bjl-init') }}

<script type="text/javascript">
    
    $(".btn-help").click(function(){
        $("#J-panel-notice2").toggle();
        $(".table-notice2-content").html($("#J-script-play-method").text());
    })

    $(".close-notice2").click(function(){
        $("#J-panel-notice2").css("display",'none');
    })

</script>

<script type="text/javascript">
    // 上一页
    $(".btn-last").on("click",function(){
        var currIndex = Number($(".pane:visible").attr('index')),
            lastIndex = currIndex-1;
        if(lastIndex<1){
            return;
        }else{
            $(".pane").hide();
            $(".pane[index="+lastIndex+"]").show();
        }
    })

    // 当前页
    $(".btn-curr").on("click",function(){
        $(".pane").hide();
        var currIndex = Number($(".pane").last().attr('index'));
        $(".pane[index="+currIndex+"]").show();
    })

    // 下一页
    $(".btn-next").on("click",function(){

        var currIndex = Number($(".pane:visible").attr('index')),
            total = Number($(".pane").last().attr('index')),
            nextIndex = currIndex+1;
        if(nextIndex>total){
            return;
        }else{
            $(".pane").hide();
            $(".pane[index="+nextIndex+"]").show();
        }
    })
</script>

<script type="text/template" id="J-script-play-method">
<div class="play-method">
    <div class="play-method-title">
        牌面点数   
    </div>
    <div class="play-method-content">
        1-9分别为1-9点；10、J、Q、K均为0点
    </div>
    <br/>
    <div class="play-method-title">
        发牌规则
    </div>
    <div class="play-method-content">
        1.如果闲家或者庄家两点之和是8点或者9点，则不用抽第三张牌，直接比较大小<br/>
        2.如果闲家点数小于或等于5点，则闲家抽取第三张牌<br/>
        3.如果闲家没有抽取第三张牌，且庄家的点数大于或等于6，则庄家不抽取第三张牌；如果闲家没有抽取第三张牌，且庄家的点数小于6，则庄家抽取第三张牌<br/>
        4.如果闲家抽取了第三张牌，那么<br/>
                            <div class="play-method-content-detail">
                                a.如果庄家点数<=2, 则庄家抽取第三张牌<br/>
                                b.如果庄家点数=3，且闲家第三张牌不是8，则庄家抽取第三张牌；<br/>
                                c.如果庄家点数=4，且闲家第三张牌不是0，1，8，9，则庄家抽取第三张牌<br/>
                                d.如果庄家点数=5，且闲家第三张牌是4，5，6，7，则庄家抽取第三张牌<br/>
                                e.如果庄家点数=6，且闲家第三张牌是6，7，则庄家抽取第三张牌<br/>
                            </div>
        Super6：庄家6点获胜，则只赔一半。即玩家压了100元庄，开奖结果：庄6点，闲2点，则应给玩家派奖150元。<br/>
        大：庄、闲发牌张数的和大于4<br/>
        小：庄、闲发牌张数的和等于4<br/>
        庄对：庄前两张牌是对子，不分花色<br/>
        闲对：闲前两张牌是对子，不分花色<br/>
        押庄或闲，结果为和，则投注金额返还给玩家；押和则1赔8；其他玩法则未中奖。

    </div>
    <br/>
    <div class="play-method-title">
        庄龙宝、闲龙宝派彩 
    </div>
        <table class="tb-pm">
            <tr>
                <th></th>
                <th colspan="2">闲龙宝</th>
                <th colspan="2">庄龙宝</th>
            </tr>
            <tr>
                <td rowspan="6">以非例牌胜出</td>
                <td>赢9点</td>
                <td>1赔30</td>
                <td>赢9点</td>
                <td>1赔30</td>
            </tr>
 
            <tr>
                <td>赢8点</td>
                <td>1赔10</td>
                <td>赢8点</td>
                <td>1赔10</td>
            </tr>
            <tr>
                <td>赢7点</td>
                <td>1赔6</td>
                <td>赢7点</td>
                <td>1赔6</td>
            </tr>
            <tr>
              
                <td>赢6点</td>
                <td>1赔4</td>
                <td>赢6点</td>
                <td>1赔4</td>
            </tr>
            <tr>
           
                <td>赢5点</td>
                <td>1赔2</td>
                <td>赢5点</td>
                <td>1赔2</td>
            </tr>
            <tr>
                <td>赢4点</td>
                <td>1赔1</td>
                <td>赢4点</td>
                <td>1赔1</td>
            </tr>

            <tr>
                <td >以例牌胜出</td>
                <td colspan="4">1赔1</td>

            </tr>       
            <tr>
                <td></td>
                <td colspan="4">和&nbsp;&nbsp;&nbsp;&nbsp;退回本金</td>
            </tr>
        </table>
        <div class="play-method-content">
            非例牌定义：头两张牌的点数总和不是8点或9及所有三张牌的情况<br/>
            例牌定义：头两张牌的点数总和为8点或9点    
        </div>
    </div>
</div>
</script>


@stop

















