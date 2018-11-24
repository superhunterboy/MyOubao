<div class="area-search">
    <form action="{{ route('user-profits.index') }}" class="form-inline" method="get">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <input type="hidden" name="type" value="{{ $type }}" />
        <p class="row">
            用户名：
            <input type="text" class="input w-2" name="username" value="{{ Input::get('username') }}" />
            &nbsp;&nbsp;
            用户属性：
            <select id="J-select-user-groups" style="display:none;" name="is_agent">
                <option value="" {{ Input::get('is_agent') === '' ? 'selected' : '' }}>全部用户</option>
                <option value="2" {{ Input::get('is_agent') === '2' ? 'selected' : '' }}>自己</option>
                <option value="1" {{ Input::get('is_agent') === '1' ? 'selected' : '' }}>下级</option>
            </select>
            &nbsp;&nbsp;
            时间：
            <input type="text" name="date_from" value="{{ Input::get('date_from')?Input::get('date_from'):$dateFrom }}" class="input w-2" id="J-date-start" />
            至
            <input type="text" name="date_to" value="{{ Input::get('date_to')?Input::get('date_to'):$dateTo  }}" class="input w-2" id="J-date-end" />
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
