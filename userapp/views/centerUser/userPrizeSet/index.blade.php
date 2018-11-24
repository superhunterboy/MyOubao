@extends('l.home')

@section('title')
    我的奖金
    @parent
@stop

<!-- {{ style('ucenter')}} -->
@section ('styles')
    @parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
        .page-content .row {
            padding: 20px 0 10px 0;
            margin: 0;
        }
        .page-content-inner {
            box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
            border: 0px solid #CCC;
            background-color: #FFF;
        }
        .bonusgroup-title {
            border: none;
        }
        .table td {
            border-right: 1px solid #E6E6E6;
        }
        .table tbody tr:hover td {
            background: #FFF;
        }
    </style>
@stop




@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>



    <div class="page-content">
        <div class="g_main clearfix">

            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-user')
            </div>
            <div class="page-content-inner">
                <table width="100%" class="table" style="border:none;">
                    <thead>
                    <tr>
                        <th width="150"></th>
                        <th width="200">类型</th>
                        <th width="200">奖金组</th>
                        <th class="last"><span class="tip">玩法</span></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="6">彩票</td>
                            <td>时时彩</td>
                            <td>{{$iPrizeGroup}}（返点{{$fCommissionRate}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 1) }}">查看详情</a></td>
                        </tr>
                        <tr>
                            <td>11选5</td>
                            <td>{{$iPrizeGroup - 20}}（返点{{$fCommissionRateFor11Y5}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 2) }}">查看详情</a></td>
                        </tr>
                        <tr>
                            <td>低频彩</td>
                            <td>{{$iPrizeGroup - 30}}（返点{{$fCommissionRateForDpc}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 13) }}">查看详情</a></td>
                        </tr>
                        <tr>
                            <td>快3</td>
                            <td>{{$iPrizeGroup}}（返点{{$fCommissionRate}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 22) }}">查看详情</a></td>
                        </tr>
                        <tr>
                            <td>PK10</td>
                            <td>{{$iPrizeGroup}}（返点{{$fCommissionRate}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 53) }}">查看详情</a></td>
                        </tr>
{{--                        <tr>
                            <td>幸运28</td>
                            <td>{{$iPrizeGroup}}（返点{{$fCommissionRate}}%）</td>
                            <td><a target="_blank" href="{{ route('user-user-prize-sets.game-prize-set', 54) }}">查看详情</a></td>
                        </tr>--}}
                    </tbody>
                </table>
{{--                <table width="100%" class="table" style="border:none;">
                    <thead>
                    <tr>
                        <th width="150"></th>
                        <th width="200">类型</th>
                        <th width="200">返点</th>
                        <th class="last"><span class="tip">玩法</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width="150" rowspan="2">体育</td>
                        <td width="200" rowspan="2">足球</td>
                        <td width="200">{{$oUserSingleCommissionSets ? $oUserSingleCommissionSets->commission_rate : ''}}%</td>
                        <td>单关</td>
                    </tr>
                    <tr>
                        <td>{{$oUserMixCommissionSets ? $oUserMixCommissionSets->commission_rate : ''}}%</td>
                        <td>串关</td>
                    </tr>
                    </tbody>
                </table>--}}
                <table width="100%" class="table" style="border:none;">
                    <thead>
                    <tr>
                        <th></th>
                        <th>类型</th>
                        <th>返点</th>
                        <th class="last"><span class="tip">玩法</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td width="150" rowspan="3">电子娱乐</td>
                        <td width="200">骰宝</td>
                        <td width="200">{{$oUserDiceCommissionSets ? $oUserDiceCommissionSets->commission_rate : ''}}%</td>
                        <td>--</td>
                    </tr>
                    <tr>
                        <td>龙虎斗</td>
                        <td>{{$oUserDiceCommissionSets ? $oUserLhdCommissionSets->commission_rate : ''}}%</td>
                        <td>--</td>
                    </tr>
                    <tr>
                        <td>百家乐</td>
                        <td>{{$oUserBjlCommissionSets ? $oUserBjlCommissionSets->commission_rate : ''}}%</td>
                        <td>--</td>
                    </tr>
                    </tbody>
                </table>
            </div>



        </div>
    </div>



    @include('w.footer')
@stop






