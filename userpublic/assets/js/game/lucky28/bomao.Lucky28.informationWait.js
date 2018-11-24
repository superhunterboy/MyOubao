(function(host, Event, undefined){
	var defConfig = {
		name:'informationwait',
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
	};

	var html_all = [];
	html_all.push('<div class="waiting_box">');
		html_all.push('<span class="wait_icon"></span>');
		html_all.push('<span class="wait_label">等待开奖中...</span>');
	html_all.push('</div>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);