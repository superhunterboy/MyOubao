
(function(host, name, Event, undefined){
	var defConfig = {
		//ID
		id : -1,
		//游戏名称
		name : '',
		//父类容器
		UIContainer:'',
		//自身游戏容器
		container : '',
		gameMothed:null,
		clock:null,
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.id = cfg.id;
			me.name = cfg.name;
			me._token = '';
			me.prize_group = '';
			me.curPrizeIndex = 0;
			//数据模型
			me.tag_data = [0,-1,-1];
			//游戏赔率数据
			me.gameMothed=cfg.gameMothed;
			//构建游戏模型
			me.UIContainer = $(cfg.UIContainer);
			me.container = $('<div class="game-panel"></div>').appendTo(me.UIContainer);
			me.container.html(html_all.join(''));
			//游戏订单
			me.game_orders = [];
			//游戏初始化
			me.initload = true;
			//时钟
			me.clock = cfg.clock;
			//最大下注限额
			me.bet_max_amount = 0;

			//初始化订单窗口(为每个游戏配置一个下单窗口)
			// me.order_widnow = new bomao.Lucky28.list.orderWindow({'parentGame':me});

			//初始化历史走势
			me.mini_history = new bomao.Lucky28.list.miniHistory({'parentGame':me});

			//数据对象数组
			me.priedIDArr = [];
			//奖期对象数组
			me.caches = [];
			//当前奖期
			me.currentPrize = null;
			//动画中
			me.isAnimating = false;

		},
		getId:function(){
			return this.id;
		},
		//设置ID
		setId:function(id){
			this.id = Number(id);
		},
		getName:function(){
			return this.name;
		},
		//设置游戏名称
		setName:function(name){
			this.name = name;
		},
		//玩法的父类容器
		getPlayContainer:function(){
			var me = this;
			return me.PlayContainer || (me.PlayContainer  = $(me.defConfig.PlayContainer));
		},
		//添加奖期
		addPrize:function(prizeId,leftTime,cycleTime,result_num,entertainedTime){
			var me = this;
			var bet_game  = null;

			if(result_num != ''){
				//奖期号，剩余时间，游戏，奖期状态
				bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':4,'entertainedTime':entertainedTime});
				
				var result_data = {
					'num_1':Number(result_num.charAt(0)),
					'num_2':Number(result_num.charAt(1)),
					'num_3':Number(result_num.charAt(2)),
					'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
				}
				bet_game.result_number = result_data;
				bet_game.information_result.updateResult(result_data);
			}else{
				//判断是否处于停盘状态
				if(leftTime>cycleTime){
					bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':5,'entertainedTime':entertainedTime});
				}else{
					bet_game = new bomao.Lucky28.list.prizePeriod({'prize_id':prizeId,'leftTime':leftTime,'cycleTime':cycleTime,'parentGame':me,'status':0,'entertainedTime':entertainedTime});
				}
			}

			if(me.initload){
				me.currentPrize = bet_game;
			}

			if(me.initload){
				for(var i=0;i<me.game_orders.length;i++){
					if(me.game_orders[i].number == prizeId && me.game_orders[i].status != "已撤销"){
						bet_game.prize_orders.push(me.game_orders[i]);
					}
				}
				//对投注记录进行解析
				bet_game.analyzeOrderRecords();
			}

			//数组保留3期数据
			if(me.priedIDArr.length == 3){
				var del_prize = me.container.find('.bet-history-content').children().eq(0).get(0);
				del_prize.parentNode.removeChild(del_prize);
			}
			if(me.priedIDArr.length > 3){
				me.priedIDArr.pop();
				me.caches.pop();

				var del_prize = me.container.find('.bet-history-content').children().eq(0).get(0);
				del_prize.parentNode.removeChild(del_prize);
			}
			//添加一期奖期数据
			me.priedIDArr.unshift(prizeId);
			me.caches.unshift(bet_game);
			
			bet_game.container.css('display','none');

			me.updataTags(me.tag_data);
		},
		//切换奖期
		switchPrize:function(index){
			var me = this;
			//获取将要展示的对象群组结构
			i=0,
			len = me.tag_data.length;
			
			for(;i<len;i++){
				if(i == index){
					me.tag_data[index] = 0;
				}else{
					me.tag_data[i] = -1;
				}
			}

			if(index != 0){
				me.container.find('.przie-left-time').removeClass('przie-left-time-hide').addClass('przie-left-time-show');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-2');

			}else{
				me.container.find('.przie-left-time').removeClass('przie-left-time-show').addClass('przie-left-time-hide');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-1');
			}

			me.lastPrize = me.currentPrize;

			me.currentPrize = me.getPrizePeriodByNumber(index);
			me.fireEvent('afert_select_recompense' ,me.tag_data);

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("webkitAnimationStart",function(){
				me.isAnimating = true;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("animationstart",function(){
				me.isAnimating = true;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-up-move');
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-down-move');

				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-up-move-miss');
				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-down-move-miss');

				if(me.lastPrize.prize_id != me.currentPrize.prize_id){
					me.container.find('.prize-id-'+me.lastPrize.prize_id).hide();
				}

				me.isAnimating = false;
			});

			me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).get(0).addEventListener("animationend",function(){
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-up-move');
				me.container.find('.prize-id-'+me.getPrizePeriodByNumber(index).prize_id).removeClass('prize-down-move');

				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-up-move-miss');
				me.container.find('.prize-id-'+me.lastPrize.prize_id).removeClass('prize-down-move-miss');
				
				if(me.lastPrize.prize_id != me.currentPrize.prize_id){
					me.container.find('.prize-id-'+me.lastPrize.prize_id).hide();
				}

				me.isAnimating = false;
			});
		},
		//自动切换将期
		autoSwitchPrize:function(index){
			var me = this;

			//获取将要展示的对象群组结构
			i=0,
			len = me.tag_data.length;
			
			for(;i<len;i++){
				if(i == index){
					me.tag_data[index] = 0;
				}else{
					me.tag_data[i] = -1;
				}
			}

			if(index != 0){
				me.container.find('.przie-left-time').removeClass('przie-left-time-hide').addClass('przie-left-time-show');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-2');

			}else{
				me.container.find('.przie-left-time').removeClass('przie-left-time-show').addClass('przie-left-time-hide');

				me.container.find('.play-button').removeClass().addClass('play-button').addClass('play-button-1');
			}

			me.currentPrize = me.getPrizePeriodByNumber(index);
			me.fireEvent('auto_switch_recompense' ,me.tag_data);
		},
		//获取当前奖期
		getPrizePeriodByNumber:function(index){
			var me = this;
			for(var i in me.caches){
				if(i== index){
					return me.caches[i];
				}
			}
			return me.caches[0] ;
		},
		//获取当前奖期数据
		getPrizePeriodDataByNumber:function(index){
			var me = this;
			for(var i in me.priedIDArr){
				if(i== index){
					return me.priedIDArr[i];
				}
			}
			return me.priedIDArr[0] ;
		},
		//获取当前奖期
		getCurrentPrize:function(){
			var me = this;
			return me.currentPrize;
		},
		//获取当前奖期Dom节点
		getCurrentPrizeDOM:function(index){
			var me = this;
			return me.container.find(".bet-history-panel");
		},
		//显示下注窗口
		// showOrderWindow:function(cell_data){
		// 	var me = this;
		// 	me.container.find('.order-panel').removeClass('order-panel-hide').addClass('order-panel-show');

		// 	me.order_widnow.updateContent(cell_data);
		// },
		//隐藏下注窗口
		// hideOrderWindow:function(){
		// 	var me = this;
		// 	me.container.find('.order-panel').removeClass('order-panel-show').addClass('order-panel-hide');
		// },
		//更新tag标签内容
		updataTags:function(tag_data){
			var me = this;
			var tags = me.container.find('.tag-lab');

			for(var i=0 ; i<3 ; i++){
				if(me.priedIDArr[i]){
					if(i==0){
						tags.eq(i).text('No.'+me.priedIDArr[i]);
					}else{
						tags.eq(i).text('No...'+me.priedIDArr[i].substring(me.priedIDArr[i].length - 3));
					}
				}
			}

			me.showPrizesStatus();
		},
		//显示奖期的状态
		showPrizesStatus:function(){
			var me = this;

			if(me.caches){
				for(var i in me.caches){
					var class_str = '.lab-'+i;
					me.container.find(class_str).html(me.analysisStatus(me.caches[i].status));
				}
			}
		},
		//根据奖期的状态返回文字显示
		analysisStatus:function(prizeStatus){
			var me = this;

			var lab = '';
			switch(prizeStatus){
				case 0 : lab = '受注中';break;
				case 1 : lab = '已封盘';break;
				case 2 : lab = '待开奖';break;
				case 3 : lab = '开奖中';break;
				case 4 : lab = '已开奖';break;
				case 5 : lab = '已停盘';break;
				default : lab = '已取消';break;
			}

			return lab;
		},
		//当前奖期的剩余时间
		showDeadLine:function(endTime){
			var me = this;
			me.leftEndTime = endTime;
			me.container.find('.przie-left-time-lab').html(endTime);
			me.clock.updataLeftTime(me.leftEndTime);
		},
		//通过奖期号获取奖期对象
		getPrizeObjByPrizeId:function(prizeId){
			var me = this;
			for(var i in me.caches){
				if(me.caches[i].prize_id == prizeId){
					return me.caches[i];
				}
			}
		},
		//更新下注信息
		updateBetInformation:function(prizeId , data){
			var me = this;
			me.game_orders = data.data[0].data;

			//根据奖期号获取奖期对象
			var prizeObj = me.getPrizePeriodByNumber(prizeId);
			prizeObj.prize_orders = [];

			for(var i=0;i<me.game_orders.length;i++){
				if(me.game_orders[i].number == prizeId && me.game_orders[i].status != "已撤销"){
					prizeObj.prize_orders.push(me.game_orders[i]);
				}
			}
			
			//对投注记录进行解析
			prizeObj.analyzeOrderRecords();

		}
		
	};

	//html模板
	var html_all = [];

	html_all.push('<div class="bet-history-panel">');
		html_all.push('<ul class="bet-history-nav">');
			html_all.push('<li class="current-recompense recompense-selected" data-param="0">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-0"></span>');
				html_all.push('<span class="przie-left-time przie-left-time-hide">[<span class="przie-left-time-lab"></span>s]</span>');
			html_all.push('</li>');


			html_all.push('<li class="history-recompense" data-param="1">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-1"></span>');
			html_all.push('</li>');


			html_all.push('<li class="history-recompense his-r-2" data-param="2">');
				html_all.push('<span class="tag-lab"></span>');
				html_all.push('<span class="prize-status-lab lab-2"></span>');
			html_all.push('</li>');
		html_all.push('</ul>');

		html_all.push('<ul class="play-choose play-choose-select-0">');
			html_all.push('<li class="play-button play-button-1" data-param="0"></li>');
			html_all.push('<li class="play-button play-button-1" data-param="1"></li>');
		html_all.push('</ul>');

		html_all.push('<div class="odds-explain-list odds-list-normal">');
			html_all.push('<ul class="odds-explain-list-menu">');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
				html_all.push('<li class="odds-menu">和值</li>');
				html_all.push('<li class="odds-menu">赔率</li>');
			html_all.push('</ul>');

			html_all.push('<div class="odds-list-box">');
				html_all.push('<ul class="odds-list-content">');
					$.each([0,1,2,3,4,5,6,7,8,9],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');

				html_all.push('<ul class="odds-list-content">');
					$.each([10,11,12,13,14,15,16,17,18,19],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');

				html_all.push('<ul class="odds-list-content">');
					$.each([20,21,22,23,24,25,26,27],function(){
						html_all.push('<li>');
							html_all.push('<span class="odds-content-num">'+this+'</span>');
							html_all.push('<span class="odds-content odds-content-'+this+'"></span>');
						html_all.push('</li>');
					});
				html_all.push('</ul>');
			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="bet-history-content">');
		html_all.push('</div>');
	html_all.push('</div>');

	html_all.push('<div class="trend-panel"></div>');




	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao, "GameBase", bomao.Event);
