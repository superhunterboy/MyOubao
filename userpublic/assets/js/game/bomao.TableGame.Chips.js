// 区域筹码组对象，对应某个区域内已经下注的筹码
(function(host, Event, $, undefined) {
    var defConfig = {

    };

    var pros = {
        init: function(cfg) {
            var me = this;
            me._chips = [];
        },
        addChip: function(chip) {
            var me = this;
            me._chips.push(chip);
        },
        getChips: function() {
            return this._chips;
        },
        getLastChip: function() {
            return this._chips[this._chips.length-1];
        },
        delLastChip: function() {
            var me = this,
                lastChip = me._chips.pop();
            me.fireEvent("delLastChip_after", lastChip);
            return lastChip;
        },
        delAllChips: function() {
            var me = this,
                tempChips = me._chips;
            me._chips = [];
            me.fireEvent("delAllChips", tempChips);
            return tempChips;
        },
        getResult: function() {
            var me = this,
                chipsnum = me._chips.length,
                money = 0,
                i = 0;

            for (i = 0; i < chipsnum; i++) {
                money += me._chips[i].getMoney();
            }

            return {
                chipsnum: chipsnum,
                money: money
            };

        }
    }


    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Chips = Main;


})(bomao, bomao.Event, jQuery);
