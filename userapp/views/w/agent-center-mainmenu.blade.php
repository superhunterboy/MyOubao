<div class="nav">
            <ul class="menu clearfix">
                <li class="first">
                    <div class="title"><a href="/">代理首页</a></div>
                </li>
                <li>
                    <div class="title"><a href="{{ route('users.index') }}">团队管理<i class="sj"></i></a></div>
                    <ul class="child">
                        <li><a href="{{ route('users.accurate-create') }}"><i class="fa fa-user-plus"></i>&nbsp;&nbsp;新增用户</a></li>
                        <li><a href="{{ route('user-links.create') }}#list"><i class="fa fa-link"></i>&nbsp;&nbsp;开户链接</a></li>
                        <li class="last"><a href="{{ route('users.index') }}"><i class="fa fa-users"></i>&nbsp;&nbsp;管理团队</a></li>
                        @if(Session::get('show_overlimit'))
                        <li class="last"><a href="{{ route('my-overlimit-quotas.index') }}"><i class="fa fa-diamond"></i>&nbsp;&nbsp;高点配额</a></li>
                        @endif
                    </ul>
                </li>
                <li>
                    <div class="title"><a href="{{ route('user-profits.index') }}">盈亏报表<i class="sj"></i></a></div>
                  @if(Session::get('is_top_agent'))
                    <ul class="child">
                        <li><a href="{{ route('user-profits.index') }}"><i class="fa fa-building-o"></i>&nbsp;&nbsp;盈亏报表</a></li>
                        
                        <li class="last"><a href="{{ route('user-profits.bonus') }}"><i class="fa fa-cutlery"></i>&nbsp;&nbsp;分红报表</a></li>
                    </ul>
                    @endif
                </li>
                <li>
                    <div class="title"><a href="{{ route('user-transactions.index') }}">资金明细<i class="sj"></i></a></div>
                    <ul class="child">
                        <li><a href="{{ route('user-transactions.index') }}"><i class="fa fa-bars"></i>&nbsp;&nbsp;账变记录</a></li>
                        <li class="last"><a href="{{ route('user-transactions.mywithdraw',Session::get('user_id')) }}"><i class="fa fa-check-circle-o"></i>&nbsp;&nbsp;提现记录</a></li>
                        <li class="last"><a href="{{ route('user-withdrawals.index') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;提现申请</a></li>
                        <li class="last"><a href="{{ route('user-transactions.mytransfer',Session::get('user_id')) }}"><i class="fa fa-exchange"></i>&nbsp;&nbsp;转账记录</a></li>
                    </ul>
                </li>
                <li>
                    <div class="title"><a href="{{ route('users.password-management')}}">账户资料<i class="sj"></i></a></div>
                    <ul class="child">
                        <li><a href="{{ route('users.password-management')}}"><i class="fa fa-key"></i>&nbsp;&nbsp;密码管理</a></li>
                        <li><a href="{{ route('bank-cards.index') }}"><i class="fa fa-credit-card"></i>&nbsp;&nbsp;银行卡管理</a></li>
                        <li class="last"><a href="{{ route('user-user-prize-sets.game-prize-set')}}"><i class="fa fa-usd"></i>&nbsp;&nbsp;&nbsp;&nbsp;我的奖金</a></li>
                    </ul>
                </li>
            </ul>

            <div class="user-money">
                <span class="money-text">可用余额：<span class="num"><i class="fa fa-cny"></i>&nbsp;{{ number_format($fAvailable,2) }}</span></span>
                <a class="btn" href="{{ route('user-withdrawals.withdraw')}}">提 现</a>
                <a class="btn" style="  margin-left: 7px;" href="{{ route('user-transfers.index')}}">转 账</a>
            </div>
        </div>