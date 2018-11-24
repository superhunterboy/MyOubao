<div class="layout-top">
	<div class="container">
		<div class="inner clearfix">

			<div class="left">
				<div class="notice">
					<a href="" target="_blank">测速中心</a>
					&nbsp;&nbsp;
					<a href="" target="_blank">手机客户端</a>
					&nbsp;&nbsp;
					<a href="/events/repairDNS/index.html" target="_blank">防劫持教程</a>
				</div>
			</div>

			<div class="right">
				<ul>
					<li class="mu mu-control">

                                                @if ($unreadMessagesNum)
                                                <a class="at message-reddot" href="#">管理中心</a>
                                                @else
                                                <a class="at" href="#">管理中心</a>
                                                @endif

                                                <div class="panel panel-manage">
                                                        <span class="p-sj"></span>
                                                        <?php $aUserCenterMenu = UserCenterMenu::getMenu(); ?>
                                                        @foreach($aUserCenterMenu as $k=>$v)
                                                         <div class="cell">
                                                            <div class="title">{{$v['title']}}</div>
                                                                @if(isset($v['children']) && $v['children'])
                                                                 <ul>
                                                                @foreach($v['children'] as $j=>$n)
                                                                    @if($n['route_name'] == 'my-overlimit-quotas.index')
                                                                        @if(Session::get('show_overlimit'))<li><a href="{{$n['url']}}">{{$n['title']}}</a></li>@endif
																	@elseif($n['route_name'] == 'user-profits.bonus')
																			 @if(Session::get('is_top_agent') ) <li><a href="{{$n['url']}}">{{$n['title']}}</a></li> @endif
                                                                     @else
                                                                        <li><a href="{{$n['url']}}">{{$n['title']}}</a></li>
                                                                     @endif
                                                                 @endforeach
                                                                 </ul>
                                                             @endif
                                                         </div>
                                                        @endforeach 
                                                </div>

					</li>
					<li class="mu mu-user">
						<span class="at">
							<i class="ico-top-user"></i>
							<span style="cursor: pointer;">余额：<span id="J-top-user-balance" class="money-num">{{ number_format($fAvailable, 2) }}</span> 元
							<a class="J-a-refreshAmount" href="javascript:void(0)"><img src="/assets/images_v2/ico_refresh.png" class="c_check1" style="vertical-align: middle;"></a>
							</span>
							<i class="ico-top-sj"></i>
						</span>

						<div class="panel">
							<span class="p-sj"></span>
							<p>你好, {{ Session::get('nickname') }}</p>

							<p>
                                                                                                                                    <?php
                                                                                                                                        $iDefaultPaymentPlatformId = isset($iDefaultPaymentPlatformId) ? $iDefaultPaymentPlatformId : 3;
                                                                                                                                        $unreadMessagesNum = isset($unreadMessagesNum) ? $unreadMessagesNum : 0;
                                                                                                                                     ?>
                                                                                                                                     <a href="{{ route('user-recharges.quick',  $iDefaultPaymentPlatformId)}}" class="go-recharge">立即充值</a>
							</p>
							<p class="fund-btns">
								<a href="{{ route('user-withdrawals.withdraw') }}">提款</a>
								<a class="last" href="/user-transfers/index/">转账</a>
							</p>
							<p class="row-logout">
								<a href="{{ route('logout') }}" class="logout"><span class="ico-logout"></span> 退出游戏</a>
							</p>
							<p class="info">
								<span>上次登录</span>
								<span>{{Session::get('last_signin_at')}}</span>
							</p>
						</div>
					</li>
                                             <?php if(empty($oUser)){ $oUser = \User::find(Session::get('user_id')); } ?>
                                             @if ($oUser->isEnableVoucher())
					<li><span class="at">礼金：{{{ number_format($oUser->voucher_amount, 2) }}}元</span></li>
                                             @endif
					<li><a class="at" href="{{ route('user-recharges.quick',  $iDefaultPaymentPlatformId)}}">充值</a></li>
					<li><a class="at" href="{{ route('user-withdrawals.withdraw') }}">提款</a></li>
					<li><a class="at at-chat" href="javascript:void(0);" onClick="openKF()">客服</a></li>
				</ul>
			</div>

		</div>
	</div>
</div>
