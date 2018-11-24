// 顺序走势类

(function(host, Trand, $, undefined) {
    var defConfig = {

    };

    pros = {
        init: function(cfg) {

        },

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
        }
    };

    Main = host.Class(pros, Trand);
    Main.defConfig = defConfig;
    host.TableGame.SequentialTrand = Main;

})(bomao, bomao.TableGame.Trand, jQuery)
