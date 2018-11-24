

(function(host, GameMethod, undefined){
	var defConfig = {
		name:'liangmianpan.zhixuan.guanyahezhi'
		
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
						[-1,-1,-1,-1]
						];
		},
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));
		},
		formatViewBalls:function(original){
			var me = this,
				result = [],
				len = original.length,
				i = 0,
				tempArr = [],
				names = ['大', '小', '单', '双'];
			for (; i < len; i++) {
				tempArr = [];
				$.each(original[i], function(j){
					tempArr[j] = names[Number(original[i][j] )];
				});
				result = result.concat(tempArr.join(''));
			}
			return result.join('|');
		},
		//data 该玩法的单注信息
		editSubmitData:function(data){
			var ball_num = {'0':'1','1':'0','2':'3','3':'2'},
				numArr = data['ball'].split(''),
				result = [];
			$.each(numArr, function(){
				ball_num['' + this] ? result.push(ball_num['' + this]) : result.push(this);
			});
			data['ball'] = result.join('|');
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
		html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div><div class="function-select-title"></div></div>');
		html_head.push('<div class="number-select-content">');
		html_head.push('<ul class="ball-section ball-section-pk10-liangmianpan-guanyahezhi">');
		//每行
	var html_row = [];
		html_row.push('<li style="background-image:none;">');
		html_row.push('<div class="ball-title"><strong><#=title#></strong><span></span></div>');
		html_row.push('<ul class="ball-content">');
	var signindex = [1, 0, 3, 2];
			$.each(['大','小','单','双'], function(i){
				html_row.push('<li><a data-myindex="'+ signindex[i] +'" class="ball-number" data-param="action=ball&value='+ i +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
			});
		html_row.push('</ul>');
		html_row.push('</li>');
			
	var html_bottom = [];
		html_bottom.push('</ul>');
		html_bottom.push('</div>');
		//拼接所有
	var html_all = [],rowStr = html_row.join('');
		html_all.push(html_head.join(''));
		$.each(['冠亚和'], function(i){
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
		});
		html_all.push(html_bottom.join(''));
		
		
	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	gameCase.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

