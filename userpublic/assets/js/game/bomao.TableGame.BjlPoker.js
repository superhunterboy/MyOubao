(function(host, Event, $, undefined) {
    var defConfig = {

    };
    var pros = {
        init: function(cfg) {
            var me = this,
                containerName = cfg.containerName,
                value = parseInt(cfg.value);
            me.dom = '<div class="poker-container" name="' + containerName + '"><div class="card"><div class="poker poker-' + value + '"></div><div class="poker back"></div></div></div>';
            $(me.dom).insertBefore($(cfg.sibling));
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BjlPoker = Main;
})(bomao, bomao.Event, jQuery);
