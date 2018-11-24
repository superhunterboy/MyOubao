(function(host, Event, undefined){
	var defConfig = {
		name:'orderWindow',
		//父类容器
		UIContainer:'#orderWindow',
		//自身游戏容器
		container : '',
		service : null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.service=cfg.service;
			me.current_menu = 0;
			me.cell_data = null;
			me.UIContainer = $(cfg.UIContainer);
			me.game = null;
			me.lastgame = null;
			me.current_order_data = null;
			me.container = $('<div class="order-panel order-panel-hide"></div>').appendTo($(me.UIContainer));
			me.userAccount = 0;
			me.bet_value_total = 0;
			me.win_value_total = 0;
			//当前订单数组
			me.cur_orders_arr = [];
			//当前撤单 订单id数组
			me.cur_cancel_orders_arr=[];
			//是否处于拖动
			me.drag=false;
			////记录鼠标点击的点 与 订单模块左上角的距离
			me._x = 0;
			me._y = 0;

			me.buildUI();

			me.addEvent('afert_select_order_menu' ,function(e ,data){
				me.showContent(data);
			});
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			//设置窗口位置
			me.container.css({
				left:(document.body.scrollWidth-$('.lucky-main-panel').outerWidth())/2 + $('.game').width() + 10
			});
			me.container.html(html_all.join(''));
		},
		//更新菜单信息
		updateContent:function(){
			var me = this;
			
			me.container.find('.order-city-name').html(me.game.name);
			me.container.find('.prize-id').html('NO.'+me.cell_data.prize_id);
			me.container.find('.bet-style').html(me.cell_data.bet_style);

			if(me.game.getCurrentPrize().currentPlayIndex == 1){
				if(me.container.find('.total-value').hasClass('total-value-hide')){
					me.container.find('.total-value').removeClass('total-value-hide').addClass('total-value-show');
				}
				me.container.find('.total-value').html(me.cell_data.ball);
			}else{
				if(me.container.find('.total-value').hasClass('total-value-show')){
					me.container.find('.total-value').removeClass('total-value-show').addClass('total-value-hide');
				}
				me.container.find('.total-value').addClass('total-value-hide');
			};
			me.container.find('.odds').html(me.cell_data.odds);

			me.container.find('.limit-value-lab').html("1.00-"+me.cell_data.extra+".00");

			me.getGameOrders(me.game.id);
		},
		//显示菜单
		showOrderWindow:function(game , cell_data){
			var me = this;

			me.game = game;
			//不同游戏之间 玩法切换时,还原前一次玩法面板
			me.container.find('.money-box').val('请输入下注金额');
			if(me.lastgame != me.game){
				if(me.lastgame){
					me.lastgame.getCurrentPrize().play_hezhi.reSet();
					me.lastgame.getCurrentPrize().play_zuhe.reSet();
				}
				me.lastgame = me.game;
			}

			me.cell_data = cell_data;

			me.current_order_data  = {
				"gameId":game.id,
				"isTrace":"0",
				"traceWinStop":"1",
				"traceStopValue":"1",
				"balls":[
					{
						"jsId":"1",
						"wayId":me.cell_data.play_id,
						"ball":me.cell_data.ball,
						"viewBalls":"",
						"num": "1",
						"type":cell_data.play_type,
						"onePrice":"1",
						"moneyunit":"1",
						"multiple":"1",
						"prize_group":me.game.prize_group
					}

				],
				"orders":{},
				"amount":'1',
				"_token":game._token
			};

			me.current_order_data["orders"][me.cell_data.prize_id] = 1;

			if(me.container.hasClass('order-panel-hide')){
				me.container.removeClass('order-panel-hide').addClass('order-panel-show');
				me.getUserAccount();
			}

			me.updateContent();
			// me.container.find('.money-box').focus();

			//默认切换成下注界面
			me.switchMenu(0);
		},
		//获取账号余额
		getUserAccount:function(){
			var me = this;
			me.service.getUserAccount(function(data){
				me.userAccount = parseFloat(data.data[0].data[0].data);

				me.userAccount = Math.round(me.userAccount*100)/100;
				var money = me.formatMoney(me.userAccount,2);

				me.container.find('.user-account').html(money);
				//强制刷新页面上方的余额
				$('#J-top-user-balance').html(money);
			});
		},
		//金额格式化
		formatMoney:function(money, digit){
			var tpMoney = '0.00';

			if(undefined != money){  
				tpMoney = money;  
			}

			tpMoney = new Number(tpMoney);  
			if(isNaN(tpMoney)){  
				return '0.00';  
			}

			tpMoney = tpMoney.toFixed(digit) + '';
			var re = /^(-?\d+)(\d{3})(\.?\d*)/;

			while(re.test(tpMoney)){  
				tpMoney = tpMoney.replace(re, "$1,$2$3")  
			}  

			return tpMoney;  
		},
		//关闭菜单
		closeOrder:function(){
			var me = this;
			//回复默认
			me.container.find('.money-box').val('请输入下注金额');
			if(me.container.hasClass('order-panel-show')){
				me.container.removeClass('order-panel-show').addClass('order-panel-hide');
			}
			me.switchMenu(0);
			me.container.find("input[name='all_select_box']").attr("checked",false);
			/*玩法样式要复原*/
			me.game.getCurrentPrize().play_hezhi.reSet();
			me.game.getCurrentPrize().play_zuhe.reSet();
		},
		//切换菜单
		switchMenu:function(index){
			var me = this;
			me.current_menu = index;
			if(index==1){
				me.container.find('.order-submit').html('确认撤单');
			}else{
				me.container.find('.order-submit').html('确认下单');
			}

			me.fireEvent('afert_select_order_menu' ,me.current_menu);
		},
		//切换标签，显示对应的标签内容
		showContent:function(data){
			var me = this;
			me.container.find('.tag-menu').removeClass('menu-active');
			me.container.find('.tag-menu-sign').removeClass('tag-menu-sign-active');
			me.container.find('.tag-menu').eq(data).addClass('menu-active');
			me.container.find('.tag-menu-sign').eq(data).addClass('tag-menu-sign-active');

			me.container.find('.details-panel').children().removeClass('content-show');
			me.container.find('.details-panel').children().eq(data).addClass('content-show');
			
		},
		//更新下注金额
		updateBetAmount:function(num){
			var me = this;
			me.current_order_data.balls[0].multiple = num;
		},
		//提交订单
		submitOrder:function(){
			var me = this;
			//更新下注金额
			if(me.container.find('.money-box').val() == "请输入下注金额"){
				me.updateBetAmount(1);
			}else{
				me.updateBetAmount(me.container.find('.money-box').val());
			}
			
			if((Number(me.current_order_data.balls[0].multiple)+Number(me.bet_value_total))>(me.cell_data.extra)){
				//提醒
				var message = new bomao.GameMessage();
				message.showTip('抱歉，下注金额已经到达最大额限！');
				var sNum = 1;
				var timer = setInterval(function(){
					sNum -= 1;
					if(sNum < 0){
						clearInterval(timer);
						message.hideTip();
					}
				}, 1 * 500);
			}else{
				me.service.sumbitOrder(me.game.id,me.current_order_data,function(data){
					//回复默认1元
					me.container.find('.money-box').val('1');
					//更新余额信息
					me.getUserAccount();
					//更新订单信息
					me.getGameOrders(me.game.id);

					if(data.isSuccess == 1){
						if(me.game.getCurrentPrize().currentPlayIndex == 0){
							me.game.getCurrentPrize().play_zuhe.updateBetButtonArray(me.cell_data.index , me.current_order_data.balls[0].multiple);
						}else{
							me.game.getCurrentPrize().play_hezhi.updateBetButtonArray(me.cell_data.index , me.current_order_data.balls[0].multiple);
						}
					}
				});
				me.closeOrder();
			}
		},
		//提交取消订单(撤单)
		submitCancelOrder:function(){
			var me = this;
			if(me.cur_cancel_orders_arr.length != 0){
				me.service.cancelOrder(me.cur_cancel_orders_arr , me.cell_data.prize_id , me.game.id , me.game._token , function(data){
					//订单数组
					var arr = [];
					for(var i in me.cur_orders_arr){
						arr.push(me.cur_orders_arr[i]);
					}
					
					//回复默认1元
					me.container.find('.money-box').val('1');
					//更新余额信息
					me.getUserAccount();
					//更新订单信息
					me.getGameOrders(me.game.id);

					if(data.isSuccess == 1){
						if(me.game.getCurrentPrize().currentPlayIndex == 0){
							for(var i in arr){
								for(var j in me.cur_cancel_orders_arr){
									if(arr[i].id == me.cur_cancel_orders_arr[j]){
										me.game.getCurrentPrize().play_zuhe.updateBetButtonArray(me.cell_data.index , -parseInt(arr[i].bet_value));
									}
								}
							}
			
						}else{
							for(var i in arr){
								for(var j in me.cur_cancel_orders_arr){
									if(arr[i].id == me.cur_cancel_orders_arr[j]){
										me.game.getCurrentPrize().play_hezhi.updateBetButtonArray(me.cell_data.index , -parseInt(arr[i].bet_value));
									}
								}
							}
						}
					}

					me.container.find("input[name='all_select_box']").attr("checked",false);

					me.cur_cancel_orders_arr = [];

					me.closeOrder();
				});
			}else{
				//提醒
				var message = new bomao.GameMessage();
				message.showTip('请选择至少一条订单');
				var sNum = 1;
				var timer = setInterval(function(){
					sNum -= 1;
					if(sNum < 0){
						clearInterval(timer);
						message.hideTip();
					}
				}, 1 * 500);
			}
		},
		//取消下注
		cancelOrder:function(){
			var me = this;
			me.container.find('.money-box').val('1');
			//更新下注金额
			me.updateBetAmount(1);
			//取消-强制关闭
			me.closeOrder();
		},
		//取消要撤单的订单
		cancelSelectOrder:function(){
			var me = this;
			me.cur_cancel_orders_arr = [];
			me.container.find("input[name='all_select_box']").prop("checked", false);
			me.container.find("input[name='cancel_order']").prop("checked", false);
			//取消-强制关闭
			me.closeOrder();
		},
		//获取某一游戏的订单信息
		getGameOrders:function(gameId){
			var me = this;
			me.container.find('.order-list-content-box').html('');
			me.bet_value_total = 0;
			me.win_value_total = 0;
			me.cur_orders_arr = [];
			me.service.getOrders(gameId , function(data){
				me.container.find('.order-list-content-box').html('');
				var ordersArray = data.data[0].data;
				me.updateOrderInformation(ordersArray);
			});
		},
		//显示当前奖期,所选号码的订单信息
		updateOrderInformation:function(orderData){
			var me = this;
			for(var i in orderData){
				if(me.cell_data.prize_id == orderData[i].number && orderData[i].status != "已撤销" && (me.cell_data.bet_style == orderData[i].balls || (me.cell_data.bet_style == '和'&&me.cell_data.ball == orderData[i].balls))){
					var win_value_num = (parseFloat(orderData[i].money)*parseFloat(me.cell_data.odds)).toFixed(2);
					var order = new bomao.Lucky28.order({id:orderData[i].id , bet_value:orderData[i].money , win_value:win_value_num});
					order.buildUI();
					me.cur_orders_arr.push(order);

					me.bet_value_total = parseFloat(me.bet_value_total) + parseFloat(orderData[i].money);
					me.win_value_total = parseFloat(me.win_value_total) + parseFloat(win_value_num);
				}
			}

			me.container.find('.win-value-lab').html(parseFloat(me.win_value_total).toFixed(2));
			me.container.find('.realy-bet-value-lab').html(parseFloat(me.bet_value_total).toFixed(2));

			if(me.cell_data.extra-me.bet_value_total <= 0){
				me.container.find('.limit-value-lab').html("0.00");
			}else{
				me.container.find('.limit-value-lab').html("1.00-"+(me.cell_data.extra-me.bet_value_total)+".00");
			}
		}
	};


	var html_all = [];
	html_all.push('<div class="order-menu-head">');
		html_all.push('<div class="close-order"></div>');
		html_all.push('<ul class="order-tag">');
			html_all.push('<li><span class="tag-menu menu-active" param="0">下单详情</span><span class="tag-menu-sign tag-menu-sign-active"></span></li>');
			html_all.push('<li><span class="tag-menu" param="1">快速撤单</span><span class="tag-menu-sign"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');
	html_all.push('<div class="order-content">');
		html_all.push('<ul>');
			html_all.push('<li class="money-information"><span>钱包：</span><span class="user-account"></span></li>');
			html_all.push('<li class="order-city"><span class="order-city-name"></span>&nbsp;&nbsp;<span class="prize-id"></span></li>');
			
			html_all.push('<li class="order-bet-information">');
				html_all.push('<ul class="bet-details">');
					html_all.push('<li><span class="bet-style">和</span></li>');
					html_all.push('<li><span class="total-value total-value-hide"></span></li>');
					html_all.push('<li><span>x&nbsp;</span><span class="odds"></span></li>');
				html_all.push('</ul>');
			html_all.push('</li>');
		html_all.push('</ul>');

		html_all.push('<span class="order-city-name order-city-name-lab"></span>');
		
		html_all.push('<div class="details-panel">');
			html_all.push('<div class="order-details content-show">');
				html_all.push('<ul >');
					html_all.push('<li>');

						html_all.push('<input class="money-box" type="text" value="请输入下注金额"></input>');
						html_all.push('<ul class="money-list">');
							html_all.push('<li param="all"><span></span></li>');
							html_all.push('<li class="normal-money-box" param="1"><span>1</span></li>');
							html_all.push('<li class="normal-money-box" param="2"><span>2</span></li>');
							html_all.push('<li class="normal-money-box" param="5"><span>5</span></li>');
							html_all.push('<li class="normal-money-box" param="10"><span>10</span></li>');
							html_all.push('<li class="normal-money-box" param="50"><span>50</span></li>');
							html_all.push('<li class="normal-money-box" param="100"><span>100</span></li>');
							html_all.push('<li class="normal-money-box" param="500"><span>500</span></li>');
						html_all.push('</ul>');

					html_all.push('</li>');
					html_all.push('<li class="bet-list-lab"><span>可赢金额: </span><span class="win-value-lab">0.00</span></li>');
					html_all.push('<li class="bet-list-lab"><span>已投金额: </span><span class="realy-bet-value-lab">0.00</span></li>');
					html_all.push('<li class="bet-list-lab"><span>下注限额: </span><span class="limit-value-lab">1.00-1000.00</span></li>');
				html_all.push('</ul>');
			html_all.push('</div>');
			//撤单信息面板
			html_all.push('<div class="revocation-list">');
				html_all.push('<ul class="order-list-head">');
					html_all.push('<li class="order-list-head-content">');
						html_all.push('<span>全选&nbsp;&nbsp;</span>');
						html_all.push('<input class="all-select-box" type="checkbox" name="all_select_box"/>');
					html_all.push('</li>');
					html_all.push('<li class="order-list-head-content"><span>下注金额</span></li>');
					html_all.push('<li class="order-list-head-content"><span>可赢金额</span></li>');
				html_all.push('</ul>');

				html_all.push('<div class="order-list-content-box"></div>');

			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="button-box">');
			//下注/撤单按钮
			html_all.push('<span class="order-submit">确认下单</span>');
			//取消 
			html_all.push('<span class="order-cancel">取消</span>');
		html_all.push('</div>');
	html_all.push('</div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);