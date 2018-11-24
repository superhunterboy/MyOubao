


<ul class="list clearfix">
    @if($current_tab == 'station-letters')
    <li @if($current_tab == 'station-letters')class="active"@endif>
        <span @if($current_tab == 'station-letters')class="top-bg"@endif></span>
    	<a href="{{ route('station-letters.index') }}">站内信 ({{ isset($unreadMessagesNum) ? $unreadMessagesNum : 0 }})</a>
    </li>
    @else
    <li @if($current_tab == '{type?}')class="active"@endif>
        <span @if($current_tab == '{type?}')class="top-bg"@endif></span>
    	<a href="{{ route('users.password-management')}}">密码管理</a>
    </li>
    <li @if($current_tab == 'security-questions' || $current_tab == 'checkrules' || $current_tab == 'savedata')class="active"@endif>
        <span @if($current_tab == 'security-questions' || $current_tab == 'checkrules' || $current_tab == 'savedata')class="top-bg"@endif></span>
    	<a href="{{ route('security-questions.index')}}">安全口令</a>
    </li>
    <li @if($current_tab == 'user-bank-cards' || $current_tab == 'bind-card')class="active"@endif>
        <span @if($current_tab == 'user-bank-cards' || $current_tab == 'bind-card')class="top-bg"@endif></span>
    	<a href="{{ route('bank-cards.index') }}">银行卡管理</a>
    </li>

    <li @if($current_tab == '{id?}' || $current_tab == 'user-user-prize-sets')class="active"@endif>
        <span @if($current_tab == '{id?}' || $current_tab == 'user-user-prize-sets')class="top-bg"@endif></span>
    	<a href="{{ route('user-user-prize-sets.index')}}">我的奖金</a>
    </li>
     
    @endif
</ul>








