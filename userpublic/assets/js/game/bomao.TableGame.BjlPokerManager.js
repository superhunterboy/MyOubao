(function(host, Event, $, undefined) {
    var defConfig = {

    };
    var pros = {
        // 预加载扑克组
        init: function() {
            var me = this;
            me.pokers = [];
            me.values = {
                twoXianSum: 0,
                threeXianSum: 0,
                twoZhuangSum: 0,
                threeZhuangSum: 0
            }
        },

        // 预加载所有的扑克
        cachePokers: function() {

        },

        setValues: function(value) {
            var me = this,
                zx = value.split('|'),
                strZhuang = zx[1],
                strXian = zx[0],
                arrZhuang = strZhuang.split(" "),
                arrXian = strXian.split(" "),
                zhuang1 = 0,
                zhuang2 = 0,
                zhuang3 = 0,
                xian1 = 0,
                xian2 = 0,
                xian3 = 0;
            if (typeof arrXian[0] != 'undefined') {
                xian1 = me.getRealValue(arrXian[0]);
            }
            if (typeof arrXian[1] != 'undefined') {
                xian2 = me.getRealValue(arrXian[1]);
            }
            if (typeof arrXian[2] != 'undefined') {
                xian3 = me.getRealValue(arrXian[2]);
            }
            if (typeof arrZhuang[0] != 'undefined') {
                zhuang1 = me.getRealValue(arrZhuang[0]);
            }
            if (typeof arrZhuang[1] != 'undefined') {
                zhuang2 = me.getRealValue(arrZhuang[1]);
            }
            if (typeof arrZhuang[2] != 'undefined') {
                zhuang3 = me.getRealValue(arrZhuang[2]);
            }
            me.values.twoXianSum = me.getSum(xian1, xian2);
            me.values.threeXianSum = me.getSum(me.values.twoXianSum, xian3);
            me.values.twoZhuangSum = me.getSum(zhuang1, zhuang2);
            me.values.threeZhuangSum = me.getSum(me.values.twoZhuangSum, zhuang3);
        },

        getRealValue: function(val) {
            var val = Number(val);
            val = val % 13;
            if (val > 9) {
                val = 0;
            }
            return val;
        },

        getSum: function(val1, val2) {
            var me = this,
                sum = 0,
                val1 = me.getRealValue(val1),
                val2 = me.getRealValue(val2);

            sum = val1 + val2;

            if (sum >= 10) {
                sum -= 10;
            }
            return sum;
        },

        // 根据开奖号从扑克组中拿到对应的扑克
        initPokers: function(value) {
            var me = this,
                BjlPoker = bomao.TableGame.BjlPoker,
                zx = value.split('|'),
                strZhuang = zx[1],
                strXian = zx[0],
                arrZhuang = strZhuang.split(" "),
                arrXian = strXian.split(" "),
                zhuang1,
                zhuang2,
                zhuang3,
                xian1,
                xian2,
                xian3;
            // 清空
            me.pokers = [];

            if (typeof arrXian[2] != 'undefined') {
                xian3 = new BjlPoker({
                    containerName: 'poker-container-xian-3',
                    value: arrXian[2],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(xian3);
            }
            if (typeof arrZhuang[2] != 'undefined') {
                zhuang3 = new BjlPoker({
                    containerName: 'poker-container-zhuang-3',
                    value: arrZhuang[2],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(zhuang3);
            }
            if (typeof arrXian[1] != 'undefined') {
                xian2 = new BjlPoker({
                    containerName: 'poker-container-xian-2',
                    value: arrXian[1],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(xian2);
            }
            if (typeof arrZhuang[1] != 'undefined') {
                zhuang2 = new BjlPoker({
                    containerName: 'poker-container-zhuang-2',
                    value: arrZhuang[1],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(zhuang2);
            }
            if (typeof arrXian[0] != 'undefined') {
                xian1 = new BjlPoker({
                    containerName: 'poker-container-xian-1',
                    value: arrXian[0],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(xian1);
            }
            if (typeof arrZhuang[0] != 'undefined') {
                zhuang1 = new BjlPoker({
                    containerName: 'poker-container-zhuang-1',
                    value: arrZhuang[0],
                    sibling: '.poker-sender-cover'
                })
                me.pokers.push(zhuang1);
            }
        },

        sendPokers: function() {
            var me = this;
            setTimeout(function() {
                $('[name=poker-container-xian-1]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-xian-1]').find('.card').toggleClass('filpped');
                }, 500)
                setTimeout(function() {
                    $('[name=poker-container-xian-1]').toggleClass('poker-container-xian-1');
                }, 1000);
            }, 0);

            setTimeout(function() {
                $('[name=poker-container-zhuang-1]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-zhuang-1]').find('.card').toggleClass('filpped');
                }, 500)
                setTimeout(function() {
                    $('[name=poker-container-zhuang-1]').toggleClass('poker-container-zhuang-1');
                }, 1000);
            }, 1500);

            setTimeout(function() {
                $('[name=poker-container-xian-2]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-xian-2]').find('.card').toggleClass('filpped');
                }, 500)
                setTimeout(function() {
                    $('[name=poker-container-xian-2]').toggleClass('poker-container-xian-2');
                }, 1000);

            }, 3000);

            // 显示闲家点数
            setTimeout(function(){
                $(".xian-value").html(me.values.twoXianSum);
                $(".xian-value").show();
            },4500)

            setTimeout(function() {
                $('[name=poker-container-zhuang-2]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-zhuang-2]').find('.card').toggleClass('filpped');
                }, 500);
                setTimeout(function() {
                    $('[name=poker-container-zhuang-2]').toggleClass('poker-container-zhuang-2');
                }, 1000);
            }, 4500);

            // 显示庄家点数
            setTimeout(function() {
                $(".zhuang-value").html(me.values.twoZhuangSum);
                $(".zhuang-value").show();
            }, 6000);

            setTimeout(function() {
                $('[name=poker-container-xian-3]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-xian-3]').find('.card').toggleClass('filpped');
                }, 500);
                setTimeout(function() {
                    $('[name=poker-container-xian-3]').toggleClass('poker-container-xian-3');
                }, 1000);
            }, 6000);

            // 显示闲家点数
            setTimeout(function() {
                $(".xian-value").html(me.values.threeXianSum);
            }, 7500);

            setTimeout(function() {
                $('[name=poker-container-zhuang-3]').toggleClass("poker-container").toggleClass('poker-station');
                setTimeout(function() {
                    $('[name=poker-container-zhuang-3]').find('.card').toggleClass('filpped');
                }, 500);
                setTimeout(function() {
                    $('[name=poker-container-zhuang-3]').toggleClass('poker-container-zhuang-3');
                }, 1000);
            }, 7500);

            // 显示庄家点数
            setTimeout(function() {
                $(".zhuang-value").html(me.values.threeZhuangSum);
            }, 9000);
        },

        collectPokers: function() {
            $.each($('.poker-station'), function() {
                if (!$(this).hasClass("poker-container-left")) {
                    $(this).addClass("poker-container-left").removeAttr("name").find('.card').removeClass('filpped');
                }
            })

            // 删除回收后的扑克,只保留第一张
            $('.poker-container-left').each(function(i) {
                if (i != 1) {
                    $(this).remove();
                }
            })

            // 隐藏点数
            $(".xian-value").hide();
            $(".zhuang-value").hide();
        }
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BjlPokerManager = Main;

})(bomao, bomao.Event, jQuery);
