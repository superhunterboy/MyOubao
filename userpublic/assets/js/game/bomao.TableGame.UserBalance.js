//余额对象
(function(host, Event, $, undefined) {
    var defConfig = {

    };

    var pros = {
        init: function(cfg) {
            var me = this;
            me.balance = 0;
        },
        initUserBalance:function(money){
            var me = this;
            me.balance = money;
            me.fireEvent("setUserBalance_after", me.balance);
        },
        setUserBalance: function(money) {
            var me = this;
            me.balance += money;
            me.fireEvent("setUserBalance_after", me.balance);
        },
        getUserBalance: function() {
            return this.balance;
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.UserBalance = Main;

})(bomao, bomao.Event, jQuery);
