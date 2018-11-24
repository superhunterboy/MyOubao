@extends('l.base')


@section ('styles')

@parent
    {{ style('game-v3') }}
    {{ style('animate') }}
    {{ style('font-awesome')}}
@stop


@section ('container')
    @include('w.header')
    
    @include('w.game-header')


    <div class="g_33 main" id="J-page-container-main">
        <div class="betsvoice" id="betsvoice">
            <span class="open"></span>
            <h5>音效</h5>

        </div>
        <div id="J-panel-cont-types" class="play-section">
            @include('w.play-select')
        </div>


        <div id="J-panel-cont-balls" class="number-section clearfix">
                @section ('centerGame')
                @show
        </div>





        <div class="play-footer clearfix">
            <div class="panel-section">
                @include('w.lottery-box')
            </div>
            @include('w.trace-panel')
        </div>



        <div class="bet-statics-commit">
                <div class="bet-statistics">
                    总计：<em id="J-gameOrder-lotterys-num">0</em>注，共<em id="J-gameOrder-amount">0.00</em>元&nbsp;&nbsp;<span class="bet-statics-text-small panel-text-user-balance">可用余额 <em id="J-user-amount-num">0.00</em>元</span>
                </div>
                <div class="btn-bet-bg" id="J-btn-bet">
                    <a id="J-submit-order" class="btn-bet btn-bet-disable" href="javascript:void(0)"><span class="btn-bet-text"><span id="J-button-btn-time">--:--</span></span></a>
                </div>
        </div>




        <div class="list-full-history" id="J-list-history-panel">
            <div class="title clearfix">
                <ul>
                    <li>我的投注</li>
                    <li>我的追号</li>
                </ul>
            </div>
            <div class="content">
                <table width="100%">
                    <thead>
                        <tr>
                            <th><div class="th-line">游戏</div></th>
                            <th><div class="th-line">玩法</div></th>
                            <th><div class="th-line">期号</div></th>
                            <th width="150"><div class="th-line">开奖号</div></th>
                            <th><div class="th-line">投注内容</div></th>
                            <th><div class="th-line">投注金额</div></th>
                            <th width="120"><div class="th-line">奖金</div></th>
                            <th><div class="th-line">返点</div></th>
                            <th><div class="th-line">状态</div></th>
                            <th><div class="th-line">操作</div></th>
                        </tr>
                    </thead>
                    <tbody id="J-tbody-historys-bets">
                    </tbody>
                </table>
            </div>
            <div class="content">
                <table width="100%">
                    <thead>
                        <tr>
                            <th><div class="th-line">游戏</div></th>
                            <th><div class="th-line">玩法</div></th>
                            <th><div class="th-line">起始奖期</div></th>
                            <th><div class="th-line">追号进度</div></th>
                            <th><div class="th-line">总追号金额</div></th>
                            <th><div class="th-line">已中奖金额</div></th>
                            <th><div class="th-line">追中即停</div></th>
                            <th><div class="th-line">状态</div></th>
                            <th><div class="th-line">操作</div></th>
                        </tr>
                    </thead>
                    <tbody id="J-tbody-historys-traces">
                    </tbody>
                </table>
            </div>
        </div>




        <div id="J-mask-page-inner" class="mask-page-inner"></div>


    </div>



    <div id="J-panel-sidetip" class="panel-sidetip">
        <div class="sidetip-title">
            <span class="sidetip-title-text">提示</span>
            <a href="#" class="sidetip-close">关闭</a>
        </div>
        <div class="sidetip-content">

        </div>
    </div>


    @include('w.footer-v4')

@stop




@section('end')
@parent
	{{ script('game-all') }}
@stop

<script>
    window.onload=function () {
        bomao.voice.voicestart();
    };
</script>

