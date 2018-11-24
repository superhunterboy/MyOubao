@extends('l.home')

@section('title')
            盈亏报表
@parent
@stop

@section('scripts')
@parent
    {{ script('jquery.jscrollpane')}}
    {{ script('bomao.DatePicker')}}
@stop


@section ('main')
<div class="nav-bg">
            <div class="title-normal">
                盈亏报表
            </div>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    游戏时间：<input id="J-date-start" class="input w-3" type="text" value="2014-06-10  00:00:00" /> 至 <input id="J-date-end" class="input w-3" type="text" value="2014-06-11  00:00:00" />
                    &nbsp;&nbsp;
                    <input class="btn" type="button" value=" 搜 索 " />
                </p>
            </div>


            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th>时间</th>
                        <th>充值总额</th>
                        <th>提现总额</th>
                        <th>投注总额</th>
                        <th>中奖总额</th>
                        <th>游戏总盈亏</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tr>
                        <td>2014-05-30</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td>800.00</td>
                        <td><span class="c-green">+ 10.00</span></td>
                    </tr>
                    <tfoot>
                        <tr>
                            <td>小结</td>
                            <td><span class="c-red">- 98.00</span></td>
                            <td><span class="c-red">- 98.00</span></td>
                            <td><span class="c-red">- 98.00</span></td>
                            <td><span class="c-red">- 98.00</span></td>
                            <td><span class="c-red">- 98.00</span></td>
                        </tr>
                    </tfoot>
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
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });
})(jQuery);
</script>
@stop