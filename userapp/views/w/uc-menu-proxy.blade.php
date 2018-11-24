



<ul class="list clearfix">
    <li @if(Route::current()->getName() == 'team-profits.home')class="active"@endif>
        <span @if(Route::current()->getName() == 'team-profits.home')class="top-bg"@endif></span>
        <a href="{{ route('team-profits.home') }}">团队首页</a>
    </li>

    <li @if($current_tab == 'team-profits')class="active"@endif>
        <span @if($current_tab == 'team-profits')class="top-bg"@endif></span>
        <a href="{{ route('team-profits.index') }}">团队盈亏</a>
    </li>

    <li @if($current_tab == 'mycommission')class="active"@endif>
        <span @if($current_tab == 'mycommission')class="top-bg"@endif></span>
        <a href="{{ route('user-transactions.mycommission',Session::get('user_id')) }}">彩票佣金</a>
    </li>
{{--
    @if(Session::get('is_top_agent') )
    <li @if($current_tab == 'bonus')class="active"@endif>
        <span @if($current_tab == 'bonus')class="top-bg"@endif></span>
        <a href="{{ route('user-profits.bonus') }}">分红报表 </a>
    </li>
    @endif
--}}
    <li @if($current_tab == 'users')class="active"@endif>
        <span @if($current_tab == 'users')class="top-bg"@endif></span>
        <a href="{{ route('users.index') }}">团队管理</a>
    </li>

    <li @if($current_tab == 'accurate-create' || $current_tab == 'create')class="active"@endif>
        <span @if($current_tab == 'accurate-create' || $current_tab == 'create')class="top-bg"@endif></span>
        <a href="{{ route('users.accurate-create') }}">下级开户</a>
    </li>

    @if(Session::get('show_overlimit'))
    <li @if($current_tab == 'my-overlimit-quotas')class="active"@endif>
        <span @if($current_tab == 'my-overlimit-quotas')class="top-bg"@endif></span>
        <a href="{{ route('my-overlimit-quotas.index') }}">高点配额</a>
    </li>
    @endif
</ul>








