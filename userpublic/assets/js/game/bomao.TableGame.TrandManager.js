// 转弯走势类


// panel中的元素以及对应的含义：
// 0-可以写入

// -----------------
// 龙虎路面板里的flag

// 1-long-龙
// 2-hu-虎
// 3-he-和


// 4-long-----空心蓝圆
// 5-hu-------空心红圆
// 6-he-------斜杠


// ------------------
// 龙单双面板里的字段
// 7-longdan-龙单
// 8-longshuang-龙双

// 9-longdan
// 10-longshuang
// -------------------
// 11-hudan-虎单
// 12-hushuang-虎双
// 13-t_hudan
// 14-t_hushuang
// ------------------
// longhong-龙红
// longhei-龙黑
// t_longhong
// t_longhei
// ------------------
// huhong-虎红
// huhei-虎黑
// t_huhong
// t_huhei
// ------------------

// 可以代表所
// d_long
// d_hu
// x_long
// x_hu
// y_long
// y_hu
(function(host, Trand, $, undefined) {
    var defConfig = {

    };
    var flagRecordConfig={
        "long":1,
        "hu":2,
        "he":3,
        "t_long":4,
        "t_hu":5,
        "t_he":6
    }

    pros = {
        init: function(cfg) {
            var me = this;
        },
        // 提供record
        getRecordFlag:function(record){
            return flagRecordConfig[record];
        },
    };

    Main = host.Class(pros, Trand);
    Main.defConfig = defConfig;
    host.TableGame.TrandManager = Main;
})(bomao, bomao.TableGame.Trand, jQuery)







