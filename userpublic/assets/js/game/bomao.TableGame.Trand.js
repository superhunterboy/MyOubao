// 走势类,基类

(function(host, Event, $, undefined) {
    var defConfig = {

    };

    pros = {
        init: function(cfg) {
            var me = this;
            me.columns = cfg.columns;
            me.rows = cfg.rows;

            // 最后一个被插入的数据所处的位置，[-1,-1]表示还没有数据。
            // me.currentPosition = [-1, -1];
            me.panels = [];
        },

        // 生成Panel：内存中的panel和页面上的dom
        createPanel: function() {
            var me = this,
                panel = [],
                rows = me.rows,
                columns = me.columns;
                
            me.currentPosition = [-1,-1];

            for (var i = 0; i < rows; i++) {
                var row = [];
                for (var j = 0; j < columns; j++) {
                    row.push(0)
                }
                panel.push(row);
            }
            me.panels.push(panel)

            // 通知前台生成对应的dom

            me.fireEvent('createPanel_after', me.panels);
        },

        // 修改元素
        addRecord: function(record) {
            var me = this,
                curPosition = [];
            // 根据元素的值，以及当前位置的元素值
            curPosition = me.getCurrentPosition();


            if (curPosition[0] == (me.columns - 1) && curPosition[1] == (me.rows - 1)) {
                // 通知大Tab里的所有Trands换panel
                me.fireEvent("addPanel_before",record);

            } else {
                var column = curPosition[0];
                var row = curPosition[1];

                if (column == -1 && row == -1) {
                    column = 0;
                    row = 0;
                } else {
                    if (row == me.rows - 1) {
                        column++;
                        row = 0;
                    } else if (row < me.rows - 1) {
                        row++;
                    }

                }
                me.panels[me.panels.length-1][row][column] = record;
                me.setCurrentPosition(column, row);
                me.fireEvent("addRecord_after");
            }
        },

        // 获得当前位置
        getCurrentPosition: function() {
            var me = this;
            return me.currentPosition;
        },

        setCurrentPosition:function(x,y){
            var me = this;
            me.currentPosition=[x,y];
        },

        // 根据位置获得该处的值
        getValue: function(position) {
            var me = this;
            if (me.panels[me.panels.length-1][position[0]] != 'undefined' && me.panels[me.panels.length-1][position[0]][position[1]] != 'undefined') {
                return me.panels[me.panels.length-1][position[0]][position[1]];
            } else {
                return -1;
            }
        },

        getRecordFlag:function(record){

        }



    };

    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Trand = Main;
})(bomao, bomao.Event, jQuery)
