@if (Session::get('is_agent'))
        <div class="nav-bg nav-bg-tab">
            <div class="title-normal">报表查询</div>
            <ul class="tab-title clearfix">
                <li @if($reportName=='commission')class="current"@endif><a href="{{ route('user-profits.commission') }}"><span>代理返点报表</span></a></li>
                <li @if($reportName=='profit')class="current"@endif><a href="{{ route('user-profits.index') }}"><span>代理盈亏报表</span></a></li>
                <li @if($reportName=='bonus')class="current"@endif><a href="{{ route('user-bonuses.index') }}"><span>代理分红报表</span></a></li>
                @if(Session::get('is_top_agent'))
                <li @if($reportName=='prizesetfloat')class="current"@endif><a href="{{ route('user-prizeset-float-reports.index') }}"><span>代理升降点报表</span></a></li>
                @endif
            </ul>
        </div>
    @else
        <div class="nav-bg">
            <div class="title-normal">
                盈亏报表
            </div>
        </div>
    @endif