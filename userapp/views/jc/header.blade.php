


<div class="layout-header-menu">
    <div class="sports-header-container container clearfix">
        
        <a href="/jc/football" class="logo"></a>
        
        <ul class="sprts-menu">
            @foreach($aMenuList as $oMethodGroup)
            <li @if($sTabKey== $oMethodGroup->identifier) class="active" @endif><span></span><a href="{{{ route('jc.match_list', [$oJcLottery->identifier, $oMethodGroup->identifier]) }}}">{{{ $oMethodGroup->name }}}</a></li>
            @endforeach
            <li @if(isset($sTabKey) && $sTabKey == 'single') class="active" @endif><span></span><a href="{{{ route('jc.match_list', [$oJcLottery->identifier, 'single']) }}}">单关投注</a></li>
        </ul>

        <ul class="menu-sm">
            <li class="ass-menu @if($sTabKey== 'groupbuy')ass-menu-active@endif"><a href="{{{ route('jc.groupbuy', $oJcLottery->identifier) }}}">合买大厅</a></li>
            <li class="ass-menu @if($sTabKey== 'bet_list')ass-menu-active@endif"><a href="{{{ route('jc.bet_list', $oJcLottery->identifier) }}}">我的投注</a></li>
            <li><a href="#" target="view_window" onclick="window.open('http://c.spdex.com/spdex500b','spdex','scrollbars=yes,width=830,height=600,top=60,left=100');">必发指数</a></li>
            <li class="ass-menu @if($sTabKey== 'result')ass-menu-active@endif"><a href="{{{ route('jc.result', $oJcLottery->identifier) }}}">开奖</a></li>
            <li class="live"><a href="http://info.sporttery.cn/livescore/fb_livescore.html" target="_blank">比分</a></li>
        </ul>

    </div>
</div>



<div class="float-help" id="J-sports-float-help">
    <ul class="list">
        <li><a class="help-ico-a" target="_blank" href="{{{ route('jc.help', 'a') }}}">新手指南</a></li>
        <li><a class="help-ico-b" target="_blank" href="{{{ route('jc.help', 'b') }}}">竞彩玩法</a></li>
        <li><a class="help-ico-c" target="_blank" href="{{{ route('jc.help', 'c') }}}">竞彩投注</a></li>
        <li><a class="help-ico-d" target="_blank" href="{{{ route('jc.help', 'd') }}}">亚洲盘口</a></li>
        <li><a class="help-ico-e" target="_blank" href="{{{ route('jc.help', 'e') }}}">欧洲赔率</a></li>
        <li><a class="help-ico-f" target="_blank" href="{{{ route('jc.help', 'f') }}}">竞足攻略</a></li>
        <li><a class="help-ico-a" target="_blank" href="/help/14">更多帮助</a></li>
    </ul>
</div>











