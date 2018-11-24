// 区块对象，向下关联筹码，向上关联桌面对象
(function(host, Event, $, undefined) {
    var defConfig = {

    };


    var pros = {
        init: function(cfg) {
            var me = this;
            me.chips = null;
            me.id = cfg['id'];
            me.name = cfg['name_en'];
            me.odds = cfg['prize_odds'];
        },
        getName:function(){
            return this.name;
        },
        getChips:function(){
            return this.chips;
        },
        getLastChip:function(){
            return this.chips.getLastChip();
        },
        getChipsCase:function(){
            var me = this,
                result = [];

            $.each(me.getChips().getChips(), function(){
                result.push(this);
            });
            return result;
        },
        addChip:function(chip){
            var me = this;
            me.chips.addChip(chip);
            me.fireEvent('addchip_after', chip,me.getChipsNum);
        },
        compensateChip:function(chip){
            var me = this;
            me.chips.addChip(chip);
            me.fireEvent('compensateChip_after',chip);
        },
        getChipsNum:function(){
            return this.chips.getResult()['chipsnum'];
        },
        getResult:function(){
            return this.chips.getResult();
        },
        cancelChip:function(){
            return this.chips.delLastChip();
        },
        clearAll:function(){
            return this.chips.delAllChips();
        },
        getOdds:function(){
            return this.odds;
        }


    }


    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Area = Main;


})(bomao, bomao.Event, jQuery);
