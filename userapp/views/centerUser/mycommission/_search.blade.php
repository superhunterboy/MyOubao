<div class="area-search">
    @if($reportName=='transaction')<form action="{{ route('user-transactions.index') }}" class="form-inline" method="get">@endif
        @if($reportName=='deposit')<form action="{{ route('user-transactions.mydeposit',Session::get('user_id')) }}" class="form-inline" method="get">@endif
            @if($reportName=='withdraw')<form action="{{ route('user-transactions.mywithdraw',Session::get('user_id')) }}" class="form-inline" method="get">@endif
            @if($reportName=='transfer')<form action="{{ route('user-transactions.mytransfer',Session::get('user_id')) }}" class="form-inline" method="get">@endif
            @if($reportName=='commission')<form action="{{ route('user-transactions.mycommission',Session::get('user_id')) }}" class="form-inline" method="get">@endif
                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                <p class="row" style="position:relative">
                    时间：<input id="J-date-start" class="input w-3" type="text" name="created_at_from" value="{{ Input::get('created_at_from')?Input::get('created_at_from'):(isset($created_at_from)?$created_at_from:'') }}" />
                    至 <input id="J-date-end" class="input w-3" type="text" name="created_at_to" value="{{ Input::get('created_at_to')?Input::get('created_at_to'):(isset($created_at_to)?$created_at_to:'') }}" />
                    &nbsp;&nbsp;
                    @if($reportName=='transaction')
                        <select id="J-select-issue" style="display:none;" name="number_type">
                            <option value="serial_number" {{ Input::get('number_type') == 'serial_number' ? 'selected' : '' }}>账变编号</option>
                            <option value="project_no" {{ Input::get('number_type') == 'project_no' ? 'selected' : '' }}>注单编号</option>
                            <option value="trace_id" {{ Input::get('number_type') == 'trace_id' ? 'selected' : '' }}>追号编号</option>
                            <option value="issue" {{ Input::get('number_type') == 'issue' ? 'selected' : '' }}>奖期编号</option>
                        </select>
                        <input class="input w-3" type="text" name="number_value" value="{{ Input::get('number_value') }}" />
                    @endif

                    @if (Session::get('is_agent') && ($reportName=='transaction' || $reportName=='transfer'))
                        用户：<input class="input w-3" type="text" name="username" value="{{ isset($sJumpUsername) ? $sJumpUsername : Input::get('username')  }}" />
                        &nbsp;&nbsp;
                    @endif
                    @if($reportName=='commission')
                 
                    <select id="J-select-bill-type" style="display:none;" name="type_id">
                            <option value="">所有类型</option>
                          
                            @foreach ($aTransactionTypes as $oTransactionType)
                                @if($reportName=='transaction' || in_array($oTransactionType->id, $depositTransactionType))
                                    <option value="{{ $oTransactionType->id }}" {{ Input::get('type_id') == $oTransactionType->id ? 'selected' : '' }}>{{ $oTransactionType->friendly_description }}</option>
                                @endif
                            @endforeach
                        </select>
                    
                    @endif
                    &nbsp;&nbsp;
                    <a name="time-choose" id="today" href="javascript:void(0);">今日</a>
                    <a name="time-choose" id="week" href="javascript:void(0);">本周</a>
                    <a name="time-choose" id="month" href="javascript:void(0);">本月</a>
                    <a name="time-choose" id="3day" href="javascript:void(0);">近三日</a>
                    <a name="time-choose" id="hmonth" href="javascript:void(0);">近半月</a>
                    <a name="time-choose" id="1month" href="javascript:void(0);">近一月</a>
                    &nbsp;&nbsp;
                    <input type="submit" value="搜 索" class="btn" id="J-submit">
                    @if($reportName=='transaction')
                    <a id="J-button-showdetail" href="javascript:;" style="position:absolute;right:0;top:25px;">高级搜索</a>
                    @endif
                </p>




                
                <div style="display:none;" id="J-panel-search-ad">
                    <p class="row">
                        @include('widgets.lottery-group-ways')
                        &nbsp;&nbsp;
{{--                        类型：
                        <select id="J-select-bill-type" style="display:none;" name="type_id">
                            <option value="">所有类型</option>
                            @foreach ($aTransactionTypes as $oTransactionType)
                                @if($reportName=='transaction' || in_array($oTransactionType->id, $depositTransactionType))
                                    <option value="{{ $oTransactionType->id }}" {{ Input::get('type_id') == $oTransactionType->id ? 'selected' : '' }}>{{ $oTransactionType->friendly_description }}</option>
                                @endif
                            @endforeach
                        </select>--}}

                        @if($reportName != 'transaction')
                            &nbsp;&nbsp;
                            <input type="submit" value="搜 索" class="btn" id="J-submit">
                        @endif

                    </p>

                     {{--
                    <p class="row">
                        游戏模式：
                            <select id="J-select-game-mode" style="display:none;" name="coefficient">
                            <option value="">所有</option>
                            @foreach ($aCoefficients as $key => $desc)
                                <option value="{{ $key }}" {{ Input::get('coefficient') == $key ? 'selected' : '' }}>{{ $desc }}</option>
                            @endforeach
                            </select>
                    </p>
                    --}}
                </div>
                


                


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