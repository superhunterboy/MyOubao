
(function(host, Danshi, undefined){
	var defConfig = {
			name:'caipaiwei.zhixuanpk.pk10qiansidanshi',
			//玩法提示
			tips: '前三直选单式玩法提示',
			//选号实例
			exampleTip: '前三直选单式弹出层22提示'
		},
		Games = host.Games,
		PK10 = Games.PK10.getInstance();


	//定义方法
	var pros = {
		init: function(cfg) {
			var me = this;
			//建立编辑器DOM
			//防止绑定事件失败加入定时器
			setTimeout(function() {
				me.initFrame();
			}, 25);
		},
		rebuildData: function() {
			var me = this;
			me.balls = [
				[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
				[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
				[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
			];
		},
		//按钮处理投注号码
		editSubmitData:function(data){
			var me=this,
				b=data['ball'].split('|'),
				c='';
			for(var j=0;j<b.length;j++){
				if(j===b.length-1) {
					c += me.minus1(b[j]);
				}else{
					c += me.minus1(b[j])+'|';
				}
			}
			data['ball'] = c;
		},
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum) {
			var me = this,
				isPass = true;
			if(lotteryNum.length != 4){
				return false;
			}
			if(me.checkRepeat(lotteryNum)){
				return false;
			}
			$.each(lotteryNum, function() {
				if (!me.defConfig.checkNum.test(this)  || Number(this) < 1 || Number(this) > 10) {
					isPass = false;
					return false;
				}
			});
			return isPass;
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


	//继承Danshi
	var Main = host.Class(pros, Danshi);
	Main.defConfig = defConfig;
	//将实例挂在游戏管理器上
	PK10.setLoadedHas(defConfig.name, new Main());



})(bomao, bomao.Games.PK10.Danshi);

