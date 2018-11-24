//records
(function(host, name, Event, undefined) {
	var defConfig = {
			dom:'',
			iframe:'',
			url:''
		},
		instance,
		Games = host.Games;

	var pros = {
		//初始化
		init: function(cfg) {
			var me = this,
				cfg = me.defConfig;
			me.dom = $(cfg.dom);
			me.iframe = $(cfg.iframe);
			me.url = cfg.url;
		},
		show:function(){
			var me = this,mask;
			me.refresh();
			me.showMask();
			me.dom.show();
		},
		hide:function(){
			var me = this;
			me.dom.hide();
			me.hideMask();
		},
		showMask:function(){
			var me = this,mask = host.Mask ? host.Mask.getInstance() : null;
			if(mask){
				me.mask = mask;
				mask.show();
			}
		},
		hideMask:function(){
			var me = this;
			if(me.mask){
				me.mask.hide();
			}
			me.dom.hide();
		},
		refresh:function(){
			var me = this;
			me.iframe.attr('src', me.url + '?_=' + Math.random());
		}
	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	Main.getInstance = function(cfg) {
		return instance || (instance = new Main(cfg));
	};
	host[name] = Main;

})(bomao, "GameRecords", bomao.Event);