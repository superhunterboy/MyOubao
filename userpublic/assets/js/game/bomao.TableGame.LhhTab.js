// 龙虎斗历史记录Tab类，承载Panel

(function(host, Event, $, undefined) {
    var defConfig = {

    };

    pros = {
        init: function() {
        	var me = this;
        	me.panels = [""];

        },
        addPanel:function(){

        },
        switchPanel:function(){

        },
        addRecord:function(record){
        	// 处理几种panel的

        	$.each(me.panel,function(){
        		panel = this;
        		if(panel.addRecord(record)){

        		}else{
        			me.addPanel();

        		}
        	})
        }
        
    };

    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.LhhPanel = Main;
})(bomao, bomao.Event, jQuery)