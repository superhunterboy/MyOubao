<ul class="menu-middle clearfix">
    @if (Session::get('is_agent'))
    <li class="list-money {{ isset($resource) && $resource == 'transactions' ? 'list-money-current' : '' }}">
        <a href="{{ route('user-transactions.index') }}"><span>资金明细</span></a>
    </li>
    <li class="list-client {{ isset($resource) && $resource == 'user-client' ? 'list-client-current' : '' }}">
        <a href="{{ route('users.index') }}"><span>用户管理</span></a>
    </li>
    <li class="list-reportform {{ isset($resource) && $resource == 'user-reportform' ? 'list-reportform-current' : '' }}">
        <a href="{{ route('user-profits.index') }}"><span>报表查询</span></a>
    </li>
    @else
    <li class="record-game {{ isset($resource) && $resource == 'bets' ? 'record-game-current' : '' }}">
        <a href="{{ route('projects.index') }}"><span>游戏记录</span></a>
    </li>
    <li class="record-trace {{ isset($resource) && $resource == 'traces' ? 'record-trace-current' : '' }}">
        <a href="{{ route('traces.index') }}"><span>追号记录</span></a>
    </li>
    <li class="list-money {{ isset($resource) && $resource == 'transactions' ? 'list-money-current' : '' }}">
        <a href="{{ route('user-transactions.index') }}"><span>资金明细</span></a>
    </li>
    @endif
</ul>

<ul class="menu-big clearfix">
    @if (Session::get('is_agent'))
    <li class="adduser"><a href="{{ route('users.accurate-create') }}">新增用户</a></li>
    @else
    <li><a href="{{ route('user-recharges.netbank') }}">充值</a></li>
    @endif
    <li class="last"><a href="{{ route('user-withdrawals.withdraw') }}">提现</a></li>
</ul>


<ul class="menu-small clearfix">
    <li><a href="{{ route('users.password-management') }}">密码管理</a></li>
    <li><a href="{{ route('bank-cards.index') }}">银行卡管理</a></li>
    <li><a href="{{ route('users.personal') }}">个人资料</a></li>
    <li><a href="{{ route('user-user-prize-sets.game-prize-set') }}">我的奖金</a></li>
    <li><a href="{{ route('station-letters.index') }}">站内信</a></li>
    <li><a href="{{ route('announcements.index') }}">平台公告</a></li>
</ul>

