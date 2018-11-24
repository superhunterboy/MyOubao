//筹码对象
(function(host, Event, $, undefined) {
    var defConfig = {

        },
        autoId = 1;

    var pros = {
        init: function(cfg) {
            var me = this;
            me.id = autoId++;
            me.money = cfg.money;
            me.marginTop = cfg.marginTop;
            me.isAvaliable = true;
        },
        getId: function() {
            return this.id;
        },
        getMoney: function() {
            return this.money;
        },
        getMarginTop:function(){
            return this.marginTop;
        },
        setStatus: function(isAvaliable) {
            var me = this;
            me.isAvaliable = isAvaliable;
            me.fireEvent("setStatus_after", isAvaliable);
        },
        getStatus: function() {
            return this.isAvaliable;
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Chip = Main;

})(bomao, bomao.Event, jQuery);
