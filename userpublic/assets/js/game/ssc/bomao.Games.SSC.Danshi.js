(function(host, name, GameMethod, undefined) {
		var defConfig = {
				name: 'wuxing.zhixuan.danshi',
				//iframe编辑器
				editorobj: '.content-text-balls',
				//FILE上传按钮
				uploadButton: '#file',
				//单式导入号码示例
				exampleText: '12345 33456 87898 <br />12345 33456 87898 <br />12345 33456 87898 ',
				//玩法提示
				tips: '五星直选单式玩法提示',
				//选号实例
				exampleTip: '这是单式弹出层提示',
				//中文 全角符号  中文
				checkFont: /[\u4E00-\u9FA5]|[/\n]|[/W]/g,
				//过滤方法
				filtration: /[^\d]/g,
				//验证是否纯数字
				checkNum: /^[0-9]*$/,
				//单式玩法提示
				normalTips: ['说明：',
					'1、支持常见的各种单式格式，间隔符如： 换行符 回车 逗号 分号等',
					'2、上传文件后缀必须是.txt格式,最大支持10万注，并支持拖拽文件到文本框进行上传',
					'3、文件较大时会导致上传时间较长，请耐心等待！',
					'',
					'格式范例：12345 23456 88767 33021 98897 '
				].join('\n')

			},
			gameCaseName = 'SSC',
			Games = host.Games,
			//游戏类
			gameCase = host.Games[gameCaseName];

	//定义方法
	var pros = {
		init:function(cfg){
			var me = this;

			//IE Range对象
			me.ieRange = '';
			//正确结果
			me.vData = [];
			//所有结果
			me.aData = [];
			
			me.tData = [];
			//出错提示记录
			me.errorData = [];
			//重复记录
			me.sameData = [];
			//机选标记
			me.ranNumTag = false;
			//是否初次进行投注
			me.isFirstAdd = true;

			Games.getCurrentGameOrder().addEvent('beforeAdd', function(e, orderData){
				var that = this,
					data = me.tData,
					html = '';

				if(orderData['type'] == me.defConfig.name){
					
					//使用去重后正确号码进行投注
					if(me.isFirstAdd){
						if(!me['ranNumTag']){
							orderData['lotterys'] = [];
							me.isFirstAdd = null;
							//重新输出去重后号码
							me.updateData();
							Games.getCurrentGameOrder().add(Games.getCurrentGameStatistics().getResultData());
						}
					}else{
						//如果存在重复和错误号进行提示
						if(me.errorData.join('') != '' || me.sameData.join('') != ''){
							me.ballsErrorTip();
						}
						me.isFirstAdd = true;
					}
				}

			});
			



			
		},
		//启用textarea的单式输入方式，以支持十万级别的单式
		initTextarea:function(){
			var me = this,
				CLS = 'content-textarea-balls-def',
				cfg = me.defConfig,
				defText = $.trim(cfg.normalTips);
			me.importTextarea = $('<textarea class="content-textarea-balls '+CLS+'">'+defText+'</textarea>');
			me.container.find('.panel-select').html('').append(me.importTextarea);



			//绑定输入框事件
			me.importTextarea.focus(function(){
				var v = $.trim(this.value);
				if(v == defText){
					this.value = '';
					me.importTextarea.removeClass(CLS);
				}
			}).blur(function(){
				var v = $.trim(this.value);
				if(v == ''){
					me.removeOrderAll();
					me.showNormalTips();
				}
			}).keyup(function(){
				me.updateData();
			});



		},
		//废除使用iframe形式的单式
		initFrame:function(){
			var me = this;
			//由iframe模式改成textarea模式
			me.initTextarea();
			//文件上传事件
			me.bindPressTextarea();
			//拖拽上传
			me.dragUpload();

			/**
			me.win = me.container.find(me.defConfig.editorobj)[0].contentWindow;
			me.doc = me.win.document;
			
			me._bulidEditDom();

			//查看标准格式样本按钮
			var tip = host.Tip.getInstance();
			me.container.find('.balls-example-danshi-tip').click(function(e){
				e.preventDefault();
				var dom = $(this);
				tip.setText(me.getExampleText());
				tip.show(dom.outerWidth() + 10, 0, this);
			}).mouseout(function(){
				tip.hide();
			});
			**/
			
		},
		getExampleText:function(){
			return this.defConfig.exampleText;
		},
		rebuildData:function(){
			var me = this;
			me.balls = [
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1],
						[-1,-1,-1,-1,-1,-1,-1,-1,-1,-1]
						];
		},
		buildUI:function(){
			var me = this;
			me.container.html(me.getHTML());
		},
		//单式不能反选
		reSelect:function(){
			
		},
		//单式没有选球dom
		batchSetBallDom:function(){
			
		},
		//获取默认提示文案
		getNormalTips: function(){
			return this.defConfig.normalTips
		},
		//显示默认提示文案
		showNormalTips: function(){
			var me = this,
				CLS = 'content-textarea-balls-def';
			if(me.importTextarea){
				me.importTextarea.addClass(CLS);
			}
			me.replaceText(me.getNormalTips.call(me));
		},
		//建立可编辑的文字区域
		_bulidEditDom: function(){
			var me = this,
				headHTML =	'';

			me.doc.designMode = 'On';//可编辑
			me.doc.contentEditable = true;
			//但是IE与FireFox有点不同，为了兼容FireFox，所以必须创建一个新的document。
			me.doc.open();
			headHTML='<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			headHTML=headHTML+'<style>*{margin:0;padding:0;font-size:14px;}</style>';
			headHTML=headHTML+'</head>';
			me.doc.writeln('<html>'+headHTML+'<body style="word-break: break-all">' + me.getNormalTips() + '</body></html>');
			me.doc.close();
			// //FOCUS光标	
			// if(!document.all){
			// 	me.win.focus();
			// }else{
			// 	me.doc.body.focus();
			// }
			//绑定事件
			me.bindPress();
			//IE回车输出<br> 与 FF 统一；
			if(document.all){
				me.doc.onkeypress = function(){
					return me._ieEnter()
				};
			};
			
			me.dragUpload();
		},
		dragUpload:function(){
			var me = this,iframeBody = me.importTextarea;
			//拖拽上传
			if(window.FileReader){
				iframeBody.bind("dragover", function(e){
					e.preventDefault();
					e.stopPropagation();
				});
				iframeBody.get(0).addEventListener('drop', function(e){
					e.preventDefault();
					e.stopPropagation();
					var files = e.dataTransfer.files,file = files[0],
						reader = new FileReader(),
						fType = file.type ? file.type : 'n/a';
					
					if(fType != 'text/plain'){
						return;
					}
					
					reader.onload = function(e){
						var text = e.target.result;
						if($.trim(text) != ''){
							me.replaceText(text);
							me.updateData();
						}
					};
					reader.readAsText(file);
				},false);	
			}
		},
		
		//IE回车修改
		_ieEnter: function(){
			var me = this,
				e = me.win.event;
			if(e.keyCode == 13){
				this._saveRange();
				this._insert("<br/>");
				return false;
			}
		},
		//编辑器中插入文字
		_insert: function(text) {//插入替换字符串
			var me = this;
				
			if (!!me.ieRange) {
				me.ieRange.pasteHTML(text);
				me.ieRange.select();
				me.ieRange = false; //清空下range对象
			} else {//焦点不在html编辑器内容时
				me.win.focus();
				if (document.all) {
					me.doc.body.innerHTML += text; //IE插入在最后
				} else {//Firefox
					var sel = win.getSelection();
					var rng = sel.getRangeAt(0);
					var frg = rng.createContextualFragment(text);
					rng.insertNode(frg);
				}
			}
		},
		//IE下保存Range对象
		_saveRange: function(){
			if(!!document.all&&!me.ieRange){//是否IE并且判断是否保存过Range对象
				var sel = me.doc.selection;
				me.ieRange = sel.createRange();
				if(sel.type!='Control'){//选择的不是对象
					var p = me.ieRange.parentElement();//判断是否在编辑器内
					if(p.tagName=="INPUT"||p == document.body)me.ieRange=false;
				}
			}
		},
		//返回结果HTML
		getHtml: function(){
			var me = this,v = !!me.importTextarea ? me.importTextarea.val() : '',
				defText = $.trim(me.defConfig.normalTips);
			v = $.trim(v) == defText ? '' : v;
			return v;

			//由iframe模式改成textarea模式
			//return me.doc ? $(me.doc.body).html() : '';
		},
		//修改HTML
		//返回结果HTML
		replaceText: function(text){
			var me = this;
			if(me.importTextarea){
				me.importTextarea.val(text);
			}
		},
		bindPressTextarea:function(){
			var me = this,
				uploadButton = me.container.find(me.defConfig.uploadButton),
				agentValue = window.navigator.userAgent.toLowerCase();
			//绑定用户上传按钮
			uploadButton.bind('change', function(){
				var form = $(this).parent();
				me.checkFile(this, form);
			});
		},
		//用拆分符号拆分成单注
		iterator: function(data) {
			var me= this,
				cfg = me.defConfig,
				result = [];

			data = data.replace(cfg.filtration, ' ');
			data = data.replace(/\s+/g, ' ');
			data = $.trim(data);
			result = data.split(' ');

			return result;
		},
		//检测结果重复
		checkResult: function(data, array){
			//检查重复
			for (var i = array.length - 1; i >= 0; i--) {
				if(array[i].join('') == data){
					return false;
				}
			};
			return true;
		},
		//正则过滤输入框HTML
		//提取正确的投注号码
		filterLotters : function(data){
			var me = this,
				result = '';
			
			result = data.replace(/<br>+|&nbsp;+/gi, ' ');
			result = result.replace(/[\s]|[,]+|[;]+|[，]+|[；]+/gi, ' ');
			result = result.replace(/<(?:"[^"]*"|'[^']*'|[^>'"]*)+>/g, ' ');
			result = result.replace(me.defConfig.checkFont,'') +  ' ';
			
			return result;
		},
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum){
			var me = this;

			return lotteryNum.length == me.balls.length;
			/**
			return me.defConfig.checkNum.test(lotteryNum) && lotteryNum.length == me.balls.length;
			**/
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete:function(data){
			var me = this,
				len,
				i = 0,
				balls,
				has = {},
				result = [];

				me.aData = [];
				me.vData = [];
				me.sameData = [];
				me.errorData = [];
				me.tData = [];
			
			//按规则进行拆分结果
			result = me.iterator(data);
			len = result.length;

			for(i = 0; i < len; i++){
				balls = result[i].split('');
				//检测基本长度
				if(me.checkSingleNum(balls)){
					if(has[balls]){
						//重复
						me.sameData.push(balls);
					}else{
						me.tData.push(balls);
						has[balls] = true;
					}
				}else{
					me.errorData.push(balls);
				}
			}
			//校验
			if(me.tData.length > 0){
				me.isBallsComplete = true;
				return me.tData;
			}else{
				me.isBallsComplete = false;
				return [];
			}
		},
		//返回正确的索引
		countInstances: function(mainStr, subStr){
			var count = [];
			var offset = 0;
			do{
				offset = mainStr.indexOf(subStr, offset);
				if(offset != -1){
					count.push(offset);
					offset += subStr.length;
				}
			}while(offset != -1)
			return count;
		},
		//三项操作提示
		//显示正确项
		//排除错误项
		removeOrderError: function(){
			var me  = this,str = [],i = 0,len = me.tData.length;
			for(i = 0; i < len; i++){
				str[i] = me.tData[i].join('');
			}
			str = $.trim(str.join(' '));
			me.errorDataTips();
			me.replaceText(str);
			me.errorData = [];
			me.sameData = [];
			if(str == ''){
				me.showNormalTips();
			}
		},
		//排除重复项
		removeOrderSame: function(){
			var me  = this,str = [],i = 0,len = me.tData.length;
			for(i = 0; i < len; i++){
				str[i] = me.tData[i].join('');
			}
			str = $.trim(str.join(' '));
			me.sameDataTips();
			me.replaceText(str);
			me.errorData = [];
			me.sameData = [];
			if(str == ''){
				me.showNormalTips();
			}
		},
		//清空选区
		removeOrderAll: function(){
			var me=this;
			me.replaceText(' ');
			me.sameData = [];
			me.aData = [];
			me.tData = [];
			me.vData = [];
			//清空选号状态
			Games.getCurrentGameStatistics().reSet();
			me.showNormalTips();
		},
		//检测上传
		checkFile: function(dom, form){
			var result = dom.value,
				fileext=result.substring(result.lastIndexOf("."),result.length),
				fileext=fileext.toLowerCase();
			if (fileext != '.txt') {
				alert("对不起，导入数据格式必须是.txt格式文件，请您调整格式后重新上传，谢谢 ！");            
				return false;
			}
			form[0].submit();
		},
		//接收文件
		getFile: function(result){
			var me = this,
				resetDom = me.container.find(':reset');

				if(!result){return};
				me.replaceText(result);
				me.updateData();
				resetDom.click();
		},
		//出错提示
		//暂时搁置
		errorTip: function(html, data){
			var me = this,
				start, end,
				indexData = [];
			
			alert(me.errorData.join())
		},
		sameDataTips: function(){
			var me = this,
				sameData = me.sameData,
				sameDataHtmlText = '',
				sameGroupText = '',
				msg = Games.getCurrentGameMessage(),
				saveSameData = [],
				indexData = [];

			if(sameData.join('') == ''){return};
			
			
			for (var i = 0; i < sameData.length; i++) {
				if($.trim(sameData[i].join(''))){
					saveSameData.push(sameData[i].join(''));
				}
			};
			sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join(', ') + '</textarea>';

			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<div style="display:inline-block;*zoom:1;*display:inline;vertical-align:middle">' + sameDataHtmlText + '</div>',
								'</div>',
							'</div>'].join(''),
				closeIsShow: true,
				closeFun: function(){
					this.hide();
				}
			})
		},
		errorDataTips: function(){
			var me = this,
				errorData = me.errorData,
				errorDataHtmlText = '',
				errorGroupText = '',
				msg = Games.getCurrentGameMessage(),
				saveError = [],
				indexData = [];
			
			if(errorData.join('') == ''){return};

			for (var i = 0; i < errorData.length; i++) {
				if($.trim(errorData[i].join(''))){
					saveError.push(errorData[i].join(''));
				}
			};
			errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveError.join(', ') + '</textarea>';
			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<div style="display:inline-block;*zoom:1;*display:inline;vertical-align:middle">' + errorDataHtmlText + '</div>',
								'</div>',
							'</div>'].join(''),
				closeIsShow: true,
				closeFun: function(){
					this.hide();
				}
			})
		},
		//单式出错提示
		ballsErrorTip: function(html, data){
			var me = this,
				errorData = me.errorData,
				sameData = me.sameData,
				errorDataHtmlText = '',
				sameDataHtmlText = '',
				errorGroupText = '',
				sameGroupText = '',
				msg = Games.getCurrentGameMessage(),
				saveError = [],
				saveSameData = [],
				indexData = [];
		
			//重复号码
			if(sameData.join('') != ''){
				for (var i = 0; i < sameData.length; i++) {
					if($.trim(sameData[i].join(''))){
						saveSameData.push(sameData[i].join(''));
					}
				};
				sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join(', ') + '</textarea>';
			}
			//错误号码
			if(errorData.join('') != ''){
				for (var i = 0; i < errorData.length; i++) {
					if($.trim(errorData[i].join(''))){
						saveError.push(errorData[i].join(''));
					}
				};
				errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveError.join(', ') + '</textarea>';
			}

			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<div style="display:inline-block;*zoom:1;*display:inline;vertical-align:middle">' + sameDataHtmlText + errorDataHtmlText + '</div>',
								'</div>',
							'</div>'].join(''),
				closeIsShow: true,
				closeFun: function(){
					this.hide();
				}
			});
		},
		//复位
		//单式需提到子类方法实现
		reSet:function(){
			var me = this;
			me.isBallsComplete = false;
			me.rebuildData();
			me.updateData();
			if(!me.ranNumTag){
				me.showNormalTips();
			};
			//重置机选标记
			me.removeRanNumTag();
		},
		formatViewBalls: function(original) {
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result[i] = original[i].join('');
			}
			return result.join('|');
		},
		//生成后端参数格式
		makePostParameter: function(data, order){
			var me = this,
				result = [],
				data = order['lotterys'],
				len = data.length,
				i = 0;
			for (; i < len; i++) {
				result[i] = data[i].join('');
			}
			return result.join('|');
		},
		//获取组合结果
		getLottery:function(){
			var me = this, data = me.getHtml();
			if(data == ''){
				return [];
			}
			//返回投注
			return me.checkBallIsComplete(data);
		},
		//单组去重处理
		removeSameNum: function(data) {
			var i = 0, result, me = this,
				numLen = this.getBallData()[0].length;
				len = data.length;
			result = Math.floor(Math.random() * numLen);
			for(;i<data.length;i++){
				if(result == data[i]){
					return arguments.callee.call(me, data);
				}
			}
			return result;
		},
		//清空重复号码记录
		emptySameData: function(){
			this.sameData  = [];
		},
		//清空错误号码记录
		emptyErrorData: function(){
			this.errorData = [];
		},
		//增加单式机选标记
		addRanNumTag: function(){
			var me = this;
			me.ranNumTag = true;
			me.emptySameData();
			me.emptyErrorData();
		},
		getTdata : function(){
			return this.tData; 
		},
		getOriginal:function(){
			return this.getTdata();
		},
		//去除单式机选标记
		removeRanNumTag: function(){
			this.ranNumTag = false;
		},
		//限制随机投注重复
		checkRandomBets: function(hash,times){
			var me = this,
				allowTag = typeof hash == 'undefined' ? true : false,
				hash = hash || {},
				current = [],
				times = times || 0,
				len = me.getBallData().length,
				rowLen = me.getBallData()[0].length,
				order = Games.getCurrentGameOrder().getTotal()['orders'];

			//生成单数随机数
			current = me.createRandomNum(); 

			//如果大于限制数量
			//则直接输出
			if(Number(times) > Number(me.getRandomBetsNum())){
				return current;
			}

			//建立索引
			if(allowTag){
				for (var i = 0; i < order.length; i++) {
					if(order[i]['type'] == me.defConfig.name){
						var name = order[i]['original'].join('').replace(/,/g,'');
						hash[name] = name;
					}
				};
			}
			//对比结果
			if(hash[current.join('')]){
				times++;
				return arguments.callee.call(me, hash, times);
			}

			return current;
		},
		//生成一个当前玩法的随机投注号码
		//该处实现复式，子类中实现其他个性化玩法
		//返回值： 按照当前玩法生成一注标准的随机投注单(order)
		randomNum:function(){
			var me = this,
				i = 0, 
				current = [], 
				currentNum, 
				ranNum,
				order = null,
				dataNum = me.getBallData(),
				name = me.defConfig.name,
				name_en = Games.getCurrentGame().getCurrentGameMethod().getGameMethodName(),
				lotterys = [],
				original = [];
			
			//增加机选标记
			me.addRanNumTag();

			current  = me.checkRandomBets();
			original = current;
			lotterys = me.combination(original);
				
			//生成投注格式
			order = {
				'type':  name_en,
				'original':original,
				'lotterys':lotterys,
				'moneyUnit': Games.getCurrentGameStatistics().getMoneyUnit(),
				'multiple': Games.getCurrentGameStatistics().getMultip(),
				'onePrice': Games.getCurrentGame().getGameConfig().getInstance().getOnePrice(name_en),
				'num': lotterys.length
			};
			order['amountText'] = Games.getCurrentGameStatistics().formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
			return order;		
		},
		getHTML:function(){
			//html模板
			var iframeSrc = Games.getCurrentGame().getGameConfig().getInstance().getUploadPath();
			var token = Games.getCurrentGame().getGameConfig().getInstance().getToken();
			var html_all = [];
				html_all.push('<div class="balls-import clearfix">');
					html_all.push('<form id="form1" name="form1" enctype="multipart/form-data" method="post" action="'+ iframeSrc +'" target="check_file_frame" style="position:relative;padding-bottom:10px;">');
					html_all.push('<input name="betNumber" type="file" id="file" size="40" hidefocus="true" value="导入" style="outline:none;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);filter:alpha(opacity=0);opacity: 0;position:absolute;top:0px; left:0px; width:115px; height:30px;z-index:1;background:#000;cursor: pointer;" />');
					html_all.push('<input name="_token" type="hidden" value="'+ token +'" />');
					html_all.push('<input type="button" class="btn balls-import-input" style="cursor: pointer;" value="导入注单" onclick=document.getElementById("form1").file.click()>&nbsp;&nbsp;&nbsp;&nbsp;<a style="display:none;" class="balls-example-danshi-tip" href="#">查看标准格式样本</a>');
					html_all.push('<input type="reset" style="outline:none;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);filter:alpha(opacity=0);opacity: 0;width:0px; height:0px;z-index:1;background:#000" />');
					html_all.push('<iframe src="'+ iframeSrc +'" name="check_file_frame" style="display:none;"></iframe>');
					html_all.push('</form>');
					html_all.push('<div class="panel-select" ><iframe style="width:100%;height:100%;border:0 none;background-color:#F9F9F9;" class="content-text-balls"></iframe></div>');
					html_all.push('<div class="panel-btn">');
					html_all.push('<a class="btn remove-error" href="javascript:void(0);">清理错误与重复</a>');
					//html_all.push('<a class="btn remove-same" href="javascript:void(0);">删除重复项</a>');
					html_all.push('<a class="btn remove-all" href="javascript:void(0);">清空文本框</a>');
					html_all.push('</div>');
				html_all.push('</div>');
			return html_all.join('');
		}
	};


	
	var Main = host.Class(pros, GameMethod);
	Main.defConfig = defConfig;
	gameCase[name] = Main;
		
})(bomao, 'Danshi', bomao.GameMethod);

