//j奖期类 --对游戏类和信息面板类进行实例化 并加载模块
(function(host, Event, undefined){
	var defConfig = {
		name:'prizePeriod',
		container:'',
		UIContainer:'.bet-history-content',
		//受注\封盘\历史状态
		status:0,
		parentGame:null,
		//剩余时间
		leftTime:0,
		//奖期周期
		cycleTime:0,
		//奖期号
		prize_id:'',
		//封盘时间
		entertainedTime:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.status = cfg.status;
			me.prize_id = cfg.prize_id;
			me.leftTime = cfg.leftTime;
			me.parentGame=cfg.parentGame;
			me.cycleTime = cfg.cycleTime;
			//封盘时间设置
			me.entertainedTime = cfg.entertainedTime;
			me.container = $('<div></div>').appendTo(me.parentGame.container.find(me.UIContainer));
			//玩法的数据结构 0组合 1和值
			me.currentPlayIndex = 0;
			//当前玩法
			me.currentPlay=null;
			//玩法列表
			me.playlist=null;
			//信息列表
			me.informationlist=null;
			//玩法id数组
			me.pladIdArray=null;
			//当前奖期的订单数组
			me.prize_orders = [];
			//开奖结果
			me.result_number=null;

			me.resultNumberData=null;
			//奖金组限制额度
			me.limite_extra=[];

			//切换玩法
			me.addEvent('afert_select_play', function(e, data) {	
				me.showplay(data);
			});
			//切换奖期状态
			me.addEvent('change_prize_status' , function(e,data){
				me.updataStatus(data.status);
			});
			//获取结果
			me.addEvent('start_catch_issue_result' , function(e,data){
				me.catchIssue(data);
			});
			//开奖动画
			me.addEvent('start_lottery_animation' , function(e,data){
				me.startAnimation(data);
			});
			//加载模型
			me.buildUI();

			if(me.parentGame.gameMothed){
				me.initGameMethod(me.parentGame.gameMothed);
			}
		},
		//初始化赔率数据
		initGameMethod:function(data){
			var me = this;
			var daxiao_lab = data.getMethodConfigByName("daxiaodans-bsde-daxiao").extra_prize[0];
			var danshuang = data.getMethodConfigByName("daxiaodans-bsde-danshuang").extra_prize[0];
			var liangji = data.getMethodConfigByName("daxiaodans-bsde-liangji").extra_prize[0];
			var chuanguan = [
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['00'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['01'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['10'],
							data.getMethodConfigByName("daxiaodans-bsde-chuanguan").extra_prize['11']
							];
			
			me.pladIdArray = [
				data.getMethodConfigByName("daxiaodans-bsde-daxiao").id,
				data.getMethodConfigByName("daxiaodans-bsde-danshuang").id,
				data.getMethodConfigByName("daxiaodans-bsde-liangji").id,
				data.getMethodConfigByName("daxiaodans-bsde-chuanguan").id,
				data.getMethodConfigByName("hezhi-hezhi-hezhi").id
				];

			me.container.find('.daxiaodans-bsde-daxiao').html(daxiao_lab.substring(0,daxiao_lab.length-2));
			me.container.find('.daxiaodans-bsde-danshuang').html(danshuang.substring(0,danshuang.length-2));
			me.container.find('.daxiaodans-bsde-liangji').html(liangji.substring(0,liangji.length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-00').html(chuanguan[0].substring(0,chuanguan[0].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-01').html(chuanguan[1].substring(0,chuanguan[1].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-10').html(chuanguan[2].substring(0,chuanguan[2].length-2));
			me.container.find('.daxiaodans-bsde-chuanguan-11').html(chuanguan[3].substring(0,chuanguan[3].length-2));

			
			$.each([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],function(){
				var class_str = ".tip-"+this;
				var class_str2 = ".odds-content-"+this;

				var extra_prize = data.getMethodConfigByName("hezhi-hezhi-hezhi").extra_prize[this];
				var extra_prize_lab = extra_prize.substring(0,extra_prize.length-2);

				me.container.find(class_str).html(extra_prize_lab);
				me.parentGame.container.find(class_str2).html("x"+extra_prize_lab);

				me.limite_extra.push(data.getMethodConfigByName("hezhi-hezhi-hezhi").extra[this]);
			});
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			me.updataStatus(me.status);
			//加载玩法模型
			me.loadplay();
			me.loadinformation();

			me.container.find('.bet-panel-hezhi').get(0).addEventListener("webkitAnimationStart",function(){
				me.parentGame.isAnimating = true;
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.bet-panel-hezhi').removeClass('prize-left-move').removeClass('prize-right-move-miss');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-hezhi').hide();
				}else{
					me.container.find('.bet-panel-hezhi').show();
				}
				me.container.find('.bet').removeClass('bet-over');
				me.parentGame.isAnimating = false;
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("animationstart",function(){
				me.parentGame.isAnimating = true;
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-hezhi').get(0).addEventListener("animationend",function(){
				me.container.find('.bet-panel-hezhi').removeClass('prize-left-move').removeClass('prize-right-move-miss');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-hezhi').hide();
				}else{
					me.container.find('.bet-panel-hezhi').show();
				}
				me.container.find('.bet').removeClass('bet-over');
				me.parentGame.isAnimating = false;
			});

			me.container.find('.bet-panel-zuhe').get(0).addEventListener("webkitAnimationStart",function(){
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.bet-panel-zuhe').removeClass('prize-left-move-miss').removeClass('prize-right-move');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-zuhe').show();
				}else{
					me.container.find('.bet-panel-zuhe').hide();
				}
				me.container.find('.bet').removeClass('bet-over');
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("animationstart",function(){
				me.container.find('.bet').addClass('bet-over');
				me.container.find('.bet-panel-hezhi').show();
				me.container.find('.bet-panel-zuhe').show();
			});
			me.container.find('.bet-panel-zuhe').get(0).addEventListener("animationend",function(){
				me.container.find('.bet-panel-zuhe').removeClass('prize-left-move-miss').removeClass('prize-right-move');
				if(me.currentPlayIndex==0){
					me.container.find('.bet-panel-zuhe').show();
				}else{
					me.container.find('.bet-panel-zuhe').hide();
				}
				me.container.find('.bet').removeClass('bet-over');
			});

		},
		//加载玩法模型
		loadplay:function(){
			var me = this;
			//组合玩法
			me.play_zuhe = new bomao.Lucky28.list.playlist.zuhe({'parentPrize':me});
			me.play_zuhe.container = me.container.find('.bet-panel-zuhe');
			// me.container.find('.bet-panel-zuhe').hide();
			me.play_zuhe.buildUI();
			//和值玩法
			me.play_hezhi = new bomao.Lucky28.list.playlist.hezhi({'parentPrize':me});
			me.play_hezhi.container = me.container.find('.bet-panel-hezhi');
			me.play_hezhi.buildUI();

			me.playlist =[me.play_zuhe,me.play_hezhi];
			me.currentPlay = me.play_zuhe;
		},
		//切换玩法
		swtichplay:function(index){
			var me = this;

			if(index == 1 && me.currentPlayIndex!=1){
				me.container.find('.bet-panel-hezhi').addClass('prize-left-move');
				me.container.find('.bet-panel-zuhe').addClass('prize-left-move-miss');
			}
			if(index == 0 && me.currentPlayIndex!=0){
				me.container.find('.bet-panel-hezhi').addClass('prize-right-move-miss');
				me.container.find('.bet-panel-zuhe').addClass('prize-right-move');
			}

			me.currentPlayIndex = index;
			me.currentPlay = me.playlist[index];
			
			me.fireEvent('afert_select_play',me.currentPlayIndex);
		},
		//展示玩法
		showplay:function(index){
			var me = this;
			//切换选择框
			me.parentGame.container.find('.play-choose').removeClass().addClass('play-choose').addClass('play-choose-select-'+index);
			//切换玩法
			me.parentGame.container.find('.prize-id-'+me.prize_id).find('.bet').removeClass().addClass('bet').addClass('bet-panel-'+index);
		},
		//获取当前奖期的游戏玩法
		getCurrentPlay:function(){
			var me = this;
			return me.currentPlay;
		},
		//获取当前奖期的游戏玩法Dom节点
		getCurrentPlayDOM:function(index){
			var me = this;
			if(index == 0){
				return me.container.find(".bet-panel-zuhe");
			}else{
				return me.container.find(".bet-panel-hezhi");
			}
		},
		//加载信息面板
		loadinformation:function(){
			var me = this;
			//倒计时信息模板
			me.information_timer = new bomao.Lucky28.list.informationlist.informationtimer();
			me.information_timer.parentPrize = me;
			me.information_timer.container = me.container.find('.information-panel-timer');
			me.information_timer.buildUI();

			me.information_timer.setLeftTime(me.leftTime);

			//等待开奖信息模板
			me.information_wait = new bomao.Lucky28.list.informationlist.informationwait();
			me.information_wait.parentPrize = me;
			me.information_wait.container = me.container.find('.information-panel-wait');
			me.information_wait.buildUI();

			//开奖结果信息模板
			me.information_result = new bomao.Lucky28.list.informationlist.informationresult();
			me.information_result.parentPrize = me;
			me.information_result.container = me.container.find('.information-panel-result');
			me.information_result.buildUI();

			//停盘信息版
			me.information_suspension = new bomao.Lucky28.list.informationlist.informationSuspension();
			me.information_suspension.parentPrize = me;
			me.information_suspension.container = me.container.find('.information-panel-suspension');
			me.information_suspension.buildUI();

			informationlist=[me.information_timer , me.information_wait , me.information_result , me.information_suspension];

		},
		//修改状态，重新渲染
		//暂定０为受注游戏，１封盘状态　2待开奖状态 3开奖中状态　4已开奖状态 5已封盘状态
		updataStatus:function(status){
			var me = this;
			me.status = status;

			if(status==2){
				me.fireEvent('start_catch_issue_result' , me.prize_id);
			}

			me.container.removeClass().addClass('prize-id-'+me.prize_id).addClass("panel-main-status-"+status);

			me.parentGame.showPrizesStatus();

		},
		//刷新奖期
		fleshPrize:function(leftTime , status){
			var me = this;
			me.leftTime = leftTime;
			me.information_timer.setLeftTime(me.leftTime);
			me.updataStatus(status);
		},
		//获取开奖号码
		catchIssue:function(data){
			var me = this;

			var service = new bomao.Lucky28.DataService();

			service.getPrizeIssueByPrizeID(me.parentGame.id,function(data){
				if(data[1]){
					var resultData = me.prize_id == data[0].number?data[0].code:(me.prize_id == data[1].number?data[1].code : "");
				}else{
					var resultData = data[0].code;
				}

				if(resultData != ""){
					if(resultData != "/"){
						var result_num = resultData.replace(/\s+/g,"");

						me.status = 3;
						me.play_zuhe.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
						me.play_hezhi.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-money-lab').removeClass('bet-locked-button').addClass('bet-locked-button');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img').removeClass('bet-chip-locked').addClass('bet-chip-locked');
						me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img-normal').removeClass('bet-chip-locked').addClass('bet-chip-locked');
						me.play_hezhi.container.find('.hezhi-li-style-select-bet').removeClass('bet-locked-button').addClass('bet-locked-button');
						me.fireEvent("change_prize_status" , me);

						var result_data = {
							'num_1':Number(result_num.charAt(0)),
							'num_2':Number(result_num.charAt(1)),
							'num_3':Number(result_num.charAt(2)),
							'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
						}
						me.result_number = result_data;

						me.information_result.updateResult(result_data);

						me.fireEvent("start_lottery_animation" , result_data);

						//开奖结束后显示走势图
						me.resultNumberData = {'code':resultData , 'number':me.prize_id};
					}else{
						//取消奖期效果
						me.information_wait.container.find('.wait_label').html("奖期已取消");
						var lab_str = ".lab-"+me.parentGame.priedIDArr.indexOf(me.prize_id);
						me.parentGame.container.find(lab_str).html('已取消');
						me.status = 6;
						//开奖结束后显示走势图
						me.parentGame.mini_history.updataSourceData({'code':resultData,'number':me.prize_id});
					}
					
				}else{
					me.parentGame.mini_history.updataSourceData({'code':'','number':me.prize_id});

					var updata = setInterval(function(){
						service.getPrizeIssueByPrizeID(me.parentGame.id,function(data){
							resultData = me.prize_id == data[0].number?data[0].code:(me.prize_id == data[1].number?data[1].code : "");

							if(resultData != ""){
								clearInterval(updata);
								if(resultData != "/"){
									var result_num = resultData.replace(/\s+/g,"");

									me.status = 3;
									me.play_zuhe.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
									me.play_hezhi.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-money-lab').removeClass('bet-locked-button').addClass('bet-locked-button');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img').removeClass('bet-chip-locked').addClass('bet-chip-locked');
									me.play_zuhe.container.find('.li-style-select-bet').find('.bet-chip-img-normal').removeClass('bet-chip-locked').addClass('bet-chip-locked');
									me.play_hezhi.container.find('.hezhi-li-style-select-bet').removeClass('bet-locked-button').addClass('bet-locked-button');
									me.fireEvent("change_prize_status" , me);

									var result_data = {
										'num_1':Number(result_num.charAt(0)),
										'num_2':Number(result_num.charAt(1)),
										'num_3':Number(result_num.charAt(2)),
										'num_total':Number(result_num.charAt(0))+Number(result_num.charAt(1))+Number(result_num.charAt(2))
									}

									me.information_result.updateResult(result_data);

									me.fireEvent("start_lottery_animation" , result_data);

									//开奖结束后显示走势图
									me.resultNumberData = {'code':resultData , 'number':me.prize_id};
								}else{
									//取消奖期效果
									me.information_wait.container.find('.wait_label').html("奖期已取消");
									var lab_str = ".lab-"+me.parentGame.priedIDArr.indexOf(me.prize_id);
									me.parentGame.container.find(lab_str).html('已取消');
									me.status = 6;
									//开奖结束后显示走势图
									me.parentGame.mini_history.updataSourceData({'code':resultData,'number':me.prize_id});
								}
							}
						});
					}, 10*1000);
				}
			});
		},
		//开奖动画
		startAnimation:function(result_data){
			var me = this;
			me.information_result.playAnimation(result_data);
		},
		getParentGame:function(){
			var me = this;
			return me.parentGame;
		},
		setParentGame:function(game){
			var me = this;
			me.parentGame = game;
		},
		//解析投注记录
		analyzeOrderRecords:function(){
			var me = this;

			for(var i=0;i<me.play_zuhe.betAmountData.length;i++){
				me.play_zuhe.betAmountData[i] = 0;
			}

			for(var i=0;i<me.play_hezhi.betAmountData.length;i++){
				me.play_hezhi.betAmountData[i] = 0;
			}

			for(var i=0;i<me.prize_orders.length;i++){
				var ball = me.prize_orders[i].balls+'';
				var money = (me.prize_orders[i].money).split('.')[0];

				switch(ball){
					case '大': 
						me.play_zuhe.betAmountData[0] = Number(me.play_zuhe.betAmountData[0])+Number(money);
						break;
					case '小':
						me.play_zuhe.betAmountData[1] = Number(me.play_zuhe.betAmountData[1])+Number(money);
						break;
					case '单':
						me.play_zuhe.betAmountData[2] = Number(me.play_zuhe.betAmountData[2])+Number(money);
						break;
					case '双':
						me.play_zuhe.betAmountData[3] = Number(me.play_zuhe.betAmountData[3])+Number(money);
						break;
					case '极大':
						me.play_zuhe.betAmountData[4] = Number(me.play_zuhe.betAmountData[4])+Number(money);
						break;
					case '极小':
						me.play_zuhe.betAmountData[5] = Number(me.play_zuhe.betAmountData[5])+Number(money);
						break;
					case '大单':
						me.play_zuhe.betAmountData[6] = Number(me.play_zuhe.betAmountData[6])+Number(money);
						break;
					case '大双':
						me.play_zuhe.betAmountData[7] = Number(me.play_zuhe.betAmountData[7])+Number(money);
						break;
					case '小单':
						me.play_zuhe.betAmountData[8] = Number(me.play_zuhe.betAmountData[8])+Number(money);
						break;
					case '小双':
						me.play_zuhe.betAmountData[9] = Number(me.play_zuhe.betAmountData[9])+Number(money);
						break;
					case '0':
						me.play_hezhi.betAmountData[0] = Number(me.play_hezhi.betAmountData[0])+Number(money);
						break;
					case '1':
						me.play_hezhi.betAmountData[1] = Number(me.play_hezhi.betAmountData[1])+Number(money);
						break;
					case '2':
						me.play_hezhi.betAmountData[2] = Number(me.play_hezhi.betAmountData[2])+Number(money);
						break;
					case '3':
						me.play_hezhi.betAmountData[3] = Number(me.play_hezhi.betAmountData[3])+Number(money);
						break;
					case '4':
						me.play_hezhi.betAmountData[4] = Number(me.play_hezhi.betAmountData[4])+Number(money);
						break;
					case '5':
						me.play_hezhi.betAmountData[5] = Number(me.play_hezhi.betAmountData[5])+Number(money);
						break;
					case '6':
						me.play_hezhi.betAmountData[6] = Number(me.play_hezhi.betAmountData[6])+Number(money);
						break;
					case '7':
						me.play_hezhi.betAmountData[7] = Number(me.play_hezhi.betAmountData[7])+Number(money);
						break;
					case '8':
						me.play_hezhi.betAmountData[8] = Number(me.play_hezhi.betAmountData[8])+Number(money);
						break;
					case '9':
						me.play_hezhi.betAmountData[9] = Number(me.play_hezhi.betAmountData[9])+Number(money);
						break;
					case '10':
						me.play_hezhi.betAmountData[10] = Number(me.play_hezhi.betAmountData[10])+Number(money);
						break;
					case '11':
						me.play_hezhi.betAmountData[11] = Number(me.play_hezhi.betAmountData[11])+Number(money);
						break;
					case '12':
						me.play_hezhi.betAmountData[12] = Number(me.play_hezhi.betAmountData[12])+Number(money);
						break;
					case '13':
						me.play_hezhi.betAmountData[13] = Number(me.play_hezhi.betAmountData[13])+Number(money);
						break;
					case '14':
						me.play_hezhi.betAmountData[14] = Number(me.play_hezhi.betAmountData[14])+Number(money);
						break;
					case '15':
						me.play_hezhi.betAmountData[15] = Number(me.play_hezhi.betAmountData[15])+Number(money);
						break;
					case '16':
						me.play_hezhi.betAmountData[16] = Number(me.play_hezhi.betAmountData[16])+Number(money);
						break;
					case '17':
						me.play_hezhi.betAmountData[17] = Number(me.play_hezhi.betAmountData[17])+Number(money);
						break;
					case '18':
						me.play_hezhi.betAmountData[18] = Number(me.play_hezhi.betAmountData[18])+Number(money);
						break;
					case '19':
						me.play_hezhi.betAmountData[19] = Number(me.play_hezhi.betAmountData[19])+Number(money);
						break;
					case '20':
						me.play_hezhi.betAmountData[20] = Number(me.play_hezhi.betAmountData[20])+Number(money);
						break;
					case '21':
						me.play_hezhi.betAmountData[21] = Number(me.play_hezhi.betAmountData[21])+Number(money);
						break;
					case '22':
						me.play_hezhi.betAmountData[22] = Number(me.play_hezhi.betAmountData[22])+Number(money);
						break;
					case '23':
						me.play_hezhi.betAmountData[23] = Number(me.play_hezhi.betAmountData[23])+Number(money);
						break;
					case '24':
						me.play_hezhi.betAmountData[24] = Number(me.play_hezhi.betAmountData[24])+Number(money);
						break;
					case '25':
						me.play_hezhi.betAmountData[25] = Number(me.play_hezhi.betAmountData[25])+Number(money);
						break;
					case '26':
						me.play_hezhi.betAmountData[26] = Number(me.play_hezhi.betAmountData[26])+Number(money);
						break;
					case '27':
						me.play_hezhi.betAmountData[27] = Number(me.play_hezhi.betAmountData[27])+Number(money);
						break;

					default:break;
				}
			}

			me.play_zuhe.updateRealyBetButtonDate();
			me.play_hezhi.updateRealyBetButtonDate();
			if(me.result_number){
				me.information_result.updateResult(me.result_number);
			}
		}
		
	};

	var html_all = [];
		html_all.push('<div class="bet bet-panel-0">');
			html_all.push('<div class="bet-panel bet-panel-zuhe">');
			html_all.push('</div>');

			html_all.push('<div class="bet-panel bet-panel-hezhi">');
			html_all.push('</div>');

			html_all.push('<div class="bet-panel-mask">');
			html_all.push('</div>');
		html_all.push('</div>');

		html_all.push('<div class="information-panel">');
			html_all.push('<div class="information-panel-timer">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-wait">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-result">');
			html_all.push('</div>');

			html_all.push('<div class="information-panel-suspension">');
			html_all.push('</div>');
		html_all.push('</div>');

	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list[defConfig.name] = Main;
})(bomao, bomao.Event);