// 走势Tab大

(function(host, Event, $, undefined) {
    var defConfig = {

    };

    pros = {
        init: function(cfg) {
            var me = this;
            me.smallTabs = [];
        },

        addBigTab:function(){

        }

    };

    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BigTrandTab = Main;

})(bomao, bomao.Event, jQuery);
