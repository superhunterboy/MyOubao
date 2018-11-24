



<ul class="list clearfix">
	<li @if($current_tab == 'user-transactions' || $current_tab == '{id?}')class="active"@endif>
		<span @if($current_tab == 'user-transactions' || $current_tab == '{id?}')class="top-bg"@endif></span>
		<a href="{{ route('user-transactions.index') }}">账变记录</a>
	</li>

	<li @if($current_tab == 'mydeposit')class="active"@endif>
		<span @if($current_tab == 'mydeposit')class="top-bg"@endif></span>
		<a href="{{ route('user-transactions.mydeposit',Session::get('user_id')) }}">我的充值</a>
	</li>

	<li @if($current_tab == 'deposit')class="active"@endif>
		<span @if($current_tab == 'deposit')class="top-bg"@endif></span>
		<a href="{{ route('user-recharges.index') }}">充值申请</a>
	</li>

	<li @if($current_tab == 'mywithdraw')class="active"@endif>
		<span @if($current_tab == 'mywithdraw')class="top-bg"@endif></span>
		<a href="{{ route('user-transactions.mywithdraw',Session::get('user_id')) }}">提现记录</a>
	</li>

	<li @if($current_tab == 'user-withdrawals')class="active"@endif>
		<span @if($current_tab == 'user-withdrawals')class="top-bg"@endif></span>
		<a href="{{ route('user-withdrawals.index') }}"><span>提现申请</span></a>
	</li>

	<li @if($current_tab == 'mytransfer')class="active"@endif>
		<span @if($current_tab == 'mytransfer')class="top-bg"@endif></span>
		<a href="{{ route('user-transactions.mytransfer',Session::get('user_id')) }}">转账记录</a>
	</li>
</ul>









