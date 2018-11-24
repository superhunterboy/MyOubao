
(function(host, name, parClass, undefined){
	var defConfig = {

	};


	var pros = {
		init:function(cfg){

		}
	};


	var Main = host.Class(pros, parClass);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao, "Game", bomao.Lucky28.GameBase);







