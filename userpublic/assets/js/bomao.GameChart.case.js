
$(function() {
	var panelControl = $('#J-panel-control'),
		periodsControl = $('#J-periods-data'),
		timePeriods = $('#J-time-periods'),
		reportDownload = $('#report-download'),
		buttonControl = $('#J-button-showcontrol'),
		table = $('#J-chart-table'),
		chartTbody = $('#J-chart-content');

	//操作面板显示/隐藏
	buttonControl.click(function(e) {
		var ico = buttonControl.parent().find('i'),
			cls1 = 'arrow-down',
			cls2 = 'arrow-up';
		panelControl.toggle();
		if (ico.hasClass(cls1)) {
			ico.removeClass(cls1).addClass(cls2);
                                        buttonControl.text('展开');
		} else {
			ico.removeClass(cls2).addClass(cls1);
                                        buttonControl.text('收起');
		}
		e.preventDefault();
	});


	//头部显示控制区
	panelControl.on('click', '[data-action]', function(e) {
		var el = $(e.target),
			action = el.attr('data-action'),
			cls = '';
		switch (action) {
			//号温
			case 'temperature':
				cls = 'table-temperature';
				if (table.hasClass(cls)) {
					table.removeClass(cls);
				} else {
					table.addClass(cls);
				}
				break;
				//遗漏条
			case 'lost-post':
				cls = 'table-lost-post';
				if (table.hasClass(cls)) {
					table.removeClass(cls);
				} else {
					table.addClass(cls);
				}
				break;
				//辅助线
			case 'guides':
				cls = 'table-guides';
				if (chartTbody.hasClass(cls)) {
					chartTbody.removeClass(cls);
				} else {
					chartTbody.addClass(cls);
				}
				break;
				//遗漏数
			case 'lost':
				cls = 'table-lost';
				if (table.hasClass(cls)) {
					table.removeClass(cls);
				} else {
					table.addClass(cls);
				}
				break;
				//走势图
			case 'trend':
				cls = 'table-trend';
				if ($('body').hasClass(cls)) {
					$('body').removeClass(cls);
				} else {
					$('body').addClass(cls);
				}
				break;
			default:
				break;
		}
	});

	//选球
	$('#J-select-content').on('click', '[data-action]', function(e) {
		var el = $(e.target),
			action = el.attr('data-action');
		switch (action) {
			case 'addSelect':
				CHART.addSelectRow();
				break;
			case 'delSelectRow':
				CHART.delSelectRow(el.parent().parent());
				break;
			case 'selectBall':
				if (el.hasClass('ball-orange')) {
					el.removeClass('ball-orange');
				} else {
					el.addClass('ball-orange');
				}
				break;
			default:
				break;
		}
	});

	//时间戳期数读取
	timePeriods.click(function() {
		CHART.show();
	});

	//日期参数输入框
	$('#J-date-star').focus(function() {
		var dt = new bomao.DatePicker({
			startYear:(new Date()).getFullYear(),
			input: this,
			isShowTime: true
		});
		dt.show();
	});
	$('#J-date-end').focus(function() {
		var dt = new bomao.DatePicker({
			startYear:(new Date()).getFullYear(),
			input: this,
			isShowTime: true
		});
		dt.show();
	});

	//makeLinks
	if(CHART && CHART.sysConfig){
		$('#J-periods-data').find('a').click(function(e){
			var el = $(this),
			type = $.trim(el.attr('data-type')),
			value = $.trim(el.attr('data-value')),
			sysConfig = CHART.sysConfig ;
			switch(type){
				case 'count':
					CHART.getDataUrl = function(){
				                    var param = {
				                        'lottery_id':sysConfig.lotteryId,
				                        'num_type':sysConfig.wayId,
				                        'count':value
				                    },
				                    arr = [sysConfig.queryBaseUrl + '?'],
				                    p;
				                    for(p in param){
				                        if(param.hasOwnProperty(p)){
				                            arr.push(p + '=' + param[p]);
				                        }
				                    }
				                    return arr.join('&');
					};
				break;
				case 'today':
					CHART.getDataUrl = function(){
				                    var 
				                        date = new Date(Date.parse(sysConfig.nowTime.replace(/-/g, '/'))),
				                        start,
				                        end,
				                        arr,
				                        p,
				                        param;

				                        date.setHours(0);
				                        date.setMinutes(0);
				                        date.setMinutes(1);
				                        start = Date.parse(date)/1000;
				                        date.setHours(23);
				                        date.setMinutes(59);
				                        date.setMinutes(59);
				                        end = Date.parse(date)/1000;
				                        param = {
				                        'lottery_id':sysConfig.lotteryId,
				                        'num_type':sysConfig.wayId,
				                        'begin_time':start,
				                        'end_time':end
				                    };

				                    arr = [sysConfig.queryBaseUrl + '?'];
				                    p;
				                    for(p in param){
				                        if(param.hasOwnProperty(p)){
				                            arr.push(p + '=' + param[p]);
				                        }
				                    }
				                    return arr.join('&');
					};
				break;
				case 'day':
					CHART.getDataUrl = function(){
				                    var 
				                        date = new Date(Date.parse(sysConfig.nowTime.replace(/-/g, '/'))),
				                        start,
				                        end,
				                        arr,
				                        p,
				                        param;

				                        end = Date.parse(date)/1000;
				                        date.setDate(date.getDate() - Number(value));
				                        start = Date.parse(date)/1000;
				                        param = {
				                        'lottery_id':sysConfig.lotteryId,
				                        'num_type':sysConfig.wayId,
				                        'begin_time':start,
				                        'end_time':end
				                    };

				                    arr = [sysConfig.queryBaseUrl + '?'];
				                    p;
				                    for(p in param){
				                        if(param.hasOwnProperty(p)){
				                            arr.push(p + '=' + param[p]);
				                        }
				                    }
				                    return arr.join('&');
					};
				break;
				default:
				break;
			}
			CHART.show();
			el.parent().find('a').removeClass('current');
			el.addClass('current');
			e.preventDefault();
		});
	}

});