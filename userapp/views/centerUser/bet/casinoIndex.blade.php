@extends('l.home')

@section('title')
    游戏记录
    @parent
@stop

@section('styles')
    <style type="text/css">
        .table th a+a {margin-left: 0;}
        .table th a {text-decoration: none;}
        div.select-table-fileter {border:none;background: none;}
        div.select-table-fileter .choose-input {text-align: center;}
        div.select-table-fileter .choose-list-cont a {text-align: center;}
    </style>
    <style type="text/css">
        .content .row {
            float: left;
        }
        .content .row:last-child {border-bottom: none;}
        .content .row .text-title {
            width: 35%;
            float: left;
            text-align: right;
            padding-right: 20px;
            padding-top: 5px;
        }
        .content .row-set-prize .input {
            padding: 5px 10px;
            font-size: 16px;
        }
        .content .row .field li {
            float: left;
        }
        .field-type-switch li {
            font-size: 12px;
        }
        .field-type-switch li a {display: inline-block;padding:10px 20px;color: #999;}
        .field-type-switch li.current {
            color: #333;
            background: #EEE;
        }
        .field-type-switch li.current a {
            color: #333;
        }
        .page-content .row-nav ul{
            width: 176px;
            height: 38px;
            margin-top: 10px;
            margin-left: 20px;
            margin-bottom: 10px;
            border-radius: 4px;
            background-color: #31CEAC;
            padding: 5px 10px;
            font-size: 13px;
        }
    </style>
    @parent
@stop



@section ('main')
    <div class="nav-inner nav-bg-tab">
        <div class="title-normal">
            游戏记录
        </div>


        @include('w.uc-menu-game')


    </div>

    <div class="content">
        <div class="row row-nav clearfix">
            @if($projectType == 'lottery')
                <ul class="field field-type-switch">
                    <li @if($current_tab == 'projects')class="current"@endif><a href="{{ route('projects.index') }}"><span>游戏记录</span></a></li>
                    <li @if($current_tab == 'traces')class="current"@endif><a href="{{ route('traces.index') }}"><span>追号记录</span></a></li>
                </ul>
            @endif
        </div>


            @include('centerUser.bet.single_search')
            @include('centerUser.bet.single_list')



        {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
    </div>
@stop


@section('end')
    @parent
    <script>
        (function($){
            var table = $('#J-table'),
                    details = table.find('.view-detail'),
                    tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-page-records'}),
            // selectGameType = new bomao.Select({realDom:'#J-select-game-type',cls:'w-3'}),
            // selectMethodGroup = new bomao.Select({realDom:'#J-select-method-group',cls:'w-3'}),
            // selectMethod = new bomao.Select({realDom:'#J-select-method',cls:'w-3'}),
                    selectIssue = new bomao.Select({realDom:'#J-select-issue',cls:'w-2'}),
                    loadMethodgroup,
                    loadMethod;

            $('#J-date-start').focus(function(){
                (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
            });
            $('#J-date-end').focus(function(){
                (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
            });

            details.hover(function(e){
                var el = $(this),
                        text = el.parent().find('.data-textarea').val();
                tip.setText(text);
                tip.show(-90, tip.getDom().height() * -1 - 22, el);

                e.preventDefault();
            },function(){
                tip.hide();
            });


            var tableFilterSelect = new bomao.Select({
                cls:'w-2 select-table-fileter',
                realDom:$('#J-select-table-fileter')
            });
            tableFilterSelect.addEvent('change', function(e, v, text){
                var urltpl = $('#J-select-table-fileter').attr('data-urltpl').replace(/<#=status#>/g, v);
                location.href = urltpl;
            });


            /**
             setTimeout(function(){
        $(".choose-list-cont").jscroll({Btn:{btn:false}});
    }, 0);
             **/


        })(jQuery);
    </script>

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
@stop