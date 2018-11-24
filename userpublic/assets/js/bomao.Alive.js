
//消息通知
;(function(host, name, Event, undefined){
	var defConfig = {
		url:'/message.php',
		dataType:'json',
		method:'post',
		//间隔时间
		looptime:5 * 1000
	};
	Games = host.Games;

	var pros = {
		//初始化
		init: function(cfg){
			var me = this;
			me.timer = null;
		},
		start:function(){
			var me = this,cfg = me.defConfig,ajaxCfg = {};
			ajaxCfg = $.extend({}, cfg);
			ajaxCfg['success'] = function(data){
				me.fireEvent('beforeSuccess', data);
				me.success(data);
				me.fireEvent('afterSuccess', data);
			};
			ajaxCfg['error'] = function(xhr, type){
				me.fireEvent('beforeError', xhr ,type);
				me.error(xhr, type);
				me.fireEvent('afterError', xhr ,type);
			};
			ajaxCfg['complete'] = function(){
				me.fireEvent('beforeComplete');
				me.complete();
				me.fireEvent('afterComplete');
			};
			ajaxCfg['data'] = me.getParams();
			//console.log(me.getParams());
			$.ajax(ajaxCfg);
		},
		pause:function(){
			clearTimeout(this.timer);
		},
		loop:function(){
			var me = this,cfg = me.defConfig;
			if(cfg.looptime > 0){
				clearTimeout(me.timer);
				me.timer = setTimeout(function(){
					me.start();
				}, cfg.looptime);
			}
		},
		getParams:function(){
			var params = [
					{
						'type':'account'
					},
					{
						'type':'bets'
					},
					{
						'type':'traces'
					}
			];
			return {'params':params};
		},
		success:function(data){
			this.loop();
		},
		error:function(xhr, type){
			this.loop();
		},
		complete:function(){

		}
	}

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
	host[name] = Main;

})(bomao, "Alive",  bomao.Event);










