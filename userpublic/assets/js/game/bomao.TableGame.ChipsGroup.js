// 筹码组对象，对应桌面下方的筹码道具

(function(host, Event, undefined) {
    var defConfig = {

    };



    var pros = {
        init: function(cfg) {
            var me = this;
            me.selected = null;
            me.chips = {};
        },
        addChip: function(chip) {
            var me = this;
            me.chips['' + chip.getMoney()] = chip;
        },
        getChip: function(money) {
            return this.chips['' + money];
        },
        getChips: function() {
            return this.chips;
        },
        select: function(money) {
            var me = this,
                chip = me.getChips()[money];
            if (chip && chip != me.selected) {
                me.selected = chip;
                me.fireEvent('change_after', chip);
            }
        },
        getSelectedChip: function() {
            return this.selected;
        },
        //获取最小筹码
        getMinChip: function() {
            var me = this;
            return me.getChip(me.getMoneyList()[0]);
        },
        getMoneyList: function() {
            var me = this,
                chips = me.getChips(),
                arr = [],
                p;
            for (p in chips) {
                if (chips.hasOwnProperty(p)) {
                    money = chips[p].getMoney();
                    arr.push(money);
                }
            }
            arr.sort(function(a, b) {
                return a - b > 0;
            });
            return arr;
        },
        //金额兑换成筹码栈
        moneyToChips: function(num) {
            var me = this,
                moneyList = me.getMoneyList(),
                i = 0,
                len,
                result = [];
            num = parseInt(num);
            moneyList.sort(function(a, b) {
                return a - b < 0;
            });

            len = moneyList.length;
            for (i - 0; i < len; i++) {
                if (Math.floor(num / moneyList[i]) == 0) {
                    continue;
                }
                result.push({
                    'money': moneyList[i],
                    'num': Math.floor(num / moneyList[i])
                });
                num = num % moneyList[i];
                if (num == 0) {
                    break;
                }
            }

            return result;

        },
        // 根据当前余额设置每个筹码的可用状态
        setChipsStatus: function(balance) {
            var me = this,
                chips = me.getChips(),
                p;
            for (p in chips) {
                if (chips.hasOwnProperty(p)) {
                    if (balance < chips[p].getMoney()) {
                        chips[p].setStatus(false);
                    } else {
                        chips[p].setStatus(true);
                    }

                }
            }
        }
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.ChipsGroup = Main;


})(bomao, bomao.Event, jQuery);
