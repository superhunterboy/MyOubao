var gameMethods = {};

var analysisData = function (id, name) {

    var gameMethodConfig = {
        data: {},
        getMethodConfigByName: function (name) {
            return this.data[name];
        }
    };

    var gameConfigData = name;
    gameMethodConfig.id = id;

    $.each(gameConfigData, function () {
        var data = gameConfigData['gameMethods'],
            nodeCache = {},
            methodCache = {},
            node1,
            node2,
            node3;

        $.each(data, function () {
            node1 = this;
            node1['fullname_en'] = [node1['name_en']];
            node1['fullname_cn'] = [node1['name_cn']];
            nodeCache['' + node1['id']] = node1;

            if (node1['children']) {
                $.each(node1['children'], function () {
                    node2 = this;
                    node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
                    node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
                    nodeCache['' + node2['id']] = node2;

                    if (node2['children']) {
                        $.each(node2['children'], function () {
                            node3 = this;
                            node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
                            node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
                            gameMethodConfig.data[node3['fullname_en'].join('-')] = node3;
                        });
                    }
                });
            }
        });
    });

    gameMethods[id] = gameMethodConfig;
};

(function (host) {
    //获取浏览器信息
    function getBrowserInfo() {
        var Sys = {};
        var ua = navigator.userAgent.toLowerCase();
        var s; (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
            (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
            (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
            (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
            (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;

        if(Sys.ie) {
            return Sys.ie;
        }
    }

    var browser = getBrowserInfo() ;
    //var verinfo = (browser+"").replace(/[^0-9.]/ig, "");      // 版本号
    
    //ie版本低于ie10
    if(Number(browser)<Number('10.0')){
        $('.happy-panel').html('');
        $('.happy-panel').hide();
        $('.ie-tips').show();

        var downloadUrl = '';

        $('.browser-box').on('click','.browser-lab' , function(){
            var index = $(this).attr('param');
            switch(index){
                case '0': downloadUrl='https://www.google.com/chrome/browser/desktop/index.html';break;
                case '1': downloadUrl='http://se.360.cn/';break;
                case '2': downloadUrl='http://www.firefox.com.cn/';break;
                case '3': downloadUrl='https://liulanqi.baidu.com/';break;
                case '4': downloadUrl='http://www.maxthon.cn/';break;
                default:break;
            };

            window.open(downloadUrl);
        });
    }
})(bomao);

//游戏数组
var gameArray = [];

(function (host) {

    for (var i in global_game_config_lucky28.gameInfo) {
        analysisData(global_game_config_lucky28.gameInfo[i].gameId, global_game_config_lucky28.gameInfo[i]);
    }

    //监听屏幕宽度
    window.onresize = function () {
        if (document.body.clientWidth > 1280) {
            $('#clockList').css({
                display: 'block'
            });
        }
    };
    
    //左侧时钟列表，位置调整
    $(window).scroll(function(){
        if($(window).scrollTop()>=124){
            $(".clock-slider").addClass('clock-slider-active');
            $(".informationList").addClass('informationList-active');
        }else{
            $(".clock-slider").removeClass('clock-slider-active');
            $(".informationList").removeClass('informationList-active');
        }
    });

})(bomao);

(function (host) {
    var service = new bomao.Lucky28.DataService();

    //游戏名称 ，游戏ID ， 游戏父容器  ， 游戏赔率设定 ， 游戏新奖期数据 ， 游戏初始化奖期数据
    for (var i in global_game_config_lucky28.gameInfo) {

        var gameName = global_game_config_lucky28.gameInfo[i].gameName_cn.substring(0, global_game_config_lucky28.gameInfo[i].gameName_cn.length - 4);
        var gameUIContainer = '#' + global_game_config_lucky28.gameInfo[i].gameName_en;

        creatGame(
            gameName,
            global_game_config_lucky28.gameInfo[i].gameId,
            gameUIContainer,
            gameMethods[global_game_config_lucky28.gameInfo[i].gameId],
            global_game_config_lucky28,
            global_game_init_data_lucky28[global_game_config_lucky28.gameInfo[i].gameId]
        );

    }

    $('#clockList').css({
        height: $('.lucky-main-panel').height()
    });


    var order_window = new bomao.Lucky28.orderWindow({service: service});

    //只允许输入数字
    order_window.container.find(".money-box").bind('input propertychange', function () {
        var box_value = order_window.container.find(".money-box").val();
        order_window.container.find(".money-box").val(box_value.replace(/[^\d]/g, ''));

        if (box_value.charAt(0) == 0) {
            order_window.container.find(".money-box").val('');
        }

        //兼容ie判断
        if(order_window.cell_data){
            if (Number(box_value) > (order_window.cell_data.extra-order_window.bet_value_total)) {
                order_window.container.find(".money-box").val((order_window.cell_data.extra-order_window.bet_value_total));
            }
        }
        
        //限制输入金额为最大账户金额
        if (Number(box_value) > parseInt(order_window.userAccount)) {
            order_window.container.find(".money-box").val(parseInt(order_window.userAccount));
        }
    });
    //处于焦点时，清空内容
    order_window.container.on('focus', '.money-box', function () {
        if ($(this).val() == '请输入下注金额') {
            $(this).val('');
        }
    });
    //离开输入框，若为空。
    order_window.container.on('blur', '.money-box', function () {
        if ($(this).val() == '') {
            $(this).val('请输入下注金额');
        }
    });
    //点击1 2 5 10 50 100 500金额输入框
    order_window.container.on('mousedown', '.money-list li', function () {
        var money_value = $(this).attr('param');

        if(money_value == "all"){
            $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
        }else{
             //下注金额不能超过限额
            if(money_value>(order_window.cell_data.extra-order_window.bet_value_total)){
                $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
            }else{
                $('.money-box').val(money_value);
            }
        }

        if ($(this).attr('param')) {
            $(this).addClass('money-list-box-active');
        }
    });
    order_window.container.on('mouseup', '.money-list li', function () {
        $('.money-box').focus();
        var money_value = $(this).attr('param');

        if(money_value == "all"){
            $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
        }else{
             //下注金额不能超过限额
            if(money_value>(order_window.cell_data.extra-order_window.bet_value_total)){
                $('.money-box').val((order_window.cell_data.extra-order_window.bet_value_total));
            }else{
                $('.money-box').val(money_value);
            }
        }
        
        $(this).removeClass('money-list-box-active');
    });
    order_window.container.on('mouseleave', '.money-list li', function () {
        $(this).removeClass('money-list-box-active');
    });
    //关闭订单菜单
    order_window.container.on('click', '.close-order', function () {
        order_window.closeOrder();
    });
    //监听下单窗口的菜单切换
    order_window.container.on('click', '.tag-menu', function () {
        var index = $(this).attr('param');
        order_window.switchMenu(index);
    });
    //点击全选框
    order_window.container.on('click', '.all-select-box', function () {
        $("input[name='cancel_order']").prop("checked", $("input[name='all_select_box']").is(':checked'));

        if ($("input[name='all_select_box']").is(':checked')) {
            order_window.cur_cancel_orders_arr = [];
            for (var i in order_window.cur_orders_arr) {
                order_window.cur_cancel_orders_arr.push(order_window.cur_orders_arr[i].id);
            }
        } else {
            order_window.cur_cancel_orders_arr = [];
        }
    });
    //点击单个订单
    order_window.container.on('click', 'input[name="cancel_order"]', function () {
        var orderId = Number($(this).parent().parent().attr("row-list"));
        if ($(this).is(':checked')) {
            if (order_window.cur_cancel_orders_arr.indexOf(orderId) == -1) {
                order_window.cur_cancel_orders_arr.push(orderId);
            }
        } else {
            if (order_window.cur_cancel_orders_arr.indexOf(orderId) != -1) {
                order_window.cur_cancel_orders_arr.splice(order_window.cur_cancel_orders_arr.indexOf(orderId), 1);
            }

            $("input[name='all_select_box']").attr("checked",false);
        }
    });
    //订单-确定按钮效果
    order_window.container.on('mousedown', '.order-submit', function () {
        $(this).addClass('order-button-active');

        if (order_window.current_menu == 0) {
            order_window.submitOrder();
        } else {
            order_window.submitCancelOrder();
        }
    });
    order_window.container.on('mouseup', '.order-submit', function () {
        $(this).removeClass('order-button-active');
    });
    order_window.container.on('mouseleave', '.order-submit', function () {
        $(this).removeClass('order-button-active');
    });
    //确认下注，增加回车监听
    $(document).keydown(function(e){
        var key = e.which; //e.which是按键的值
        if(key == 13 && order_window.container.hasClass('order-panel-show')){
            if (order_window.current_menu == 0) {
                order_window.submitOrder();
            }
        };
    });
    //订单-取消按钮效果
    order_window.container.on('mousedown', '.order-cancel', function () {
        $(this).addClass('order-button-active');
        if (order_window.current_menu == 0) {
            order_window.cancelOrder();
        } else {
            order_window.cancelSelectOrder();
        }
    });

    order_window.container.on('mouseup', '.order-cancel', function () {
        $(this).removeClass('order-button-active');
    });

    order_window.container.on('mouseleave', '.order-cancel', function () {
        $(this).removeClass('order-button-active');
    });
    //订单拖动效果
    order_window.container.on('mousedown', '.order-menu-head', function (event) {
        var e = event || window.event;
        order_window.drag = true;
        order_window._x = e.pageX - order_window.container.position().left;
        order_window._y = e.pageY - order_window.container.position().top;
    });

    order_window.container.on('mousemove', '.order-menu-head', function (event) {
        if (!order_window.drag) {
            return false;
        }
        var e = event || window.event;
        //当前可视区域的坐标
        var x = e.pageX - order_window._x;
        var y = e.pageY - order_window._y;

        var minL = $(".lucky-main-panel").offset().left;
        var maxL = $(".lucky-main-panel").offset().left + $(".lucky-main-panel").width() - order_window.container.outerWidth();

        var minT = $(".lucky-main-panel").offset().top > $(window).scrollTop() ? $(".lucky-main-panel").offset().top - $(window).scrollTop() : 0;
        var maxT = $(".lucky-main-panel").height() - ($(window).scrollTop() - $(".lucky-main-panel").offset().top) - order_window.container.outerHeight();

        x = x < minL ? minL : x;
        x = x > maxL ? maxL : x;

        y = y < minT ? minT : y;
        y = y > maxT ? maxT : y;

        order_window.container.css({
            left: x,
            top: y,
        });
    });
    order_window.container.on('mouseup', '.order-menu-head', function (event) {
        order_window.drag = false;
    });

    order_window.container.on('mouseleave', '.order-menu-head', function (event) {
        order_window.drag = false;
    });

    $(window).scroll(function (event) {
        if (order_window.container.offset().top - $(".lucky-main-panel").offset().top < 0) {
            order_window.container.css({
                top: $(".lucky-main-panel").offset().top - $(window).scrollTop(),
            });
        }
        if (order_window.container.offset().top - $(".lucky-main-panel").offset().top > $(".lucky-main-panel").height() - order_window.container.outerHeight()) {
            if ($(".lucky-main-panel").height() + $(".lucky-main-panel").offset().top - order_window.container.outerHeight() > $(window).scrollTop()) {
                order_window.container.css({
                    top: $(".lucky-main-panel").height() + $(".lucky-main-panel").offset().top - order_window.container.outerHeight() - $(window).scrollTop(),
                });
            }
        }
    });

    var historyRecord = new bomao.Lucky28.historyRecord({'initData':global_game_config_lucky28.gameInfo});

    var resultRecord = new bomao.Lucky28.resultRecord();

    var Awardforlottery = new bomao.Lucky28.Awardforlottery({'initData':global_game_config_lucky28.gameInfo});




    (function () {

      function checkdate(id){
          // alert(id);
                switch(id){
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
          // alert(formatDate(startDate));
                // document.getElementById('J-date-start').value=formatDate(startDate)+ ' 00:00:00';
               $('#r-allday').val(formatDate(startDate));
                $('#r-alldayto').val(formatDate(endDate));
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
        $('#r-allday').focus(function () {
            (new bomao.DatePicker({input: '#r-allday', isShowTime: false, startYear: 2013})).show();
        });
        $('#r-alldayto').focus(function () {
            (new bomao.DatePicker({input: '#r-alldayto', isShowTime: false, startYear: 2013})).show();
        });
        $('#r-kjjg').focus(function () {
            (new bomao.DatePicker({input: '#r-kjjg', isShowTime: false, startYear: 2013})).show();
        });

        $('.r-allday #today,.r-allday #week,.r-allday #3day,.r-allday #hmonth').on('click',function () {
            // alert(1);
                var _a = $(this).attr('id');
            checkdate(_a);
        });



//         $('#r-allday').daterangepicker(
//             {
//                 startDate: moment().subtract(1, 'days').startOf('day'),
//                 autoApply: true,
//                 endDate: moment(),
//                 minDate: '01/01/2016',  //最小时间
//                 maxDate: moment(), //最大时间
//                 dateLimit: {
//                     days: 365
//                 }, //起止时间的最大间隔
//                 showDropdowns: true,
//                 showWeekNumbers: false, //是否显示第几周
// //                  timePicker : true, //是否显示小时和分钟
//                 timePickerIncrement: 60, //时间的增量，单位为分钟
// //                  timePicker12Hour : false, //是否使用12小时制来显示时间
//                 ranges: {
//                     //'最近1小时': [moment().subtract('hours',1), moment()],
//                     '今日': [moment().startOf('day'), moment()],
//                     '昨日': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
//                     '最近7日': [moment().subtract(6, 'days'), moment()],
//                     '最近30日': [moment().subtract(29, 'days'), moment()]
//                 },
//                 opens: 'right', //日期选择框的弹出位置
//                 buttonClasses: ['btn btn-default'],
//                 applyClass: 'btn-small btn-primary blue',
//                 cancelClass: 'btn-small',
//                 separator: ' to ',
//                 locale: {
//                     format: 'YYYY-MM-DD', //控件中from和to 显示的日期格式
//                     separator: "/",
//                     applyLabel: '确定',
//                     cancelLabel: '取消',
//                     fromLabel: '起始时间',
//                     toLabel: '结束时间',
//                     customRangeLabel: '自定义',
//                     daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
//                     monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
//                         '七月', '八月', '九月', '十月', '十一月', '十二月'],
//                     firstDay: 1
//                 },
//                 linkedCalendars: false,
//                 alwaysShowCalendars: true
//
//             });
//         $('#r-kjjg').daterangepicker(
//             {
//                 startDate: moment().startOf('day'),
//                 autoApply: true,
//                 endDate: moment(),
//                 minDate: '01/01/2016',	//最小时间
//                 maxDate: moment(), //最大时间
//                 dateLimit: {
//                     days: 365
//                 }, //起止时间的最大间隔
//                 showDropdowns: true,
//                 showWeekNumbers: false, //是否显示第几周
// //                  timePicker : true, //是否显示小时和分钟
//                 timePickerIncrement: 60, //时间的增量，单位为分钟
// //                  timePicker12Hour : false, //是否使用12小时制来显示时间
//                 ranges: {
//                     //'最近1小时': [moment().subtract('hours',1), moment()],
//                     '今日': [moment().startOf('day'), moment()],
//                     '昨日': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
//                     '最近7日': [moment().subtract(6, 'days'), moment()],
//                     '最近30日': [moment().subtract(29, 'days'), moment()]
//                 },
//                 opens: 'right', //日期选择框的弹出位置
//                 buttonClasses: ['btn btn-default'],
//                 applyClass: 'btn-small btn-primary blue',
//                 cancelClass: 'btn-small',
//                 separator: ' to ',
//                 locale: {
//                     format: 'YYYY-MM-DD', //控件中from和to 显示的日期格式
//                     separator: "/",
//                     applyLabel: '确定',
//                     cancelLabel: '取消',
//                     fromLabel: '起始时间',
//                     toLabel: '结束时间',
//                     customRangeLabel: '自定义',
//                     daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
//                     monthNames: ['一月', '二月', '三月', '四月', '五月', '六月',
//                         '七月', '八月', '九月', '十月', '十一月', '十二月'],
//                     firstDay: 1
//                 },
//                 singleDatePicker: true
//
//             });

        // caizhong);
        var _address = "/projects/mini-window-xy28?series_id=20",
            _b = 1,
            _c = $('.r-history .a3-2').height(),
            _lsadd = '',
            _kjjg = '/bets/wnnumber-result?',
            _kjjgdiv = $('.r-kjjg .main-bot .bottom').height(),
            _lskjjg = '';

        function csh() {
            _b = 1;
            _c = 433;
            // $('.r-history .a-3 .a3-2').scrollTop(0);
            // $('.r-kjjg .main-bot').scrollTop(0);
            $('html').removeAttr('style');
            // alert(1);
        };

        function address_clean() {
            _address = "/projects/mini-window-xy28?series_id=20";
            _lsadd = '';
        }

        function unchecked() {
            $('.a-2 li input').attr('checked', false);
        }

        function unoption() {
            $('#all-cp option').attr('selected', false);
            $('#all-cp option:eq(0)').attr('selected', true);

        }

        function rZZ(onOff) {
            if (onOff) {
                $('body').append('<div class="r-zz"></div>');
            } else {
                $('body .r-zz').remove();
            }

        }
        function cztoid(cz) {
            var _a =1;

            switch(cz)
            {
                case '重庆快乐28':
                    _a=54;
                    break;
                case '黑龙江快乐28':
                    _a=55;
                    break;
                case '新疆快乐28':
                    _a=56;
                    break;
                case '天津快乐28':
                    _a=57;
                    break;
                case '北京快乐28':
                    _a=58;
                    break;
                case '上海快乐28':
                    _a=59;
                    break;
            }

            return _a;
        }

        $(document)
            .on('click', '.r-allday .six', function () {
                var _address = '/bets/profits/20/',
                    _choose = $('#r-allday').val()+'/'+$('#r-alldayto').val();
                
                $.ajax({
                    url: _address += _choose,
                    cache: false,
                    success: function (html) {
                        if(html=='[]'){
                            html='<div class="r-error">您查询的条目不存在，请重试</div>'
                            $('.r-allday .bot-l .bottom').empty().append(html);
                        }else {
                            var _a = JSON.parse(html),
                                _lenght = _a.length,
                                _all = '';
                            $.each(_a, function (i) {
                                _all += '<ul>' +
                                    '<li class="num-1">' + _a[i][0] + '</li>' +
                                    '<li class="num-2">' + _a[i][1] + '</li>' +
                                    '<li class="num-3">' + _a[i][2] + '</li>' +
                                    '<li class="num-4">' + _a[i][3] + '</li>' +
                                    '<li class="r-xq num-5"></li>' +
                                    '</ul>';
                            })
                            $('.r-allday .bot-l .bottom').empty().append(_all);
                        }




                    }
                });
            })
            .on('click', '.r-allday .r-close', function () {
                $('html').removeAttr('style');
                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-allday').fadeOut();


            })
            .on('click', '.r-history .r-close', function () {

                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-history').fadeOut();

            })
            .on('click', '.r-kjjg .r-close', function () {

                unchecked();
                csh();
                address_clean();
                rZZ(false);
                $('.r-kjjg').fadeOut();
                $('#kjjg-select option:eq(0)').attr('selected', true);

            })
            .on('click', '.r-introduce .r-close', function () {
                $('.r-introduce').hide();
                $('html,body').removeAttr('style');
                rZZ(false);
            })
            .on('click', '.historyButton', function () {
                $('body,html').scrollTop(0);
                $('html').css('overflow-y', 'hidden');
                rZZ(true);
                $.ajax({
                    url: "/bets/profits/20/",
                    cache: false,
                    success: function (html) {
                        if(html=='[]'){
                            html = '<div class="r-error">暂时没有投注记录</div>'
                            $('.r-allday .bot-l .bottom').empty().append(html);
                        }else {
                            var _a = JSON.parse(html),
                                _lenght = _a.length,
                                _all = '';
                            $.each(_a, function (i) {
                                _all += '<ul>' +
                                    '<li class="num-1">' + _a[i][0] + '</li>' +
                                    '<li class="num-2">' + _a[i][1] + '</li>' +
                                    '<li class="num-3">' + _a[i][2] + '</li>' +
                                    '<li class="num-4">' + _a[i][3] + '</li>' +
                                    '<li class="r-xq num-5"></li>' +
                                    '</ul>';
                            })
                            $('.r-allday .bot-l .bottom').empty().append(_all);
                        }
                    }
                });

                // http://bocat.user/bets/wnnumber-result
                $('.r-allday').fadeIn();
                $('.r-kjjg').hide();

            })
            .on('click', '.resultButton', function () {
                $('body,html').scrollTop(0);
                $('html').css('overflow-y', 'hidden');
                rZZ(true);

                $.ajax({
                    url: "/bets/wnnumber-result",
                    cache: false,
                    success: function (html) {
                        $('.r-kjjg .main-bot .bot-l .bottom-a').empty().append(html);
                        $('.r-kjjg').fadeIn();
                        $('.r-allday').fadeOut();
                        $('.r-history').fadeOut();

                    }
                });

            })
            .on('click', '#r-search', function () {

                $('.r-history .a-3 .a3-2').scrollTop(0);
                _b = 1;
                _c = 433;
                var _a = $('#all-cp').select(),
                    _b = [],
                    _blength = 0;
                _lsadd = _address;

                $('.a-2 input:checkbox:checked').each(function (i) {
                    if ($(this).attr('name') != 'allselect') {
                        _b.push($(this).attr('name'));
                    }
                });
                $.each(_b, function (i) {
                    _lsadd += '&' + _b[i];
                });
                if ($('#all-cp').val() != '所有游戏') {
                    var _cpName = $("#all-cp").find('option:selected').attr('name');
                    _lsadd += '&' + _cpName;
                }
                $.ajax({
                    url: _lsadd,
                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = '<div class="r-error">抱歉，您查询的条目暂时没有数据</ul>'
                        }
                        $('.r-history .a3-2 .l').empty().append(html);
                        $('.r-history').fadeIn();
                        $('html').css('overflow-y', 'hidden');

                    }
                });
            })
            .on('click', '.r-xq', function () {
                var _a = $(this).siblings('li:eq(0)').text(),
                // var _a = '2016-06-30',
                    _b = ' 00:00:00',
                    _c = ' 23:59:59',
                    _d = '/projects/mini-window-xy28?series_id=20&bought_at_from=' + _a + _b + '&bought_at_to=' + _a + _c;
                _address = _d;

                $.ajax({
                    url: _d,
                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = "<div class='r-error'>当天没有投注数据，请返回查看其他日期</ul>"
                        }
                        $('.r-history .a3-2 .l').empty().append(html);
                    }
                });
                $('.r-allday').fadeOut();
                $('.r-history').fadeIn();
            })
            .on('click', '#r-return', function () {
                _b = 1;
                _c = 433;
                unchecked();
                unoption();
                $('.r-history').fadeOut();
                $('.r-allday').fadeIn();
            })
            .on('click', '.r-history .a-2 li input[name="series_id=20"]', function () {
                if ($(this).is(':checked')) {
                    $(this).parent().parent().siblings('li').find('input').prop('checked', true);
                    $(this).parent().parent().siblings('li').find('label').removeClass('check').addClass('checkin');
                } else {
                    $(this).parent().parent().siblings('li').find('input').prop('checked', false);
                    $(this).parent().parent().siblings('li').find('label').removeClass('checkin').addClass('check');
                }
            })
            // .on('click', '.a-2 li label', function () {
            //     alert(1);
            //
            // })
            .on('click', '.r-kjjg .six', function () {
                _b = 1;
                _c = 433;
                $('.r-kjjg .main-bot').scrollTop(0);
                var _choose = 'date=' + $('#r-kjjg').val(),
                    _text = $('.r-kjjg .main-mid input[name=r-jiangqi]').val().replace(/\s/g, ""),
                    _true = false;


                if (_text != '') {
                    _choose += '&issue=' + _text;
                    _true = true;
                }

                if ($('#kjjg-select').val() != '所有游戏') {
                    var _cpName = $("#kjjg-select").find('option:selected').attr('name');

                    _choose += '&' + _cpName;
                }
                $.ajax({
                    url: _kjjg + _choose,

                    cache: false,
                    success: function (html) {
                        if (html == '') {
                            html = '<div class="r-error">抱歉，您查询的条目暂时没有数据</ul>'
                        }
                        $('.r-kjjg .main-bot .bot-l .bottom-a').empty();
                        // $('.r-kjjg .main-bot .bot-l ul[class!="title"]').remove();
                        // $('.r-kjjg .main-bot .bot-l div').remove();
                        if (_true) {
                            // $('.r-kjjg .main-bot .bot-l').append(html);
                            // alert(typeof html);
                            // alert(html);
                            var _aArr = html.split("</ul>");
                            $('.r-kjjg .main-bot .bot-l .bottom-a').append(_aArr[0] + '</ul>');
                        } else {
                            $('.r-kjjg .main-bot .bot-l .bottom-a').append(html);
                        }

                    }
                });

                _lskjjg = _kjjg + _choose;
            })
            .on('click', '.r-zz', function () {
                $('.r-allday').hide();
                $('.r-history').hide();
                $('.r-kjjg').hide();
                $('.r-introduce').hide();
                $('html,body').removeAttr('style');
                $(this).remove();
            })
            .on('click', '.r-history .num-10 .confirm', function () {
                // var _a ='http://bocat.user/projects/9960702/drop/0',

                var _a = $(this).attr('value'),
                    me = this,
                    _c = $(me).parent().parent('ul'),
                    _idnum = cztoid($.trim($(me).parent().parent().find('.num-3').text())),
                    _jqnum = $.trim($(me).parent().parent().find('.num-5').text()),
                    _jqid=0;

                $.ajax({
                    url: _a,
                    cache: false,
                    success: function (html) {
                        var _b = JSON.parse(html);
                        if (_b.isSuccess == 1) {
                            // alert('恭喜撤单成功!')
                            _c.hide();
                            _c.attr('style', '');
                            _c.find('.num-9').text('已撤销');
                            _c.find('.num-10 div').remove();
                            _c.show();

                            $.each(gameArray,function (i) {
                                if(gameArray[i].id==_idnum){
                                    _jqid = i;
                                }
                            });

                            service.getOrders(_idnum , function(data){
                                gameArray[_jqid].updateBetInformation(_jqnum ,data);
                            });



                        } else {
                            var popWindowNew = bomao.Message.getInstance();
                            var data = {
                                title: '撤单失败(3秒后自动关闭)',
                                content: "<i class=\"ico-waring\"></i><p class=\"pop-text\">撤单失败！已开奖或正在开奖中...</p>",
                                isShowMask: true,
                                cancelIsShow: false,
                                confirmIsShow: false
                            };

                            popWindowNew.show(data);
                            setTimeout(function () {
                                popWindowNew.hide();
                            }, 2800);
                            _c.attr('style', '');

                            $('#r-search').trigger('click');
                        }
                    }
                });
            })
            .on('click', '.cancel', function () {
                // $(this).css('background-image','url(/assets/images/lucky28/confirm-cd.gif) no-repeat center');
                // $(this).css('height','20px');
                var _a = $(this).parent().parent('ul');

                _a.css({
                    'border-bottom': '1px solid #96c0bd',
                    'border-top': '1px solid #96c0bd'
                });


                $(this).removeClass('cancel').addClass('confirm');

            })
            .on('click', '.introduce', function () {
                rZZ(true);
                $('html,body').scrollTop(0).css('overflow', 'hidden');
                var html =
                    '<div class="r-introduce" style="display: block;"><div class="close-order r-close"></div><div class="title">幸运28</div>' +
                    '<div class="row-text">'+
                    '    <p><strong><span style="line-height: 1em;">幸运28-北京</span></strong><span style="line-height: 1em;">：开奖结果来源于北京福彩网北京快乐8(官网)开奖号码，从早上9:05至23:55，每5分钟一期不停开奖。 北京快乐8每期开奖共开出20个数字，幸运28将这20个开奖号码按照由小到大的顺序依次排列；取其1-6位开奖号码相加，和值的末位数作为幸运 28开奖第一个数值； 取其7-12位开奖号码相加，和值的末位数作为幸运28开奖第二个数值，取其13-18位开奖号码相加，和值的末位数作为幸运 28开奖第三个数值；三个数值相加即为幸运28最终的开奖结果。官方网址http://www.bwlc.net</span><br>'+
                    '</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-重庆</strong>：开奖结果来源于重庆市彩票网重庆时时彩(官网)开奖号码，从10:00至22:00，每10分钟一期不停开奖，从22:00至01:55，每5分钟一期不停开奖。'+
                    '        重庆时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.cqcp.net</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-黑龙江</strong>：开奖结果来源于黑龙江福彩网黑龙江时时彩(官网)开奖号码，从08:50至22:40，每10分钟一期不停开奖。'+
                    '        黑龙江时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.lottost.cn</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-新疆</strong>：开奖结果来源于新疆福彩网新疆时时彩(官网)开奖号码，从10:10至02:00，每10分钟一期不停开奖。'+
                    '        新疆时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.xjflcp.com</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-天津</strong>：开奖结果来源于天津福彩网天津时时彩(官网)开奖号码，从09:10至23:00，每10分钟一期不停开奖。'+
                    '        天津时时彩每期开奖共开出5个数字，幸运28将前三码做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.tjflcpw.com</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>幸运28-上海</strong>：开奖结果来源于上海福彩网上海时时乐(官网)开奖号码，从10:30至21:30，每30分钟一期不停开奖。上海时时乐每期开奖共开出3个数字，幸运28将这三个号做为开奖号码。三个数值相加即为幸运28最终的开奖结果。官方网址http://www.swlc.gov.cn'+
                    '    </p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p><strong>五大玩法简介</strong></p>'+
                    '    <p>大小玩法：数字14-27为大 数字0-13为小 。</p>'+
                    '    <p>单双玩法: 数字1，3，5，~27为单 数字0，2，4~26为双 。</p>'+
                    '    <p>极值玩法: [极小0-5] [极大22-27] 。</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p>组合玩法:</p>'+
                    '    <p>数字14，16，~26为大双 数字0，2，4，~12为小双。</p>'+
                    '    <p>数字15，17，~27为大单 数字1，3，5，~13为小单。</p>'+
                    '    <p>&nbsp;</p>'+
                    '    <p>和值玩法:</p>'+
                    '    <p>从0~27中任何选择1个或1个以上号码，所选数值等于开奖号码的相加之和，即为中奖。</p>'+
                    '</div>'+
                    '</div>';
                $('.record-panel').append(html);
                $('.r-introduce').fadeIn();

            })
            .on('click', '.r-history .a-2 label', function () {
                if($(this).find('input').is(':checked')){
                    $(this).removeClass('check').addClass('checkin')
                }else {
                    $(this).removeClass('checkin').addClass('check')
                }
                var _a = $('.r-history .a-2 li label input:checked').length;
                if (_a == 4) {
                    $('.r-history .a-2 li:eq(3) label input').prop('checked', false);
                    $('.r-history .a-2 li:eq(3) label').removeClass('checkin').addClass('check');
                }

            });

        $('.r-history .a3-2').scroll(function () {
            // $('html').css('overflow-y','hidden');
            var _a = $(this).scrollTop(),
                _d = $(this).find('.l').height() + 1;
            if (_lsadd == '') {
                _lsadd = _address;
            }

            if (_a >= _d - _c) {
                _b += 1;


                $.ajax({
                    url: _lsadd + '&page=' + _b,
                    cache: false,
                    success: function (html) {
                        $('.r-history .a3-2 .l').append(html);

                    }
                });
            }
        });
        $('.r-kjjg .bottom').scroll(function () {

            var _a = $(this).scrollTop(),
                _d = $(this).find('.bottom-a').height() - 1,
                me=this;

            if (_lskjjg == '') {
                _lskjjg = _kjjg;
            }

            if (_a >= _d - _kjjgdiv) {
                _b += 1;

                $.ajax({
                    url: _lskjjg + '&page=' + _b,
                    cache: false,
                    success: function (html) {
                        $('.r-kjjg .main-bot .bot-l .bottom .bottom-a').append(html);

                    }
                });
            }

        });
    })();

    
    //游戏名称 ，游戏ID ， 游戏父容器 ， 游戏赔率设定 ， 游戏新奖期数据 ， 游戏初始化奖期数据
    function creatGame(gameName, gameId, gameUIContainer, gameMethod, newPrizeData, initData) {
        var game = null;
        var time = null;

        var getData = function () {
            if (!game) {

                var clockUIContainer = gameUIContainer + '-clock';
                var clock = new bomao.Lucky28.clock({UIContainer: clockUIContainer, cityName: gameName});
                clock.updateCityName();

                game = new bomao.Lucky28.Game({
                    name: gameName,
                    id: gameId,
                    UIContainer: gameUIContainer,
                    gameMothed: gameMethod,
                    clock: clock
                });
                game._token = newPrizeData._token;
                game.prize_group = newPrizeData.user_prize_group;
                game.bet_max_amount = Number(newPrizeData.bet_max_amount);

                gameArray.push(game);

                //获取游戏订单数组
                game.game_orders = newPrizeData.gameInfo[gameId].orders;

                //模拟三期 需要设置一个时间分界点
                var currentGameData = newPrizeData.gameInfo[gameId];
                //初始化封盘状态判断
                if(currentGameData.currentNumberTime - newPrizeData.currentTime <= currentGameData.cycle){
                    if(initData.length == 3){
                        game.addPrize(initData[2].issue, 0 , currentGameData.cycle , initData[2].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                    }
                    if(initData.length == 2){
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                    }
                    game.addPrize(currentGameData.currentNumber , (currentGameData.currentNumberTime-newPrizeData.currentTime) , currentGameData.cycle , '' , currentGameData.entertainedTime);
                    time = currentGameData.currentNumberTime - newPrizeData.currentTime;
                }else{
                    //有奖期正常显示，无新奖期则显示历史3期
                    if(currentGameData.currentNumberTime){
                        if(initData.length == 3){
                            game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                            game.addPrize(initData[0].issue, 0 , currentGameData.cycle , initData[0].wn_number.replace(/\s+/g,""),0);
                        }
                        if(initData.length == 2){
                            game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                        }
                        game.addPrize(currentGameData.currentNumber , (currentGameData.currentNumberTime-newPrizeData.currentTime) , currentGameData.cycle , '' , currentGameData.entertainedTime);
                        time = currentGameData.currentNumberTime - newPrizeData.currentTime - currentGameData.cycle;
                        //设置开盘时间
                        var startDate = new Date(new Date(currentGameData.gameNumbers[0].time).getTime()-currentGameData.cycle*1000);
                        //IE设置
                        if(isNaN(startDate)){
                            var currentDate = new Date(Date.parse(currentGameData.gameNumbers[0].time.replace(/-/g,"/"))).getTime() - currentGameData.cycle*1000;
                            startDate = new Date(currentDate);
                        }
                        game.caches[0].information_suspension.updateOpenTime(
                            startDate.getMonth()+1,
                            startDate.getDate(),
                            startDate.getHours(),
                            startDate.getMinutes(),
                            startDate.getSeconds()
                            );
                    }else{
                        game.addPrize(initData[2].issue, 0 , currentGameData.cycle , initData[2].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[1].issue, 0 , currentGameData.cycle , initData[1].wn_number.replace(/\s+/g,""),0);
                        game.addPrize(initData[0].issue, 0 , currentGameData.cycle , initData[0].wn_number.replace(/\s+/g,""),0);
                    }
                }

                setTimeout(getData,(time+1)*1000);
                game.initload = false;

                //初始化显示最新奖期
                game.getCurrentPrize().container.show();

                //初始化历史数据
                game.mini_history.getInitData(newPrizeData.gameInfo[gameId].winNumbers);

                //切换奖期
                game.container.find('.bet-history-nav').on('click', 'li', function () {
                    //判断是否处于动画中
                    if(!game.isAnimating){
                        var index = $(this).attr('data-param');
                        // 当前奖期进入动画
                        if(game.currentPrize.prize_id != game.getPrizePeriodByNumber(index).prize_id){
                            if(game.currentPrize.prize_id > game.getPrizePeriodByNumber(index).prize_id){
                                game.container.find('.prize-id-'+game.currentPrize.prize_id).addClass('prize-up-move-miss');
                                game.container.find('.prize-id-'+game.getPrizePeriodByNumber(index).prize_id).addClass('prize-up-move');
                            }else{
                                game.container.find('.prize-id-'+game.currentPrize.prize_id).addClass('prize-down-move-miss');
                                game.container.find('.prize-id-'+game.getPrizePeriodByNumber(index).prize_id).addClass('prize-down-move');
                            }
                        }
                        game.switchPrize(index);
                    };
                });

                //手动切换奖期菜单
                var rec_menu = game.container.find('.bet-history-nav li');
                game.addEvent('afert_select_recompense', function (e, data) {
                    var rec_content = game.container.find('.bet-history-content').children();
                    rec_menu.removeClass('recompense-selected');
                    for (var i = 0; i < data.length; i++) {
                        if (data[i] == 0) {
                            rec_menu.eq(i).addClass('recompense-selected');
                        }
                    }

                    game.container.find('.prize-id-'+game.getCurrentPrize().prize_id).show();
                    if (game.getCurrentPrize()) {
                        game.getCurrentPrize().showplay(game.getCurrentPrize().currentPlayIndex);
                    }
                });

                //自动切换奖期
                game.addEvent('auto_switch_recompense', function (e, data) {
                    var rec_content = game.container.find('.bet-history-content').children();
                    rec_menu.removeClass('recompense-selected');
                    for (var i = 0; i < data.length; i++) {
                        rec_content.eq((2 - i)).hide();
                        if (data[i] == 0) {
                            rec_menu.eq(i).addClass('recompense-selected');
                        }
                    }

                    game.container.find('.prize-id-'+game.getCurrentPrize().prize_id).show();
                    if (game.getCurrentPrize()) {
                        game.getCurrentPrize().showplay(game.getCurrentPrize().currentPlayIndex);
                    }
                });

                //切换游戏玩法
                game.getCurrentPrizeDOM().find('.play-choose').on('click', 'li', function () {
                    var index = Number($(this).attr('data-param'));
                    if(game.getCurrentPrize().currentPlayIndex != index && !game.isAnimating){
                        game.getCurrentPrize().swtichplay(index);
                    }
                });

                //游戏玩法
                game.container.on('click', '.bet li', function () {
                    //当前为组合玩法
                    if (game.getCurrentPrize().currentPlayIndex == 0) {
                        //判断和值是否已有点击效果，如果有，进行清除
                        if (game.getCurrentPrize().play_hezhi.isActivity()) {
                            game.getCurrentPrize().play_hezhi.reSet();
                        }
                        //当前为和值玩法
                    } else {
                        //判断组合是否已有点击效果，如果有，进行清除
                        if (game.getCurrentPrize().play_zuhe.isActivity()) {
                            game.getCurrentPrize().play_zuhe.reSet();
                        }
                    }

                    if ($(this).attr('param')) {
                        game.getCurrentPrize().getCurrentPlay().completeSelect($(this).attr('param'));

                        if (game.getCurrentPrize().getCurrentPlay().squareData[$(this).attr('param')] == -1) {
                            //隐藏下单菜单
                            order_window.closeOrder();
                        } else {
                            //显示下单菜单
                            order_window.showOrderWindow(game, game.getCurrentPrize().getCurrentPlay().current_cell_data);
                        }
                    }
                });

                //和值玩法-鼠标移入显示赔率
                game.container.on('mouseenter', '.bet-panel-hezhi li a', function () {
                    if ($(this).next().hasClass('hezhi-odds-tip-show')) {
                        $(this).next().removeClass('hezhi-odds-tip-show');
                    }
                    $(this).next().addClass('hezhi-odds-tip-show');
                });
                //和值玩法-鼠标移出隐藏赔率
                game.container.on('mouseleave', '.bet-panel-hezhi li a', function () {
                    if ($(this).next().hasClass('hezhi-odds-tip-show')) {
                        $(this).next().removeClass('hezhi-odds-tip-show');
                    }
                });
                //和值机选
                game.container.on('mousedown', '.random-submit', function () {
                    //判断组合是否已有点击效果，如果有，进行清除
                    if (game.getCurrentPrize().play_zuhe.isActivity()) {
                        game.getCurrentPrize().play_zuhe.reSet();
                    }
                    //获取随机数
                    var ran = function () {
                        var val = parseInt(Math.random() * 28);
                        if (val == game.getCurrentPrize().play_hezhi.last_random) {
                            ran();
                        } else {
                            game.getCurrentPrize().play_hezhi.last_random = val;
                        }
                    };

                    ran();
                    game.getCurrentPrize().getCurrentPlay().completeSelect(game.getCurrentPrize().play_hezhi.last_random);
                    $(this).addClass('random-box-active');
                    //显示下单菜单
                    order_window.showOrderWindow(game, game.getCurrentPrize().getCurrentPlay().current_cell_data);
                });
                game.container.on('mouseup', '.random-submit', function () {
                    $(this).removeClass('random-box-active');
                });
                game.container.on('mouseleave', '.random-submit', function () {
                    $(this).removeClass('random-box-active');
                });
                //和值赔率列表
                game.container.on('mouseenter','.odds-explain',function(){
                    game.container.find('.odds-explain-list').removeClass('odds-list-normal').addClass('odds-list-active');
                });
                game.container.on('mouseleave','.odds-explain',function(){
                    game.container.find('.odds-explain-list').removeClass('odds-list-active').addClass('odds-list-normal');
                });

                //取消机选
                // game.container.on('mousedown', '.random-cancel', function () {
                //     game.getCurrentPrize().play_hezhi.reSet();
                //     $(this).addClass('random-box-active');
                //     //隐藏下单菜单
                //     order_window.closeOrder();
                // });
                // game.container.on('mouseup', '.random-cancel', function () {
                //     $(this).removeClass('random-box-active');
                // });
                // game.container.on('mouseleave', '.random-cancel', function () {
                //     $(this).removeClass('random-box-active');
                // });


                var r_dxds = game.mini_history._dxds,
                    r_lushu = game.mini_history._lushu,
                    r_move = game.mini_history._move,
                    r_move1 = game.mini_history._move1,
                    _find = game.mini_history.container;

                _find.on('click', '.ul-1 .dx', function () {
                    _find.find('.ul2-main').css('right', '');
                    r_dxds = 0;
                    game.mini_history._dxds = 0;
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);

                });

                _find.on('click', '.ul-1 .ds', function () {
                    _find.find('.ul2-main').css('right', '');
                    r_dxds = 1;
                    game.mini_history._dxds = 1;
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);
                });

                // ico1
                _find.on('click', '.ul-3 .ico1', function () {
                    _find.find('.ul2-main').css('right', '');
                    if (r_lushu == 0) {
                        r_lushu = 1;
                        game.mini_history._lushu = 1;
                    } else {
                        r_lushu = 0;
                        game.mini_history._lushu = 0;
                    }
                    $(this).toggleClass('ico1-1');
                    game.mini_history.getHistory(r_dxds, _find.find('.ul2-main'), game.mini_history.historyArr, r_lushu);
                });

                //走势图拖动
                _find.on('mousedown', '.ul2-main', function (e) {
                    _find.find('.ul-2').css('overflow-x','auto');
                    var _x = e.pageX,
                        _width = parseInt($(this).css('width')),
                        _pyl = _find.find('.ul-2').scrollLeft();
                        // e=e||window.event;

                    $(this).mousemove(function (f) {
                        // var f=e||window.event;
                        r_move1 = -(f.pageX - _x) + _pyl;

                        if (r_move1 <= 0) {
                            r_move1 = 0;
                            $(this).unbind('mousemove');
                            _find.find('.ul2-l').show().fadeOut(1000);
                        } else if (r_move1 > _width - 425) {
                            r_move1 = _width - 425;
                            $(this).unbind('mousemove');
                            _find.find('.ul2-r').show().fadeOut(1000);
                        }

                        _find.find('.ul-2').scrollLeft(r_move1);


                    });
                }).on('mouseup mouseleave', '.ul2-main', function () {

                    _find.find('.ul-2').css('overflow-x','auto');
                    $(this).off('mousemove');
                });

                //走势图按钮，跳转官网
                _find.on('click','.ico2',function(event){
                    game.mini_history.linkOfficePage();
                });

                //走势图按钮，刷新走势图
                _find.on('click', '.ico3', function(event){
                    game.mini_history.updataModel();
                });

            } else {
                service.getGameDataByNumber(gameId, function (data) {
                    if(data.currentNumberTime){
                        
                        if(data.currentNumberTime - data.currentTime <= data.cycle){
                            time = data.currentNumberTime - data.currentTime;
                        }else{
                            time = data.currentNumberTime - data.currentTime - data.cycle;
                        }

                        setTimeout(getData,(time+1)*1000);
                        //相同期期号奖期，只更新，不新创建。用于封盘开始后的第一期
                        if(data.currentNumber == game.priedIDArr[0]){
                            if(data.currentNumberTime - data.currentTime <= data.cycle){
                                game.caches[0].fleshPrize(time,0);
                            }else{
                                game.caches[0].fleshPrize(time,5);
                            }
                        }else{
                            game.addPrize(data.currentNumber , time , data.cycle ,'' , data.entertainedTime);
                            game.autoSwitchPrize(1);
                        }

                        //应该处于停盘阶段
                        if(time > data.cycle){
                            var startDate = new Date(new Date(data.gameNumbers[0].time).getTime()-data.cycle*1000);
                            //IE设置
                            if(isNaN(startDate)){
                                var currentDate = new Date(Date.parse(data.gameNumbers[0].time.replace(/-/g,"/"))).getTime() - data.cycle*1000;
                                startDate = new Date(currentDate);
                            }
                            //设置开盘时间
                            game.caches[0].information_suspension.updateOpenTime(
                                startDate.getMonth()+1,
                                startDate.getDate(),
                                startDate.getHours(),
                                startDate.getMinutes(),
                                startDate.getSeconds()
                                );
                        }
                    }
                });
            }

        }

        getData();
    }
})(bomao);


