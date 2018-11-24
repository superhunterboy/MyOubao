

(function(host, name, message, undefined){
	var defConfig = {
		
	},
	gameCaseName = 'L115',
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[gameCaseName][name] = Main;
	
})(bomao, "Message", bomao.GameMessage);










