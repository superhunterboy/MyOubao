//闲、庄、和 0、1、2
//闲对 5
//庄对 6

//庄闲和：0-闲、1-庄、2-和
//闲对：0-未出现，1-出现了
//庄对：0-未出现，2-出现了


(function(host, Event, $, undefined) {
    var defConfig = {
        mainContainer: '.betTrand',
    };
    var pros = {

        init: function(cfg) {
            var me = this;
            me.paneIndex = 1;
            me.currentRow = 0;
            me.currentColumn = 0;
            me.rows = 6;
            me.columns = 30;
            me.cfg = cfg;
            me.initContainer(me.cfg.mainContainer,me.paneIndex,me.rows,me.columns);
        },

        initTrend:function(items){
            var me = this;
            $.each(items, function() {
                me.addItem(this);
            });
        },

        // 初始化容器
        initContainer: function(mainContainer, paneIndex, rows, columns) {
            var me = this,
                pane = "<div class='pane' index='" + paneIndex + "'>";
            for (var i = 0; i < columns; i++) {
                var column = "<div class='column'>";
                for (var j = 0; j < rows; j++) {
                    column += "<div class='item item-blank'></div>";
                }
                column += "</div>";
                pane += column;
            }
            pane += "</div>"
            $(mainContainer).append(pane);
        },

        addItem: function(item) {
            var me = this,
                columnIndex = me.currentColumn + 1,
                rowIndex = me.currentRow + 1,
                zxh = item.zhuangxianhe,

                xd = item.xiandui,
                
                zd = item.zhuangdui,
                mainContainer = me.cfg.mainContainer,
                CLS = 'item';
            switch (zxh) {
                case 0:
                    CLS += '-xian';
                    break;
                case 1:
                    CLS += '-zhuang';
                    break;
                case 2:
                    CLS += '-he';
                    break;
            };
            zd == 1 ? CLS += '-zhuangdui' : "";
            xd == 1 ? CLS += '-xiandui' : "";

            $(mainContainer).find('.pane').last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").removeClass("item-blank").addClass(CLS);
            if (me.currentRow >= me.rows - 1) {
                me.currentRow = 0;

                if (me.currentColumn >= me.columns - 1) {
                    me.currentColumn = 0;
                    me.paneIndex ++;
                    $(".pane").hide();
                    me.initContainer(".betTrand",me.paneIndex, me.rows, me.columns);
                } else {
                    me.currentColumn++;
                }
            } else {
                me.currentRow++;
            }
        },
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BjlHistory = Main;



})(bomao, bomao.Event, jQuery);
