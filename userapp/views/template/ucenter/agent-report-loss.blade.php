@extends('l.home')

@section('title')
   代理盈亏报表
@parent
@stop

@section('scripts')
@parent
    {{ script('jscroll')}}
    {{ script('datePicker')}}
@stop

@section('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal"></div>
            <ul class="tab-title clearfix">
                <li><a href="agent-report-rebate.php"><span>代理返点报表</span></a></li>
                <li class="current"><a href="agent-report-loss.php"><span>代理盈亏报表</span></a></li>
                <li><a href="agent-report-dividend.php"><span>代理分红报表</span></a></li>
            </ul>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    查询日期：
                    <input type="text" value="2014-06-10  00:00:00" class="input w-3" id="J-date-start">&nbsp;&nbsp;
                    客户类型：
                    <select style="display:none;" id="J-select-custom-type">
                        <option selected="selected" value="0">全部客户</option>
                        <option value="1">重庆时时彩</option>
                        <option value="2">江西时时彩</option>
                        <option value="3">黑龙江时时彩</option>
                        <option value="4">新疆时时彩</option>
                        <option value="5">上海时时乐</option>
                        <option value="6">乐利时时彩</option>
                        <option value="7">天津时时彩</option>
                        <option value="8">吉利分分彩</option>
                        <option value="9">顺利秒秒彩</option>
                    </select>&nbsp;&nbsp;
                    用户名：
                    <input type="text" value="" class="input w-2">&nbsp;&nbsp;
                    <input type="button" class="btn" value="搜 索" />
                </p>
            </div>
            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th>用户名</th>
                        <th>日期</th>
                        <th>充值总额</th>
                        <th>提现总额</th>
                        <th>销售总额</th>
                        <th>返点总额</th>
                        <th>中奖总额</th>
                        <th>游戏总盈亏</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-green">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-red">+170.00</span></td>
                    </tr>
                        <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-green">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-red">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-green">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-red">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-green">+170.00</span></td>
                    </tr>
                    <tr>
                        <td>个梵蒂冈和吃蛋黄</td>
                        <td>代理</td>
                        <td>88,888.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td>80.00</td>
                        <td>80.00</td>
                        <td><span class="c-red">+170.00</span></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>本页合计&gt;</td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                        <td><span class="c-red">- 98.00</span></td>
                    </tr>
                </tfoot>
            </table>

            @include('w.pages')
        </div>
@stop

@section('end')
<script>
(function($){
    new bomao.Select({realDom:'#J-select-custom-type',cls:'w-2'});
    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
})(jQuery);
</script>
@stop