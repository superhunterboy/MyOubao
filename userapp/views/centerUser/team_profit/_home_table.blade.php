<?php $flag=false;if(count($datas)) $flag=true;?>
<table width="98%" class="table">
    <tr>
        <td style="text-align: right">注册人数：</td>
        <td style="text-align: left"><code>{{$iRegistUserCount}}</code></td>
        <td style="text-align: right">总下级人数：</td>
        <td style="text-align: left"><code>{{$iSubUserCount}}</code></td>
        <td style="text-align: right">当前在线：</td>
        <td style="text-align: left"><code>{{$iTeamOnlineCount}}</code></td>
        <td style="text-align: right"></td>
        <td style="text-align: left">
            <code></code></td>
    </tr>
    <tr>
        <td style="text-align: right">总可用金额：</td>
        <td style="text-align: left"><code>{{$aTeamAccountData[0]->sum_available}}</code>
        </td>
        <td style="text-align: right">总冻结金额：</td>
        <td style="text-align: left"><code>{{$aTeamAccountData[0]->sum_frozen}}</code>
        </td>
        <td style="text-align: right">总可提金额：</td>
        <td style="text-align: left"><code>{{$aTeamAccountData[0]->sum_withdrawable}}</code>
        </td>
        <td style="text-align: right"></td>
        <td style="text-align: left"></td>
    </tr>
    <tr>
        <td style="text-align: right">总投注总额：</td>
        <td style="text-align: left"><code>@if( $flag ){{ $datas[0]->turnover_formatted }}@else 0.00 @endif</code></td>
        <td style="text-align: right">投注盈亏总额：</td>
        <td style="text-align: left"><code>@if( $flag ){{ number_format(($datas[0]->turnover - $datas[0]->prize),4) }}@else 0.00 @endif</code></td>
        <td style="text-align: right">中奖奖金：</td>
        <td style="text-align: left"><code>@if( $flag ){{ $datas[0]->prize_formatted }}@else 0.00 @endif</code></td>
        <td style="text-align: right"></td>
        <td style="text-align: left">
            <code></code></td>
    </tr>
    <tr>
        <td style="text-align: right">总投注笔数：</td>
        <td style="text-align: left"><code>{{array_sum($aTeamProjectData)}}</code></td>
<!--        <td style="text-align: right">彩票类投注笔数：</td>
        <td style="text-align: left"><code>0</code></td>
        <td style="text-align: right">电子游戏类投注笔数：</td>
        <td style="text-align: left"><code>0</code></td>-->
        <td style="text-align: right">输笔数：</td>
        <td style="text-align: left"><code>{{(int)array_get($aTeamProjectData, Project::STATUS_LOST)}}</code></td>
        <td style="text-align: right">赢笔数：</td>
        <td style="text-align: left"><code>{{(int)array_get($aTeamProjectData, Project::STATUS_WON)}}</code></td>
        <td style="text-align: right"></td>
        <td style="text-align: left">
            <code></code></td>
    </tr>
    <tr>
        <td style="text-align: right">用户取消笔数：</td>
        <td style="text-align: left"><code>{{(int)array_get($aTeamProjectData, Project::STATUS_DROPED)}}</code></td>
        <td style="text-align: right">系统取消笔数：</td>
        <td style="text-align: left"><code>{{(int)array_get($aTeamProjectData, Project::STATUS_DROPED_BY_SYSTEM)}}</code></td>
        <td style="text-align: right">总充值金额：</td>
        <td style="text-align: left"><code>@if( $flag ){{ $datas[0]->deposit }}@else 0.00 @endif</code></td>
        <td style="text-align: right">总提现金额：</td>
        <td style="text-align: left"><code>@if( $flag ){{ $datas[0]->withdrawal }}@else 0.00 @endif</code></td>
    </tr>
    <tr>
        <th colspan="8" style="text-align: center"><h3 style="font-weight: bold;color: black">盈亏信息</h3></th>
</tr>
</table>
<table width="100%" class="table">
    <thead>
        <tr>
            <th>用户名</th>
            <th>{{ custom_order_by('turnover', '总销售金额', 'asc') }}</th>
            <th>{{ custom_order_by('prize', '总中奖金额', 'asc') }}</th>
            <th>{{ custom_order_by('commission', '总返点金额', 'asc') }}</th>
            <th>  {{ custom_order_by('bonus', '总活动金额', 'asc') }}</th>
            <th>{{ custom_order_by('profit', '总盈亏', 'asc') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            unset($search_params['parent_user']);
            $search_params['username'] = $flag ? $datas[0]->username : Session::get('username');
            ?>
            <td>@if( $flag ){{ $datas[0]->username }}@else {{$search_params['username']}} @endif</td>
            <td>@if( $flag ){{ $datas[0]->turnover_formatted }}@else 0.00 @endif</td>
            <td>@if( $flag ){{ $datas[0]->prize_formatted }}@else 0.00 @endif</td>
            <td>@if( $flag ){{ $datas[0]->commission_formatted }}@else 0.00 @endif</td>
            <td>@if( $flag ){{ $datas[0]->bonus_formatted }}@else 0.00 @endif</td>
            <td><span class="@if( $flag ){{ $datas[0]->profit > 0 ? 'c-red' : 'c-green' }}@endif">@if( $flag ){{ $datas[0]->profit_formatted }}@else 0.00 @endif</span></td>
        </tr>
        @if( !$flag )
        <tr>
            <td colspan="8">没有符合条件的记录，请更改查询条件</td>
        </tr>
        @endif
<!--        <tr>
            <th colspan="8" style="text-align: center"><h3 style="font-weight: bold;color: black">盈亏信息（电子游戏）</h3></th>
</tr>-->
    </tbody>
</table>
<!--<table class="table">
    <tr>
        <th>总投注金额</th>
        <th>总派奖金额</th>
        <th>总投注返点</th>
        <th>总返点</th>
        <th>总促销红利</th>
        <th>总盈亏</th>
    </tr>
    <tr>
        <td><code>0.0000</code></td>
        <td><code>0.0000</code></td>
        <td><code>0.0000</code></td>
        <td><code>0.0000</code></td>
        <td><code>0.0000</code></td>
        <td><code>0.0000</code></td>
    </tr>
</table>-->
