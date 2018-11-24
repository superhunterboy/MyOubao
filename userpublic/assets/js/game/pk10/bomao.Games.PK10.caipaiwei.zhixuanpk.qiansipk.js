

//前二直选复式玩法实现类
(function(host, GameMethod, undefined){
	var defConfig = {
		name:'caipaiwei.zhixuanpk.qiansipk',
		//玩法提示
		tips:'',
		//选号实例
		exampleTip: ''
	},
	Games = host.Games,
	gameCase = Games.PK10.getInstance();
	
	
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
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
		},
		makePostParameter:function(data, order){
			var me = this,
				result = [],
				data = order['original'],
				i = 0;
			for (; i < data.length; i++) {
				result = result.concat(data[i].join('-'));
			}
			return result.join('|');
		},
		editSubmitData:function(data){
			var rows = data['ball'].split('|'),
				i = 0,
				len = rows.length,
				j = 0,
				len2,
				cells;
			for(i = 0; i < len; i++){
				cells = rows[i].split('-');
				len2 = cells.length;
				for(j = 0; j < len2; j++){
					if(cells[j] != ''){
						cells[j] = Number(cells[j]) - 1;
					}
				}
				rows[i] = cells.join('');
			}
			data['ball'] = rows.join('|');
		},
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));
		},



		setBallData: function(x, y, value) {
			var me = this,
				data = me.getBallData(),
				i = 0,
				len = data.length;
			//console.log(x, y, value);
			if(y == 0){
				return;
			}
			me.fireEvent('beforeSetBallData', x, y, value);
			if (x >= 0 && x < data.length && y >= 0) {
				data[x][y] = value;
			}
			me.fireEvent('afterSetBallData', x, y, value);
		},
		//过滤结果集合中的双重号和豹子号
		filterErrorData: function(arr) {
			var me = this,
				saveArray = [],
				i = 0,
				len = arr.length,
				j = 0,
				len2,
				has = {},
				isrepeat = false;

			for(i = 0; i < len; i++){
				has = {};
				isrepeat = false;
				for(j = 0, len2 = arr[i].length; j < len2; j++){
					if(has['' + arr[i][j]]){
						isrepeat = true;
					}
					has['' + arr[i][j]] = true;
				}
				if(!isrepeat){
					saveArray.push(arr[i]);
				}
			}
			return saveArray;
		},
		//获取总注数/获取组合结果
		//isGetNum=true 只获取数量，返回为数字
		//isGetNum=false 获取组合结果，返回结果为单注数组
		getLottery: function(isGetNum) {
			var me = this,
				data = me.getBallData(),
				i = 0,
				len = data.length,
				row, isEmptySelect = true,
				_tempRow = [],
				j = 0,
				len2 = 0,
				result = [],
				//总注数
				total = 1,
				rowNum = 0;

			//检测球是否完整
			for (; i < len; i++) {
				result[i] = [];
				row = data[i];
				len2 = row.length;
				isEmptySelect = true;
				rowNum = 0;
				for (j = 0; j < len2; j++) {
					if (row[j] > 0) {
						isEmptySelect = false;
						//需要计算组合则推入结果
						if (!isGetNum) {
							result[i].push(j);
						}
						rowNum++;
					}
				}
				if (isEmptySelect) {
					//alert('第' + i + '行选球不完整');
					me.isBallsComplete = false;
					return [];
				}
				//计算注数
				total *= rowNum;
			}
			me.isBallsComplete = true;
			//返回注数
			if (isGetNum) {
				return total;
			}

			if (me.isBallsComplete) {
				result = me.filterErrorData(me.combination(result));
				//组合结果
				return result;
			} else {
				return [];
			}
		},



		miniTrend_createHeadHtml:function(){
			var me = this,
				html = [];
			html.push('<table width="100%" class="bet-table-trend" id="J-minitrend-trendtable-'+ me.getId() +'">');
				html.push('<thead><tr>');
				html.push('<th><span class="number">奖期</span></th>');
				html.push('<th><span class="balls">开奖</th>');
				html.push('</tr></thead>');
				html.push('<tbody>');
			return html.join('');
		},
		miniTrend_createRowHtml:function(){
			var me = this,
				data = me.miniTrend_getBallsData(),
				dataLen = data.length,
				trcls = '',
				currCls = 'curr',
				item,
				html = [],
				xtText = [],
				xtText2 = [];

			$.each(data, function(i){
				item = this;
				xtText = [];
				xtText2 = [];
				trcls = '';
				html.push('<tr class="first">');
					html.push('<td><span class="number">'+ item['number'].substr(2) +' 期</span></td>');
					html.push('<td class="bg"><span class="balls">');
					html.push('<p>');
					$.each(item['balls'], function(j){
						currCls = 'curr';
						html.push('<i><b class="nums-s-'+this+'">' + this + '</b></i>');
						if(j == 4){
							html.push('</p>');
							html.push('<p>');
						}
					});
					html.push('</p>');
					html.push('</span></td>');
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
		html_head.push('<ul class="ball-section ball-section-pk10-caipaiwei-zhixuanpk-qiansipk">');
		//每行
	var html_row = [];
		html_row.push('<li>');
		html_row.push('<div class="ball-title"><strong><#=title#></strong><span></span></div>');
		html_row.push('<ul class="ball-content">');
			$.each([0,1,2,3,4,5,6,7,8,9,10], function(i){
				if(i == 0){
					html_row.push('<li style="display:none;"><a class="ball-number ball-bgnum-'+this+'" data-param="action=ball&value='+ this +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
				}else{
					html_row.push('<li><a class="ball-number ball-bgnum-'+this+'" data-param="action=ball&value='+ this +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
				}
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
		$.each(['冠军','亚军','第三名','第四名'], function(i){
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
		});
		html_all.push(html_bottom.join(''));
		
	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	gameCase.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

