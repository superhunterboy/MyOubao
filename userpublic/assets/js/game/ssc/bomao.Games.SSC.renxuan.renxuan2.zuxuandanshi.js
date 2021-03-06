﻿
(function(host, Danshi, undefined){
	var defConfig = {
		name:'renxuan.renxuan2.zuxuandanshi',
		//玩法提示
		tips: '',
		//选号实例
		exampleTip: ''
	},
	Games = host.Games,
	SSC = Games.SSC.getInstance();
	
	
	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;
			//建立编辑器DOM
			//防止绑定事件失败加入定时器
			/**
			setTimeout(function(){
				me.initFrame();
				me.initPositionOption();
			},25);
			**/
			me.initFrame();
			me.initPositionOption();
		},
		rebuildData:function(){
			var me = this;
			me.balls = [
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
		},
		makePostParameter:function(data, order){
			var me = this,
				result = [],
				data = order['original'],
				i = 0;
			for (; i < data.length; i++) {
				result = result.concat(data[i].join(''));
			}
			return result.join('|');
		},
		getOrderExtraData:function(){
			var me = this;
			return {'position':me.getPositionOptionIndex()};
		},
		getPositionOptionIndex:function(){
			var me = this,option = me.getPositionOptionData(),result = [];
			$.each(option, function(i){
				if(this > 0){
					result.push(i);
				}
			});
			return result;
		},
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum){
			var me = this;

			return lotteryNum.length == me.balls.length && lotteryNum[0] != lotteryNum[1];
		},
		getLottery:function(){
			var me = this, 
				data = me.getHtml(),
				has = {},
				result = [],
				optionArr,
				optionIndex = [],
				optionResult = [],
				tempArr = [],
				resultData = [];
			if(data == ''){
				return [];
			}
			optionArr  = me.getPositionOptionData();
			data = me.checkBallIsComplete(data);

			$.each(data, function(i){
				if(!has[data[i]]){
					result.push(data[i]);
					has[data[i]] = true;
				}
			});

			$.each(optionArr, function(i){
				if(this > 0){
					optionIndex.push(i);
				}
			});
			optionResult = me.combine(optionIndex, 2);

			$.each(optionResult, function(i){
				optionResult[i] = optionResult[i].join(',');
			});

			tempArr.push(optionResult);
			tempArr.push(result);

			resultData = me.combination(tempArr);

			me.isBallsComplete = resultData.length > 0 ? true : false;
			//console.log(resultData);
			//返回投注
			return resultData;
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete:function(data){
			var me = this,
				len,
				i = 0,
				balls,
				has = {},
				result = [];

				me.aData = [];
				me.vData = [];
				me.sameData = [];
				me.errorData = [];
				me.tData = [];
			
			//按规则进行拆分结果
			result = me.iterator(data);
			len = result.length;

			for(i = 0; i < len; i++){
				balls = result[i].split('');
				balls.sort();
				//检测基本长度
				if(me.checkSingleNum(balls)){
					if(has[balls]){
						//重复
						me.sameData.push(balls);
					}else{
						me.tData.push(balls);
						has[balls] = true;
					}
				}else{
					me.errorData.push(balls);
				}
			}
			//校验
			if(me.tData.length > 0){
				me.isBallsComplete = true;
				return me.tData;
			}else{
				me.isBallsComplete = false;
				return [];
			}
		},
		initPositionOption:function(){
			var me = this,
				dom,
				labels,
				inputs,
				CLS = 'current',
				random = (''+Math.random()).replace('0.', ''),
				html = ['<div class="balls-import-positionOption">'];
			html.push('<label for="J-position-option-'+ random +'-0"><input id="J-position-option-'+ random +'-0" data-index="0" type="checkbox" />万位</label>');
			html.push('<label for="J-position-option-'+ random +'-1"><input id="J-position-option-'+ random +'-1" data-index="1" type="checkbox" />千位</label>');
			html.push('<label for="J-position-option-'+ random +'-2"><input id="J-position-option-'+ random +'-2" data-index="2" type="checkbox" />百位</label>');
			html.push('<label class="current" for="J-position-option-'+ random +'-3"><input id="J-position-option-'+ random +'-3" data-index="3" type="checkbox" checked="checked" />十位</label>');
			html.push('<label class="current" for="J-position-option-'+ random +'-4"><input id="J-position-option-'+ random +'-4" data-index="4" type="checkbox" checked="checked" />个位</label>');
			html.push('</div>');
			dom = $(html.join(''));
			me.container.find('form').eq(0).parent().prepend(dom);

			labels = dom.find('label');
			inputs = dom.find('input');
			me.positionOptionInputs = inputs;
			inputs.click(function(){
				var el = $(this);
				if(this.checked){
					el.parent().addClass(CLS);
				}else{
					if(me.isCheckPositionOption()){
						el.parent().removeClass(CLS);
					}else{
						this.checked = true;
					}
				}
				me.updateData();
				me.fireEvent('afterSwitchPositionOption', inputs);
			});
		},
		isCheckPositionOption:function(){
			var me = this,inputs = me.positionOptionInputs,num = 0;
			if(typeof inputs == 'undefined'){
				return result;
			}
			inputs.each(function(){
				if(this.checked){
					num++;
				}
			});
			return num > 1 ? true : false;
		},
		getPositionOptionData:function(){
			var me = this,inputs = me.positionOptionInputs,result = [];
			inputs.each(function(i){
				result[i] = 0;
				if(this.checked){
					result[i]  = 1;
				}
			});
			return result;
		},
		//生成一个当前玩法的随机投注号码
		//该处实现复式，子类中实现其他个性化玩法
		//返回值： 按照当前玩法生成一注标准的随机投注单(order)
		randomNum:function(){
			var me = this,
				i = 0, 
				current = [], 
				currentNum, 
				ranNum,
				order = null,
				dataNum = me.getBallData(),
				name_en = Games.getCurrentGame().getCurrentGameMethod().getGameMethodName(),
				name = me.defConfig.name,
				lotterys = [],
				original = [];
			
			//增加机选标记
			me.addRanNumTag();

			current  = me.checkRandomBets();
			original = [[current.join(',')],[],[],[]];
			lotterys = me.combination(current);
			//生成投注格式
			order = {
				'type': name_en,
				'original':original,
				'lotterys':lotterys,
				'moneyUnit': Games.getCurrentGameStatistics().getMoneyUnit(),
				'multiple': Games.getCurrentGameStatistics().getMultip(),
				'onePrice': Games.getCurrentGame().getGameConfig().getInstance().getOnePrice(name_en),
				'num': lotterys.length
			};
			order['amountText'] = Games.getCurrentGameStatistics().formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
			return order;		
		}
	};
	
	
	//继承Danshi
	var Main = host.Class(pros, Danshi);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器上
	SSC.setLoadedHas(defConfig.name, new Main());
	
	
	
})(bomao, bomao.Games.SSC.Danshi);

