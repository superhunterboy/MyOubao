(function(host, Event, $, undefined) {
    var defConfig = {
        records_container: ".body-bet-records",
        tpl_record: '<tr><td><#=number#></td><td><#=bought_at#></td><td><#=method#></td><td><#=balls#></td><td><#=prizeballs#></td><td><#=money#></td><td><#=prize#></td><td><#=status#></td><td><a href="/projects/view/<#=id#>" style="text-decoration:underline">详情</a></td></tr>',
        tpl_empty:'<tr><td colspan="9" height="122px">您最近7天暂时没有投注记录！</td></tr>',
        view_more:'<tr><td colspan="9"><a class="btn-more-records" href="/projects" target="_blank">更多游戏记录...</a></td></tr>'
    }

    var pros = {

        init: function(cfg) {
            var me = this;
            me.records_container = !!cfg.records_container ? cfg.records_container : me.defConfig.records_container;
            me.tpl_record = !!cfg.tpl_record ? cfg.tpl_record : me.defConfig.tpl_record;
            me.tpl_empty = !!cfg.tpl_empty?cfg.tpl_empty:me.defConfig.tpl_empty;
            me.view_more =!!cfg.view_more?cfg.view_more:me.defConfig.view_more;
        },

        updateBet:function(records){

            var me = this;
            // 清空列表
            $(me.records_container).empty();


            if(records.length > 0){
                 // 添加记录
                $.each(records,function(i){
                    if(i > 5){
                        return;
                    }
                    var record = this;
                    if(record['prize']==null){
                        record['prize']="0.000000";
                    }
                    if(record['is_overprize']){
                        record['prize']=record['prize']+"奖金超限";
                    }
                    record_template = host.util.template(me.tpl_record,record);
                    $(me.records_container).append(record_template);
                })

                $(me.records_container).append(me.view_more);
            }else{
                $(me.records_container).append(me.tpl_empty);
            }
           
                    
        }
    }


    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BetHistory = Main;

})(bomao, bomao.Event, jQuery);




