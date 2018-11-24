<div class="area-search">

    <form action="{{ route('projects.index') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">
            创建时间：<input id="J-date-start" class="input w-3" type="text" name="bought_at_from" value="{{ Input::get('bought_at_from') }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="bought_at_to" value="{{ Input::get('bought_at_to') }}" />
            &nbsp;&nbsp;
            <input class="input w-3" type="text" value="{{ Input::get('url') }}" name="url" />
        </p>
        <p class="row">
            <input type="submit" value="搜 索" class="btn" id="J-submit">
        </p>
    </form>
</div>