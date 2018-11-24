// 走势Tab小

(function(host, Event, $, undefined) {
    var defConfig = {

    };

    pros = {
        init: function(cfg) {
        	var me = this;
            me.name_en="";
            me.trands = [];

        },

        addSmallTab:function(){

        }

    };

    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.SmallTrandTab = Main;

})(bomao, bomao.Event, jQuery)
