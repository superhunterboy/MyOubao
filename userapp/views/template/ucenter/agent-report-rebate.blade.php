@extends('l.home')

@section('title')
   代理返点报表
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
        <li class="current"><a href="agent-report-rebate.php"><span>代理返点报表</span></a></li>
        <li><a href="agent-report-loss.php"><span>代理盈亏报表</span></a></li>
        <li><a href="agent-report-dividend.php"><span>代理分红报表</span></a></li>
    </ul>
</div>

<div class="content">
    <div class="area-search">
        <p class="row">
            查询日期<span class="ui-prompt">（当前只显示最近14天的返点数据）</span>
            <input type="text" value="2014-06-10  00:00:00" class="input w-3" id="J-date-start">
            <input type="button" class="btn" value="搜 索" />
        </p>
    </div>
    <table width="100%" class="table">
        <thead>
            <tr>
                <th>用户名</th>
                <th>日期</th>
                <th>消费总额</th>
                <th>返点总额</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr>
            <tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr><tr>
                <td>个梵蒂冈和吃蛋黄</td>
                <td>2013-06-01</td>
                <td>88,888.00 元</td>
                <td>0.00</td>
            </tr>
        </tbody>
    </table>
    @include('w.pages')
</div>

@stop

@section('end')
<script>
(function($){
    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
})(jQuery);
</script>
@stop