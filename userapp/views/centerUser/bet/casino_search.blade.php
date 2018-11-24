<style>
    .select-method-2 {display:none}
</style>


<div class="area-search">

    <form action="{{ route('projects.index') }}" class="form-inline" method="get">
        <input type="hidden" name="jc_type" value="<?php echo $projectType?>"/>
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">

            游戏时间：<input id="J-date-start" class="input w-3" type="text" name="bought_at_from" value="{{ $bought_at_from }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="bought_at_to" value="{{ $bought_at_to }}" />
            &nbsp;&nbsp;&nbsp;&nbsp;
            <a name="time-choose" id="today" href="javascript:void(0);">今日</a>
            <a name="time-choose" id="week" href="javascript:void(0);">本周</a>
            <a name="time-choose" id="month" href="javascript:void(0);">本月</a>
            <a name="time-choose" id="3day" href="javascript:void(0);">近三日</a>
            <a name="time-choose" id="hmonth" href="javascript:void(0);">近半月</a>
            <a name="time-choose" id="1month" href="javascript:void(0);">近一月</a>
            <br/> <br/>

            注单编号：<input class="input w-3" type="text" value="{{ Input::get('serial_number') }}" name="serial_number" />
            奖期编号：<input class="input w-3" type="text" value="{{ Input::get('issue') }}" name="issue" />

            用户名：
            <input class="input w-3" type="text" value="{{ $susername }}" name="username" />
        </p>
        <p class="row">
            @include('widgets.casino-group-ways')
            <br/> <br/>
            状态：&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="status_0"  {{ Input::get('status_0') == '0' ? 'checked' : '' }} value="0">有效投注（未开奖）&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="status_1"  {{ Input::get('status_1') == '1' ? 'checked' : '' }} value="1">已撤单&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="status_2"  {{ Input::get('status_2') == '2' ? 'checked' : '' }} value="2">未中奖&nbsp;&nbsp;&nbsp;&nbsp;
            <input type="checkbox" name="status_3"  {{ Input::get('status_3') == '3' ? 'checked' : '' }} value="3">已中奖&nbsp;&nbsp;&nbsp;&nbsp;
            {{--
            @if (Session::get('is_agent'))
            游戏用户：<input class="input w-3" type="text" name="username" value="{{ Input::get('username') }}" />
            &nbsp;&nbsp;
            @endif--}}
            <input type="submit" value="搜 索" class="btn" id="J-submit">
        </p>


    </form>
</div>