@extends('l.home')

@section('title')
充值申请
@parent
@stop


@section ('styles')
@parent
{{ style('proxy-global') }}
{{ style('proxy') }}
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
            @include('w.uc-menu-funds')
        </div>


        <div class="page-content-inner page-content-inner-nobg">
            <div class="area-search">
                <form action="{{ route('user-recharges.index') }}" class="form-inline" method="get">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <p class="row">
                        时间：
                        <input id="J-date-start" class="input w-3" type="text" name="created_at_from" value="{{ Input::get('request_time_from') }}" />
                        至
                        <input id="J-date-end" class="input w-3" type="text" name="created_at_to" value="{{ Input::get('request_time_to') }}" />

                        &nbsp;&nbsp;
                        <a name="time-choose" id="today" href="javascript:void(0);">今日</a>
                        <a name="time-choose" id="week" href="javascript:void(0);">本周</a>
                        <a name="time-choose" id="month" href="javascript:void(0);">本月</a>
                        <a name="time-choose" id="3day" href="javascript:void(0);">近三日</a>
                        <a name="time-choose" id="hmonth" href="javascript:void(0);">近半月</a>
                        <a name="time-choose" id="1month" href="javascript:void(0);">近一月</a>
                        &nbsp;&nbsp;
                        <input class="btn" type="submit" value=" 搜 索 " />




                    </p>

                </form>
            </div>

            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th width="300">编号</th>
                        <th width="200">充值时间</th>
                        <th>到账时间</th>
                        <th>充值渠道</th>
                        <th>申请金额</th>
                        <th>实际充值</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $fTotalAmount = $fTotalRealAmount = $fTotalFee = 0; ?>

                    @if(count($datas))

                    @foreach ($datas as $key => $data)
                    <tr class="withdrawalRow">
                        <td>{{ $data->order_no }}</td>
                        <td>
                            {{ $data->created_at }}
                        </td>
                        <td>{{ $data->pay_time }}</td>
                        <td>@if(isset($aPayMode[$data->pay_mode])){{ $aPayMode[$data->pay_mode] }}@endif</td>
                        <td><span class="c-green amount">{{ $data->amount }}</span></td>
                        <td><span class="c-green amount"> {{ $data->real_amount }}</span></td>
                        <td>{{ $data->formatted_status }}</td>
                    </tr>
                    <?php
                    $fTotalAmount += $data->amount;
                    $fTotalRealAmount += $data->real_amount;
                    $fTotalFee += $data->fee;
                    ?>
                    @endforeach

                    @else
                    <tr><td colspan="5">没有符合条件的记录，请更改查询条件</td></tr>
                    @endif



                </tbody>
                <tfoot>
                    <tr>
                        <td>小结</td>
                        <td>本页资金变动<br /></td>
                        <td></td>
                        <td></td>
                        <td>{{  number_format($fTotalAmount, 2) }}</td>
                        <td>{{  number_format($fTotalRealAmount, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
        </div>
        @stop

        @section('end')
        @parent
        <script>
            (function ($) {

                new bomao.Select({realDom: '#J-select-recharge', cls: 'w-2'});

                $('#J-date-start').focus(function () {
                    (new bomao.DatePicker({input: '#J-date-start', isShowTime: true, startYear: 2013})).show();
                });
                $('#J-date-end').focus(function () {
                    (new bomao.DatePicker({input: '#J-date-end', isShowTime: true, startYear: 2013})).show();
                });


            })(jQuery);
        </script>
        <script language="javascript">

            o = document.getElementsByName('time-choose');
            l = o.length;
            for (i = 0; i < l; i++) {
                o[i].onclick = function () {
                    switch (this.id) {
                        case 'today':
                            startDate = now;
                            endDate = now;
                            break;
                        case 'week':
                            startDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);
                            endDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek));
                            break;
                        case 'month':
                            startDate = new Date(nowYear, nowMonth, 1);
                            endDate = new Date(nowYear, nowMonth, getMonthDays(nowMonth));
                            break;
                        case '3day':
                            startDate = new Date(now.getTime() - 3 * 24 * 3600 * 1000);
                            endDate = now;
                            break;
                        case 'hmonth':
                            startDate = new Date(now.getTime() - 15 * 24 * 3600 * 1000);
                            endDate = now;
                            break;
                        case '1month':
                            startDate = new Date(now.getTime() - 30 * 24 * 3600 * 1000);
                            endDate = now;
                            break;
                    }
                    document.getElementById('J-date-start').value = formatDate(startDate) + ' 00:00:00';
                    document.getElementById('J-date-end').value = formatDate(endDate) + ' 23:59:59';
                }
            }
            var now = new Date(); //当前日期
            var nowDayOfWeek = now.getDay(); //今天本周的第几天
            var nowDay = now.getDate(); //当前日
            var nowMonth = now.getMonth(); //当前月
            var nowYear = now.getYear(); //当前年
            nowYear += (nowYear < 2000) ? 1900 : 0; //
            var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek);

            function formatDate(date) {
                var myyear = date.getFullYear();
                var mymonth = date.getMonth() + 1;
                var myweekday = date.getDate();

                if (mymonth < 10) {
                    mymonth = "0" + mymonth;
                }
                if (myweekday < 10) {
                    myweekday = "0" + myweekday;
                }
                return (myyear + "-" + mymonth + "-" + myweekday);
            }
            function getMonthDays(myMonth) {
                var monthStartDate = new Date(nowYear, myMonth, 1);
                var monthEndDate = new Date(nowYear, myMonth + 1, 1);
                var days = (monthEndDate - monthStartDate) / (1000 * 60 * 60 * 24);
                return days;
            }
        </script>
        @stop