

(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:name,
		jsNameSpace:'bomao.Games.L115.' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games[name].Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "L115", bomao.Game);










