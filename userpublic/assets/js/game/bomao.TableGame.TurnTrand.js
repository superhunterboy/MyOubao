// 转弯走势类



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

            if(curPosition[0]==-1){
                curPosition=[0,0];
            }

            // 获取已插入的最后一个值
            currentValue = me.getValue(curPosition);
            
            if (typeof record == 'string') {
                // 已插入的最后一个值不是和，是龙或虎

                if (record == currentValue) {
                    // 相同:继续

                    if (curPosition[0] == (me.columns - 1) && curPosition[1] == (me.rows - 1)) {
                        // 换面板
                        me.fireEvent("addPanel_before");
                    } else if (curPosition[1] < (me.rows - 1) && me.getValue([curPosition[0], curPosition[1] + 1]) == 0) {
                        // 向下继续
                        panels[me.panels.length-1][curPosition[0]][curPosition[1] + 1] = record;
                        // 设置当前位置
                        me.setCurrentPosition(curPosition[0], curPosition[1] + 1);
                        me.fireEvent("addRecord_after",record)
                    } else if (curPosition[0] < (me.columns - 1) && curPosition[1] == (me.rows - 1)) {
                        // 向右继续
                        panels[me.panels.length-1][curPosition[0] + 1][curPosition[1]] = record;
                        // 设置当前位置
                        me.setCurrentPosition((curPosition[0] + 1), curPosition[1]);
                        me.fireEvent("addRecord_after",record)
                    }
                } else {
                    // 不同:继续or换列or换面板

                    // 继续(和的情况下)
                    if (record.indexOf('he')>-1) {

                        // 如果是string的那么变成array并push进去，currentPosition不变

                        currentValue = [currentValue,'he-abs'];

                        panels[me.panels.length-1][curPosition[0]][curPosition[1]] = currentValue;

                        me.fireEvent("addRecord_after",record);


                    } else {
                        if (curPosition[0] < (me.columns - 1)) {
                            // 换列
                            me.panels[me.panels.length-1][curPosition[0] + 1, 0] = record;

                            // 设置当前位置
                            me.setCurrentPosition(curPosition[0] + 1, 0);

                            me.fireEvent("addRecord_after",record)



                        } else if (curPosition[0] = (me.columns - 1)) {
                            // 换面板
                            me.fireEvent("addPanel_before");
                        }

                    }
                }

            } else if (typeof currentValue == 'object'&&currentValue instanceof Array) {
                // 已插入的最后一个元素是"he"

                if (record == currentValue[0]) {
                    // 继续

                    if (curPosition[0] == (me.columns - 1) && curPosition[1] == (me.rows - 1)) {
                        // 换面板
                        me.fireEvent("addPanel_before");
                    } else if (curPosition[1] < (me.rows - 1) && me.getValue([curPosition[0], curPosition[1] + 1]) == 0) {
                        // 向下继续
                        panels[me.panels.length-1][curPosition[0]][curPosition[1] + 1] = record;
                        // 设置当前位置
                        me.setCurrentPosition(curPosition[0], curPosition[1] + 1);
                        me.fireEvent("addRecord_after",record)
                    } else if (curPosition[0] < (me.columns - 1) && curPosition[1] == (me.rows - 1)) {
                        // 向右继续
                        panels[me.panels.length-1][curPosition[0] + 1][curPosition[1]] = record;
                        // 设置当前位置
                        me.setCurrentPosition((curPosition[0] + 1), curPosition[1]);
                        me.fireEvent("addRecord_after",record)
                    }

                }else if(record == currentValue[1]){
                    currentValue = currentValue.push('he-abs');
                    panels[me.panels.length-1][curPosition[0]][curPosition[1]] = currentValue;

                    me.fireEvent("addRecord_after",record);

                }else if(record != currentValue[0] && record !=currentValue[1]){
                    if (curPosition[0] < (me.columns - 1)) {
                        // 换列
                        panels[me.panels.length-1][curPosition[0] + 1, 0] = record;

                        // 设置当前位置
                        me.setCurrentPosition(curPosition[0] + 1, 0);

                        me.fireEvent("addRecord_after",record);
                    } else if (curPosition[0] = (me.columns - 1)) {
                        // 换面板
                        me.fireEvent("addPanel_before");
                    }
                }
            }

        }
    };

    Main = host.Class(pros, Trand);
    Main.defConfig = defConfig;
    host.TableGame.TurnTrand = Main;
})(bomao, bomao.TableGame.Trand, jQuery)
