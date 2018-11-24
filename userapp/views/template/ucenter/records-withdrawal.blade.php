@extends('l.home')

@section('title')
            我的提现
@parent
@stop

@section('scripts')
@parent
    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
    {{ script('datePicker')}}
@stop


@section ('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal">
                资金明细
            </div>
            <ul class="tab-title">
                <li><a href="records-bill.php"><span>账变记录</span></a></li>
                <li><a href="records-recharge.php"><span>我的充值</span></a></li>
                <li class="current"><a href="records-withdrawal.php"><span>我的提现</span></a></li>
            </ul>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    时间：<input id="J-date-start" class="input w-3" type="text" value="2014-06-10  00:00:00" /> 至 <input id="J-date-end" class="input w-3" type="text" value="2014-06-11  00:00:00" />
                </p>
                <p class="row">
                    类型：<select id="J-select-recharge" style="display:none;">
                              <option selected="selected" value="0">所有类型</option>
                              <option value="1">平台提现申请</option>
                              <option value="2">本人发起提现</option>
                        </select>

                    &nbsp;
                    游戏用户：<input class="input w-3" type="text" value="" />
                    &nbsp;&nbsp;
                    <input class="btn" type="button" value=" 搜 索 " />
                </p>
            </div>

            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th>账变编号</th>
                        <th>时间</th>
                        <th>类型</th>
                        <th>收入</th>
                        <th>支出</th>
                        <th>余额</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30
                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30

                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30
                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30

                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30
                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30

                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30
                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30

                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30
                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                    <tr>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>
                            2014-05-30

                            13:32:03
                        </td>
                        <td>人工充值</td>
                        <td><span class="c-green">+ 10.00</span></td>
                        <td><span class="c-red">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>成功</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>小结</td>
                        <td>本页资金变动<br /><span class="c-red">- 98.00</span></td>
                        <td></td>
                        <td>+ 98.00</td>
                        <td>- 98.00</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

          @include('w.pages')
        </div>
@stop

@section('end')
<script>
(function($){

    new bomao.Select({realDom:'#J-select-recharge',cls:'w-2'});

    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });


})(jQuery);
</script>
@stop