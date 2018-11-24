@extends('l.ahome')

@section('title') 
    代理分红报表
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
@stop




@section ('container')

    @include('w.header-v3')


    <div class="banner">
        <img src="assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_33 clearfix">
            <div class="page-content-inner"> 

            <div class="row-tip">
                2015-05 月分红 <span class="red">正在结算中…</span>  预计于5号前发放至您的账户。 请耐心等待
            </div>


            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>分红时间</th>
                        <th>当前销售总额</th>
                        <th>当前分红比例</th>
                        <th>输额总计</th>
                        <th>分红金额</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($datas as $data)
                    <tr>
                        <td>{{ $data->username }}</td>
                        <td>{{ $data->verified_at }}</td>
                        <td>{{ $data->turnover }}</td>
                        <td>{{ $data->rate }}</td>
                        <td>{{ number_format($data->direct_profit, 4) }}</td>
                        <td>{{ number_format($data->bonus, 4) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row-tip row-tip-gray">
                由于系统仍然在计算过程中，当前分红金额仅供参考，实际以到账分红金额为准。
            </div>


            </div>
        </div>
    </div>



    @include('w.footer-v3')
@stop





