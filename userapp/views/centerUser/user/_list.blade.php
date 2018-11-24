<table width="100%" class="table table-toggle" id="J-table-users">
    <thead>
        <tr>
            <th rowspan="2">用户名</th>
            <th rowspan="2">在线状态</th>
            @if(empty(Input::get('parent_id')))
	            @if(empty(Input::get('_token')))
	            <th rowspan="2">QQ</th>
	            @endif
            @endif
            <th rowspan="2">彩票奖金组</th>
            <th colspan="3" style="border-bottom:none;">电子娱乐</th>
            <th rowspan="2">下级人数</th>
            <th rowspan="2">团队余额</th>
            <th rowspan="2">操作</th>
        </tr>
        <tr>
            <th>骰宝</th>
            <th>百家乐</th>
            <th style="border-right: 1px solid #DCDCDC;">龙虎斗</th>
        </tr>
    </thead>
    <tbody>
        @foreach($datas as $oUser)
        <tr>
            <td>
                <a class="ct-username" data-reg="{{ formatShortDate($oUser->register_at) }}" data-login="{{ formatShortDate($oUser->signin_at) }}" href="/users?parent_id={{$oUser->id}}">{{ $oUser->username }}</a>
            </td>
            <td>
                @if(UserOnline::isOnline($oUser->id))
                    <span class="tb-inner-online"></span>
                @else
                    <span class="tb-inner-offline"></span>
                @endif
            </td>
            @if(empty(Input::get('parent_id')))
            	@if(empty(Input::get('_token')))
	            <td>{{$oUser->qq}}</td>
	            @endif
            @endif
            @foreach(SeriesSet::$aSequenceIds as $iSeriesIds)
            
            <td>
                @if($iSeriesIds == SeriesSet::ID_LOTTERY)    {{$oUser->prize_group}} (    @endif

                @if ($oCommission = UserCommissionSet::getUserCommissionSet($oUser->id, $iSeriesIds))
                    {{$oCommission->commission_rate}}
                @else
                    0.00
                @endif
                    %

                @if($iSeriesIds == SeriesSet::ID_LOTTERY)    )    @endif
            </td>
            @endforeach

            <td>{{$oUser->children()->count()}}</td>
            <td>{{ number_format($oUser->getGroupAccountSum(), 2)}}</td>

{{--            <?php $aUserProfit = UserProfit::getCurrentMonthData($oUser->id); ?>
            <td>{{ $aUserProfit['team_turnover'] }}</td>
            <td>{{ $aUserProfit['team_profit'] }}</td>--}}

            <td>
                <a class="tb-inner-btn" href="{{ route('user-user-prize-sets.set-prize-set', $oUser->id) }}">奖金组/返点</a>
                <a class="tb-inner-btn" href="{{ route('user-transfers.index', $oUser->id) }}">转账</a>

                @if($oUser->parent_id == Session::get('user_id'))
                    @if($user = User::find($oUser->id))
                        @if($user->hasOverlimitQuotaOrNot())
                            <a class="tb-inner-btn tb-inner-btn-highlight" href="{{ route('my-overlimit-quotas.index', $oUser->id) }}">配额</a>
                        @endif
                    @endif
                @endif

            </td>

        </tr>
        @endforeach
    </tbody>
</table>