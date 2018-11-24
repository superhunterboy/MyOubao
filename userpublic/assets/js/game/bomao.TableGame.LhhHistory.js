// 龙虎斗历史

(function() {

    // 共有三层
    // 1、简洁、专业版
    // 2、不同版本下的路数
    // 3、不同路数下的走势
    var defConfig = [{
        name_en: "normal",
        name_cn: "简洁版",
        tabs: [{
            name_cn: "龙虎路",
            name_en: "normal-longhu",
            type:"longhu",
            trands: [{
                name: 'normal-longhu-txt',
                rows: 6,
                columns: 13,
                type: 'txt',
                cell_size: "big",
            }, {
                name: 'normal-longhu-abs',
                rows: 6,
                columns: 13,
                type: 'abs',
                cell_size: "big",
            }]
        }, {
            name_cn: "龙单双",
            name_en: "normal-longdanshuang",
            type:"longdanshuang",
            trands: [{
                name: 'normal-longdanshuang-txt',
                rows: 6,
                columns: 13,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'normal-longdanshuang-abs',
                rows: 6,
                columns: 13,
                type: 'abs',
                cell_size: "big"
            }]
        }, {
            name_cn: "虎单双",
            name_en: "normal-hudanshuang",
            type:"hudanshuang",
            trands: [{
                name: "normal-hudanshuang-txt",
                rows: 6,
                columns: 13,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'normal-hudanshuang-abs',
                rows: 6,
                columns: 13,
                type: 'abs',
                cell_size: "big"
            }]
        }, {
            name_cn: "龙红黑",
            name_en: "normal-longhonghei",
            type:"longhonghei",
            trands: [{
                name: "normal-longhonghei-txt",
                rows: 6,
                columns: 13,
                type: 'txt',
                cell_size: "big"
            }, {
                name: "normal-longhonghei-abs",
                rows: 6,
                columns: 13,
                type: 'abs',
                cell_size: "big"
            }]
        }, {
            name_cn: "虎红黑",
            name_en: "normal-huhonghei",
            type:"huhonghei",
            trands: [{
                name: "normal-huhonghei-txt",
                rows: 6,
                columns: 13,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'normal-huhonghei-abs',
                rows: 6,
                columns: 13,
                type: 'abs',
                cell_size: "big"
            }]
        }]
    }, {
        name_en: "pro",
        name_cn: "专业版",
        tabs: [{
            name_cn: "龙虎路",
            name_en: "pro-longhu",
            type:"longhu",
            trands: [{
                name: 'pro-longhu-txt',
                rows: 6,
                columns: 10,
                type: 'txt',
                cell_size: "big",
            }, {
                name: 'pro-longhu-abs',
                rows: 6,
                type: 'abs',
                columns: 33,
                cell_size: "normal",
            }, {
                name: 'pro-longhu-da',
                rows: 6,
                columns: 66,
                type: 'da',
                cell_size: "normal",
            }, {
                name: 'pro-longhu-xiao',
                rows: 6,
                columns: 33,
                type: 'xiao',
                cell_size: "small",
            }, {
                name: 'pro-longhu-yue',
                rows: 6,
                columns: 33,
                type: 'yue',
                cell_size: "small",
            }]
        }, {
            name_cn: "龙单双",
            name_en: "pro-longdanshuang",
            type:"longdanshuang",
            trands: [{
                name: 'pro-longdanshuang-txt',
                rows: 6,
                columns: 10,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'pro-longdanshuang-abs',
                rows: 6,
                columns: 33,
                type: 'abs',
                cell_size: "normal"
            }, {
                name: 'pro-longdanshuang-da',
                rows: 6,
                columns: 66,
                type: 'da',
                cell_size: "normal",
            }, {
                name: 'pro-longdanshuang-xiao',
                rows: 6,
                columns: 33,
                type: 'xiao',
                cell_size: "small",
            }, {
                name: 'pro-longdanshuang-yue',
                rows: 6,
                columns: 33,
                type: 'yue',
                cell_size: "small",
            }]
        }, {
            name_cn: "虎单双",
            name_en: "pro-hudanshuang",
            type:"hudanshuang",
            trands: [{
                name: 'pro-hudanshuang-txt',
                rows: 6,
                columns: 10,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'pro-hudanshuang-abs',
                rows: 6,
                columns: 33,
                type: 'abs',
                cell_size: "normal"
            }, {
                name: 'pro-hudanshuang-da',
                rows: 6,
                columns: 66,
                type: 'da',
                cell_size: "normal",
            }, {
                name: 'pro-hudanshuang-xiao',
                rows: 6,
                columns: 33,
                type: 'xiao',
                cell_size: "small",
            }, {
                name: 'pro-hudanshuang-yue',
                rows: 6,
                columns: 33,
                type: 'yue',
                cell_size: "small",
            }]
        }, {
            name_cn: "龙红黑",
            name_en: "pro-longhonghei",
            type:"longhonghei",
            trands: [{
                name: 'pro-longhonghei-txt',
                rows: 6,
                columns: 10,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'pro-longhonghei-abs',
                rows: 6,
                columns: 33,
                type: 'abs',
                cell_size: "normal"
            }, {
                name: 'pro-longhonghei-da',
                rows: 6,
                columns: 66,
                type: 'da',
                cell_size: "normal",
            }, {
                name: 'pro-longhonghei-xiao',
                rows: 6,
                columns: 33,
                type: 'xiao',
                cell_size: "small",
            }, {
                name: 'pro-longhonghei-yue',
                rows: 6,
                columns: 33,
                type: 'yue',
                cell_size: "small",
            }]
        }, {
            name_cn: "虎红黑",
            name_en: "pro-huhonghei",
            type:"huhonghei",
            trands: [{
                name: 'pro-huhonghei-txt',
                rows: 6,
                columns: 10,
                type: 'txt',
                cell_size: "big"
            }, {
                name: 'pro-huhonghei-abs',
                rows: 6,
                columns: 33,
                type: 'abs',
                cell_size: "normal"
            }, {
                name: 'pro-huhonghei-da',
                rows: 6,
                columns: 66,
                type: 'da',
                cell_size: "normal",
            }, {
                name: 'pro-huhonghei-xiao',
                rows: 6,
                columns: 33,
                type: 'xiao',
                cell_size: "small",
            }, {
                name: 'pro-huhonghei-yue',
                rows: 6,
                columns: 33,
                type: 'yue',
                cell_size: "small",
            }]
        }]
    }];


    // 和后台协议的结果：52张牌的点数、红黑、单双情况
	var puker = {
		'01':{honghei:'hong',danshuang:'dan',data:1},
		'02':{honghei:'hong',danshuang:'shuang',data:2},
		'03':{honghei:'hong',danshuang:'dan',data:3},
		'04':{honghei:'hong',danshuang:'shuang',data:4},
		'05':{honghei:'hong',danshuang:'dan',data:5},
		'06':{honghei:'hong',danshuang:'shuang',data:6},
		'07':{honghei:'hong',danshuang:'dan',data:7},
		'08':{honghei:'hong',danshuang:'shuang',data:8},
		'09':{honghei:'hong',danshuang:'dan',data:9},
		'10':{honghei:'hong',danshuang:'shuang',data:10},
		'11':{honghei:'hong',danshuang:'dan',data:11},
		'12':{honghei:'hong',danshuang:'shuang',data:12},
		'13':{honghei:'hong',danshuang:'dan',data:13},

		'14':{honghei:'hong',danshuang:'dan',data:1},
		'15':{honghei:'hong',danshuang:'shuang',data:2},
		'16':{honghei:'hong',danshuang:'dan',data:3},
		'17':{honghei:'hong',danshuang:'shuang',data:4},
		'18':{honghei:'hong',danshuang:'dan',data:5},
		'19':{honghei:'hong',danshuang:'shuang',data:6},
		'20':{honghei:'hong',danshuang:'dan',data:7},
		'21':{honghei:'hong',danshuang:'shuang',data:8},
		'22':{honghei:'hong',danshuang:'dan',data:9},
		'23':{honghei:'hong',danshuang:'shuang',data:10},
		'24':{honghei:'hong',danshuang:'dan',data:11},
		'25':{honghei:'hong',danshuang:'shuang',data:12},
		'26':{honghei:'hong',danshuang:'dan',data:13},

		'27':{honghei:'hei',danshuang:'dan',data:1},
		'28':{honghei:'hei',danshuang:'shuang',data:2},
		'29':{honghei:'hei',danshuang:'dan',data:3},
		'30':{honghei:'hei',danshuang:'shuang',data:4},
		'31':{honghei:'hei',danshuang:'dan',data:5},
		'32':{honghei:'hei',danshuang:'shuang',data:6},
		'33':{honghei:'hei',danshuang:'dan',data:7},
		'34':{honghei:'hei',danshuang:'shuang',data:8},
		'35':{honghei:'hei',danshuang:'dan',data:9},
		'36':{honghei:'hei',danshuang:'shuang',data:10},
		'37':{honghei:'hei',danshuang:'dan',data:11},
		'38':{honghei:'hei',danshuang:'shuang',data:12},
		'39':{honghei:'hei',danshuang:'dan',data:13},

		'40':{honghei:'hei',danshuang:'dan',data:1},
		'41':{honghei:'hei',danshuang:'shuang',data:2},
		'42':{honghei:'hei',danshuang:'dan',data:3},
		'43':{honghei:'hei',danshuang:'shuang',data:4},
		'44':{honghei:'hei',danshuang:'dan',data:5},
		'45':{honghei:'hei',danshuang:'shuang',data:6},
		'46':{honghei:'hei',danshuang:'dan',data:7},
		'47':{honghei:'hei',danshuang:'shuang',data:8},
		'48':{honghei:'hei',danshuang:'dan',data:9},
		'49':{honghei:'hei',danshuang:'shuang',data:10},
		'50':{honghei:'hei',danshuang:'dan',data:11},
		'51':{honghei:'hei',danshuang:'shuang',data:12},
		'52':{honghei:'hei',danshuang:'dan',data:13}
	};




    // 根据配置文件生成Trands
    var createTrand = function(cfg) {
        var bigTabs = [];


        for (var v = 0; v < cfg.length; v++) {

            var bigTabConfig = cfg[v];

            var bigTab = new bomao.TableGame.BigTrandTab();

            bigTab.name = bigTabConfig.name_en;

            var smallTabsConfig = cfg[v].tabs;


            var smallTabs = [];

            for (var t = 0; t < smallTabsConfig.length; t++) {

                var smallTabConfig = smallTabsConfig[t];

                var smallTab = new bomao.TableGame.SmallTrandTab();

                smallTab.name = smallTabConfig.name_en;
                smallTab.type = smallTabConfig.type;

                var trandsConfig = smallTabConfig.trands;

                var trands = [];

                for (var o = 0; o < trandsConfig.length; o++) {

                    var trandConfig = trandsConfig[o];

                    if (trandConfig.type == "txt") {
                        var trand = new bomao.TableGame.SequentialTrand({
                            columns: trandConfig.columns,
                            rows: trandConfig.rows
                        });
                        trand.name = trandConfig.name;
                        trand.type = trandConfig.type;

                        // 添加新的面板之前
                        trand.addEvent("addPanel_before", function(e,record) {
                           	bigTab.fireEvent("addPanel_before",e,record);
                        });

                        // 添加新的面板之后
                        trand.addEvent("createPanel_after",function(e,panel){
                        	// 创建新的容器
                        	// 并将其他panel的容器隐藏
                        });

                        trand.addEvent("addRecord_after", function(e,record) {
                            // 将这个record对应标识渲染到前端页面
                        });
                        trand.createPanel();
                    }else{
                    	// 有可能是
                        var trand = new bomao.TableGame.TurnTrand({
                            columns: trandConfig.columns,
                            rows: trandConfig.rows
                        });

                        trand.name=trandConfig.name;
                        trand.type=trandConfig.type;
						trand.addEvent("addPanel_before", function(e,record) {
                           	bigTab.fireEvent("addPanel_before",record);
                        });
                        trand.addEvent("createPanel_after",function(e,panel){
                        	// 创建新的容器
                        	// 并将其他panel的容器隐藏
                        	
                        });
                        trand.addEvent("addRecord_after", function(e,record) {
                            // 将这个record对应标识渲染到前端页面
                        });
                        trand.createPanel();
                    }
                    trands.push(trand);
                }
                smallTab.trands = trands;
                smallTabs.push(smallTab);
            }

            bigTab.smallTabs = smallTabs;

            bigTab.addEvent("addPanel_before",function(e,record){
            	var bTab = this,
            		smTabs = bTab.smallTabs,
            		smTabsLen = smTabs.length,
            		ts = [];

        		for(var a = 0;a< smTabsLen; a++){
        			ts = smTabs[smTabsLen].trands

        			var tsLen = ts.length;
        			for(var b = 0;b<tsLen;b++){
        				this.createPanel();
        				ts[b].addRecord(record);
        			}
        		}
            })

            bigTabs.push(bigTab);
        }
        return bigTabs;
    };

    var getlonghuTxtRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData],
            Hu = puker[huData];

        if (Long['data'] > Hu['data']) {
            return 'long-txt';
        } else if (Long['data'] < Hu['data']) {
            return 'hu-txt';
        } else {
            return 'he-txt';
        }
    };

    var getlonghuAbsRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData],
            Hu = puker[huData];

        if (Long['data'] > Hu['data']) {
            return 'long-abs';
        } else if (Long['data'] < Hu['data']) {
            return 'hu-abs';
        } else {
            return 'he-abs';
        }
    }

    // TODO:
    var getlonghuDaRecord = function(code,panel){

    }
    var getlonghuXiaoRecord = function(code,panel){

    }
    var getlonghuYueRecord = function(code,panel){

    }

    var getlongdanshuangTxtRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            // huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData];
            // Hu = puker[huData];
        return Long["danshuang"]=="dan"?"longdan-txt":"longshuang-txt";
    }

    var getlongdanshuangAbsRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            // huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData];
            // Hu = puker[huData];
        return Long["danshuang"]=="dan"?"longdan-abs":"longshuang-abs";
    }

    var gethudanshuangTxtRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            // longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            // Long = puker[longData];
            Hu = puker[huData];
        return Hu["danshuang"]=="dan"?"hudan-txt":"hushuang-txt";
    }

    var gethudanshuangAbsRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            // longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            // Long = puker[longData];
            Hu = puker[huData];
        return Hu["danshuang"]=="dan"?"hudan-abs":"hushuang-abs";
    }

    var getlonghongheiTxtRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            // huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData];
            // Hu = puker[huData];
        return Long["honghei"]=="hong"?"longdhong-txt":"longhei-txt";
    }

    var getlonghongheiAbsRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            // huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            Long = puker[longData];
            // Hu = puker[huData];
        return Long["honghei"]=="hong"?"longhong-abs":"longhei-abs";
    }


    var gethuhongheiTxtRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            // longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            // Long = puker[longData];
            Hu = puker[huData];
        return Hu["honghei"]=="hong"?"huhong-txt":"huhei-txt";
    }

    var gethuhongheiAbsRecord = function(code){
    	var me = this,
    		data = code.split(" "),
            // longData =  Number(data[0])<10?"0"+Number(data[0]):data[0],
            huData = Number(data[1])<10?"0"+Number(data[1]):data[1],
            // Long = puker[longData];
            Hu = puker[huData];
       	return Hu["honghei"]=="hong"?"huhong-abs":"huhei-abs";
    }

    var bigTabs = createTrand(defConfig);

  
    var records = [{code:"01 03"}];

    var recordsLength = records.length;

    // 生成record添加进入panel中，根据panel中的值渲染页面。

    var longhuTxtRecord = "",
        longhuAbsRecord = "",

        longdanshuangTxtRecord = "",
        longdanshuangAbsRecord = "",

        hudanshuangTxtRecord = "",
        hudanshuangAbsRecord = "",

        longhongheiTxtRecord = "",
        longhongheiAbsRecord = "",

        huhongheiTxtRecord = "",
        huhongheiAbsRecord = "";

    for(var x = 0;x < recordsLength; x++){

    	longhuTxtRecord = getlonghuTxtRecord(records[x].code);
    	longhuAbsRecord = getlonghuAbsRecord(records[x].code);
    	longhuDaRecord = getlonghuDaRecord(records[x].code);
    	longdanshuangTxtRecord = getlongdanshuangTxtRecord(records[x].code);
    	longdanshuangAbsRecord = getlongdanshuangAbsRecord(records[x].code);
    	hudanshuangTxtRecord = gethudanshuangTxtRecord(records[x].code);
    	hudanshuangAbsRecord = gethudanshuangAbsRecord(records[x].code);
    	longhongheiTxtRecord = getlonghongheiTxtRecord(records[x].code);
    	longhongheiAbsRecord = getlonghongheiAbsRecord(records[x].code);
    	huhongheiTxtRecord = gethuhongheiTxtRecord(records[x].code);
    	huhongheiAbsRecord = gethuhongheiAbsRecord(records[x].code);




    	var bigTabsLength = bigTabs.length;
    	for(var i = 0; i < bigTabsLength; i++){
    		var bigTab = bigTabs[i];
    		var smallTabs = bigTab.smallTabs;
    		var smallTabsLength = smallTabs.length;

    		for(var j = 0;j < smallTabsLength; j++){
    			var trands = smallTabs[j].trands;
    			var trandsLength = smallTabs[j].trands.length;
    			
    			switch(smallTabs[j].type){
    				case "longhu":
    					for(var y=0;y<trandsLength;y++){
		    				switch(trands[y].type){
		    					case 'txt':
		    						trands[y].addRecord(longhuTxtRecord);
		    						break;
		    					case 'abs':	
		    						trands[y].addRecord(longhuAbsRecord);
		    						break;
		    					case 'da':
		    						break;
		    					case 'xiao':
		    						break;
		    					case 'yue':
		    						break;
		    				}
    					}
    					break;
    				case "longdanshuang":
    					for(var y=0;y<trandsLength;y++){
		    				switch(trands[y].type){
		    					case 'txt':
		    						trands[y].addRecord(longdanshuangTxtRecord);
		    						break;
		    					case 'abs':	
		    						trands[y].addRecord(longdanshuangAbsRecord);
		    						break;
		    					case 'da':
		    						break;
		    					case 'xiao':
		    						break;
		    					case 'yue':
		    						break;
		    				}
    					}
    					break;
    				case "hudanshuang":
    					for(var y=0;y<trandsLength;y++){
		    				switch(trands[y].type){
		    					case 'txt':
		    						trands[y].addRecord(hudanshuangTxtRecord);
		    						break;
		    					case 'abs':	
		    						trands[y].addRecord(hudanshuangAbsRecord);
		    						break;
		    					case 'da':
		    						break;
		    					case 'xiao':
		    						break;
		    					case 'yue':
		    						break;
		    				}
    					}
    					break;
    				case "longhonghei":
    				    for(var y=0;y<trandsLength;y++){
		    				switch(trands[y].type){
		    					case 'txt':
		    						trands[y].addRecord(longhongheiTxtRecord);
		    						break;
		    					case 'abs':	
		    						trands[y].addRecord(longhongheiAbsRecord);
		    						break;
		    					case 'da':
		    						break;
		    					case 'xiao':
		    						break;
		    					case 'yue':
		    						break;
		    				}
    					}
    					break;
    				case "huhonghei":
    				    for(var y=0;y<trandsLength;y++){
		    				switch(trands[y].type){
		    					case 'txt':
		    						trands[y].addRecord(huhongheiTxtRecord);
		    						break;
		    					case 'abs':	
		    						trands[y].addRecord(huhongheiAbsRecord);
		    						break;
		    					case 'da':
		    						break;
		    					case 'xiao':
		    						break;
		    					case 'yue':
		    						break;
		    				}
    					}
    					break;
    			}


    		}
    	}
    }






   




})()
