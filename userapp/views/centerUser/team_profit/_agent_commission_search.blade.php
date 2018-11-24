<div class="area-search">
    <form action="{{ route('user-profits.commission') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">
            查询日期：
            <input type="text" name="date" value="{{ Input::get('date') ? Input::get('date') : Carbon::yesterday()->toDateString() }}" class="input w-3" id="J-date-start">
            &nbsp;&nbsp;
            用户属性：
            <select id="J-select-user-groups" style="display:none;" name="is_agent">
                <option value="" {{ Input::get('is_agent') === '' ? 'selected' : '' }}>全部用户</option>
                <option value="1" {{ Input::get('is_agent') === '1' ? 'selected' : '' }}>代理</option>
                <option value="0" {{ Input::get('is_agent') === '0' ? 'selected' : '' }}>玩家</option>
            </select>

            &nbsp;&nbsp;&nbsp;&nbsp;用户名：
            <input type="text" class="input w-2" name="username" value="{{ Input::get('username') }}" />
            <input type="submit" value="搜 索" class="btn" id="J-submit">
        </p>
        <p class="row">
            <!--<a href="#" target="_blank">报表下载</a>-->
        </p>
    </form>
</div>