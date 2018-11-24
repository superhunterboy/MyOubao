
(function(host, Danshi, undefined){
	var defConfig = {
		name:'renxuan.renxuan3.zuliudanshi',
		//父容器
		UIContainer:'#J-balls-main-panel'
	},
	Games = host.Games,
	SSC = Games.SSC.getInstance();
	
	
	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;
			//建立编辑器DOM
			//防止绑定事件失败加入定时器
			setTimeout(function(){
				me.initFrame();
				me.initPositionOption();
			},25);
		},
		rebuildData:function(){
			var me = this;
			me.balls = [
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
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
			html.push('<label class="current" for="J-position-option-'+ random +'-2"><input id="J-position-option-'+ random +'-2" data-index="2" type="checkbox" checked="checked" />百位</label>');
			html.push('<label class="current" for="J-position-option-'+ random +'-3"><input id="J-position-option-'+ random +'-3" data-index="3" type="checkbox" checked="checked" />十位</label>');
			html.push('<label class="current" for="J-position-option-'+ random +'-4"><input id="J-position-option-'+ random +'-4" data-index="4" type="checkbox" checked="checked" />个位</label>');
			html.push('</div>');
			dom = $(html.join(''));
			me.container.find('form').eq(0).append(dom);

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
			return num > 2 ? true : false;
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
		checkNumSameIndex: function(num, index){
			var me = this,
				result,
				arr = num.length > 0 ? num : [];
			
			for (var i = 0; i < arr.length; i++) {
				if(me.arrIndexOf(arr[i], arr) >= index){
					result = true;
					break;
				}
			};
			
			return result || false;
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete:function(data){
				//去除中文 && 全角符号 && 英文字符
			var me = this,
				i = 0,
				result = [],
				tempArr = [],
				resultData = [],
				optionResult = [],
				optionIndex = me.getPositionOptionIndex();
				
				me.aData = [];
				me.sameData = [];
				me.errorData = [];
				me.tData = [];
				me.vData = [];
			
			//按规则进行拆分结果
			result = me.iterator(me.filterLotters(data)) || [];
			
			//判断结果
			for(;i<result.length;i++){
				if(me.defConfig.checkNum.test(result[i]) 
					&& result[i].length == me.balls.length
					&& !me.checkNumSameIndex(result[i], 2)){
					if(me.checkResult(result[i], me.tData)){
						me.tData.push(result[i].split(''))
					}else{
						if(me.checkResult(result[i], me.sameData)){
							//重复结果
							me.sameData.push(result[i].split(''));
						}
					}
					//正确结果[不去重]
					me.vData.push(result[i].split(''));
				}else{
					if(me.checkResult(result[i], me.errorData)){
						me.errorData.push(result[i].split(''));
					}
				}
				//记录
				if(me.checkResult(result[i], me.aData)){
					me.aData.push(result[i].split(''));
				}
			}



			optionResult = me.combine(optionIndex, 3);
			$.each(optionResult, function(i){
				optionResult[i] = optionResult[i].join(',');
			});
			tempArr.push(optionResult);
			tempArr.push(me.tData);
			resultData = me.combination(tempArr);

			
			//校验
			if(me.tData.length > 0){
				me.isBallsComplete = true;
				return resultData;
			}else{
				me.isBallsComplete = false;
				return [];
			}
		},
		//用拆分符号拆分成单注
		//组选中的单式需要重新排序去除重复
		iterator: function(data) {
			var me= this,
				cfg = me.defConfig,
				result = [],
				breakNum = 0;
			
			for (var i = 0; i < data.length; i++) {
				if(cfg.filtration.test(data.charAt(i))){
					result.push(data.substr(breakNum, i - breakNum));
					breakNum = i+1;
				}
			}
			$.each(result, function(i){
				result[i] = result[i].split('').sort().join('');		
			});
			return result;
		},
		randomNum:function(){
			var me = this,
				i = 0, 
				current = [], 
				currentNum, 
				ranNum,
				lotterys = [],
				order = null,
				original = [],
				dataNum = me.getBallData(),
				len = me.getBallData().length,
				rowLen = me.getBallData()[0].length,
				name_en = Games.getCurrentGame().getCurrentGameMethod().getGameMethodName(),
				name = me.defConfig.name;				

			//增加机选标记
			me.addRanNumTag();

			//生成随机数
			for(;i < len; i++){
				current[i] = me.removeSameNum(current)
			}
			current.sort(function(a, b){
				return a > b ? 1 : -1;
			});

			original = [[current.join(',')],[],[]];
			lotterys.push(current);
			//生成投注格式
			order = {
				'type': name_en,
				'original': original,
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
	
	
	//继承Danshi
	var Main = host.Class(pros, Danshi);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器上
	SSC.setLoadedHas(defConfig.name, new Main());
	
	
	
})(bomao, bomao.Games.SSC.Danshi);
