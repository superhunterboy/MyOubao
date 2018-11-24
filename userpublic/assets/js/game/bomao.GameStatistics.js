
//游戏选球统计，如注数、当前操作金额等
(function(host, name, Event, undefined){
	var defConfig = {
		//主面板dom
		mainPanel:'#J-balls-statistics-panel',
		//注数dom
		lotteryNumDom:'#J-balls-statistics-lotteryNum',
		//倍数
		multipleDom:'#J-balls-statistics-multiple',
		//总金额
		amountDom:'#J-balls-statistics-amount',
		moneyUnitDom:'#J-balls-statistics-moneyUnit',
		//元/角模式比例  1为元模式 0.1为角模式
		moneyUnit:1,
		//元角模式对应的中文
		moneyUnitData:{'0.01':'分','0.1':'角','1':'元'},
		//倍数
		multiple:1
	},
	instance,
	Games = host.Games;


	var pros = {
		init:function(cfg){
			var me = this;
			Games.setCurrentGameStatistics(me);

			me.panel = $(cfg.mainPanel);
			me.moneyUnit = cfg.moneyUnit;
			me.multiple = cfg.multiple;
			//已组合好的选球数据
			me.lotteryData = [];




			//倍数选择模拟下拉框
			me.multipleDom = new bomao.Select({cls:'select-game-statics-multiple',realDom:cfg.multipleDom,isInput:true,expands:{inputEvent:function(){
													var meSelect = this;
													this.getInput().keyup(function(e){
														var v = this.value,
															id = Games.getCurrentGame().getCurrentGameMethod().getId(),
															unit = me.getMoneyUnit(),
															maxv = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
														
														this.value = this.value.replace(/[^\d]/g, '');
														if($.trim(this.value) != ''){
															v = Number(this.value);
															if(v < 1){
																this.value = 1;
															}else if(v > maxv){
																this.value = maxv;
															}
															meSelect.setValue(this.value);
														}
													});
													this.getInput().blur(function(){
														var v = this.value,
															id = Games.getCurrentGame().getCurrentGameMethod().getId(),
															unit = me.getMoneyUnit(),
															maxv = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
														this.value = this.value.replace(/[^\d]/g, '');
														v = Number(this.value);
														if(v < 1){
															this.value = 1;
														}else if(v > maxv){
															this.value = maxv;
														}
														meSelect.setValue(this.value);
													});
												}}});
			me.multipleDom.setValue(me.multiple);
			me.multipleDom.addEvent('change', function(e, value, text){
				var num = Number(value),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = me.getMoneyUnit(),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit),
					method = Games.getCurrentGame().getCurrentGameMethod(),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					prizesMultipleBound;

				if(methodCfg['is_enable_extra'] == 1){
					prizesMultipleBound = me.getPrizesMultipleBound();
					maxnum = Math.min(prizesMultipleBound['min'], prizesMultipleBound['max']);
				}

				if(num > maxnum){
					num = maxnum;
					this.setValue(num);
				}
				me.setMultiple(num);
				//console.log(Games.getCurrentGame().getCurrentGameMethod().getLottery());
				me.updateData({
					'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),
					'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal(),
					'position':Games.getCurrentGame().getCurrentGameMethod().getPositionOptionData()
				}, Games.getCurrentGame().getCurrentGameMethod().getName());
			});
			//手动加减
			$('#J-bet-statics-multiple-reduce').click(function(){
				var v = Number(me.multipleDom.getValue()),v2 = (v - 1) < 1 ? 1 : v - 1;
				me.multipleDom.setValue(v2);
			});
			$('#J-bet-statics-multiple-add').click(function(){
				var v = Number(me.multipleDom.getValue());
				me.multipleDom.setValue(v + 1);
			});




			/**
			//元角模式模拟下拉框
			//me.moneyUnitDom = new host.SlideCheckBox({realDom:cfg.moneyUnitDom});
			me.moneyUnitDom = new host.Select({realDom:cfg.moneyUnitDom});
			//在未添加change事件之前设置初始值
			me.moneyUnitDom.setValue(me.moneyUnit);
			me.moneyUnitDom.addEvent('change', function(e, value, text){
				var multiple = me.getMultip(),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Number(value),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
				multiple = multiple > maxnum ? maxnum : multiple;
				me.setMultipleDom(multiple);

				me.setMoneyUnit(Number(value));
				me.updateData({'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal()}, Games.getCurrentGame().getCurrentGameMethod().getName());
				
				
				//更新单注奖金金额
				var prize = Number(methodCfg['prize']) * unit;
				prize = bomao.util.formatMoney(prize);
				prize = prize.split('.');
				prize[1] = '<i>' + prize[1] + '</i>';
				prize = prize.join('.');
				$('#J-method-prize').html(prize);
			});
			**/



			//元角模式tab操作
			me.moneyUnitDom = new host.Tab({par:'#J-bet-statics-tab-moneyunit', triggers:'.item', panels:'.bet-statics-moneyunit-cont', eventType:'click'});
			me.moneyUnitDom.setValue = function(v){
				var v = '' + $.trim(v),curr = this.par.find('[data-value="'+ v +'"]'),index = this.triggers.index(curr.get(0));
				this.triggers.removeClass('current');
				curr.addClass('current');
				this.index = index;
				v = Number(v);
				me.setMoneyUnit(v);
			};
			me.moneyUnitDom.getValue = function(){
				return Number(this.par.find('.current').attr('data-value'));
			};
			me.moneyUnitDom.addEvent('afterSwitch', function(e, i){
				var multiple = me.getMultip(),
					method = Games.getCurrentGame().getCurrentGameMethod(),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Number(this.triggers.eq(i).attr('data-value')),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit),
					prizesMultipleBound;

				multiple = multiple > maxnum ? maxnum : multiple;

				if(methodCfg['is_enable_extra'] == 1){
					prizesMultipleBound = me.getPrizesMultipleBound();
					multiple = Math.min(multiple, prizesMultipleBound['min']);
					multiple = Math.min(multiple, prizesMultipleBound['max']);
				}
				

				me.setMultipleDom(multiple);

				me.setMoneyUnit(unit);
				me.updateData({
						'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),
						'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal(),
						'position':Games.getCurrentGame().getCurrentGameMethod().getPositionOptionData()
					}, Games.getCurrentGame().getCurrentGameMethod().getName());
				
				//
				if(methodCfg['is_enable_extra'] == 1 && (method.getName().indexOf('hezhi.wuxing.hezhi') == 0 || method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0)){
					me.setMultiplePrizes();
				}else{
					//更新单注奖金金额
					var prize = Number(methodCfg['prize']) * unit;
					prize = bomao.util.formatMoney(prize);
					prize = prize.split('.');
					prize[1] = '<i>' + prize[1] + '</i>';
					prize = prize.join('.');
					$('#J-method-prize').html(prize);
				}

			});
			me.moneyUnitDom.setValue(me.moneyUnit);


			//初始化相关界面，使得界面和配置统一
			me.updateData({'lotterys':[], 'position':[], 'original':[]});
			

			me.initRebate();
		},
		getPrizesMultipleBound:function(){
			var method = Games.getCurrentGame().getCurrentGameMethod(),
				methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(method.getId()),
				allBallsDoms = method.getBallsDom(),
				i = 0,
				len = allBallsDoms.length,
				j = 0,
				len2,
				multipleArr = [],
				allMultipleArr = [],
				extracfg,
				unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue());

			for(var p in methodCfg['extra']){
				if(methodCfg['extra'].hasOwnProperty(p)){
					allMultipleArr.push(Number(methodCfg['extra'][p]));
				}
			}
			allMultipleArr.sort(function(a, b){
				return a - b;
			});

			extracfg = methodCfg['extra'];

			var cnHash = {
				'大':'1',
				'小':'0',
				'单':'3',
				'双':'2'
			};

			for(i = 0; i < len; i++){
				for(j = 0; j < allBallsDoms[i].length; j++){
					if(allBallsDoms[i][j].className.indexOf('ball-number-current') != -1){
						if(method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0){
							multipleArr.push(Number(extracfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
						}else{
							multipleArr.push(Number(extracfg['' + Number(allBallsDoms[i][j].innerHTML)]));
						}
					}
				}
			}
			multipleArr.sort(function(a, b){
				return a - b;
			});
			if(multipleArr.length == 1){
				return {min:multipleArr[0] / unit, max:multipleArr[0] / unit};
			}else if(multipleArr.length == 0){
				return {min:allMultipleArr[0] / unit, max:allMultipleArr[allMultipleArr.length - 1] / unit};
			}else{
				return {min:multipleArr[0] / unit, max:multipleArr[multipleArr.length - 1] / unit};
			}
		},
		setMultiplePrizes:function(){
			var method = Games.getCurrentGame().getCurrentGameMethod(),
				methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(method.getId()),
				allBallsDoms = method.getBallsDom(),
				i = 0,
				len = allBallsDoms.length,
				j = 0,
				len2,
				multipleArr = [],
				maxmultiple = 1,
				prizesArr = [],
				allprizesArr = [],
				extracfg,
				extraprizecfg,
				prizes = [],
				unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue());

			for(var p in methodCfg['extra_prize']){
				if(methodCfg['extra_prize'].hasOwnProperty(p)){
					allprizesArr.push(Number(methodCfg['extra_prize'][p]));
				}
			}
			allprizesArr.sort(function(a, b){
				return a - b;
			});

			extracfg = methodCfg['extra'];
			extraprizecfg = methodCfg['extra_prize'];

			var cnHash = {
				'大':'1',
				'小':'0',
				'单':'3',
				'双':'2'
			};

			for(i = 0; i < len; i++){
				for(j = 0; j < allBallsDoms[i].length; j++){
					if(allBallsDoms[i][j].className.indexOf('ball-number-current') != -1){
						if(method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0){
							multipleArr.push(Number(extracfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
							prizesArr.push(Number(extraprizecfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
						}else{
							multipleArr.push(Number(extracfg['' + Number(allBallsDoms[i][j].innerHTML)]));
							prizesArr.push(Number(extraprizecfg['' + Number(allBallsDoms[i][j].innerHTML)]));
						}
					}
				}
			}
			prizesArr.sort(function(a, b){
				return a - b;
			});
			multipleArr.sort(function(a, b){
				return a - b;
			});
			if(prizesArr.length == 0){
				prizes.push((allprizesArr[0] * unit).toFixed(4));
				prizes.push((allprizesArr[allprizesArr.length - 1] * unit).toFixed(4));
				$('#J-method-prize').html(prizes.join(' - '));
				return;
			}
			if(prizesArr.length == 1){
				prizes.push(prizesArr.length > 0 ? (prizesArr[0] * unit).toFixed(4) : 0);
				$('#J-method-prize').html(prizes.join(' - '));
			}else{
				prizes.push(prizesArr.length > 0 ? (prizesArr[0] * unit).toFixed(4) : 0);
				prizes.push(prizesArr.length > 0 ? (prizesArr[prizesArr.length - 1] * unit).toFixed(4) : 0);
				$('#J-method-prize').html(prizes.join('-'));
			}
		},
		//初始化返点内容
		initRebate:function(){
			var me = this,
				cfg = Games.getCurrentGame().getGameConfig().getInstance(),
				subgroup = Number(cfg.getConfig('subtract_prize_group')),
				min = Number(cfg.getConfig('bet_min_prize_group')),
				max = Number(cfg.getConfig('user_prize_group')),
				umax = Number(cfg.getConfig('bet_max_prize_group')),
				ugroup = Number(cfg.getConfig('user_prize_group')),
				base = Number(cfg.getConfig('series_amount')),
				list = [],
				num = Math.floor(min/10) * 10,
				per,
				html = [],
				resultdata = [];
			list.push(min);
			while(num < max){
				if(num != min){
					list.push(num);
				}
				num += 10;
			}
			list.push(max);
			$.each(list, function(i){
				if(this <= umax){
					resultdata.push(this);
				}
			});
			$.each(resultdata, function(i){
				per = ((max - this)/base*100).toFixed(2);
				if(i == 0){
					html.push('<option value="'+ (this) +'">'+ (this) + ' - ' + per +'%</option>');
				}else if(i == resultdata.length - 1){
					html.push('<option selected="selected" value="'+ (this) +'">'+ (this) + ' - ' + per +'%</option>');
				}
			});


			$('#J-select-rebate').html(html.join(''));

			//返点
			me.rebateSelect = new host.Select({
				realDom:'#J-select-rebate',
				cls:'w-2'
			});

		},
		getMultipleDom:function(){
			return this.multipleDom;
		},
		getMultipleTextDom:function(){
			return $('#J-balls-statistics-multiple-text');
		},
		getMoneyUnitText:function(moneyUnit){
			return this.defConfig.moneyUnitData[''+moneyUnit];
		},
		//更新各种数据
		updateData:function(data, name){
			var me = this,
				cfg = me.defConfig,
				count = data['lotterys'].length,
				price = 2,
				multiple = me.multiple,
				moneyUnit = me.moneyUnit;

				if(Games.getCurrentGame() && Games.getCurrentGame().getCurrentGameMethod()){
					price = Games.getCurrentGame().getGameConfig().getInstance().getOnePriceById(Games.getCurrentGame().getCurrentGameMethod().getId());
				}

			//设置投注内容
			me.setLotteryData(data);
			//设置倍数
			//由于设置会引发updateData的死循环，因此在init里手动设置一次，之后通过change事件触发updateData
			//me.setMultipleDom(multiple);
			//更新元角模式
			//me.setMoneyUnitDom(moneyUnit);
			//更新注数
			me.setLotteryNumDom(data['lotterys'].length);
			//更新总金额
			me.setAmountDom(me.formatMoney(count * moneyUnit * multiple * price));
			//参数：注数、金额
			me.fireEvent('afterUpdate', data['lotterys'].length, count * moneyUnit * multiple * price);

		},
		//获取当前数据
		getResultData:function(){
			var me = this,
				cfg = Games.getCurrentGame().getGameConfig().getInstance(),
				subgroup = Number(cfg.getConfig('subtract_prize_group')),
				onePrice,
				method = Games.getCurrentGame().getCurrentGameMethod(),
				lotterys = me.getLotteryData();
			if(lotterys['lotterys'].length < 1){
				return {};
			}
			onePrice = Games.getCurrentGame().getGameConfig().getInstance().getOnePriceById(method.getId());
			return {
					mid:method.getId(),
					type:method.getName(),
					original:lotterys['original'],
					position:lotterys['position'],
					lotterys:lotterys['lotterys'],
					prize_group:Number(me.rebateSelect.getValue()) + subgroup,
					moneyUnit:me.moneyUnit,
					num:lotterys['lotterys'].length,
					multiple:me.multiple,
					//单价
					//onePrice:me.onePrice,
					//单价修改为从动态配置中获取，因为每个玩法有可能单注价格不一样
					onePrice:onePrice,
					//总金额
					amount:lotterys['lotterys'].length * me.moneyUnit * me.multiple * onePrice,
					//格式化后的总金额
					amountText:me.formatMoney(lotterys['lotterys'].length * me.moneyUnit * me.multiple * onePrice)
				};
		},
		//设置元角模式
		setMoneyUnit:function(num){
			var me = this;
			me.moneyUnit = num;
			me.fireEvent('setMoneyUnit_after', num);
		},
		getMoneyUnit:function(){
			return this.moneyUnit;
		},
		getLotteryData:function(){
			return this.lotteryData;
		},
		setLotteryData:function(data){
			var me = this;
			me.lotteryData = data;
		},
		//将数字保留两位小数并且千位使用逗号分隔
		formatMoney:function(num){
			var num = Number(num),
				re = /(-?\d+)(\d{3})/;

			if(Number.prototype.toFixed){
				num = (num).toFixed(2);
			}else{
				num = Math.round(num*100)/100
			}
			num  =  '' + num;
			while(re.test(num)){
				num = num.replace(re,"$1,$2");
			}
			return num;
		},
		//注数
		getLotteryNumDom:function(){
			var me = this,cfg = me.defConfig;
			return me.lotteryNumDom || (me.lotteryNumDom = $(cfg.lotteryNumDom));
		},
		setLotteryNumDom:function(v){
			var me = this;
			me.getLotteryNumDom().html(v);
		},
		//倍数
		getMultipleDom:function(){
			return this.multipleDom;
		},
		getMultip: function() {
			var me = this;
			return me.multiple;
		},
		setMultipleDom:function(v){
			var me = this;
			me.getMultipleDom().setValue(v);
		},
		setMultiple:function(num){
			this.multiple = num;
		},
		//元角模式
		getMoneyUnitDom:function(){
			return this.moneyUnitDom;
		},
		setMoneyUnitDom:function(v){
			var me = this;
			me.getMoneyUnitDom().setValue(v);
		},
		hidesetMoneyUnitDom: function(){
			this.moneyUnitDom.hide();
		},
		//总金额
		getAmountDom:function(){
			var me = this,cfg = me.defConfig;
			return me.amountDom || (me.amountDom = $(cfg.amountDom));
		},
		setAmountDom:function(v){
			var me = this;
			me.getAmountDom().html(v);
		},
		reSet:function(){
			var me = this,cfg = me.defConfig;
			me.multipleDom.setValue(cfg.multiple);
			//me.moneyUnitDom.setValue(cfg.moneyUnit);
		}


	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;

})(bomao, "GameStatistics", bomao.Event);










