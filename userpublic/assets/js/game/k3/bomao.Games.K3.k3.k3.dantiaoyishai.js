

(function(host, GameMethod, undefined){
	var defConfig = {
		name:'k3.k3.dantiaoyishai',
		//玩法提示
		tips:'',
		//选号实例
		exampleTip: ''
	},
	Games = host.Games,
	K3 = Games.K3.getInstance();
	
	
	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;
			
			//默认加载执行30期遗漏号码
			//me.getHotCold(me.getGameMethodName(), 'currentFre', 'lost');
			//初始化冷热号事件
			//me.initHotColdEvent();
		},
		//时时彩复式结构为5行10列
		//复位选球数据
		rebuildData:function(){
			var me = this;
			me.balls = [
					[-1, -1, -1, -1, -1, -1]
				];
		},
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));
			me.container.find('.dice').each(function(){
				var el = $(this),par = el.parent();
				$(this).attr('data-param', par.attr('data-param'));
			});
		},
		checkBallIsComplete: function(){
			var me = this,
				ball = me.getBallData()[0],
				i = 0,
				len = ball.length,
				num = 0;
			for(;i < len;i++){
				if(ball[i] > 0){
					num++;
				}
			}
			//console.log(num);
			if(num >= 1){
				return me.isBallsComplete = true;
			}
			return me.isBallsComplete = false;
		},
		makePostParameter: function(original){
			return this.formatViewBalls(original);
		},
		formatViewBalls:function(original){
			var me = this,
				result = [],
				numberHash = [
					['6', '5', '4', '3', '2', '1']
				],
				len = original.length,
				i = 0,
				len2;
				j = 0;
			for (i = 0; i < len; i++) {
				len2 = original[i].length;
				for(j = 0; j < len2; j++){
					result.push(numberHash[i][original[i][j]]);
				}
			}
			return result.join('|');
		},
		//获取总注数/获取组合结果
		//isGetNum=true 只获取数量，返回为数字
		//isGetNum=false 获取组合结果，返回结果为单注数组
		getLottery:function(isGetNum){
			var me = this,data = me.getBallData(),
				i = 0,len = data.length,row,
				_tempRow = [],
				j = 0,len2 = 0,
				result = [],
				result2 = [],
				//总注数
				total = 1,
				rowNum = 0;
			
			//检测球是否完整
			for(;i < len;i++){
				result[i] = [];
				row = data[i];
				len2 = row.length;
				isEmptySelect = true;
				rowNum = 0;
				for(j = 0;j < len2;j++){
					if(row[j] > 0){
						me.isBallsComplete = true;
						//需要计算组合则推入结果
						if(!isGetNum){
							result[i].push(j);
						}
						rowNum++;
					}
				}
				//计算注数
				total *= rowNum;
			}
			
			//返回注数
			if(isGetNum){
				return total;
			}
			
			if(me.isBallsComplete){
				//组合结果
				for(i = 0,len = result.length;i < len;i++){
					for(j = 0,len2 = result[i].length;j < len2;j++){
						result2.push([result[i][j]]);
					}
				}
				return result2;
			}else{
				return [];
			}	
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
				'type': name_en,
				'original':[current],
				'lotterys': lotterys,
				'moneyUnit': Games.getCurrentGameStatistics().getMoneyUnit(),
				'multiple': Games.getCurrentGameStatistics().getMultip(),
				'onePrice': Games.getCurrentGame().getGameConfig().getInstance().getOnePrice(name_en),
				'num': lotterys.length
			};
			order['amountText'] = Games.getCurrentGameStatistics().formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
			return order;
		},



		miniTrend_createRowHtml:function(){
			var me = this,
				data = me.miniTrend_getBallsData(),
				dataLen = data.length,
				trcls = '',
				currCls = 'curr',
				item,
				html = [],
				xtText = '';

			$.each(data, function(i){
				item = this;
				trcls = '';
				trcls = i == 0 ? 'first' : trcls;
				trcls = i == dataLen - 1 ? 'last' : trcls;
				html.push('<tr class="'+ trcls +'">');
					html.push('<td><span class="number">'+ item['number'].substr(2) +' 期</span></td>');
					html.push('<td><span class="balls">');
					$.each(item['balls'], function(j){
						if(j > 2){
							currCls = 'curr';
						}else{
							currCls = '';
						}
						html.push('<i class="dice dice-' + this + ' ' + currCls +'">' + this + '</i>');
					});
					html.push('</span></td>');
					//xtText = item['balls'][3] + item['balls'][4];
					xtText = '-';
					if(item['balls'][0] != item['balls'][1] && item['balls'][0] != item['balls'][2] && item['balls'][1] != item['balls'][2]){
						xtText = '<span class="color-hl">三不同</span>';
					}
					//html.push('<td>'+ xtText +'</td>');
				html.push('</tr>');
			});
			return html.join('');
		}


		
		
	};
	



	//html模板
	var html_head = [];
		//头部
		html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="" class="pick-rule">选号规则</a><a href="" class="win-info">中奖说明</a></div><div class="function-select-title"></div></div>');
		html_head.push('<div class="number-select-content">');
		html_head.push('<ul class="ball-section ball-section-k3-dantiaoyishai">');
	var numberHash = {
		'0-0':'6', '0-1':'5',  '0-2':'4',  '0-3':'3', '0-4':'2', '0-5':'1'
	};
		//每行
	var html_row = [];
		html_row.push('<li>');
		html_row.push('<ul class="ball-content">');
			$.each(['<#=row#>-0','<#=row#>-1','<#=row#>-2','<#=row#>-3','<#=row#>-4','<#=row#>-5'], function(i){
				html_row.push('<li><a href="javascript:void(0);" data-param="action=ball&value='+ i +'&row=<#=row#>" class="ball-number">'+this+'</a></li>');
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

		//替换数字内容
		$.each(numberHash, function(k, v){
			var vs = v.split(''),vsHtml = [];
			$.each(vs, function(){
				vsHtml.push('<span class="dice dice-'+ this +'">'+ this +'</span>');
			});
			vsHtml = vsHtml.join('');
			$.each(html_all, function(i){
				html_all[i] = html_all[i].replace(k, vsHtml);
			});
		});

	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	K3.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

