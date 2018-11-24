(function(host, Event, $, undefined) {
    var defConfig = {
        normalLonghuSequentialContainer: '.normal-sequential-longhu',
        normalLonghuTurnoverContainer: '.normal-turnover-longhu',
    }

    var pros = {

        init: function(cfg) {
            var me = this;

            // 可由任意一个面板触发，如果任意一个面板满了，则将此值置为1。
            me.isPaneFull = 0;

            // 左侧顺序排列走势的面板索引
            me.LonghuSequentialPaneIndex = 1;
            me.LongdanshuangSequentialPaneIndex = 1;
            me.HudanshuangSequentialPaneIndex = 1;
            me.LonghongheiSequentialPaneIndex = 1;
            me.HuhongheiSequentialPaneIndex = 1;

            // 右侧转弯走势的面板索引
            me.LonghuTurnoverPaneIndex = 1;
            me.LongdanshuangTurnoverPaneIndex = 1;
            me.HudanshuangTurnoverPaneIndex = 1;
            me.LonghongheiTurnoverPaneIndex = 1;
            me.HuhongheiTurnoverPaneIndex = 1;


            me.normalLonghuSequentialContainer = '.normal-sequential-longhu';
            me.normalLonghuTurnoverContainer = '.normal-turnover-longhu';


            // 左侧顺序排列走势容器的行列数量以及初始位置
            me.sequentialContainerRows = 6;
            me.currentSequentialContainerRow = 0;
            me.sequentialContainerColumns = 28;
            me.currentSequentialContainerColumn = 0;

            me.currentLongdanshuangSequentialContainerColumn = 0;
            me.currentLongdanshuangSequentialContainerRow = 0;
            me.LongdanshuangSequentialContainerColumn = 28;
            me.LongdanshuangSequentialContainerRow = 6;

            me.currentHudanshuangSequentialContainerColumn = 0;
            me.currentHudanshuangSequentialContainerRow = 0;
            me.HudanshuangSequentialContainerColumn = 28;
            me.HudanshuangSequentialContainerRow = 6;

            me.currentLonghongheiSequentialContainerColumn = 0;
            me.currentLonghongheiSequentialContainerRow = 0;
            me.LonghongheiSequentialContainerColumn = 28;
            me.LonghongheiSequentialContainerRow = 6;

            me.currentHuhongheiSequentialContainerColumn = 0;
            me.currentHuhongheiSequentialContainerRow = 0;
            me.HuhongheiSequentialContainerColumn = 28;
            me.HuhongheiSequentialContainerRow = 6;



            // 右侧顺序排列走势容器的行列数量以及初始位置
            me.LonghuTurnoverContainerRows = 6;
            me.currentLonghuTurnoverContainerRows = 0;
            me.LonghuTurnoverContainerColumns = 15;
            me.currentLonghuTurnoverContainerColumns = 0;

            me.LongdanshuangTurnoverContainerRows = 6;
            me.currentLongdanshuangTurnoverContainerRows = 0;
            me.LongdanshuangTurnoverContainerColumns = 15;
            me.currentLongdanshuangTurnoverContainerColumns = 0;

            me.LonghuTurnoverContainerRows = 6;
            me.currentLonghuTurnoverContainerRows = 0;
            me.LonghuTurnoverContainerColumns = 15;
            me.currentLonghuTurnoverContainerColumns = 0;


            me.LonghongheiTurnoverContainerRows = 6;
            me.currentLonghongheiTurnoverContainerRows = 0;
            me.LonghongheiTurnoverContainerColumns = 15;
            me.currentLonghongheiTurnoverContainerColumns = 0;

            me.HuhongheiTurnoverContainerRows = 6;
            me.currentHuhongheiTurnoverContainerRows = 0;
            me.HuhongheiTurnoverContainerColumns = 15;
            me.currentHuhongheiTurnoverContainerColumns = 0;



            // 初始化左侧容器
            me.initSequentialContainer(".normal-sequential-longhu",'sequential-trands-pane-longhu', me.LonghuSequentialPaneIndex, 6, 28);
            me.initSequentialContainer(".normal-sequential-longdanshuang",'sequential-trands-pane-longdanshuang', me.LongdanshuangSequentialPaneIndex, 6, 28);

            me.initSequentialContainer(".normal-sequential-hudanshuang",'sequential-trands-pane-hudanshuang', me.HudanshuangSequentialPaneIndex, 6, 28);

            me.initSequentialContainer(".normal-sequential-longhonghei",'sequential-trands-pane-longhonghei', me.LonghongheiSequentialPaneIndex, 6, 28);
            me.initSequentialContainer(".normal-sequential-huhonghei",'sequential-trands-pane-huhonghei', me.HuhongheiSequentialPaneIndex, 6, 28);


            // 初始化右侧容器
            // me.initNomalTurnoverContainer(".normal-turnover-longhu","turnover-trands-pane-longhu",me.LonghuTurnoverPaneIndex, 6, 15);
            // me.initNomalTurnoverContainer(".normal-turnover-longdanshuang","turnover-trands-pane-longdanshuang",me.LongdanshuangTurnoverPaneIndex, 6, 15);
            // me.initNomalTurnoverContainer(".normal-turnover-hudanshuang","turnover-trands-pane-hudanshuang",me.HudanshuangTurnoverPaneIndex , 6, 15);
            // me.initNomalTurnoverContainer(".normal-turnover-longhonghei","turnover-trands-pane-longhonghei", me.LonghongheiTurnoverPaneIndex ,6, 15);
            // me.initNomalTurnoverContainer(".normal-turnover-huhonghei","turnover-trands-pane-huhonghei",me.HuhongheiTurnoverPaneIndex , 6, 15);

            
            

            me.records = cfg.records;
            me.puker = cfg.puker;

            // 上次记录
            me.currRecord = null;
            me.lastRecord = null;

            // 往容器中添加内容
            if (me.records.length > 0) {
                for (var x = 0; x < me.records.length; x++) {
                    var data = me.records[x].code.split(" "),
                        longData = data[0],
                        huData = data[1],
                        item = me.getWinnerPuker(longData, huData);
                        longItem = me.getLongPuker(longData);
                        huItem = me.getHuPuker(huData);

                    if(me.isPaneFull==1){
                        // 所有区域的面板隐藏
                        $(".sequentail-trands-pane").hide();
                        $(".turnover-trands-pane").hide();



                        // 所有区域的面板全部新建
                        // 左侧
                        me.LonghuSequentialPaneIndex++;
                        me.LongdanshuangSequentialPaneIndex++;
                        me.HudanshuangSequentialPaneIndex++;
                        me.LonghongheiSequentialPaneIndex++;
                        me.HuhongheiSequentialPaneIndex++;

                        me.initSequentialContainer(".normal-sequential-longhu",'sequential-trands-pane-longhu', me.LonghuSequentialPaneIndex, 6, 28);
                        me.initSequentialContainer(".normal-sequential-longdanshuang",'sequential-trands-pane-longdanshuang', me.LongdanshuangSequentialPaneIndex, 6, 28);
                        me.initSequentialContainer(".normal-sequential-hudanshuang",'sequential-trands-pane-hudanshuang', me.HudanshuangSequentialPaneIndex, 6, 28);
                        me.initSequentialContainer(".normal-sequential-longhonghei",'sequential-trands-pane-longhonghei', me.LonghongheiSequentialPaneIndex, 6, 28);
                        me.initSequentialContainer(".normal-sequential-huhonghei",'sequential-trands-pane-huhonghei', me.HuhongheiSequentialPaneIndex, 6, 28);


                        // 右侧
                        // me.LonghuTurnoverPaneIndex++;
                        // me.LongdanshuangTurnoverPaneIndex++;
                        // me.HudanshuangTurnoverPaneIndex++;
                        // me.LonghongheiTurnoverPaneIndex++;
                        // me.HuhongheiTurnoverPaneIndex++;

                        // me.initNomalTurnoverContainer(".normal-turnover-longhu","turnover-trands-pane-longhu",me.LonghuTurnoverPaneIndex, 6, 15);
                        // me.initNomalTurnoverContainer(".normal-turnover-longdanshuang","turnover-trands-pane-longdanshuang",me.LongdanshuangTurnoverPaneIndex, 6, 15);
                        // me.initNomalTurnoverContainer(".normal-turnover-hudanshuang","turnover-trands-pane-hudanshuang",me.HudanshuangTurnoverPaneIndex , 6, 15);
                        // me.initNomalTurnoverContainer(".normal-turnover-longhonghei","turnover-trands-pane-longhonghei", me.LonghongheiTurnoverPaneIndex ,6, 15);
                        // me.initNomalTurnoverContainer(".normal-turnover-huhonghei","turnover-trands-pane-huhonghei",me.HuhongheiTurnoverPaneIndex , 6, 15);

                        // 所有区域的插入点全部置顶（左上角，从头开始）
                        // 左侧
                        me.currentLongdanshuangSequentialContainerColumn = 0;
                        me.currentLongdanshuangSequentialContainerRow = 0;

                        me.currentLongdanshuangSequentialContainerColumn = 0;
                        me.currentLongdanshuangSequentialContainerRow = 0;

                        me.currentHudanshuangSequentialContainerColumn = 0;
                        me.currentHudanshuangSequentialContainerRow = 0;

                        me.currentLonghongheiSequentialContainerColumn = 0;
                        me.currentLonghongheiSequentialContainerRow = 0;

                        me.currentHuhongheiSequentialContainerColumn = 0;
                        me.currentHuhongheiSequentialContainerRow = 0;


                        // 右侧
                        me.currentLonghuTurnoverContainerRows = 0;
                        me.currentLonghuTurnoverContainerColumns = 0;

                        me.currentLongdanshuangTurnoverContainerRows = 0;
                        me.currentLongdanshuangTurnoverContainerColumns = 0;

                        me.currentLonghuTurnoverContainerRows = 0;
                        me.currentLonghuTurnoverContainerColumns = 0;

                        me.currentLonghongheiTurnoverContainerRows = 0;
                        me.currentLonghongheiTurnoverContainerColumns = 0;

                        me.currentHuhongheiTurnoverContainerRows = 0;
                        me.currentHuhongheiTurnoverContainerColumns = 0;

                        me.isPaneFull = 0;
                    }

                    // 添加左侧走势图
                    me.addLonghuSequentialItem(".normal-sequential-longhu", '.sequential-trands-pane-longhu', me.LonghuSequentialPaneIndex, item);
                    me.addLongdanshuangSequentialItem(".normal-sequential-longdanshuang", '.sequential-trands-pane-longdanshuang', me.LongdanshuangSequentialPaneIndex, longItem);
                    me.addHudanshuangSequentialItem(".normal-sequential-hudanshuang", '.sequential-trands-pane-hudanshuang', me.HudanshuangSequentialPaneIndex, huItem);
                    me.addLonghongheiSequentialItem(".normal-sequential-longhonghei", '.sequential-trands-pane-longhonghei', me.LonghongheiSequentialPaneIndex, longItem);
                    me.addHuhongheiSequentialItem(".normal-sequential-huhonghei", '.sequential-trands-pane-huhonghei', me.HuhongheiSequentialPaneIndex, huItem);

                    // 添加右侧走势图
                    // me.addLonghuTurnoverItem(".normal-turnover-longhu",item);
                }
            }
        },

        // 初始化简洁和专业版顺序排列走势容器
        initSequentialContainer: function(mainContainer,CLS,paneIndex, rows, columns) {
            var me=this,
                pane = "<div class='sequentail-trands-pane "+ CLS +"' index='" + paneIndex + "'>";
            for (var i = 0; i < columns; i++) {
                var column = "<div class='column'>";
                for (var j = 0; j < rows; j++) {
                    column += "<div class='item'></div>";
                }
                column += "</div>";
                pane += column;
            }
            pane += "</div>"
            $(mainContainer).append(pane);
        },

        // 初始化简洁版转弯走势容器（龙虎、龙单双、虎单双、龙红黑、虎红黑简洁版对应的转弯走势容器都一样）
        initNomalTurnoverContainer: function(mainContainer,CLS,paneIndex, rows, columns) {

            var me = this,
                pane = "<div class='turnover-trands-pane "+CLS+"' index='"+paneIndex+"'>";
            for (var a = 0; a < columns; a++) {
                var column = "<div class='column'>";
                for (var b = 0; b < rows; b++) {
                    column += "<div class='item'></div>";
                }
                column += "</div>";
                pane +=column;
            }
            pane +="</div>";
            $(mainContainer).append(pane);
        },

        // TODO:初始化专业版转弯走势容器（龙虎、龙单双、虎单双、龙红黑、虎红黑专业版对应的转弯走势容器都一样）
        initProTurnoverContainer: function() {
        },

        // TODO:初始化专业版大眼仔路走势容器（龙虎、龙单双、虎单双、龙红黑、虎红黑专业版对应的大眼仔路走势容器都一样）
        initProDaluContainer: function() {},

        // TODO:初始化专业版小眼仔路走势容器（龙虎、龙单双、虎单双、龙红黑、虎红黑专业版对应的小眼仔路走势容器都一样）
        initProXiaoluContainer: function() {

        },

        // TODO:初始化专业版曱甴路路走势容器（龙虎、龙单双、虎单双、龙红黑、虎红黑专业版对应的曱甴路走势容器都一样）
        initProYueyouluContainer: function() {

        },

        // 添加简洁版和专业版龙虎走势项（专业版和简洁版一样）
        // 参数为：所属容器，子容器，当前Panel的索引，当前记录。
        addLonghuSequentialItem: function(mainContainer, childContainer, paneIndex, record) {

            var me = this,
                columnIndex = me.currentSequentialContainerColumn + 1,
                rowIndex = me.currentSequentialContainerRow + 1,
                CLS = Object.keys(record)[0] + "-item";

            $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");
            
            if (me.currentSequentialContainerRow >= me.sequentialContainerRows - 1) {
                me.currentSequentialContainerRow = 0;

                if (me.currentSequentialContainerColumn >= me.sequentialContainerColumns - 1) {
                    me.currentSequentialContainerColumn = 0;


                    me.isPaneFull = 1;
                    // $(childContainer).hide();
                    // me.LonghuSequentialPaneIndex++;
                    // me.initSequentialContainer(".normal-sequential-longhu",'sequential-trands-pane-longhu', me.LonghuSequentialPaneIndex, 6, 10);

                } else {
                    me.currentSequentialContainerColumn++;
                }
            } else {
                me.currentSequentialContainerRow++;
            }
        },

        addLongdanshuangSequentialItem: function(mainContainer, childContainer, paneIndex, record) {
            var me = this,
                columnIndex = me.currentLongdanshuangSequentialContainerColumn + 1,
                rowIndex = me.currentLongdanshuangSequentialContainerRow + 1,
                CLS = "";


            // console.log(rowIndex);
            if (Object.keys(record)[0] == "long") {
                CLS = record['long'].danshuang + "-item";
                $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");

                if (me.currentLongdanshuangSequentialContainerRow >= me.LongdanshuangSequentialContainerRow - 1) {
                    me.currentLongdanshuangSequentialContainerRow = 0;

                    if (me.currentLongdanshuangSequentialContainerColumn >= me.LongdanshuangSequentialContainerColumn - 1) {
                        me.currentLongdanshuangSequentialContainerColumn = 0;


                        me.isPaneFull = 1;
                        // $(childContainer).hide();
                        // me.LongdanshuangSequentialPaneIndex++;
                        // me.initSequentialContainer(".normal-sequential-longdanshuang",'sequential-trands-pane-longdanshuang', me.LongdanshuangSequentialPaneIndex, 6, 10);



                    } else {
                        me.currentLongdanshuangSequentialContainerColumn++;
                    }
                } else {
                    me.currentLongdanshuangSequentialContainerRow++;
                }
            }

        },
        addHudanshuangSequentialItem: function(mainContainer, childContainer, paneIndex, record) {
            var me = this,
                columnIndex = me.currentHudanshuangSequentialContainerColumn + 1,
                rowIndex = me.currentHudanshuangSequentialContainerRow + 1,

                CLS = "";

            if (Object.keys(record)[0] == "hu") {
                CLS = record['hu'].danshuang + "-item";
                $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");

                if (me.currentHudanshuangSequentialContainerRow >= me.HudanshuangSequentialContainerRow - 1) {
                    me.currentHudanshuangSequentialContainerRow = 0;

                    if (me.currentHudanshuangSequentialContainerColumn >= me.HudanshuangSequentialContainerColumn - 1) {
                        me.currentHudanshuangSequentialContainerColumn = 0;

                        me.isPaneFull = 1;
                        // $(childContainer).hide();
                        // me.HudanshuangSequentialPaneIndex++;
                        // me.initSequentialContainer(".normal-sequential-hudanshaung",'sequential-trands-pane-hudanshaung', me.HudanshuangSequentialPaneIndex, 6, 10);

                    } else {
                        me.currentHudanshuangSequentialContainerColumn++;
                    }
                } else {
                    me.currentHudanshuangSequentialContainerRow++;
                }
            }

        },
        addLonghongheiSequentialItem: function(mainContainer, childContainer, paneIndex, record) {
            var me = this,
                columnIndex = me.currentLonghongheiSequentialContainerColumn + 1,
                rowIndex = me.currentLonghongheiSequentialContainerRow + 1,
                CLS = "";

            if (Object.keys(record)[0] == "long") {
                CLS = record['long'].honghei + "-item";
                $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");

                if (me.currentLonghongheiSequentialContainerRow >= me.LonghongheiSequentialContainerRow - 1) {
                    me.currentLonghongheiSequentialContainerRow = 0;

                    if (me.currentLonghongheiSequentialContainerColumn >= me.LonghongheiSequentialContainerColumn - 1) {
                        me.currentLonghongheiSequentialContainerColumn = 0;

                        me.isPaneFull=1;
                    } else {
                        me.currentLonghongheiSequentialContainerColumn++;
                    }
                } else {
                    me.currentLonghongheiSequentialContainerRow++;
                }
            }

        },
        addHuhongheiSequentialItem: function(mainContainer, childContainer, paneIndex, record) {
            var me = this,
                columnIndex = me.currentHuhongheiSequentialContainerColumn + 1,
                rowIndex = me.currentHuhongheiSequentialContainerRow + 1,
                CLS = "";

            if (Object.keys(record)[0] == "hu") {
                CLS = record['hu'].honghei + "-item";
                $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");

                if (me.currentHuhongheiSequentialContainerRow >= me.HuhongheiSequentialContainerRow - 1) {
                    me.currentHuhongheiSequentialContainerRow = 0;

                    if (me.currentHuhongheiSequentialContainerColumn >= me.HuhongheiSequentialContainerColumn - 1) {
                        me.currentHuhongheiSequentialContainerColumn = 0;
                        // $(childContainer).hide();
                        // me.HuhongheiSequentialPaneIndex++;
                        // me.initSequentialContainer(".normal-sequential-huhonghei",'sequential-trands-pane-huhonghei', me.HuhongheiSequentialPaneIndex, 6, 10);
                        me.isPaneFull = 1;
                    } else {
                        me.currentHuhongheiSequentialContainerColumn++;
                    }
                } else {
                    me.currentHuhongheiSequentialContainerRow++;
                }
            }
        },

        // 添加龙虎转弯走势项（专业版和简洁版的行数不同，可通过参数设定）
        addLonghuTurnoverItem: function(mainContainer, childContainer, paneIndex, record) {
            // 是否需要继续--前龙本龙，前虎本虎，本和（前龙则本龙和，前虎则本虎和，前龙和则本龙和，前虎和则本虎和），如果第一期就是和则龙和
            // 如果需要继续，当前位置应该是行数+1，列数不变，查看此处内容是否为空，如果为空则插入此处，如果不为空则当前位置应该为列数+1，行数为0.如果列数+1>最大列数，则清空后并从第一列第一行开始。
            // 还需判断往下还是往右排列，如果本列还有为空的行则往下，如果本列没有为空的行了则往右
            // 如果不需要继续，则本次的位置为列数+1，行数为0。。如果如果列数+1>最大列数，则清空后并从第一列第一行开始。
            var me = this,
                columnIndex = me.currentLonghuTurnoverContainerColumn + 1,
                rowIndex = me.currentLonghuTurnoverContainerRows + 1,
                currentColumnNextRowIndex = rowindex+1,
                currentRowNextColumnIndex = columnIndex+1,
                CLS = "";

            if(lastRecord && lastRecord !== "null" && lastRecord !== "undefined"){
                // 如果已经有开奖数据了
                var lastResult = Object.keys(lastRecord)[0],
                    currentResult = Object.keys(record)[0];

                if(lastResult == currentResult){
                    // 需要继续：前龙本龙，前虎本虎，前和本和
                    // 判断是否需要更换pane
                    var currentColumnNextRowContent = $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").html();
                    
                    if(currentColumnNextRowContent==""&&(me.currentSequentialContainerRow<me.sequentialContainerRows-1)){
                        // 如果此列下一行为空，且不是最后一行，则写入，
                        $(mainContainer).find(childContainer).last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append();

                    }else if(currentColumnNextRowContent!=""&&(me.currentSequentialContainerRow>=me.sequentialContainerRows-1)){
                    // }else if(){

                    // }else{

                    }


          

                }else if(lastResult!=currentResult&&currentResult=="he"){
                    // 需要继续：本和（前龙则本龙和，前虎则本虎和，前龙和则本龙和，前虎和则本虎和）
                    // 判断是否需要更换pane
                    

                }else if(lastResult!=currentResult&&currentResult!="he"){
                    // 不需要继续：看是否需要更换pane，如果不需要则更换列
                    
                }

                
            }else{
                // 如果是第一条数据
                var currentResult = Object.keys(record)[0];
                if(currentResult=="he"){

                }
                lastRecord = record;
            }
            $(mainContainer).find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").append("<div class='" + CLS + "'></div>");
        },


        getWinnerPuker: function(longData, huData) {
            if (Number(longData) < 10) {
                longData = "0" + Number(longData);
            }
            if (Number(huData) < 10) {
                huData = "0" + Number(huData);
            }
            var me = this,
                Long = me.puker[longData],
                Hu = me.puker[huData];
            if (Long['data'] > Hu['data']) {
                return {
                    'long': Long
                };
            } else if (Long['data'] < Hu['data']) {
                return {
                    'hu': Hu
                };
            } else {
                return {
                    'he': {}
                }
            }
        },

        getLongPuker:function(longData){
            if (Number(longData) < 10) {
                longData = "0" + Number(longData);
            }
            var me = this,
                Long = me.puker[longData];
            return {
                'long':Long
            }
            
        },

        getHuPuker:function(huData){
            if (Number(huData) < 10) {
                huData = "0" + Number(huData);
            }
            var me = this,
                Hu = me.puker[huData];
            return {
                'hu':Hu
            }
        },

        // 往容器中添加记录
        addRecord: function(record) {
            var me = this;

            record['nums'].sort(function(a, b) {
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
            $(me.records_container + " > li:first").removeClass('active');

            record_template = host.util.template(me.tpl_record, record);
            tmp = $(record_template);
            $(me.records_container).prepend(tmp);

            $(me.records_container + " > li:first").addClass('active');

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
    host.TableGame.LhdHistory = Main;

})(bomao, bomao.Event, jQuery);
