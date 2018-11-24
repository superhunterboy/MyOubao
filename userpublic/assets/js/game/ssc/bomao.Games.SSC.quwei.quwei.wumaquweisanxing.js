

(function(host, GameMethod, undefined){
	var defConfig = {
		name:'quwei.quwei.wumaquweisanxing'
		
	},
	Games = host.Games,
	SSC = Games.SSC.getInstance();
	
	
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
						[-1,-1],
						[-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
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
				names = ['小','大'];
			for (; i < len; i++) {
				if(i < 2){
					tempArr = [];
					$.each(original[i], function(j){
						tempArr[j] = names[Number(original[i][j] )];
					});
					result = result.concat(tempArr.join(''));
				}else{
					result = result.concat(original[i].join(''));
				}
				
			}
			return result.join('|');
		},
		//取消选球状态
		//参数：x为纵坐标 y为横坐标 v为修改值
		exeEvent_cancelCurrentButton: function(x, y, v) {
			var me = this,
				container = me.container;
			container.find('.ball-control').each( function(){
				if($(this).attr('row') == x){
			 	$(this).find('a').removeClass('current');
			 }
			})
		},
		
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
		html_row.push('<div class="ball-title"><strong><#=title#>位</strong><span></span></div>');
		html_row.push('<ul class="ball-content">');
			$.each([0,1,2,3,4,5,6,7,8,9], function(i){
				html_row.push('<li><a class="ball-number" data-param="action=ball&value='+ i +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
			});
		html_row.push('</ul>');
		html_row.push('<div class="ball-control" row="<#=row#>">');
		html_row.push('<a href="javascript:void(0);" class="circle"></a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=all"  class="all">全</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=big" class="big">大</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=small" class="small">小</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=odd" class="odd">奇</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=even" class="even">偶</a>');
		html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=none" class="none">清</a>');
		html_row.push('</div>');
		html_row.push('</li>');
		
	var html_row1 = [];
		html_row1.push('<li style="background-image:none;">');
		html_row1.push('<div class="ball-title"><strong><#=title#>位</strong><span></span></div>');
		html_row1.push('<ul class="ball-content ball-content-long">');
			$.each(['小(0-4)', '大(5-9)'], function(i){
				html_row1.push('<li><a class="ball-number ball-number-long" data-param="action=ball&value='+ i +'&row=<#=row#>" href="javascript:void(0);">'+ this +'</a></li>');
			});
		html_row1.push('</ul>');
		html_row1.push('</li>');
		
			
	var html_bottom = [];
		html_bottom.push('</ul>');
		html_bottom.push('</div>');
		//拼接所有
	var html_all = [],
		rowStr = html_row.join(''),
		rowStr1 = html_row1.join('');
		html_all.push(html_head.join(''));

		$.each(['万','千'], function(i){
			html_all.push(rowStr1.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
		});
		$.each(['百','十','个'], function(i){
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i + 2));
		});
		
		html_all.push(html_bottom.join(''));
		
		
	
	
	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
		Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	SSC.setLoadedHas(defConfig.name, new Main());
	
})(bomao, bomao.GameMethod);

