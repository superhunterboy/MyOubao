<div class="area-search">

    <form action="{{ route('traces.index') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">
            游戏时间：<input id="J-date-start" class="input w-3" type="text" name="bought_at_from" value="{{ $bought_at_from }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="bought_at_to" value="{{ $bought_at_to }}" />
            &nbsp;&nbsp;
            <select id="J-select-issue" style="display:none;" name="number_type">
                <option value="serial_number" {{ Input::get('number_type') == 'serial_number' ? 'selected' : '' }}>追号编号</option>
                <option value="start_issue" {{ Input::get('number_type') == 'start_issue' ? 'selected' : '' }}>奖期编号</option>
            </select>
            <input class="input w-3" type="text" value="{{ Input::get('number_value') }}" name="number_value" />
        </p>
        <p class="row">
            @include('widgets.lottery-group-ways')
            <!-- 游戏名称：<select id="J-select-game-type" style="display:none;">
                    <option value="0" selected="selected">所有游戏</option>
                    <option value="1">重庆时时彩</option>
                    <option value="2">江西时时彩</option>
                    <option value="3">黑龙江时时彩</option>
                    <option value="4">新疆时时彩</option>
                    <option value="5">上海时时乐</option>
                    <option value="6">乐利时时彩</option>
                    <option value="7">天津时时彩</option>
                    <option value="8">吉利分分彩</option>
                    <option value="9">顺利秒秒彩</option>
                </select>
            &nbsp;&nbsp;
            玩法群：
                <select id="J-select-method-group" style="display:none;">
                    <option value="0" selected="selected">所有玩法群</option>
                </select>
            &nbsp;&nbsp;
            玩法：
                <select id="J-select-method" style="display:none;">
                    <option value="0" selected="selected">所有玩法</option>
                </select> -->



            @if (Session::get('is_agent'))
            游戏用户：<input class="input w-3" type="text" name="username" value="{{ Input::get('username') }}" />
            &nbsp;&nbsp;
            @endif
            <input type="submit" value="搜 索" class="btn" id="J-submit">


                

        </p>
    </form>
</div>