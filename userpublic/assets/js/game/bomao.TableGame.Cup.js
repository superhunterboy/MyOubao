

//色盅对象
// cup.status=1 wait
// cup.status=2 play
// cup.status=3 stop
// cup.status=4 hide

(function(host, Event, $, undefined) {
    var defConfig = {
        dom:'#J-tagle-game-cup'
    };

    var pros = {
        init:function(cfg) {
            var me = this;
            me.status = 4;
            me.dom = $(cfg.dom);
            me.dom.appendTo($(cfg.context));
            me.dices = me.dom.find('.dices');
            me.timer = null;
            me.diceItems = me.dom.find('.dice');
            me.diceItemsObjs = [];
            me.diceItems.each(function(){
                me.diceItemsObjs.push($(this));
            });
        },
        getRandom:host.util.getRandom,
        wait:function(callback){

            var me = this;
            me.status = 1;
            me.dom.animate({
                width:267,
                left:491,
                top:80
            });
            me.dices.animate({
                width:180,
                height:120,
                top:50
            });
            me.diceItems.animate({
                width:58
            });

            $.each(me.diceItems,function(i){
                $(me.diceItems[i]).animate({left:i*59,top:25})
            })

            setTimeout(function(){
                if($.isFunction(callback)){
                    callback.call(me);
                }
            }, 400);
        },
        play:function(){
            var me = this,
                items = me.diceItemsObjs;
            me.status = 2;
            clearInterval(me.timer);
            me.timer = setInterval(function(){
                $.each(items, function(){
                    this.css({
                        left:me.getRandom(0, 115),
                        top:me.getRandom(0, 45)
                    });
                    this.removeClass().addClass('dice dice-' + me.getRandom(1, 6));
                    this.find('img').attr('src', '/assets/images/game/table/dice/cup-dice-'+ me.getRandom(1, 6) +'.png');
                });

                me.dom.css({
                    top:me.getRandom(80, 90)
                });

                
                // $(document.body).css({
                //     marginLeft:me.getRandom(0, 2),
                //     marginTop:me.getRandom(0, 2)
                // });
                
            }, 80);
        },
        stop:function(result,callback){
            var me = this,
                rPos = me.getAllPositionX(),
                i = 0;

            me.status = 3;
            clearInterval(me.timer);

            $.each(me.diceItemsObjs,function(i){
                this.css({
                    left:i*59,
                    top:25
                });
                this.removeClass().addClass('dice dice-' + result[i]);
                this.find('img').attr('src', '/assets/images/game/table/dice/cup-dice-'+ result[i] +'.png');
            });

            if($.isFunction(callback)){
                callback.call(me);
            };

        },
        hide:function(callback){
            var me = this;

            me.status = 4;
            me.dom.animate({
                width:73,
                left:584,
                top:-7
            });
            me.dices.animate({
                width:42,
                height:28,
                top:16
            });
            me.diceItems.animate({
                width:15,
                left:5,
                top:5
            });

            $.each(me.diceItems,function(i){
                $(me.diceItems[i]).animate({left:i*15,top:5})
            })

            if($.isFunction(callback)){
                callback.call(me);
            }
        },
        //所有坐标点
        getAllPositionX:function(){
            var me = this,
                pw = 180,
                ph = 100,
                w = 40,
                h = 68,
                x1 = 0,
                y1 = 0,
                x2 = pw,
                y2 = ph - (h/2),
                i = 0,
                lenx = Math.floor((x2 - x1)/w),
                xarr = [],
                result = [];
                
            

            for(i = 0; i < lenx; i++){
                xarr.push(i * w);
            }

            //console.log(lenx);

            xarr.sort(function(){
                return Math.random() > 0.5 ? -1 : 1;
            });



            result = xarr.slice(0, 3);

            return result;
            
        }



    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Cup = Main;

})(bomao, bomao.Event, jQuery);



