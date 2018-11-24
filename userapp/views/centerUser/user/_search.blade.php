<div class="area-search">
    <form action="{{ route('users.index') }}" method="get" id="J-form" autocomplete="off">
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        <p class="row">
            用户名：
            <input class="input w-2" type="text" value="{{ Input::get('username') }}" name="username" />
            
           &nbsp;&nbsp;
            注册时间：
            <input type="text" name="reg_date_from" value="{{ Input::get('reg_date_from') }}" class="input w-2" id="J-date-start" />
            至
            <input type="text" name="reg_date_to" value="{{ Input::get('reg_date_to') }}" class="input w-2" id="J-date-end" />
            &nbsp;&nbsp;
            <a name="time-choose" id="today" href="javascript:void(0);">今日</a>&nbsp;&nbsp;
            <a name="time-choose" id="week" href="javascript:void(0);">本周</a>&nbsp;&nbsp;
            <a name="time-choose" id="month" href="javascript:void(0);">本月</a>&nbsp;&nbsp;
            <a name="time-choose" id="3day" href="javascript:void(0);">近三日</a>&nbsp;&nbsp;
            <a name="time-choose" id="hmonth" href="javascript:void(0);">近半月</a>&nbsp;&nbsp;
            <a name="time-choose" id="1month" href="javascript:void(0);">近一月</a>
            &nbsp;&nbsp;
            &nbsp;&nbsp;
            <input type="submit" value=" 查 询 " class="btn" id="J-submit">
 
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