

(function(host, GameMethod, undefined){
	var defConfig = {
		name:'liangmianpan.zhixuan.dragonwithtiger'
		
	},
	Games = host.Games,
	gameCase = Games.PK10.getInstance();
	
	
	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;
			
		},
		//时时彩复式结构为5行10列
		//复位选球数据
		rebuildData:function(){
			var me = this;
			me.balls = [
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
							[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
		},
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));
		},
		setBallData: function(x, y, value) {
			var me = this,
				data = me.getBallData();
			me.fireEvent('beforeSetBallData', x, y, value);
			if (x != y) {
				data[x][y] = value;
			}
			me.fireEvent('afterSetBallData', x, y, value);
		},
		formatViewBalls:function(original){
			var me = this,
				result = [],
				len = original.length,
				i = 0,
				tempArr = [],
				flags = [];
			for (; i < len; i++) {
				tempArr = [];
				$.each(original[i], function(j){
					tempArr[j] = (i + 1) + '-' + (original[i][j] + 1);
				});
				result = result.concat(tempArr.join(','));
			}
			return result.join('|');
		},
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
	

	var ballsHTMLData = [];
	(function(){
		var i = 0,
			len = 10,
			j = 0,
			len2 = 10,
			list = [],
			rownames = ['冠军', '亚军', '第三名', '第四名', '第五名', '第六名', '第七名', '第八名', '第九名', '第十名'];
		for(i = 0; i < len; i++){
			list = [];
			for(j = 0; j < len2; j++){
				list.push({x:i+1, y:j+1});
			}
			ballsHTMLData.push({name:rownames[i], list:list});
		}
	})();

	//html模板
	var html_head = [];
		//头部
		html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div><div class="function-select-title"></div></div>');
		html_head.push('<div class="number-select-content">');
		html_head.push('<ul class="ball-section ball-section-pk10-liangmianpan-dragonwithtiger">');
		//每行
	var html_row = [];
		$.each(ballsHTMLData, function(i){
			html_row.push('<li style="background-image:none;">');
			html_row.push('<div class="ball-title"><strong>'+ ballsHTMLData[i]['name'] +'</strong><span></span></div>');
			html_row.push('<ul class="ball-content">');
			$.each(ballsHTMLData[i]['list'], function(j){
				if(i != j){
					html_row.push('<li><a class="ball-number" data-param="action=ball&value='+ j +'&row='+ i +'" href="javascript:void(0);">'+ ((i+1)+'龙'+(j+1)) +'虎</a></li>');
				}else{
					html_row.push('<li><a class="ball-number ball-number-space"></a></li>');
				}
			});
			html_row.push('</ul>');
			html_row.push('<div class="ball-control">');
			html_row.push('<a href="javascript:void(0);" class="circle"></a>');
			html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row='+ i +'&amp;bound=all" class="all">全</a>');
			html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row='+ i +'&amp;bound=none" class="none">清</a>');
			html_row.push('</div>');
			html_row.push('</li>');
		});

	var html_bottom = [];
		html_bottom.push('</ul>');
		html_bottom.push('</div>');
		//拼接所有
	var html_all = [],rowStr = html_row.join('');
		html_all.push(html_head.join(''));
		html_all.push(html_row.join(''));
		html_all.push(html_bottom.join(''));
		
		
	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	gameCase.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

