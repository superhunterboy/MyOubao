<div class="gheader">
    <div class="g_33">
        <a class="logo-lottery"><img src="/assets/images/game/logo/{{ strtolower($sLotteryCode) }}.png" alt="{{ $sLotteryName }}" title="{{ $sLotteryName }}"  style="width: 100%;
   margin-top: 4px;" /></a>
        <div class="deadline">
            <div class="deadline-text">第<strong id="J-header-currentNumber">------</strong>期<br>投注截止</div>
            <div class="deadline-number" id="J-deadline-panel">

                <em class="min-left">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
                <em class="min-right">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
                <span class="space">:</span>

                <em class="min-left">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
                <em class="min-right">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
                <span class="space">:</span>
                <em class="sec-left">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
                <em class="sec-right">
                    <b class="deadline-number-mask"></b>
                    <span class="deadline-num deadline-num-next-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-next-b">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-t">
                        <span class="inner"></span>
                    </span>
                    <span class="deadline-num deadline-num-b">
                        <span class="inner"></span>
                    </span>
                </em>
            </div>
        </div>
        <div class="lottery">
            <div class="lottery-text"><span id="J-header-newnum"></span>期</div>
            <input type="hidden" value="" id="J-input-hidden-lastballs" />
            <div id="J-lottery-balls-lasttime" class="lottery-number">
                <em>-</em>
                <em>-</em>
                <em>-</em>
                <em>-</em>
                <em>-</em>
            </div>
            <div class="lottery-link">
                <!-- <a href="#" target="_blank" class="info">冷热遗漏</a> -->
                <a href="{{ $ways_note_url }}" target="_blank" class="info">玩法说明</a>
                <a href="{{ route('user-trends.trend-view', [$iLotteryId]) }}" target="_blank" class="chart">走势图</a>
            </div>
        </div>
    </div>
</div>


@include('w.feedback')













