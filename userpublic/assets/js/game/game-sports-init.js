(function($, host){


	var gameCase = new bomao.SportsGame();
		gameCase.initOption(global_sports_config['option']);
		gameCase.setMatchTypeValue($.trim($('#J-match-method-type-value').val()));
		gameCase.setMethodMixMaxCfg({
			'win':8,
			'handicapWin':8,
			'haFu':4,
			'correctScore':4,
			'totalGoals':6
		});


	var orderListDom = $('#J-order-list-cont'),
		matchListDom = $('#J-game-match');
	var MSG = host.Message.getInstance(),
		MASK = host.Mask.getInstance(),
		TIP = host.Tip.getInstance();

	gameCase.getMultiple = function(){
		return Number($('#J-input-multiple').val());
	};
	$('#J-input-multiple').keyup(function(){
		var v = Number(this.value.replace(/[^\d]/g, ''));
		v = v < 1 ? 1 : v;
		v = v > Number(global_sports_config_max['MaxMultiple']) ? Number(global_sports_config_max['MaxMultiple']) : v;
		this.value = v;
		gameCase.updateStatics();
	});
	$('#J-ct-multiple-reduce').click(function(){
		var v = gameCase.getMultiple();
		v -= 1;
		v = v <= 0 ? 1 : v;
		$('#J-input-multiple').val(v);
		gameCase.updateStatics();
	});
	$('#J-ct-multiple-add').click(function(){
		var v = gameCase.getMultiple();
		v += 1;
		$('#J-input-multiple').val(v);
		gameCase.updateStatics();
	});


	var updateOrderHtml = function(){
		var me = gameCase,
			orders = me.copyRebuildOrders(me.getOrders()),
			i = 0,
			len = orders.length,
			j = 0,
			len2,
			bet,
			firstIt,
			tempStr = '',
			orderTpl = $.trim($('#J-orders-row-tpl').html()),
			htmlOuter = [],
			htmlInner = [];

		for(i = 0; i < len; i++){
			firstIt = orders[i]['bets'][0];
			tempStr = host.util.template(orderTpl, firstIt);
			htmlInner = [];
			for(j = 0,len2 = orders[i]['bets'].length; j < len2; j++){
				bet = orders[i]['bets'][j];
				htmlInner.push('<span data-matchid="'+bet['matchid']+'" data-type="'+bet['type']+'" data-value="'+ bet['value'] +'" class="o-bet o-bet-'+ bet['type'] +' o-bet-'+ bet['type'] + '-' + bet['value']+ '">' + bet['name'] + '<b class="one-del">x</b></span>');
			}
			tempStr = tempStr.replace(/<#=items#>/g, htmlInner.join(''));
			tempStr = tempStr.replace(/<#=dancls#>/g, orders[i]['dan'] == 1 ? 'dan-active' : '');
			htmlOuter.push(tempStr);
		}
		orderListDom.html(htmlOuter.join(''));
	};

	gameCase.addEvent('after_addOrder', updateOrderHtml);
	gameCase.addEvent('after_delBet', function(e, matchid, type, value){
		updateOrderHtml();
		matchListDom.find('.row-list-' + matchid).find('.method-' + type).find('[data-value="'+ value +'"]').removeClass('item-active');
	});
	//动画
	var flyAnimation = function(fromDom, toDom, moveDom){
		var fromOffset = fromDom.offset(),
			toOffset = toDom.offset();
		toDom.hide();
		moveDom.css({
			left:fromOffset.left,
			top:fromOffset.top,
			opacity:0
		});
		moveDom.animate({
			left:toOffset.left,
			top:toOffset.top,
			opacity:1,
		}, 300, function(){
			moveDom.remove();
			toDom.show();
			$('#J-order-list-cont').scrollTop(10000);
		});
	};
	gameCase.addEvent('after_addOrder', function(){
		if(!lastClickItemDom){
			return;
		}
		var el = lastClickItemDom,
			pstr = el.attr('data-param'),
			params = formatParam(pstr),
			matchid = params['matchid'],
			type = params['type'],
			value = params['value'],
			targetDom = orderListDom.find('.o-match-' + matchid).find('.o-bet-' + type + '-' + value),
			moveDom = targetDom.clone().addClass('global-fly-bet');

		moveDom.appendTo(document.body);

		flyAnimation(lastClickItemDom, targetDom, moveDom);
	});


	orderListDom.on('click', '.fa-times-circle', function(e){
		var el = $(this),matchid = el.attr('data-matchid');
		gameCase.delOrder(matchid);
	});
	//胆
	orderListDom.on('click', '.dan', function(e){
		var el = $(this),matchid = el.attr('data-matchid'),CLS = 'dan-active';
		if(el.hasClass(CLS)){
			el.removeClass(CLS);
			gameCase.setDan(matchid, 0);
		}else{
			el.addClass(CLS);
			gameCase.setDan(matchid, 1);
		}
	});
	gameCase.addEvent('after_delOrder', function(e, matchid){
		var CLS = 'item-active';
		orderListDom.find('.o-match-' + matchid).remove();
		matchListDom.find('.row-list-' + matchid).find('.item').removeClass(CLS);
		updateClearButton();
	});
	gameCase.addEvent('after_delAll', function(e, matchid){
		var CLS = 'item-active';
		orderListDom.find('.o-match').remove();
		matchListDom.find('.row-list').find('.item').removeClass(CLS);
		updateClearButton();
	});

	orderListDom.on('click', '.o-bet', function(){
		var el = $(this),
			matchid = el.attr('data-matchid'),
			type = el.attr('data-type'),
			value = el.attr('data-value');
		gameCase.delBet(matchid, type, value);
		updateClearButton();
	});
	var updateClearButton = function(){
		if(orderListDom.children().size() < 1){
			$('#J-button-clearall').addClass('ct-clearall-disabled');
		}else{
			$('#J-button-clearall').removeClass('ct-clearall-disabled');
		}
	};
	//清空选号栏
	$('#J-button-clearall').click(function(e){
		e.preventDefault();
		gameCase.delAll();
	});




	matchListDom.on('click', '.item-more', function(){
		var el = $(this),
			matchid = el.attr('data-matchid'),
			row = matchListDom.find('.row-list-' + matchid),
			CLS = 'row-list-active',
			textDesc = el.attr('data-desc'),
			textAsc = el.attr('data-asc');
		if(row.hasClass(CLS)){
			row.removeClass(CLS);
			el.html(textDesc + ' <i class="fa fa-sort-desc"></i>');
		}else{
			row.addClass(CLS);
			el.html(textAsc + ' <i class="fa fa-sort-asc"></i>');
		}
	});


	gameCase.addEvent('after_update', function(e){
		var me = this,
			selects = me.getSelects(),
			orders = me.getOrders(),
			tabindex = me.getTabType(),
			types = me.getTypeOption(me.getOrdersLen(), tabindex == 0 ? false : true),
			panel = $('#J-order-type').find('.list').hide().eq(tabindex).show(),
			i = 0,
			len = types.length,
			selStr = '',
			html = [];
		for(i = 0; i < len; i++){
			if(selects[types[i]['code']]){
				selStr = ' checked="checked" ';
			}else{
				selStr = '';
			}
			html.push('<label><input '+ selStr +' class="ct-select" data-code="'+ types[i]['code'] +'" type="checkbox" value="'+ types[i]['code'] +'" /> '+ types[i]['name'] +'</label>');
		}
		panel.html(html.join(''));

	});

	gameCase.addEvent('after_update_statics', function(){
		var me = this,
			multiple = me.getMultiple(),
			num = me.getCount();
		$('#J-times-match').text(me.getOrdersLen());
		$('#J-bets-num').text(num);

		$('#J-money-num').text(host.util.formatMoney(num * multiple * 2));
	});

	gameCase.addEvent('after_countMaxPrize', function(e, num){
		var me = this,
			maxPrize = me.getMaxPrize();
		$('#J-prize-max').text(host.util.formatMoney(maxPrize));
	});



	$('#J-order-type').find('.tab li').click(function(){
		var el = $(this),
			index = $('#J-order-type').find('.tab li').index(this);
		if(index == gameCase.getTabType()){
			return;
		}
		$('#J-order-type').find('.tab li').removeClass('active');
		el.addClass('active');
		gameCase.setTabType(index);
	});


	$('#J-order-type').on('click', '.ct-select', function(){
		var el = $(this),
			items = el.parent().parent().find('.ct-select'),
			result = [];
			
			if(items.attr('value')=='1_1' && items.is(':checked')){
				$('.link-detail').hide();
			}else{
				$('.link-detail').show();
			}
		items.each(function(){
			if(this.checked){
				result.push($(this).attr('data-code'));
			}
		});
		if(result.length > 5){
			MSG.show({
				isShowMask:true,
				closeIsShow:true,
				content:'最多可选择5种过关方式',
				closeFun:function(){
					MSG.hide();
				}
			});
			el.get(0).checked = false;
		}else{
			gameCase.setSelects(result);
		}
		
	});


	var formatParam = function(param){
		var arr = $.trim(param).split('&'),i = 0,len = arr.length,
			paramArr = [],
			result = {};
		for(;i < len;i++){
			paramArr = arr[i].split('=');
			if(paramArr.length > 0){
				if(paramArr.length == 2){
					result[paramArr[0]] = paramArr[1];
				}else{
					result[paramArr[0]] = '';
				}
			}
		}
		return result;
	};
	var lastClickItemDom = null;
	$(document).on('click', '[data-param]', function(e){
		var el = $(this),
			pstr = el.attr('data-param'),
			params = formatParam(pstr),
			matchid = params['matchid'],
			CLS = 'item-active';

		//console.log(gameCase.checkOrderMatchLen(matchid));
		if(params['action'] == 'addOrder' && !gameCase.checkOrderMatchLen(matchid) && !el.hasClass(CLS)){
			MSG.show({
				isShowMask:true,
				closeIsShow:true,
				content:'最多可以选择15场比赛',
				closeFun:function(){
					MSG.hide();
				}
			});
			return;
		}


		if($.isFunction(gameCase[params['action']])){
			if(el.hasClass(CLS)){
				el.removeClass(CLS);
			}else{
				el.addClass(CLS);
				if(params['action'] == 'addOrder'){
					lastClickItemDom  = el;
				}
			}
			gameCase[params['action']].call(gameCase, params);
		}


		updateClearButton();
	});




	//显示截止时间还是比赛时间
	matchListDom.on('click', '.ct-select-timetype', function(e){
		var el = $(this),
			type = $.trim(el.attr('data-type'));
		e.preventDefault();
		matchListDom.find('.time-type').hide().filter('.time-type-' + type).show();
		el.parent('.col-time').removeClass('active');

		matchListDom.find('.ct-select-timetype').removeClass('active');
		el.addClass('active');

		matchListDom.find('.col-time').find('span').eq(0).text(el.attr('data-text'));
	});



	//赛事筛选
	matchListDom.on('click', '.ct-select-league', function(e){
		var el = $(this),
			CLS = 'selected',
			leagueid = el.attr('data-id');
		e.preventDefault();
		if(el.hasClass(CLS)){
			matchListDom.find('.row-list-league-' + leagueid).hide();
			el.removeClass(CLS);
		}else{
			matchListDom.find('.row-list-league-' + leagueid).show();
			el.addClass(CLS);
		}
	});
	matchListDom.on('click', '.ct-select-all', function(e){
		e.preventDefault();
		var CLS = 'selected';
		matchListDom.find('.r').show();
		matchListDom.find('.ct-select-league').addClass(CLS);
	});
	matchListDom.on('click', '.ct-select-none', function(e){
		e.preventDefault();
		var CLS = 'selected';
		matchListDom.find('.r').hide();
		matchListDom.find('.ct-select-league').removeClass(CLS);
	});


	//让球显示
	matchListDom.on('click', '.ct-select-handicap', function(e){
		var el = $(this),
			CLS = 'game-match-status-handicap'
			type = $.trim(el.attr('data-type')),
			cls_all = CLS + '-all',
			cls_yes = CLS + '-yes',
			cls_no = CLS + '-no';
		e.preventDefault();

		matchListDom.removeClass(cls_all);
		matchListDom.removeClass(cls_yes);
		matchListDom.removeClass(cls_no);
		matchListDom.addClass(CLS + '-'+type);


		matchListDom.find('.ct-select-handicap').removeClass('active');
		el.addClass('active');

		matchListDom.find('.col-handicap').find('span').eq(0).text(el.attr('data-text'));
	});


	//日期收缩
	matchListDom.on('click', '.row-date .ct', function(e){
		var el = $(this),date = el.attr('data-date'),lists;
		e.preventDefault();
		lists = matchListDom.find('.row-list-date-' + date);
		lists_finish_match = matchListDom.find('.isStop');
		if(el.hasClass('ct-open')){
			lists.show();
			//将已截止的比赛，再判断一次是否显示
			if($('.finish-box').get(0).checked){
				lists_finish_match.show();
			}else{
				lists_finish_match.hide();
			}
			el.text('收起');
			el.removeClass('ct-open');
		}else{
			lists.hide();
			el.text('展开');
			el.addClass('ct-open');
		}
	});


	
	function getToken(){
		return $('#J-form').find('[name="_token"]').val();
	}
	function readySubmit(data){
		var form = $('#J-form');
		form.find('[name="gameData"]').val(data['orders']);
		form.find('[name="gameExtra"]').val(data['selects']);
		form.find('[name="betTimes"]').val(data['multiple']);
		return data;
		/**
		return {
			'_token':getToken(),
			'gameData':data['orders'],
			'gameExtra':data['selects'],
			'betTimes':data['betnum']
		};
		**/
	}
	function blinkSelects(){
		var dom = $('#J-order-type .list'),
			timer = null,
			i = 6;
		timer = setInterval(function(){
			if(i > 0){
				if(i%2==0){
					dom.addClass('list-blink');
				}else{
					dom.removeClass('list-blink');
				}
			}else{
				dom.removeClass('list-blink');
				clearInterval(timer);
			}
			i--;
		}, 100);
	}
	$('#J-button-submit').click(function(){
		var form = $('#J-form'),
			data = gameCase.getSubmitData(),
			ordernum = 0,
			submaxnum = Number(global_sports_config_max['MaxCount']),
			submaxmoney = Number(global_sports_config_max['MaxAmount']);
		form.find('[name="is_group_buy"]').val(0);
		data = readySubmit(data);

		if(data['betnum'] > submaxnum){
            MSG.show({
                title:'系统提示',
                content:'您输入的投注注数超过限制: <span class="c-red">' + submaxnum + '</span> 注',
                closeIsShow:true,
                isShowMask:true,
                closeFun:function(){
                    MSG.hide();
                    MASK.hide();
                }
            });
            setTimeout(function(){
            	MASK.show();
            }, 100);
			return false;
		}
		if(data['money'] * data['multiple'] > submaxmoney){
            MSG.show({
                title:'系统提示',
                content:'您的投注金额超过限制: <span class="c-yellow">' + host.util.formatMoney(submaxmoney) + '</span> 元',
                closeIsShow:true,
                isShowMask:true,
                closeFun:function(){
                    MSG.hide();
                    MASK.hide();
                }
            });
            setTimeout(function(){
            	MASK.show();
            }, 100);
			return false;
		}


		if(gameCase.getOrders().length > 0 && data['betnum'] == 0){
			blinkSelects();
		}else{
			if(data['betnum'] > 0){
				form.submit();
			}
		}

	});
	$('#J-button-submit-group').click(function(){
		var form = $('#J-form'),
			data = gameCase.getSubmitData(),
			submaxnum = Number(global_sports_config_max['MaxCount']),
			submaxmoney = Number(global_sports_config_max['MaxAmount']);
		form.find('[name="is_group_buy"]').val(1);
		data = readySubmit(data);


		if(data['betnum'] > submaxnum){
            MSG.show({
                title:'系统提示',
                content:'您输入的投注注数超过限制: <span class="c-red">' + submaxnum + '</span> 注',
                closeIsShow:true,
                isShowMask:true,
                closeFun:function(){
                    MSG.hide();
                    MASK.hide();
                }
            });
            setTimeout(function(){
            	MASK.show();
            }, 100);
			return false;
		}
		if(data['money'] * data['multiple'] > submaxmoney){
            MSG.show({
                title:'系统提示',
                content:'您输入的投注金额超过限制: <span class="c-yellow">' + host.util.formatMoney(submaxmoney) + '</span> 元',
                closeIsShow:true,
                isShowMask:true,
                closeFun:function(){
                    MSG.hide();
                    MASK.hide();
                }
            });
            setTimeout(function(){
            	MASK.show();
            }, 100);
			return false;
		}


		if(gameCase.getOrders().length > 0 && data['betnum'] == 0){
			blinkSelects();
		}else{
			if(data['betnum'] > 0){
				form.submit();
			}
		}
	});

	




	(function(){
		var panel = $('#J-panel-main-side'),
			offset = panel.offset(),
			oldtop = offset.top,
			oldleft = offset.left,
			win = $(window),
			CLS = 'panel-main-side-fixed';
		var fn = function(){
			var wtop = $(window).scrollTop();
			if((oldtop - wtop) < 0 && !panel.hasClass(CLS)){
				panel.addClass(CLS);
				panel.css('left', oldleft);
				if(panel.height() >= $(window).height()){
					panel.css('bottom', 0);
				}else{
					panel.css('top', 0);
				}
			}
			if((oldtop - wtop) >= 0 && panel.hasClass(CLS)){
				panel.removeClass(CLS);
			}
		};
		win.scroll(fn);
		win.resize(fn);
	})();


	/*天气状况提示信息*/
	matchListDom.on('mouseover','.r .col-weather img',function(e){
		TIP.setText(this.getAttribute("detail"));
		TIP.show(30,0,this);
	});

	matchListDom.on('mouseout','.r .col-weather img',function(e){
		TIP.hide();
	});

	/*时间提示信息*/
	matchListDom.on('mouseover','.time-type',function(e){
		TIP.setText(this.getAttribute("detail"));
		TIP.show(40,-15,this);
	});

	matchListDom.on('mouseout','.time-type',function(e){
		TIP.hide();
	});



	/**
	function submitOrder(data){
		$.ajax({
			url:'',
			dataType:'json',
			method:'POST',
			data:data,
			beforeSend:function(){
				MASK.show();
			},
			success:function(data){
				if(Number(data['isSuccess']) == 1){
					msg_show_success(data['data']);
				}else{
					alert(data['msg']);
				}
			},
			error:function(xhr, type){
				alert('数据提交失败:' + type);
			},
			complete:function(){
				MASK.hide();
			}
		});
	}
	function msg_show_success(){
		MSG.show({
			isShowMask:true,
			closeIsShow:true,
			content:'注单提交成功',
			closeFun:function(){
				MSG.hide();
			}
		});
	}
	



	$('#J-order-list-outer').jScrollPane({
		autoReinitialise: true,
		showArrows:true,
		animateScroll:true,
		animateDuration:100
	});
	**/


	/*新增筛选历史数据结果*/
	var row_date_select = $('.row-date-select');
		current_date = $('.current-date');
		date_select_box = $('.date-select-box');
		date_list = $('.date-list');
		arrow_box = $('.arrow-box');

	/*显示/关闭日期列表*/
	row_date_select.on('click', '.date-select-box', function() {
		if(date_list.hasClass('open-list')){
			date_list.removeClass('open-list');
			date_list.addClass('close-list');

			arrow_box.html('<span class="fa fa-sort-desc"></span>');
		}else{
			date_list.removeClass('close-list');
			date_list.addClass('open-list');

			arrow_box.html('<span class="fa fa-sort-asc"></span>');
		}
	});

	/*选择历史日期*/
	date_list.on('click', 'li', function() {
		date_list.removeClass('open-list');
		date_list.addClass('close-list');
		
		current_date.html($(this).text());
	});

	/*已截止比赛按钮*/
	var his_match = $('.finished-match');
	var finish_match = $('.isStop');
	//含有ct-open类，表示列表已收起
	var bt_list = $('.ct');
	his_match.on('click', '.finish-box', function() {
		var isClose = bt_list.hasClass('ct-open');
		if(!isClose){
			if(this.checked){
				finish_match.show();
			}else{
				finish_match.hide();
			}
		}
	});

	/*跳转至明细窗口*/
	var link_detail = $('.link-detail');

	link_detail.click(function() {
	    //下注金额
	    var bet_num = $('#J-bets-num').html();

	    if (Number(bet_num) > 0 && !$(":checkbox[value='1_1']").is(":checked")) {
	        gameCase.analyseMatchDetail();
	        window.open('./prize_detail');
	    }
	});


})(jQuery, bomao);