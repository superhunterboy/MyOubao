(function(host, Event, undefined){
	var defConfig = {
		name:'informationSuspension',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = null;
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Slovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};
		},
		//更新开盘时间
		updateOpenTime:function(month,day,hour,minute,second){
			var me = this;
			var date = month+'月'+day+'日 '+(hour>=10?hour:'0'+hour)+':'+(minute>=10?minute:'0'+minute)+':'+(second>=10?second:'0'+second);
			me.container.find('.suspension-lab-2').html(date);
		}
	};

	var html_all = [];
	html_all.push('<span class="suspension-lab suspension-lab-1">该彩种官网目前已停盘，预计开奖时间:</span>');
	html_all.push('<span class="suspension-lab suspension-lab-2"></span>');
	html_all.push('<span class="suspension-lab suspension-lab-3">给您造成的不便敬请见谅!</span>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);