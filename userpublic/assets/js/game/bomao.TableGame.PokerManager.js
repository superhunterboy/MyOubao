//扑克牌管理器
(function(host, Event, $, undefined) {
    var defConfig = {
            pkcls:'poker'
        },
        Poker = host.TableGame.Poker;

    var pros = {
        init: function(cfg) {
            var me = this;
            me._attrCfg = {};
            me._container = cfg.container ? $(cfg.container) : $(document.body);
        },
        getContainer:function(){
            return this._container;
        },
        getPoker:function(value, cls){
            var me = this,
                value = Number(value);
            return new Poker({
                cls:me.defConfig.pkcls + ' poker-' + value + (cls || ' poker-red'),
                value:value,
                par:me.getContainer(),
                attrs:me.getPokerAttr(value)
            });
        },
        //设置扑克牌属性配置
        setAttrConfig:function(cfg){
            var me = this;
            me._attrCfg = cfg;
        },
        getAttrConfig:function(){
            return this._attrCfg;
        },
        getPokerAttr:function(value){
            var me = this,
                cfg = me.getAttrConfig();
            value = Number(value) < 10 ? '0'+value : '' + Number(value);
            return cfg[value];
         }

    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.PokerManager = Main;

})(bomao, bomao.Event, jQuery);
