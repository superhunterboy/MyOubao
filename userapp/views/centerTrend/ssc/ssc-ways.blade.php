<div class="trend-header">
    <div class="trend-header-inner">
        <ul>
            @foreach ($aLotteries as $key => $aLottery)
            <li class="{{ $iLotteryId == $aLottery['id'] ? 'current' : '' }}"><a href="{{ route('user-trends.trend-view', [$aLottery['id']]) }}">{{ $aLottery['name'] }}</a></li>
            @endforeach
        </ul>
    </div>
</div>
<div class="select-section">
    <div class="select-section-inner clearfix">
        <ul class="select-list">
        @foreach ($aViewTypes as $key => $sViewType)
            <li class="{{ $sTrendType == $key ? 'current' : '' }}"><a href="{{ route('user-trends.trend-view', [$iLotteryId, $key]) }}">{{ __('_trend.' . $sViewType) }}</a></li>
        @endforeach
        </ul>
        <div class="select-function">
            <i class="arrow-down"></i><a class="arrow-button" href="#" id="J-button-showcontrol">收起</a>
            <!-- <a target="_blank" id="report-download" class="select-download" href="http://www.ph158.com/reportDownload/gametype=cqssc?dataType=periods?dataNum=30">报表下载</a> -->
        </div>
    </div>
</div>


<div class="select-section-content clearfix" id="J-panel-control">
    <div class="select-section-content-inner">
        <div class="function">
            <label class="label"><input data-action="guides" type="checkbox" class="checkbox" checked="checked">&nbsp;&nbsp;辅助线</label>
            <label class="label"><input data-action="lost" type="checkbox" class="checkbox" checked="checked">&nbsp;&nbsp;遗漏</label>
            <label class="label"><input data-action="lost-post" type="checkbox" class="checkbox">&nbsp;&nbsp;遗漏条</label>
            <label class="label"><input data-action="trend" type="checkbox" class="checkbox" checked="checked">&nbsp;&nbsp;走势</label>
            <label class="label"><input data-action="temperature" type="checkbox" class="checkbox">&nbsp;&nbsp;号温</label>
        </div>
        <div class="time" id="J-periods-data">
            <a data-value="30" data-type="count"  href="javascript:void(0);" class="current">近30期</a>
            <a data-value="50" data-type="count" href="javascript:void(0);">近50期</a>
            <a data-value="" data-type="today" href="javascript:void(0);">今日数据</a>
            <a data-value="2" data-type="day" href="javascript:void(0);">近2天</a>
            <a data-value="5" data-type="day" href="javascript:void(0);">近5天</a>
            <!--
            <select id="J-select-param-day">
                <option value="30">近30期</option>
                <option value="50">近50期</option>
                <option value="1">今日数据</option>
                <option value="2">近2天</option>
                <option value="5">近5天</option>
            </select>
        -->
        </div>
        <div class="search">
            <input type="text" value="" id="J-date-star" class="input"> 至 <input id="J-date-end" type="text" value="" class="input">
            <a id="J-time-periods" class="btn" href="javascript:void(0);">搜索</a>
        </div>
    </div>
</div>