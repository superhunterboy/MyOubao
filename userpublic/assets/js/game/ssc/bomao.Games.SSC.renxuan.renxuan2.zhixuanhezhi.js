

//
(function(host, GameMethod, undefined){
	var defConfig = {
		name:'renxuan.renxuan2.zhixuanhezhi',
		//玩法提示
		tips:'',
		//选号实例
		exampleTip: ''
	},
	Games = host.Games,
	SSC = Games.SSC.getInstance();


	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;

			//默认加载执行30期遗漏号码
			//me.getHotCold(me.getGameMethodName(), 'currentFre', 'lost');
			//初始化冷热号事件
			//me.initHotColdEvent();
			me.initPositionOption();
		},
		//时时彩复式结构为5行10列
		//复位选球数据
		rebuildData:function(){
			var me = this;
			me.balls = [
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
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
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));
		},
		formatViewBalls:function(original){
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result = result.concat(original[i].join('|'));
			}
			return result.join('|');
		},
		makePostParameter: function(original){
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			//console.log(original);
			for (; i < len; i++) {
				result = result.concat(original[i].join('|'));
			}
			return result.join('|');
		},
		//计算各种结果
		mathResult: function(sum, nBegin, nEnd){
			var me = this,
				arr = [],
				checkArray = [],
				x,y;

			for (x=nBegin;x<=nEnd ;x++ ){
				for (y=nBegin;y<=nEnd ;y++ ){
					if(x+y == sum){
						arr.push([x,y]);
					}
				}
			}
			return arr;
		},
		//获取总注数/获取组合结果
		//isGetNum=true 只获取数量，返回为数字
		//isGetNum=false 获取组合结果，返回结果为单注数组
		getLottery:function(isGetNum){
			var me = this,
				data = me.getBallData()[0],
				i = 0,
				len = data.length,
				j = 0,
				len2,

				optionArr = me.getPositionOptionData(),
				optionIndex = [],
				optionResult = [],
				numArr = [],
				tempArr = [],
				result = [],
				resultData = [];

			$.each(optionArr, function(i){
				if(this > 0){
					optionIndex.push(i);
				}
			});
			optionResult = me.combine(optionIndex, 2);
			$.each(optionResult, function(i){
				optionResult[i] = optionResult[i].join(',');
			});

			for(i = 0;i < len;i++){
				if(data[i] > 0){
					numArr.push(i);
				}
			}


			for(i = 0,len = numArr.length;i < len;i++){
				result = result.concat(me.mathResult(numArr[i], 0, 9));
			}

			tempArr.push(optionResult);
			tempArr.push(result);

			resultData = me.combination(tempArr);

			me.isBallsComplete = resultData.length > 0 ? true : false;

			//console.log(resultData);

			return resultData;

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
			me.container.find('.number-select-title').parent().prepend(dom);

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
			inputs.each(function(){
				if(this.checked){
					num++;
				}
			});
			return num > 1 ? true : false;
		},
		getPositionOptionData:function(){
			var me = this,inputs = me.positionOptionInputs,result = [];
			if(typeof inputs == 'undefined'){
				return result;
			}
			inputs.each(function(i){
				result[i] = 0;
				if(this.checked){
					result[i]  = 1;
				}
			});
			return result;
		},
		//获取随机数
		randomNum:function(){
			var me = this,
				i = 0,
				current = [],
				currentNum,
				ranNum,
				lotterys = [],
				order = null,
				dataNum = me.getBallData(),
				len = me.getBallData()[0].length,
				name_en = Games.getCurrentGame().getCurrentGameMethod().getGameMethodName(),
				name = me.defConfig.name;

			current[0] = Math.floor(Math.random() * len);
			lotterys = me.mathResult(current[0], 0, 9);
			order = {
				'type':  name_en,
				'original':[current],
				'lotterys': lotterys,
				'moneyUnit': Games.getCurrentGameStatistics().getMoneyUnit(),
				'multiple': Games.getCurrentGameStatistics().getMultip(),
				'onePrice': Games.getCurrentGame().getGameConfig().getInstance().getOnePrice(name_en),
				'num': lotterys.length
			};
			order['amountText'] = Games.getCurrentGameStatistics().formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
			return order;
		}


	};




	//html模板
	var html_head = [];
		//头部
		html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div><div class="function-select-title"></div></div>');
		html_head.push('<div class="number-select-content">');
		html_head.push('<ul class="ball-section">');
		//每行
	var html_row = [];
			html_row.push('<li>');
			html_row.push('<div class="ball-title" style="display:none;"><strong><#=title#>位</strong><span></span></div>');
			html_row.push('<ul class="ball-content">');
			$.each([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18], function(i){
				html_row.push('<li class="lismall"><a href="javascript:void(0);" data-param="action=ball&value='+ this +'&row=<#=row#>" class="ball-number ball-number-small">'+this+'</a></li>');
			});
			html_row.push('</ul>');
		html_row.push('</li>');
	var html_bottom = [];
		html_bottom.push('</ul>');
		html_bottom.push('</div>');
		//拼接所有
	var html_all = [],rowStr = html_row.join('');
		html_all.push(html_head.join(''));
		$.each([''], function(i){
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
		});
		html_all.push(html_bottom.join(''));



	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	SSC.setLoadedHas(defConfig.name, new Main());

})(bomao, bomao.GameMethod);

