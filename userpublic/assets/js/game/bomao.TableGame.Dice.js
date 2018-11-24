
//桌面游戏基类
(function(host, TableGame, undefined){
	var defConfig = {

	};



	var pros = {
		init:function(cfg){

		}
	};
	
		
	

	var Main = host.Class(pros, TableGame);
		Main.defConfig = defConfig;
	TableGame.Dice = Main;
	
})(bomao, bomao.TableGame);






