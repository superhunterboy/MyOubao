(function(host, $, undefined){

	var areasConfig=[
	{id:1, name_en:'long-dan', name_cn:'龙单', bet_max:10000.00, prize_odds:0.75, width: 157, height: 120, left: 0,top: 0, bgPosition:[0,0], oddsPos:[68,83]},
	{id:1, name_en:'long-shuang', name_cn:'龙双',bet_max:10000.00, prize_odds:1.05, width: 157, height: 168, left: 0,top: 135,bgPosition:[0,-132], oddsPos:[68,83]},
	{id:1, name_en:'long', name_cn:'龙',bet_max:10000.00, prize_odds:1, width: 202, height: 218, left: 173,top: 0,bgPosition:[-170,0], oddsPos:[75,163]},
	{id:1, name_en:'long-hong', name_cn:'龙红',bet_max:10000.00, prize_odds:0.9, width: 94, height: 104, left: 173,top: 232,bgPosition:[-170,-230], oddsPos:[28,62]},
	{id:1, name_en:'long-hei', name_cn:'龙黑',bet_max:10000.00, prize_odds:0.9, width: 94, height: 120, left: 280,top: 234,bgPosition:[-277,-232], oddsPos:[28,62]},
	{id:1, name_en:'he', name_cn:'和',bet_max:10000.00, prize_odds:8, width: 405, height: 112, left: 397,top: 260,bgPosition:[-391,-257], oddsPos:[268,43]},	
	{id:1, name_en:'hu-dan', name_cn:'虎单', bet_max:10000.00, prize_odds:0.75, width: 157, height: 120, right: 0,top: 0, bgPosition:[-1026,0], oddsPos:[51,83]},
	{id:1, name_en:'hu-shuang', name_cn:'虎双',bet_max:10000.00, prize_odds:1.05, width: 157, height: 168, right: 0,top: 135,bgPosition:[-1026,-132], oddsPos:[51,83]},
	{id:1, name_en:'hu', name_cn:'虎',bet_max:10000.00, prize_odds:1, width: 202, height: 218, right: 173,top: 0,bgPosition:[-810,0], oddsPos:[84,163]},
	{id:1, name_en:'hu-hong', name_cn:'虎红',bet_max:10000.00, prize_odds:0.9, width: 94, height: 104, right: 173,top: 235,bgPosition:[-918,-231], oddsPos:[27,62]},
	{id:1, name_en:'hu-hei', name_cn:'虎黑',bet_max:10000.00, prize_odds:0.9, width: 94, height: 120, right: 279,top: 235,bgPosition:[-812,-231], oddsPos:[27,62]},
	]




	var audioConfig = [
		// 筹码音效名称以及对应的音效地址
		{'name':'chipToPlayer','url':"/assets/images/game/table/dice/chipToPlayer.mp3"},
		{'name':'chipToTable','url':"/assets/images/game/table/dice/chipToTable.mp3"},
		{'name':'timeoutTips','url':"/assets/images/game/table/dice/timeoutTips.mp3"},
		{'name':'diceRolling','url':"/assets/images/game/table/dice/diceRolling.mp3"}
	];


	// 和后台协议的结果：52张牌的点数、红黑、单双情况
	var puker = {
		'01':{honghei:'hong',danshuang:'dan',data:1},
		'02':{honghei:'hong',danshuang:'shuang',data:2},
		'03':{honghei:'hong',danshuang:'dan',data:3},
		'04':{honghei:'hong',danshuang:'shuang',data:4},
		'05':{honghei:'hong',danshuang:'dan',data:5},
		'06':{honghei:'hong',danshuang:'shuang',data:6},
		'07':{honghei:'hong',danshuang:'dan',data:7},
		'08':{honghei:'hong',danshuang:'shuang',data:8},
		'09':{honghei:'hong',danshuang:'dan',data:9},
		'10':{honghei:'hong',danshuang:'shuang',data:10},
		'11':{honghei:'hong',danshuang:'dan',data:11},
		'12':{honghei:'hong',danshuang:'shuang',data:12},
		'13':{honghei:'hong',danshuang:'dan',data:13},

		'14':{honghei:'hong',danshuang:'dan',data:1},
		'15':{honghei:'hong',danshuang:'shuang',data:2},
		'16':{honghei:'hong',danshuang:'dan',data:3},
		'17':{honghei:'hong',danshuang:'shuang',data:4},
		'18':{honghei:'hong',danshuang:'dan',data:5},
		'19':{honghei:'hong',danshuang:'shuang',data:6},
		'20':{honghei:'hong',danshuang:'dan',data:7},
		'21':{honghei:'hong',danshuang:'shuang',data:8},
		'22':{honghei:'hong',danshuang:'dan',data:9},
		'23':{honghei:'hong',danshuang:'shuang',data:10},
		'24':{honghei:'hong',danshuang:'dan',data:11},
		'25':{honghei:'hong',danshuang:'shuang',data:12},
		'26':{honghei:'hong',danshuang:'dan',data:13},

		'27':{honghei:'hei',danshuang:'dan',data:1},
		'28':{honghei:'hei',danshuang:'shuang',data:2},
		'29':{honghei:'hei',danshuang:'dan',data:3},
		'30':{honghei:'hei',danshuang:'shuang',data:4},
		'31':{honghei:'hei',danshuang:'dan',data:5},
		'32':{honghei:'hei',danshuang:'shuang',data:6},
		'33':{honghei:'hei',danshuang:'dan',data:7},
		'34':{honghei:'hei',danshuang:'shuang',data:8},
		'35':{honghei:'hei',danshuang:'dan',data:9},
		'36':{honghei:'hei',danshuang:'shuang',data:10},
		'37':{honghei:'hei',danshuang:'dan',data:11},
		'38':{honghei:'hei',danshuang:'shuang',data:12},
		'39':{honghei:'hei',danshuang:'dan',data:13},

		'40':{honghei:'hei',danshuang:'dan',data:1},
		'41':{honghei:'hei',danshuang:'shuang',data:2},
		'42':{honghei:'hei',danshuang:'dan',data:3},
		'43':{honghei:'hei',danshuang:'shuang',data:4},
		'44':{honghei:'hei',danshuang:'dan',data:5},
		'45':{honghei:'hei',danshuang:'shuang',data:6},
		'46':{honghei:'hei',danshuang:'dan',data:7},
		'47':{honghei:'hei',danshuang:'shuang',data:8},
		'48':{honghei:'hei',danshuang:'dan',data:9},
		'49':{honghei:'hei',danshuang:'shuang',data:10},
		'50':{honghei:'hei',danshuang:'dan',data:11},
		'51':{honghei:'hei',danshuang:'shuang',data:12},
		'52':{honghei:'hei',danshuang:'dan',data:13}
	};







	var game = new bomao.TableGame.Lhd();
	game.initDeskTop(areasConfig);

	game.setConfig(global_game_config);

	
	game.getRealTimeGameInfo();


	var historyRecords = game.getConfig('historyNumbers').reverse();
	var trand = new bomao.TableGame.LhdHistory({records:historyRecords,puker:puker}); 




	//从后台开奖结果获得扑克牌点数
	var get_poker_config_real_value = function(numstr){
		var num = Number(numstr),
			num = num < 10 ? '0'+num : ''+num;
		return puker[num]['data'];
	};
	//从后台开奖数据中获得某一个投注区域编号对应的数据对象
	var get_result_data_by_area = function(numstr, data){
		var numstr = '' + Number(numstr),
			hash = {};
		for(var p in data['bet_prize']){
			if(data['bet_prize'].hasOwnProperty(p)){
				for(var k in data['bet_prize'][p]){
					hash[k] = data['bet_prize'][p][k];
				}
			}
		}
		return hash[numstr];
	};
	//将后台的开奖结果数据对象进行处理
	//处理打和的情况,将龙虎设置为输
	var rebuild_open_result_data = function(data){
		if(!data['bet_prize']){
			return data;
		}
		if(!data['win_number']){
			return data;
		}
		var balls = data['win_number'].split(' ');
		if(get_poker_config_real_value(balls[0]) != get_poker_config_real_value(balls[1])){
			return data;
		}
		//打和的情况
		for(var p in data['bet_prize']){
			if(data['bet_prize'].hasOwnProperty(p)){
				for(var k in data['bet_prize'][p]){
					if(data['bet_prize'][p].hasOwnProperty(k) && (k == '0' || k == '1')){
						data['bet_prize'][p][k]['is_win'] = 0;
					}
				}
			}
		}
		return data;
	};




	(function(){
		var event_area_addchip_after = function(e, chip){
			var area = this,
				areaDom = Manager.deskTop.find('[data-name="'+ area.getName() +'"]'),
				newChip = Manager.makeChipDom(chip),
				topi = 0,
				left = 0,
				id = 0,
				chipsNum = area.getChipsNum();

			topi = (area.getChipsNum() - 1) * -3;


			game.update();

			var money = area.getResult()["money"];
			if(money<10){
				left = 15;
			}else if(money>=10 && money<100){
				left = 12;
			}else if(money>=100 && money<1000){
				left = 9;
			}else if(money>=1000 && money<10000){
				left = 6;
			}else if(money>=10000 && money<100000){
				left = 3;
			}else if(money>=1000000){
				left = 0;
			}

			newChip.appendTo(areaDom);

			newChip.css({
				left:areaDom.width()/2 - newChip.width()/2,
				top:areaDom.height()/2 - newChip.height()/2 + topi
			});
			chip = area.getLastChip();
			
			if(!!!chip){
				return;
			}

			// chipDom =$('[data-id="'+chip.id+'"]');
			// moneyTip.setText(money);
			// moneyTip.show(left,-30,chipDom);
			
		};

		var event_cancel_one_chip = function(e, chip){
			var money = chip.getMoney(),
				id = chip.getId(),
				sourceDom = Manager.deskTop.find('[data-id="'+ id +'"]'),
				sourceOffset = sourceDom.offset(),
				targetDom = Manager.chipsBar.find('[data-money="'+ money +'"]'),
				targetOffset = targetDom.offset(),
				moveChipDom = Manager.makeChipDom(money);

			moveChipDom.css({
				left:sourceOffset.left,
				top:sourceOffset.top
			});
			moveChipDom.animate({
				left:targetOffset.left,
				top:targetOffset.top
			}, function(){
				game.update();
			});

		};

		var event_area_compensateChip_after = function(e, chip){
			
		};


		$.each(game.getAreas(), function(){
			var area = this;
			//区域增加投注筹码后添加投注动画
			area.addEvent('addchip_after', event_area_addchip_after);

			area.addEvent("compensateChip_after",event_area_compensateChip_after);
		});


		//游戏桌面数据发生变化触发界面更新
		game.addEvent('update_after', function(){

			var me = this,
				betResult = me.getResult(),
				betMoney = betResult['money'];

			$('#J-money-bet').text(Manager.formatMoneyCN(betMoney));
			
		});

		game.addEvent("cancelAll_after",function(e,money){
			userBalance.setUserBalance(money);
		})

		game.addEvent('getRealTimeGameInfo_after',function(e, data){
			var me = this,
				data = rebuild_open_result_data(data),
				status = parseInt(data['status']),
				leftTime = data['leftTime'],
				currentNumber = data['currNumber'],
				betCount = parseInt(data["bet_count"]),
				currNumber = data["currNumber"],
				betPrize = data["bet_prize"],
				keepReqGameInfoTimer1,
				keepReqGameInfoTimer2,
				isReturn = false,
				reqRate = 1*1000,
				winNumber = data['win_number'],
				balls = [],
				kaijiang_balance = Math.floor(data['balance']),
				winPrize=data["win_amount"],
				cnumber = currNumber;

			userBalance.initUserBalance(kaijiang_balance);

			if(winNumber){
				balls = winNumber.split(' ');
			}

			me.setCurrNumber(currentNumber);

			cnumber = cnumber.substr(cnumber.length - 4);

			// 更新当前期号
			$('#J-current-number').text(cnumber);


			// 可投注状态下
			if(status == 1){
				// 启动倒计时
				Manager.clockTimeStart(leftTime);


				// 解锁桌面
				Manager.unlockTable();

				Manager.hideNotice();


				//复位扑克牌
				PokerManager.coverPokers();


				// 当前用户已下注
				if(betCount != 0){
					var money = game.getResult()['money'];
					if(money == 0){
						Manager.restoreChips(global_last_bet_history);
						Manager.lockTable();
					}

					Manager.showNotice("买定离手，等待开奖...");

				}
			}

			// 不可投注，尚未完成派奖
			if(status == 2){

				var money = game.getResult()['money'];

				if(betCount!=0){
					if(money==0){
						Manager.restoreChips(global_last_bet_history);
					}
				}

				// 无论用户是否刷新都需要锁定桌面
				Manager.lockTable();

				
				Manager.showNotice("即将开奖，祝您好运!");


				keepReqGameInfoTimer1 = setTimeout(function(){
					game.getRealTimeGameInfo();
				},reqRate);
			}

			// 后台已经完成派奖，等待前台完成派奖动画
			if(status == 3){
				
				Manager.lockTable();
				if(leftTime<=5){
					if(betCount!=0){
						if(winPrize==0){
							Manager.showNotice("加油，祝您下次好运!</br>"+leftTime+"秒后，进入新奖期");
						}else{
							Manager.showNotice("<font style='font-size:14px'>恭喜您中了<br/><font class='win-prize-in-tips' style='color:#F7CD1F;font-size:32px'>"+Manager.formatMoneyCN(winPrize)+"!</font></br>"+leftTime+"秒后，进入新奖期");
						}
					}else{
						Manager.showNotice(leftTime+"秒后，进入新奖期");
					}
					setTimeout(function(){
						me.getRealTimeGameInfo();
					},1000)
					
				}else{
					var	bet_prize = data['bet_prize'],
						content,
						ct,
						areas,
						balanceBeforeSettlement,
						result=[],
						shineAreas=[],
						luckyAreas = [],
						unluckyAreas = [],
						areaname = '';

					for(var way_id in bet_prize){
						if(bet_prize.hasOwnProperty(way_id)){
							content = bet_prize[way_id];
							for(var cnt in content){
								if(content.hasOwnProperty(cnt)){
									switch(cnt){
										case '0':
											areaname="long";
											break;
										case '1':
											areaname="hu";
											break;
										case '2':
											areaname="he";
											break;
										case '3':
											areaname="long-dan";
											break;
										case '4':
											areaname="hu-dan";
											break;
										case '5':
											areaname="long-shuang";
											break;
										case '6':
											areaname="hu-shuang";
											break;
										case '7':
											areaname="long-hong";
											break;
										case '8':
											areaname="hu-hong";
											break;
										case '9':
											areaname="long-hei";
											break;
										case '10':
											areaname="hu-hei";
											break;
									}
									var isWin = content[cnt]["is_win"],
									    betAmount = content[cnt]["bet_amount"],
									    winAmount = content[cnt]["win_amount"];

									result.push({"name_en":areaname,"is_win":isWin,"bet_amount":betAmount,"win_amount":winAmount});
									
								}
							}
						}
					};

					for(var m = 0; m<result.length;m++){
						if(result[m]["is_win"] == 1){
							// 中奖的玩法(无论用户是否下注)
							shineAreas.push(result[m]);

							// 用户下注且中奖的玩法
							if(!!result[m]["bet_amount"]&&result[m]["bet_amount"] != 0){
								luckyAreas.push(result[m]);
							}
						};
						// 用户下注但未中奖的玩法
						if(!result[m]["is_win"] && !!result[m]["bet_amount"]){
							unluckyAreas.push(result[m]);
						};
					};

					// 用户下注了
					if(betCount != 0){

						// 桌面金额
						var money = game.getResult()['money'];
						
						if(money == 0){
							// 桌面没筹码说明，用户刷新了界面(用户下注且刷新了界面，需要恢复筹码)
							switch(leftTime){
								case 13:
								case 12:
								case 11:
								case 10:
									Manager.restoreChips(global_last_bet_history);

									// 1秒后奖区闪动，持续3.5秒
									setTimeout(function(){
										Manager.areaShine(shineAreas);
									},1000)
									
									// 等待4.5秒+执行动画3秒=7.5秒
									setTimeout(function(){
										// 停止闪动并保持
										Manager.areaStopShine(shineAreas);

							 			// 庄家得
							 			// 等待0秒+耗时1秒
										Manager.bankerWin(unluckyAreas);

										// 玩家得
										// 等待1秒+耗时2秒=3秒
										setTimeout(function(){
											Manager.playerWin(luckyAreas, balls, data);
											// 设置用户余额
											if(winPrize>0){
												Manager.bonusAnimate(winPrize,kaijiang_balance);
											}else{
												userBalance.initUserBalance(kaijiang_balance);
											}
											Manager.rebetOrDouble();
										},1000);
									},4500);

									setTimeout(function(){
										Manager.areaClearShine(shineAreas);
										game.getRealTimeGameInfo();
									},7500);
									break;
								case 9:
								case 8:
								case 7:
									Manager.restoreChips(global_last_bet_history);
									Manager.areaHight(shineAreas);
									// 等待1秒+动画3秒=4秒
									setTimeout(function(){
							 			// 庄家得
							 			// 等待0秒+耗时1秒
										Manager.bankerWin(unluckyAreas);

										// 玩家得
										// 等待1秒+耗时2秒=3秒
										setTimeout(function(){
											Manager.playerWin(luckyAreas, balls, data);
											if(winPrize>0){
												Manager.bonusAnimate(winPrize,kaijiang_balance);
											}else{
												userBalance.initUserBalance(kaijiang_balance);
											}
											Manager.rebetOrDouble();
										},1000);
									},3500);

									setTimeout(function(){
										Manager.areaClearShine(shineAreas);
										game.getRealTimeGameInfo();
									},4000);
									break;
								case 6:
									Manager.areaHight(shineAreas);
									// 动画持续时间2秒
									if(winPrize > 0){
											Manager.bonusAnimate(winPrize,kaijiang_balance);
									}else{
										userBalance.initUserBalance(kaijiang_balance);
									}
									Manager.rebetOrDouble();
									setTimeout(function(){
										Manager.areaClearShine(shineAreas);
										game.getRealTimeGameInfo();
									},2000);
									break;
								case 5:
								case 4:
								case 3:
								case 2:
								case 1:
									game.getRealTimeGameInfo();
									break;
							}

						}else{
							// 桌面有筹码，说明用户未刷新，连贯执行派奖动画
							
							// 动画A：0秒
							// 骰盅停摆（本身消耗的时间忽略不计）

							// TODO:修改成翻牌动画
							
							//扑克牌管理器进行开奖结果展示
							PokerManager.showResult(balls);
							//Manager.showNotice("开奖号码为：" + balls.join(" "));
							setTimeout(function(){
								$(".sequential-trands-container").empty();
								var historyRecords = game.getConfig('historyNumbers');
								historyRecords.push({code:winNumber});
								var trand = new bomao.TableGame.LhdHistory({records:historyRecords,puker:puker}); 
							},1000)

							// history.addRecord({
							// 	'issue':currNumber,
							// 	'nums':balls
							// });


							// 动画B：4.5秒
							// 骰盅归位，奖区闪动(等待的时间1秒+动画的时间3.5秒=4.5秒)
							setTimeout(function(){
					 			Manager.hideNotice();
								Manager.areaShine(shineAreas);
							},1000);
							
							
							// 动画C：等待时间4.5秒+动画时间3秒=7.5秒
							// 区域停止闪动并保持，执行派奖
							setTimeout(function(){
								// 停止闪动并保持
								Manager.areaStopShine(shineAreas);

					 			// 庄家得
					 			// 等待0秒+耗时1秒
								Manager.bankerWin(unluckyAreas);

								// 玩家得
								// 等待1秒+耗时2秒=3秒
								setTimeout(function(){
									Manager.playerWin(luckyAreas, balls, data);

									// 设置用户余额
									if(winPrize>0){
										Manager.bonusAnimate(winPrize,kaijiang_balance);
									}else{
										userBalance.initUserBalance(kaijiang_balance);
									}
									Manager.rebetOrDouble();
								},1000);
							},4500);


							// 7.5秒
							setTimeout(function(){
								Manager.areaClearShine(shineAreas);
								game.getRealTimeGameInfo();
							},7500);
						}
					}


					// 如果用户未下注
					if(betCount == 0){
						switch(leftTime){
							case 13:
							case 12:
							case 11:
							case 10:

								//扑克牌管理器进行开奖结果展示
								PokerManager.showResult(balls);
								setTimeout(function(){
									$(".sequential-trands-container").empty();
									var historyRecords = game.getConfig('historyNumbers');
									historyRecords.push({code:winNumber});
									var trand = new bomao.TableGame.LhdHistory({records:historyRecords,puker:puker}); 
								},1000)
								//Manager.showNotice("开奖号码为：" + balls.join(" "));
								
								// 2秒后骰盅归位
								setTimeout(function(){
						 			Manager.hideNotice();
									Manager.areaShine(shineAreas);
								},2000);

								// 
								setTimeout(function(){
									Manager.areaStopShine(shineAreas);
								},5500)

								// 9秒后停止闪动
								setTimeout(function(){
									Manager.areaClearShine(shineAreas);
									game.getRealTimeGameInfo();
								},8000);
								break;
							case 9:
							case 8:
							case 7:
								Manager.areaHight(shineAreas);
								setTimeout(function(){
									Manager.areaClearShine(shineAreas);
									game.getRealTimeGameInfo();
								},4000);
								break;
							case 6:
								Manager.areaHight(shineAreas);
								setTimeout(function(){
									Manager.areaClearShine(shineAreas);
									game.getRealTimeGameInfo();
								},2000);
								break;
							case 4:
							case 5:
							case 3:
							case 2:
							case 1:
								game.getRealTimeGameInfo();
								break;
						}
					}
				}
			}

			// 奖期取消
			if(status == 4){

				Manager.cancelAll();
				
				Manager.showNotice("本奖期取消，系统已撤单<br/>"+leftTime+"秒后，进入新奖期!");

				setTimeout(function(){
					game.getRealTimeGameInfo();
				},1000);

			}
		});


		//清桌动作
		game.addEvent('cancelAll_before', function(){
			var me = this,
				chips = me.getAllChips();
			$.each(chips, function(){
				Manager.cancelChipAnimate(this);
			});
		});

		// 用户获得筹码以后
		game.addEvent('playerGet_after',function(e,chips){
			var me = this;

			if(chips.length>=120){
				chips = chips.slice(0,120);
			}
			$.each(chips,function(){
				Manager.playerGetChipAnimate(this);
			})
		})



		//提交之前
		game.addEvent('submit_before', function(){
			
			
		});
		//获得注单提交结果
		game.addEvent('success_after', function(e, data){
				Manager.showNotice('买定离手，等待开奖...');
				Manager.lockTable();
				// Manager.updateBet();
		});


	})();

	//管理器
	var Manager = {
		doc:$(document),
		body:$(document.body),
		deskTop:$('#J-desktop'),
		chipsBar:$('#J-chip-group-cont'),
		mask:$('#J-table-mask-lock'),
		//可投注区右键菜单
		rightMenu:new host.TableGame.ContextMenu(),
		//桌面其他区域右键菜单
		tableMenu:new host.TableGame.ContextMenu(),
		// notice:$('#J-panel-notice'),
		notice:$("#J-panel-notice"),
		resultNotice:$('#J-panel-result'),
		playMethod:$('#J-panel-notice2'),
		clockNumbers:$('#J-clock-number .num'),
		sand:$(".table-game-sand"),
		init:function(){
			var me = this;
			me.timer_clock = null;
			me.initEvent();
		},
		initEvent:function(){
			var me = this,
				deskDom = $('#J-desktop'),
				tip = new bomao.Tip({cls:'j-ui-tip-t j-ui-tip-play-help'});
				moneyTip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-money'});
				hotColdTips = new bomao.Tip({cls:'j-ui-tip-l j-ui-tip-hot-cold'});
			//监听桌面事件
			deskDom.on('click', '.area', function(e){
				var el = $(this),
					action = el.attr('data-action');
				if(action){
					me.action(action, el);
				}
			});

			deskDom.on('mouseover','.area',function(){
				var el = $(this),
					areaname = el.attr('data-name'),
					area,
					chips,
					id,
					money,
					intMoney,
					chipDom,
					left,
					chipsNum=0;

				if(!!!areaname){
					return;
				}

				area = game.getArea(areaname);

				chipsNum = area.getChipsNum();

				money = area.getResult()["money"];
				if(money<10){
					left = 15;
				}else if(money>=10 && money<100){
					left = 12;
				}else if(money>=100 && money<1000){
					left = 9;
				}else if(money>=1000 && money<10000){
					left = 6;
				}else if(money>=10000 && money<100000){
					left = 3;
				}else if(money>=1000000){
					left = 0;
				}
				
				chip = area.getLastChip();
				
				if(!!!chip){
					return;
				}
				
				chipDom =$('[data-id="'+chip.id+'"]');
				moneyTip.setText(money);
				moneyTip.show(left,-30,chipDom);

			}).on("mouseout",'.area',function(){
				moneyTip.hide();
			})

			deskDom.on("mouseenter",".help",function(e){
				var el=$(this);
				var helpText = el.attr("helpText");
				tip.setText(helpText);
    			tip.show(-210, 25, el);
			});

		    deskDom.on("mouseout",".help",function(e){
		    	tip.hide();
			});

			//清桌
			$('#J-button-clearall').click(function(){
				me.cancelAll();
			});



			//添加右键菜单
			me.rightMenu.addItem({'title':'撤销', 'action':'area-cancel'});
			me.rightMenu.addItem({'title':'清空', 'action':'area-clear'});
			me.rightMenu.addItem({'title':'翻倍x2', 'action':'area-x2'});
			me.rightMenu.addItem({'title':'ALL IN', 'action':'area-allin'});
			me.deskTop.on('contextmenu', '[data-action="addchip"]', function(e){
				var el = $(this),
					areaname = el.attr('data-name'),
					area = game.getArea(areaname);
				me.rightMenu.setData({'areaname':areaname});
				me.rightMenu.show(e.clientX, e.clientY);
				e.stopPropagation();
				e.preventDefault();
			});
			me.rightMenu.addEvent('click', function(e, action, dom){
				var me = this;
				switch(action){
					//区域单步撤销
					case 'area-cancel':
						Manager.action('areacancel', me.getData());
					break;
					//区域全部撤销
					case 'area-clear':
						Manager.action('areaclear', me.getData());
					break;
					//翻倍
					case 'area-x2':
						Manager.action('areax2', me.getData());
					break;
					//all in
					case 'area-allin':
						Manager.action('areaallin', me.getData());
					break;
					default:
					break;
				}
				me.hide();
			});
			me.tableMenu.addItem({'title':'撤销操作', 'action':'desk-cancel'});
			me.tableMenu.addItem({'title':'恢复操作', 'action':'desk-recovery'});
			me.tableMenu.addItem({'title':'确认投注', 'action':'desk-submit'});
			me.deskTop.on('contextmenu', function(e){
				// me.tableMenu.show(e.clientX, e.clientY);
				e.preventDefault();
			});



			//提交
			$('#J-button-submit').click(function(){

				if(!game.getResult().money==0){
					game.submit();

				}else{
					Manager.showNotice("尚未下注！");
					setTimeout(function(){
						Manager.hideNotice();
					},1000);
				}
			});

			$("#J-button-rebet").click(function(){
				var	lastBetInfo = game.getLastBetInfo()?game.getLastBetInfo():global_last_bet_history,
					balls = lastBetInfo.balls,
					i = 0,
					j = 0,
					bets = [],
					area_name = "",
					area,
					money = 0,
					chips;

				money = game.getResult()['money'];

				if(lastBetInfo.isFinish){
					for(;i<balls.length;i++){
						chips = chipsGroup.moneyToChips(balls[i]["multiple"]*2/100);
						switch(parseInt(balls[i].ball)){
							case 0:
								areaname="long";
								break;
							case 1:
								areaname="hu";
								break;
							case 2:
								areaname="he";
								break;
							case 3:
								areaname="long-dan";
								break;
							case 4:
								areaname="hu-dan";
								break;
							case 5:
								areaname="long-shuang";
								break;
							case 6:
								areaname="hu-shuang";
								break;
							case 7:
								areaname="long-hong";
								break;
							case 8:
								areaname="hu-hong";
								break;
							case 9:
								areaname="long-hei";
								break;
							case 10:
								areaname="hu-hei";
								break;

						}
						area = game.getArea(areaname);
						bets.push({"area":area,"chips":chips});
					}

					Manager.rebet(bets);
				}else{
					Manager.showNotice("未获得往期押注数据，不可重押！");

					setTimeout(function(){
						Manager.hideNotice();
					},1000)
				}
				
			});

			$("#J-button-double").click(function(){
				var money = game.getResult()['money'];
				if(money*2>userBalance.getUserBalance()){
					Manager.balanceNotice();
					return;
				}else{
					$.each(game.getAreas(),function(){
						var area = this;

						if(area.getResult()['money']!=0){
							var data = {'areaname':area.name};
							Manager.action_areax2(data);
						}
					});
				}
			})

			$("#hot-cold-txt").hover(function(){
				hotColdTips.setText("冷热号的统计期数<br/>点击可设置(5至200期)");
				hotColdTips.show(40,-11,$(this));
			}).mouseout(function(){
				hotColdTips.hide();
			}).focus(function(){
				$(this).attr('placeholder',"");
			}).blur(function(){
				$(this).attr('placeholder',"30");
			});

			$("#hot-cold-txt").on('input',function(){
				
				var reg = /^[0-9]*[1-9][0-9]*$/,
					hotColdNum = $(this).val();
				if(!hotColdNum.match(reg)){
					$(this).val("");
				}
				if(parseInt(hotColdNum)>200){
					$(this).val("");
				}
			}).on('change',function(){
				var hotColdNum = $(this).val();
				if(parseInt(hotColdNum)<5){
					$(this).val("");
				}
			}).on("mouseout",function(){
				var hotColdNum = $(this).val();
				if(parseInt(hotColdNum)<5){
					$(this).val("");
				}
			})
		},
		action:function(type, data){
			var me = this;
			if($.isFunction(me['action_' + type])){
				me['action_' + type](data);
			}
		},
		action_addchip:function(dom){
			var me = this,
				name = dom.attr('data-name'),
				area = game.getArea(name),
				chip = chipsGroup.getSelectedChip(),
				balance = userBalance.getUserBalance();

			if(chip.getMoney()>balance){
				me.balanceNotice();
				return;
			}

			me.addChipAnimate(chip, area);
			userBalance.setUserBalance(parseInt(0-chip.getMoney()));

		},
		action_areacancel:function(data){
			var me = this,
				name = data['areaname'],
				area = game.getArea(name),
				chip = area.cancelChip();
			if(chip){
				me.cancelChipAnimate(chip);
				game.update();
				userBalance.setUserBalance(chip.getMoney());
			}
		},
		action_areaclear:function(data){
			var me = this,
				name = data['areaname'],
				area = game.getArea(name),
				money = area.getResult()["money"];
				chips = area.clearAll();

			$.each(chips, function(){
				me.cancelChipAnimate(this);
			});
			game.update();
			userBalance.setUserBalance(money);
		},
		action_areax2:function(data){
			var me = this,
				name = data['areaname'],
				area = game.getArea(name),
				chips = area.getChipsCase(),
				balance = userBalance.getUserBalance(),
				money = area.getResult()["money"];

			if(Number(money)*2 > balance){
				me.balanceNotice();
				return;
			}

			userBalance.setUserBalance(0-Number(money));
			$.each(chips, function(){
				me.addChipAnimate(this, area);
			});
		},
		action_areaallin:function(data){
			var me = this,
				name = data['areaname'],
				area = game.getArea(name),
				minChip = chipsGroup.getMinChip(),
				balance = userBalance.getUserBalance(),
				allchips = chipsGroup.moneyToChips(balance),
				i = 0,
				len = 0;

			if(balance < minChip.getMoney()){
				me.balanceNotice();
				return;
			}


			userBalance.setUserBalance(0-balance);

			$.each(allchips, function(){
				for(i = 0; i < this['num']; i++){
					me.addChipAnimate(chipsGroup.getChip(this['money']), area);	
				}
			});

		},
		rebet:function(bets){
			var me = this,
				area,
				chip,
				balance = userBalance.getUserBalance(),
				money = 0,
				area,
				chips;

			for(var i = 0; i < bets.length; i ++){
				area = bets[i].area;
				chips = bets[i].chips;
				for(var j = 0; j < chips.length; j ++){
					money += chips[j].money*chips[j].num;
				}
			}

			if(money > global_balance){
				me.balanceNotice();
				return;
			}

			for(var i = 0; i < bets.length; i ++){
				area = bets[i].area;
				chips = bets[i].chips;
				$.each(chips, function(){
					var cp = this;
					c = chipsGroup.getChip(cp.money);
					for(var j=0;j<cp.num;j++){
						me.addChipAnimate(c, area);
					}
				});
				
			}
		
			userBalance.setUserBalance(0 - money);

		},

		resetChips:function(bets){
			var me = this,
				area,
				chip,
				balance = userBalance.getUserBalance(),
				money = 0,
				area,
				chips;

			for(var i = 0; i < bets.length; i ++){
				area = bets[i].area;
				chips = bets[i].chips;
				$.each(chips, function(){
					var cp = this;
					c = chipsGroup.getChip(cp.money);
					for(var j=0;j<cp.num;j++){
						me.addChipAnimate(c, area);
					}
				});
				
			}
		},

		// 恢复筹码
		restoreChips:function(lastBetInfo){
			var me = this,
				balls = lastBetInfo.balls,
				bets = [],
				i = 0;
			// 还原筹码
			for(;i<balls.length;i++){
				chips = chipsGroup.moneyToChips(balls[i]["multiple"]*2/100);
				switch(parseInt(balls[i].ball)){
					case 0:
						areaname="long";
						break;
					case 1:
						areaname="hu";
						break;
					case 2:
						areaname="he";
						break;
					case 3:
						areaname="long-dan";
						break;
					case 4:
						areaname="hu-dan";
						break;
					case 5:
						areaname="long-shuang";
						break;
					case 6:
						areaname="hu-shuang";
						break;
					case 7:
						areaname="long-hong";
						break;
					case 8:
						areaname="hu-hong";
						break;
					case 9:
						areaname="long-hei";
						break;
					case 10:
						areaname="hu-hei";
						break;
				}
				area = game.getArea(areaname)
				bets.push({"area":area,"chips":chips});
			}

			me.resetChips(bets);
		},

		//拷贝一个筹码
		copyChip:function(money){
			var me = this,
				dom = $('<i data-money="'+ money +'" class="chip move-chip chip-'+ me.getMoneyForClass(money) +'" ></i>');
			return dom;
		},
		//生成一个投注筹码
		makeChipDom:function(chip, isHtml){
			var me = this,
				html = '<i id="J-chip-'+ chip.getId() +'" data-id="'+ chip.getId() +'" data-money="'+ chip.getMoney() +'" class="chip move-chip chip-'+ me.getMoneyForClass(chip.getMoney()) +'" ></i>';
			
			if(isHtml){
				return html;
			}

			return $(html);
		},
		getMoneyForClass:function(money){
			return ('' + money).replace('.', '-');
		},
		//清桌
		cancelAll:function(){
			var me = this;
			game.cancelAll();
		},

		playerGet:function(){
			var me = this;
			game.playerGet();
		},
		bankerAddChipAnimate:function(topi,chip,area){
			var me = this,
				sourceDom = $(".banker"),
				sourceOffset = sourceDom.offset(),
				targetDom = $('#J-desktop').find('[data-name="'+ area.getName() +'"]'),
				targetOffset = targetDom.offset(),
				// moveChipDom = me.copyChip(chip.getMoney());
				newChip = new host.TableGame.Chip({money:chip.getMoney()}),
				moveChipDom = me.makeChipDom(newChip),
				chipsNum = area.getChipsNum(),
				soruceTop = 0,
				targetTop = 0;

			// 所有的chip加到内存中
			area.compensateChip(newChip);

			// 前端界面上的chip只加到120个
			if(chipsNum < 120){

				soruceTop=(topi- 1) * -3;
				targetTop=(chipsNum - 1) * -3;
				moveChipDom.appendTo(me.body);

				//console.log(sourceOffset.left + sourceDom.width()/2 - moveChipDom.width()/2, sourceOffset.top + sourceDom.height()/2 - moveChipDom.height()/2);

				moveChipDom.css({
					left:sourceOffset.left + sourceDom.width()/2,
					top:sourceOffset.top + sourceDom.height()/2
				});
				
				moveChipDom.animate({
					left:targetOffset.left + targetDom.width()/2 - moveChipDom.width()/2 + 2,
					top:(targetOffset.top + targetDom.height()/2 - moveChipDom.height()/2 + 1)+targetTop
				}, 1000,function(){
					// me.playAudio("chipToPlayer");
					// moveChipDom.remove();
				});
			}


		},
		//从庄家直接赔付筹码到玩家
		bankerAddChipAnimateToPlayer:function(i, money){
			var me = this,
				sourceDom = $(".banker"),
				sourceOffset = sourceDom.offset(),
				targetDom = $('.money-bet'),
				targetOffset = targetDom.offset(),
				newChip = new host.TableGame.Chip({money:money}),
				moveChipDom = me.makeChipDom(newChip),
				chipsNum = 0,
				soruceTop = 0,
				targetTop = 0;

			soruceTop = 0;
			targetTop =  i * 40;
			moveChipDom.appendTo(me.body);

			moveChipDom.css({
				left:sourceOffset.left + sourceDom.width()/2,
				top:sourceOffset.top + sourceDom.height()/2
			});
			
			moveChipDom.animate({
				left:targetOffset.left + targetDom.width()/2 - moveChipDom.width()/2 + 2,
				top:(targetOffset.top + targetDom.height()/2 - moveChipDom.height()/2 + 1) - targetTop
			}, 1500,function(){
				moveChipDom.remove();
			});
		},
		bankerGetChipAnimate:function(chip,callback){
			var me = this,
				id = chip.getId(),
				money = chip.getMoney(),
				sourceDom = $('#J-chip-' + id),
				targetDom = $('.banker'),
				sourceOffset = sourceDom.offset(),
				targetOffset = targetDom.offset(),
				moveDom = me.makeChipDom(chip);

			sourceDom.remove();
			moveDom.css({
				left:sourceOffset.left,
				top:sourceOffset.top
			});
			moveDom.appendTo(me.body);
			moveDom.animate({
				left:targetOffset.left + moveDom.width()/2,
				top:targetOffset.top + moveDom.height()/2
			},1000, function(){
				// me.playAudio("chipToPlayer");
				moveDom.remove();
				if(callback){
					callback.call(me);
				}
			});

		},
		playerGetChipAnimate:function(chip,callback){
			var me = this,
				id = chip.getId(),
				money = chip.getMoney(),
				sourceDom = $('#J-chip-' + id),
				targetDom = Manager.chipsBar.find('[data-money="'+ money +'"]'),
				sourceOffset = sourceDom.offset(),
				targetOffset = targetDom.offset(),
				moveDom = me.makeChipDom(chip);

			sourceDom.remove();
			moveDom.css({
				left:sourceOffset.left,
				top:sourceOffset.top
			});

			moveDom.appendTo(me.body);
			moveDom.animate({
				left:targetOffset.left,
				top:targetOffset.top
			},1000, function(){
				me.playAudio("chipToPlayer");
				me.rebetOrDouble();
				moveDom.remove();
				if(callback){
					callback.call(me);
				}
			});

		},
		addChipAnimate:function(chip, area){

			var me = this,
				sourceDom = $('#J-chip-group-cont').find('[data-money="'+ chip.getMoney() +'"]'),
				sourceOffset = sourceDom.offset(),
				targetDom = $('#J-desktop').find('[data-name="'+ area.getName() +'"]'),
				targetOffset = targetDom.offset(),
				moveChipDom = me.copyChip(chip.getMoney()),
				newChip = new host.TableGame.Chip({money:chip.getMoney()});

			moveChipDom.appendTo(me.body)

			moveChipDom.css({
				left:sourceOffset.left,
				top:sourceOffset.top
			});
			moveChipDom.animate({
				left:targetOffset.left + targetDom.width()/2 - moveChipDom.width()/2 + 2,
				top:targetOffset.top + targetDom.height()/2 - moveChipDom.height()/2 + 1
			}, function(){
				me.playAudio("chipToTable");
				area.addChip(newChip);
				me.rebetOrDouble();
				moveChipDom.remove();
			});
		},
		cancelChipAnimate:function(chip, callback){
			var me = this,
				id = chip.getId();
			var	money = chip.getMoney(),
				sourceDom = $('#J-chip-' + id),
				targetDom = Manager.chipsBar.find('[data-money="'+ money +'"]'),
				sourceOffset = sourceDom.offset();
			var	targetOffset = targetDom.offset(),
				moveDom = me.makeChipDom(chip);

			sourceDom.remove();
			moveDom.css({
				left:sourceOffset.left,
				top:sourceOffset.top
			});

			moveDom.appendTo(me.body);
			moveDom.animate({
				left:targetOffset.left,
				top:targetOffset.top
			}, function(){
				me.playAudio("chipToPlayer");
				me.rebetOrDouble();
				moveDom.remove();
				if(callback){
					callback.call(me);
				}
			});

			

		},
		//锁定桌面禁止操作
		lockTable:function(){
			var me = this;
			me.mask.show();
		},
		unlockTable:function(){
			var me = this;
			me.mask.hide();
		},
		showNotice:function(msg){
			var me = this;
			me.resultNotice.html(msg).removeClass("table-notice2").addClass("table-notice1");
			me.resultNotice.show();
		},
		hideNotice:function(){
			var me = this;
			me.resultNotice.hide();
		},
		showResultNotice:function(msg){
			var me = this;
			me.resultNotice.html(msg)
			me.resultNotice.show();
		},
		hideResultNotice:function(){
			var me = this;
			me.resultNotice.hide();
		},
		hideResult:function(){

		},
		clock:function(time){
			var me = this,
				// h = Math.floor(time/3600),
				// m = Math.floor(time%3600/60),
				m = (time-time%60)/60,
				s = time%60;
			// h = h < 10 ? '0' + h : h;
			m = m < 10 ? '0' + m : m;
			s = s < 10 ? '0' + s : s;

			if(time == 10){
				me.playAudio("timeoutTips");
				me.clockNumbers.addClass('num-red');
			}
			if(time > 10){
				me.clockNumbers.removeClass('num-red');
			}

			me.clockNumbers.text(time);
			/**
			me.clockNumbers[0].innerHTML = m;
			me.clockNumbers[1].innerHTML = s;
			**/
		},
		sandClock:function(time,totalTime){
			var me = this,
				totalTime = 1*45,
				pastTime = totalTime-time,
				pastPer = pastTime/totalTime*100;
			me.sand.css("height",pastPer+"%");
		},

		// 从后台获取剩余时间，并以此作为总时间进行倒计时
		clockTimeStart:function(time){
			var me = this,
				now_start = new Date(),
				num,
				now;

			clearInterval(me.timer_clock);
			me.timer_clock = setInterval(function(){
				now = new Date();
				num = time - Math.floor((now - now_start)/1000);

				if(num < 0){
					clearInterval(me.timer_clock);
					game.getRealTimeGameInfo();
					me.sand.css("height",0);
					Manager.showNotice("即将开奖，祝您好运!");
					Manager.lockTable();
				}else{
					me.clock(num);
					me.sandClock(num);
				}
			}, 1000);
		},

		balanceNotice:function(){
			$("#J-money-user-balance").animate({fontSize:"20px"},100);
			$("#J-money-user-balance").animate({fontSize:"14px"},100);
			$("#J-money-user-balance").animate({fontSize:"20px"},100);
			$("#J-money-user-balance").animate({fontSize:"14px"},100);
			$("#J-money-user-balance").animate({fontSize:"20px"},100);
			$("#J-money-user-balance").animate({fontSize:"14px"},100);
		},
		// 区域闪烁
		// 每个循环耗时1秒
		areaShine:function(areas){
			var me = this;
			me.lightTimer = setInterval(function(){
                $.each(areas, function(){
 					$('[data-name='+this["name_en"]+']').css("background-image","url(/assets/images/game/table/lhd/areas-hover.png)");
                });

                me.darkTimer = setTimeout(function(){
                	$.each(areas, function(){
                		$('[data-name='+this["name_en"]+']').css("background-image","url(/assets/images/game/table/lhd/areas.png)");
                	});
                },500)

            }, 1000);
		},
		areaClearShine:function(areas){
			$.each(areas, function(){
                $('[data-name='+this["name_en"]+']').css("background-image","url(/assets/images/game/table/lhd/areas.png)").hover(function(e) {
  					$(this).css("background-image",e.type === "mouseenter"?"url(/assets/images/game/table/lhd/areas-hover.png)":"url(/assets/images/game/table/lhd/areas.png)");
				});
            });
		},
		areaHight:function(areas){
			$.each(areas, function(){
                $('[data-name='+this["name_en"]+']').css("background-image","url(/assets/images/game/table/lhd/areas-hover.png)");
            });
		},
		areaStopShine:function(areas){
			var me = this;
			clearTimeout(me.lightTimer);
			clearTimeout(me.darkTimer);
		},
		// 筹码流向庄家，共耗时1秒
		bankerWin:function(areas){
			var area,me = this;
			// 庄家得
			for(var i = 0;i < areas.length;i ++){
				area = game.getArea(areas[i]["name_en"]);
				$.each(area.getChipsCase(),function(){
					me.bankerGetChipAnimate(this,function(){
						
					});
				})
				area.clearAll();
			}
		},
		// 筹码流向玩家,共耗时1+1=2秒
		playerWin:function(areas, balls, data){
			var me = this,
				area1,
				chipsNum,
				odds;


			// 庄家赔
			for(var i = 0;i < areas.length;i ++){
				var topi = 0;

				area1 = game.getArea(areas[i]["name_en"]);
				prize_odds = area1.getOdds();

				$.each(area1.getChipsCase(),function(z){
					for(var j = 1;j < prize_odds+1;j++){
						topi=z*j;
						me.bankerAddChipAnimate(topi,this,area1);
					}
				});	
			};


			//如果是打和
			//庄家直接赔付对应投注额的一半给玩家
			if(!!balls && balls.length > 1 && (get_poker_config_real_value(balls[0]) ==  get_poker_config_real_value(balls[1]))){
				(function(){
					var longData = get_result_data_by_area(0, data),
						huData = get_result_data_by_area(1, data),
						longwinmoney = 0,
						huwinmoney = 0,
						moneyall = 0,
						chips = [],
						i = 0,
						len,
						j = 0,
						len2;
					if(longData['win_amount'] && Number(longData['win_amount']) > 0){
						longwinmoney = Number(longData['win_amount']);
					}
					if(huData['win_amount'] && Number(huData['win_amount']) > 0){
						huwinmoney = Number(huData['win_amount']);
					}
					moneyall = longwinmoney + huwinmoney;

					if(Math.floor(moneyall) == 0){
						chips = [];
					}else{
						chips = chipsGroup.moneyToChips(Math.floor(moneyall));
					}
					if(moneyall - Math.floor(moneyall) > 0){
						chips.push({money:0.5, num:1});
					}

					for(i = 0,len = chips.length; i < len; i++){
						for(j = 0,len2 = chips[i]['num']; j < len2; j++){
							me.bankerAddChipAnimateToPlayer(i, chips[i]['money']);
						}
					}

				})();
			}


			// 玩家得
			setTimeout(function(){
				me.playerGet();
			},1000)
		},
		formatMoneyCN:function(num){
			var num = Number(num),
            re = /(-?\d+)(\d{3})/;
            num = '' + num;
	        while (re.test(num)) {
	            num = num.replace(re, "$1,$2")
	        }
	        return num + "元";
		},
		rebetOrDouble:function(){
			var money = game.getResult()['money'];

			if(money == 0){
				$('#J-button-rebet').css('display','inline-block');
				$('#J-button-double').css('display','none');
			}else{
				$('#J-button-rebet').css('display','none');
				$('#J-button-double').css('display','inline-block');

			}
		},
		bonusAnimate:function(winPrize,balanceAfter){

			$('.table-bar > .win-bonus-container > .win-bonus-txt').text(winPrize);
		 	$('.table-bar > .win-bonus-container').css({display:'block'},{top:'-75px'}).animate({
		 		'top':'-25px'
		 	},2000,function(){
		 		$(this).css("display",'none');
		 		userBalance.initUserBalance(balanceAfter);
		 	});
		 	Manager.hideNotice();
		},
		getClass:function(num){
			var cls;
			switch(num){
				case 0:
					cls = 'cold';
					break;
				case 1:
					cls = "warm"
					break;
				case 2:
					cls = "hot";
					break;
			}
			return cls;
		},
		playAudio:function(name){
			for(var i=0;i<audioConfig.length;i++){
				if(audioConfig[i].name == name){
					$("#dice-tips-audio").attr("src",audioConfig[i].url);
					document.getElementById('dice-tips-audio').play();
				}
			}
		},
		stopAudio:function(name){
			for(var i=0;i<audioConfig.length;i++){
				if(audioConfig[i].name == name){
					if($("#dice-tips-audio").attr("src") == audioConfig[i].url){
						document.getElementById('dice-tips-audio').pause();
					}
				}
			}
			
		}

	};

	//桌面设定
	var max_prize = game.getConfig('max_prize'),
		cycle = game.getConfig("cycle"),
		table_num = 1,
		table_name = "娱乐场",
		background_image ="dice/low-bg.png";


	switch(cycle){
		case 45:
			table_num = 1;
			break;
		case 60:
			table_num = 2;
			break;
		case 75:
			table_num = 3;
			break;
	}

	switch(max_prize){
		case 5:
			background_image = "/assets/images/game/table/dice/low-bg.jpg";
			table_name = "娱乐场";
			break;
		case 10:
			background_image = "/assets/images/game/table/dice/normal-bg.jpg";
			table_name = "普通场";
			break;
		case 20:
			table_name = "高级场";
			background_image = "/assets/images/game/table/dice/high-bg.jpg";
			break;
	}

	$("#max-prize").text(max_prize);
	$("#table-num").text(table_num);
	$("#table-name").text(table_name);
	/**
	$(".table-layout").css("backgroundImage","url("+background_image+")");
	**/
    
    //console.log(max_prize, cycle, table_num, table_name, background_image);
    




	//筹码组实例
	//初始化时根据余额设定状态
	var chipsGroup = new host.TableGame.ChipsGroup();



	(function(){

		var chipsCfg = [{money:1,marginTop:0},{money:2,marginTop:0},{money:5,marginTop:0},{money:10,marginTop:0},{money:50,marginTop:0},{money:100,marginTop:0},{money:1000,marginTop:0}];
		switch(max_prize){
			case 5:
				chipsCfg = [{money:1,marginTop:0},{money:2,marginTop:0},{money:5,marginTop:0},{money:10,marginTop:0},{money:50,marginTop:0},{money:100,marginTop:0},{money:1000,marginTop:0}];
				break;
			case 10:
				chipsCfg = [{money:100,marginTop:0},{money:200,marginTop:0},{money:300,marginTop:0},{money:500,marginTop:0},{money:600,marginTop:0},{money:800,marginTop:0},{money:1000,marginTop:0}];
				break;
			case 20:
				chipsCfg = [{money:1000,marginTop:0},{money:2000,marginTop:0},{money:3000,marginTop:0},{money:5000,marginTop:0},{money:6000,marginTop:0},{money:8000,marginTop:0},{money:10000,marginTop:0}];
				break;
		}
		
		var	html = [],
			groupDom = $('#J-chip-group-cont'),
			marginTop = [],
			CLS = 'active';


		$.each(chipsCfg, function(){
			var chipData = this;
				value = Number(chipData.money),
				marginTop = Number(chipData.marginTop),
				chip = new host.TableGame.Chip({money:value,marginTop:marginTop}),
				chipDom = Manager.makeChipDom(chip,true);
				html.push(chipDom);
			chip.addEvent("setStatus_after",function(e,isAvaliable){
				var jChipDom = $("#J-chip-" + this.getId());
				var chipMoney = this.getMoney();
				if(isAvaliable){
					jChipDom.unbind();
					jChipDom.bind('mousedown',function(){
						chipsGroup.select(chipMoney);
					});
				}else{
					jChipDom.unbind();
					jChipDom.bind('mousedown',function(){
						Manager.balanceNotice();
					});
					jChipDom.removeClass(CLS).animate({
						marginTop:chipData.marginTop
					}, 150);
				}
			})
			chipsGroup.addChip(chip);
		});
		groupDom.html(html.join(''));

		chipsGroup.addEvent('change_after', function(e, chip){
			$('#J-chip-group-cont .chip').removeClass('active').css({marginTop:0});
			groupDom.find('[data-money="'+ chip.getMoney() +'"]').addClass(CLS).animate({
				marginTop:-20
			}, 150);
		});
		chipsGroup.select(chipsCfg[0].money);
	})();


	// 余额实例
	var userBalance = new host.TableGame.UserBalance();
	(function(){
		userBalance.addEvent("setUserBalance_after",function(e,balance){
			$(".J-text-money-value").text(host.util.formatMoney(balance));
			chipsGroup.setChipsStatus(balance);

		});
	})();


	userBalance.setUserBalance(parseFloat(global_balance));
	


	Manager.init();






















	//扑克牌相关
	var PokerManager = new host.TableGame.PokerManager();
		PokerManager.setAttrConfig(puker);
		PokerManager.tableDom = $('#J-desktop');
		PokerManager.pokerPosLong = PokerManager.tableDom.find('.poker-long');
		PokerManager.pokerPosHu = PokerManager.tableDom.find('.poker-hu');
		PokerManager.objPokerLong = null;
		PokerManager.objPokerHu = null;

	//预载全套牌图片缓存
	PokerManager.loadPokerCache = function(){
		var me = this,
			i = 1,
			len = 53;
		for(;i < len; i++){
			me.getPoker(i, ' ');
		}
	};
	//isEmpty 是否只放空牌不进行开奖翻牌
	PokerManager.showResult = function(result, isEmpty){
		var me = this,
			addx = 13,
			addy = 10,
			plong = me.getPoker(result[0]),
			phu = me.getPoker(result[1]),
			loffset = me.pokerPosLong.offset(),
			hoffset = me.pokerPosHu.offset();

		me.objPokerLong = plong;
		me.objPokerHu = phu;

		phu.addClass('poker-blue');

		plong.setPos(loffset.left + addx, loffset.top + addy);
		phu.setPos(hoffset.left + addx, hoffset.top + addy);

		if(isEmpty){
			return;
		}
		setTimeout(function(){
			plong.showCard();

			setTimeout(function(){
				phu.showCard();
			}, 600);
		}, 1500);
	};
	PokerManager.coverPokers = function(){
		var me = this;
		if(me.objPokerLong){
			me.objPokerLong.coverCard('poker-red');
			me.objPokerHu.coverCard('poker-blue');
		}
	};

	//背景效果
	PokerManager.showResult([1,1], true);

	//预先载入图片缓存
	PokerManager.loadPokerCache();

	//更新界面显示内容
	var checkUserTimeout = function(data){
		if(data['type'] == 'loginTimeout'){
			// var msgwd = Games.getCurrentGameMessage();
			// msgwd.hide();
			// msgwd.show({
			// 	mask:true,
			// 	confirmIsShow:true,
			// 	confirmText:'关 闭',
			// 	confirmFun:function(){
			// 		location.href = "/";
			// 	},
			// 	closeFun:function(){
			// 		location.href = "/";
			// 	},
			// 	content:'<div class="pop-waring"><i class="ico-waring"></i><h4 class="pop-text">登录超时，请重新登录平台！</h4></div>'
			// });
			return false;
		}
		return true;
	};

	// var sideTip = bomao.SideTip.getInstance();
	
	//读取账户金额开始 ========================================
	var accountCache = {'recharge':{}, 'withdrawals':{}};
	(function(){
		var balanceDoms = $('#J-balls-statistics-balance, #J-user-amount-num, #J-top-user-balance'),balanceCache = 0;
		var updateBalance = function(balance){
			if(balance != balanceCache){
				balanceDoms.text(bomao.util.formatMoney(balance));
				balanceCache = balance;
			}
		};
        // var updateRecharge = function(data){
        //     var has = accountCache['recharge'],
        //         lastId = '' + $.cookie('user-recharge-id'),
        //         id = '' + data['id'],
        //         num = Number(data['amount']);

        //     if(has[id]){
        //         return;
        //     }
        //     if(!!lastId && lastId == id){
        //         return;
        //     }
        //     $.cookie('user-recharge-id', id);
        //     sideTip.setTitle('充值到账提醒');
        //     sideTip.setContent('<div class="row">您有一笔金额为 <span class="num">' + bomao.util.formatMoney(num) + '</span> 元的充值已到账。</div>');
        //     sideTip.show();
        //     has[id] = data;
        // };
        // var updateWithdrawals = function(data){
        //     var has = accountCache['withdrawals'],
        //         lastId = '' + $.cookie('user-withdrawals-id'),
        //         id = '' + data['id'],
        //         num = Number(data['amount']);
        //     if(has[id]){
        //         return;
        //     }
        //     if(!!lastId && lastId == id){
        //         return;
        //     }
        //     $.cookie('user-withdrawals-id', id);
        //     sideTip.setTitle('提现转账提醒');
        //     sideTip.setContent('<div class="row">您有一笔金额为 <span class="num">' + bomao.util.formatMoney(num) + '</span> 元的提现已处理完毕，请注意查收。</div>');
        //     sideTip.show();
        //     has[id] = data;
        // };
		//消息监听部分
		var MSG = new bomao.Alive({
				url: game.getConfig('pollUserAccountUrl'),
				cache:false,
				dataType:'json',
				method:'get',
				looptime:10 * 1000
		});
		MSG.getParams = function(){
			return {'params':[{'type':'account'}]};
		};
		MSG.addEvent('afterSuccess', function(e, data){
			var me = this,cfg = me.defConfig;
				if(!checkUserTimeout(data)){
					return;
				}
				//updateBalance(2000);
				if(Number(data['isSuccess']) == 1){
					var results = data['data'],list,it;
					$.each(results, function(){
						switch(this['type']){
							case 'account':
								list = this['data'];
								$.each(list, function(){
									it = this;
									switch(it['type']){
										//更新余额
										case 'balance':
											//it['data'] = 34745.12;
											updateBalance(Number(it['data']));
										break;
										//充值到账
										case 'recharge':
											// updateRecharge(it['data']);
										break;
										//提现消息
										case 'withdrawals':
											// updateWithdrawals(it['data']);
										break;
										default:
										break;
									}
								});
							break;
							default:
							break;
						}
					});
				}
		});
		/**
		if(userRole != 'agent'){
			MSG.start();
		}
		**/
		MSG.start();
	})();
	//读取账户金额结束 ========================================
	

})(bomao, jQuery);















