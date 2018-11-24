<div class="area-search">
	<form action="{{ route('user-profits.index') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
	    <p class="row">
	        游戏时间：<input id="J-date-start" class="input w-3" type="text" name="date_from" value="{{ Input::get('date_from', '') }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="date_to" value="{{ Input::get('date_to', '') }}" />
	        &nbsp;&nbsp;
	        <input type="submit" value="搜 索" class="btn" id="J-submit">
	    </p>
    </form>
</div>
