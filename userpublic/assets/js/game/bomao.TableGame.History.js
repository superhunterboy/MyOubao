(function(host, Event, $, undefined) {
    var defConfig = {
        records_container: ".his-list",
        tpl_record: '<li> <span class = "num num-<#=num0#>"></span><span class = "num num-<#=num1#>"></span><span class = "num num-<#=num2#>"></span><span class = "text" ><#=bs#></span><span class = "text"> <#=oe#> </span> <span class = "text"> <#=sum#> </span> <span class = "text text-number"><#=short_issue#></span> </li>',
        last_record_container: ".balls",
        tpl_last_record: '<i class="dice dice-<#=num0#>"></i><i class="dice dice-<#=num1#>"></i><i class="dice dice-<#=num2#>"></i>'
    }

    var pros = {

        init: function(cfg) {
            var me = this;

            me.records_container = !!cfg.records_container ? cfg.records_container : me.defConfig.records_container;
            me.tpl_record = !!cfg.tpl_record ? cfg.tpl_record : me.defConfig.tpl_record;
            me.last_record_container = !!cfg.last_record_container ? cfg.last_record_container : me.defConfig.last_record_container;
            me.tpl_last_record = !!cfg.tpl_last_record ? cfg.tpl_last_record : me.defConfig.tpl_last_record;
            if(cfg.records.length > 0){
                me.addHistoryRecords(cfg.records);
            }
        },

        addRecord: function(record) {
            var me = this;

            record['nums'].sort(function(a, b){
                return a - b;
            });
            me.addLastRecord(record);
            me.addHistoryRecord(record, true);
        },


        // 添加上一条历史记录（右侧）
        addLastRecord: function(last_record) {
            var me = this,
                issue = last_record.issue,
                nums = last_record.nums,
                sum = me.sum(nums),
                oe = me.judgeOE(nums),
                bs = me.judgeBS(nums),
                last_record = {
                    sum: sum,
                    oe: oe,
                    bs: bs,
                    issue: issue
                },
                i = 0,
                length = nums.length;

            for (i; i < length; i++) {
                last_record["num" + i] = nums[i];
            }
            last_record_template = host.util.template(me.tpl_last_record, last_record);

            $(me.last_record_container).empty().append(last_record_template);

            me.fireEvent("addLastRecord_after", last_record);
        },

        // 添加一条历史记录（左侧）
        addHistoryRecord: function(record, effect) {
            var me = this,
                issue = record.issue,
                nums = record.nums,
                sum = me.sum(nums),
                oe = me.judgeOE(nums),
                bs = me.judgeBS(nums),
                short_issue = me.getShortIssue(issue),
                record = {
                    sum: sum,
                    oe: oe,
                    bs: bs,
                    short_issue: short_issue
                },
                i = 0,
                length = nums.length;

            for (i; i < length; i++) {
                record["num" + i] = nums[i];
            }
            $(me.records_container+" > li:first").removeClass('active');

            record_template = host.util.template(me.tpl_record, record);
            tmp = $(record_template);
            $(me.records_container).prepend(tmp);

            $(me.records_container+" > li:first").addClass('active');

            if (effect) {
                me.fireEvent("addHistoryRecord_after", record);

            }

        },

        // 批量添加历史记录(用于页面初始化时)
        addHistoryRecords: function(records) {
            var me = this,
                i = 0,
                length = records.length;
            for (i; i < length; i++) {
                me.addHistoryRecord(records[i]);
            }

            me.addLastRecord(records[length - 1]);
        },

        // 获得和值
        sum: function(nums) {
            var i = 0,
                length = nums.length,
                sum = 0;

            for (i; i < length; i++) {
                sum += parseInt(nums[i]);
            }
            return sum;
        },

        // 判断大小
        judgeBS: function(nums) {
            var i = 0,
                length = nums.length,
                sum = 0;

            for (i; i < length; i++) {
                sum += parseInt(nums[i]);
            }
            return (sum > 10) ? "大" : "小";
        },

        // 判断单双
        judgeOE: function(nums) {
            var i = 0,
                length = nums.length,
                sum = 0;

            for (i; i < length; i++) {
                sum += parseInt(nums[i]);
            }
            return (sum % 2 == 1) ? "单" : "双";
        },

        // 获得短期号（后4位）
        getShortIssue: function(issue) {
            return issue.substr(issue.length - 4);
        }
    }


    Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.History = Main;

})(bomao, bomao.Event, jQuery);




