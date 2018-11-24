//扑克牌对象
(function(host, Event, $, undefined) {
    var defConfig = {
            
        };

    var pros = {
        init: function(cfg) {
            var me = this;
            me._value = cfg.value;
            me._attrs = cfg.attrs;
            me._cls = cfg.cls;
            me._par = $(typeof cfg['par'] != 'undefined' ? cfg['par'] : document.body);

            me._dom = $('<div class="'+ me.getCls() +'"></div>');
            me._dom.appendTo(me.getPar());
        },
        getPar:function(){
            return this._par;
        },
        getDom:function(){
            return this._dom;
        },
        getValue:function(){
            return this._value;
        },
        getCls:function(){
            return this._cls;
        },
        getAttr:function(key){
            var me = this;
            return me._attrs[key];
        },
        setPos:function(x, y){
            var me = this,
                dom = me.getDom();
            dom.css({
                left:x,
                top:y
            });
        },
        addClass:function(cls){
            var me = this,
                dom = me.getDom();
            dom.addClass(cls);
        },
        removeClass:function(){
            var me = this,
                dom = me.getDom();
            dom.removeClass(cls);
        },
        moveTo:function(x, y){
            var me = this;

        },
        showCard:function(){
            var me = this;
            me.getDom().removeClass('poker-red poker-blue');
        },
        coverCard:function(cls){
            var me = this;
            me.getDom().addClass(cls);
        },
        destroy:function(){
            var me = this;
            me.dom.remove();
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Poker = Main;

})(bomao, bomao.Event, jQuery);
