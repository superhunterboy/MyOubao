(function(host, Event, undefined){
	var defConfig = {
		name:'order',
		UIContainer:'.order-list-content-box',
		container:'',
		bet_value:0,
		win_value:0,
		id:''
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.id=cfg.id;
			me.bet_value=cfg.bet_value;
			me.win_value=cfg.win_value;
			me.UIContainer = $(cfg.UIContainer);
			me.container = $('<ul class="order-list-content" row-list='+me.id+'></ul>').appendTo($(me.UIContainer));
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//更新下注金额 与 可赢金额
			me.container.find('.order-list-bet-value').html(me.bet_value);
			me.container.find('.order-list-win-value').html(me.win_value);
		}
	};

	html_all=[];
	html_all.push('<li class="order-list-head-content">');
		html_all.push('<span>撤单&nbsp;&nbsp;</span>');
		html_all.push('<input class="single-select-box" type="checkbox" name="cancel_order"/>');
	html_all.push('</li>');
	html_all.push('<li class="order-list-head-content"><span class="order-list-bet-value">0.00</span></li>');
	html_all.push('<li class="order-list-head-content"><span class="order-list-win-value">0.00</span></li>');


	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);