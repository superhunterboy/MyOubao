@extends('l.home')

@section('title')
    代理分红报表
    @parent
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
    <style type="text/css">
        .page-content .row {
            padding: 0 0 10px 0;
            margin: 10px 0 0 0;
        }
        .page-content .row-nav {
            padding: 0 35px;
            margin-bottom: 10px;
        }
        .page-content-inner {
            box-shadow: 1px 1px 10px rgba(102, 102, 102, 0.1);
            border:0px solid #E6E6E6;
        }
        .page-content .row-nav ul{
            width: 264px;
            height: 38px;
            border-radius: 4px;
            background-color: #31CEAC;
            padding: 5px 10px;
            font-size: 13px;
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
                @include('w.uc-menu-proxy')
            </div>


            <div class="page-content-inner">
                <div class="row row-nav clearfix">
                    @include('w.uc-menu-proxy-catetory')
                </div>


                <table width="100%" class="table">
                    <thead>
                    <tr>
                        <th>分红计算周期</th>
                        <th>投注总额</th>
                        <th>派奖总额</th>
                        @if($type == 1)
                        <th>合买提成</th>
                        @endif
                        <th>返点总额</th>
                        <th>促销红利</th>
                        <th>净盈亏</th>
                        <th>累计盈亏</th>
                        <th>分红比例</th>
                        <th>总分红金额</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($datas as $data)
                        <tr>
                            <td>{{ $data->team_bonus_month }}</td>
                            <td>{{ number_format($data->team_turnover, 4) }}</td>
                            <td>{{ number_format($data->team_prize, 4) }}</td>
                            @if($type == 1)
                            <td>{{ number_format($data->group_buy_commission, 4) }}</td>
                            @endif
                            <td>{{ number_format($data->team_commission + $data->team_bet_commission, 4) }}</td>
                            <td>{{ number_format($data->team_dividend, 4) }}</td>
                            <td>{{ number_format($data->team_profit, 4) }}</td>
                            <td>{{ $data->team_accumulation_profit }}</td>
                            <td>{{ $data->team_bonus_rate }}%</td>
                            <td>{{ number_format($data->team_bonus, 4) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>



            </div>

            <div style="text-align:right">
                由于系统仍然在计算过程中，当前分红金额仅供参考，实际以到账分红金额为准。
            </div>
        </div>
    </div>



    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){


})(jQuery);
</script>
@stop


