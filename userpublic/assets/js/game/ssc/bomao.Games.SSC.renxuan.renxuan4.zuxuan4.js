//五星直选复式玩法实现类
(function(host, GameMethod, undefined){
	var defConfig = {
		name:'renxuan.renxuan4.zuxuan4'
		
	},
	Games = host.Games,
	//游戏类
	SSC = host.Games.SSC.getInstance();
	
	
	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;
			me.initPositionOption();
		},
		//时时彩复式结构为5行10列
		//复位选球数据
		rebuildData: function(){
			var me = this;
			me.balls = [
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
		},
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
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
			html.push('<label class="current" for="J-position-option-'+ random +'-1"><input id="J-position-option-'+ random +'-1" data-index="1" type="checkbox" checked="checked" />千位</label>');
			html.push('<label class="current" for="J-position-option-'+ random +'-2"><input id="J-position-option-'+ random +'-2" data-index="2" type="checkbox" checked="checked" />百位</label>');
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
			return num > 3 ? true : false;
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
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete: function(){
			var me = this,
				ball = me.getBallData(),
				i = 0,
				len = ball[0].length,
				num = 0, oNum = 0;

			for(;i < len;i++){
				if(ball[0][i] > 0){
					oNum++;
				}
				if(ball[1][i] > 0){
					num++;
				}
			}
			//二重号大于1 && 单号大于3
			if(num >= 1 && oNum >= 1){
				return me.isBallsComplete = true;
			}
			return me.isBallsComplete = false;
		},
		//获取组合结果
		getLottery: function(){
			var me = this,
				ball = me.getBallData(),
				i = 0,
				len = ball[1].length,
				arr = [], 
				nr = new Array(),

				result = [],
				resultData = [],
				tempArr = [],
				optionResult = [],
				optionIndex = me.getPositionOptionIndex();
			
			//校验当前的面板
			//获取选中数字
			if(me.checkBallIsComplete()){
				for(;i < len;i++){
					if(ball[1][i] > 0){
						arr.push(i);
					}
				}
				//存储单号组合
				result = me.combine(arr, 1);
				//二重号组合
				for(var i=0,current;i<ball[0].length;i++){
					if(ball[0][i] == 1){
						//加上单号各种组合	
						for(var s=0;s<result.length;s++){
							if(me.arrIndexOf(i, result[s]) == -1){
								nr.push(result[s].concat([i,i,i]));
							}
						}
					}	
				}

				optionResult = me.combine(optionIndex, 4);
				$.each(optionResult, function(i){
					optionResult[i] = optionResult[i].join(',');
				});
				tempArr.push(optionResult);
				tempArr.push(nr);
				resultData = me.combination(tempArr);

				return resultData;
			}
			return [];
		},
		//获取随机数
		randomNum: function(){
			var me = this,
				i = 0,
				current = [],
				current2 = [],
				lotterys = [],
				len = me.getBallData()[0].length,
				name_en = Games.getCurrentGame().getCurrentGameMethod().getGameMethodName(),
				allArr = [],
				num;

			for(;i < 2;i++){
				if(i < 1){
					num = me.removeSame(allArr);
					current = current.concat(num);
					allArr.push(num);
				}else{
					num = me.removeSame(allArr);
					current2 = current2.concat(num);
					allArr.push(num);
				}
			}
			
			current.sort(function(a, b){
				return a - b;
			});
			
			lotterys.push(current[0]);
			lotterys = [lotterys.concat(current2)];
			
			original = [[lotterys[0][0]], [lotterys[0][1]]];
			
			
			order = {
				'type': name_en,
				'original':original,
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
		html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="" class="pick-rule">选号规则</a><a href="" class="win-info">中奖说明</a></div><div class="function-select-title"></div></div>');
		html_head.push('<div class="number-select-content">');
		html_head.push('<ul class="ball-section">');
		//每行
	var html_row = [];
		html_row.push('<li>');
		html_row.push('<div class="ball-title"><strong><#=title#></strong><span></span></div>');
		html_row.push('<ul class="ball-content">');
			$.each([0,1,2,3,4,5,6,7,8,9], function(i){
				html_row.push('<li><a class="ball-number" data-param="action=ball&value='+ this +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
			});
		html_row.push('</ul>');
		html_row.push('<div class="ball-control">');
		html_row.push('<a href="javascript:void(0);" class="circle"></a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=all" class="all">全</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=big" class="big">大</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=small" class="small">小</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=odd" class="odd">奇</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=even" class="even">偶</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=none" class="none">清</a>');
		html_row.push('</div>');
		html_row.push('</li>');
			
	var html_bottom = [];
		html_bottom.push('</ul>');
		html_bottom.push('</div>');
		//拼接所有
	var html_all = [],rowStr = html_row.join('');
		html_all.push(html_head.join(''));
		$.each(['三重号', '单号'], function(i){
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
		});
		html_all.push(html_bottom.join(''));
		
		
	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	SSC.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

