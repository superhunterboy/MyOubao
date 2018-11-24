<div class="area-search">
    <form action="{{ route('user-withdrawals.index') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">
            时间：<input id="J-date-start" class="input w-3" type="text" name="request_time_from" value="{{ Input::get('request_time_from') }}" /> 至 <input id="J-date-end" class="input w-3" type="text" name="request_time_to" value="{{ Input::get('request_time_to') }}" />
            &nbsp;
            状态：
            <select id="J-select-status" name="status">
                <option value>所有状态</option>
                @foreach(Withdrawal::$validStatuses as $iStatus => $sStatus)
                    <?php if (in_array($iStatus, [6,7,8])) continue;?>
                    <option value="{{$iStatus}}" {{Input::get('status') == $iStatus ? 'selected="selected"' : ''}}>{{__('_withdrawal.' . strtolower(Str::slug($sStatus)))}}</option>

                @endforeach
            </select>
            &nbsp;&nbsp;
            <a name="time-choose" id="today" href="javascript:void(0);">今日</a>
            <a name="time-choose" id="week" href="javascript:void(0);">本周</a>
            <a name="time-choose" id="month" href="javascript:void(0);">本月</a>
            <a name="time-choose" id="3day" href="javascript:void(0);">近三日</a>
            <a name="time-choose" id="hmonth" href="javascript:void(0);">近半月</a>
            <a name="time-choose" id="1month" href="javascript:void(0);">近一月</a>
            &nbsp;&nbsp;
            <input type="submit" value="搜 索" class="btn" id="J-submit">
        </p>

    </form>
</div>
<script language="javascript">

    o = document.getElementsByName('time-choose');
    l = o.length;
    for(i=0;i<l;i++){
        o[i].onclick=function(){
            switch(this.id){
                case 'today':
                    startDate = now;
                    endDate =   now;
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
                    startDate =new Date(now.getTime() -3*24*3600*1000);
                    endDate =   now;
                    break;
                case 'hmonth':
                    startDate =new Date(now.getTime() -15*24*3600*1000);
                    endDate =   now;
                    break;
                case '1month':
                    startDate =new Date(now.getTime() -30*24*3600*1000);
                    endDate =   now;
                    break;
            }
            document.getElementById('J-date-start').value=formatDate(startDate)+ ' 00:00:00';
            document.getElementById('J-date-end').value=formatDate(endDate)+ ' 23:59:59';
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
        var mymonth = date.getMonth()+1;
        var myweekday = date.getDate();

        if(mymonth < 10){
            mymonth = "0" + mymonth;
        }
        if(myweekday < 10){
            myweekday = "0" + myweekday;
        }
        return (myyear+"-"+mymonth + "-" + myweekday);
    }
    function getMonthDays(myMonth){
        var monthStartDate = new Date(nowYear, myMonth, 1);
        var monthEndDate = new Date(nowYear, myMonth + 1, 1);
        var days = (monthEndDate - monthStartDate)/(1000 * 60 * 60 * 24);
        return days;
    }
</script>