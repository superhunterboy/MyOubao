
//右键菜单类
(function(host, Event, $, undefined) {


    var defConfig = {
        //外容器
        warpHtml:'<div class="game-contextmenu"></div>',
        //菜单行选择符
        rowSelection:'.item',
        //文本类型
        tpl_text:'<div class="item txt-item" data-action="<#=action#>"><#=title#></div>',
        //图标类型
        tpl_icon:'<div class="item icon-text-item"  data-action="<#=action#>"><img src="<#=src#>"><#=name#></div>'
    }


    var pros = {
        init:function(cfg) {
            var me = this;
            me.data = null;
            me.dom = $(me.defConfig.warpHtml);
            me.dom.appendTo(document.body);

            me.initEvent();
        },
        initEvent:function(){
            var me = this;
            me.dom.on('click', me.defConfig.rowSelection, function(e){
                var el = $(this),
                    action = el.attr('data-action');
                me.fireEvent('click', action, el);
            });
            $(document).on('mousedown', function(e){
                if(!$.contains(me.dom.get(0), e.target)){
                    me.hide();
                }
            });
        },
        setData:function(data){
            this.data = data;
        },
        getData:function(){
            return this.data;
        },
        //{type:菜单类型, action:菜单命令, tpl:自定义模板}
        addItem:function(opt) {
            var me = this,
                cfg = me.defConfig,
                tpl = !!opt.tpl ? opt.tpl : cfg['tpl_' + (opt.type ? opt.type : 'text')];
            tpl = host.util.template(tpl, opt);
            $(tpl).appendTo(me.dom);
        },
        show:function(x, y, zIndex) {
            var me = this,
                win = $(window),
                x = x + win.scrollLeft(),
                y = y + win.scrollTop();
            me.fireEvent('show_before');
            if(typeof zIndex == 'undefined'){
                me.dom.css({
                    left:x,
                    top:y
                });
            }else{
                me.dom.css({
                    left:x,
                    top:y,
                    zIndex:zIndex
                });
            }
            me.effectShow(function(){
                me.fireEvent('show_after')
            });
        },
        hide:function() {
            var me = this;
            me.fireEvent('hide_before');
            me.effectHide(function(){
                me.fireEvent('hide_after');
            });
        },
        effectShow:function(callback){
            var me = this;
            me.dom.show();
            if(callback){
                callback.call(me);
            }
        },
        effectHide:function(callback){
            var me = this;
            me.dom.hide();
            if(callback){
                callback.call(me);
            }
        }
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.ContextMenu = Main;


})(bomao, bomao.Event, jQuery);
