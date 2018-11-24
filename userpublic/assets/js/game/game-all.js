

//Games
(function(host, name, undefined){
	
	var Main = {};
		//缓存
		Main.cacheData = {};
	
		//当前游戏
		Main.currentGame = null;
		//玩法切换
		Main.currentGameTypes = null;
		//当前统计
		Main.currentGameStatistics = null;
		//当前号码篮
		Main.currentGameOrder = null;
		//当前追号
		Main.currentGameTrace = null;
		//投注按钮
		Main.currentGameSubmit = null;
		//当前游戏消息类
		Main.currentGameMessage = null;
		
		Main.userAccountDom = $('#J-top-user-balance');
		
		//当前游戏
		Main.getCurrentGame = function(){
			return Main.currentGame;
		};
		Main.setCurrentGame = function(game){
			Main.currentGame = game;
		};
		
		//玩法切换
		Main.getCurrentGameTypes = function(){
			return Main.currentGameTypes;
		};
		Main.setCurrentGameTypes = function(currentGameTypes){
			Main.currentGameTypes = currentGameTypes;
		};
		
		//选号状态
		Main.getCurrentGameStatistics = function(){
			return Main.currentGameStatistics;
		};
		Main.setCurrentGameStatistics = function(gameStatistics){
			Main.currentGameStatistics = gameStatistics;
		};
		
		//号码篮
		Main.getCurrentGameOrder = function(){
			return Main.currentGameOrder;
		};
		Main.setCurrentGameOrder = function(currentGameOrder){
			Main.currentGameOrder = currentGameOrder;
		};
		
		//追号
		Main.getCurrentGameTrace = function(){
			return Main.currentGameTrace;
		};
		Main.setCurrentGameTrace = function(currentGameTrace){
			Main.currentGameTrace = currentGameTrace;
		};

		//投注提交
		Main.getCurrentGameSubmit = function(){
			return Main.currentGameSubmit;
		};
		Main.setCurrentGameSubmit = function(currentGameSubmit){
			Main.currentGameSubmit = currentGameSubmit;
		};

		//消息提示
		Main.getCurrentGameMessage = function(){
			return Main.currentGameMessage;
		};
		Main.setCurrentGameMessage = function(currentGameMessage){
			Main.currentGameMessage = currentGameMessage;
		};
		
		//更新账户余额
		Main.updateUserAccount = function(userbalance){
			Main.userAccountDom.text(bomao.util.formatMoney(userbalance));
		}
		
	host[name] = Main;

})(bomao, "Games");










//游戏类
//所有游戏应继承该类
(function(host, name, Event, undefined){
	var defConfig = {
		id:-1,
		//游戏名称
		name:'',
		//文件名前缀
		jsNameSpace:'bomao.Games.SSC.',
		//添加事件代理的主面板
		eventProxyPanel:'body'
	},
	Games = host.Games;
	//将来仿url类型的参数转换为{}对象格式，如 q=wahaha&key=323444 转换为 {q:'wahaha',key:'323444'}
	//所有参数类型均为字符串
	var formatParam = function(param){
		var arr = $.trim(param).split('&'),i = 0,len = arr.length,
			paramArr = [],
			result = {};
		for(;i < len;i++){
			paramArr = arr[i].split('=');
			if(paramArr.length > 0){
				if(paramArr.length == 2){
					result[paramArr[0]] = paramArr[1];
				}else{
					result[paramArr[0]] = '';
				}
			}
		}
		return result;
	};



	
	var pros = {
		init:function(cfg){
			var me = this;
			me.setName(cfg.name);
			//设置当前游戏
			Games.setCurrentGame(me);
			
			me.setJsNameSpace(cfg.jsNameSpace);
			
			//资源加载缓存
			me.loadedHas = {};
			//当前使用的玩法
			me.currentGameMethod = null;
			
			me.addEvent('afterSwitchGameMethod', function(){
				Games.getCurrentGame().getCurrentGameMethod().reSet();
				
				//切换玩法时，针对当前玩法进行倍数限制设置
				var methodId = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Games.getCurrentGameStatistics().getMoneyUnit(),
					maxmultiple = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(methodId, unit),
					multiple = Games.getCurrentGameStatistics().getMultip();
				multiple = multiple > maxmultiple ? maxmultiple : multiple;
				Games.getCurrentGameStatistics().setMultipleDom(multiple);
				//console.log(maxmultiple);
				/**
				//切换后获取对应的走势图
				Games.getCurrentGame().getCurrentGameMethod().updataGamesInfo();
				**/
				
			});
		},
		getId:function(){
			return this.id;
		},
		setId:function(id){
			this.id = id;
		},
		//从服务器端获取数据
		//返回数据格式
		//{"isSuccess":1,"type":"消息代号","msg":"返回的文本消息","data":{xxx:xxx}}
		getServerDynamicConfig:function(callback, callbackError){
			var me = this,cfg = Games.getCurrentGame().getGameConfig().getInstance();
			/**
			//test
			var currentNumber = Number(cfg.getCurrentGameNumber()) + 1;
			var nowTime = (new Date()).getTime()/1000;
			var data = {currentNumber:'' + currentNumber, currentNumberTime:nowTime + (60 * 10), currentTime:nowTime, lotteryBalls:'' + Math.floor(Math.random()*(99999-11111))+11111, lastNumber:'140804064', gameNumbers:[]};
			//需要补全的属性有 lotteryBalls  lastNumber 
			me.setDynamicConfig(data);
			return;
			**/
			
			$.ajax({
				url:cfg.getUpdateUrl(),
				dataType:'JSON',
				success:function(data){
					if(data['type'] == 'loginTimeout'){
						var msgwd = Games.getCurrentGameMessage();
						msgwd.hide();
						msgwd.show({
							mask:true,
							confirmIsShow:true,
							confirmText:'关 闭',
							confirmFun:function(){
								location.href = "/";
							},
							closeFun:function(){
								location.href = "/";
							},
							content:'<div class="pop-waring"><i class="ico-waring"></i><h4 class="pop-text">登录超时，请重新登录平台！</h4></div>'
						});
						return false;
					}
					

					if(Number(data['isSuccess']) == 1){
						me.setDynamicConfig(data['data']);
						if($.isFunction(callback)){
							callback.call(me, data['data']);
						}
					}else{
						if($.isFunction(callbackError)){
							callbackError.call(me, data);
						}
					}
					
				}
			});
		},
		//需要更新的数据{currentNumber:当前期号, currentNumberTime:本期开奖时间,currentTime: 当前时间, lotteryBalls:上期开奖号码, lastNumber:上期期号, gameNumbers:期号列表}
		setDynamicConfig:function(cfg){
			Games.getCurrentGame().getGameConfig().getInstance().updateConfig(cfg);
			this.fireEvent('changeDynamicConfig', cfg);
		},
		//事件代理，默认只监听鼠标点击事件，如需要监听其他事件，请在具体的游戏类中实现
		//例： <span data-param="action=doSelect&value=10">点击</span>
		eventProxy:function(){
			var me = this,cfg = me.defConfig,panel = $(cfg.eventProxyPanel),
				action = '';
			panel.click(function(e){
				var q = e.target.getAttribute('data-param'),param,gameMethod;
				if(q && $.trim(q) != ''){
					e.preventDefault();
					param = formatParam(q);
					gameMethod = me.getCurrentGameMethod();
					if(gameMethod){
						gameMethod.exeEvent(param, e.target);
					}
				}
			});
		},
		setLoadedHas:function(key, value){
			this.loadedHas[key] = value;
		},
		//获取玩法的实例对象
		getCacheMethod:function(id){
			var me = this,has = me.loadedHas,fullname_en = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullNameById(id).join('.');
			if(has.hasOwnProperty(fullname_en)){
				return has[fullname_en];
			}
		},
		//切换游戏玩法
		switchGameMethod:function(id){
			var me = this,
				id = Number(id),
				has = me.loadedHas,
				obj,
				fullname_en = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullNameById(id).join('.');
			//当前游戏即为将要切换的游戏则无需执行
			if(me.ccurrentGameMethod && me.currentGameMethod.getId() == id){
				return;
			}
			if(has[fullname_en]){
				obj = has[fullname_en];
				me.fireEvent('beforeSwitchGameMethod', id);
				if(me.currentGameMethod){
					me.currentGameMethod.hide();
				}
				me.currentGameMethod = obj;
				obj.setId(id);
				obj.show();
				me.fireEvent('afterSwitchGameMethod', id);
			}else{
				me.fireEvent('beforeSetup', id);
				me.setup(id, function(){
					me.fireEvent('beforeSwitchGameMethod', id);
					if(me.currentGameMethod){
						me.currentGameMethod.hide();
					}
					obj = me.getCacheMethod(id);
					obj.setId(id);
					obj.show();
					me.currentGameMethod = obj;
					me.fireEvent('afterSetup');
					me.fireEvent('afterSwitchGameMethod', id);
				});
			}
			
		},
		getCurrentGameMethod:function(){
			return this.currentGameMethod;
		},
		//id 玩法id
		setup:function(id, callback){
			var me = this,
				path = me.buildPath(id),
				fn = function(){},
				_callback;
			//获取最后一个参数作为回调函数
			_callback = arguments.length > 0 ? arguments[arguments.length - 1] : fn;
			if(!$.isFunction(_callback)){
				_callback = fn;
			}
			//加载脚本并缓存
			if(!me.isSetuped(id)){
				$.ajax({
					url:path,
					cache:true,
					dataType:'script',
					success:function(){
						me.loadedHas[path] = true;
						_callback.call(me);
					},
					error:function(xhr, type){
						alert('资源加载失败\n' + path + '\n错误类型：' + type);
					}
				});
			}
		},
		//拼接路径
		buildPath:function(id){
			var me = this,
				Cfg = Games.getCurrentGame().getGameConfig().getInstance(),
				path = Cfg.getJsPath(),
				name = Cfg.getMethodFullNameById(id).join('.'),
				nameSpace = me.getJsNamespace(),
				//拼接名称为路径，并剔除空参数(空参数为了适应没有三级分组的游戏)
 
				src = path + nameSpace + name + Cfg.getJsSuffix();
 
			return src;
		},
		//检测某模块是否已安装
		isSetuped:function(id){
			var me = this,has = me.loadedHas,path = me.buildPath(id);
			return has.hasOwnProperty(path);
		},
		//直接设置某资源已经加载
		setSetuped:function(type, group, method){
			
		},
		setJsNameSpace:function(nameSpace){
			this.jsNameSpace = nameSpace;
		},
		getJsNamespace:function(){
			return this.jsNameSpace;
		},
		//返回该游戏的游戏配置
		//在子类中实现
		getGameConfig:function(){
		},
		getName:function(){
			return this.name;
		},
		setName:function(name){
			this.name = name
		},
		//对最后即将进行提交的数据进行处理
		//调用对应玩法的editSubmitData对将要提交的注单信息进行修改
		editSubmitData:function(data){
			var me = this,balls = data['balls'],it,method;
			$.each(balls, function(){
				it = this;
				method = me.getCacheMethod(it['wayId']);
				if(method){
					method.editSubmitData(it);
					it['viewBalls'] = '';
				}else{
					//如果遇到未知的玩法，则清空注单，本次提交失败
					alert('当前玩法文件未加载:' + it['type']);
					data['balls'] = [];
				}
			});
			//console.log(data);
			//return;
			//data['balls'] = balls;
			data['balls'] = encrypt(JSON.stringify(balls));
			data['is_encoded'] = 1;

			
			data['_token'] = Games.getCurrentGame().getGameConfig().getInstance().getToken();
			return data;
		}
		
	};
	
	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
	host[name] = Main;
	
})(bomao, "Game", bomao.Event);











//游戏方法类
//所有具体游戏实现应继承该类
(function(host, name, Event, undefined) {
	var defConfig = {
			id: -1,
			//如：'wuxing.zhixuan.fushi'
			name: '',
			//父容器
			UIContainer: '#J-balls-main-panel',
			//球dom元素选择器
			ballsDom: '.ball-number',
			//选球高亮class
			ballCurrentCls: 'ball-number-current',
			//玩法提示信息
			methodMassageDom: '.prompt .method-tip',
			//玩法提示信息
			methodExampleDom: '.prompt .example-tip',
			//限制选求重复次数
			randomBetsNum: 500
		},
		Games = host.Games;

	var pros = {
		init: function(cfg) {
			var me = this;

			me.id = cfg.id;
			me.name = cfg.name;

			//父容器
			me.UIContainer = $(cfg.UIContainer);
			//自身容器
			me.container = $('<div></div>').appendTo(me.UIContainer);
			me.buildUI();

			me.hide();

			//初始化数据结构
			me.balls = [];
			me.rebuildData();

			//所有选球dom
			me.ballsDom = me.getBallsDom();
			//当前选球是否完整
			me.isBallsComplete = false;

			//由于玩法是异步延后加载并实例化，所以与其他组件的结合不能提取到外部
			//选球数据更改后触发动作
			me.addEvent('updateData', function(e, data) {
				//更新统计
				var me = this,
					data = me.isBallsComplete ? data : {
						'lotterys': [],
						'original': []
					};
				Games.getCurrentGameStatistics().updateData(data, me.getName());
				//更新选球界面
				me.batchSetBallDom();
			});

			//面板复位时执行批量选求状态清空
			me.addEvent('afterReset', function() {
				me.exeEvent_cancelCurrentButton();
			});

			//选球动作结束执行批量选求状态清空
			me.addEvent('afterSetBallData', function(e, x, y, v) {
				me.exeEvent_cancelCurrentButton(x, y, v);
			});
			
		},
		getId: function() {
			return this.id;
		},
		setId: function(id) {
			this.id = Number(id);
		},
		//获取选球dom元素，保存结构和选球数据(me.balls)一致
		getBallsDom: function() {
			var me = this,
				cfg = me.defConfig,
				dataMode = me.balls;
			if (dataMode.length < 1) {
				return [];
			}
			return me.ballsDom || (function() {
				var balls = me.container.find(cfg.ballsDom),
					len,
					num = 0,
					i = 0,
					row,
					result = [],
					it;
				$.each(dataMode, function(i) {
					row = this;
					result[i] = [];
					$.each(row, function(j) {
						result[i][j] = balls[num];
						num++;
					});
				});
				return result;
			})();
		},
		//游戏类型切换后
		//游戏相关信息的更新方法
		updataGamesInfo: function() {
			var me = this,
				type = me.getGameMethodName(),
				currentGame = Games.getCurrentGame(),
				freCacheName = type + 'lostcurrentFre',
				//url = ctx + '/gameBet/historyballs?type=' + type + '&extent=currentFre&line=5&lenth=30';
				url = 'simulatedata/getBetAward.php?type=' + type + '&extent=currentFre&line=5&lenth=30&lotteryid=99101&userid=31';

			if (!Games.cacheData['gameBonus']) {
				Games.cacheData['gameBonus'] = {};
			}
			if (!Games.cacheData['gameTips']) {
				Games.cacheData['gameTips'] = {};
			}
			if (!Games.cacheData['frequency']) {
				Games.cacheData['frequency'] = {};
			}

			//奖金组
			if (Games.cacheData['gameBonus'][url]) {
				currentGame.addDynamicBonus(type, Games.cacheData['gameBonus'][url]);
			}
			if (Games.cacheData['gameTips'][url]) {
				me.methodTip(Games.cacheData['gameTips'][url]);
			}
			//冷热号缓存
			//缓存名称必须和手动加载的一致
			if (Games.cacheData['frequency'][freCacheName]) {
				me.getHotCold(type, 'currentFre', 'lost');
			}
			//验证缓存
			//禁止异步请求数据
			if (Games.cacheData['gameBonus'][url] && Games.cacheData['frequency'][freCacheName] && Games.cacheData['gameTips'][url]) {
				return
			};
			//获取游戏相关数据
			$.ajax({
				url: url,
				dataType: 'json',
				success: function(result) {
					if (Number(result['isSuccess']) == 1) {
						data = result['data'];

						//游戏玩法提示
						if (typeof data['gameTips'] != 'undefined') {
							Games.cacheData['gameTips'][url] = data.gameTips;
							me.methodTip(data.gameTips);
						}
						//冷热号
						if (typeof data['frequency'] != 'undefined') {
							Games.cacheData['frequency'][freCacheName] = data['frequency'];
							me.getHotCold(type, 'currentFre', 'lost');
						}
						//奖金组
						if (typeof data['bonus'] != 'undefined') {
							Games.cacheData['gameBonus'][url] = data['bonus'];
							currentGame.addDynamicBonus(type, data['bonus']);
						}
					} else {

					}
				}
			})
		},
		//修改玩法提示方法
		methodTip: function(data) {
			var me = this,
				cfg = me.defConfig;
			//玩法提示
			$(cfg.methodMassageDom).html(data.tips);
			//玩法实例
			$(cfg.methodExampleDom).html(data.example);
		},
		//format balls for view
		formatViewBalls: function(original) {
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result = result.concat(original[i].join(''));
			}
			return result.join('|');
		},
		//生成原始选球数据(不拆分成单注)
		//返回字符串形式的原始选球数字
		//在子类中实现/覆盖
		makePostParameter: function(original) {
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result = result.concat(original[i].join(''));
			}
			return result.join('|');
		},
		//检查数组存在某数
		arrIndexOf: function(value, arr) {
			var r = 0;
			for (var s = 0; s < arr.length; s++) {
				if (arr[s] == value) {
					r += 1;
				}
			}
			return r || -1;
		},
		//重新构建选球数据
		//在子类中实现
		rebuildData: function() {

		},
		getBallData: function() {
			return this.balls;
		},
		//设置选球数据
		//x y value   x y 为选球数据二维数组的坐标 value 为-1 或1
		setBallData: function(x, y, value) {
			var me = this,
				data = me.getBallData();
			me.fireEvent('beforeSetBallData', x, y, value);
			if (x >= 0 && x < data.length && y >= 0) {
				data[x][y] = value;
			}
			me.fireEvent('afterSetBallData', x, y, value);
		},
		//设置遗漏冷热辅助
		//x y value   x y 为选球数据二维数组的坐标 value 为-1 或1
		//classname为冷热选球所需要的高亮效果
		setBallAidData: function(x, y, value, className) {
			var me = this,
				currentName = 'ball-aid',
				data = me.getBallsAidDom(),
				className = className ? currentName + ' ' + className : currentName;
			if (x >= 0 && x < data.length && y >= 0 && y < data[0].length) {
				data[x][y].innerHTML = value;
				data[x][y].className = className;
			}
		},
		//复位
		reSet: function() {
			var me = this;
			me.isBallsComplete = false;
			me.rebuildData();
			me.updateData();
			me.fireEvent('afterReset');
		},
		//获取该玩法的名称
		getName: function() {
			return this.name;
		},
		setName: function(name) {
			this.name = name;
		},
		//显示该游戏玩法
		show: function() {
			var me = this;
			me.fireEvent('beforeShow');
			me.container.show();
			me.fireEvent('afterShow');
		},
		//隐藏该游戏玩法
		hide: function() {
			var me = this;
			me.fireEvent('beforeHide');
			me.container.hide();
			me.fireEvent('afterHide');
		},
		//实现事件
		exeEvent: function(param, target) {
			var me = this;
			if ($.isFunction(me['exeEvent_' + param['action']])) {
				me['exeEvent_' + param['action']].call(me, param, target);
			}
		},
		//批量选球事件
		exeEvent_batchSetBall: function(param, target) {
			var me = this,
				ballsData = me.balls,
				x = Number(param['row']),
				y = Number(param['cell']),
				bound = param['bound'],
				row = ballsData[x],
				i = 0,
				len = isNaN(y) ? row.length : 0,
				len2 = ballsData.length,
				makearr = [],
				start = (typeof param['start'] == 'undefined') ? 0 : Number(param['start']);
			halfLen = Math.ceil((len - start) / 2 + start),
			dom = $(target),
			i = start;


			//快三辅助选球特殊判断
			if(bound == 'all' && dom.parent().hasClass('current')){
				bound = 'none';
				dom.parent().removeClass('current')
			}

			//清空该行/列选球
			if(isNaN(y)){
				for (; i < len; i++) {
					//me.setBallData(x, i, -1);
					ballsData[x][i] = -1;
				}
			}else{
				for (; i < len2; i++) {
					//me.setBallData(i, y, -1);
					ballsData[i][y] = -1;
				}
			}




			switch (bound) {
				case 'all':
					if(isNaN(y)){
						for (i = start; i < len; i++) {
							me.setBallData(x, i, 1);
						}
					}else{
						for (i = start; i < len2; i++) {
							me.setBallData(i, y, 1);
						}
					}
					break;
				case 'big':
					for (i = halfLen; i < len; i++) {
						me.setBallData(x, i, 1);
					}
					break;
				case 'small':
					for (i = start; i < halfLen; i++) {
						me.setBallData(x, i, 1);
					}
					break;
				case 'odd':
					for (i = start; i < len; i++) {
						if ((i + 1) % 2 != 1) {
							me.setBallData(x, i, 1);
						}
					}
					break;
				case 'even':
					for (i = start; i < len; i++) {
						if ((i + 1) % 2 == 1) {
							me.setBallData(x, i, 1);
						}
					}
					break;
				case 'none':

					break;
				default:
					break;
			}


			if(dom.hasClass('dice') && bound != 'none'){
				dom.parent().addClass('current');
			}else{
				dom.addClass('current');
			}
			
			me.updateData();
		},
		//取消选球状态
		//参数：x为纵坐标 y为横坐标 v为修改值
		exeEvent_cancelCurrentButton: function(x, y, v) {
			var me = this,
				container = me.container,
				control = (typeof x != 'undefined') ? container.find('.ball-control').eq(x) : container.find('.ball-control');

			//control.find('a').removeClass('current');

			container.find('.current').removeClass('current');
		},
		//选球事件
		//球参数 action=ball&value=2&row=0  表示动作为'选球'，球值为2，行为第1行(万位)
		//函数名称： exeEvent_动作名称
		exeEvent_ball: function(param, target) {
			var me = this,
				el = $(target),
				currCls = me.defConfig.ballCurrentCls;
			//必要参数
			if (param['value'] != undefined && param['row'] != undefined) {
				if(el.get(0).nodeName.toLowerCase() == 'a'){
					if (el.hasClass(currCls)) {
						//取消选择
						me.setBallData(Number(param['row']), Number(param['value']), -1);
					} else {
						me.fireEvent('beforeSelect', param);
						//选择
						me.setBallData(Number(param['row']), Number(param['value']), 1);
					}
				}else{
					if (el.parent().hasClass(currCls)) {
						//取消选择
						me.setBallData(Number(param['row']), Number(param['value']), -1);
					} else {
						me.fireEvent('beforeSelect', param);
						//选择
						me.setBallData(Number(param['row']), Number(param['value']), 1);
					}
				}

			} else {
				try {
					console.log('GameMethod.exeEvent_ball: lack param');
				} catch (ex) {}
			}

			try{
				Games.getCurrentGame().fireEvent('afterSelectBall', param);
			}catch(ex){

			}

			me.updateData();
		},
		//渲染球dom元素的对应状态
		batchSetBallDom: function() {
			var me = this,
				cfg = me.defConfig,
				cls = cfg.ballCurrentCls,
				balls = me.balls,
				i = 0,
				j = 0,
				len = balls.length,
				len2 = 0,
				ballsDom = me.getBallsDom(),
				_cls = '';
			//同步选球数据和选球dom
			//...
			for (; i < len; i++) {
				len2 = balls[i].length;
				for (j = 0; j < len2; j++) {
					if (balls[i][j] == 1) {
						_cls = ballsDom[i][j].className;
						_cls = (' ' + _cls + ' ').replace(' ' + cls, '');
						_cls += ' ' + cls;
						ballsDom[i][j].className = _cls.replace(/\s+/g, ' ');
					} else {
						_cls = ballsDom[i][j].className;
						_cls = (' ' + _cls + ' ').replace(' ' + cls, '');
						ballsDom[i][j].className = _cls.replace(/\s+/g, ' ');
					}
				}
			}

		},
		getPositionOptionData:function(){
			return [];
		},
		//当选球/取消发生，更新相关数据
		updateData: function() {
			var me = this,
				lotterys = me.getLottery();
			//通知其他模块更新
			me.fireEvent('updateData', {
				'lotterys': lotterys,
				'position':me.getPositionOptionData(),
				'original': me.getOriginal()
			});
		},
		//在最后提交数据之前对该玩法的提交数据进行替换处理
		//data 该玩法的单注信息
		editSubmitData: function(data) {

		},
		getOriginal: function() {
			var me = this,
				balls = me.getBallData(),
				len = balls.length,
				len2 = 0,
				i = 0,
				j = 0,
				row = [],
				result = [];
			for (; i < len; i++) {
				row = [];
				len2 = balls[i].length;
				for (j = 0; j < len2; j++) {
					if (balls[i][j] > 0) {
						row.push(j);
					}
				}
				result.push(row);
			}
			return result;
		},
		//根据下注反选球
		reSelect: function(original, pos) {
			var me = this,
				type = me.getName(),
				ball = original,
				i,
				len,
				j,
				len2,
				x,
				y,
				isFlag = false;

			//任选反选位数
			if(pos && pos.length > 0){
				me.reSelectPosition(pos);
			}

			me.reSet();

			for (i = 0, len = ball.length; i < len; i++) {
				for (j = 0, len2 = ball[i].length; j < len2; j++) {
					x = i;
					y = ball[i][j];
					me.setBallData(x, y, 1);
					isFlag = true;
				}
			}
			if (isFlag) {
				me.updateData();
			}
		},
		//任选反选位数
		reSelectPosition:function(posarr){
			var me = this,
				ipt,
				CLS = 'current',
				checkboxs = me.container.find('.balls-import-positionOption input[type="checkbox"]');

			checkboxs.each(function(i){
				ipt = $(this);
				if(posarr[i] > 0){
					ipt.parent().addClass(CLS);
					ipt.get(0).checked = true;
				}else{
					ipt.parent().removeClass(CLS);
					ipt.get(0).checked = false;
				}
			});
		},
		//获取总注数/获取组合结果
		//isGetNum=true 只获取数量，返回为数字
		//isGetNum=false 获取组合结果，返回结果为单注数组
		getLottery: function(isGetNum) {
			var me = this,
				data = me.getBallData(),
				i = 0,
				len = data.length,
				row, isEmptySelect = true,
				_tempRow = [],
				j = 0,
				len2 = 0,
				result = [],
				//总注数
				total = 1,
				rowNum = 0;
			//检测球是否完整
			for (; i < len; i++) {
				result[i] = [];
				row = data[i];
				len2 = row.length;
				isEmptySelect = true;
				rowNum = 0;
				for (j = 0; j < len2; j++) {
					if (row[j] > 0) {
						isEmptySelect = false;
						//需要计算组合则推入结果
						if (!isGetNum) {
							result[i].push(j);
						}
						rowNum++;
					}
				}
				if (isEmptySelect) {
					//alert('第' + i + '行选球不完整');
					me.isBallsComplete = false;
					return [];
				}
				//计算注数
				total *= rowNum;
			}
			me.isBallsComplete = true;
			//返回注数
			if (isGetNum) {
				return total;
			}
			if (me.isBallsComplete) {
				//组合结果
				return me.combination(result);
			} else {
				return [];
			}
		},
		//单组去重处理
		removeSame: function(data) {
			var i = 0,
				result, me = this,
				numLen = this.getBallData()[0].length,
				len = data.length;
			result = Math.floor(Math.random() * numLen);
			for (; i < data.length; i++) {
				if (result == data[i]) {
					return arguments.callee.call(me, data);
				}
			}
			return result;
		},
		//移除一维数组的重复项
		removeArraySame: function(arr) {
			var me = this,
				i = 0,
				result,
				numLen = me.getBallData()[0].length,
				len = data.length;

			result = Math.floor(Math.random() * numLen);
			for (; i < arr.length; i++) {
				if (result == arr[i]) {
					return arguments.callee.call(me, arr);
				}
			}
			return result;
		},
		getRandomBetsNum: function() {
			return this.defConfig.randomBetsNum;
		},
		//生成单注随机数
		createRandomNum: function() {
			var me = this,
				current = [],
				len = me.getBallData().length,
				rowLen = me.getBallData()[0].length;
			//随机数
			for (var k = 0; k < len; k++) {
				current[k] = [Math.floor(Math.random() * rowLen)];
				current[k].sort(function(a, b) {
					return a > b ? 1 : -1;
				});
			};
			return current;
		},
		//限制随机投注重复
		checkRandomBets: function(hash, times) {
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
			if (Number(times) > Number(me.getRandomBetsNum())) {
				return current;
			}
			//建立索引
			if (allowTag) {
				for (var i = 0; i < order.length; i++) {
					if (order[i]['type'] == me.defConfig.name) {
						var name = order[i]['original'].join('');
						hash[name] = name;
					}
				};
			}
			//对比结果
			if (hash[current.join('')]) {
				times++;
				return arguments.callee.call(me, hash, times);
			}
			return current;
		},
		//生成一个当前玩法的随机投注号码
		//该处实现复式，子类中实现其他个性化玩法
		//返回值： 按照当前玩法生成一注标准的随机投注单(order)
		randomNum: function() {
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

			current = me.checkRandomBets();
			original = current;
			lotterys = me.combination(original);

			order = {
				'type': name_en,
				'original': original,
				'lotterys': lotterys,
				'moneyUnit': Games.getCurrentGameStatistics().getMoneyUnit(),
				'multiple': Games.getCurrentGameStatistics().getMultip(),
				'onePrice': Games.getCurrentGame().getGameConfig().getInstance().getOnePrice(name_en),
				'num': lotterys.length
			};
			order['amountText'] = Games.getCurrentGameStatistics().formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
			return order;
		},
		//生成指定数目的随机投注号码，并添加进号码篮
		randomLotterys: function(num) {
			var me = this,
				i = 0;
			Games.getCurrentGameOrder().cancelSelectOrder();
			for (; i < num; i++) {
				Games.getCurrentGameOrder().add(me.randomNum());
			}
		},
		//游戏错误提示
		//主要用于进行单式投注错误提示
		//具体实现在子类中的单式投注玩法
		ballsErrorTip: function() {

		},
		//计算当前选中的球数量
		countBallsNum: function() {
			var me = this,
				num = 0,
				ball = me.getBallData();

			for (var i = ball.length - 1; i >= 0; i--) {
				if (Object.prototype.toString.call(ball[i]) == '[object Array]' && ball[i].length > 0) {
					for (var j = ball[i].length - 1; j >= 0; j--) {
						if (ball[i][j] == 1) {
							num++;
						};
					};
				} else {
					if (ball[i] == 1) {
						num++;
					}
				}
			};

			return num;
		},
		//计算当前选中的球数量
		//限制计算某一单行内球数量
		countBallsNumInLine: function(lineNum) {
			var me = this,
				num = 0,
				ball = me.getBallData();


			if (Object.prototype.toString.call(ball[lineNum]) == '[object Array]' && ball[lineNum].length > 0) {
				for (var j = ball[lineNum].length - 1; j >= 0; j--) {
					if (ball[lineNum][j] == 1) {
						num++;
					};
				};
			} else {
				if (ball[lineNum] == 1) {
					num++;
				}
			}

			return num || -1;
		},
		//是否超出限制选球数量
		LimitMaxBalls: function(limitNum) {
			var me = this,
				num = 0,
				ball = me.getBallData(),
				ballCount = Number(num);

			//当前选中的球数量
			num = me.countBallsNum();

			if (num > limitNum) {
				return true;
			} else {
				return false;
			}
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete: function() {
			var me = this,
				data = me.getBallData(),
				i = 0,
				len = data.length,
				row, isEmptySelect = true,
				j = 0,
				len2 = 0;

			//检测球是否完整
			for (; i < len; i++) {
				row = data[i];
				len2 = row.length;
				isEmptySelect = true;
				for (j = 0; j < len2; j++) {
					if (row[j] > 0) {
						isEmptySelect = false;
					}
				}
				if (isEmptySelect) {
					//alert('第' + i + '行选球不完整');
					me.isBallsComplete = false;
					return false;
				}
			}
			return me.isBallsComplete = true;
		},

		//单行数组的排列组合
		//list 参与排列的数组
		//num 每组提取数量
		//last 递归中间变量
		combine: function(list, num, last) {
			var result = [],
				i = 0;
			last = last || [];
			if (num == 0) {
				return [last];
			}
			for (; i <= list.length - num; i++) {
				result = result.concat(arguments.callee(list.slice(i + 1), num - 1, last.slice(0).concat(list[i])));
			}
			return result;
		},
		//二维数组的排列组合
		//arr2 二维数组
		combination: function(arr2) {
			if (arr2.length < 1) {
				return [];
			}
			var w = arr2[0].length,
				h = arr2.length,
				i, j,
				m = [],
				n,
				result = [],
				_row = [];

			m[i = h] = 1;

			while (i--) {
				m[i] = m[i + 1] * arr2[i].length;
			}
			n = m[0];
			for (i = 0; i < n; i++) {
				_row = [];
				for (j = 0; j < h; j++) {
					_row[j] = arr2[j][~~(i % m[j] / m[j + 1])];
				}
				result[i] = _row;
			}
			return result;
		},
		//创建投注界面的小走势图
		miniTrend_create:function(){
			var me = this,
				html = [],
				dom;

			html.push(me.miniTrend_createHeadHtml());

			html.push(me.miniTrend_createRowHtml());

			html.push(me.miniTrend_createFootHtml());

			dom = $(html.join(''));
			me.miniTrend_getContainer().prepend(dom);

			return dom;
		},
		miniTrend_createHeadHtml:function(){
			var me = this,
				html = [];
			html.push('<table width="100%" class="bet-table-trend" id="J-minitrend-trendtable-'+ me.getId() +'">');
				html.push('<thead><tr>');
				html.push('<th><span class="number">奖期</span></th>');
				html.push('<th><span class="balls">开奖</th>');
				html.push('</tr></thead>');
				html.push('<tbody>');
			return html.join('');
		},
		miniTrend_createRowHtml:function(){
			var me = this,
				data = me.miniTrend_getBallsData(),
				dataLen = data.length,
				trcls = '',
				currCls = 'curr',
				item,
				html = [];
			$.each(data, function(i){
				item = this;
				trcls = '';
				trcls = i == 0 ? 'first' : trcls;
				trcls = i == dataLen - 1 ? 'last' : trcls;
				html.push('<tr class="'+ trcls +'">');
					html.push('<td><span class="number">'+ item['number'].substr(2) +' 期</span></td>');
					html.push('<td><span class="balls">');
					$.each(item['balls'], function(j){
						html.push('<i class='+ currCls +'>' + this + '</i>');
					});
					html.push('</span></td>');
				html.push('</tr>');
			});
			return html.join('');
		},
		miniTrend_createFootHtml:function(){
			var me = this,
				html = [];
				html.push('</tbody>');
			html.push('</table>');
			return html.join('');
		},
		//切换或更新走势图
		miniTrend_updateTrend:function(){
			var me = this,tbody = me.miniTrend_getTrendTable().find('tbody');

			tbody.html(me.miniTrend_createRowHtml());

			me.miniTrend_getContainer().find('.bet-table-trend').hide();
			me.miniTrend_getTrendTable().show();
		},
		miniTrend_getTrendTable:function(){
			var me = this,id = this.getId(),pageDom = $('#J-minitrend-trendtable-' + id);
			if(pageDom.size() > 0){
				return pageDom;
			}else{
				return me.miniTrend_create();
			}
		},
		miniTrend_getContainer:function(){
			return this.miniTrendContainer || (this.miniTrendContainer = $('#J-minitrend-cont'));
		},
		//获取最新的开奖数据
		miniTrend_getBallsData:function(){
			var me = this,
				cfg = Games.getCurrentGame().getGameConfig().getInstance();
			return cfg.getHistoryBallsList();
		},
		//更新完整走势图链接
		miniTrend_updateTrendUrl:function(){

		}


	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	host[name] = Main;

})(bomao, "GameMethod", bomao.Event);

//消息
;(function(host, name, Event,undefined){
	var defConfig = {
			//彩种休市提示
			lotteryClose : ['<div class="bd text-center">',
							'<p class="text-title text-left">非常抱歉，本彩种已休市。<br />请与<#=orderDate#>后再购买</p>',
							'<div class="lottery-numbers text-left">',
								'<div class="tltle"><#=lotteryName#> 第<strong class="color-green"><#=lotteryPeriods#></strong>期开奖号码：</div>',
								'<div class="content">',
									'<#=lotterys#>',
									'<a href="#">查看更多&raquo;</a>',
								'</div>',
							'</div>',
							'<dl class="lottery-list">',
								'<dt>您可以购买以下彩种</dt>',
								'<#=lotteryType#>',
							'</dl>',
						'</div>'].join(''),

			//投注信息核对
			checkLotters : ['<div class="bd game-submit-confirm-cont">',
									'<p class="game-submit-confirm-title">',
										'<label class="ui-label">彩种：<#=lotteryName#></label>',
									'</p>',
									'<ul class="ui-form">',
										'<li>',
											'<div class="textarea">',
												'<#=lotteryInfo#>',
											'</div>',
										'</li>',
										'<li class="game-submit-confirm-tip">',
											'<label class="ui-label">付款总金额：<span class="color-red"><#=lotteryamount#></span>元</label>',
										'</li>',
									'</ul>',
							'</div>'].join(''),

			//未到销售时间
			nonSaleTime : ['<div class="bd text-center">',
							'<p class="text-title text-left">非常抱歉，本彩种未到销售时间。<br />请与<#=orderDate#>后再购买</p>',
							'<dl class="lottery-list">',
								'<dt>您可以购买以下彩种</dt>',
								'<#=lotteryType#>',
							'</dl>',
						'</div>'].join(''),

			//正常提示
			normal : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
							'</div>'].join(''),

			//无效字符提示
			invalidtext : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
							'</div>'].join(''),

			//投注过期提示
			betExpired : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
							'</div>'].join(''),

			//倍数超限
			multipleOver : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
							'</div>'].join(''),

			//暂停销售
			pauseBet : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
							'</div>'].join(''),

			//成功提示
			successTip : ['<div class="bd text-center">',
								'<div class="pop-title">',
									'<i class="ico-waring <#=icon-class#>"></i>',
									'<h4 class="pop-text"><#=msg#><br /></h4>',
								'</div>',
								'<p class="text-note" style="padding:5px 0;">您可以通过”<a href="<#=link#>" target="_blank">游戏记录</a>“查询您的投注记录！</p>',
							'</div>'].join(''),
			//提醒选求提示
			checkBalls : ['<div class="bd text-center">',
							'<div class="pop-title">',
								'<i class="ico-waring <#=iconClass#>"></i>',
								'<h4 class="pop-text">请至少选择一注投注号码！</h4>',
							'</div>',
							'<div class="pop-btn ">',
								'<a href="javascript:void(0);" class="btn closeBtn">关 闭<b class="btn-inner"></b></a>',
							'</div>',
						'</div>'].join(''),
			//错误提示
			errorTip : ['<div class="bd text-center">',
							'<div class="pop-title">',
								'<i class="ico-error"></i>',
								'<h4 class="pop-text"><#=msg#></h4>',
							'</div>',
						'</div>'].join(''),
			//封锁变价
			blockade : ['<div class="bd panel-game-msg-blockade" id="J-blockade-panel-main">',
							'<form id="J-form-blockade-detail" action="ssc-blockade-detail.php" target="_blank" method="post"></form>',
							'<div class="game-msg-blockade-text">存在<#=blockadeType#>内容，系统已为您做出 <a href="#" data-action="blockade-detail">最佳处理</a> ，点击<span class="color-red">“确认”</span>完成投注</div>',
							'<div>',
								'<div class="game-msg-blockade-line-title">彩种：<#=gameTypeTitle#></div>',
								'<div class="game-msg-blockade-line-title">期号：<#=currentGameNumber#></div>',
							'</div>',
							'<div id="J-game-panel-msg-blockade-0">',
								'<div class="game-msg-blockade-cont" id="J-msg-panel-submit-blockade-error0"><#=blockadeData0#></div>',
							'</div>',
							'<div class="game-msg-blockade-panel-money">',
								'<div><b>付款总金额：</b><span class="color-red"><b id="J-money-blockade-adjust"><#=amountAdjust#></b></span> 元&nbsp;&nbsp;&nbsp;&nbsp;<span style="display:<#=display#>"><b>减少投入：</b><span class="color-red"><b id="J-money-blockade-change"><#=amountChange#></b></span> 元</span></div>',
								'<div><b>付款账号：</b><#=username#></div>',
							'</div>',
							'<div>',
								'<p class="text-note">购买后请您尽量避免撤单，如撤单将收取手续费：￥<span class="handlingCharge">0.00</span>元</p>',
								'<p class="text-note">本次投注，若未涉及到付款金额变化，将不再提示</p>',
							'</div>',
						'</div>'].join(''),
			//user type is proty or other,just player allowed to bet
			userTypeError:['<div class="bd text-center">',
							'<div class="pop-title">',
								'<i class="ico-error"></i>',
								'<h4 class="pop-text">对不起，仅玩家允许投注</h4>',
							'</div>',
						'</div>'].join('')
		},
	instance,
	closeTime = null,
	Games = host.Games;

	var pros = {
		//初始化
		init: function(cfg){
			var me = this;
			me.win = new host.MiniWindow({
				//实例化时追加的最外层样式名
				cls:'pop w-9'
			});
			me.mask = host.Mask.getInstance();
			//绑定隐藏完成事件
			me.reSet();
			me.win.addEvent('afterHide', function(){
				me.reSet();
			})
		},
		//彩种提示类型
		doAction: function(data){
			var me = this,
				funName = 'rebuild' + data['type'],
				getHtml = 'getHtml' + data['type'],
				fn = function(){
				};
			//'-' is not allowed to be a function name
			getHtml = getHtml.replace('-', '_');
			funName = funName.replace('-', '_');

			//console.log(getHtml);
			//console.log(funName);
			if(!$.isFunction(me[getHtml])){
				getHtml = 'getHtmlnormal';
			}

			if(me[funName] && $.isFunction(me[funName])){
				fn = me[funName];
			}
			data['tpl']  = typeof data['tpl'] == 'undefined' ? me[getHtml]() : '' + data['tpl'];
			//删除type数据
			//防止在渲染的时候进行递归调用
			delete data['type'];
			//调用子类方法
			fn.call(me, data);
		},
		formatHtml:function(tpl, order){
			var me = this,o = order,p,reg;
			for(p in o){
				if(o.hasOwnProperty(p)){
					reg = RegExp('<#=' + p + '#>', 'g');
					tpl = tpl.replace(reg, o[p]);
				}
			}
			return tpl;
		},
		//检查数组存在某数
		arrIndexOf: function(value, arr){
		    var r = 0;
		    for(var s=0; s<arr.length; s++){
		        if(arr[s] == value){
		            r += 1;
		        }
		    }
		    return r || -1;
		},
		//common error tip
		getHtmlerrorTip :function(){
			var cfg = this.defConfig;
			return cfg.errorTip;
		},
		rebuilderrorTip :function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide()
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		//通用
		getHtmlWaring: function(){
			var cfg = this.defConfig;
			return cfg.normal;
		},
		//默认弹窗
		rebuildnormal: function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide()
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		//获取默认提示弹窗
		getHtmlnormal: function(){
			return this.getHtmlWaring();
		},
		rebuildlow_balance:function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmllow_balance:function(){
			return this.getHtmlWaring();
		},
		//issue_error
		rebuildissue_error:function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlissue_error:function(){
			return this.getHtmlWaring();
		},
		//bet failed
		rebuildbet_failed:function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlbet_failed:function(){
			return this.getHtmlWaring();
		},
		/*
			//彩种核对
			bomao.Games.getCurrentGameMessage().show({
			   type : 'checkLotters',
			   data : {
			   		tplData : {
				   		//当期彩票详情
				        lotteryDate : '20121128-023',
				        //彩种名称
				        lotteryName : 'shishicai',
				        //投注详情
				        lotteryInfo : ,
				        //彩种金额
				        lotteryamount : {'year':'2013','month':'5','day':'3','hour':'1','min':'30'},
				        //付款帐号
				        lotteryAcc :，
				       	//手续费
				       	lotteryCharge
			   		}
				}
			})
		 */
		rebuildcheckLotters : function(parameter){
			var me = this,
				order = Games.getCurrentGameOrder().getTotal()['orders'],
				result = {};
				result['mask'] = true;
				result['iconClass'] = '';

				// //彩种名称
				// parameter['data']['tplData']['lotteryName'] = function(){
				// 	return lotteryName || '';
				// };
				// //本次开奖期数
				// parameter['data']['tplData']['lotteryPeriods'] = function(){
				// 	return lotteryPeriods || '';
				// };
				// //购买日期
				// parameter['data']['tplData']['orderDate'] = function(){
				// 	return time['year'] + '年' + time['month'] + '月' + time['day'] + '日 ' + time['hour'] + ':' + time['min'];
				// };
				// //彩票详情
				// parameter['data']['tplData']['lotterys'] = function(){
				// 	var html  = '';
				// 	if($.isArray(lotterys)){
				// 		for (var i = 0; i < lotterys.length; i++) {
				// 			html += '<em>' + lotterys[i] + '</em>';
				// 		};
				// 	}
				// 	return html;
				// };
				// //彩票种类
				// parameter['data']['tplData']['lotteryType'] = function(){
				// 	var html  = '';
				// 	if($.isArray(typeArray)){
				// 		for (var i = 0; i < typeArray.length; i++) {
				// 			html += '<dd><span style="background:url(' + typeArray[i]['pic'] +')" class="pic" title="' + typeArray[i]['name'] + '" alt="' + typeArray[i]['name'] + '"></span><a href="' + typeArray[i]['url'] + '" class="btn">去投注<b class="btn-inner"></b></a></dd>';
				// 		};
				// 	}
				// 	return html;
				// };
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlcheckLotters : function(){
			var cfg = this.defConfig;
			return cfg.checkLotters;
		},
		/*
			//彩种关闭调用实例
			bomao.Games.getCurrentGameMessage().show({
			   type : 'lotteryClose',
			   data : {
			   		tplData : {
				   		//当期彩票详情
				        lotterys : [1,2,3,4,5,6],
				        //彩种名称
				        lotteryName : 'shishicai',
				        //开奖期数
				        lotteryPeriods : '20130528-276',
				        //开始购买时间
				        orderDate : {'year':'2013','month':'5','day':'3','hour':'1','min':'30'},
				        //提示彩票种类
				        lotteryType : [{'name':'leli','pic':'#','url':'http://163.com'},{'name':'kuaile8','pic':'#','url':'http://pp158.com'}]
			   		}
				}
			})
		 */
		//彩种关闭
		rebuildlotteryClose : function(parameter){
			var me = this,
				result = {};
				lotteryName = parameter['data']['tplData']['lotteryName'];
				lotteryPeriods = parameter['data']['tplData']['lotteryPeriods'];
				time = parameter['data']['tplData']['orderDate'];
				lotterys = parameter['data']['tplData']['lotterys'];
				typeArray = parameter['data']['tplData']['lotteryType'];
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				//彩种名称
				parameter['data']['tplData']['lotteryName'] = function(){
					return lotteryName || '';
				};
				//本次开奖期数
				parameter['data']['tplData']['lotteryPeriods'] = function(){
					return lotteryPeriods || '';
				};
				//购买日期
				parameter['data']['tplData']['orderDate'] = function(){
					return time['year'] + '年' + time['month'] + '月' + time['day'] + '日 ' + time['hour'] + ':' + time['min'];
				};
				//彩票详情
				parameter['data']['tplData']['lotterys'] = function(){
					var html  = '';
					if($.isArray(lotterys)){
						for (var i = 0; i < lotterys.length; i++) {
							html += '<em>' + lotterys[i] + '</em>';
						};
					}
					return html;
				};
				//彩票种类
				parameter['data']['tplData']['lotteryType'] = function(){
					var html  = '';
					if($.isArray(typeArray)){
						for (var i = 0; i < typeArray.length; i++) {
							html += '<dd><span style="background:url(' + typeArray[i]['pic'] +')" class="pic" title="' + typeArray[i]['name'] + '" alt="' + typeArray[i]['name'] + '"></span><a href="' + typeArray[i]['url'] + '" class="btn">去投注<b class="btn-inner"></b></a></dd>';
						};
					}
					return html;
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmllotteryClose : function(){
			var cfg = this.defConfig;
			return cfg.lotteryClose;
		},
		/*
			//调用实例
			bomao.Games.getCurrentGameMessage().show({
			   type : 'nonSaleTime',
			   data : {
			       tplData:{
						//开始购买时间
				        orderDate : {'year':'2013','month':'5','day':'3','hour':'1','min':'30'},
				        //提示彩票种类
				        lotteryType : [{'name':'leli','pic':'#','url':'http://163.com'},{'name':'kuaile8','pic':'#','url':'http://pp158.com'}]
			       }
			   }
			})
		 */
		//未到销售时间
		rebuildnonSaleTime : function(parameter){
			var me = this,
				result = {};
				time = parameter['data']['tplData']['orderDate'];
				typeArray = parameter['data']['tplData']['lotteryType'];
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				//购买日期
				parameter['data']['tplData']['orderDate'] = function(){
					return time['year'] + '年' + time['month'] + '月' + time['day'] + '日 ' + time['hour'] + ':' + time['min'];
				};
				//彩票种类
				parameter['data']['tplData']['lotteryType'] = function(){
					var html  = '';

					if($.isArray(typeArray)){
						for (var i = 0; i < typeArray.length; i++) {
							html += '<dd><span style="background:url(' + typeArray[i]['pic'] +')" class="pic" title="' + typeArray[i]['name'] + '" alt="' + typeArray[i]['name'] + '"></span><a href="' + typeArray[i]['url'] + '" class="btn">去投注<b class="btn-inner"></b></a></dd>';
						};
					}
					return html;
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlnonSaleTime : function(){
			var cfg = this.defConfig;
			return cfg.nonSaleTime;
		},
		//just user player allowed to bet
		rebuildno_right :function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		//至少选择一注
		rebuildmustChoose : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlmustChoose : function(){
			return this.getHtmlWaring();
		},
		//网络连接异常
		rebuildnetAbnormal : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlnetAbnormal : function(){
			return this.getHtmlWaring();
		},
		//提交成功 (moz 增加注单详情按钮)
		rebuildsuccess : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				/*增加注单详情按钮*/
				result['otherText'] = '注单详情';
				if(parameter['isSuccess'] == 1 && parameter['data']['tplData']['detail_url'] != undefined){
					result['secondButtonIsShow'] = true;
					result['otherFun'] = function(){
						window.open(parameter['data']['tplData']['detail_url'],'_blank');
						me.hide();
					}
				}else{
					result['secondButtonIsShow'] = false;
				}
				/*END*/
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlsuccess : function(){
			var cfg = this.defConfig;
			return cfg.successTip;
		},
		//登陆超时loginTimeout
		rebuildloginTimeout : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
					location.href = '/';
				};
				result['normalCloseFun'] = function(){
					location.href = '/';
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlloginTimeout : function(){
			return this.getHtmlWaring();
		},
		//服务器错误
		rebuildserverError : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlserverError : function(){
			return this.getHtmlWaring();
		},
		//余额不足
		rebuildInsufficientbalance : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlInsufficientbalance : function(){
			return this.getHtmlWaring();
		},
		//暂停销售
		rebuildpauseBet : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['confirmText'] = '投 注';
				result['confirmIsShow'] = true;
				result['confirmFun'] = function(){
					var order = Games.getCurrentGameOrder(),
						i = 0;
					//删除指定类别的投注
					for (; i < parameter['data']['tplData']['balls'].length; i++) {
						order.removeData(parameter['data']['tplData']['balls'][i]['id']);
					};
					//提交投注
					Games.getCurrentGameSubmit().submitData();
				};
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				//生成消息
				parameter['data']['tplData']['msg'] = function(){
					var numText = [],
						gameConfig = Games.getCurrentGame().getGameConfig().getInstance(),
						k = 0;
						//输出暂停销售名称集合
						for (; k < parameter['data']['tplData']['balls'].length; k++) {
							var current = parameter['data']['tplData']['balls'][k]['type'],
								typeText = gameConfig.getTitleByName(current);
							if(me.arrIndexOf(typeText.join(''), numText) == -1){
								numText.push(typeText.join(''));
							}
						};
						return '您的投注内容中“' + numText.join('') + '”已暂停销售，是否完成剩余内容投注？';
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlpauseBet : function(){
			var cfg = this.defConfig;
			return cfg.pauseBet;
		},
		//倍数超限
		rebuildmultipleOver : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['iconClass'] = '';
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				//生成消息
				parameter['data']['tplData']['msg'] = function(){
					var numText = [],
						gameConfig = Games.getCurrentGame().getGameConfig().getInstance(),
						k = 0;
						//输出暂停销售名称集合
						for (; k < parameter['data']['tplData']['balls'].length; k++) {
							var current = parameter['data']['tplData']['balls'][k]['type'],
								typeText = gameConfig.getTitleByName(current);
							if(me.arrIndexOf(typeText.join(''), numText) == -1){
								numText.push(typeText.join(''));
							}
						};
						return '您的投注内容中“' + numText.join('') + '”超出倍数限制，请调整！';
				};
				result['content'] = me.formatHtml(parameter['tpl'], parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlmultipleOver : function(){
			var cfg = this.defConfig;
			return cfg.multipleOver;
		},
		//无效字符
		rebuildinvalidtext : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['confirmText'] = '刷新页面';
				result['confirmIsShow'] = true;
				result['confirmFun'] = function(){
					window.location.reload();
				};
				result['content'] = me.formatHtml(me.getHtmlinvalidtext(), parameter);
				me.show($.extend(result, parameter));
		},
		getHtmlinvalidtext : function(){
			var cfg = this.defConfig;
			return cfg.invalidtext;
		},
		//投注过期
		rebuildbetExpired : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				parameter['data']['tplData']['msg'] = function(){
						return '您好，' + parameter['data']['tplData']['bitDate']['expiredDate'] + '期 已截止销售，当前期为' + parameter['data']['tplData']['bitDate']['current'] + '期 ，请留意！';
				};
				result['content'] = me.formatHtml(me.getHtmlbetExpired(), parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlbetExpired : function(){
			var cfg = this.defConfig;
			return cfg.betExpired;
		},
		//非法投注工具
		rebuildillegalTools : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['confirmText'] = '刷新页面';
				result['confirmIsShow'] = true;
				result['confirmFun'] = function(){
					window.location.reload();
				};
				result['content'] = me.formatHtml(me.getHtmlbetExpired(), parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},


		//封锁变价模板
		getHtmlblockade : function(){
			return this.defConfig.blockade;
		},
		//封锁变价
		rebuildblockade : function(parameter){
			var me = this, result = {},tplData = parameter['data']['tplData'],orderData = parameter['data']['orderData'],blockadeInfo = parameter['data']['blockadeInfo'],
				balls = orderData['balls'],
				dataHas = {},
				ballStr = '',
				typeName = '',
				formatMoney = Games.getCurrentGameOrder().formatMoney,
				maxLen = 28,
				//是否在提交中
				isSubmitLoading = false,
				blockadeData0 = ['<ul class="game-msg-blockade-balls">'];

				result['mask'] = true;
				result['closeIsShow'] = true;
				result['closeText'] = '关 闭';
				result['confirmIsShow'] = true;
				result['confirmText'] = '确 认';
				result['closeFun'] = function(){
					me.hide();
				};

				$.each(balls, function(i){
					dataHas['' + this['id']] = this;
					ballStr = this['ball'];
					if(ballStr.length > maxLen){
						ballStr = ballStr.substr(0, maxLen) + '...';
					}
					typeName = Games.getCurrentGame().getGameConfig().getInstance().getTitleByName(this['type']).join('_');

					blockadeData0.push('<li data-id="'+ this['id'] +'">['+ typeName +'] '+ ballStr +'</li>');
				});
				blockadeData0.push('</ul>');

				tplData['gameTypeTitle'] = Games.getCurrentGame().getGameConfig().getInstance().getGameTypeCn();
				tplData['blockadeData0'] = blockadeData0.join('');
				tplData['amount'] = formatMoney(orderData['amount']);
				tplData['username'] = blockadeInfo['username'];
				tplData['amountAdjust'] = formatMoney(blockadeInfo['amountAdjust']);
				tplData['amountChange'] = formatMoney(orderData['amount'] - blockadeInfo['amountAdjust']);
				tplData['display'] = '';

				if(blockadeInfo['type'] == 1){
					tplData['blockadeType'] = '受限';
				}else if(blockadeInfo['type'] == 2){
					tplData['blockadeType'] = '奖金变动';
					tplData['display'] = 'none';
				}else{
					tplData['blockadeType'] = '奖金变动及受限';
				}

				//获得撤单手续费
				result['callback'] = function(){
					$.ajax({
						url: Games.getCurrentGameSubmit().defConfig.handlingChargeURL + '?amout=' + blockadeInfo['amountAdjust'],
						dataType: 'json',
						method: 'GET',
						success: function(r){
							if(Number(r['isSuccess']) == 1){
								me.getContentDom().find('.handlingCharge').html(r['data']['handingcharge']);
							}
						}
					});
				};

				result['content'] = me.formatHtml(me.getHtmlbetExpired(), tplData);


				//再次提交注单
				result['confirmFun'] = function(){
					var message = Games.getCurrentGameMessage();
					if(isSubmitLoading){
						return false;
					}
					$.ajax({
						url: Games.getCurrentGameSubmit().defConfig.URL,
						data: orderData,
						dataType: 'json',
						method: 'POST',
						beforeSend:function(){
							isSubmitLoading = true;
						},
						success: function(r){
						//返回消息标准
						// {"isSuccess":1,"type":"消息代号","msg":"返回的文本消息","data":{xxx:xxx}}
							if(Number(r['isSuccess']) == 1){
								message.show(r);
								me.clearData();
								me.fireEvent('afterSubmitSuccess');
							}else{
								message.show(r);
							}
						},
						complete: function(){
							isSubmitLoading = false;
							me.fireEvent('afterSubmit');
						}
					});
				};
				//console.log(parameter);
				me.show($.extend(result, parameter));
				host.util.toViewCenter(me.win.dom);
				//console.log(parameter);



				//面板内的事件
				$('#J-blockade-panel-main').on('click', '[data-action]', function(e){
					var el = $(this),action = $.trim(el.attr('data-action')),id = $.trim(el.parent().attr('data-id'));
					e.preventDefault();
					//console.log(action, id, dataHas[id]);
					switch(action){
						//查看详情
						case 'blockade-detail' :
							//将投注内容转换成Input内容
							var form = $('#J-form-blockade-detail'),
								splitStr = '-';
							form.html('');
							//游戏名称
							$('<input type="hidden" value="'+ orderData['gameType'] +'" name="gameType" />').appendTo(form);
							//选球内容和玩法名称以 /// 分隔
							$.each(balls, function(){
								var me = this;
								if(me['lockPoint']){
									if($.trim(me['lockPoint']['beforeBlockadeList']) != ''){
										$.each(me['lockPoint']['beforeBlockadeList'], function(){
											var dt = this;
											$('<input type="hidden" value="'+ dt['beishu'] + splitStr + dt['blockadeDetail'] + splitStr + dt['realBeishu'] + splitStr + me['type'] + splitStr + me['ball'] + '" name="beforeBlockadeList[]" />').appendTo(form);
										});
									}
									if($.trim(me['lockPoint']['pointsList']) != ''){
										$.each(me['lockPoint']['pointsList'], function(){
											var dt = this;
											$('<input type="hidden" value="'+ dt['mult'] + splitStr + dt['point'] + splitStr + dt['retValue'] + splitStr + me['type'] + splitStr + me['ball'] + '" name="pointsList[]" />').appendTo(form);
										});
									}

								}

							});
							form.submit();
						break;
						default:
						break;
					}
				});


		},


		getHtmlillegalTools : function(){
			return this.getHtmlWaring();
		},
		//提交失败
		rebuildsubFailed : function(parameter){
			var me = this, result = {};
				result['mask'] = true;
				result['closeText'] = '关 闭';
				result['closeIsShow'] = true;
				result['closeFun'] = function(){
					me.hide();
				};
				result['content'] = me.formatHtml(me.getHtmlbetExpired(), parameter['data']['tplData']);
				me.show($.extend(result, parameter));
		},
		getHtmlsubFailed : function(){
			return this.getHtmlWaring();
		},
		//user type is proxy
		getHtmlno_right:function(){
			return this.defConfig.userTypeError;
		},
		//添加题目
		setTitle: function(html){
			var me = this, win = me.win;
			win.setTitle(html);
		},
		//添加内容
		setContent: function(html, delay){
			var me = this, win = me.win;

			win.setContent(html, delay);
		},
		//隐藏关闭按钮
		hideClose: function(){
			var me = this, win = me.win;

			win.getCloseDom().hide();
		},
		//隐藏标题栏
		hideTitle: function(){
			var me = this, win = me.win;

			win.getTitleDom().hide();
		},

		//弹窗显示 具体参数说明
		//弹窗类型(会根据弹窗类型自动获取模版) type
		//模版 tpl  数据 tplData
		//内容:content, 绑定函数: callback, 是否遮罩: mask
		//宽度:width, 长度:height, 自动关闭时间单位S:time
		//是否显示头部: hideTitle, 是否显示关闭按钮:hideClose
		//确认按钮 是否显示: confirmIsShow 名称: confirmText 事件: confirmFun
		//取消按钮 是否显示: cancelIsShow  名称: cancelText	事件: cancelFun
		//关闭按钮 是否显示: closeIsShow   名称: closeText	事件: closeFun
		//预留按钮 是否显示: secondButtonIsShow 名称: otherText 事件: otherFun
		show: function(data){
			var me = this, win = me.win;
			me.reSet();
			if(typeof data['data'] == 'undefined'){
				data['data'] = {};
			}
			data['data']['tplData'] = typeof data['data']['tplData'] == 'undefined' ? {} : data['data']['tplData'];

			if(!data){return}

			if(data['type']){
				me.doAction(data);
				return;
			}else{
				if(typeof data['tpl'] != 'undefined'){
					data['content'] = me.formatHtml(data['tpl'], data['data']['tplData']);
				}
			}

			//取消自动关闭时间缓存
			if(closeTime){
				clearTimeout(closeTime);
				closeTime = null;
			}

			//加入题目 && 内容
			me.setTitle(data['title'] || '温馨提示');
			me.setContent(data['content'] || '');
			//按钮名称
			if(data['confirmText']){
				win.setConfirmName(data['confirmText']);
			}
			if(data['cancelText']){
				win.setCancelName(data['cancelText']);
			}
			if(data['closeText']){
				win.setCloseName(data['closeText']);
			}
			if(data['otherText']){
				win.setSecondButtonName(data['otherText']);
			}
			//按钮事件
			if(data['normalCloseFun']){
				win.doNormalClose = data['normalCloseFun'];
			}
			if(data['confirmFun']){
				win.doConfirm = data['confirmFun'];
			}
			if(data['cancelFun']){
				win.doCancel = data['cancelFun'];
			}
			if(data['closeFun']){
				win.doClose = data['closeFun'];
			}
			if(data['otherFun']){
				win.doOther = data['otherFun'];
			}
			//按钮显示
			if(data['confirmIsShow']){
				win.showConfirmButton();
			}
			if(data['cancelIsShow']){
				win.showCancelButton();
			}
			if(data['closeIsShow']){
				win.showCloseButton();
			}
			if(data['secondButtonIsShow']){
				win.showSecondButton();
			}else{
				win.hideSecondButton();
			}
			//判断是否隐藏头部和关闭按钮
			if(data['hideTitle']){
				me.hideTitle();
			}
			if(data['hideClose']){
				me.hideClose();
			}
			//遮罩显示
			if(data['mask']){
				setTimeout(function(){
					me.mask.show();
				}, 100);
			}

			win.show();

			//执行回调事件
			if(data['callback']){
				data['callback'].call(me);
			}

			//定时关闭
			if(data['time']){
				closeTime = setTimeout(function(){
					me.hide();
					clearTimeout(closeTime);
					closeTime = null;
				}, data['time'] * 1000)
			}
		},
		getContainerDom : function(){
			var me = this;
			return me.win.getContainerDom();
		},
		//获取内容容器DOM
		getContentDom : function(){
			var me = this;
			return me.win.getContentDom();
		},
		//弹窗隐藏
		hide: function(){
			var me = this, win = me.win;

			win.hide();
			me.reSet();
		},
		//重置
		reSet: function(){
			var me = this, win = me.win;

			me.mask.hide();
			me.setTitle('提示');
			me.setContent('');
			win.hideConfirmButton();
			win.hideCancelButton();
			win.hideCloseButton();
			win.doConfirm = function(){};
			win.doCancel = function(){};
			win.doClose = function(){};
			win.doNormalClose = function(){};
			win.setConfirmName('确 认');
			win.setCancelName('取 消');
			win.setCloseName('关 闭');
		},
		showTip:function(msg, callback){
			var me = this;
			me.mask.show();
			//console.log(me.win);
			me.win.showTip(msg, callback);
		},
		hideTip:function(){
			var me = this;
			me.win.hideTip();
			me.mask.hide();
		}
	}

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;

})(bomao, "GameMessage",  bomao.Event);













(function(host, name, Event, undefined){
	var defConfig = {
		//主面板dom
		panel:'#J-panel-gameTypes'
	},
	//渲染实例
	instance,
	//游戏实例
	Games = host.Games;
	
	//渲染方法
	var pros = {
		init:function(cfg){
			var me = this;
			//缓存方法
			Games.setCurrentGameTypes(me);
			me.container = $(cfg.panel);
			//玩法数据
			me.data = Games.getCurrentGame().getGameConfig().getInstance().getMethods();
			me.buildDom();
			me.initEvent();


			//联动玩法面板的切换
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(obj, id){
				me.changePanel(id);
			});

		},
		buildDom:function(){
			var me = this,it,it2,it3,strArr = [],strArrAll = [],modeArr = [];
			$.each(me.data, function(){
				strArr = [];
				it = this;
				strArr.push('<li class="gametypes-menu-'+ it['name_en'] +'">');
					strArr.push('<div class="title">');
						strArr.push(it['name_cn']);
						modeArr[0] = it['name_en'];
						strArr.push('<span></span>');
					strArr.push('</div>');
					strArr.push('<div class="content clearfix">');
						strArr.push('<div class="sj"></div>');
					
					$.each(it['children'], function(){
						it2 = this;
						modeArr[1] = it2['name_en'];
						strArr.push('<dl>');
							strArr.push('<dd class="types-node types-node-'+ it2['name_en'] +'">'+ it2['name_cn'] +'</dd>');
							$.each(it2['children'], function(){
								it3 = this;
								modeArr[2] = it3['name_en'];
								strArr.push('<dd class="types-item" data-id="'+ it3['id'] +'">'+ it3['name_cn'] +'</dd>');
							});
						strArr.push('</dl>');
					});
					strArr.push('</div>');
				strArr.push('</li>');
				
				strArrAll.push(strArr.join(''));
			});
			me.getContainerDom().html(strArrAll.join(''));



			//构建平板面板菜单
			me.buildPanelMenu();


			
			setTimeout(function(){
				me.fireEvent('endShow');
			}, 20);
			
		},
		buildPanelMenu:function(){
			var me = this,it,it2,it3,strArr = [],strArrAll = [],modeArr = [],
				panelDom = me.getPanelDom();
			$.each(me.data, function(){
				strArr = [];
				it = this;
				strArr.push('<li class="gametypes-sort gametypes-menu-'+ it['name_en'] +'">');
					/**
					strArr.push('<div class="title">');
						strArr.push(it['name_cn']);
						modeArr[0] = it['name_en'];
						strArr.push('<span></span>');
					strArr.push('</div>');
					**/
					strArr.push('<div class="content clearfix">');
					
					$.each(it['children'], function(){
						it2 = this;
						modeArr[1] = it2['name_en'];
						strArr.push('<dl>');
							strArr.push('<dd class="types-node types-node-'+ it2['name_en'] +'">'+ it2['name_cn'] +'</dd>');
							$.each(it2['children'], function(){
								it3 = this;
								modeArr[2] = it3['name_en'];
								strArr.push('<dd class="types-item" data-id="'+ it3['id'] +'">'+ it3['name_cn'] +'</dd>');
							});
						strArr.push('</dl>');
					});
					strArr.push('</div>');
				strArr.push('</li>');
				
				strArrAll.push(strArr.join(''));
			});
			panelDom.html(strArrAll.join(''));
		},
		initEvent:function(){
			var me = this;
			me.container.on('click', '.types-item', function(){
				var el = $(this),id = el.attr('data-id');
				if(id){
					me.changeMode(id, el);
				}
			});


			me.getPanelDom().on('click', '.types-item', function() {
				var el = $(this),id = el.attr('data-id');
				if(id){
					me.changeMode(id, el);
				}
			});
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(obj, id){
				//只有当开启全展模式的时候事件才有效
				if(!me.getContainerDom().parent().hasClass('play-select-status-b')){
						return;
				}
				//显示玩法高亮
				var el = me.getPanelDom().find('dd[data-id=' + id + ']'),CLS = 'types-item-current',CLS2 = 'gametypes-sort-current';
				me.getPanelDom().find('.types-item').removeClass(CLS);
				el.addClass(CLS);
				//显示大面板
				me.getPanelDom().children().removeClass(CLS2);
				el.parents('.gametypes-sort').addClass(CLS2);

				Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').hide();
			});

			//大玩法群切换
			me.getContainerDom().find('li > .title').on('click', function() {
				var el = $(this),
					parent = el.parent(),
					lis = parent.parent().children(),
					index = lis.index(parent.get(0)),
					panel = me.getPanelDom().find('.gametypes-sort').eq(index),
					dom = panel.find('.types-item').eq(0),
					id = dom.attr('data-id');
					//只有当开启全展模式的时候事件才有效
					if(me.getContainerDom().parent().hasClass('play-select-status-b')){
						me.changeMode(id, dom);
					}
			});

			me.initMenuStatusEvent();
			

		},
		//菜单模式切换
		initMenuStatusEvent:function(){
			var me = this;
			$('#J-menu-status-control').click(function(e) {
				var el = $(this),CLS = 'menu-control-current';
				e.preventDefault();

				//切换到鼠标划过模式
				if(el.hasClass(CLS)){
					me.fireEvent('beforeSwitchMenuStatus', 1);
					el.removeClass(CLS);
					me.getContainerDom().parent().removeClass('play-select-status-b');
					me.getPanelDom().find('.gametypes-sort-current').removeClass('gametypes-sort-current');
					Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').show();
					me.fireEvent('afterSwitchMenuStatus', 1);
				}else{
				//切换到平板模式
					me.fireEvent('beforeSwitchMenuStatus', 0);
					el.addClass(CLS);
					me.getContainerDom().parent().addClass('play-select-status-b');
					me.changeMode(Games.getCurrentGame().getCurrentGameMethod().getId());
					Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').hide();
					me.fireEvent('afterSwitchMenuStatus', 0);
				}
			});
		},
		//获取外部容器DOM
		getContainerDom: function(){
			return this.container;
		},
		getPanelDom:function(){
			return this.panelDom || (this.panelDom = $('#J-gametyes-menu-panel'));
		},
		//切换事件
		changeMode: function(mode, el){
			var me = this,
				container = me.getContainerDom();
			
			//执行自定义事件
			me.fireEvent('beforeChange', mode);
			try{
				if(mode == Games.getCurrentGame().getCurrentGameMethod().getGameMethodName()){
					return;
				}
			}catch(e){
			}
			//执行切换
			Games.getCurrentGame().switchGameMethod(mode);
		},
		//切换
		changePanel:function() {
			
		}
	};
	
	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;
	
})(bomao, "GameTypes", bomao.Event);












//游戏选球统计，如注数、当前操作金额等
(function(host, name, Event, undefined){
	var defConfig = {
		//主面板dom
		mainPanel:'#J-balls-statistics-panel',
		//注数dom
		lotteryNumDom:'#J-balls-statistics-lotteryNum',
		//倍数
		multipleDom:'#J-balls-statistics-multiple',
		//总金额
		amountDom:'#J-balls-statistics-amount',
		moneyUnitDom:'#J-balls-statistics-moneyUnit',
		//元/角模式比例  1为元模式 0.1为角模式
		moneyUnit:1,
		//元角模式对应的中文
		moneyUnitData:{'0.01':'分','0.1':'角','1':'元'},
		//倍数
		multiple:1
	},
	instance,
	Games = host.Games;


	var pros = {
		init:function(cfg){
			var me = this;
			Games.setCurrentGameStatistics(me);

			me.panel = $(cfg.mainPanel);
			me.moneyUnit = cfg.moneyUnit;
			me.multiple = cfg.multiple;
			//已组合好的选球数据
			me.lotteryData = [];




			//倍数选择模拟下拉框
			me.multipleDom = new bomao.Select({cls:'select-game-statics-multiple',realDom:cfg.multipleDom,isInput:true,expands:{inputEvent:function(){
													var meSelect = this;
													this.getInput().keyup(function(e){
														var v = this.value,
															id = Games.getCurrentGame().getCurrentGameMethod().getId(),
															unit = me.getMoneyUnit(),
															maxv = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
														
														this.value = this.value.replace(/[^\d]/g, '');
														if($.trim(this.value) != ''){
															v = Number(this.value);
															if(v < 1){
																this.value = 1;
															}else if(v > maxv){
																this.value = maxv;
															}
															meSelect.setValue(this.value);
														}
													});
													this.getInput().blur(function(){
														var v = this.value,
															id = Games.getCurrentGame().getCurrentGameMethod().getId(),
															unit = me.getMoneyUnit(),
															maxv = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
														this.value = this.value.replace(/[^\d]/g, '');
														v = Number(this.value);
														if(v < 1){
															this.value = 1;
														}else if(v > maxv){
															this.value = maxv;
														}
														meSelect.setValue(this.value);
													});
												}}});
			me.multipleDom.setValue(me.multiple);
			me.multipleDom.addEvent('change', function(e, value, text){
				var num = Number(value),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = me.getMoneyUnit(),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit),
					method = Games.getCurrentGame().getCurrentGameMethod(),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					prizesMultipleBound;

				if(methodCfg['is_enable_extra'] == 1){
					prizesMultipleBound = me.getPrizesMultipleBound();
					maxnum = Math.min(prizesMultipleBound['min'], prizesMultipleBound['max']);
				}

				if(num > maxnum){
					num = maxnum;
					this.setValue(num);
				}
				me.setMultiple(num);
				//console.log(Games.getCurrentGame().getCurrentGameMethod().getLottery());
				me.updateData({
					'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),
					'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal(),
					'position':Games.getCurrentGame().getCurrentGameMethod().getPositionOptionData()
				}, Games.getCurrentGame().getCurrentGameMethod().getName());
			});
			//手动加减
			$('#J-bet-statics-multiple-reduce').click(function(){
				var v = Number(me.multipleDom.getValue()),v2 = (v - 1) < 1 ? 1 : v - 1;
				me.multipleDom.setValue(v2);
			});
			$('#J-bet-statics-multiple-add').click(function(){
				var v = Number(me.multipleDom.getValue());
				me.multipleDom.setValue(v + 1);
			});




			/**
			//元角模式模拟下拉框
			//me.moneyUnitDom = new host.SlideCheckBox({realDom:cfg.moneyUnitDom});
			me.moneyUnitDom = new host.Select({realDom:cfg.moneyUnitDom});
			//在未添加change事件之前设置初始值
			me.moneyUnitDom.setValue(me.moneyUnit);
			me.moneyUnitDom.addEvent('change', function(e, value, text){
				var multiple = me.getMultip(),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Number(value),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit);
				multiple = multiple > maxnum ? maxnum : multiple;
				me.setMultipleDom(multiple);

				me.setMoneyUnit(Number(value));
				me.updateData({'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal()}, Games.getCurrentGame().getCurrentGameMethod().getName());
				
				
				//更新单注奖金金额
				var prize = Number(methodCfg['prize']) * unit;
				prize = bomao.util.formatMoney(prize);
				prize = prize.split('.');
				prize[1] = '<i>' + prize[1] + '</i>';
				prize = prize.join('.');
				$('#J-method-prize').html(prize);
			});
			**/



			//元角模式tab操作
			me.moneyUnitDom = new host.Tab({par:'#J-bet-statics-tab-moneyunit', triggers:'.item', panels:'.bet-statics-moneyunit-cont', eventType:'click'});
			me.moneyUnitDom.setValue = function(v){
				var v = '' + $.trim(v),curr = this.par.find('[data-value="'+ v +'"]'),index = this.triggers.index(curr.get(0));
				this.triggers.removeClass('current');
				curr.addClass('current');
				this.index = index;
				v = Number(v);
				me.setMoneyUnit(v);
			};
			me.moneyUnitDom.getValue = function(){
				return Number(this.par.find('.current').attr('data-value'));
			};
			me.moneyUnitDom.addEvent('afterSwitch', function(e, i){
				var multiple = me.getMultip(),
					method = Games.getCurrentGame().getCurrentGameMethod(),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Number(this.triggers.eq(i).attr('data-value')),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					maxnum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id, unit),
					prizesMultipleBound;

				multiple = multiple > maxnum ? maxnum : multiple;

				if(methodCfg['is_enable_extra'] == 1){
					prizesMultipleBound = me.getPrizesMultipleBound();
					multiple = Math.min(multiple, prizesMultipleBound['min']);
					multiple = Math.min(multiple, prizesMultipleBound['max']);
				}
				

				me.setMultipleDom(multiple);

				me.setMoneyUnit(unit);
				me.updateData({
						'lotterys':Games.getCurrentGame().getCurrentGameMethod().getLottery(),
						'original':Games.getCurrentGame().getCurrentGameMethod().getOriginal(),
						'position':Games.getCurrentGame().getCurrentGameMethod().getPositionOptionData()
					}, Games.getCurrentGame().getCurrentGameMethod().getName());
				
				//
				if(methodCfg['is_enable_extra'] == 1 && (method.getName().indexOf('hezhi.wuxing.hezhi') == 0 || method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0)){
					me.setMultiplePrizes();
				}else{
					//更新单注奖金金额
					var prize = Number(methodCfg['prize']) * unit;
					prize = bomao.util.formatMoney(prize);
					prize = prize.split('.');
					prize[1] = '<i>' + prize[1] + '</i>';
					prize = prize.join('.');
					$('#J-method-prize').html(prize);
				}

			});
			me.moneyUnitDom.setValue(me.moneyUnit);


			//初始化相关界面，使得界面和配置统一
			me.updateData({'lotterys':[], 'position':[], 'original':[]});
			

			me.initRebate();
		},
		getPrizesMultipleBound:function(){
			var method = Games.getCurrentGame().getCurrentGameMethod(),
				methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(method.getId()),
				allBallsDoms = method.getBallsDom(),
				i = 0,
				len = allBallsDoms.length,
				j = 0,
				len2,
				multipleArr = [],
				allMultipleArr = [],
				extracfg,
				unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue());

			for(var p in methodCfg['extra']){
				if(methodCfg['extra'].hasOwnProperty(p)){
					allMultipleArr.push(Number(methodCfg['extra'][p]));
				}
			}
			allMultipleArr.sort(function(a, b){
				return a - b;
			});

			extracfg = methodCfg['extra'];

			var cnHash = {
				'大':'1',
				'小':'0',
				'单':'3',
				'双':'2'
			};

			for(i = 0; i < len; i++){
				for(j = 0; j < allBallsDoms[i].length; j++){
					if(allBallsDoms[i][j].className.indexOf('ball-number-current') != -1){
						if(method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0){
							multipleArr.push(Number(extracfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
						}else{
							multipleArr.push(Number(extracfg['' + Number(allBallsDoms[i][j].innerHTML)]));
						}
					}
				}
			}
			multipleArr.sort(function(a, b){
				return a - b;
			});
			if(multipleArr.length == 1){
				return {min:multipleArr[0] / unit, max:multipleArr[0] / unit};
			}else if(multipleArr.length == 0){
				return {min:allMultipleArr[0] / unit, max:allMultipleArr[allMultipleArr.length - 1] / unit};
			}else{
				return {min:multipleArr[0] / unit, max:multipleArr[multipleArr.length - 1] / unit};
			}
		},
		setMultiplePrizes:function(){
			var method = Games.getCurrentGame().getCurrentGameMethod(),
				methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(method.getId()),
				allBallsDoms = method.getBallsDom(),
				i = 0,
				len = allBallsDoms.length,
				j = 0,
				len2,
				multipleArr = [],
				maxmultiple = 1,
				prizesArr = [],
				allprizesArr = [],
				extracfg,
				extraprizecfg,
				prizes = [],
				unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue());

			for(var p in methodCfg['extra_prize']){
				if(methodCfg['extra_prize'].hasOwnProperty(p)){
					allprizesArr.push(Number(methodCfg['extra_prize'][p]));
				}
			}
			allprizesArr.sort(function(a, b){
				return a - b;
			});

			extracfg = methodCfg['extra'];
			extraprizecfg = methodCfg['extra_prize'];

			var cnHash = {
				'大':'1',
				'小':'0',
				'单':'3',
				'双':'2'
			};

			for(i = 0; i < len; i++){
				for(j = 0; j < allBallsDoms[i].length; j++){
					if(allBallsDoms[i][j].className.indexOf('ball-number-current') != -1){
						if(method.getName().indexOf('liangmianpan.zhixuan.guanyahezhi') == 0){
							multipleArr.push(Number(extracfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
							prizesArr.push(Number(extraprizecfg[cnHash[$.trim(allBallsDoms[i][j].innerHTML)]]));
						}else{
							multipleArr.push(Number(extracfg['' + Number(allBallsDoms[i][j].innerHTML)]));
							prizesArr.push(Number(extraprizecfg['' + Number(allBallsDoms[i][j].innerHTML)]));
						}
					}
				}
			}
			prizesArr.sort(function(a, b){
				return a - b;
			});
			multipleArr.sort(function(a, b){
				return a - b;
			});
			if(prizesArr.length == 0){
				prizes.push((allprizesArr[0] * unit).toFixed(4));
				prizes.push((allprizesArr[allprizesArr.length - 1] * unit).toFixed(4));
				$('#J-method-prize').html(prizes.join(' - '));
				return;
			}
			if(prizesArr.length == 1){
				prizes.push(prizesArr.length > 0 ? (prizesArr[0] * unit).toFixed(4) : 0);
				$('#J-method-prize').html(prizes.join(' - '));
			}else{
				prizes.push(prizesArr.length > 0 ? (prizesArr[0] * unit).toFixed(4) : 0);
				prizes.push(prizesArr.length > 0 ? (prizesArr[prizesArr.length - 1] * unit).toFixed(4) : 0);
				$('#J-method-prize').html(prizes.join('-'));
			}
		},
		//初始化返点内容
		initRebate:function(){
			var me = this,
				cfg = Games.getCurrentGame().getGameConfig().getInstance(),
				subgroup = Number(cfg.getConfig('subtract_prize_group')),
				min = Number(cfg.getConfig('bet_min_prize_group')),
				max = Number(cfg.getConfig('user_prize_group')),
				umax = Number(cfg.getConfig('bet_max_prize_group')),
				ugroup = Number(cfg.getConfig('user_prize_group')),
				base = Number(cfg.getConfig('series_amount')),
				list = [],
				num = Math.floor(min/10) * 10,
				per,
				html = [],
				resultdata = [];
			list.push(min);
			while(num < max){
				if(num != min){
					list.push(num);
				}
				num += 10;
			}
			list.push(max);
			$.each(list, function(i){
				if(this <= umax){
					resultdata.push(this);
				}
			});
			$.each(resultdata, function(i){
				per = ((max - this)/base*100).toFixed(2);
				if(i == 0){
					html.push('<option value="'+ (this) +'">'+ (this) + ' - ' + per +'%</option>');
				}else if(i == resultdata.length - 1){
					html.push('<option selected="selected" value="'+ (this) +'">'+ (this) + ' - ' + per +'%</option>');
				}
			});


			$('#J-select-rebate').html(html.join(''));

			//返点
			me.rebateSelect = new host.Select({
				realDom:'#J-select-rebate',
				cls:'w-2'
			});

		},
		getMultipleDom:function(){
			return this.multipleDom;
		},
		getMultipleTextDom:function(){
			return $('#J-balls-statistics-multiple-text');
		},
		getMoneyUnitText:function(moneyUnit){
			return this.defConfig.moneyUnitData[''+moneyUnit];
		},
		//更新各种数据
		updateData:function(data, name){
			var me = this,
				cfg = me.defConfig,
				count = data['lotterys'].length,
				price = 2,
				multiple = me.multiple,
				moneyUnit = me.moneyUnit;

				if(Games.getCurrentGame() && Games.getCurrentGame().getCurrentGameMethod()){
					price = Games.getCurrentGame().getGameConfig().getInstance().getOnePriceById(Games.getCurrentGame().getCurrentGameMethod().getId());
				}

			//设置投注内容
			me.setLotteryData(data);
			//设置倍数
			//由于设置会引发updateData的死循环，因此在init里手动设置一次，之后通过change事件触发updateData
			//me.setMultipleDom(multiple);
			//更新元角模式
			//me.setMoneyUnitDom(moneyUnit);
			//更新注数
			me.setLotteryNumDom(data['lotterys'].length);
			//更新总金额
			me.setAmountDom(me.formatMoney(count * moneyUnit * multiple * price));
			//参数：注数、金额
			me.fireEvent('afterUpdate', data['lotterys'].length, count * moneyUnit * multiple * price);

		},
		//获取当前数据
		getResultData:function(){
			var me = this,
				cfg = Games.getCurrentGame().getGameConfig().getInstance(),
				subgroup = Number(cfg.getConfig('subtract_prize_group')),
				onePrice,
				method = Games.getCurrentGame().getCurrentGameMethod(),
				lotterys = me.getLotteryData();
			if(lotterys['lotterys'].length < 1){
				return {};
			}
			onePrice = Games.getCurrentGame().getGameConfig().getInstance().getOnePriceById(method.getId());
			return {
					mid:method.getId(),
					type:method.getName(),
					original:lotterys['original'],
					position:lotterys['position'],
					lotterys:lotterys['lotterys'],
					prize_group:Number(me.rebateSelect.getValue()) + subgroup,
					moneyUnit:me.moneyUnit,
					num:lotterys['lotterys'].length,
					multiple:me.multiple,
					//单价
					//onePrice:me.onePrice,
					//单价修改为从动态配置中获取，因为每个玩法有可能单注价格不一样
					onePrice:onePrice,
					//总金额
					amount:lotterys['lotterys'].length * me.moneyUnit * me.multiple * onePrice,
					//格式化后的总金额
					amountText:me.formatMoney(lotterys['lotterys'].length * me.moneyUnit * me.multiple * onePrice)
				};
		},
		//设置元角模式
		setMoneyUnit:function(num){
			var me = this;
			me.moneyUnit = num;
			me.fireEvent('setMoneyUnit_after', num);
		},
		getMoneyUnit:function(){
			return this.moneyUnit;
		},
		getLotteryData:function(){
			return this.lotteryData;
		},
		setLotteryData:function(data){
			var me = this;
			me.lotteryData = data;
		},
		//将数字保留两位小数并且千位使用逗号分隔
		formatMoney:function(num){
			var num = Number(num),
				re = /(-?\d+)(\d{3})/;

			if(Number.prototype.toFixed){
				num = (num).toFixed(2);
			}else{
				num = Math.round(num*100)/100
			}
			num  =  '' + num;
			while(re.test(num)){
				num = num.replace(re,"$1,$2");
			}
			return num;
		},
		//注数
		getLotteryNumDom:function(){
			var me = this,cfg = me.defConfig;
			return me.lotteryNumDom || (me.lotteryNumDom = $(cfg.lotteryNumDom));
		},
		setLotteryNumDom:function(v){
			var me = this;
			me.getLotteryNumDom().html(v);
		},
		//倍数
		getMultipleDom:function(){
			return this.multipleDom;
		},
		getMultip: function() {
			var me = this;
			return me.multiple;
		},
		setMultipleDom:function(v){
			var me = this;
			me.getMultipleDom().setValue(v);
		},
		setMultiple:function(num){
			this.multiple = num;
		},
		//元角模式
		getMoneyUnitDom:function(){
			return this.moneyUnitDom;
		},
		setMoneyUnitDom:function(v){
			var me = this;
			me.getMoneyUnitDom().setValue(v);
		},
		hidesetMoneyUnitDom: function(){
			this.moneyUnitDom.hide();
		},
		//总金额
		getAmountDom:function(){
			var me = this,cfg = me.defConfig;
			return me.amountDom || (me.amountDom = $(cfg.amountDom));
		},
		setAmountDom:function(v){
			var me = this;
			me.getAmountDom().html(v);
		},
		reSet:function(){
			var me = this,cfg = me.defConfig;
			me.multipleDom.setValue(cfg.multiple);
			//me.moneyUnitDom.setValue(cfg.moneyUnit);
		}


	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;

})(bomao, "GameStatistics", bomao.Event);











//游戏订单模块
(function(host, name, Event, undefined) {
	var defConfig = {
			//事件监听容器
			containerEvent: '#J-panel-order-list-cont',
			//主面板dom
			containerDom: '#J-balls-order-container',
			//总注数dom
			totalLotterysNumDom: '#J-gameOrder-lotterys-num',
			//总金额dom
			totalAmountDom: '#J-gameOrder-amount',
			//当注单被选中时的样式
			selectedClass: 'game-order-current',
			//每行投注记录html模板
			//rowTemplate: '<li data-param="action=reselect&id=<#=id#>" id="gameorder-<#=id#>"><div class="result"><span class="moneyUnitText"><#=moneyUnitText#></span><span class="bet"><#=num#>注</span><span class="multiple"><#=multiple#>倍</span><span class="price"><span>&yen;</span><#=amountText#></span><span class="close"><a data-param="action=del&id=<#=id#>" href="javascript:void(0);" title="删除">删除</a></span></div><span>[<#=typeText#>]</span><span><#=lotterysText#></span></li>',
			rowTemplate: '<li data-param="action=reselect&id=<#=id#>&mid=<#=mid#>" id="gameorder-<#=id#>"><span data-param="action=reselect&id=<#=id#>" class="name" title="<#=typeText#>"><#=typeText#></span><span data-param="action=reselect&id=<#=id#>" class="number" title="<#=lotterysText#>"><#=lotterysText#></span><span data-param="action=reselect&id=<#=id#>" class="bet"><#=num#></span><span data-param="action=reselect&id=<#=id#>" class="multiple"><#=multiple#></span><span data-param="action=reselect&id=<#=id#>" class="price"><#=amountText#></span><a data-param="action=del&id=<#=id#>" href="javascript:void(0);" title="删除" class="delete"></a></li>',
			//显示内容截取字符串长度
			lotterysTextLength: 40,
			//投注按钮Dom
			addOrderDom: '#J-add-order'
		},

		//获取当前游戏
		Games = host.Games,
		instance,
		orderID = 1,
		Ts = Object.prototype.toString;
	//将来仿url类型的参数转换为{}对象格式，如 q=wahaha&key=323444 转换为 {q:'wahaha',key:'323444'}
	//所有参数类型均为字符串
	var formatParam = function(param) {
		var arr = $.trim(param).split('&'),
			i = 0,
			len = arr.length,
			paramArr,
			result = {};
		for (; i < len; i++) {
			paramArr = arr[i].split('=');
			if (paramArr.length > 0) {
				if (paramArr.length == 2) {
					result[paramArr[0]] = paramArr[1];
				} else {
					result[paramArr[0]] = '';
				}
			}
		}
		return result;
	};

	var pros = {
		init: function(cfg) {
			var me = this,
				cfg = me.defConfig;
			me.cacheData = {};
			me.cacheData['detailPostParameter'] = {};
			me.orderData = [];
			Games.setCurrentGameOrder(me);
			me.container = $(cfg.containerDom);
			me.containerEvent = $(cfg.containerEvent);
			me.totalLotterysNum = 0;
			me.totalLotterysNumDom = $(cfg.totalLotterysNumDom);
			me.totalAmount = 0.00;
			me.totalAmountDom = $(cfg.totalAmountDom);
			me.currentSelectId = 0;

			me.eventProxy();

			//当添加数据发生时，触发追号面板相关变更
			me.addEvent('afterAdd', function() {
				var tableType = Games.getCurrentGameTrace().getRowTableType();
				if (Games.getCurrentGameTrace().getIsTrace() == 1) {
					Games.getCurrentGameTrace().autoDeleteTrace();
				}
			});
			//删除
			me.addEvent('afterRemoveData', function() {
				var tableType = Games.getCurrentGameTrace().getRowTableType();
				if (Games.getCurrentGameTrace().getIsTrace() == 1) {
					Games.getCurrentGameTrace().autoDeleteTrace();
				}
			});
			//清空
			me.addEvent('afterResetData', function() {
				var tableType = Games.getCurrentGameTrace().getRowTableType();
				if (Games.getCurrentGameTrace().getIsTrace() == 1) {
					Games.getCurrentGameTrace().autoDeleteTrace();
				}
			});

			//当发生玩法面板切换时，触发取消注单的选择状态
			Games.getCurrentGameTypes().addEvent('endChange', function() {
				me.cancelSelectOrder();
			});

		},
		setTotalLotterysNum: function(v) {
			var me = this,
				oldNum = me.totalLotterysNum;
			me.totalLotterysNum = Number(v);
			me.totalLotterysNumDom.html(v);
			if (oldNum != me.totalLotterysNum) {
				me.fireEvent('afterChangeLotterysNum', me.totalLotterysNum);
			}
		},
		setTotalAmount: function(v) {
			var me = this,
				oldAmout = me.totalAmount;
			me.totalAmount = Number(v);
			me.totalAmountDom.html(me.formatMoney(v));
			if (oldAmout != me.totalAmount) {
				me.fireEvent('afterChangeAmout', me.totalAmount);
			}
		},
		addData: function(order) {
			var me = this;
			me.orderData.unshift(order);
		},
		getOrderById: function(id) {
			var me = this,
				id = Number(id),
				orderData = me.orderData,
				i = 0,
				len = orderData.length;

			for (i = 0; i < len; i++) {
				if (Number(orderData[i]['id']) == id) {
					return orderData[i];
				}
			}
		},
		removeData: function(id) {
			var me = this,
				id = Number(id),
				data = me.orderData,
				i = 0,
				len = data.length;
			for (; i < len; i++) {
				if (data[i]['id'] == id) {
					me.fireEvent('beforeRemoveData', data[i]);
					me.orderData.splice(i, 1);
					me.updateData();
					me.fireEvent('afterRemoveData');
					break;
				}
			}
			$('#gameorder-' + id).remove();
			me.fireEvent('afterRemoveData');
		},
		reSet: function() {
			var me = this;

			me.container.empty();
			me.orderData = [];
			me.updateData();
			me.fireEvent('afterResetData');

			return me;
		},
		updateData: function() {
			var me = this,
				total = me.getTotal();
			//
			//显示所有订单信息.......
			//方案注数 1000注，金额 ￥2000.00 元
			me.setTotalLotterysNum(total['count']);
			me.setTotalAmount(total['amount']);
		},
		getTotal: function() {
			var me = this,
				data = me.orderData,
				i = 0,
				len = data.length,
				count = 0,
				amount = 0;
			for (; i < len; i++) {
				count += data[i]['num'];
				amount += (data[i]['num'] * data[i]['onePrice'] * data[i]['moneyUnit'] * data[i]['multiple']);
			}
			return {
				'count': count,
				'amount': amount,
				'orders': data
			};
		},
		//获取订单允许设置的最大倍数(通过获取每个玩法倍数限制的最小值)
		//返回值 {gameMethod:'玩法名称',maxnum:999}
		getOrderMaxMultiple: function() {
			var me = this,
				methodCfg, limit, orders = me.getTotal()['orders'],
				i = 0,
				type, len = orders.length,
				mid, multiple,
				arr = [],
				typeText = '',
				maxNum;
			for (; i < len; i++) {
				mid = orders[i]['mid'];
				multiple = orders[i]['multiple'];
				methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(mid);
				type = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullNameById(mid);
				if (!methodCfg || !methodCfg['max_multiple']) {
					typeText = Games.getCurrentGame().getGameConfig().getInstance().getMethodCnFullNameById(mid).join('');
					alert('[' + typeText + ']\n玩法未配置奖金组中奖金额，投注倍数未能得到限制\n请配置该玩法相关配置');
					return;
				}
				limit = methodCfg['max_multiple'];
				if(methodCfg['is_enable_extra'] == 1){
					limit = me.getOrderPrizesMaxMultiple(methodCfg, orders[i]);
				}
				maxNum = Number(limit) < 0 ? 99999999 : (Number(limit) * (1/orders[i]['moneyUnit']));
				arr.push({
					'gameMethod': type,
					'maxnum': Math.floor(maxNum / multiple)
				});
			}
			arr.sort(function(a, b) {
				return a['maxnum'] - b['maxnum'];
			});
			if (arr.length > 0) {
				return arr[0];
			} else {
				return {
					'gameMethod': '',
					'maxnum': 100000000
				}
			}
		},
		getOrderPrizesMaxMultiple:function(methodCfg, order){
			var balls = order['lotterys'].join('|').split('|'),
				extraHash = methodCfg['extra'],
				arr = [],
				i,
				len = balls.length;
			for(i = 0; i < len; i++){
				if(extraHash[balls[i]]){
					arr.push(Number(extraHash[balls[i]]));
				}
			}
			arr.sort(function(a, b){
				return a - b;
			});
			return arr[0];
		},
		//添加一条投注
		//order 参数可为单一对象或数组
		//接收参数 order {type:'玩法类型',lotterys:'投注具体数据',moneyUnit:'元角模式',num:'注数',multiple:'倍数',onePrice:'单价'}
		add: function(order) {
			var me = this,
				html = '',
				sameIndex = -1,
				tpl = me.defConfig.rowTemplate,
				i = 0,
				j = 0,
				isTrace = Games.getCurrentGameTrace().getIsTrace(),
				len,
				len2;

			//var time = new Date();

			me.fireEvent('beforeAdd', order);



			if (order['lotterys'] && order['lotterys'].length > 0) {

				//判断是否为编辑注单
				if (me.currentSelectId > 0) {
					order['id'] = me.currentSelectId;
				} else {
					sameIndex = me.checkData(order);
					//发现有相同注，则增加倍数
					if (sameIndex != -1) {
						Games.getCurrentGameMessage().show({
							type: 'normal',
							closeText: '确定',
							closeFun: function() {
								me.addMultiple(order['multiple'], sameIndex);
								this.hide();
							},
							data: {
								tplData: {
									msg: '您选择的号码在号码篮已存在，将直接进行倍数累加'
								}
							}
						});
						return;
					}
					//新增唯一id标识
					order['id'] = orderID++;
				}

				//如果追号面板被打开，则修改倍数为1倍 (修改说明：机制修改为当修改注单时，自动取消追号)
				//order['multiple'] = !!isTrace ? 1 : order['multiple'];
				order['amountText'] = me.formatMoney(order['num'] * order['moneyUnit'] * order['multiple'] * order['onePrice']);
				//如果追号面板打开，并且正在操作盈利追号或盈利率追号，则不允许进行混投
				//清空所有追号列表
				if (!!isTrace && (Games.getCurrentGameTrace().getRowTableType() == 'yingli' || Games.getCurrentGameTrace().getRowTableType() == 'yinglilv')) {
					//不允许混投
					for (j = 0, len2 = me.orderData.length; j < len2; j++) {
						if (me.orderData[j]['type'] != order['type'] || me.orderData[j]['moneyUnit'] != order['moneyUnit']) {
							alert('盈利追号和盈利率追号不允许混投，\n 请确保玩法类型和元角模式一致');
							return;
						}
					}
				}
				//原始选球数据
				order['postParameter'] = Games.getCurrentGame().getCurrentGameMethod().makePostParameter(order['original'], order);
				//倍数备份，用于恢复原始选择的倍数
				order['oldMultiple'] = order['multiple'];

				

				html = me.formatRow(tpl, me.rebuildData(order));

				//console.log((new Date()) - time);



				//是修改，则替换原有的order对象
				if (me.currentSelectId > 0) {
					me.replaceOrder(order['id'], order);
				} else {
					me.addData(order);
				}

			} else {
				return;
			}


			//如果是修改注单则删除原有的dom
			if (me.currentSelectId > 0) {
				$(html).replaceAll($('#gameorder-' + me.currentSelectId));
				me.cancelSelectOrder();
			} else {
				$(html).prependTo(me.container);
			}

			//复位选球区
			Games.getCurrentGame().getCurrentGameMethod().reSet();

			Games.getCurrentGameStatistics().reSet();

			me.updateData();
			me.fireEvent('afterAdd', order);

			

		},
		//快速提交下单
		fastOrder:function(order){
			var me = this;
			
			//新增唯一id标识
			order['id'] = orderID++;
			//原始选球数据
			order['postParameter'] = Games.getCurrentGame().getCurrentGameMethod().makePostParameter(order['original'], order);


			//复位选球区
			Games.getCurrentGame().getCurrentGameMethod().reSet();
			Games.getCurrentGameStatistics().reSet();
			me.updateData();
			
			order = me.rebuildData(order);
			
			Games.getCurrentGameSubmit().fastSubmitData(order);

		},
		//替换某个Order注单对象
		replaceOrder: function(id, newOrder) {
			var me = this,
				orders = me.orderData,
				i = 0,
				len = orders.length;
			for (; i < len; i++) {
				if (orders[i]['id'] == id) {
					orders[i] = newOrder;
					return;
				}
			}
		},
		render: function() {
			var me = this,
				orders = me.getTotal()['orders'],
				i = 0,
				len = orders.length,
				html = [],
				tpl = me.defConfig.rowTemplate;
			for (; i < len; i++) {
				html[i] = me.formatRow(tpl, me.rebuildData(orders[i]));
			}
			me.updateData();
			me.container.html(html.join(''));
		},
		//填充其他数据用户界面显示
		//格式化后的数据 {typeText:'玩法类型名称',type:'玩法类型名称(英文)',lotterys:'投注具体内容',lotterysText:'显示投注具体内容的文本',moneyUnit:'元角模式',moneyUnitText:'显示圆角模式文字',num:'注数',multiple:'倍数',amount:'总金额',amountText:'显示的总金额',onePrice:'单价'}
		rebuildData: function(order) {
			var me = this,
				cfg = me.defConfig,
				gameConfig = Games.getCurrentGame().getGameConfig().getInstance(),
				typeText = gameConfig.getMethodCnFullNameById(order['mid']).join(','),
				method = Games.getCurrentGame().getCacheMethod(order['mid']);

			order['typeText'] = typeText;
			//order['lotterysText'] = order['postParameter'];
			order['lotterysText'] = method.formatViewBalls(order['original']).substr(0, 200);
			order['viewBalls'] = order['lotterysText'];

			order['moneyUnitText'] = '元';

			return order;
		},
		formatRow: function(tpl, order) {
			var me = this,
				o = order,
				p, reg;
			for (p in o) {
				if (o.hasOwnProperty(p)) {
					reg = RegExp('<#=' + p + '#>', 'g');
					tpl = tpl.replace(reg, o[p]);
				}
			}
			return tpl;
		},
		//从投注结果返回原始数据
		//用来向后台POST原始结果
		originalData: function(data) {

			var me = this,
				v = [];
			for (var i = 0; i < data.length; i++) {
				for (var j = 0; j < data[i].length; j++) {
					v[j] = v[j] || [];
					if (!me.arrIndexOf(data[i][j], v[j])) {
						v[j].push(data[i][j]);
					}
				}
			}
			return v;
		},
		//检查数组存在某数
		arrIndexOf: function(value, arr) {
			var r;
			for (var s = 0; s < arr.length; s++) {
				if (arr[s] == value) {
					r = true;
				};
			}
			return r || false;
		},
		/**
		 * [判断参数是否重复]
		 * @return {[type]} [description]
		 */
		checkData: function(order) {
			var original, current, name,
				me = this,
				saveArray = [],
				i = 0,
				_index,
				len;
			name = order['type'];
			original = order['original'];
			for (var i = 0; i < original.length; i++) {
				saveArray.push(original[i].join(''));
			};
			moneyUnit = order['moneyUnit'];
			//返回对象在数组的索引值index
			//未找到返回-1
			return me.searchSameResult(name, saveArray.join(), moneyUnit, order['position']);
		},
		eventProxy: function() {
			var me = this,
				panel = me.containerEvent;
			panel.on('click', function(e) {
				var q = e.target.getAttribute('data-param'),
					param;
				if (q && $.trim(q) != '') {
					param = formatParam(q);
					if ($.isFunction(me['exeEvent_' + param['action']])) {
						me['exeEvent_' + param['action']].call(me, param, e.target);
					}
				}
			});
		},
		exeEvent_del: function(param) {
			var me = this,
				id = Number(param['id']);
			if (me.currentSelectId == id) {
				Games.getCurrentGame().getCurrentGameMethod().reSet();
				me.cancelSelectOrder();
			}
			me.removeData(id);
		},
		exeEvent_detailhide: function(params, el) {
			$(el).parents('.lottery-details-area').eq(0).hide();
		},
		exeEvent_detail: function(param, el) {
			var me = this,
				el = $(el),
				index = Number(param['id']),
				id = index,
				dom = el.next(),
				multipleArea = dom.find('.multiple'),
				result = dom.find('.list'),
				currentData = me.getTotal().orders,
				html = '';


			//隐藏之前打开的内容容器
			//避免遍历
			if (me.cacheData['currentDetailId']) {
				$('#gameorder-' + me.cacheData['currentDetailId'] + ' .lottery-details-area').hide();
			}
			//判断是否有缓存结果
			if (me.cacheData['detailPostParameter'][id]) {
				html = me.cacheData['detailPostParameter'][id];
				//缓存面板
				me.cacheData['currentDetailId'] = id;
			} else {
				//获取结果
				for (var i = currentData.length - 1; i >= 0; i--) {
					if (currentData[i]['id'] == index) {
						currentData = currentData[i];
						break;
					}
				}
				//填充结果
				multipleArea.text('共 ' + currentData.num + ' 注');
				html = currentData['postParameter'];
				//缓存面板
				me.cacheData['currentDetailId'] = id;
				//缓存结果
				me.cacheData['detailPostParameter'][id] = html;
				//位置调整
				dom.css({
					left: dom.position().left + dom.width() + 5
				});
			}
			//渲染DOM
			result.html(html);
			//显示结果
			dom.show();
		},
		//号码篮点击事件
		exeEvent_reselect: function(param) {
			var me = this,id = Number(param['id']);
			me.selectOrderById(id);
		},
		//界面状态更新
		updateDomStatus: function() {
			var me = this,
				className = 'button-game-edit',
				id = me.currentSelectId,
				addOrderButtonDom = $(me.defConfig.addOrderDom);

			if (id > 0) {
				//设置添加投注按钮样式
				addOrderButtonDom.addClass(className);
			} else {
				addOrderButtonDom.removeClass(className);
			}
		},
		//选择一个注单
		selectOrderById: function(id) {
			var me = this,
				order = me.getOrderById(id),
				original = order['original'],
				position = order['position'],
				type = order['type'],
				cls = me.defConfig.selectedClass,
				dom = $('#gameorder-' + id);

			//单式不能反选
			if (me.getOrderById(id)['type'].indexOf('danshi') != -1) {
				return;
			}

			//修改选中样式
			dom.parent().children().removeClass(cls);
			dom.addClass(cls);

			//反选球
			//切换玩法面板
			Games.getCurrentGameTypes().changeMode(order['mid']);

			//设置倍数、元角模式
			Games.getCurrentGameStatistics().getMultipleDom().setValue(order['multiple']);
			Games.getCurrentGameStatistics().getMoneyUnitDom().setValue(order['moneyUnit']);

			//反选球
			Games.getCurrentGame().getCurrentGameMethod().reSelect(original, position);

			//标记当前选中注单
			me.currentSelectId = id;

			//更新界面
			me.updateDomStatus();

			//反选后将滚动条位置移动到合适位置
			//$(window).scrollTop($('#J-play-select').offset()['top']);
		},
		//取消选择的注单
		cancelSelectOrder: function() {
			var me = this,
				id = me.currentSelectId,
				addOrderButtonDom = $(me.defConfig.addOrderDom);

			if (id > 0) {
				$('#gameorder-' + id).removeClass(me.defConfig.selectedClass);
				me.currentSelectId = 0;
				//更新界面
				me.updateDomStatus();

				Games.getCurrentGame().getCurrentGameMethod().reSet();
			}
		},
		//将数字保留两位小数并且千位使用逗号分隔
		formatMoney: function(num) {
			var num = Number(num),
				re = /(-?\d+)(\d{3})/;

			if (Number.prototype.toFixed) {
				num = (num).toFixed(2);
			} else {
				num = Math.round(num * 100) / 100
			}
			num = '' + num;
			while (re.test(num)) {
				num = num.replace(re, "$1,$2");
			}
			return num;
		},
		/**
		 * 查询同类玩法重复结果
		 * @param  {string} name [游戏玩法 例:wuxing.zhixuan.danshi]
		 * @param  {string} data [投注号码 例:12345]
		 */
		searchSameResult: function(name, lotteryText, moneyUnit, position) {
			var me = this,
				current, dataNum,
				i = 0,
				saveArray = [],
				pos,
				isSamePosition = true,
				data = me.getTotal().orders;

			for (; i < data.length; i++) {
				saveArray = [];
				current = data[i];
				ordersLotteryText = current['original'];
				for (var k = 0; k < ordersLotteryText.length; k++) {
					saveArray.push(ordersLotteryText[k].join(''));
				};

				pos = data[i]['position'];
				if(pos.length != position.length){
					isSamePosition = false;
				}else{
					$.each(pos, function(j){
						if(pos[j] != position[j]){
							isSamePosition = false;
							return false;
						}
					})
				}

				if (isSamePosition && current.type == name && lotteryText == saveArray.join() && current.moneyUnit == moneyUnit) {
					return i;
				}
			}
			return -1;
		},
		//增加某注倍数
		addMultiple: function(num, index) {
			var me = this,
				orders = me.getTotal()['orders'],
				order = orders[index],
				type = order['type'],
				id = order['mid'],
				maxNum = 999999;
			if (Games.getCurrentGameTrace().getIsTrace() == 1) {
				return;
			}
			maxNum = Games.getCurrentGame().getGameConfig().getInstance().getLimitByMethodId(id);
			maxNum = maxNum < 0 ? 999999999 : maxNum;

			if ((order['multiple'] + num) > maxNum) {
				setTimeout(function() {
					Games.getCurrentGameMessage().show({
						type: 'normal',
						closeText: '确定',
						closeFun: function() {

							orders[index]['multiple'] = maxNum
							orders[index]['oldMultiple'] = orders[index]['multiple'];
							orders[index]['amount'] = orders[index]['num'] * orders[index]['moneyUnit'] * orders[index]['multiple'] * orders[index]['onePrice'];
							orders[index]['amountText'] = me.formatMoney(orders[index]['num'] * orders[index]['moneyUnit'] * orders[index]['multiple'] * orders[index]['onePrice']);
							me.render();

							//复位选球区
							Games.getCurrentGame().getCurrentGameMethod().reSet();
							//游戏错误提示
							//主要用于单式投注进行错误提示
							Games.getCurrentGame().getCurrentGameMethod().ballsErrorTip();
							Games.getCurrentGameStatistics().reSet();

							this.hide();
						},
						data: {
							tplData: {
								msg: '该组号码倍数已经超过最大限制(' + maxNum + '倍)，将调整为系统支持的最大倍数进行添加'
							}
						}
					});
				}, 100);
				return;
			}



			orders[index]['multiple'] += num;
			orders[index]['oldMultiple'] = orders[index]['multiple'];
			orders[index]['amount'] = orders[index]['num'] * orders[index]['moneyUnit'] * orders[index]['multiple'] * orders[index]['onePrice'];
			orders[index]['amountText'] = me.formatMoney(orders[index]['num'] * orders[index]['moneyUnit'] * orders[index]['multiple'] * orders[index]['onePrice']);
			me.render();

			//复位选球区
			Games.getCurrentGame().getCurrentGameMethod().reSet();
			//游戏错误提示
			//主要用于单式投注进行错误提示
			Games.getCurrentGame().getCurrentGameMethod().ballsErrorTip();
			Games.getCurrentGameStatistics().reSet();

			me.cancelSelectOrder();
		},
		//修改所有投注倍数
		editMultiples: function(num) {
			var me = this,
				orders = me.getTotal()['orders'],
				i = 0,
				len = orders.length;
			for (; i < len; i++) {
				orders[i]['multiple'] = num;
				orders[i]['amount'] = orders[i]['num'] * orders[i]['moneyUnit'] * orders[i]['multiple'] * orders[i]['onePrice'];
				orders[i]['amountText'] = me.formatMoney(orders[i]['amount']);
			}
			me.render();

			me.cancelSelectOrder();
		},
		//修改单注投注倍数
		editMultiple: function(num, index) {
			var me = this,
				orders = me.getTotal()['orders'];
			orders[index]['multiple'] = num;
			orders[index]['amount'] = orders[index]['num'] * orders[index]['moneyUnit'] * orders[index]['multiple'] * orders[index]['onePrice'];
			orders[index]['amountText'] = me.formatMoney(orders[i]['amount']);
			me.render();

			me.cancelSelectOrder();
		},
		//恢复原来的投注的倍数
		restoreMultiples: function() {
			var me = this,
				orders = me.getTotal()['orders'],
				i = 0,
				len = orders.length;
			for (; i < len; i++) {
				orders[i]['multiple'] = orders[i]['oldMultiple'];
				orders[i]['amount'] = orders[i]['num'] * orders[i]['moneyUnit'] * orders[i]['multiple'] * orders[i]['onePrice'];
				orders[i]['amountText'] = me.formatMoney(orders[i]['amount']);
			}
			me.render();

			me.cancelSelectOrder();
		}
	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	Main.getInstance = function(cfg) {
		return instance || (instance = new Main(cfg));
	};
	host[name] = Main;

})(bomao, "GameOrder", bomao.Event);

//追号区域
(function(host, name, Event, undefined){
	var defConfig = {
		//主面板dom
		mainPanel:'#J-trace-panel',
		//高级追号类型(与tab顺序对应)
		advancedTypeHas:['fanbei','yingli','yinglilv'],
		//追号数据表头
		dataRowHeader:'<tr><th style="width:50px;" class="text-center">序号</th><th><input data-action="checkedAll" type="checkbox"  checked="checked"/> 追号期次</th><th>倍数</th><th>金额</th><th>预计开奖时间</th></tr>',
		//追号数据列表模板
		dataRowTemplate:'<tr><td class="text-center"><#=No#></td><td><input data-action="checkedRow" class="trace-row-checked" type="checkbox" checked="checked"> <span class="trace-row-number"><#=traceNumber#></span></td><td><input class="trace-row-multiple input" value="<#=multiple#>" type="text" style="width:30px;text-align:center;"></td><td><span class="trace-row-money"><#=money#></span></td><td><span class="trace-row-time"><#=publishTime#></span></td></tr>',
		//高级追号盈利金额追号/盈利率追号表模板
		dataRowYingliHeader:'<tr><th class="text-center">序号</th><th><input data-action="checkedAll" type="checkbox" checked="checked" /> 追号期次</th><th>倍数</th><th>金额</th><th>奖金</th><th>盈利金额</th><th>盈利率</th></tr>',
		//lirunlv
		dataRowLirunlvTemplate:'<tr><td class="text-center"><#=No#></td><td><input data-action="checkedRow" class="trace-row-checked" type="checkbox" checked="checked"> <span class="trace-row-number"><#=traceNumber#></span></td><td><input class="trace-row-multiple" value="<#=multiple#>" type="text" style="width:30px;text-align:center;"></td><td><span class="trace-row-money"><#=money#></span></td><td><span class="trace-row-userGroupMoney"><#=userGroupMoney#></span></td><td><span class="trace-row-winTotalAmount"><#=winTotalAmout#></span></td><td><span class="trace-row-yinglilv"><#=yinglilv#></span>%</td></tr>'
	},
	instance,
	Games = host.Games;

	//只允许输入正整数
	//v 值
	//def 默认值
	//mx 最大值
	var checkInputNum = function(v, def, mx){
		var v = ''+v,mx = mx || 1000000000;
		v = v.replace(/[^\d]/g, '');
		v = v == '' ? def : (Number(v) >  mx ? mx : v);
		return Number(v);
	};

	//只允许输入正整数
	var checkInputNumber = function(v){
		v = v.replace(/[^\d]/g, '');
		return Number(v);
	};


	var pros = {
		init:function(cfg){
			var me = this;
			Games.setCurrentGameTrace(me);
			me.panel = $(cfg.mainPanel);

			//追号tab
			me.TraceTab = null;
			//高级追号tab
			me.TraceAdvancedTab = null;

			//订单数据
			me.orderData = null;

			//公共属性部分
			//追号类型，普通追号 高级追号
			me.traceType = 'lirunlv';
			//追号期数
			me.times = 0;
			//追号起始期号
			me.traceStartNumber = '';
			//当前期号
			me.currentTraceNumber = '';
			//是否有追号
			me.isTrace = 0;
			//追号信息的缓存
			me.savedTraceData = null;

			//普通追号属性


			//高级追号属性
			//高级追号类型
			me.advancedType = cfg.advancedTypeHas[0];
			me.typeTypeType = 'a';


			me.initEvent();
			me.setCurrentTraceNumber();


			//配置更新后追号面板相关更新
			//重新构建期号选择列表
			Games.getCurrentGame().addEvent('changeDynamicConfig', function(){
				me.buildStartNumberSelectDom();
				me.updateTableNumber();
			});


		},
		getIsTrace:function(){
			return this.isTrace;
		},
		setIsTrace:function(v){
			this.isTrace = Number(v);
		},
		setAdvancedType:function(i){
			if(Object.prototype.toString.call(i) == '[object Number]'){
				this.advancedType = this.getAdvancedTypeBuIndex(i);
			}else{
				this.advancedType = i;
			}
		},
		getAdvancedType:function(){
			return this.advancedType;
		},
		getAdvancedTypeBuIndex:function(i){
			var me = this,has = me.defConfig.advancedTypeHas,len = has.length;
			if(i < len){
				return has[i];
			}
			return '';
		},
		initEvent:function(){
			var me = this;

			//追号tab
			me.TraceTab = new host.Tab({par:'#J-trace-panel',triggers:'.chase-tab-t',panels:'.chase-tab-content',currPanelClass:'chase-tab-content-current',eventType:'click'});
			me.TraceTab.addEvent('afterSwitch', function(e, i){
					var types = ['lirunlv', 'tongbei','fanbei'];
					if(i < types.length){
						me.setTraceType(types[i]);
					}
					me.updateStatistics();
				});
			//高级追号tab
			me.TraceAdvancedTab = new host.Tab({par:'#J-trace-advanced-type-panel',triggers:'.tab-title li',panels:'.tab-content li',eventType:'click'});
			me.TraceAdvancedTab.addEvent('afterSwitch', function(e, i){
					var ipts = this.getPanel(i).find('.trace-advanced-type-switch');
					me.setAdvancedType(i);
					ipts.each(function(){
						if(this.checked){
							me.setTypeTypeType($(this).parent().attr('data-type'));
							return false;
						}
					});
				});


			//追中即停说明提示
			var TraceTip1 = new host.Hover({triggers:'#J-trace-iswintimesstop-hover',panels:'#chase-stop-tip-1',currPanelClass:'chase-stop-tip-current',hoverDelayOut:300});
			$('#chase-stop-tip-1').mouseleave(function(){
				TraceTip1.hide();
			});
			var TraceTip2 = new host.Hover({triggers:'#J-trace-iswinstop-hover',panels:'#chase-stop-tip-2',currPanelClass:'chase-stop-tip-current',hoverDelayOut:300});
			$('#chase-stop-tip-2').mouseleave(function(){
				TraceTip2.hide();
			});
			$('#J-chase-stop-switch-1').click(function(e){
				e.preventDefault();
				$('#J-trace-iswintimesstop-panel').hide();
				$('#J-trace-iswinstop-panel').show();
				$('#J-trace-iswintimesstop').get(0).checked = false;
				$('#J-trace-iswinstop').get(0).checked = true;
				$('#J-trace-iswinstop-money').removeAttr('disabled');
				$('#J-trace-iswintimesstop-times').attr('disabled', 'disabled');
			});
			$('#J-chase-stop-switch-2').click(function(e){
				e.preventDefault();
				$('#J-trace-iswinstop-panel').hide();
				$('#J-trace-iswintimesstop-panel').show();
				$('#J-trace-iswintimesstop').get(0).checked = true;
				$('#J-trace-iswinstop').get(0).checked = false;
				$('#J-trace-iswinstop-money').attr('disabled', 'disabled');
				$('#J-trace-iswintimesstop-times').removeAttr('disabled');
			});
			$('#J-trace-iswinstop-money').keyup(function(){
				this.value = checkInputNum(this.value, 1, 999999);
			});
			$('#J-trace-iswintimesstop-times').keyup(function(){
				this.value = checkInputNum(this.value, 1, 999999);
			});

			//是否止盈追号(按中奖次数)
			$('#J-trace-iswintimesstop').click(function(){
				var ipt = $('#J-trace-iswintimesstop-times');
				if(this.checked){
					ipt.attr('disabled', false).focus();
				}else{
					ipt.attr('disabled', 'disabled');
				}
			});
			//是否止盈追号(按中奖金额)
			$('#J-trace-iswinstop').click(function(){
				var ipt = $('#J-trace-iswinstop-money');
				if(this.checked){
					ipt.attr('disabled', false).focus();
				}else{
					ipt.attr('disabled', 'disabled');
				}
			});

			//普通追号事件
			//普通追号Input输入事件
			$('#J-trace-normal-times').keyup(function(){
				var	maxnum = Games.getCurrentGame().getGameConfig().getInstance().getTraceMaxTimes(),
					v = '' + this.value,
					num,
					list = $('#J-function-select-tab').find('.function-select-title li'),
					cls = 'current';
				v = v.replace(/[^\d]/g, '');
				v = v == '' ? 1 : (Number(v) >  maxnum ? maxnum : v);
				this.value = v;
				num = Number(v);
				//修改追号期数选项样式
				if(num > 0 && num <= 20 && (num%5 == 0)){
					list.removeClass(cls).eq(num/5 - 1).addClass(cls);
				}
				me.buildDetail();
			});
			/**
			$('#J-trace-normal-times').blur(function(){
				me.buildDetail();
			});
			**/
			//期数选择操作
			var NormalSelectTimesTab = new host.Tab({par:'#J-function-select-tab',triggers:'.function-select-title li',panels:'.function-select-panel li',eventType:'click',index:1});
				NormalSelectTimesTab.addEvent('afterSwitch', function(e, i){
					var tab = this,num = parseInt(tab.getTrigger(i).text());
					$('#J-trace-normal-times').val(num);
					me.buildDetail();
				});

			/**
			//倍数模拟下拉框
			me.normalSelectMultiple = new host.Select({realDom:'#J-trace-normal-multiple',isInput:true,expands:{inputEvent:function(){
														var me = this;
														me.getInput().keyup(function(e){
															var v = this.value,
																maxnum = 99999;
															this.value = this.value.replace(/[^\d]/g, '');
															v = Number(this.value);
															if(v < 1){
																this.value = 1;
															}
															if(v > maxnum){
																this.value = maxnum;
															}
															me.setValue(this.value);
														});
													}}});
			me.normalSelectMultiple.addEvent('change', function(e, value, text){
				var amount = me.getOrderData()['amount'],num = Number(value),maxObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),maxnum = maxObj['maxnum'],Msg = Games.getCurrentGameMessage(),
					typeTitle = '';

				if(num > maxnum){
					typeTitle = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullName(maxNumltipleObj['gameMethod']).join('-');


					alert('您输入的倍数超过了['+ typeTitle + '] 玩法的最高倍数限制，\n\n系统将自动修改为最大可输入倍数');
					value = maxnum;
					me.normalSelectMultiple.setValue(value);
					Msg.hide();

					me.getTable().find('.trace-row-multiple').val(value);
					me.getTable().find('.trace-row-money').each(function(){
						var el = $(this),multiple = Number(el.parent().parent().find('.trace-row-multiple').val());
						el.html(me.formatMoney(amount * Number(value)));
					});
					me.updateStatistics();
				}else{
					me.getTable().find('.trace-row-multiple').val(value);
					me.getTable().find('.trace-row-money').each(function(){
						var el = $(this),multiple = Number(el.parent().parent().find('.trace-row-multiple').val());
						el.html(me.formatMoney(amount * Number(value)));
					});
					me.updateStatistics();
				}


			});
			**/

			//数据行限制输入正整数,(可清空,失焦自动填充一倍.首字符不能为0,单选框没选中禁用，选中初始1倍值)
			me.panel.find('.chase-table').keyup(function(e){
				var el = $(e.target),amount = me.getOrderData()['amount'];
				if(el.hasClass('trace-row-multiple')){ //处理当删除注数时，追号倍数不限制

					var multiple = Number(checkInputNumber(el.val())),
						tableType = me.getRowTableType(),
						maxnum = Number(Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']);
					if(multiple == 0){
						el.val(el.val().replace(/^0/g, ''));
						me.updateStatistics();
					}
					else if(multiple > maxnum){
						el.val(maxnum);

					}else{
						el.parent().parent().find('.trace-row-money').html(me.formatMoney(amount * multiple));
						el.val(multiple);
						//如果是盈利追号和盈利率追号，则需要重新计算盈利金额和盈利率
						if(tableType == 'trace_advanced_yingli_a' || tableType == 'trace_advanced_yingli_b' || tableType == 'trace_advanced_yinglilv_a' || tableType == 'trace_advanced_yinglilv_b'){
							me.rebuildYinglilvRows();
						}
						me.updateStatistics();
					}
				}
			}).on('blur', '.trace-row-multiple', function(e){
				var el = $(e.target);
				el.val(checkInputNum(el.val(), 1, Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']));
				me.updateStatistics();
			});




			//高级追号事件
			//创建期号列表
			setTimeout(function(){
				me.buildStartNumberSelectDom();
			}, 10);


			//追号期数
			$('#J-trace-advanced-times').keyup(function(){
				this.value = checkInputNum(this.value, 10, Number($('#J-trace-number-max').text()));
			});

			//起始倍数
			$('#J-trace-advance-multiple').keyup(function(e){
				var el = $(e.target),multiple = Number(checkInputNumber(el.val())),maxnum = Number(Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']);

				if(multiple == 0){
					el.val(el.val().replace(/^0/g, ''));
				}
				else if(multiple > maxnum){
					el.val(maxnum);
				}else{
					el.val(multiple);
				}

			}).blur(function(){ //失去焦点纠正为1倍
				this.value = checkInputNum(this.value, 1, Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']);
			});

			//高级追号填写参数切换
			me.panel.find('.trace-advanced-type-switch').click(function(){
				var el = $(this),par = el.parent(),pars = par.parent().children(),_par;
				pars.each(function(i){
					_par = pars.get(i);
					if(par.get(0) != _par){
						//alert($(_par).html());
						$(_par).find('input[type="text"]').attr('disabled', 'disabled');
					}else{
						$(_par).find('input[type="text"]').attr('disabled', false).eq(0).focus();
						me.setTypeTypeType(par.attr('data-type'));
					}

					if(el.parent().hasClass('trace-input-multiple')){
						this.value = checkInputNum(this.value, 1, Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']);
					}else{
						this.value = checkInputNum(this.value, 1, 99999999);
					}

				});
			});
			//高级追号区域输入事件
			$('#J-trace-advanced-type-panel').on('keyup', 'input[type=text]', function(e){
				var dom = $(e.target);
				//如果是倍数输入框
				if(dom.hasClass('trace-input-multiple')){
					this.value = checkInputNum(this.value, 1, Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum']);
				}else{
					this.value = checkInputNum(this.value, 1, 99999999);
				}

			});



			//生成追号计划事件
			$('#J-trace-panel .trace-button-detail').click(function(){
				//将号码篮倍数设置为1倍
				Games.getCurrentGameOrder().editMultiples(1);
				me.confirmSetting();
			});

			//数据行选择行生效/失效事件
			me.panel.find('.chase-table').click(function(e){
				var el = $(e.target),action = $.trim(el.attr('data-action')),isChecked = true,tableType,type = me.getTraceType();
				if(!!action && action != ''){
					switch(action){
						case 'checkedAll':
							isChecked = !!el.get(0).checked ? true : false;
							tableType = me.getRowTableType(type);
							me.getRowTable(type).find('.trace-row-checked').each(function(){
								this.checked = isChecked;
							});
							//console.log(tableType);
							//如果是盈利追号和盈利率追号，则需要重新计算盈利金额和盈利率
							if(tableType == 'lirunlv'){
								me.rebuildYinglilvRows();
							}
							me.updateStatistics();
							break;
						case 'checkedRow':
							if(el.size() > 0){
								tableType = me.getRowTableType();
								//如果是盈利追号和盈利率追号，则需要重新计算盈利金额和盈利率
								if(tableType == 'trace_advanced_yingli_a' || tableType == 'trace_advanced_yingli_b' || tableType == 'trace_advanced_yinglilv_a' || tableType == 'trace_advanced_yinglilv_b'){
									me.rebuildYinglilvRows();
								}
								me.updateStatistics();
							}
							break;
						default:
							break;
					}
				}
			});

			
			//删除追号内容
			$('#J-button-trace-clear').click(function(){
				me.autoDeleteTrace();
			});


			//追号期数输入限制
			$('#J-trace-lirunlv-times').keyup(function(){
				this.value = me.getTimes(me.getTraceType());
			});
			$('#J-trace-tongbei-times').keyup(function(){
				this.value = me.getTimes(me.getTraceType());
			});
			$('#J-trace-fanbei-times').keyup(function(){
				this.value = me.getTimes(me.getTraceType());
			});




			$('#J-trace-tongbei-multiple').keyup(function(){
				var v = Number(this.value);
				var max = Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum'];
				v = isNaN(v) ? 1 : Math.min(v, max);
				this.value = v;
			});
			$('#J-trace-fanbei-multiple').keyup(function(){
				var v = Number(this.value);
				var max = Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum'];
				v = isNaN(v) ? 1 : Math.min(v, max);
				this.value = v;
			});

		},
		//创建期号列表slect元素
		buildStartNumberSelectDom:function(){
			var me = this,
				gameCfg = Games.getCurrentGame().getGameConfig().getInstance(),
				list = gameCfg.getGameNumbers(),
				len = list.length,
				i = 0,
				strArr = [],
				currentNumber = gameCfg.getCurrentGameNumber(),
				currStr = '(当前期)',
				curr = currStr,
				oldValue,
				checkedStr = '';
			if(me.traceStartNumberSelect){
				oldValue = me.traceStartNumberSelect.getValue();
			}

			for(;i < len;i++){
				curr = currentNumber == list[i]['number'] ? currStr : '';
				checkedStr = (!!me.traceStartNumberSelect && (list[i]['number'] == oldValue)) ? ' selected="selected" ' : '';
				strArr.push('<option value="'+ list[i]['number'] +'" '+ checkedStr +' >'+ list[i]['number'] + curr +'</option>');
			}
			$('#J-traceStartNumber').html(strArr.join(''));
			$('#J-trace-number-max').text(len);

			//起始号选择
			if(me.traceStartNumberSelect){
				me.traceStartNumberSelect.dom.remove();
			}
			me.traceStartNumberSelect = new host.Select({realDom:'#J-traceStartNumber',cls:'chase-trace-startNumber-select'});
			me.traceStartNumberSelect.addEvent('change', function(e, value, text){
				me.setTraceStartNumber(value);
			});
		},
		//更新表格期号
		updateTableNumber:function(){
			var me = this,list = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers(),len = list.length,trs1,trs2,
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				startNumber,
				dom,
				numberDom,
				dateDom,
				currText = '',
				index,
				traceLastNumber = '',//上期号
				//当前期
				currStr = '<span class="icon-period-current"></span>',
				traceNo;

			//当当前期号发生更变时
			if(len > 0){
				trs1 = me.getNormalRowTable().find('tr');
				trs2 = me.getAdvancedRowTable().find('tr');

				index = me.getStartNumberIndexByNumber(startNumber);
				trs1.each(function(i){
					if(i == 0){ //当当前(普通)期号发生更变时,跳出表头不放在trs1对象中循环
						return true;
					}
					dom = $(this);
					numberDom = dom.find('.trace-row-number');//当前开奖期号
					dateDom = dom.find('.trace-row-time');
					multipleDom = dom.find('.trace-row-multiple');
					startNumber = numberDom.text().replace(/[^\d]/g, '');
					traceNo = dom.find('.text-center'); //序号
					dom.find('.trace-row-multiple').removeAttr('disabled'); //被禁用倍数文本框开启，倍数1

					if((index+1) < len){
						currText = list[index+1]['number'] == currNumber ? currStr : '';
						numberDom.html(list[index+1]['number'] + currText);
						multipleDom.text('1');
						dateDom.text(list[index+1]['time']);
						traceNo.html('').html(i);
						/**
						if(traceLastNumber != numberDom.text().substr(0,8) && traceLastNumber != ""){//增加隔天期标识
							traceNo.html('').append('<div class="icon-chase-mark">明天 ' + dom.find('.trace-row-number').text().substr(0,8) + '</div>');
						}
						**/
						traceLastNumber = numberDom.text().substr(0,8);
						index++;
					}

				});

				index = me.getStartNumberIndexByNumber(startNumber);
				trs2.each(function(i){
					if(i == 0){ //去除表头（高级）
						return true;
					}
					dom = $(this);
					numberDom = dom.find('.trace-row-number');
					dateDom = dom.find('.trace-row-time');
					multipleDom = dom.find('.trace-row-multiple');
					startNumber = numberDom.text().replace(/[^\d]/g, '');
					traceNo = dom.find('.text-center'); //序号
					dom.find('.trace-row-multiple').removeAttr('disabled'); //被禁用倍数文本框开启，倍数1

					if((index+1) < len){
						currText = list[index+1]['number'] == currNumber ? currStr : '';
						numberDom.html(list[index+1]['number'] + currText);
						multipleDom.text('1');
						dateDom.text(list[index+1]['time']);
						traceNo.html('').html(i);
						/**
						if(traceLastNumber != numberDom.text().substr(0,8) && traceLastNumber != ""){//增加隔天期标识
							traceNo.html('').append('<div class="icon-chase-mark">明天 ' + dom.find('.trace-row-number').text().substr(0,8) + '</div>');
						}
						**/
						traceLastNumber = numberDom.text().substr(0,8);
						index++;
					}
				});

			}

		},
		//重新计算盈利金额和盈利率表格数据
		rebuildYinglilvRows:function(){
			var me = this,
				trs = me.getRowTable().find('tr'),
				orderData = me.getOrderData(),
				//单注预计中奖金额
				orderUserGroupMoney = me.getWinMoney(),

				rowDom = null,
				checkboxDom = null,
				multipleDom = null,
				multiple = 1,
				amountDom = null,
				amount = 0,
				userGroupMoneyDom = null,
				winMoneyDom = null,
				yinglilvDom = null,
				yinglilv = -1;

				//累计投注成本
				costAmount = 0;

			//console.log('rebuild');

			trs.each(function(i){
				//第一行为表头
				if(i > 0){
					rowDom = $(this);
					checkboxDom = rowDom.find('.trace-row-checked');
					//当该行处于选中状态
					if(checkboxDom.size() > 0 && checkboxDom.get(0).checked){
						multipleDom = rowDom.find('.trace-row-multiple');
						multiple = Number(multipleDom.val());
						amountDom = rowDom.find('.trace-row-money');
						amount = Number(amountDom.text().replace(',', ''));
						userGroupMoneyDom = rowDom.find('.trace-row-userGroupMoney');
						winMoneyDom = rowDom.find('.trace-row-winTotalAmount');
						yinglilvDom = rowDom.find('.trace-row-yinglilv');

						costAmount += orderData['amount'] * multiple;

						amountDom.text(me.formatMoney(orderData['amount'] * multiple));
						userGroupMoneyDom.text(me.formatMoney(orderUserGroupMoney * multiple));
						winMoneyDom.text(me.formatMoney(orderUserGroupMoney * multiple - costAmount));
						yinglilv = (orderUserGroupMoney * multiple - costAmount)/costAmount*100;
						yinglilvDom.text(Number(yinglilv).toFixed(2));

					}
				}
			});

		},
		setTypeTypeType:function(v){
			this.typeTypeType = v;
		},
		getTypeTypeType:function(){
			return this.typeTypeType;
		},
		getIsWinStop:function(){
			var me = this,stopDom1 = $('#J-trace-iswintimesstop'),stopDom2 = $('#J-trace-iswinstop');
			if(stopDom1.get(0).checked){
				return 1;
			}
			if(stopDom2.get(0).checked){
				return 2;
			}
			return 0;
		},
		getTraceWinStopValue:function(){
			var me = this,isWinStop = me.getIsWinStop();
			if(isWinStop == 1){
				return Number($('#J-trace-iswintimesstop-times').val());
			}
			if(isWinStop == 2){
				return Number($('#J-trace-iswinstop-money').val());
			}
			return -1;
		},
		updateStatistics:function(){
			var me = this,data = me.getResultData();
			$('#J-trace-statistics-times').html(data['times']);
			$('#J-trace-statistics-lotterys-num').html(data['lotterysNum']);
			$('#J-trace-statistics-amount').html(me.formatMoney(data['amount']));

			me.applyTraceData();
		},
		//已经设置完成的追号信息，每次修改追号信息时将更新该对象
		//格式和getResultData一直，但来源不同，getResultData来自dom中分析出数据，该函数获取的是上次成功设置最好的缓存信息
		getSavedTraceData:function(){
			return this.savedTraceData;
		},
		setSavedTraceData:function(data){
			this.savedTraceData = data;
		},
		getResultData:function(){
			var me = this,orderData = me.getOrderData(),trs = me.getRowTable(me.getTraceType()).find('tr'),rowDom,checkedDom,
				times = 0,
				lotterysNum = 0,
				amount = 0,
				traceData = [],
				par,
				result = {'times':0,'lotterysNum':0,'amount':0,'orderData':orderData,'traceData':[],'traceType':me.getTraceType()},
				traceLastNumber = '',//上期号
				list = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers(),
				issueCode,
				index;

			trs.each(function(i){
				rowDom = $(this);
				checkedDom = rowDom.find('.trace-row-checked'),
				tracenumber = rowDom.find('.trace-row-number'),//当前开奖期号
				traceNo = rowDom.find('.text-center'); //序号

				if( i != 0){
					traceNo.html('').html(i);
				}
				if(checkedDom.size() > 0 && checkedDom.get(0).checked){
					par = checkedDom.parent();
					index = me.getStartNumberIndexByNumber(par.find('.trace-row-number').text());
					index = index == -1 ? 0 :index;
					issueCode = list[index]['issueCode'];
					//0倍时再选中，初始倍数为1倍
					rowDom.find('.trace-row-multiple').removeAttr('disabled');
					if(rowDom.find('.trace-row-multiple').val() == '0'){
						rowDom.find('.trace-row-multiple').val('1');
						rowDom.find('.trace-row-money').text(me.formatMoney(orderData['amount'] * 1));

					}

					traceData.push({'traceNumber':par.find('.trace-row-number').text(),'issueCode':issueCode,'multiple':Number(par.parent().find('.trace-row-multiple').val())});
					times++;
					amount += Number(rowDom.find('.trace-row-money').text().replace(/,/g,''));

				}
				else{//没有勾选时状态
					rowDom.find('.trace-row-money').text('0');
					rowDom.find('.trace-row-multiple').val('0');
					rowDom.find('.trace-row-multiple').attr('disabled', 'disabled').css('border','1px solid #CECECE');

				}

				/**
				if(traceLastNumber != tracenumber.text().substr(0,8) && traceLastNumber != ""){//增加隔天期标识
						traceNo.html('').append('<div class="icon-chase-mark">明天 ' + rowDom.find('.trace-row-number').text().substr(0,8) + '</div>');

				}
				**/
				traceLastNumber = tracenumber.text().substr(0,8);

			});

			if(!!orderData){
				lotterysNum = times * orderData['count'];
				result = {'times':times,'lotterysNum':lotterysNum,'amount':amount,'orderData':orderData,'traceData':traceData,'traceType':me.getTraceType()};
			}
			return result;
		},
		//追加或删除投注，在追号面板展开的情况下再次进行选球投注，追号相关信息追加或减少投注金额
		//isShowMessage 是否关闭提示
		updateOrder:function(isNotShowMessage){
			var me = this,orderData = Games.getCurrentGameOrder().getTotal(),tableType = me.getRowTableType(),
				maxObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),maxnum = maxObj['maxnum'],
				selValue = Number(me.normalSelectMultiple.getValue()),
				inputValue = Number($('#J-trace-advance-multiple').val());

			me.setOrderData(orderData);

			//按照最新的允许设置的最大倍数，设置相关的倍数输入框和下拉框
			if(selValue > maxnum){
				me.normalSelectMultiple.setValue(maxnum);
			}
			if(inputValue > maxnum){
				$('#J-trace-advance-multiple').val(maxnum);
			}

			//当注单发生变化时，清空盈利追号和盈利率追号表格
			if(!isNotShowMessage && (tableType == 'trace_advanced_fanbei_a' || tableType == 'trace_advanced_fanbei_b' || tableType == 'trace_advanced_yingli_a' || tableType == 'trace_advanced_yingli_b' || tableType == 'trace_advanced_yinglilv_a' || tableType == 'trace_advanced_yinglilv_b')){
				Games.getCurrentGameMessage().show({
						type : 'normal',
						closeFun: function(){
							this.hide();
						},
						data : {
							tplData:{
								msg:'您的方案已被修改，如果需要根据最新方案进行追号，请点击生成追号计划按钮'
							}
						}
				});
			}
			//盈利追号/盈利率追号每次都清空表格
			me.getAdvancedRowTable().html('');


			//更新表格
			me.updateDetail(orderData['amount']);

			//更新界面金额
			me.updateStatistics();
		},
		//更新详细表格单条金额
		updateDetail:function(amount){
			var me = this,trs = me.getTable().find('tr'),rowDom = null,rowAmountDom = null,rowUserGroupMoneyDom = null,rowWinTotalAmountDom = null,rowYinglilvDom = null,userGroupMoney = 0,tableType = me.getRowTableType(),advancedType;
			//console.log(me.getRowTable());
			//高级追号和普通追号表格结构不一样
			if(tableType == 'trace_advanced_yingli_a' || tableType == 'trace_advanced_yingli_b' || tableType == 'trace_advanced_yinglilv_a' || tableType == 'trace_advanced_yinglilv_b'){
				me.rebuildYinglilvRows();
			}else{
				//翻倍追号自动更新表格
				advancedType = me.getAdvancedRowTable().attr('data-type');
				if(advancedType == 'trace_advanced_fanbei_a' || advancedType == 'trace_advanced_fanbei_b'){
					trs = me.getAdvancedTable().find('tr');
					trs.each(function(){
						rowDom = $(this);
						rowMoney = rowDom.find('.trace-row-money');
						rowMultiple = Number(rowDom.find('.trace-row-multiple').val());
						rowMoney.text(me.formatMoney(rowMultiple * amount));
					});
				}
			}

			//普通追号每次都自动更新表格
			trs = me.getNormalTable().find('tr');
			trs.each(function(){
				rowDom = $(this);
				rowMoney = rowDom.find('.trace-row-money');
				rowMultiple = Number(rowDom.find('.trace-row-multiple').val());
				rowMoney.text(me.formatMoney(rowMultiple * amount));
			});



		},
		//计算投注内容中的预计中奖金额
		//选球内容有可能是不同的玩法内容，需要各自计算中奖将进组金额
		getWinMoney:function(){
			var me = this,orders = me.getOrderData()['orders'],i = 0,len = orders.length,winMoney = 0;
			for(;i < len;i++){
				winMoney += me.getPlayGroupMoneyByGameMethodName(orders[i]['mid']) * orders[i]['moneyUnit'];
			}
			return winMoney;
		},
		//根据追号选择条件生成详细表格
		confirmSetting:function(){
			var me = this;
			me.setOrderData(Games.getCurrentGameOrder().getTotal());
			me.buildDetail();
		},
		//检测当前投注列表中是否全部为同一玩法
		//且元角模式一致
		isSameGameMethod:function(){
			var me = this,orders = me.getOrderData()['orders'],type = '',moneyUnit = -1;
				i = 0,
				len = orders.length;
			for(;i < len;i++){
				if(type != ''){
					if(type != orders[i]['type']){
						return false;
					}
				}else{
					type = orders[i]['type'];
				}

				if(moneyUnit != -1){
					if(moneyUnit != orders[i]['moneyUnit']){
						return false;
					}
				}else{
					moneyUnit = orders[i]['moneyUnit'];
				}
			}
			return true;
		},
		getSameGameMethodName:function(){
			var me = this,orders = me.getOrderData()['orders'];
			if(orders.length > 0){
				return orders[0]['type'];
			}
		},
		getSameGameMoneyUnti:function(){
			var me = this,orders = me.getOrderData()['orders'];
			if(orders.length > 0){
				return orders[0]['moneyUnit'];
			}
		},
		setOrderData:function(data){
			this.orderData = data;
		},
		getOrderData:function(){
			return this.orderData == null ? {'count':0,'amount':0,'orders':[]} : this.orderData;
		},
		//由期号获得期号在列表中的索引值

		getStartNumberIndexByNumber:function(number){
			var me = this,numberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers(),len = numberList.length,i = 0;
			for(;i < len;i++){
				if(numberList[i]['number'] == number){
					return i;
				}
			}
			return -1;
		},
		getStartNumberByIndex:function(index){
			var me = this,numberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers();
			if(numberList.length > index){
				return numberList[index];
			}
			return {};
		},
		//生成追号计划详情内容
		//maxMultipleNum 如果参数中有设置该参数，则最大倍数都使用该值(用于检测倍数超出最大值后重新设置倍数)
		//isAuto 是否自动渲染，自动渲染只适合普通追号
		buildDetail:function(){
			var me = this,
				type = me.getTraceType(),
				msg = Games.getCurrentGameMessage();
			//每次获取最新的投注信息
			me.setOrderData(Games.getCurrentGameOrder().getTotal());
			orderAmount = me.getOrderData()['amount'];


			//投注内容为空
			if(orderAmount <= 0){
				msg.show({
					type : 'mustChoose',
					msg : '请至少选择一注投注号码！',
					data : {
						tplData : {
							msg : '请至少选择一注投注号码！'
						}
					}
				});
				return;
			}
			if($.isFunction(me['trace_' + type])){
				me['trace_' + type].call(me);
			}
			//console.log('trace_' + type);
			me.updateStatistics();
		},
		trace_lirunlv: function() {
			var me = this,
				type = me.getTraceType(),
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(type),
				timesTemp = times,
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//基础倍数
				multipleBase = me.getMultiple(),
				//盈利率计算结果
				resultData = [],

				//每期必须要达到的盈利率
				yinglilv = Number($('#J-trace-lirunlv-num').val()) / 100,
				len2 = 0,
				//元角模式
				moneyUnit = me.getSameGameMoneyUnti(),
				//用户奖金组中该玩法中每注的中奖金额
				userGroupMoney = 0,
				//玩法中的单注单价
				onePrice = 0,
				//启用另外表头和行模板
				tpl = me.defConfig.dataRowLirunlvTemplate,
				orders = me.getOrderData()['orders'],


				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				//当前期标识
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				i = 0,
				//标记是否已经提示过一次
				isAlerted = false,
				numberData,
				//期号列表
				traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers();


			//盈利/盈利率追号不支持混投
			if (!me.isSameGameMethod()) {
				Games.getCurrentGameMessage().show({
					type: 'mustChoose',
					msg: '',
					data: {
						tplData: {
							msg: '利润率追号不支持混投<br />请确保您的投注都为同一玩法类型<br />且元角模式一致。'
						}
					}
				});
				return;
			}



			$.each(orders, function() {
				var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
				userGroupMoney += Number(method['prize']);
				onePrice += this['num'] * Number(method['price']);
			});
			userGroupMoney *= moneyUnit;
			onePrice *= moneyUnit;


			tplArr.push(me.defConfig.dataRowYingliHeader);

			startIndex = me.getStartNumberIndexByNumber(settingStartNumber);

			timesTemp = times;
			resultData = me.getMultipleByYinglilv(yinglilv, userGroupMoney, onePrice, timesTemp, multipleBase, maxNumltipleObj['maxnum']);

			if (resultData.length < 1) {
				alert('您设置的参数无法达到盈利，请重新设置');
				return;
			}

			$.each(resultData, function() {
				if (this['oldMultiple'] > maxNumltipleObj['maxnum']) {
					isAlerted = true;
					alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大可设置倍数');
					return false;
				}
			});


			for (i =0; i < resultData.length; i++) {
				currNumberText = traceNumberList[i + startIndex]['number'];
				if (currNumberText == currNumber) {
					currNumberText = currNumberText + currStr;
				}
				rowData = {
					'No': (i + 1),
					'traceNumber': currNumberText,
					'multiple': resultData[i]['multiple'],
					'money': me.formatMoney(onePrice * resultData[i]['multiple']),
					'userGroupMoney': me.formatMoney(userGroupMoney * resultData[i]['multiple']),
					'winTotalAmout': me.formatMoney(resultData[i]['winAmountAll']),
					'yinglilv': Number(resultData[i]['winAmountAll'] / resultData[i]['amountAll'] * 100).toFixed(2)
				};
				tplArr.push(me.formatRow(tpl, rowData));
			}

			me.getRowTable(type).html(tplArr.join(''));
			//在表格上设置最后生成列表的类型，用于区分列表类型
			me.getRowTable(type).attr('data-type', 'lirunlv');
		},
		trace_tongbei:function(){
			var me = this,
				type = me.getTraceType(),
				cfg = me.defConfig,
				tpl = cfg.dataRowTemplate,
				tplArr = [],
				//类型
				type = me.getTraceType(),
				//追号期数
				times = me.getTimes(type),
				//倍数
				multiple = Number($('#J-trace-tongbei-multiple').val()),
				//最大倍数限制
				maxMultiple = Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum'],
				//投注金额
				orderAmount = 0,
				i = 0,

				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表长度
				numberLength = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers().length,
				rowData;


			me.setOrderData(Games.getCurrentGameOrder().getTotal());
			orderAmount = me.getOrderData()['amount'];

			tplArr.push(cfg.dataRowHeader);


			startIndex = me.getStartNumberIndexByNumber(settingStartNumber);
			i = startIndex;
			times += i;

			multiple = multiple > maxMultiple ? maxMultiple : multiple;

			for(;i < times;i++){
				numberData = me.getStartNumberByIndex(i);
				currNumberText = numberData['number'];
				if(currNumberText == currNumber){
					currNumberText = currNumberText + currStr;
				}
				if(numberData['number']){
					rowData = {'No':i+1,'traceNumber':currNumberText,'multiple':multiple,'money':me.formatMoney(orderAmount * multiple),'publishTime':numberData['time']};
					tplArr.push(me.formatRow(tpl, rowData));
				}
			}
			me.getRowTable(type).html(tplArr.join(''));


			//在表格上设置最后生成列表的类型，用于区分列表类型
			me.getRowTable(type).attr('data-type', 'tongbei');
		},
		trace_fanbei:function(){
			var me = this,
				type = me.getTraceType(),
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(type),
				orders = me.getOrderData()['orders'],
				moneyUnit = me.getSameGameMoneyUnti(),
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//最大倍数限制
				maxMultiple = maxNumltipleObj['maxnum'],
				jiangeNum = Number($('#J-trace-fanbei-jump').val()),
				//间隔变量
				jiangeNum2 = jiangeNum,
				//基础倍数
				multipleBase = Number($('#J-trace-fanbei-multiple').val()),
				//倍数变量
				multiple = multipleBase,
				//间隔倍数
				multiple2 = Number($('#J-trace-fanbei-num').val()),
				//玩法中的单注单价
				onePrice = 0,
				i = 0,
				isAlerted = false,

				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表
				traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers(),
				//序号列
				traceNo = 1;

				$.each(orders, function(){
					var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
					onePrice += this['num'] * method['price'];
				});
				onePrice *= moneyUnit;


				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);

				tplArr.push(me.defConfig.dataRowHeader);

				i = i + startIndex;
				times = times + startIndex;

				for(;i < times;i++){
					if(jiangeNum2 < 1){
						jiangeNum2 = jiangeNum;
						multiple *= multiple2;
					}
					if(multiple > maxMultiple){
						if(!isAlerted){
							/**
							alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大可设置倍数');
							isAlerted = true;
							**/
						}
						multiple = maxMultiple;
					}
					currNumberText = traceNumberList[i]['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					rowData = {'No':traceNo,'traceNumber':currNumberText,'multiple':multiple,'money':me.formatMoney(onePrice * multiple),'publishTime':traceNumberList[i]['time']};

					jiangeNum2 -= 1;
					traceNo++;
					tplArr.push(me.formatRow(tpl, rowData));
				}

				me.getRowTable(type).html(tplArr.join(''));
				//在表格上设置最后生成列表的类型，用于区分列表类型
				me.getRowTable(type).attr('data-type', 'fanbei');
		},














		//以下追号方法为已废除的 ==================================================
		//普通追号
		trace_normal:function(){
			var me = this,
				cfg = me.defConfig,
				tpl = cfg.dataRowTemplate,
				tplArr = [],
				//类型
				type = me.getTraceType(),
				//追号期数
				times = me.getTimes(),
				//倍数
				multiple = me.getMultiple(),
				//最大倍数限制
				maxMultiple = Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum'],
				//投注金额
				orderAmount = 0,
				i = 0,

				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表长度
				numberLength = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers().length,
				rowData;



			me.setOrderData(Games.getCurrentGameOrder().getTotal());
			orderAmount = me.getOrderData()['amount'];

			tplArr.push(cfg.dataRowHeader);


			startIndex = me.getStartNumberIndexByNumber(settingStartNumber);
			i = startIndex;
			times += i;
			for(;i < times;i++){
				numberData = me.getStartNumberByIndex(i);
				currNumberText = numberData['number'];
				if(currNumberText == currNumber){
					currNumberText = currNumberText + currStr;
				}
				if(numberData['number']){
					rowData = {'No':i+1,'traceNumber':currNumberText,'multiple':multiple,'money':me.formatMoney(orderAmount * multiple),'publishTime':numberData['time']};
					tplArr.push(me.formatRow(tpl, rowData));
				}
			}
			me.getRowTable().html(tplArr.join(''));


			//在表格上设置最后生成列表的类型，用于区分列表类型
			me.getRowTable().attr('data-type', 'trace_normal');

		},
		//高级追号
		trace_advanced:function(){
			var me = this,
				type = me.getTraceType(),
				advancedType = me.getAdvancedType(),
				typeTypeType = me.getTypeTypeType(),
				fnName = 'trace_' + type + '_' + advancedType + '_' + typeTypeType;


			//盈利/盈利率追号不支持混投
			if(!me.isSameGameMethod() && (advancedType == 'yingli' || advancedType == 'yinglilv')){
				Games.getCurrentGameMessage().show({
					type : 'mustChoose',
					msg : '',
					data : {
						tplData : {
							msg : '盈利金额追号不支持混投<br />请确保您的投注都为同一玩法类型<br />且元角模式一致。'
						}
					}
				});
				return;
			}

			if($.isFunction(me[fnName])){
				me[fnName]();
			}
			//在表格上设置最后生成列表的类型，用于区分列表类型
			me.getRowTable().attr('data-type', fnName);
		},
		//高级追号 -- 翻倍追号 -- 间隔追号
		trace_advanced_fanbei_a:function(){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				orders = me.getOrderData()['orders'],
				moneyUnit = me.getSameGameMoneyUnti(),
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//最大倍数限制
				maxMultiple = maxNumltipleObj['maxnum'],
				jiangeNum = Number($('#J-trace-advanced-fanbei-a-jiange').val()),
				//间隔变量
				jiangeNum2 = jiangeNum,
				//基础倍数
				multipleBase = me.getMultiple(),
				//倍数变量
				multiple = 1,
				//间隔倍数
				multiple2 = Number($('#J-trace-advanced-fanbei-a-multiple').val()),
				//玩法中的单注单价
				onePrice = 0,
				i = 0,
				isAlerted = false,

				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表
				traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers(),
				//序号列
				traceNo = 1;

				$.each(orders, function(){
					var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
					onePrice += this['num'] * method['price'];
				});
				onePrice *= moneyUnit;


				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);

				tplArr.push(me.defConfig.dataRowHeader);

				i = i + startIndex;
				times = times + startIndex;

				for(;i < times;i++){
					if(jiangeNum2 < 1){
						jiangeNum2 = jiangeNum;
						multiple *= multiple2;
					}
					if(multiple > maxMultiple){
						if(!isAlerted){
							/**
							alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大可设置倍数');
							isAlerted = true;
							**/
						}
						multiple = maxMultiple;
					}
					currNumberText = traceNumberList[i]['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					rowData = {'No':traceNo,'traceNumber':currNumberText,'multiple':multiple,'money':me.formatMoney(onePrice * multiple),'publishTime':traceNumberList[i]['time']};

					jiangeNum2 -= 1;
					traceNo++;
					tplArr.push(me.formatRow(tpl, rowData));
				}

				me.getRowTable().html(tplArr.join(''));
		},
		//高级追号 -- 翻倍追号 -- 前后追号
		trace_advanced_fanbei_b:function(){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				orders = me.getOrderData()['orders'],
				moneyUnit = me.getSameGameMoneyUnti(),
				//最大倍数限制
				maxMultiple = Games.getCurrentGameOrder().getOrderMaxMultiple()['maxnum'],
				jiangeNum = Number($('#J-trace-advanced-fanbei-a-jiange').val()),
				//基础倍数
				multipleBase = me.getMultiple(),
				//中间运算倍数
				multiple = 1,
				//间隔倍数
				multiple2 = Number($('#J-trace-advanced-fanbei-a-multiple').val()),
				//玩法中的单注单价
				onePrice = 0,
				i = 0,
				//间隔临时计数器
				_i = jiangeNum,

				beforeNum = Number($('#J-trace-advanced-fanbei-b-num').val()),
				startMultiple = Number($('#J-trace-advance-multiple').val()),
				afterMultiple = Number($('#J-trace-advanced-fanbei-b-multiple').val()),


				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表长度
				numberLength = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers().length,
				rowData,
				traceLastNumber = '',//上期号
				traceNo=''; //序号列


				$.each(orders, function(){
					var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
					onePrice += this['num'] * method['price'];
				});
				onePrice *= moneyUnit;


				tplArr.push(me.defConfig.dataRowHeader);

				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);
				i = startIndex;
				times += i;
				numberData = me.getStartNumberByIndex(i);
				for(;i < times;i++){
					if(i < (beforeNum + startIndex)){
						multiple = startMultiple > maxMultiple ? maxMultiple : startMultiple;
					}else{
						multiple = afterMultiple > maxMultiple ? maxMultiple : afterMultiple;
					}

					numberData = me.getStartNumberByIndex(i);
					if(!numberData['number']){
						break;
					}
					currNumberText = numberData['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					traceNo = i +1;
					rowData = {'No':traceNo,'traceNumber':currNumberText,'multiple':multiple,'money':me.formatMoney(onePrice * multiple),'publishTime':numberData['time']};
					traceLastNumber = currNumberText.substr(0,8);

					tplArr.push(me.formatRow(tpl, rowData));
				}
				me.getRowTable().html(tplArr.join(''));
		},
		//高级追号 -- 盈利金额追号 -- 预期盈利金额
		trace_advanced_yingli_a:function(maxnum){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//最大倍数限制
				maxMultiple = maxnum || maxNumltipleObj['maxnum'],
				typeTitle = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullName(maxNumltipleObj['gameMethod']).join('-'),
				//基础倍数
				multipleBase = me.getMultiple(),
				//中间运算倍数
				multiple = 1,
				testData,
				i = 0,



				//玩法类型
				gameMethodType = me.getSameGameMethodName(),
				//每期必须要达到的盈利金额
				yingliMoney = Number($('#J-trace-advanced-yingli-a-money').val()),
				//元角模式
				moneyUnit = me.getSameGameMoneyUnti(),
				//用户奖金组中该玩法中每注的中奖金额
				userGroupMoney = me.getWinMoney(),
				//基础倍数，盈利追号和盈利率追号通过修改倍数达到预期值，所以初始值设置为1
				multipleBase = 1,
				//启用另外表头和行模板
				tpl = me.defConfig.dataRowYingliTemplate,
				orders = me.getOrderData()['orders'],
				//投注组本金
				orderAmount = 0,
				//所有投注本金
				orderTotalAmount = 0,
				//中奖总金额
				winTotalAmout = 0,
				//盈利率
				yinglilv = 0,


				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表长度
				numberLength = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers().length,
				rowData,
				traceLastNumber = '',//上期号
				traceNo=''; //序号列


			tplArr.push(me.defConfig.dataRowYingliHeader);

			startIndex = me.getStartNumberIndexByNumber(settingStartNumber);
			i = startIndex;
			times += i;
			numberData = me.getStartNumberByIndex(i);
			for(;i < times;i++){
				orderAmount = 0;
				winTotalAmout = 0;
				//基础倍数，盈利追号和盈利率追号通过修改倍数达到预期值，所以初始值设置为1
				multipleBase = 1;
				//计算预计中奖金额
				$.each(orders, function(i){
					var order = this,
						num = order['num'],
						price = order['onePrice'],
						multiple = order['multiple'],
						//本金
						amount = num * multiple * price,
						//单注中奖金额
						winAmout = userGroupMoney * multiple;

						//该投注组盈利金额
						winTotalAmout += winAmout;

						orderAmount += amount;
				});


				//获得倍数
				multipleBase = me.getMultipleByMoney(userGroupMoney, yingliMoney, orderAmount, orderTotalAmount);
				//无法达到预期目标
				if(multipleBase < 0){
					alert('盈利金额追号无法到达您预期设定的目标值，请修改您的设置');
					return;
				}

				//倍数超限时提示
				if(multipleBase > maxMultiple){
					Games.getCurrentGameMessage().show({
						type : 'normal',
							closeText: '确定',
							closeFun: function(){
								me.trace_advanced_yingli_a(maxMultiple);
								me.updateStatistics();
								this.hide();
							},
							data : {
								tplData:{
									msg:'盈利金额追号中的<b>['+ typeTitle +']</b>的倍数超过了最大倍数限制，系统将自动调整为最大可设置倍数'
								}
							}
					});
					if(!maxnum){
						return;
					}else{
						multipleBase = maxnum;
					}
				}

				//花费本金
				orderAmount *= multipleBase;
				//累计本金
				orderTotalAmount += orderAmount;
				//利润减去累计花费
				winTotalAmout = (userGroupMoney * multipleBase) - orderTotalAmount;
				//盈利率
				yinglilv = winTotalAmout/orderTotalAmount;


				numberData = me.getStartNumberByIndex(i);
				if(!numberData['number']){
					break;
				}
				currNumberText = numberData['number'];
				if(currNumberText == currNumber){
					currNumberText = currNumberText + currStr;
				}
				 //增加隔天期标识
				 /**
				if(traceLastNumber != currNumberText.substr(0,8) && traceLastNumber != ""){
					traceNo ='<div class="icon-chase-mark">明天 ' + currNumberText.substr(0,8) + '</div>';
				}else{
					traceNo = i+1;
				}
				**/
				traceNo = i +1;
				rowData = {'No':traceNo,'traceNumber': currNumberText,
							'multiple':multipleBase,
							'money':me.formatMoney(orderAmount),
							'userGroupMoney':me.formatMoney(userGroupMoney * multipleBase),
							'winTotalAmout':me.formatMoney(winTotalAmout),
							'yinglilv':Number(yinglilv*100).toFixed(2)
							};

				traceLastNumber = currNumberText.substr(0,8);
				tplArr.push(me.formatRow(tpl, rowData));
			}
			me.getRowTable().html(tplArr.join(''));

		},
		//高级追号 -- 盈利金额追号 -- 前后预期盈利金额
		trace_advanced_yingli_b:function(maxnum){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//最大倍数限制
				maxMultiple = maxnum || maxNumltipleObj['maxnum'],
				typeTitle = Games.getCurrentGame().getGameConfig().getInstance().getMethodFullName(maxNumltipleObj['gameMethod']).join('-'),
				//基础倍数
				multipleBase = me.getMultiple(),
				//中间运算倍数
				multiple = 1,
				testData,
				i = 0,


				//玩法类型
				gameMethodType = me.getSameGameMethodName(),
				//前几期
				yingliNum = Number($('#J-trace-advanced-yingli-b-num').val()),
				//第一期必须要达到的盈利金额
				yingliMoney = Number($('#J-trace-advanced-yingli-b-money1').val()),
				//第二期必须要达到的盈利金额
				yingliMoney2 = Number($('#J-trace-advanced-yingli-b-money2').val()),
				//元角模式
				moneyUnit = me.getSameGameMoneyUnti(),
				//用户奖金组中该玩法中每注的中奖金额
				userGroupMoney = me.getWinMoney(),
				//基础倍数，盈利追号和盈利率追号通过修改倍数达到预期值，所以初始值设置为1
				multipleBase = 1,
				//启用另外表头和行模板
				tpl = me.defConfig.dataRowYingliTemplate,
				orders = me.getOrderData()['orders'],
				//投注组本金
				orderAmount = 0,
				//所有投注本金
				orderTotalAmount = 0,
				//中奖总金额
				winTotalAmout = 0,
				//盈利率
				yinglilv = 0,



				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				numberData,
				//期号列表长度
				numberLength = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers().length,
				rowData,
				traceLastNumber = '',//上期号
				traceNo=''; //序号列



				tplArr.push(me.defConfig.dataRowYingliHeader);

				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);
				i = startIndex;
				times += i;
				numberData = me.getStartNumberByIndex(i);
				for(;i < times;i++){
					if((i+1) > (yingliNum + startIndex)){
						yingliMoney = yingliMoney2;
					}
					orderAmount = 0;
					winTotalAmout = 0;
					//基础倍数，盈利追号和盈利率追号通过修改倍数达到预期值，所以初始值设置为1
					multipleBase = 1;
					//计算预计中奖金额
					$.each(orders, function(i){
						var order = this,
						num = order['num'],
						price = order['onePrice'],
						multiple = order['multiple'],
						//本金
						amount = num * multiple * price,
						//单注中奖金额
						winAmout = userGroupMoney * multiple;

						//该投注组盈利金额
						winTotalAmout += winAmout;
						orderAmount += amount;
					});

					//获得倍数
					multipleBase = me.getMultipleByMoney(userGroupMoney, yingliMoney, orderAmount, orderTotalAmount);
					//无法达到预期目标
					if(multipleBase < 0){
						Games.getCurrentGameMessage().show({
							type : 'normal',
								closeText: '确定',
								closeFun: function(){
									this.hide();
								},
								data : {
									tplData:{
										msg:'盈利金额追号无法到达您预期设定的目标值，请修改您的设置'
									}
								}
						});
						return;
					}


					//倍数超限时提示
					if(multipleBase > maxMultiple){
						Games.getCurrentGameMessage().show({
							type : 'normal',
								closeText: '确定',
								closeFun: function(){
									me.trace_advanced_yingli_b(maxMultiple);
									me.updateStatistics();
									this.hide();
								},
								data : {
									tplData:{
										msg:'盈利金额追号中的<b>['+ typeTitle +']</b>的倍数超过了最大倍数限制，系统将自动调整为最大可设置倍数'
									}
								}
						});
						if(!maxnum){
							return;
						}else{
							multipleBase = maxnum;
						}
					}


					//花费本金
					orderAmount *= multipleBase;
					//累计本金
					orderTotalAmount += orderAmount;
					//利润减去累计花费
					winTotalAmout = (userGroupMoney * multipleBase) - orderTotalAmount;
					//盈利率
					yinglilv = winTotalAmout/orderTotalAmount;


					numberData = me.getStartNumberByIndex(i);
					if(!numberData['number']){
						break;
					}
					currNumberText = numberData['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					 //增加隔天期标识
					 /**
					if(traceLastNumber != currNumberText.substr(0,8) && traceLastNumber != ""){
						traceNo ='<div class="icon-chase-mark">明天 ' + currNumberText.substr(0,8) + '</div>';
					}else{
						traceNo = i+1;
					}
					**/
					rowData = {'No':traceNo,'traceNumber': currNumberText,
								'multiple':multipleBase,
								'money':me.formatMoney(orderAmount),
								'userGroupMoney':me.formatMoney(userGroupMoney * multipleBase),
								'winTotalAmout':me.formatMoney(winTotalAmout),
								'yinglilv':Number(yinglilv*100).toFixed(2)
							};
					traceLastNumber = currNumberText.substr(0,8);
					tplArr.push(me.formatRow(tpl, rowData));
				}

				me.getRowTable().html(tplArr.join(''));

		},
		//高级追号 -- 盈利率追号 -- 预期盈利率
		trace_advanced_yinglilv_a:function(){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//基础倍数
				multipleBase = me.getMultiple(),
				//盈利率计算结果
				resultData = [],

				//每期必须要达到的盈利率
				yinglilv = Number($('#J-trace-advanced-yinglilv-a').val())/100,
				//元角模式
				moneyUnit = me.getSameGameMoneyUnti(),
				//用户奖金组中该玩法中每注的中奖金额
				userGroupMoney = 0,
				//玩法中的单注单价
				onePrice = 0,
				//启用另外表头和行模板
				tpl = me.defConfig.dataRowYingliTemplate,
				orders = me.getOrderData()['orders'],


				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				//当前期标识
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				i = 0,
				numberData,
				//期号列表
				traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers();

				$.each(orders, function(){
					var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
					userGroupMoney += method['prize'];
					onePrice += this['num'] * method['price'];
				});
				userGroupMoney *= moneyUnit;
				onePrice *= moneyUnit;


				tplArr.push(me.defConfig.dataRowYingliHeader);

				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);

				resultData = me.getMultipleByYinglilv(yinglilv, userGroupMoney, onePrice, times, multipleBase, maxNumltipleObj['maxnum']);

				if(resultData.length < 1){
					alert('您设置的参数无法达到盈利，请重新设置');
					return;
				}

				$.each(resultData, function(i){
					if(this['oldMultiple'] > maxNumltipleObj['maxnum']){
						alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大可设置倍数');
						return false;
					}
				});

				for(;i < resultData.length;i++){
					currNumberText = traceNumberList[i + startIndex]['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					rowData = {'No':(i + 1),
								'traceNumber': currNumberText,
								'multiple':resultData[i]['multiple'],
								'money':me.formatMoney(onePrice * resultData[i]['multiple']),
								'userGroupMoney':me.formatMoney(userGroupMoney * resultData[i]['multiple']),
								'winTotalAmout':me.formatMoney(resultData[i]['winAmountAll']),
								'yinglilv':Number(resultData[i]['winAmountAll']/resultData[i]['amountAll']*100).toFixed(2)
					};

					tplArr.push(me.formatRow(tpl, rowData));
				}
				me.getRowTable().html(tplArr.join(''));

		},
		//高级追号 -- 盈利率追号 -- 前后预期盈利率
		trace_advanced_yinglilv_b:function(maxnum){
			var me = this,
				tpl = me.defConfig.dataRowTemplate,
				tplArr = [],
				//追号期数
				times = me.getTimes(),
				timesTemp = times,
				maxNumltipleObj = Games.getCurrentGameOrder().getOrderMaxMultiple(),
				//基础倍数
				multipleBase = me.getMultiple(),
				//盈利率计算结果
				resultData = [],

				//每期必须要达到的盈利率
				yinglilv1 = Number($('#J-trace-advanced-yingli-b-yinglilv1').val())/100,
				yinglilv2 = Number($('#J-trace-advanced-yingli-b-yinglilv2').val())/100,
				len2 = 0,
				//前多少期应用yinglilv1
				timesPre = Number($('#J-trace-advanced-yinglilv-b-num').val()),
				//元角模式
				moneyUnit = me.getSameGameMoneyUnti(),
				//用户奖金组中该玩法中每注的中奖金额
				userGroupMoney = 0,
				//玩法中的单注单价
				onePrice = 0,
				//启用另外表头和行模板
				tpl = me.defConfig.dataRowYingliTemplate,
				orders = me.getOrderData()['orders'],


				//当前期
				currNumber = Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),
				//当前期标识
				currStr = '<span class="icon-period-current"></span>',
				//当前期文本
				currNumberText = '',
				//用户选择的开始期号
				settingStartNumber = me.traceStartNumberSelect.getValue(),
				startIndex,
				i = 0,
				//标记是否已经提示过一次
				isAlerted = false,
				numberData,
				//期号列表
				traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers();

				$.each(orders, function(){
					var method = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(this['mid']);
					userGroupMoney += method['prize'];
					onePrice += this['num'] * method['price'];
				});
				userGroupMoney *= moneyUnit;
				onePrice *= moneyUnit;


				tplArr.push(me.defConfig.dataRowYingliHeader);

				startIndex = me.getStartNumberIndexByNumber(settingStartNumber);

				timesTemp = times <= timesPre ? times : timesPre;
				resultData = me.getMultipleByYinglilv(yinglilv1, userGroupMoney, onePrice, timesTemp, multipleBase, maxNumltipleObj['maxnum']);

				if(resultData.length < 1){
					alert('您设置的参数无法达到盈利，请重新设置');
					return;
				}

				$.each(resultData, function(){
					if(this['oldMultiple'] > maxNumltipleObj['maxnum']){
						isAlerted = true;
						alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大可设置倍数');
						return false;
					}
				});


				for(;i < resultData.length;i++){
					currNumberText = traceNumberList[i + startIndex]['number'];
					if(currNumberText == currNumber){
						currNumberText = currNumberText + currStr;
					}
					rowData = {'No':(i + 1),
								'traceNumber': currNumberText,
								'multiple':resultData[i]['multiple'],
								'money':me.formatMoney(onePrice * resultData[i]['multiple']),
								'userGroupMoney':me.formatMoney(userGroupMoney * resultData[i]['multiple']),
								'winTotalAmout':me.formatMoney(resultData[i]['winAmountAll']),
								'yinglilv':Number(resultData[i]['winAmountAll']/resultData[i]['amountAll']*100).toFixed(2)
					};
					tplArr.push(me.formatRow(tpl, rowData));
				}
				if(times > timesPre){
					$.each(resultData, function(){
						multipleBase += this['multiple'];
					});
					timesTemp = times - timesPre;
					resultData = me.getMultipleByYinglilv(yinglilv2, userGroupMoney, onePrice, timesTemp, multipleBase, maxNumltipleObj['maxnum']);

					$.each(resultData, function(){
						if(!isAlerted && this['oldMultiple'] > maxNumltipleObj['maxnum']){
							alert('生成方案中的倍数超过了系统最大允许设置的倍数，将自动调整为系统最大倍数');
							return false;
						}
					});

					len2 = i;
					i = 0;
					for(;i < resultData.length;i++){
						currNumberText = traceNumberList[i + len2 + startIndex]['number'];
						if(currNumberText == currNumber){
							currNumberText = currNumberText + currStr;
						}
						rowData = {'No':(i + 1),
									'traceNumber': currNumberText,
									'multiple':resultData[i]['multiple'],
									'money':me.formatMoney(onePrice * resultData[i]['multiple']),
									'userGroupMoney':me.formatMoney(userGroupMoney * resultData[i]['multiple']),
									'winTotalAmout':me.formatMoney(resultData[i]['winAmountAll']),
									'yinglilv':Number(resultData[i]['winAmountAll']/resultData[i]['amountAll']*100).toFixed(2)
						};
						tplArr.push(me.formatRow(tpl, rowData));
					}
				}



				me.getRowTable().html(tplArr.join(''));

		},
		//yinglilv 盈利率
		//prize 所有注单的单倍价格
		//onePrice 单注单价
		//times 需要运行的期数
		//multiple 起始倍数
		//maxnum 最大可设的倍数
		getMultipleByYinglilv:function(yinglilv, prize, onePrice, times, multipleBase, maxnum){
				//总金额
				//debugger;
			var amountAll =  multipleBase * onePrice,
				//标记原始计算出的倍数
				oldMultiple = 0,
				//每次运算结果倍数变量
				multiple,
				i = 0,
				result = [];

			//当期倍数＝ceil((总花销*(1+盈利率)/(单倍奖金-单倍成本*(1+盈利率)))
			for(;i < times;i++){
				multiple = Math.ceil(   amountAll * (1 + yinglilv)  /  (prize - onePrice * (1 + yinglilv))   );
				if(multiple < 1){
					break;
				}
				oldMultiple = multiple;
				multiple = multiple > maxnum ? maxnum : multiple;
				if(i == 0){
					amountAll = multiple * onePrice;
				}else{
					amountAll = amountAll + (multiple * onePrice);
				}
				result.push({'multiple':multiple, 'amountAll':amountAll,'winAmountAll':prize * multiple - amountAll, 'oldMultiple':oldMultiple});
			}
			return result;
		},
		//通过固定盈利金额得到倍数
		//userGroupMoney 单注中奖金额
		//yingliMoney 需要达到的盈利金额
		//amount 单笔投注成本
		//amountAll 累计投注成本
		getMultipleByMoney:function(userGroupMoney, yingliMoney, amount, amountAll){
			var i = 1,mx = 100000;
			for(;i < mx;i++){
				if((userGroupMoney * i - amountAll - amount * i) > yingliMoney){
					return i;
				}
			}
			//无法达到目标
			return -1;
		},
		//根据玩法名称获得用户当前将进组中奖金额(以元模式为单位)
		//
		getPlayGroupMoneyByGameMethodName:function(mid){
			return Number(Games.getCurrentGame().getGameConfig().getInstance().getMethodById(mid)['prize']);
		},
		formatRow:function(tpl, data){
			var me = this,o = data,p,reg;
			for(p in o){
				if(o.hasOwnProperty(p)){
					reg = RegExp('<#=' + p + '#>', 'g');
					tpl = tpl.replace(reg, o[p]);
				}
			}
			return tpl;
		},
		//将数字保留两位小数并且千位使用逗号分隔
		formatMoney:function(num){
			var num = Number(num),
				re = /(-?\d+)(\d{3})/;

			if(Number.prototype.toFixed){
				num = (num).toFixed(2);
			}else{
				num = Math.round(num*100)/100
			}
			num  =  '' + num;
			while(re.test(num)){
				num = num.replace(re,"$1,$2");
			}
			return num;
		},
		getAdvancedTable:function(){
			var me = this;
			return me._advancedTable || (me._advancedTable = $('#J-trace-table-advanced'));
		},
		getAdvancedRowTable:function(){
			var me = this;
			return me._advancedTableContainer || (me._advancedTableContainer = $('#J-trace-table-advanced-body'));
		},
		getNormalTable:function(){
			var me = this;
			return me._table || (me._table = $('#J-trace-table'));
		},
		getNormalRowTable:function(){
			var me = this;
			return me._tableContainer || (me._tableContainer = $('#J-trace-table-body'));
		},
		getTable:function(){
			var me = this;
			if(me.isAdvanced()){
				return me._advancedTable || (me._advancedTable = $('#J-trace-table-advanced'));
			}
			return me._table || (me._table = $('#J-trace-table'));
		},
		getRowTable:function(type){
			var me = this;
			return $('#J-trace-table-'+ type +'-body');
		},
		setCurrentTraceNumber:function(v){
			var me = this;
			me.currentTraceNumber = v;
		},
		getCurrentTraceNumber:function(){
			return me.currentTraceNumber;
		},
		//追号起始期号
		setTraceStartNumber:function(v){
			var me = this;
			me.traceStartNumber = v;
		},
		getTraceStartNumber:function(){
			return me.traceStartNumber;
		},
		getMultiple:function(){
			var me = this;
			if(me.isAdvanced()){
				return me.getAdvancedMultiple();
			}
			return me.getNormalMultiple();
		},
		getNormalMultiple:function(){
			return Number(this.normalSelectMultiple.getValue());
		},
		getAdvancedMultiple:function(){
			return Number($('#J-trace-advance-multiple').val());
		},
		setIsWinStop:function(v){
			var me = this;
			this.isWinStop = !!v;
		},
		getTimes:function(type){
			var me = this,
			v = $('#J-trace-'+ type +'-times').val().replace(/[^\d]/g, ''),
			traceNumberList = Games.getCurrentGame().getGameConfig().getInstance().getGameNumbers();
			num = Number(v);
			num = num == isNaN(num) ? 1 : num;
			num = num <= 0 ? 1 : num;
			num = num > traceNumberList.length ? traceNumberList.length : num;
			return num;
		},
		//获取追号期数(高级)
		getAdvancedTimes:function(){
			return Number($('#J-trace-advanced-times').val());
		},
		//是否为高级追号
		isAdvanced:function(){
			var me = this;
			return me.traceType == 'lirunlv' ||  me.traceType == 'fanbei';
		},
		//切换追号类型
		setTraceType:function(type){
			var me = this;
			me.traceType = type;
		},
		getTraceType:function(){
			return this.traceType;
		},
		//获取已生成列表的追号类型
		getRowTableType:function(type){
			var me = this;
			return me.getRowTable(type).attr('data-type');
		},
		//清空已生成的列表
		emptyRowTable:function(){
			var me = this;
			$('#J-trace-table-body').html('');
			$('#J-trace-table-advanced-body').html('');
			me.updateStatistics();
		},
		show:function(){
			var me = this,
				orderAmount = Games.getCurrentGameOrder().getTotal()['amount'],msg = Games.getCurrentGameMessage();
			//是否有投注内容
			if(orderAmount <= 0){
					msg.show({
					type : 'mustChoose',
					msg : '请至少选择一注投注号码！',
					data : {
						tplData : {
							msg : '请至少选择一注投注号码！'
						}
					}
				});
				return;
			}
			//面板展开时将号码篮倍数设置为1倍
			Games.getCurrentGameOrder().editMultiples(1);

			me.showPanel();
		},
		hide:function(){
			/**
			var me = this;
			me.hidePanel();
			if(me.getIsTrace() == 1){
				Games.getCurrentGameOrder().editMultiples(1);
			}else{
				Games.getCurrentGameOrder().restoreMultiples();
			}
			**/
		},
		showPanel:function(){
			var me = this;
			host.Mask.getInstance().show();
			me.panel.show();
		},
		hidePanel:function(){
			var me = this;
			host.Mask.getInstance().hide();
			me.panel.hide();
		},
		applyTraceData:function(){
			var me = this,
				times = Number($.trim($('#J-trace-statistics-times').text())),
				num = Number($.trim($('#J-trace-statistics-lotterys-num').text())),
				amount = Number($.trim($('#J-trace-statistics-amount').text().replace(/[^\d|\.]/g,'')));

			//追号有内容
			if(times > 0){
				me.setIsTrace(1);
				Games.getCurrentGameOrder().setTotalLotterysNum(num);
				Games.getCurrentGameOrder().setTotalAmount(amount);
				//$('#J-trace-num-tip-panel').show();
				$('#J-trace-num-text').text(times);
			}else{
				//内容没有发生改变，恢复原来号码篮原来的倍数
				Games.getCurrentGameOrder().restoreMultiples();

				me.setIsTrace(0);
				//$('#J-trace-num-tip-panel').hide();
			}

			//host.Mask.getInstance().hide();
			//me.panel.hide();

		},
		//删除追号
		deleteTrace:function(){
			var me = this
			Games.getCurrentGameOrder().restoreMultiples();
			me.setIsTrace(0);
			//$('#J-trace-num-tip-panel').hide();
			//clear table row
			me.getTbodys().html('');
			me.updateStatistics();
		},
		getTbodys:function(){
			var me = this;
			return me.panel.find('tbody[data-type]');
		},
		//自动触发删除追号，该方法将触发一个轻提示
		autoDeleteTrace:function(){
			var me = this;
			me.deleteTrace();

			/**
			var me = this,tip = new host.Tip({cls:'j-ui-tip-b'});
			me.deleteTrace();
			tip.setText('由于您对注单进行了修改，追号被自动取消，<br />如需要继续追号，请重新设置追号');
			tip.show(-90, -60, $('#J-trace-switch'));
			setTimeout(function(){
				tip.getDom().fadeOut();
			},2000);
			**/
		},
		//复位追号区的tab以及相关输入框默认值
		reSetTab:function(){
			var me = this,
				tab1 = me.TraceTab,
				tab2 = me.TraceAdvancedTab;
			//追号tab
			tab1.triggers.removeClass(tab1.defConfig.currClass);
			tab1.triggers.eq(0).addClass(tab1.defConfig.currClass);
			tab1.panels.removeClass(tab1.defConfig.currPanelClass);
			tab1.panels.eq(0).addClass(tab1.defConfig.currPanelClass);
			tab1.index = 0;
			//高级追号tab
			tab2.triggers.removeClass(tab2.defConfig.currClass);
			tab2.triggers.eq(0).addClass(tab2.defConfig.currClass);
			tab2.panels.removeClass(tab2.defConfig.currPanelClass);
			tab2.panels.eq(0).addClass(tab2.defConfig.currPanelClass);
			tab2.index = 0;

			//恢复输入框默认值
			$('#J-trace-normal-times').val(10);
			$('#J-function-select-tab .function-select-title li').removeClass('current').eq(1).addClass('current');
			me.normalSelectMultiple.setValue(1);

			$('#J-trace-advanced-times').val(10);
			$('#J-trace-advance-multiple').val(1);
			$('#J-trace-advanced-fanbei-a-jiange').val(2);
			$('#J-trace-advanced-fanbei-a-multiple').val(2);
			$('#J-trace-advanced-fanbei-b-num').val(5);
			$('#J-trace-advanced-fanbei-b-multiple').val(3);
			$('#J-trace-advanced-yingli-a-money').val(100);
			$('#J-trace-advanced-yingli-b-num').val(2);
			$('#J-trace-advanced-yingli-b-money1').val(100);
			$('#J-trace-advanced-yingli-b-money2').val(50);
			$('#J-trace-advanced-yinglilv-a').val(10);
			$('#J-trace-advanced-yinglilv-b-num').val(5);
			$('#J-trace-advanced-yingli-b-yinglilv1').val(30);
			$('#J-trace-advanced-yingli-b-yinglilv2').val(10);


			//设置对应的tab标记属性
			me.setTraceType('lirunlv');
			me.advancedType = me.defConfig.advancedTypeHas[0];
			me.typeTypeType = 'a';

			//恢复默认的高级选项
			$('#J-trace-advanced-type-panel').find('input[type="radio"]').each(function(i){
				if((i+1)%2 != 0){
					var el = $(this),par = el.parent(),pars = par.parent().children(),_par;
					this.checked = true;
					pars.each(function(i){
						_par = pars.get(i);
						if(par.get(0) != _par){
							$(_par).find('input[type="text"]').attr('disabled', 'disabled');
						}else{
							$(_par).find('input[type="text"]').attr('disabled', false);
						}
					});
				}
			});

		}
	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;

})(bomao, "GameTrace", bomao.Event);











//游戏订单模块
(function(host, name, Event, undefined) {
	var defConfig = {},
		//缓存游戏实例
		instance,
		//获取游戏类
		Games = host.Games;

	var pros = {
		//初始化
		init: function(cfg) {
			var me = this,
				cfg = me.defConfig;

			//提交数据加锁
			//防止多次重复提交
			me.postLock = null;
			//缓存方法
			Games.setCurrentGameSubmit(me);
		},
		//获取当前投注信息
		//提交的数据标准格式
		/**
		result = {
			//游戏类型
			gameType:'ssc',
			//订单总金额
			amount:100,
			//是否是追号
			isTrace:1,
			//追号追中即停(1为按中奖次数停止，2为按中奖金额停止)
			traceWinStop:1,
			//追号追中即停的值
			traceStopValue:1,
			//选球信息
			balls:[{ball:'1,2,3,4',type:'wuxing.zhixuan.fushi',moneyunit:0.1,multiple:1,id:2},{ball:'选球数据',type:'玩法类型',moneyunit:元角模式,multiple:倍数,id:ID编号}],
			//投注信息
			orders:[{number:'201312122204',multiple:2},{number:'期号',multiple:倍数}]

		};
		**/
		getSubmitData: function() {
			var me = this,
				result = {},
				ballsData = Games.getCurrentGameOrder()['orderData'],
				i = 0,
				len = ballsData.length,
				traceInfo = Games.getCurrentGameTrace().getResultData(),
				j = 0,
				len2 = traceInfo['traceData'].length;

			//console.log(ballsData);


			result['gameId'] = Games.getCurrentGame().getGameConfig().getInstance().getGameId();
			//result['gameType'] = Games.getCurrentGame().getName();
			result['isTrace'] = Games.getCurrentGameTrace().getIsTrace();
			result['traceWinStop'] = Games.getCurrentGameTrace().getIsWinStop();
			result['traceStopValue'] = Games.getCurrentGameTrace().getTraceWinStopValue();
			result['balls'] = [];
			for (; i < len; i++) {
				result['balls'].push({
					'jsId': ballsData[i]['id'],
					'wayId': ballsData[i]['mid'],
					'ball': ballsData[i]['postParameter'].split(',').join('|'),
					'position':ballsData[i]['position'],
					'viewBalls':ballsData[i]['viewBalls'],
					'num': ballsData[i]['num'],
					'type': ballsData[i]['type'],
					'onePrice': ballsData[i]['onePrice'],
					'prize_group':ballsData[i]['prize_group'],
					'moneyunit': ballsData[i]['moneyUnit'],
					'multiple': ballsData[i]['multiple']
				});
			}
			//console.log(result);

			/**
			result['orders'] = [];
			//非追号
			if(result['isTrace'] < 1){
				//获得当前期号
				result['orders'].push({'number':Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber(),multiple:1});
				//总金额
				result['amount'] = Games.getCurrentGameOrder().getTotal()['amount'];
			}else{
			//追号
				for(;j < len2;j++){
					result['orders'].push({'number':traceInfo['traceData'][j]['traceNumber'].replace(/[^\d]/g, ''),'multiple':traceInfo['traceData'][j]['multiple']});
				}
				//总金额
				result['amount'] = traceInfo['amount'];
			}
			**/
			//投注期数格式修改为键值对
			result['orders'] = {};
			//非追号
			if (result['isTrace'] < 1) {
				//获得当前期号，将期号作为键
				result['orders'][Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber()] = 1;
				//总金额
				result['amount'] = Games.getCurrentGameOrder().getTotal()['amount'];
			} else {
				//追号
				for (; j < len2; j++) {
					result['orders'][traceInfo['traceData'][j]['traceNumber']] = traceInfo['traceData'][j]['multiple'];
				}
				//总金额
				result['amount'] = traceInfo['amount'];
			}


			return result;
		},
		//执行请求锁定动作
		doPostLock: function() {
			var me = this;
			me.postLock = true;
		},
		//取消请求锁定动作
		cancelPostLock: function() {
			var me = this;
			me.postLock = false;
		},
		//清空数据缓存
		clearData: function() {
			var order = Games.getCurrentGameOrder();
			//清空订单
			order.reSet();
			//添加取消编辑
			order.cancelSelectOrder();
			//清空
			Games.getCurrentGame().getCurrentGameMethod().reSet();
		},
		fastSubmitData:function(order){
			var me = this,
				ballsData = [],
				amount = 0,
				i = 0,
				len,
				result = {};

			ballsData.push(order);
			len = ballsData.length;


			result['gameId'] = Games.getCurrentGame().getGameConfig().getInstance().getGameId();
			//result['gameType'] = Games.getCurrentGame().getName();
			result['isTrace'] = 0;
			result['traceWinStop'] = 1;
			result['traceStopValue'] = 1;
			result['balls'] = [];
			for (; i < len; i++) {
				result['balls'].push({
					'jsId': ballsData[i]['id'],
					'wayId': ballsData[i]['mid'],
					'ball': ballsData[i]['postParameter'].split(',').join('|'),
					'position':ballsData[i]['position'],
					'viewBalls':ballsData[i]['viewBalls'],
					'num': ballsData[i]['num'],
					'type': ballsData[i]['type'],
					'onePrice': ballsData[i]['onePrice'],
					'prize_group':ballsData[i]['prize_group'],
					'moneyunit': ballsData[i]['moneyUnit'],
					'multiple': ballsData[i]['multiple']
				});
				amount += (ballsData[i]['num'] * ballsData[i]['onePrice'] * ballsData[i]['moneyUnit'] * ballsData[i]['multiple']);
			}


			//投注期数格式修改为键值对
			result['orders'] = {};
			//获得当前期号，将期号作为键
			result['orders'][Games.getCurrentGame().getGameConfig().getInstance().getCurrentGameNumber()] = 1;
			//总金额
			result['amount'] = amount;


			me.submitData(true, result);

		},
		//提交游戏数据
		//isFastOrder 是否为快速订单
		submitData: function(isFastOrder, orderData) {
			var me = this,
				data = isFastOrder ? orderData : me.getSubmitData(),
				message = Games.getCurrentGameMessage(),
				subData;
			//判断加锁
			if (me.postLock) {
				return;
			}
			//提示至少选择一注
			if (data.balls.length <= 0) {
				message.show({
					type: 'mustChoose',
					msg: '请至少选择一注投注号码！',
					data: {
						tplData: {
							msg: '请至少选择一注投注号码！'
						}
					}
				});
				//请求解锁
				me.cancelPostLock();
				return;
			}



			//data = Games.getCurrentGame().editSubmitData(data);
			//console.log(Games.getCurrentGame().editSubmitData(data));



			/**
			//彩种检查
			message.show({
				type: 'checkLotters',
				msg: '请核对您的投注信息！',
				confirmIsShow: true,
				confirmFun: function() {
					if (me.postLock) {
						return;
					}
					//console.log(Games.getCurrentGame().editSubmitData(data));
					$.ajax({
						url: Games.getCurrentGame().getGameConfig().getInstance().getSubmitUrl(),
						data: Games.getCurrentGame().editSubmitData(data),
						dataType: 'json',
						method: 'POST',
						beforeSend:function(){
							me.doPostLock();
							me.fireEvent('beforeSend', message);
						},
						success: function(r) {
							//返回消息标准
							// {"isSuccess":1,"type":"消息代号","msg":"返回的文本消息","data":{xxx:xxx}}
							if (Number(r['isSuccess']) == 1) {
								message.show(r);
								me.clearData();
								me.fireEvent('afterSubmitSuccess');
							} else {
								message.show(r);
							}

							//请求解锁
							me.cancelPostLock();
						},
						complete: function() {
							me.fireEvent('afterSubmit', message);
						},
						error: function() {
							me.cancelPostLock();
						}
					});
				},
				cancelIsShow: true,
				cancelFun: function() {
					//请求解锁
					me.cancelPostLock();
					this.hide();
				},
				normalCloseFun: function() {
					//请求解锁
					me.cancelPostLock();
				},
				callback: function() {
				},
				data: {
					tplData: {
						//当期彩票详情
						lotteryDate: '--',
						//彩种名称
						lotteryName: Games.getCurrentGame().getGameConfig().getInstance().getGameNameCn(),
						//投注详情
						lotteryInfo: function() {
							var html = '',
								gmConfig = Games.getCurrentGame().getGameConfig().getInstance(),
								balls = data['balls'];
							//console.log(balls);
							for (var i = 0; i < balls.length; i++) {
								var current = balls[i];
								html += '<div class="game-submit-confirm-list">' + gmConfig.getMethodCnFullNameById(current['wayId']).join(',') + ' ' + current['viewBalls'] + '</div>';
							};
							return html;
						},
						//彩种金额
						lotteryamount: host.util.formatMoney(data['amount']),
						//付款帐号
						lotteryAcc: Games.getCurrentGame().getGameConfig().getInstance().getUserName()
					}
				}
			});
			**/


			data['amount'] = (Number(data['amount'])).toFixed(2);
			// data['_token'] = _globalObj._token;
			subData = Games.getCurrentGame().editSubmitData(data);
			$.ajax({
				url: Games.getCurrentGame().getGameConfig().getInstance().getSubmitUrl(),
				data: subData,
				dataType: 'json',
				method: 'POST',
				beforeSend:function(){
					me.doPostLock();
					me.fireEvent('beforeSend', message);
					message.showTip('提交中...');
					Games.getCurrentGameTrace().deleteTrace();
				},
				success: function(r) {
					//返回消息标准
					// {"isSuccess":1,"type":"消息代号","msg":"返回的文本消息","data":{xxx:xxx}}
					if (Number(r['isSuccess']) == 1) {
						message.show(r);
						me.clearData();
						me.fireEvent('afterSubmitSuccess', subData);
					} else {
						message.show(r);
					}
					//请求解锁
					me.cancelPostLock();
				},
				complete: function(r) {
					me.fireEvent('afterSubmit', message);
					message.hideTip();
				},
				error: function(r) {
					me.cancelPostLock();
				}
			});




		}
	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	Main.getInstance = function(cfg) {
		return instance || (instance = new Main(cfg));
	};
	host[name] = Main;

})(bomao, "GameSubmit", bomao.Event);
//records
(function(host, name, Event, undefined) {
	var defConfig = {
			dom:'',
			iframe:'',
			url:''
		},
		instance,
		Games = host.Games;

	var pros = {
		//初始化
		init: function(cfg) {
			var me = this,
				cfg = me.defConfig;
			me.dom = $(cfg.dom);
			me.iframe = $(cfg.iframe);
			me.url = cfg.url;
		},
		show:function(){
			var me = this,mask;
			me.refresh();
			me.showMask();
			me.dom.show();
		},
		hide:function(){
			var me = this;
			me.dom.hide();
			me.hideMask();
		},
		showMask:function(){
			var me = this,mask = host.Mask ? host.Mask.getInstance() : null;
			if(mask){
				me.mask = mask;
				mask.show();
			}
		},
		hideMask:function(){
			var me = this;
			if(me.mask){
				me.mask.hide();
			}
			me.dom.hide();
		},
		refresh:function(){
			var me = this;
			me.iframe.attr('src', me.url + '?_=' + Math.random());
		}
	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	Main.getInstance = function(cfg) {
		return instance || (instance = new Main(cfg));
	};
	host[name] = Main;

})(bomao, "GameRecords", bomao.Event);

//桌面游戏基类
(function(host, name, Event, undefined){
	var defConfig = {
		updateConfigTime:1 * 1000
	};





	var pros = {
		init:function(cfg){
			var me = this;
			me._currentNumber = null;
			me.timer_update = null;
			me._currNumber = null;
			//投注区域列表
			me._areas = {};
		},
		getCurrNumber:function(){
			return this._currNumber;
		},
		setCurrNumber:function(number){
			this._currNumber = number;
		},
		getCurrentNumber:function(){
			return this._currentNumber;
		},
		setCurrentNumber:function(number){
			this._currentNumber = number;
		},
		updateConfig:function(){
			var me = this;
			clearTimeout(me.timer_update);
			$.ajax({
				url:me.getConfig('loaddataUrl'),
				dataType:'JSON',
				success:function(data){
					if(Number(data['isSuccess']) == 1){
						var newCfg = $.extend(me._serverCfg, data['data'], true);
						me.setConfig(newCfg)
						me.fireEvent('updateConfig_after', me.getConfig());
					}else{
						if(!!console && console.log){
							console.log('后台数据错误:' + me.getConfig('loaddataUrl'));
						}
						//alert('更新数据失败，请刷页面重试 ' + data['msg']);
					}
				},
				error:function(xhr, type){
					//alert('更新数据失败，请刷页面重试 ' + type);
				},
				complete:function(){
					clearTimeout(me.timer_update);
					me.timer_update = setTimeout(function(){
						me.updateConfig();
					}, me.defConfig.updateConfigTime);
					
				}
			});
		},
		getConfig:function(key){
			var me = this;
			if(key){
				return me._serverCfg[key];
			}
			return me._serverCfg;
		},
		setConfig:function(serverCfg){
			this._serverCfg = serverCfg;
		},
		// 获取实时奖期状况
		getRealTimeGameInfo:function(){
			var me = this;
			$.ajax({
				url:me.getConfig('loaddataUrl'),
				dataType:'JSON',
				success:function(data){
					if(Number(data['isSuccess']) == 1){
						var newCfg = $.extend(me._serverCfg, data['data'], true);
						me.setConfig(newCfg);
						me.fireEvent('getRealTimeGameInfo_after', data['data']);
					}else{
						if(!!console && console.log){
							console.log('后台数据错误:' + me.getConfig('loaddataUrl'));
						}
						//alert('更新数据失败，请刷页面重试 ' + data['msg']);
						me.getRealTimeGameInfo();
					}
				},
				error:function(xhr, type){
					//alert('更新数据失败，请刷页面重试 ' + type);
					me.getRealTimeGameInfo();
				},
				complete:function(){
					
				}
			});

		},
		//将树状数据整理成两级缓存数据
		getGameMethods:function(){
			var me = this,
				nodeCache = {},
				methodCache = {},
				data = me.getConfig("gameMethods"),
				node1,
				node2,
				node3;

			$.each(data, function(){
				node1 = this;
				node1['fullname_en'] = [node1['name_en']];
				node1['fullname_cn'] = [node1['name_cn']];
				nodeCache['' + node1['id']] = node1;
				if(node1['children']){
					$.each(node1['children'], function(){
						node2 = this;
						node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
						node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
						nodeCache['' + node2['id']] = node2;
						if(node2['children']){
							$.each(node2['children'], function(){
								node3 = this;
								node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
								node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
								methodCache['' + node3['id']] = node3;
							});
						}
					});
				}
			});
			return methodCache;
		},
		getArea:function(name){
			return this._areas[name];
		},
		getAreas:function(){
			return this._areas;
		},
		getDeskTopDom:function(){
			return this._deskTop || (this._deskTop = $('#J-desktop'));
		},
		initDeskTop:function(areasConfig){
			var me = this,
				html = [],
				it;
			$.each(areasConfig, function(){
				it = this;
				if(it['empty']){
					html.push('<div style="width:'+ it['width'] +'px;height:'+ it['height'] +'px;left:'+ it['left'] +'px;top:'+ it['top'] +'px;" class="area area-empty area-empty-'+ it['empty'] +'">');
					if(it['helpPos']){
						html.push('<div style="left:'+ it['helpPos'][0] +'px;top:'+ it['helpPos'][1] +'px;" helpText="'+it['helpHtml']+'" class="help">?</div>');
					}
					if(it['oddsPos']){
						if(it['empty'] == "yima"){
							html.push('<div style="left:40px;top:10px;" class="odds"><span class="cn-charactor">单骰</span>1:1</div>');
							html.push('<div style="left:40px;top:30px" class="odds"><span class="cn-charactor">双骰</span>1:2</div>');
							html.push('<div style="left:40px;top:50px;" class="odds"><span class="cn-charactor">全骰</span>1:3</div>');
						}
						else{
							html.push('<div style="left:'+ it['oddsPos'][0] +'px;top:'+ it['oddsPos'][1] +'px;" class="odds">1:'+ it['prize_odds'] +'</div>');
						}
						
					}
					html.push('</div>');
				}else{
					html.push('<div data-action="addchip" data-name="'+ it['name_en'] +'" style="width:'+ it['width'] +'px;height:'+ it['height'] +'px;left:'+ it['left'] +'px;top:'+ it['top'] +'px;" class="area area-'+ it['name_en'] +'">');
					html.push('<div class="appearance-time"></div>')
					if(it['helpPos']){
						html.push('<div style="left:'+ it['helpPos'][0] +'px;top:'+ it['helpPos'][1] +'px;" helpText="'+it['helpHtml']+'" class="help">?</div>');
					}
					if(it['oddsPos']){
						html.push('<div style="left:'+ it['oddsPos'][0] +'px;top:'+ it['oddsPos'][1] +'px;" class="odds">1:'+ it['prize_odds'] +'</div>');
					}
					html.push('</div>');
					me.addArea(it);
				}
			});
			$(html.join('')).appendTo(me.getDeskTopDom());

		},
		addArea:function(opt){
			var me = this,
				area = new host.TableGame.Area(opt),
			    chips = new host.TableGame.Chips();

			chips.addEvent('delLastChip_after', function(e, chip){
				area.fireEvent('cancelLastChip', chip);
			});


			area.chips = chips;
			me._areas[opt['name_en']] = area;
			
		},
		update:function(){
			var me = this;
			me.fireEvent('update_after');
		},
		getResult:function(){
			var me = this,
				area,
				result,
				nums = 0,
				money = 0;
			$.each(me.getAreas(), function(){
				area = this;
				result = area.getResult();
				nums += result['chipsnum'];
				money += result['money'];
			});
			return {'chipsnum':nums, 'money':money};
		},
		//获取桌面所有筹码
		getAllChips:function(){
			var me = this,
				areas = me.getAreas(),
				result = [],
				p;

			for(p in areas){
				if(areas.hasOwnProperty(p)){
					$.each(areas[p].getChipsCase(), function(){
						result.push(this);
					});
				}
			}
			
			return result;
		},
		cancelAll:function(){
			var me = this,
				money = 0;
			me.fireEvent('cancelAll_before');
			$.each(me.getAreas(), function(){
				area = this;
				money += area.getResult()["money"]; 
				area.clearAll();

			});
			me.update();
			me.fireEvent('cancelAll_after',money);
		},
		playerGet:function(){
			var me = this,
				money = 0;
			// me.fireEvent('playerGet_before');
			$.each(me.getAreas(),function(){
				area = this;
				money += area.getResult()['money'];

				if(area.getChipsCase().length>0){
					me.fireEvent('playerGet_after',area.getChipsCase());
				}

				area.clearAll();


			})
			me.update();
			
		},
		editSubmitData:function(data){
			var balls = data['balls'];
			data['balls'] = encrypt(JSON.stringify(balls));
			data['is_encoded'] = 1;
			return data;
		},
		getSubmitData:function(){
			var me = this,
				areas = me.getAreas(),
				_area,
				_chips,
				_money = 0,
				i = 0,
				len = areas.length,
				amount = 0,
				num = 1,
				way = "",
				type = "",
				wayId = 1,
				ball = "",
				multiple = 1;
				prize_group = 0,
				result = {},
				methods = me.getGameMethods(),
				method = {},


			result['gameId'] = me.getConfig('gameId');
			result['isTrace'] = 0;
			result['traceWinStop'] = 0;
			result['traceStopValue'] = 0;
			result['balls'] = [];
			$.each(areas, function(){
				_area = this;
				_chips = _area.getChipsCase();
				_money = 0;
				if(_chips.length > 0){
					$.each(_chips, function(){
						_money += this.getMoney();
					});

					// 倍数，总额/2分单价
					multiple=_money*10*10/2;

					way = _area.getName().split("-")[0];
					digit = _area.getName().split("-")[1];
					switch(way){
						case "yima":
							method = methods["238"];
							wayId = method.id;
							type = method.name_en;
							ball = digit;
							prize_group = 1800;
							break;
						case "erma":
							method = methods["240"];
							wayId = method.id;
							type = method.name_en;
							ball = digit;
							prize_group = 1800;
							break;
						case "baozi":
							if(digit=="n"){
								method = methods["244"];
								wayId = method.id;
								type = method.name_en;
								ball = 0;
								prize_group = 1800;
							}else{
								method = methods["239"];
								wayId = method.id;
								type = method.name_en;
								ball = digit;
								prize_group = 1800;
							}
							break;
						case "duizi":
							method = methods["241"];
							wayId = method.id;
							type = method.name_en;
							ball = digit;
							prize_group = 1800;
							break;
						case "hezhi":
							method = methods["242"];
							wayId = method.id;
							type = method.name_en;
							ball = digit;
							prize_group = 1800;
							break;
						case "big":
							method = methods["243"];
							wayId = method.id;
							type = method.name_en;
							ball = 1;
							prize_group = 1800;
							break;
						case "small":
							method = methods["243"];
							wayId = method.id;
							type = method.name_en;
							ball = 0;
							prize_group = 1800;
							break;
						case "even":
							method = methods["243"];
							wayId = method.id;
							type = method.name_en;
							ball = 2;
							prize_group = 1800;
							break;
						case "odd":
							method = methods["243"];
							wayId = method.id;
							type = method.name_en;
							ball = 3;
							prize_group = 1800;
							break; 
						default:
							break;
					}


					// 每个区域对应的玩法、数字、单价、注数、模式、倍数
					result['balls'].push({
						// 玩法ID
						'wayId': wayId,
						// 玩法英文名
						'type': type,
						// 数字
						'ball': ball,
						// 注数
						'num': num,
						// 单价：2分
						'onePrice': 2,
						// 模式：分模式
						'moneyunit': 0.01,
						// 倍数：1，默认为1
						'multiple': multiple,
						// 奖金组
						'prize_group':prize_group
					});
					// 元转换成分
					amount += _money*10*10;

				}
			});

			//投注期数格式修改为键值对
			result['orders'] = {};
			//获得当前期号，将期号作为键
			result['orders'][me.getCurrNumber()] = 1;
			//总金额
			result['amount'] = amount;

			result['_token'] = me.getConfig('_token');

			var betInfoR = {"balls":result['balls'],'isFinish':true,'issue':me.getCurrNumber()},
				betInfo =  $.extend({},betInfoR);
			me.setLastBetInfo(betInfo);
			return result;

		},
		setLastBetInfo:function(lastBetInfo){
			var me = this;
			me.lastBetInfo = lastBetInfo;
		},
		getLastBetInfo:function(){
			var me = this;
			return me.lastBetInfo;
		},

		submit:function(){
			var me = this,
				data = me.getSubmitData(),
				url = me.getConfig('submitUrl');
			// data['gameid'] = me.getConfig('gameId');
			// data['_token'] = me.getConfig('_token');
			$.ajax({
				url:url,
				dataType:'JSON',
				method:'POST',
				data:me.editSubmitData(data),
				beforeSend:function(){
					me.fireEvent('submit_before', data);
				},
				success:function(data){
					if(Number(data['isSuccess']) == 1){
						me.fireEvent('success_after', data);
					}else{
						alert(data['Msg']);
					}
				}
			});
			
		}



	};

		
	

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
	host[name] = Main;
	
})(bomao, "TableGame", bomao.Event);








//桌面游戏基类
(function(host, TableGame, undefined){
	var defConfig = {

	};



	var pros = {
		init:function(cfg){

		}
	};
	
		
	

	var Main = host.Class(pros, TableGame);
		Main.defConfig = defConfig;
	TableGame.Dice = Main;
	
})(bomao, bomao.TableGame);







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

//筹码对象
(function(host, Event, $, undefined) {
    var defConfig = {

        },
        autoId = 1;

    var pros = {
        init: function(cfg) {
            var me = this;
            me.id = autoId++;
            me.money = cfg.money;
            me.marginTop = cfg.marginTop;
            me.isAvaliable = true;
        },
        getId: function() {
            return this.id;
        },
        getMoney: function() {
            return this.money;
        },
        getMarginTop:function(){
            return this.marginTop;
        },
        setStatus: function(isAvaliable) {
            var me = this;
            me.isAvaliable = isAvaliable;
            me.fireEvent("setStatus_after", isAvaliable);
        },
        getStatus: function() {
            return this.isAvaliable;
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Chip = Main;

})(bomao, bomao.Event, jQuery);

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


//右键菜单类
(function(host, Event, $, undefined) {


    var defConfig = {
        //外容器
        warpHtml:'<div class="game-contextmenu"></div>',
        //菜单行选择符
        rowSelection:'.item',
        //文本类型
        tpl_text:'<div class="item txt-item" data-action="<#=action#>"><#=title#></div>',
        //图标类型
        tpl_icon:'<div class="item icon-text-item"  data-action="<#=action#>"><img src="<#=src#>"><#=name#></div>'
    }


    var pros = {
        init:function(cfg) {
            var me = this;
            me.data = null;
            me.dom = $(me.defConfig.warpHtml);
            me.dom.appendTo(document.body);

            me.initEvent();
        },
        initEvent:function(){
            var me = this;
            me.dom.on('click', me.defConfig.rowSelection, function(e){
                var el = $(this),
                    action = el.attr('data-action');
                me.fireEvent('click', action, el);
            });
            $(document).on('mousedown', function(e){
                if(!$.contains(me.dom.get(0), e.target)){
                    me.hide();
                }
            });
        },
        setData:function(data){
            this.data = data;
        },
        getData:function(){
            return this.data;
        },
        //{type:菜单类型, action:菜单命令, tpl:自定义模板}
        addItem:function(opt) {
            var me = this,
                cfg = me.defConfig,
                tpl = !!opt.tpl ? opt.tpl : cfg['tpl_' + (opt.type ? opt.type : 'text')];
            tpl = host.util.template(tpl, opt);
            $(tpl).appendTo(me.dom);
        },
        show:function(x, y, zIndex) {
            var me = this,
                win = $(window),
                x = x + win.scrollLeft(),
                y = y + win.scrollTop();
            me.fireEvent('show_before');
            if(typeof zIndex == 'undefined'){
                me.dom.css({
                    left:x,
                    top:y
                });
            }else{
                me.dom.css({
                    left:x,
                    top:y,
                    zIndex:zIndex
                });
            }
            me.effectShow(function(){
                me.fireEvent('show_after')
            });
        },
        hide:function() {
            var me = this;
            me.fireEvent('hide_before');
            me.effectHide(function(){
                me.fireEvent('hide_after');
            });
        },
        effectShow:function(callback){
            var me = this;
            me.dom.show();
            if(callback){
                callback.call(me);
            }
        },
        effectHide:function(callback){
            var me = this;
            me.dom.hide();
            if(callback){
                callback.call(me);
            }
        }
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.ContextMenu = Main;


})(bomao, bomao.Event, jQuery);

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




//余额对象
(function(host, Event, $, undefined) {
    var defConfig = {

    };

    var pros = {
        init: function(cfg) {
            var me = this;
            me.balance = 0;
        },
        initUserBalance:function(money){
            var me = this;
            me.balance = money;
            me.fireEvent("setUserBalance_after", me.balance);
        },
        setUserBalance: function(money) {
            var me = this;
            me.balance += money;
            me.fireEvent("setUserBalance_after", me.balance);
        },
        getUserBalance: function() {
            return this.balance;
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.UserBalance = Main;

})(bomao, bomao.Event, jQuery);

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

//扑克牌对象
(function(host, Event, $, undefined) {
    var defConfig = {
            
        };

    var pros = {
        init: function(cfg) {
            var me = this;
            me._value = cfg.value;
            me._attrs = cfg.attrs;
            me._cls = cfg.cls;
            me._par = $(typeof cfg['par'] != 'undefined' ? cfg['par'] : document.body);

            me._dom = $('<div class="'+ me.getCls() +'"></div>');
            me._dom.appendTo(me.getPar());
        },
        getPar:function(){
            return this._par;
        },
        getDom:function(){
            return this._dom;
        },
        getValue:function(){
            return this._value;
        },
        getCls:function(){
            return this._cls;
        },
        getAttr:function(key){
            var me = this;
            return me._attrs[key];
        },
        setPos:function(x, y){
            var me = this,
                dom = me.getDom();
            dom.css({
                left:x,
                top:y
            });
        },
        addClass:function(cls){
            var me = this,
                dom = me.getDom();
            dom.addClass(cls);
        },
        removeClass:function(){
            var me = this,
                dom = me.getDom();
            dom.removeClass(cls);
        },
        moveTo:function(x, y){
            var me = this;

        },
        showCard:function(){
            var me = this;
            me.getDom().removeClass('poker-red poker-blue');
        },
        coverCard:function(cls){
            var me = this;
            me.getDom().addClass(cls);
        },
        destroy:function(){
            var me = this;
            me.dom.remove();
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.Poker = Main;

})(bomao, bomao.Event, jQuery);

//扑克牌管理器
(function(host, Event, $, undefined) {
    var defConfig = {
            pkcls:'poker'
        },
        Poker = host.TableGame.Poker;

    var pros = {
        init: function(cfg) {
            var me = this;
            me._attrCfg = {};
            me._container = cfg.container ? $(cfg.container) : $(document.body);
        },
        getContainer:function(){
            return this._container;
        },
        getPoker:function(value, cls){
            var me = this,
                value = Number(value);
            return new Poker({
                cls:me.defConfig.pkcls + ' poker-' + value + (cls || ' poker-red'),
                value:value,
                par:me.getContainer(),
                attrs:me.getPokerAttr(value)
            });
        },
        //设置扑克牌属性配置
        setAttrConfig:function(cfg){
            var me = this;
            me._attrCfg = cfg;
        },
        getAttrConfig:function(){
            return this._attrCfg;
        },
        getPokerAttr:function(value){
            var me = this,
                cfg = me.getAttrConfig();
            value = Number(value) < 10 ? '0'+value : '' + Number(value);
            return cfg[value];
         }

    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.PokerManager = Main;

})(bomao, bomao.Event, jQuery);

//龙虎斗类,
(function(host, TableGame, undefined) {
    var defConfig = {

    };



    var pros = {
        init: function(cfg) {
            var me = this;
            me._areas = {};
            me._methods = {};
        },
        getDeskTopDom: function() {
            return this._deskTop || (this._deskTop = $('#J-desktop'));
        },
        addArea: function(opt) {
            var me = this,
                area = new host.TableGame.Area(opt),
                chips = new host.TableGame.Chips();

            chips.addEvent('delLastChip_after', function(e, chip) {
                area.fireEvent('cancelLastChip', chip);
            });
            area.chips = chips;
            me._areas[opt['name_en']] = area;

        },
        initDeskTop: function(areasConfig) {
            var me = this,
                html = [],
                it;
            $.each(areasConfig, function(i) {
                it = this;
                if (i < 6) {
                    html.push('<div data-action="addchip" data-name="' + it['name_en'] + '" style="width:' + it['width'] + 'px;height:' + it['height'] + 'px;left:' + it['left'] + 'px;top:' + it['top'] + 'px;background-position:' + it['bgPosition'][0] + 'px ' + it['bgPosition'][1] + 'px" class="area area-' + it['name_en'] + '">');
                } else {
                    html.push('<div data-action="addchip" data-name="' + it['name_en'] + '" style="width:' + it['width'] + 'px;height:' + it['height'] + 'px;right:' + it['right'] + 'px;top:' + it['top'] + 'px;background-position:' + it['bgPosition'][0] + 'px ' + it['bgPosition'][1] + 'px" class="area area-' + it['name_en'] + '">');
                }

                if (it['oddsPos']) {
                    html.push('<div style="left:' + it['oddsPos'][0] + 'px;top:' + it['oddsPos'][1] + 'px;" class="odds">1:' + it['prize_odds'] + '</div>');
                }
                html.push('</div>');
                me.addArea(it);
            });
            $(html.join('')).appendTo(me.getDeskTopDom());
        },
        editSubmitData: function(data) {
            var balls = data['balls'];
            data['balls'] = encrypt(JSON.stringify(balls));
            data['is_encoded'] = 1;
            return data;
        },
        getSubmitData: function() {
            var me = this,
                areas = me.getAreas(),
                _area,
                _chips,
                _money = 0,
                i = 0,
                len = areas.length,
                amount = 0,
                num = 1,
                way = "",
                type = "",
                wayId = 1,
                ball = "",
                ball = 0,
                multiple = 1,
                prize_group = 0,
                result = {},
                methods = me.getGameMethods(),
                wayIdAndBall={},
                method = {};

            me._methods = me.getGameMethods();
            result['gameId'] = me.getConfig('gameId');
            result['isTrace'] = 0;
            result['traceWinStop'] = 0;
            result['traceStopValue'] = 0;
            result['balls'] = [];
            $.each(areas, function() {
                var _area = this,
                    _chips = _area.getChipsCase(),
                    _money = 0,
                    wayId = 0;

                // 根据areaName获得这个area的wayId以及ball
                if (_chips.length > 0) {
                    $.each(_chips, function() {
                        _money += this.getMoney();
                    });

                    // 倍数，总额/2分单价
                    multiple = _money * 10 * 10 / 2;

                    // way = _area.getName().split("-")[0];
                    // digit = _area.getName().split("-")[1];
                    
                    wayIdAndBall = me.getWayIdAndBall(_area);

                    wayId = wayIdAndBall.wayId;
                    ball = wayIdAndBall.ball;

                    // 每个区域对应的玩法、数字、单价、注数、模式、倍数
                    result['balls'].push({
                        // 玩法ID
                        'wayId': wayId,
                        // 玩法英文名
                        'type': type,
                        // 数字
                        'ball': ball,
                        // 注数
                        'num': num,
                        // 单价：2分
                        'onePrice': 2,
                        // 模式：分模式
                        'moneyunit': 0.01,
                        // 倍数：1，默认为1
                        'multiple': multiple,
                        // 奖金组
                        'prize_group': prize_group
                    });
                    // 元转换成分
                    amount += _money * 10 * 10;

                }
            });

            //投注期数格式修改为键值对
            result['orders'] = {};
            //获得当前期号，将期号作为键
            result['orders'][me.getCurrNumber()] = 1;
            //总金额
            result['amount'] = amount;

            result['_token'] = me.getConfig('_token');

            var betInfoR = {
                    "balls": result['balls'],
                    'isFinish': true,
                    'issue': me.getCurrNumber()
                },
                betInfo = $.extend({}, betInfoR);
            me.setLastBetInfo(betInfo);
            return result;

        },
        submit: function() {
            var me = this,
                data = me.getSubmitData(),
                url = me.getConfig('submitUrl');
            // data['gameid'] = me.getConfig('gameId');
            // data['_token'] = me.getConfig('_token');
            $.ajax({
                url: url,
                dataType: 'JSON',
                method: 'POST',
                data: me.editSubmitData(data),
                beforeSend: function() {
                    me.fireEvent('submit_before', data);
                },
                success: function(data) {
                    if (Number(data['isSuccess']) == 1) {
                        me.fireEvent('success_after', data);
                    } else {
                        alert(data['Msg']);
                    }
                }
            });

        },

        //将树状数据整理成两级缓存数据
        getGameMethods: function() {
            var me = this,
                nodeCache = {},
                methodCache = {},
                data = me.getConfig("gameMethods"),
                node1,
                node2,
                node3;

            $.each(data, function() {
                node1 = this;
                node1['fullname_en'] = [node1['name_en']];
                node1['fullname_cn'] = [node1['name_cn']];
                nodeCache['' + node1['id']] = node1;
                if (node1['children']) {
                    $.each(node1['children'], function() {
                        node2 = this;
                        node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
                        node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
                        nodeCache['' + node2['id']] = node2;
                        if (node2['children']) {
                            $.each(node2['children'], function() {
                                node3 = this;
                                node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
                                node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
                                methodCache['' + node3['id']] = node3;
                            });
                        }
                    });
                }
            });
            return methodCache;
        },

        // 根据areaName获得area的wayId。
        getWayIdAndBall: function(area) {
            var me = this,
                areaName = area.getName(),
                wayId = 0,
                ball = 0;
                //龙0，虎1，和2
                //龙单3,虎单4，龙双5，虎双6
                //龙红7,虎红8，龙黑9，虎黑10
                switch (areaName) {
                    case 'long-dan':
                        methodName = 'longhudan';
                        ball = 3;
                        break;
                    case 'long-shuang':
                        methodName = 'longhushuang';
                        ball = 5;
                        break;
                    case 'long':
                        methodName = 'longhudaxiao';
                        ball = 0;
                        break;
                    case 'long-hong':
                        methodName = 'longhuhonghei';
                        ball = 7;
                        break;
                    case 'long-hei':
                        methodName = 'longhuhonghei';
                        ball = 9;
                        break;
                    case 'hu-dan':
                        methodName = 'longhudan';
                        ball = 4;
                        break;
                    case 'hu-shuang':
                        methodName = 'longhushuang';
                        ball = 6;
                        break;
                    case 'hu':
                        methodName = 'longhudaxiao';
                        ball = 1;
                        break;
                    case 'hu-hong':
                        methodName = 'longhuhonghei';
                        ball = 8;
                        break;
                    case 'hu-hei':
                        methodName = 'longhuhonghei';
                        ball = 10
                        break;
                    case 'he':
                        methodName = 'longhuhe';
                        ball = 2;
                        break;
                };

                $.each(me._methods,function(){
                    var method = this;
                        if(method.name_en==methodName){
                            wayId = method.id;
                        }
                });
            return {
                wayId: wayId,
                ball: ball
            }
        }
    };




    var Main = host.Class(pros, TableGame);
    Main.defConfig = defConfig;
    TableGame.Lhd = Main;

})(bomao, bomao.TableGame);



(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:'ssc',
		jsNamespace:'' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games.SSC.Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};

	host.Games[name] = Main;
	
})(bomao, "SSC", bomao.Game);











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




(function(host, name, message, undefined){
	var defConfig = {
		
	},
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games.SSC[name] = Main;
	
})(bomao, "Message", bomao.GameMessage);













(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:name,
		jsNameSpace:'bomao.Games.L115.' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games[name].Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "L115", bomao.Game);











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
				filtration: /[；|;]+|[\n\r]+|[,|，]+/g,
				//验证是否纯数字
				checkNum: /^\d{2}$/,
				//单式玩法提示
				normalTips: ['说明：',
					'1、支持常见的各种单式格式，间隔符如： 换行符 回车 逗号 分号等, 号码之间则使用空格隔开',
					'2、上传文件后缀必须是.txt格式,最大支持10万注，并支持拖拽文件到文本框进行上传',
					'3、文件较大时会导致上传时间较长，请耐心等待！',
					'',
					'格式范例：01 02 03|03 04 05|07 08 11'
				].join('\n')

			},
			gameCaseName = 'L115',
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

			Games.getCurrentGameOrder().addEvent('beforeAdd', function(e, orderData) {
				var that = this,
					data = me.tData,
					html = '';

				if (orderData['type'] == me.defConfig.name) {

					//使用去重后正确号码进行投注
					if (me.isFirstAdd) {
						if (!me['ranNumTag']) {
							orderData['lotterys'] = [];
							me.isFirstAdd = null;
							//重新输出去重后号码
							me.updateData();
							Games.getCurrentGameOrder().add(Games.getCurrentGameStatistics().getResultData());
						}
					} else {
						//如果存在重复和错误号进行提示
						if (me.errorData.join('') != '' || me.sameData.join('') != '') {
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
				temp,
				last = [],
				result = [];

			data = $.trim(data);
			data = data.replace(cfg.filtration, '|');
			data = data.replace(/\s+/g, ' ');
			data = $.trim(data);

			result = data.split('|');

			$.each(result, function(i){
				temp = $.trim(this);
				if(temp != ''){
					last.push(temp.split(' '));
				}
			});
			//console.log(last);
			return last;
		},
		//检测结果重复
		checkResult: function(data, array) {
			//检查重复
			for (var i = array.length - 1; i >= 0; i--) {
				if (array[i].join('') == data.join('')) {
					return false;
				}
			};
			return true;
		},
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum) {
			var me = this,
				isPass = true;
			$.each(lotteryNum, function() {
				if (!me.defConfig.checkNum.test(this)) {
					isPass = false;
					return false;
				}
			});
			return isPass;
		},
		//正则过滤输入框HTML
		//提取正确的投注号码
		filterLotters: function(data) {
			var me = this,
				result = '';

			result = data.replace(/<br>+|&nbsp;+|\s+/gi, ' ');
			result = result.replace(/<(?:"[^"]*"|'[^']*'|[^>'"]*)+>/g, ',');
			result = result.replace(me.defConfig.checkFont, '') + ',';
			result = result.replace(/[,;，；:：|]+/gi, ',');

			return result;
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete
		checkBallIsComplete: function(data) {
			var me = this,
				i = 0,
				result = [];

			me.aData = [];
			me.vData = [];
			me.sameData = [];
			me.errorData = [];
			me.tData = [];

			//按规则进行拆分结果
			result = me.iterator(data);

			//判断结果
			for (; i < result.length; i++) {
				//判断单注合理
				if (me.checkSingleNum(result[i])) {
					if (me.checkResult(result[i], me.tData)) {
						//正确结果[已去重]
						me.tData.push(result[i]);
					} else {
						if (me.checkResult(result[i], me.sameData)) {
							//重复结果
							me.sameData.push(result[i]);
						}
					}
					//正确结果[不去重]
					me.vData.push(result[i]);
				} else {
					if (me.checkResult(result[i], me.errorData)) {
						//错误结果[已去重]
						me.errorData.push(result[i]);
					}
				}
				//所有结果[已去重]
				if (me.checkResult(result[i], me.aData)) {
					me.aData.push(result[i]);
				}
			}
			//校验
			if (me.tData.length > 0) {
				me.isBallsComplete = true;
				if (me.isFirstAdd) {
					return me.vData;
				} else {
					return me.tData;
				}

			} else {
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
				str[i] = me.tData[i].join(' ');
			}
			str = $.trim(str.join('|'));
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
				str[i] = me.tData[i].join(' ');
			}
			str = $.trim(str.join('|'));
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

			alert(me.errorData.join());
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
				if($.trim(sameData[i].join('')) != ''){
					saveSameData.push(sameData[i].join(' '));
				}
			};
			sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join('|') + '</textarea>';

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
				if($.trim(errorData[i].join('')) != ''){
					saveError.push(errorData[i].join(' '));
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
						saveSameData.push(sameData[i].join(' '));
					}
				};
				sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join('|') + '</textarea>';
			}
			//错误号码
			if(errorData.join('') != ''){
				for (var i = 0; i < errorData.length; i++) {
					if($.trim(errorData[i].join(''))){
						saveError.push(errorData[i].join(' '));
					}
				};
				errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveError.join('|') + '</textarea>';
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
			return this.makePostParameter(original);
		},
		//生成后端参数格式
		makePostParameter: function(original) {
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result = result.concat(original[i].join(' '));
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




(function(host, name, message, undefined){
	var defConfig = {
		
	},
	gameCaseName = 'L115',
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[gameCaseName][name] = Main;
	
})(bomao, "Message", bomao.GameMessage);













(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:'3d',
		jsNamespace:'' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games.D3.Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "D3", bomao.Game);













(function(host, name, message, undefined){
	var defConfig = {
		
	},
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games.D3[name] = Main;
	
})(bomao, "Message", bomao.GameMessage);













(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:'p35',
		jsNamespace:'' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games.P35.Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "P35", bomao.Game);











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
			gameCaseName = 'P35',
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




(function(host, name, message, undefined){
	var defConfig = {
		
	},
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games.P35[name] = Main;
	
})(bomao, "Message", bomao.GameMessage);













(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:'k3',
		jsNamespace:'' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games.K3.Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "K3", bomao.Game);











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
				filtration: /[\s]|[,]|[;]|[<br>]|[，]|[；]/i,
				//验证是否纯数字
				checkNum: /^[0-9]*$/,
				//单式玩法提示
				normalTips: '<p style="color:#999;font-size:12px;line-height:170%;">' + ['说明：',
					'1、每一注号码之间的间隔符支持 回车  空格[ ]    逗号[,]   分号[;]',
					'2、文件格式必须是.txt格式,大小不超过200KB',
					'3、文件较大时会导致上传时间较长，请耐心等待！',
					'4、将文件拖入文本框即可快速实现文件上传功能',
					'5、导入文本内容后将覆盖文本框中现有的内容。'
				].join('<br>') + '</p>'

			},
			gameCaseName = 'K3',
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
			//初级触发
			me.firstfocus = true;
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
		initFrame:function(){
			var me = this;
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
			var me = this;
			me.replaceText(me.getNormalTips.call(me));
			me.firstfocus = true;
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
			var me = this,iframeBody = $(me.doc.body);
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
							me.firstfocus = false;
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
			var me = this;
			return me.doc ? $(me.doc.body).html() : '';
		},
		//返回结果text
		getText: function(){
			var me = this;
			return me.doc ? $(me.doc.body).text() : '';
		},
		//修改HTML
		//返回结果HTML
		replaceText: function(text){
			var me = this;
			if(me.doc && text){
				$(me.doc.body).html(text);
			}
		},
		//绑定IFRAME按钮PRESS
		bindPress: function(){
			var me = this,
				uploadButton = me.container.find(me.defConfig.uploadButton),
				agentValue = window.navigator.userAgent.toLowerCase();
			//绑定按钮事件
			$(me.doc).bind('input',function(){
				me.updateData();
			})
			//iE不支持INPUT事件
			//而且IE propertychange事件不能绑定该DOM类型
			if(agentValue.indexOf('msie')>0){
				$(me.doc.body).bind('keyup',function(){
					me.updateData();
				})
				$(me.doc.body).bind('blur',function(){
					me.updateData();
				})
			}
			$(me.doc).bind('focus',function(){
				if(me.firstfocus){
					me.replaceText(' ');
					me.firstfocus = false;
				}	
			})
			$(me.doc).bind('blur',function(){
				var content = me.getText();
				if($.trim(content) == ''){
					me.showNormalTips();
				}
			})
			$(me.doc.body).bind('focus',function(){
				if(me.firstfocus){
					me.replaceText(' ');
					me.firstfocus = false;
				}	
			})
			$(me.doc.body).bind('blur',function(){
				var content = me.getText();
				if($.trim(content) == ''){
					me.showNormalTips();
				}
			})
			//绑定用户上传按钮
			uploadButton.bind('change', function(){
				var form = $(this).parent();
				me.checkFile(this, form);
			})
		},
		//用拆分符号拆分成单注
		iterator: function(data) {
			var me= this,
				cfg = me.defConfig,
				result = [],
				breakNum = 0;
			
			for (var i = 0; i < data.length; i++) {
				if(cfg.filtration.test(data.charAt(i))){
					result.push(data.substr(breakNum, i - breakNum));
					breakNum = i+1;
				}
			}
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

			return me.defConfig.checkNum.test(lotteryNum) && lotteryNum.length == me.balls.length;
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete 
		checkBallIsComplete:function(data){
			var me = this,
				i = 0,
				result = [];

				me.aData = [];
				me.vData = [];
				me.sameData = [];
				me.errorData = [];
				me.tData = [];
			
			//按规则进行拆分结果
			result = me.iterator(me.filterLotters(data)) || [];
			
			//判断结果
			for(;i<result.length;i++){
				//判断单注合理
				if(me.checkSingleNum(result[i])){
					if(me.checkResult(result[i], me.tData)){
						//正确结果[已去重]
						me.tData.push(result[i].split(''));
					}else{
						if(me.checkResult(result[i], me.sameData)){
							//重复结果
							me.sameData.push(result[i].split(''));
						}
					}
					//正确结果[不去重]
					me.vData.push(result[i].split(''));
				}else{
					if(me.checkResult(result[i], me.errorData)){
						//错误结果[已去重]
						me.errorData.push(result[i].split(''));
					}
				}
				//所有结果[已去重]
				if(me.checkResult(result[i], me.aData)){
					me.aData.push(result[i].split(''));
				}
			}
			//校验
			if(me.tData.length > 0){
				me.isBallsComplete = true;
				if(me.isFirstAdd){
					return me.vData;
				}else{
					return me.tData;	
				}
				
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
			var me= this, result = me.vData.length > 0 ? '' : ' ';
			if(me.firstfocus){
				return;
			}
			for (var i = 0; i < me.vData.length; i++) {
				result += me.vData[i].join('') + '&nbsp;';
			};
			me.errorDataTips();
			if($.trim(result) == ''){
				me.showNormalTips();
				return;
			}
			me.replaceText(result);
			me.checkBallIsComplete(result);
		},
		//排除重复项
		removeOrderSame: function(){
			var me= this, result = me.aData.length > 0 ? '' : ' ';
			if(me.firstfocus){
				return;
			}
			for (var i = 0; i < me.aData.length; i++) {
				result += me.aData[i].join('') + '&nbsp;';
			}
			me.sameDataTips();
			me.replaceText(result);
			me.checkBallIsComplete(result);
		},
		//清空选区
		removeOrderAll: function(){
			var me=this;
			if(me.firstfocus){
				return;
			}
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
				alert("对不起，导入数据格式必须是.txt格式文件哦，请您调整格式后重新上传，谢谢 ！");            
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
				me.firstfocus = false;
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
			sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><p class="pop-text" style="display:block">' + saveSameData.join(', ') + '</p>';

			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
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
			errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><p class="pop-text" style="display:block">' + saveError.join(', ') + '</p>';
			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
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
				sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><p class="pop-text" style="display:block">' + saveSameData.join(', ') + '</p>';
			}
			//错误号码
			if(errorData.join('') != ''){
				for (var i = 0; i < errorData.length; i++) {
					if($.trim(errorData[i].join(''))){
						saveError.push(errorData[i].join(''));
					}
				};
				errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><p class="pop-text" style="display:block">' + saveError.join(', ') + '</p>';
			}

			msg.show({
				mask: true,
				content : ['<div class="bd text-center">',
								'<div class="pop-waring">',
									'<i class="ico-waring <#=icon-class#>"></i>',
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
			//console.log(original);
			for (; i < len; i++) {
				result = result.concat(original[i].join(''));
			}
			return result.join('|');
		},
		//生成后端参数格式
		makePostParameter: function(data, order){
			var me = this,
				result = [],
				data = order['lotterys'],
				i = 0;
			for (; i < data.length; i++) {
				result = result.concat(data[i].join(''));
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
					html_all.push('<form id="form1" name="form1" class="balls-import-form" enctype="multipart/form-data" method="post" action="'+ iframeSrc +'" target="check_file_frame" style="position:relative;padding-bottom:10px;">');
					html_all.push('<input name="betNumber" type="file" id="file" size="40" hidefocus="true" value="导入" style="outline:none;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);filter:alpha(opacity=0);opacity: 0;position:absolute;top:0px; left:0px; width:115px; height:30px;z-index:1;background:#000;cursor: pointer;" />');
					html_all.push('<input name="_token" type="hidden" value="'+ token +'" />');
					html_all.push('<input type="button" class="btn balls-import-input" style="cursor: pointer;" value="导入注单" onclick=document.getElementById("form1").file.click()>&nbsp;&nbsp;&nbsp;&nbsp;<a style="display:none;" class="balls-example-danshi-tip" href="#">查看标准格式样本</a>');
					html_all.push('<input type="reset" style="outline:none;-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=0);filter:alpha(opacity=0);opacity: 0;width:0px; height:0px;z-index:1;background:#000" />');
					html_all.push('<iframe src="'+ iframeSrc +'" name="check_file_frame" style="display:none;"></iframe>');
					html_all.push('</form>');
					html_all.push('<div class="panel-select" ><iframe style="width:100%;height:100%;border:0 none;background-color:#F9F9F9;" class="content-text-balls"></iframe></div>');
					html_all.push('<div class="panel-btn">');
					html_all.push('<a class="btn remove-error" href="javascript:void(0);">删除错误项</a>');
					html_all.push('<a class="btn remove-same" href="javascript:void(0);">删除重复项</a>');
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




(function(host, name, message, undefined){
	var defConfig = {
		
	},
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games.K3[name] = Main;
	
})(bomao, "Message", bomao.GameMessage);













(function(host, name, Game, undefined){
	var defConfig = {
		//游戏名称
		name:name,
		jsNameSpace:'bomao.Games.PK10.' 
	},
	instance,
	Games = host.Games;
	
	var pros = {
		init:function(){
			var me = this;
			//初始化事件放在子类中执行，以确保dom元素加载完毕
			me.eventProxy();
		},
		getGameConfig:function(){
			return Games[name].Config;
		}
	};
	
	var Main = host.Class(pros, Game);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[name] = Main;
	
})(bomao, "PK10", bomao.Game);











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
				filtration: /[；|;]+|[\n\r]+|[,|，]+/g,
				//验证是否纯数字
				checkNum: /^\d{2}$/,
				//单式玩法提示
				normalTips: ['说明：',
					'1、支持常见的各种单式格式，间隔符如： 换行符 回车 逗号 分号等, 号码之间则使用空格隔开',
					'2、上传文件后缀必须是.txt格式,最大支持10万注，并支持拖拽文件到文本框进行上传',
					'3、文件较大时会导致上传时间较长，请耐心等待！',
					'',
					'格式范例1：01 02 03|03 04 05|07 08 10',
					'格式范例2：01 02 03,03 04 05,07 08 10'

				].join('\n')

			},
			gameCaseName = 'PK10',
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

			Games.getCurrentGameOrder().addEvent('beforeAdd', function(e, orderData) {
				var that = this,
					data = me.tData,
					html = '';

				if (orderData['type'] == me.defConfig.name) {

					//使用去重后正确号码进行投注
					if (me.isFirstAdd) {
						if (!me['ranNumTag']) {
							orderData['lotterys'] = [];
							me.isFirstAdd = null;
							//重新输出去重后号码
							me.updateData();
							Games.getCurrentGameOrder().add(Games.getCurrentGameStatistics().getResultData());
						}
					} else {
						//如果存在重复和错误号进行提示
						if (me.errorData.join('') != '' || me.sameData.join('') != '') {
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
				temp,
				last = [],
				result = [];

			data = $.trim(data);
			data = data.replace(cfg.filtration, '|');
			data = data.replace(/\s+/g, ' ');
			data = $.trim(data);

			result = data.split('|');

			$.each(result, function(i){
				temp = $.trim(this);
				if(temp != ''){
					last.push(temp.split(' '));
				}
			});
			//console.log(last);
			return last;
		},
		//检测结果重复
		checkResult: function(data, array) {
			//检查重复
			for (var i = array.length - 1; i >= 0; i--) {
				if (array[i].join('') == data.join('')) {
					return false;
				}
			};
			return true;
		},
		//检测输入号码中有无重复值
		checkRepeat:function (arr) {
			var hash = {};
			for(var i in arr) {
				if(hash[arr[i]])
					return true;
				hash[arr[i]] = true;
			}
			return false;
		},
		//号码减一
		minus1:function (string) {	  //'01 02 03'
			var _a = $.trim(string).split(' '),
				_b = '';
			for(var i=0;i<_a.length;i++){
				_b += Number(_a[i])-1;
			}
			return _b;
		},
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum) {
			var me = this,
				isPass = true;
			$.each(lotteryNum, function() {
				if (!me.defConfig.checkNum.test(this)) {
					isPass = false;
					return false;
				}
			});
			return isPass;
		},
		//正则过滤输入框HTML
		//提取正确的投注号码
		filterLotters: function(data) {
			var me = this,
				result = '';

			result = data.replace(/<br>+|&nbsp;+|\s+/gi, ' ');
			result = result.replace(/<(?:"[^"]*"|'[^']*'|[^>'"]*)+>/g, ',');
			result = result.replace(me.defConfig.checkFont, '') + ',';
			result = result.replace(/[,;，；:：|]+/gi, ',');

			return result;
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete
		checkBallIsComplete: function(data) {
			var me = this,
				i = 0,
				result = [];

			me.aData = [];
			me.vData = [];
			me.sameData = [];
			me.errorData = [];
			me.tData = [];

			//按规则进行拆分结果
			result = me.iterator(data);

			//判断结果
			for (; i < result.length; i++) {
				//判断单注合理
				if (me.checkSingleNum(result[i])) {
					if (me.checkResult(result[i], me.tData)) {
						//正确结果[已去重]
						me.tData.push(result[i]);
					} else {
						if (me.checkResult(result[i], me.sameData)) {
							//重复结果
							me.sameData.push(result[i]);
						}
					}
					//正确结果[不去重]
					me.vData.push(result[i]);
				} else {
					if (me.checkResult(result[i], me.errorData)) {
						//错误结果[已去重]
						me.errorData.push(result[i]);
					}
				}
				//所有结果[已去重]
				if (me.checkResult(result[i], me.aData)) {
					me.aData.push(result[i]);
				}
			}
			//校验
			if (me.tData.length > 0) {
				me.isBallsComplete = true;
				if (me.isFirstAdd) {
					return me.vData;
				} else {
					return me.tData;
				}

			} else {
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
				str[i] = me.tData[i].join(' ');
			}
			str = $.trim(str.join('|'));
			me.errorDataTips();
			me.replaceText(str);
			me.errorData = [];
			me.sameData = [];
			me.updateData();
			if(str == ''){
				me.showNormalTips();
			}
		},
		//排除重复项
		removeOrderSame: function(){
			var me  = this,str = [],i = 0,len = me.tData.length;
			for(i = 0; i < len; i++){
				str[i] = me.tData[i].join(' ');
			}
			str = $.trim(str.join('|'));
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

			alert(me.errorData.join());
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
				if($.trim(sameData[i].join('')) != ''){
					saveSameData.push(sameData[i].join(' '));
				}
			};
			sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join('|') + '</textarea>';

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
				if($.trim(errorData[i].join('')) != ''){
					saveError.push(errorData[i].join(' '));
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
						saveSameData.push(sameData[i].join(' '));
					}
				};
				sameDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码重复，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveSameData.join('|') + '</textarea>';
			}
			//错误号码
			if(errorData.join('') != ''){
				for (var i = 0; i < errorData.length; i++) {
					if($.trim(errorData[i].join(''))){
						saveError.push(errorData[i].join(' '));
					}
				};
				errorDataHtmlText = '<h4 class="pop-text" style="display:block;font-weight:bold">以下号码错误，已进行自动过滤</h4><textarea class="" style="display:block;height:100px;width:400px;padding:5px;">' + saveError.join('|') + '</textarea>';
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
			return this.makePostParameter(original);
		},
		//生成后端参数格式
		makePostParameter: function(original) {
			var me = this,
				result = [],
				len = original.length,
				i = 0;
			for (; i < len; i++) {
				result = result.concat(original[i].join(' '));
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




(function(host, name, message, undefined){
	var defConfig = {
		
	},
	gameCaseName = 'PK10',
	Games = host.Games,
	instance;

	var pros = {
		init: function(cfg){
			var me = this;
			Games.setCurrentGameMessage(me);
		}
	};
	
	var Main = host.Class(pros, message);
		Main.defConfig = defConfig;
		//游戏控制单例
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host.Games[gameCaseName][name] = Main;
	
})(bomao, "Message", bomao.GameMessage);












//竞彩
(function(host, name, Event, undefined){
	var defConfig = {

	};
	var formatParam = function(param){
		var arr = $.trim(param).split('&'),i = 0,len = arr.length,
			paramArr = [],
			result = {};
		for(;i < len;i++){
			paramArr = arr[i].split('=');
			if(paramArr.length > 0){
				if(paramArr.length == 2){
					result[paramArr[0]] = paramArr[1];
				}else{
					result[paramArr[0]] = '';
				}
			}
		}
		return result;
	};




	var pros = {
		init:function(cfg){
			var me = this;

			me._option = {};
			me._orders = [];
			me._tabType = 0;
			me._selects = {};

			//是否为单关计算
			me._isapplydanguan = false;
			//当前比赛玩法类型
			me._matchTypeValue = '';
		},
		getMatchTypeValue:function(){
			return this._matchTypeValue;
		},
		setMatchTypeValue:function(value){
			this._matchTypeValue = value;
		},
		getIsApplyDanguan:function(){
			return this._isapplydanguan;
		},
		setIsApplyDanguan:function(isdanguan){
			this._isapplydanguan = isdanguan;
		},
		getSelects:function(){
			return this._selects;
		},
		setSelects:function(selects){
			var me = this,
				i = 0,
				len = selects.length;
			me._selects = {};

			for(i = 0; i < len; i++){
				me._selects[selects[i]] = 1;
			}
			me.update();
		},
		getTabType:function(){
			return this._tabType;
		},
		setTabType:function(v){
			var me = this;
			me._tabType = v;
			me.setSelects([]);
			me.fireEvent('after_setTabType', v);
		},
		setMethodMixMaxCfg:function(config){
			var me = this;
			me._MethodMixMaxCfg = config;
		},
		getMethodMixMaxCfg:function(key){
			var me = this;
			if(!!key){
				return me._MethodMixMaxCfg['key'];
			}
			return me._MethodMixMaxCfg;
		},
		initOption:function(scfg){
			var me = this,
				cfgList = scfg,
				result = [],
				i = 0,
				len = cfgList.length,
				it,
				p,
				temp,
				numsHash = {
					'2_1':1,
					'3_1':1,
					'4_1':1,
					'5_1':1,
					'6_1':1,
					'7_1':1,
					'8_1':1
				};
			for(i = 0; i < len; i++){
				it = cfgList[i];
				temp = {};
				temp['x'] = Number(it[0].split('_')[0]);
				temp['y'] = Number(it[0].split('_')[1]);
				temp['code'] = it[0];
				temp['name'] = it[1];
				temp['numcfg'] = {};
				if(numsHash[it[0]]){
					temp['numcfg'][it[0]] = numsHash[it[0]];
				}else{
					for(p in it[2]){
						if(it[2].hasOwnProperty(p)){
							temp['numcfg'][p] = it[2][p];
						}
					}
				}

				result[i] = temp;
			}

			me._option = result;
		},
		getOption:function(){
			return this._option;
		},
		getTypeOption:function(x, isGroup){
			var me = this,
				list = me.getOption(),
				i = 0,
				len = list.length,
				result = [],
				j = 0,
				len2,
				miny = me.getMinGroupY(),
				danNum = me.getDanNum();

			//console.log(danNum);
			if(!isGroup){
				for(i = 0; i < len; i++){
					if(list[i]['y'] == 1 && list[i]['x'] <= x && list[i]['x'] > danNum){
						result.push(list[i]);
					}
				}
			}else{
				for(i = 0; i < len; i++){
					if(list[i]['x'] <= x && list[i]['y'] >= miny && list[i]['x'] > danNum){
						result.push(list[i]);
					}
				}
			}
			result = me.filterForDanguan(result);
			result = me.filterForMethodMaxCfg(result);
			return result;
		},
		//各玩法限制最高串
		filterForMethodMaxCfg:function(types){
			var me = this,
				orders = me.getOrders(),
				i = 0,
				len = orders.length,
				j = 0,
				len2,
				maxCfg = me.getMethodMixMaxCfg(),
				arr = [],
				max = 0,
				result = [];

			for(i = 0; i < len; i++){
				for(j = 0, len2 = orders[i]['bets'].length; j < len2; j++){
					arr.push(maxCfg[orders[i]['bets'][j]['type']]);
				}
			}
			if(arr.length > 0){
				arr.sort();
				max = arr[0];
			}

			for(i = 0, len = types.length; i < len; i++){
				if(types[i]['x'] <= max){
					result.push(types[i]);
				}
			}
			//console.log(max, types);

			return result;
		},
		//检测是否应用单关选项
		filterForDanguan:function(types){
			var me = this,
				orders = me.getOrders(),
				isdanguan = true,
				isDanguanMethod = me.getMatchTypeValue() == 'single' ? true : false,
				arr = [],
				i = 0,
				len = orders.length,
				j = 0,
				len2;

			for(i = 0; i < len; i++){
				for(j = 0, len2 = orders[i]['bets'].length; j < len2; j++){
					if(!orders[i]['bets'][j]['issingle']){
						isdanguan = false;
						break;
					}
				}
			}
			
			if((isDanguanMethod && isdanguan) || (me.getOrdersLen() == 1 && isdanguan)){
				for(i = 0, len = types.length; i < len; i++){
					if(types[i]['x'] == 1){
						arr.push(types[i]);
					}
				}
				me.setIsApplyDanguan(true);
			}else{
				for(i = 0, len = types.length; i < len; i++){
					if(types[i]['x'] > 1){
						arr.push(types[i]);
					}
				}
				me.setIsApplyDanguan(false);
			}
			if(!isDanguanMethod && me.getOrdersLen() > 1){
				me.setIsApplyDanguan(false);
			}
			
			//console.log(me.getIsApplyDanguan(), arr);
			return arr;
		},
		getMinGroupY:function(){
			var me = this,
				options = me.getOption(),
				i = 0,
				len  = options.length,
				arr = [];
			for(i = 0;i < len;i ++){
				if(options[i]['y'] > 1){
					arr.push(options[i]['x']);
				}
			}
			arr.sort();
			return arr[0];
		},
		getOrders:function(){
			return this._orders;
		},
		setOrders:function(orders){
			this._orders = orders;
		},
		getOrdersLen:function(){
			var me = this,
				orders = me._orders,
				i = 0,
				len = orders.length,
				hash = {},
				num = 0;
			for(i = 0; i < len; i++){
				if(!hash[orders[i]['matchid']]){
					num += 1;
					hash[orders[i]['matchid']] = true;
				}
			}
			return num;
		},
		checkOrderMatchLen:function(matchid, maxlen){
			var me = this,
				orders = me._orders,
				i = 0,
				len = orders.length,
				hash = {},
				num = 0,
				maxlen = maxlen || 15;
			for(i = 0; i < len; i++){
				if(!hash[orders[i]['matchid']]){
					num += 1;
					hash[orders[i]['matchid']] = true;
				}
			}
			if(!hash[matchid]){
				return num < maxlen;
			}
			return true;
		},
		formatOrderParams:function(params){
			params['index'] = Number(params['index']);
			params['odds'] = Number(params['odds']);
			params['handicap'] = Number(params['handicap']);
			params['issingle'] = (params['issingle'] == '') ? false : true;
			delete params['action'];
			return params;
		},
		addOrder:function(params){
			var me = this,
				matchid = params['matchid'],
				orders = me.getOrders(),
				i,
				it,
				len,
				j = 0,
				len2,
				k = 0,
				objOrders = me.getOrdersByMatchid(matchid),
				order,
				index;

			params = me.formatOrderParams(params);

			if(objOrders.length < 1){
				order = {'matchid':matchid, 'bets':[params], 'dan':0};
				orders.push(order);
				orders.sort(function(a, b){
					return Number(a['matchid']) - Number(b['matchid']);
				});
			}else{

				for(i = 0; i < objOrders.length; i++){
					order = objOrders[i];
					for(j = 0, len2 = order['bets'].length; j < len2; j++ ){
						it = order['bets'][j];
						if(it['type'] == params['type'] && it['value'] == params['value']){
							me.delBet(matchid, it['type'], it['value']);
							return;
						}
					}
				}
				for(i = 0; i < objOrders.length; i++){
					//不同玩法,做为单独场次比赛计算
					if(order['bets'][0]['type'] != params['type']){
						for(k = 0; k < order['bets'].length; k++){
							order['bets'][k]['issplit'] = 1;
						}
						params['issplit'] = 1;
						order = {'matchid':matchid, 'bets':[params], 'dan':0};
						orders.push(order);
						orders.sort(function(a, b){
							return Number(a['matchid']) - Number(b['matchid']);
						});
						break;
					}else{
						order['bets'].push(params);
						order['bets'].sort(function(a, b){
							return Number(a['index']) - Number(b['index']);
						});
						break;
					}


				}



			}

			me.fireEvent('after_addOrder');
			me.update();

		},
		//复制并整理order数据列表
		copyRebuildOrders:function(orders){
			var me = this,
				i = 0,
				j = 0,
				len = orders.length,
				result = [],
				row,
				bets,
				bet,
				newrow,
				newbets,
				hash = {};
			for(i = 0; i < len; i++){
				row = orders[i];
				bets = orders[i]['bets'];
				newbets = [];
				for(j = 0; j < bets.length; j++){
					bet = {
						'action': bets[j]['action'],
						'index': bets[j]['index'],
						'issplit': bets[j]['issplit'],
						'matchid': bets[j]['matchid'],
						'name': bets[j]['name'],
						'odds': bets[j]['odds'],
						'team1': bets[j]['team1'],
						'team2': bets[j]['team2'],
						'time': bets[j]['time'],
						'type': bets[j]['type'],
						'value': bets[j]['value']
					};
					newbets.push(bet);
				}
				if(hash[orders[i]['matchid']]){
					newrow = hash[orders[i]['matchid']];
					newrow['bets'] = newrow['bets'].concat(newbets);
					newrow['bets'].sort(function(a, b){
						return Number(a['index']) - Number(b['index']);
					});
					//console.log(newrow);
				}else{
					newrow = {
						'matchid':row['matchid'],
						'dan':row['dan'],
						'bets':newbets
					};
					newrow['bets'].sort(function(a, b){
						return Number(a['index']) - Number(b['index']);
					});
					hash[orders[i]['matchid']] = newrow;
					result.push(newrow);
				}

				
			}

			return result;
		},
		//合并两个相同比赛的投注内容
		copyMergeBets:function(betsa, betsb){
			var me = this;

		},
		//设置/取消胆
		setDan:function(matchid, v){
			var me = this,
				orders = me.getOrdersByMatchid(matchid),
				i = 0,
				len = orders.length;
			for(i = 0;i < len; i++){
				orders[i]['dan'] = v;
			}

			me.update();
		},
		getDanNum:function(){
			var me = this,
				orders = me.getOrders(),
				i = 0,
				len = orders.length,
				num = 0,
				hash = {};

			for(i = 0; i < len; i++){
				if(orders[i]['dan'] == 1 && !hash[orders[i]['matchid']]){
					num += 1;
					hash[orders[i]['matchid']] = true;
				}
			}
			//console.log(num);
			return num;
		},
		getOrdersByMatchid:function(matchid){
			var me  = this,
				orders = me.getOrders(),
				i = 0,
				len = orders.length,
				result = [];
			for(i = 0; i < len; i++){
				if(orders[i]['matchid'] == matchid){
					result.push(orders[i]);
				}
			}
			return result;
		},
		//删除整条比赛投注
		delOrder:function(matchid){
			var me = this,
				orders = me.getOrders(),
				i = 0,
				len = orders.length,
				arr = [];

			for(i = 0; i < len; i++){
				if(orders[i]['matchid'] != matchid){
					arr.push(orders[i]);
				}
			}
			me.setOrders(arr);

			me.fireEvent('after_delOrder', matchid);
			me.update();
		},
		delAll:function(){
			var me = this;

			me.setOrders([]);

			me.fireEvent('after_delAll');
			me.update();
		},
		getMultiple:function(){
			return 1;
		},
		//取消某个投注内容
		delBet:function(matchid, type, value){
			var me = this,
				orders = me.getOrders(),
				objOrders = me.getOrdersByMatchid(matchid),
				bets,
				i = 0,
				len,
				j,
				bet;

			//console.log(objOrders);
			for(i = 0; i < objOrders.length; i++){
				bets = objOrders[i]['bets'];
				for(j = 0; j < bets.length; j++){
					if(bets[j]['type'] == type && bets[j]['value'] == value){
						bet = bets.splice(j, 1);
						break;
					}
				}
			}
			for(i = 0, len = orders.length; i < len; i++){
				if(orders[i]['matchid'] == matchid){
					if(orders[i]['bets'].length == 0){
						orders.splice(i, 1);
						break;
					}
				}
			}

			me.fireEvent('after_delBet', matchid, type, value);
			me.update();
		},
		update:function(){
			var me = this;
			me.fireEvent('after_update');
			me.updateStatics();
		},
		updateStatics:function(){
			var me = this;
			me.fireEvent('after_update_statics');
		},
		setMaxPrize:function(prize){
			this._maxPrize = prize;
		},
		getMaxPrize:function(){
			return this._maxPrize;
		},
		//最大奖金计算顺序 让球胜平负 -> 胜平负 -> 其他
		countMaxPrize:function(num){
			var me = this,
				orders,
				win,lose,tie,
				result = [];
			if(num == 0){
				me.setMaxPrize(0);
				me.fireEvent('after_countMaxPrize', me.getMaxPrize());
				return;
			}
			
			me.setMaxPrize(me.getMaxPrize());

			me.fireEvent('after_countMaxPrize', me.getMaxPrize());

		},
		//让球胜
		getMaxPrize_handicapWin:function(orders){
			var me = this,
				i,len,
				p,
				p2,
				handicap = 0,
				teamMustScoreA = 0,
				teamMustScoreB = 0,
				//{'3':胜,'1':平,'0':负}
				winResult = {},
				result = [];
			for(p in orders){
				if(orders.hasOwnProperty(p)){
					//让球数
					handicap = me.getHandicapFromOrder(orders[p]);
					if(handicap < 0){
						teamMustScoreA = Math.abs(handicap) + 1;
						teamMustScoreB = 0;
						//胜平负(赢)
						winResult = {'3':true};
					}else{
						teamMustScoreA = 0;
						teamMustScoreB = 0;
						//胜平负(赢,平)
						winResult = {'3':true, '1': true};
					}
					result.push(me.getMaxPrizeByResult(winResult, teamMustScoreA, teamMustScoreB, orders[p]));
				}
			}
			var min = 1,max = 1;
			for(i = 0,len = result.length; i < len; i++){
				min *= result[i]['min'];
				max *= result[i]['max'];
			}
			return {min:min, max:max};
		},
		//让球平
		getMaxPrize_handicapTie:function(orders){
			var me = this,
				p,
				p2,
				handicap = 0,
				teamMustScoreA = 0,
				teamMustScoreB = 0,
				//{'3':胜,'1':平,'0':负}
				winResult = {};
			for(p in orders){
				if(orders.hasOwnProperty(p)){
					//让球数
					handicap = me.getHandicapFromOrder(orders[p]);
					if(handicap < 0){
						teamMustScoreA = Math.abs(handicap);
						teamMustScoreB = 0;
						//胜平负(胜)
						winResult = {'3':true};
					}else{
						teamMustScoreA = 0;
						teamMustScoreB = Math.abs(handicap);
						//胜平负(负)
						winResult = {'0':true};
					}
					return me.getMaxPrizeByResult(winResult, teamMustScoreA, teamMustScoreB, orders[p]);
				}
			}
		},
		getMaxPrize_handicapLose:function(orders){
			var me = this,
				p,
				p2,
				handicap = 0,
				teamMustScoreA = 0,
				teamMustScoreB = 0,
				//{'3':胜,'1':平,'0':负}
				winResult = {};
			for(p in orders){
				if(orders.hasOwnProperty(p)){
					//让球数
					handicap = me.getHandicapFromOrder(orders[p]);
					if(handicap < 0){
						teamMustScoreA = Math.abs(handicap) + 1;
						teamMustScoreB = 0;
						//胜平负(负)
						winResult = {'1':true,'0':true};
					}else{
						teamMustScoreA = 0;
						teamMustScoreB = Math.abs(handicap) + 1;
						//胜平负(负)
						winResult = {'0':true};
					}
					return me.getMaxPrizeByResult(winResult, teamMustScoreA, teamMustScoreB, orders[p]);
				}
			}
		},
		//由假设胜平负结果得到可选择的比赛
		getMaxPrizeByResult:function(winResult, teamMustScoreA, teamMustScoreB, order){
			var me = this,
				p,
				i,len,
				//最小总比分
				mustScore = teamMustScoreA + teamMustScoreB,
				listodds = {},
				result = {min:1, max:1};

			for(p in order){
				if(order.hasOwnProperty(p)){
					listodds[p] = [];
					switch(p){
						case 'win':
							for(i = 0,len = order[p]['bets'].length; i < len; i++){
								if(winResult[order[p]['bets'][i]['value']]){
									listodds[p].push(order[p]['bets'][i]['odds']);
								}
							}
						break;
						case 'handicapWin':
							for(i = 0,len = order[p]['bets'].length; i < len; i++){
								if(winResult[me.getOrderSpf_handicapWin(order[p]['bets'][i]['value'])]){
									listodds[p].push(order[p]['bets'][i]['odds']);
								}
							}
						break;
						case 'haFu':
							for(i = 0,len = order[p]['bets'].length; i < len; i++){
								if(winResult[me.getOrderSpf_haFu(order[p]['bets'][i]['value'])]){
									listodds[p].push(order[p]['bets'][i]['odds']);
								}
							}
						break;
						case 'correctScore':
							for(i = 0,len = order[p]['bets'].length; i < len; i++){
								if(winResult[me.getOrderSpf_correctScore(order[p]['bets'][i]['value'])]){
									//进球总数必须不小于最小总比分
									if(order[p]['bets'][i]['num'] >= mustScore){
										listodds[p].push(order[p]['bets'][i]['odds']);
									}
								}
							}
						break;
						case 'totalGoals':
							for(i = 0,len = order[p]['bets'].length; i < len; i++){
								//进球总数必须不小于最小总比分
								if(order[p]['bets'][i]['num'] >= mustScore){
									listodds[p].push(order[p]['bets'][i]['odds']);
								}
							}
						break;
						default:
						break;
					}

				}
			}


			for(p in listodds){
				if(listodds.hasOwnProperty(p)){
					listodds[p].sort();
					if(listodds[p].length > 0){
						result['min'] *= listodds[p][0];
						result['max'] *= listodds[p][listodds[p].length - 1];
					}
				}
			}

			return result;
			//console.log(maxodds);
			//console.log(winResult);
			//console.log(winResult, teamMustScoreA, teamMustScoreB, order);
		},
		//获得比赛的让球数
		getHandicapFromOrder:function(order){
			var p;
			for(p in order){
				if(order.hasOwnProperty(p)){
					return order[p]['bets'][0]['handicap'];
				}
			}
		},
		//整理orders格式
		rebuildOrders:function(){
			var me = this,
				orders = me.getOrders(),
				ordersHash = {},
				i,len,
				j,len2,
				type,value,obj;
			for(i = 0, len = orders.length; i < len; i++){
				if(!ordersHash[orders[i]['matchid']]){
					ordersHash[orders[i]['matchid']] = {};
				}
				for(j = 0, len2 = orders[i]['bets'].length; j < len2; j++){
					type = orders[i]['bets'][j]['type'];
					value = orders[i]['bets'][j]['value'];
					if(!ordersHash[orders[i]['matchid']][type]){
						ordersHash[orders[i]['matchid']][type] = {'bets':[]};
					}
					ordersHash[orders[i]['matchid']][type]['bets'].push({'odds':orders[i]['bets'][j]['odds'], 'value':value, 'type':type, 'spf':me.getOrderSpf(type, value), 'num':me.getOrderScore(type, value), 'handicap':orders[i]['bets'][j]['handicap']});
				}

			}
			return ordersHash;

		},
		//获得比赛选择内容代表的总进球数
		getOrderScore:function(type, value){
			var me = this,arr;
			if(type == 'totalGoals'){
				return Number(me.getOrderSpf(type, value));
			}
			if(type == 'correctScore'){
				arr = value.split('');
				arr[0] = Number(arr[0]);
				arr[1] = Number(arr[1]);
				return arr[0] + arr[1];
			}
		},
		//获得比赛选择内容的胜平负属性
		getOrderSpf:function(type, value){
			var me = this;
			if(me['getOrderSpf_' + type]){
				return me['getOrderSpf_' + type].call(me, value);
			}
		},
		//胜平负属性判定
		getOrderSpf_win:function(value){
			var v = value.substr(value.length - 1, 1);
			return v;
		},
		//让球胜平负属性判定
		getOrderSpf_handicapWin:function(value){
			return this.getOrderSpf_win(value);
		},
		//半全场胜平负属性判定
		getOrderSpf_haFu:function(value){
			return this.getOrderSpf_win(value);
		},
		//
		getOrderSpf_correctScore:function(value){
			var v = value.substr(value.length - 2),
				arr = v.split(''),
				result;
			arr[0] = Number(arr[0]);
			arr[1] = Number(arr[1]);
			if(arr[0] > arr[1]){
				result = '3';
			}else if(arr[0] == arr[1]){
				result = '1';
			}else{
				result = '0';
			}
			return result;
		},
		getOrderSpf_totalGoals:function(value){
			var v = this.getOrderSpf_win(value);
			return Number(v);
		},
		getCount:function(){
			var me = this,
				tabindex = me.getTabType(),
				num = me.getCountGroupPass();
			if(num > 0){
				me.countMaxPrize();
			}else{
				me.countMaxPrize(0);
			}
			//console.log(me.getOrders());
			return num;
		},
		getCountFree:function(selects){
			var me = this,
				orders = me.getOrders(),
				ordersArr = [],
				typeorders,
				roworders,
				selects = selects || me.getSelects(),
				selectsArr = me.splitSelects(selects),
				tabindex = me.getTabType(),
				types = me.getTypeOption(me.getOrdersLen(), tabindex == 0 ? false : true),
				num = 0,
				i,j,k,n,m,

				arrs = [],
				row,
				result = [],
				lastresult = [];

			for(i = 0; i < selectsArr.length; i++){
				ordersArr.push(me.splitOrders(orders, selectsArr[i]['a']));
			}
			for(j = 0; j < ordersArr.length; j++){
				typeorders = ordersArr[j];
				for(k = 0; k < typeorders.length; k++){
					roworders = typeorders[k];
					arrs = [];
					for(n = 0; n < roworders.length; n++){
						arrs[n] = [];
						for(m = 0; m < roworders[n]['bets'].length; m++){
							arrs[n].push(roworders[n]['bets'][m]);
						}
					}
					result.push(me.combination(arrs));
				}
			}
			for(i = 0; i < result.length; i++){
				row = result[i];
				for(j = 0; j < row.length; j++){

					lastresult.push(row[j]);
				}
			}

			num = lastresult.length;

			return num;
		},
		getCountFreePass:function(){
			var me = this;
			return me.getCountFree(me.getSelects());
		},
		getCountFreeForGroup:function(x, selects){
			var me = this,
				orders = me.getOrders(),
				ordersArrmix = [],
				ordersArr = [],
				typeorders,
				roworders,
				selects = selects || me.getSelects(),
				selectsArr = me.splitSelects(selects),
				tabindex = me.getTabType(),
				types = me.getTypeOption(me.getOrdersLen(), tabindex == 0 ? false : true),
				num = 0,
				i,j,k,n,m,

				arrs = [],
				row,
				key,
				keys,
				hash = {},
				issplit = false,
				issamematch = false,
				lastmatchid,
				result = [],
				noreapetArr = [];

			//console.log(orders);
			//将同一场次不同玩法进行拆分(当作另一场进行计算)
			orders = me.splitSameMathMethodOrder(orders);
			

			for(i = 0; i < selectsArr.length; i++){
				ordersArrmix.push(me.splitOrders(orders, x));
			}
			ordersArrmix = ordersArrmix.length > 0 ? ordersArrmix[0] : ordersArrmix;
			for(j = 0; j < ordersArrmix.length; j++){
				for(i = 0; i < selectsArr.length; i++){
					ordersArr.push(me.splitOrders(ordersArrmix[j], selectsArr[i]['a']));
				}
			}

			for(j = 0; j < ordersArr.length; j++){
				typeorders = ordersArr[j];
				for(k = 0; k < typeorders.length; k++){
					roworders = typeorders[k];
					arrs = [];
					for(n = 0; n < roworders.length; n++){
						arrs[n] = [];
						for(m = 0; m < roworders[n]['bets'].length; m++){
							arrs[n].push(roworders[n]['bets'][m]);
						}
					}
					result.push(me.combination(arrs));
				}
			}
			//console.log(result);
			for(i = 0; i < result.length; i++){
				row = result[i];
				for(j = 0; j < row.length; j++){
					keys = [];
					issplit = false;
					issamematch = false;
					lastmatchid = '';
					for(var k = 0; k < row[j].length; k++){
						keys.push(row[j][k]['matchid'] + '-' + row[j][k]['type']);
						issplit = row[j][k]['issplit'] == 1 ? true : issplit;
						if(k != 0 && lastmatchid == row[j][k]['matchid']){
							issamematch = true;
						}
						lastmatchid = row[j][k]['matchid'];
					}
					key = keys.join('-');
					if(issplit && hash[key]){
						//console.log(key);
					}else{
						if(!issamematch){
							noreapetArr.push(row[j]);
						}
					}
					hash[key] = true;
				}
			}

			//console.log(result.length);
			//console.log(noreapetArr.length);
			for(i = 0; i < noreapetArr.length; i++){
				//console.log(noreapetArr[i][0]['matchid']+ '-' + noreapetArr[i][0]['type'], 'x', noreapetArr[i][1]['matchid']+ '-' + noreapetArr[i][1]['type']);
			}

			num = noreapetArr.length;

			return num;
		},
		splitSameMathMethodOrder:function(orders){
			var me = this,
				i = 0,
				len = orders.length,
				j = 0,
				len2,
				bets,
				hash,
				resultHash = {},
				match,
				result = [];

			for(i = 0; i < len; i++){
				bets = orders[i]['bets'];
				len2 = bets.length - 1;
				hash = {};
				for(j = len2; j >= 0; j--){
					if(hash[bets[j]['type']]){

					}else{
						if(j != len2){
							bets[j]['issplit'] = 1;
							match = {
								'matchid':orders[i]['matchid'],
								'dan':orders[i]['dan'],
								'bets':[bets[j]]
							};
							result.push(match);
							bets.splice(j, 1);
						}else{
							hash[bets[j]['type']] = true;
						}
					}

				}
			}

			return orders.concat(result);
		},
		getCountDanguan:function(){
			var me = this,
				orders = me.getOrders(),
				selects = me.getSelects(),
				i = 0,
				len = orders.length,
				num = 0;

			if(selects['1_1']){
				for(i = 0; i < len; i++){
					num += orders[i]['bets'].length;
				}
			}else{
				num = 0;
			}

			//整理订单
			var matches = {};
            var danma_matches = {};
            var danma_count = 0;
            for (var i = 0; i < orders.length; i++) {
                var match = orders[i];
                if (match.dan) {
                    if (!danma_matches[match.matchid]) {
                        danma_matches[match.matchid] = match.matchid;
                        danma_count++;
                    }
                }
                for (var k = 0; k < match['bets'].length; k++) {
                    var bet = match['bets'][k];
                    matches[match.matchid] = matches[match.matchid] || {};
                    matches[match.matchid][bet.type] = matches[match.matchid][bet.type] || [];
                    matches[match.matchid][bet.type].push(bet);
                }
            }

            //计算每场比赛的最大赔率组合
            me.analyseBet(matches);
            //计算最大赔率和
            var max_total = 0;
            for (var v1 in matches) {
			    for (var v2 in matches[v1]) {
			        for (var v3 in matches[v1][v2]) {
			            var matchid = matches[v1][v2][v3]['matchid'];
			            if (me.match_max_resule[matchid].indexOf(matches[v1][v2][v3]['value']) != -1) {
			                max_total += matches[v1][v2][v3]['odds'];
			            }
			        }
			    }
			}

            //奖最大值*2(单价)*倍数
            me.setMaxPrize((max_total*2*me.getMultiple()).toFixed(2));
            
			return num;
		},
		getCountGroupPass:function(){
			var me = this,
				num = 0,
				selects = me.getSelects(),
				selectsArr = me.splitSelects(selects),
				tabindex = me.getTabType(),
				types = me.getTypeOption(me.getOrdersLen(), tabindex == 0 ? false : true),
				i,j,
				p,
				key,
				obj,
				lastresult = [];
        	
        	//单关计算
        	if(me.getIsApplyDanguan()){
        		return me.getCountDanguan();
        	}

                var orders = me.getOrders();

                //console.log(orders);

                // console.log(me.getOrders()); [Object { matchid="201602077013",  bets=[1],  dan=0}, Object { matchid="201602077014",  bets=[1],  dan=0}, Object { matchid="201602077014",  bets=[1],  dan=0}, Object { matchid="201602077016",  bets=[1],  dan=0}]
                //ps: orders 最好以bets合并，此处则无须进行去重处理
                var matches = {};
                var danma_matches = {};
                var danma_count = 0;
                for (var i = 0; i < orders.length; i++) {
                    var match = orders[i];
                    if (match.dan) {
                        if (!danma_matches[match.matchid]) {
                            danma_matches[match.matchid] = match.matchid;
                            danma_count++;
                        }
                    }
                    for (var k = 0; k < match['bets'].length; k++) {
                        var bet = match['bets'][k];
                        matches[match.matchid] = matches[match.matchid] || {};
                        matches[match.matchid][bet.type] = matches[match.matchid][bet.type] || [];
                        matches[match.matchid][bet.type].push(bet);
                    }
                }
                var arr_all_matchids = [];
                var choose_matchids = [];
                var danma_matchids = [];
                var match_methods = {};
                for (var matchid in matches) {
                    arr_all_matchids.push(matchid);
                    if (danma_matches[matchid]) {
                        danma_matchids.push(matchid);
                    } else {
                        choose_matchids.push(matchid);
                    }
                    match_methods[matchid] = match_methods[matchid] || [];
                    for (var type in matches[matchid]) {
                        match_methods[matchid].push(matchid + '_' + type)
                    }
                }
                if (danma_count === 0) {
                    choose_matchids = arr_all_matchids;
                }

                //对每场比赛进行下注解析，获取最大赔率组合
                me.analyseBet(matches);
                me.matches = matches;

                var all_bet_list = [];
                for (var i = 0; i < selectsArr.length; i++) {
                    key = '' + selectsArr[i]['a'] + '_' + selectsArr[i]['b'];
                    for (var j = 0; j < types.length; j++) {
                        if (key == types[j]['code']) {
                            var numcfg = types[j]['numcfg'];
                            var choose_count = selectsArr[i]['a'] - danma_count;
                            if (choose_count <= 0 || choose_count > choose_matchids.length) {
                                return; //串关数据异常
                            }
                            var match_combine = me.combine(choose_matchids, choose_count); //先取赛事组合 3串4 则先取3场赛事
                            for (var k = 0; k < match_combine.length; k++) {
                                var matchids = match_combine[k]; //赛事组 [201602077016, 201602077017]
                                matchids = matchids.concat(danma_matchids);
                                var arr_tmp = [];
                                for (var l = 0; l < matchids.length; l++) {
                                    var matchid = matchids[l];
                                    var arr_methods = match_methods[matchid]; //取出赛事投注的玩法  {win:{}, ''correctScore:{}}
                                    arr_tmp.push(arr_methods);
                                }
                                //                                                    console.log(arr_tmp); //  [["201602077013_handicapWin"], ["201602077014_handicapWin"], ["201602077016_win", "201602077016_handicapWin"]]
                                var method_combination = me.combination(arr_tmp); //取出玩法的全集合
                                for (var m = 0; m < method_combination.length; m++) {
                                    var _methods = method_combination[m];
                                    //                                                        console.log(_methods); // ["201602077013_handicapWin", "201602077014_handicapWin", "201602077016_win"]
                                    //这里开始拆分组合过关为M串1
                                    for (p in numcfg) {
                                        if (numcfg.hasOwnProperty(p)) {
                                            //console.log(p); //2_1
                                            var _split_arr = p.split('_');
                                            var _method_combine = me.combine(_methods, _split_arr[0]); //拆分M串1
                                            for (var n = 0; n < _method_combine.length; n++) {
                                                var _arr_methods = _method_combine[n];
                                                var _arr_bet = [];
                                                for (var o = 0; o < _arr_methods.length; o++) {
                                                    var str_key = _arr_methods[o];
                                                    //console.log(str_key); //201602077013_handicapWin
                                                    var _arr = str_key.split('_');
                                                    var _matchid = _arr[0];
                                                    var _type = _arr[1];
                                                    _arr_bet.push(matches[_matchid][_type]);
                                                    //                                                                        console.log(match); // [Object { action="addOrder",  matchid="201602077013",  type="handicapWin",  更多...}]
                                                }
                                                var bet_combination = me.combination(_arr_bet); //取出下注代码的全集合

                                                all_bet_list = all_bet_list.concat(bet_combination);

                                            }
                                        }
                                    }
                                    /*
                                     */
                                }
                                //                                                        console.log(method_combination)
                            }
                            //						for(p in types[j]['numcfg']){
                            //							if(types[j]['numcfg'].hasOwnProperty(p)){
                            //								obj = {};
                            //								obj[p] = 1;
                            //								lastresult.push(me.getCountFreeForGroup(selectsArr[i]['a'], obj));
                            //							}
                            //						}
                        }
                    }
                }


                //console.log(orders);
               //console.log(all_bet_list);
                //            console.log(all_bet_list.length);
                num = all_bet_list.length;
                //投注数组
                me.all_bet_list = all_bet_list;
                //理论奖金最大值
            	var total_value = 0;
            	//遍历下注订单
                for(var i in all_bet_list){
                	//每注中的比赛场次中，是否每次比赛都符合 比赛对应的最大赔率组合数组
                	var isValue = false;
                	for(var j in all_bet_list[i]){
                		var matchid = all_bet_list[i][j].matchid;
                		
                		if(me.match_max_resule[matchid].indexOf(all_bet_list[i][j].value)!=-1){
                			isValue = true;
                		}else{
                			isValue = false;
                			break;
                		}
                	}

                	// 如果每场比赛都符合，计算本注单中的赔率乘积
                	if(isValue){
    					var odd_value = 1;
            			for(var j in all_bet_list[i]){
                			odd_value *= all_bet_list[i][j].odds;
                		}
                		//奖乘机进行累加，获得最大奖金最大值
        				total_value += Number(odd_value);
                	}
                }

                //奖最大值*2(单价)*倍数
                me.setMaxPrize((total_value*2*me.getMultiple()).toFixed(2));
                //计算最大，最小奖金值
                me.countMaxMixPrize();

            return num;

		},
		splitOrders:function(orders, num){
			var me = this;
			return me.combine(orders, num);
		},
		splitSelects:function(selects){
			var me = this,
				p,
				a,
				b,
				result = [];
			for(p in selects){
				if(selects.hasOwnProperty(p)){
					a = Number(p.split('_')[0]);
					b = Number(p.split('_')[1]);
					result.push({'a':a, 'b':b});
				}
			}
			return result;
		},
		getSubmitData:function(){
			var me = this,
				orders = me.getOrders(),
				selects = me.getSelects(),
				result = {'orders':[], 'selects':[]},
				strarr = [],
				i = 0,
				len = orders.length,
				j = 0,
				len2,
				rowdata,
				p,
				key,
				ordersHash = {};

			for(i = 0; i < len; i++){
				rowdata = [];
				strarr = [];
				len2 = orders[i]['bets'].length;
				for(j = 0; j < len2; j++){
					strarr.push(orders[i]['bets'][j]['value']);
				}
				key = orders[i]['matchid'];
				if(!ordersHash[key]){
					//期号
					ordersHash[key] = {'matchid':key, 'selects':[], 'dan':0};
				}
				//选择内容
				ordersHash[key]['selects'] = ordersHash[key]['selects'].concat(strarr);
				//胆
				ordersHash[key]['dan'] = orders[i]['dan'];

				/**
				//期号
				rowdata.push(orders[i]['matchid'] + ':');
				//胆
				rowdata.push(':' + orders[i]['dan']);
				**/
			}

			for(p in ordersHash){
				if(ordersHash.hasOwnProperty(p)){
					result['orders'].push(ordersHash[p]['matchid'] + ':' + (ordersHash[p]['selects'].join('.')) + ':' + ordersHash[p]['dan']);
				}
			}

			result['orders'] = result['orders'].join('+');
			
			for(p in selects){
				if(selects.hasOwnProperty(p)){
					result['selects'].push(p);
				}
			}
			result['selects'] = result['selects'].join(',');

			result['multiple'] = me.getMultiple();
			result['betnum'] = me.getCount();
			result['money'] = result['betnum'] * 2;

			return result;
		},
		combine: function(list, num, last) {
			var result = [],
				i = 0;
			last = last || [];
			if (num == 0) {
				return [last];
			}
			for (; i <= list.length - num; i++) {
				result = result.concat(arguments.callee(list.slice(i + 1), num - 1, last.slice(0).concat(list[i])));
			}
			return result;
		},
		combination: function(arr2) {
			if (arr2.length < 1) {
				return [];
			}
			var w = arr2[0].length,
				h = arr2.length,
				i, j,
				m = [],
				n,
				result = [],
				_row = [];

			m[i = h] = 1;

			while (i--) {
				m[i] = m[i + 1] * arr2[i].length;
			}
			n = m[0];
			for (i = 0; i < n; i++) {
				_row = [];
				for (j = 0; j < h; j++) {
					_row[j] = arr2[j][~~(i % m[j] / m[j + 1])];
				}
				result[i] = _row;
			}
			return result;
		},
		//计算最大，最小奖金值
		countMaxMixPrize:function(){
			var me = this;
			//最大奖金比赛数组
			var max_bet_list = [];
			//最小奖金比赛数组
			var min_bet_list = [];
        	//遍历下注订单
            for(var i in me.all_bet_list){
            	//每注中的比赛场次中，是否每次比赛都符合 比赛对应的最大赔率组合数组
            	var isMaxValue = false;
            	var isMinValue = false;

            	for(var j in me.all_bet_list[i]){
            		var matchid = me.all_bet_list[i][j].matchid;
            		
            		if(me.match_max_resule[matchid].indexOf(me.all_bet_list[i][j].value)!=-1){
            			isMaxValue = true;
            		}else{
            			isMaxValue = false;
            			break;
            		}
            	}

            	for(var j in me.all_bet_list[i]){
            		var matchid = me.all_bet_list[i][j].matchid;
            		
            		if(me.match_min_resule[matchid].indexOf(me.all_bet_list[i][j].value)!=-1){
            			isMinValue = true;
            		}else{
            			isMinValue = false;
            			break;
            		}
            	}

            	// 如果每场比赛都符合，计算本注单中的赔率乘积
            	if(isMaxValue){
					max_bet_list.push(me.all_bet_list[i]);
            	}

            	// 如果每场比赛都符合，计算本注单中的赔率乘积
            	if(isMinValue){
					min_bet_list.push(me.all_bet_list[i]);
            	}
            }

            //标题对象
            //2串1：[...] ,3串1：[...] 
			var bet_list_data = {};
			//关键字数组
			var key_name_arr = [];

			for (var v1 in max_bet_list) {
			    var key_name = max_bet_list[v1].length + "_1";

			    if (key_name_arr.indexOf(key_name) == -1) {
			        key_name_arr.push(key_name);
			        bet_list_data[key_name] = [];
			    }

			    bet_list_data[key_name].push(max_bet_list[v1]);
			}
			// console.log(me.bet_list_data);

			window.detail_obj = { 
									'wayInfo':bet_list_data ,
									'min_bet_list':min_bet_list,
									'max_bet_list':max_bet_list,
									'match_max_odd':me.match_max_odd,
									'match_min_odd':me.match_min_odd,
								};

			//创建明细列表总值数据对象
			// console.log(max_bet_list);
			// console.log(me.match_max_odd);
		},
		//解析
		analyseBet:function(matches){
            var me = this;
            me.match_max_resule={};

            me.match_min_resule={};

            //每场比赛的最大赔率对象
            me.match_max_odd={};
             //每场比赛的最小赔率对象-有中奖
            me.match_min_odd={};
            //思路说明
				//match_resule 为每场比赛所有比分的赔率组合列表对象，
				//1.分别计算胜、平、负对象中的最大赔率和值。
				//2.计算[胜，平，负]中最大赔率，确认本场比赛的最大的押注形式
				//3.根据最大赔率，逆推出押注数值组合
				//4.根据数值组合，推出押注的data-value数组组合
				//5.通过遍历订单，匹配每注中的所有比赛，都符合data-value数组的场次

			for(var v1 in matches){
				//[0,0,0,0,0]=> 胜平负，比分，总进球，半全场，让球
	            var match_resule={};
	            match_resule.matchid = '';
	            match_resule.handicap = null;
	            //最大赔率组合
				match_resule.res_win = {
					'win_10':[0,0,0,0,0],
					'win_20':[0,0,0,0,0],
					'win_21':[0,0,0,0,0],
					'win_30':[0,0,0,0,0],
					'win_31':[0,0,0,0,0],
					'win_32':[0,0,0,0,0],
					'win_40':[0,0,0,0,0],
					'win_41':[0,0,0,0,0],
					'win_42':[0,0,0,0,0],
					'win_50':[0,0,0,0,0],
					'win_51':[0,0,0,0,0],
					'win_52':[0,0,0,0,0],
					'win_90':[0,0,0,0,0]
				};
				match_resule.res_equal = {
					'equal_00':[0,0,0,0,0],
					'equal_11':[0,0,0,0,0],
					'equal_22':[0,0,0,0,0],
					'equal_33':[0,0,0,0,0],
					'equal_99':[0,0,0,0,0]
				};
				match_resule.res_lose = {
					'lose_01':[0,0,0,0,0],
					'lose_02':[0,0,0,0,0],
					'lose_12':[0,0,0,0,0],
					'lose_03':[0,0,0,0,0],
					'lose_13':[0,0,0,0,0],
					'lose_23':[0,0,0,0,0],
					'lose_04':[0,0,0,0,0],
					'lose_14':[0,0,0,0,0],
					'lose_24':[0,0,0,0,0],
					'lose_05':[0,0,0,0,0],
					'lose_15':[0,0,0,0,0],
					'lose_25':[0,0,0,0,0],
					'lose_09':[0,0,0,0,0]
				};

				//最小赔率组合
				match_resule.min_res_win = {
					'win_10':[0,0,0,0,0],
					'win_20':[0,0,0,0,0],
					'win_21':[0,0,0,0,0],
					'win_30':[0,0,0,0,0],
					'win_31':[0,0,0,0,0],
					'win_32':[0,0,0,0,0],
					'win_40':[0,0,0,0,0],
					'win_41':[0,0,0,0,0],
					'win_42':[0,0,0,0,0],
					'win_50':[0,0,0,0,0],
					'win_51':[0,0,0,0,0],
					'win_52':[0,0,0,0,0],
					'win_90':[0,0,0,0,0]
				};
				match_resule.min_res_equal = {
					'equal_00':[0,0,0,0,0],
					'equal_11':[0,0,0,0,0],
					'equal_22':[0,0,0,0,0],
					'equal_33':[0,0,0,0,0],
					'equal_99':[0,0,0,0,0]
				};
				match_resule.min_res_lose = {
					'lose_01':[0,0,0,0,0],
					'lose_02':[0,0,0,0,0],
					'lose_12':[0,0,0,0,0],
					'lose_03':[0,0,0,0,0],
					'lose_13':[0,0,0,0,0],
					'lose_23':[0,0,0,0,0],
					'lose_04':[0,0,0,0,0],
					'lose_14':[0,0,0,0,0],
					'lose_24':[0,0,0,0,0],
					'lose_05':[0,0,0,0,0],
					'lose_15':[0,0,0,0,0],
					'lose_25':[0,0,0,0,0],
					'lose_09':[0,0,0,0,0]
				};

				//胜胜 平胜 负胜 胜平 平平 负平 胜负 平负 负负
				//1033,1013,1003,1031,1011,1001,1030,1010,1000
				match_resule.half_full_arr=[0,0,0,0,0,0,0,0,0];

				for(var v2 in matches[v1]){
					for(var v3 in matches[v1][v2]){
						
						match_resule.matchid = matches[v1][v2][v3].matchid;
						//将所选的下注内容，放置到match_resule对象中,用于匹配最大的赔率和值
						switch(matches[v1][v2][v3].type){
							case 'win':
								switch(matches[v1][v2][v3].value){
									case '3':
										for(var obj in match_resule.res_win){
											match_resule.res_win[obj][0]=matches[v1][v2][v3].odds;
										}
										for(var obj in match_resule.min_res_win){
											match_resule.min_res_win[obj][0]=matches[v1][v2][v3].odds;
										}
										break;
									case '1':
										for(var obj in match_resule.res_equal){
											match_resule.res_equal[obj][0]=matches[v1][v2][v3].odds;
										}
										for(var obj in match_resule.min_res_equal){
											match_resule.min_res_equal[obj][0]=matches[v1][v2][v3].odds;
										}
										break;
									case '0':
										for(var obj in match_resule.res_lose){
											match_resule.res_lose[obj][0]=matches[v1][v2][v3].odds;
										}
										for(var obj in match_resule.min_res_lose){
											match_resule.min_res_lose[obj][0]=matches[v1][v2][v3].odds;
										}
										break;
									}
								break;

							case 'correctScore':
								var name = matches[v1][v2][v3].value;
								if(name.charAt(0) > name.charAt(1)){

									var indexName = 'win_'+name;
									match_resule.res_win[indexName][1]=matches[v1][v2][v3].odds;
									match_resule.min_res_win[indexName][1]=matches[v1][v2][v3].odds;

								}else if(name.charAt(0) == name.charAt(1)){

									var indexName = 'equal_'+name;
									match_resule.res_equal[indexName][1]=matches[v1][v2][v3].odds;
									match_resule.min_res_equal[indexName][1]=matches[v1][v2][v3].odds;

								}else{

									var indexName = 'lose_'+name;
									match_resule.res_lose[indexName][1]=matches[v1][v2][v3].odds;
									match_resule.min_res_lose[indexName][1]=matches[v1][v2][v3].odds;

								}
								break;

							case 'totalGoals':
								switch(matches[v1][v2][v3].value){
									case '100':
										match_resule.res_equal['equal_00'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_equal['equal_00'][2]=matches[v1][v2][v3].odds;
										break;
									case '101':
										match_resule.res_win['win_10'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_10'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_01'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_01'][2]=matches[v1][v2][v3].odds;
										break;
									case '102':
										match_resule.res_win['win_20'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_20'][2]=matches[v1][v2][v3].odds;

										match_resule.res_equal['equal_11'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_equal['equal_11'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_02'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_02'][2]=matches[v1][v2][v3].odds;
										break;
									case '103':
										match_resule.res_win['win_21'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_30'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_21'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_30'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_12'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_03'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_12'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_03'][2]=matches[v1][v2][v3].odds;
										break;
									case '104':
										match_resule.res_win['win_31'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_40'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_31'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_40'][2]=matches[v1][v2][v3].odds;

										match_resule.res_equal['equal_22'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_equal['equal_22'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_13'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_04'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_13'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_04'][2]=matches[v1][v2][v3].odds;
										break;
									case '105':
										match_resule.res_win['win_32'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_41'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_50'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_32'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_41'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_50'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_23'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_14'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_05'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_23'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_14'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_05'][2]=matches[v1][v2][v3].odds;
										break;
									case '106':
										match_resule.res_win['win_42'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_51'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_42'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_51'][2]=matches[v1][v2][v3].odds;

										match_resule.res_equal['equal_33'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_equal['equal_33'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_24'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_15'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_24'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_15'][2]=matches[v1][v2][v3].odds;
										break;
									case '107':
										match_resule.res_win['win_52'][2]=matches[v1][v2][v3].odds;
										match_resule.res_win['win_90'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_52'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_win['win_90'][2]=matches[v1][v2][v3].odds;

										match_resule.res_equal['equal_99'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_equal['equal_99'][2]=matches[v1][v2][v3].odds;

										match_resule.res_lose['lose_25'][2]=matches[v1][v2][v3].odds;
										match_resule.res_lose['lose_09'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_25'][2]=matches[v1][v2][v3].odds;
										match_resule.min_res_lose['lose_09'][2]=matches[v1][v2][v3].odds;
										break;
								}
								break;

							case 'haFu':
								switch(matches[v1][v2][v3].value){
									//.胜
									case '1033':match_resule.half_full_arr[0]=matches[v1][v2][v3].odds;break;
									case '1013':match_resule.half_full_arr[1]=matches[v1][v2][v3].odds;break;
									case '1003':match_resule.half_full_arr[2]=matches[v1][v2][v3].odds;break;
									//.平
									case '1031':match_resule.half_full_arr[3]=matches[v1][v2][v3].odds;break;
									case '1011':match_resule.half_full_arr[4]=matches[v1][v2][v3].odds;break;
									case '1001':match_resule.half_full_arr[5]=matches[v1][v2][v3].odds;break;
									//.负
									case '1030':match_resule.half_full_arr[6]=matches[v1][v2][v3].odds;break;
									case '1010':match_resule.half_full_arr[7]=matches[v1][v2][v3].odds;break;
									case '1000':match_resule.half_full_arr[8]=matches[v1][v2][v3].odds;break;
								}
								//胜胜 平胜 负胜取一个最大值
								for(var obj in match_resule.res_win){
									//客队比分为0
									if(obj.charAt(obj.length-1)=='0' && obj.charAt(obj.length-2)!='9'){
										match_resule.res_win[obj][3]=match_resule.half_full_arr[0]>match_resule.half_full_arr[1]?match_resule.half_full_arr[0]:match_resule.half_full_arr[1];

										if(match_resule.min_res_win[obj][0]!=0){
											match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[1]?match_resule.half_full_arr[0]:match_resule.half_full_arr[1];
										}else{//只考虑半全场的情况
											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]==0){
												match_resule.min_res_win[obj][3] = 0;
											}

											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[1];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]==0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[1]?match_resule.half_full_arr[0]:match_resule.half_full_arr[1];
											}
										}
									}else{
										match_resule.res_win[obj][3]=match_resule.half_full_arr[0]>match_resule.half_full_arr[1]?
											(match_resule.half_full_arr[0]>match_resule.half_full_arr[2]?match_resule.half_full_arr[0]:match_resule.half_full_arr[2]):
											(match_resule.half_full_arr[1]>match_resule.half_full_arr[2]?match_resule.half_full_arr[1]:match_resule.half_full_arr[2]);

										if(match_resule.min_res_win[obj][0]!=0){
											match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[1]?
											(match_resule.half_full_arr[0]<match_resule.half_full_arr[2]?match_resule.half_full_arr[0]:match_resule.half_full_arr[2]):
											(match_resule.half_full_arr[1]<match_resule.half_full_arr[2]?match_resule.half_full_arr[1]:match_resule.half_full_arr[2]);
										}else{//只考虑半全场的情况
											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]==0 && match_resule.half_full_arr[2]==0){
												match_resule.min_res_win[obj][3]=0;
											}

											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]==0 && match_resule.half_full_arr[2]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[2];
											}

											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]!=0 && match_resule.half_full_arr[2]==0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[1];
											}

											if(match_resule.half_full_arr[0]==0 && match_resule.half_full_arr[1]!=0 && match_resule.half_full_arr[2]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[1]<match_resule.half_full_arr[2]?match_resule.half_full_arr[1]:match_resule.half_full_arr[2];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]==0 && match_resule.half_full_arr[2]==0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]==0 && match_resule.half_full_arr[2]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[2]?match_resule.half_full_arr[0]:match_resule.half_full_arr[2];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]!=0 && match_resule.half_full_arr[2]==0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[1]?match_resule.half_full_arr[0]:match_resule.half_full_arr[1];
											}

											if(match_resule.half_full_arr[0]!=0 && match_resule.half_full_arr[1]!=0 && match_resule.half_full_arr[2]!=0){
												match_resule.min_res_win[obj][3]=match_resule.half_full_arr[0]<match_resule.half_full_arr[1]?
												(match_resule.half_full_arr[0]<match_resule.half_full_arr[2]?match_resule.half_full_arr[0]:match_resule.half_full_arr[2]):
												(match_resule.half_full_arr[1]<match_resule.half_full_arr[2]?match_resule.half_full_arr[1]:match_resule.half_full_arr[2]);
											}
										}
										
									}
								}
								//胜平 平平 负平取一个最大值
								for(var obj in match_resule.res_equal){
									if(obj == "equal_00"){
										match_resule.res_equal[obj][3]=match_resule.half_full_arr[4];

										match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[4];
									}else{
										match_resule.res_equal[obj][3]=match_resule.half_full_arr[3]>match_resule.half_full_arr[4]?
											(match_resule.half_full_arr[3]>match_resule.half_full_arr[5]?match_resule.half_full_arr[3]:match_resule.half_full_arr[5]):
											(match_resule.half_full_arr[4]>match_resule.half_full_arr[5]?match_resule.half_full_arr[4]:match_resule.half_full_arr[5]);

										if(match_resule.min_res_equal[obj][0]!=0){
											match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[3]<match_resule.half_full_arr[4]?
											(match_resule.half_full_arr[3]<match_resule.half_full_arr[5]?match_resule.half_full_arr[3]:match_resule.half_full_arr[5]):
											(match_resule.half_full_arr[4]<match_resule.half_full_arr[5]?match_resule.half_full_arr[4]:match_resule.half_full_arr[5]);
										}else{//只考虑半全场
											if(match_resule.half_full_arr[3]==0 && match_resule.half_full_arr[4]==0 && match_resule.half_full_arr[5]==0){
												match_resule.min_res_equal[obj][3]=0;
											}

											if(match_resule.half_full_arr[3]==0 && match_resule.half_full_arr[4]==0 && match_resule.half_full_arr[5]!=0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[5];
											}

											if(match_resule.half_full_arr[3]==0 && match_resule.half_full_arr[4]!=0 && match_resule.half_full_arr[5]==0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[4];
											}

											if(match_resule.half_full_arr[3]==0 && match_resule.half_full_arr[4]!=0 && match_resule.half_full_arr[5]!=0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[4]<match_resule.half_full_arr[5]?match_resule.half_full_arr[4]:match_resule.half_full_arr[5];
											}

											if(match_resule.half_full_arr[3]!=0 && match_resule.half_full_arr[4]==0 && match_resule.half_full_arr[5]==0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[3];
											}

											if(match_resule.half_full_arr[3]!=0 && match_resule.half_full_arr[4]==0 && match_resule.half_full_arr[5]!=0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[3]<match_resule.half_full_arr[5]?match_resule.half_full_arr[3]:match_resule.half_full_arr[5];
											}

											if(match_resule.half_full_arr[3]!=0 && match_resule.half_full_arr[4]!=0 && match_resule.half_full_arr[5]==0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[3]<match_resule.half_full_arr[4]?match_resule.half_full_arr[3]:match_resule.half_full_arr[4];
											}

											if(match_resule.half_full_arr[3]!=0 && match_resule.half_full_arr[4]!=0 && match_resule.half_full_arr[5]!=0){
												match_resule.min_res_equal[obj][3]=match_resule.half_full_arr[3]<match_resule.half_full_arr[4]?
												(match_resule.half_full_arr[3]<match_resule.half_full_arr[5]?match_resule.half_full_arr[3]:match_resule.half_full_arr[5]):
												(match_resule.half_full_arr[4]<match_resule.half_full_arr[5]?match_resule.half_full_arr[4]:match_resule.half_full_arr[5]);
											}
										}
										
									}
								}
								//胜负 平负 负负取一个最大值
								for(var obj in match_resule.res_lose){
									if(obj.charAt(obj.length-2)=='0' && obj.charAt(obj.length-1)!='9'){
										match_resule.res_lose[obj][3]=match_resule.half_full_arr[7]>match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8];

										if(match_resule.min_res_lose[obj][0]!=0){
											match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[7]<match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8];
										}else{
											if(match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3] = 0;
											}

											if(match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[8];
											}

											if(match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[7];
											}

											if(match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[7]<match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8];
											}
										}
										
									}else{
										match_resule.res_lose[obj][3]=match_resule.half_full_arr[6]>match_resule.half_full_arr[7]?
											(match_resule.half_full_arr[6]>match_resule.half_full_arr[8]?match_resule.half_full_arr[6]:match_resule.half_full_arr[8]):
											(match_resule.half_full_arr[7]>match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8]);

										if(match_resule.min_res_lose[obj][0]!=0){
											match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[6]<match_resule.half_full_arr[7]?
											(match_resule.half_full_arr[6]<match_resule.half_full_arr[8]?match_resule.half_full_arr[6]:match_resule.half_full_arr[8]):
											(match_resule.half_full_arr[7]<match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8]);
										}else{
											if(match_resule.half_full_arr[6]==0 && match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3]=0;
											}

											if(match_resule.half_full_arr[6]==0 && match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[8];
											}

											if(match_resule.half_full_arr[6]==0 && match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[7];
											}

											if(match_resule.half_full_arr[6]==0 && match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[7]<match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8];
											}

											if(match_resule.half_full_arr[6]!=0 && match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[6];
											}

											if(match_resule.half_full_arr[6]!=0 && match_resule.half_full_arr[7]==0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[6]<match_resule.half_full_arr[8]?match_resule.half_full_arr[6]:match_resule.half_full_arr[8];
											}

											if(match_resule.half_full_arr[6]!=0 && match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]==0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[6]<match_resule.half_full_arr[7]?match_resule.half_full_arr[6]:match_resule.half_full_arr[7];
											}

											if(match_resule.half_full_arr[6]!=0 && match_resule.half_full_arr[7]!=0 && match_resule.half_full_arr[8]!=0){
												match_resule.min_res_lose[obj][3]=match_resule.half_full_arr[6]<match_resule.half_full_arr[7]?
												(match_resule.half_full_arr[6]<match_resule.half_full_arr[8]?match_resule.half_full_arr[6]:match_resule.half_full_arr[8]):
												(match_resule.half_full_arr[7]<match_resule.half_full_arr[8]?match_resule.half_full_arr[7]:match_resule.half_full_arr[8]);
											}
										}
										
									}
								}
								break;

							case 'handicapWin':
								match_resule.handicap = matches[v1][v2][v3].handicap;
								//让球+
								if(matches[v1][v2][v3].handicap>0){
									//让球为正数，主队赢比赛->让球必赢，主队平比赛->让球必赢，主队输比赛->计算让球数(胜、平、负均可出现)
									switch(matches[v1][v2][v3].value){
										case '10003':
											for(var obj in match_resule.res_win){
												match_resule.res_win[obj][4]=matches[v1][v2][v3].odds;

												match_resule.min_res_win[obj][4]=matches[v1][v2][v3].odds;
											}
											for(var obj in match_resule.res_equal){
												match_resule.res_equal[obj][4]=matches[v1][v2][v3].odds;

												match_resule.min_res_equal[obj][4]=matches[v1][v2][v3].odds;
											}
											for(var obj in match_resule.res_lose){
												var score_1 = Number(obj.charAt(obj.length-1));
												var score_2 = Number(obj.charAt(obj.length-2));
												if((score_1-score_2)<matches[v1][v2][v3].handicap){
													match_resule.res_lose[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_lose[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										case '10001':
											for(var obj in match_resule.res_lose){
												var score_1 = Number(obj.charAt(obj.length-1));
												var score_2 = Number(obj.charAt(obj.length-2));
												if((score_1-score_2)==matches[v1][v2][v3].handicap){
													match_resule.res_lose[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_lose[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										case '10000':
											for(var obj in match_resule.res_lose){
												var score_1 = Number(obj.charAt(obj.length-1));
												var score_2 = Number(obj.charAt(obj.length-2));
												if((score_1-score_2)>matches[v1][v2][v3].handicap){
													match_resule.res_lose[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_lose[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										default:break;
									}
								}else{
								//让球-
								//让球为负数，主队负比赛->让球必负，主队平比赛->让球必负，主队输比赛->计算让球数(胜、平、负均可出现)
									switch(matches[v1][v2][v3].value){
										case '10003':
											for(var obj in match_resule.res_win){
												var score_1 = Number(obj.charAt(obj.length-2));
												var score_2 = Number(obj.charAt(obj.length-1));
												if((score_1-score_2)>(-matches[v1][v2][v3].handicap)){
													match_resule.res_win[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_win[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										case '10001':
											for(var obj in match_resule.res_win){
												var score_1 = Number(obj.charAt(obj.length-2));
												var score_2 = Number(obj.charAt(obj.length-1));
												if((score_1-score_2)==(-matches[v1][v2][v3].handicap)){
													match_resule.res_win[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_win[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										case '10000':
											for(var obj in match_resule.res_lose){
												match_resule.res_lose[obj][4]=matches[v1][v2][v3].odds;

												match_resule.min_res_lose[obj][4]=matches[v1][v2][v3].odds;
											}
											for(var obj in match_resule.res_equal){
												match_resule.res_equal[obj][4]=matches[v1][v2][v3].odds;

												match_resule.min_res_equal[obj][4]=matches[v1][v2][v3].odds;
											}
											for(var obj in match_resule.res_win){
												var score_1 = Number(obj.charAt(obj.length-2));
												var score_2 = Number(obj.charAt(obj.length-1));
												if((score_1-score_2)<(-matches[v1][v2][v3].handicap)){
													match_resule.res_win[obj][4]=matches[v1][v2][v3].odds;

													match_resule.min_res_win[obj][4]=matches[v1][v2][v3].odds;
												}
											}
											break;
										default:break;
									}
								}
								break;

							default:break;
						}
						
						
					}
				}
				// console.log(match_resule);
				//思路说明
				//match_resule 为每场比赛所有比分的赔率组合列表对象，
				//1.分别计算胜、平、负对象中的最大赔率和值。
				//2.计算[胜，平，负]中最大赔率，确认本场比赛的最大的押注形式
				//3.根据最大赔率，逆推出押注数值组合
				//4.根据数值组合，推出押注的data-value数组组合
				//5.通过遍历订单，匹配每注中的所有比赛，都符合data-value数组的场次
				// console.log(match_resule);
				//[win equal lose]
				var match_resule_arr = [0,0,0];
				//[win equal lose]
				var match_min_resule_arr = [0,0,0];
				//win赔率列表
				var win_order_res_arr = [];
				var win_order_res_min_arr = [];
				var max_win_order_res = 0;
				var min_win_order_res = 0;
				//win获取最大赔率的组合方式
				for(var obj in match_resule.res_win){
					var total_odds = 0;
					for(var v in match_resule.res_win[obj]){
						total_odds += match_resule.res_win[obj][v];
					}
					win_order_res_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in win_order_res_arr){
					if(win_order_res_arr[i] > max_win_order_res){
						max_win_order_res = win_order_res_arr[i];
					}
				}
				match_resule_arr[0]=max_win_order_res;

				//win获取最小赔率的组合方式
				for(var obj in match_resule.min_res_win){
					var total_odds = 0;
					for(var v in match_resule.min_res_win[obj]){
						total_odds += match_resule.min_res_win[obj][v];
					}
					win_order_res_min_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in win_order_res_min_arr){
					if(win_order_res_min_arr[i] > 0){
						if(min_win_order_res>0){
							if(min_win_order_res > win_order_res_min_arr[i]){
								min_win_order_res = win_order_res_min_arr[i];
							}
						}else{
							min_win_order_res = win_order_res_min_arr[i];
						}
					}
				}
				match_min_resule_arr[0]=min_win_order_res;

				//equal赔率列表
				var equal_order_res_arr = [];
				var equal_order_res_min_arr = [];
				var max_equal_order_res = 0;
				var min_equal_order_res = 0;
				//equal获取最大赔率的组合方式
				for(var obj in match_resule.res_equal){
					var total_odds = 0;
					for(var v in match_resule.res_equal[obj]){
						total_odds += match_resule.res_equal[obj][v];
					}
					equal_order_res_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in equal_order_res_arr){
					if(equal_order_res_arr[i] > max_equal_order_res){
						max_equal_order_res = equal_order_res_arr[i];
					}
				}
				match_resule_arr[1]=max_equal_order_res;

				//equal获取最小赔率的组合方式
				for(var obj in match_resule.min_res_equal){
					var total_odds = 0;
					for(var v in match_resule.min_res_equal[obj]){
						total_odds += match_resule.min_res_equal[obj][v];
					}
					equal_order_res_min_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in equal_order_res_min_arr){
					if(equal_order_res_min_arr[i] > 0){
						if(min_equal_order_res>0){
							if(min_equal_order_res > equal_order_res_min_arr[i]){
								min_equal_order_res = equal_order_res_min_arr[i];
							}
						}else{
							min_equal_order_res = equal_order_res_min_arr[i];
						}
					}
				}
				match_min_resule_arr[1]=min_equal_order_res;

				//lose赔率列表
				var lose_order_res_arr = [];
				var lose_order_res_min_arr = [];
				var max_lose_order_res = 0;
				var min_lose_order_res = 0;
				//lose获取最大赔率的组合方式
				for(var obj in match_resule.res_lose){
					var total_odds = 0;
					for(var v in match_resule.res_lose[obj]){
						total_odds += match_resule.res_lose[obj][v];
					}
					lose_order_res_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in lose_order_res_arr){
					if(lose_order_res_arr[i] > max_lose_order_res){
						max_lose_order_res = lose_order_res_arr[i];
					}
				}
				match_resule_arr[2]=max_lose_order_res;

				//lose获取最小赔率的组合方式
				for(var obj in match_resule.min_res_lose){
					var total_odds = 0;
					for(var v in match_resule.min_res_lose[obj]){
						total_odds += match_resule.min_res_lose[obj][v];
					}
					lose_order_res_min_arr.push(Number(total_odds.toFixed(2)));
				}

				for(var i in lose_order_res_min_arr){
					if(lose_order_res_min_arr[i] > 0){
						if(min_lose_order_res>0){
							if(min_lose_order_res > lose_order_res_min_arr[i]){
								min_lose_order_res = lose_order_res_min_arr[i];
							}
						}else{
							min_lose_order_res = lose_order_res_min_arr[i];
						}
					}
				}
				match_min_resule_arr[2]=min_lose_order_res;

				// console.log(match_resule_arr , match_min_resule_arr);

				var max_match_odds = max_win_order_res>max_lose_order_res?
									(max_win_order_res>max_equal_order_res?max_win_order_res:max_equal_order_res):
									(max_lose_order_res>max_equal_order_res?max_lose_order_res:max_equal_order_res);

				var min_match_odds = 0;
				for(var i in match_min_resule_arr){
					if(min_match_odds==0){
						min_match_odds = match_min_resule_arr[i];
					}else{
						if(min_match_odds>match_min_resule_arr[i] && match_min_resule_arr[i]!=0){
							min_match_odds = match_min_resule_arr[i];
						}
					}
				}

				//将每次比赛组合的最大赔率保存
				me.match_max_odd[match_resule.matchid] = max_match_odds;
				//将每次比赛组合的最小赔率保存
				me.match_min_odd[match_resule.matchid] = min_match_odds;

				var win_name_arr = ['win_10','win_20','win_21','win_30','win_31','win_32','win_40','win_41','win_42','win_50','win_51','win_52','win_90'];
				var equal_name_arr = ['equal_00','equal_11','equal_22','equal_33','equal_99'];
				var lose_name_arr = ['lose_01','lose_02','lose_12','lose_03','lose_13','lose_23','lose_04','lose_14','lose_24','lose_05','lose_15','lose_25','lose_09'];
				var half_full_name_arr=['1033','1013','1003','1031','1011','1001','1030','1010','1000'];

				//最大赔率组合,data-value组合
				//[胜负平，比分，总进球，半全场，让球]
				var max_com_obj=[];
				switch(match_resule_arr.indexOf(max_match_odds)){
					case 0:
						var index = win_order_res_arr.indexOf(max_match_odds);
						var name = win_name_arr[index];

						if(match_resule.res_win[name][0]>0){
							max_com_obj.push('3');
						}
						if(match_resule.res_win[name][1]>0){
							max_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.res_win[name][2]>0){
							max_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.res_win[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[0] , match_resule.half_full_arr[1] , match_resule.half_full_arr[2]];
							var index2 = win_half_full_arr.indexOf(match_resule.res_win[name][3]);
							max_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.res_win[name][4]>0){
							if(match_resule.handicap>0){
								max_com_obj.push('10003');
							}else{
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) > (-match_resule.handicap)){
									max_com_obj.push('10003');
								}
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) == (-match_resule.handicap)){
									max_com_obj.push('10001');
								}
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) < (-match_resule.handicap)){
									max_com_obj.push('10000');
								}
							}
						}
						break;
					case 1:
						var index = equal_order_res_arr.indexOf(max_match_odds);
						var name = equal_name_arr[index];

						if(match_resule.res_equal[name][0]>0){
							max_com_obj.push('1');
						}
						if(match_resule.res_equal[name][1]>0){
							max_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.res_equal[name][2]>0){
							max_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.res_equal[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[3] , match_resule.half_full_arr[4] , match_resule.half_full_arr[5]];
							var index2 = 3+win_half_full_arr.indexOf(match_resule.res_equal[name][3]);
							max_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.res_equal[name][4]>0){
							if(match_resule.handicap>0){
								max_com_obj.push('10003');
							}else{
								max_com_obj.push('10000');
							}
						}
						break;
					case 2:
						var index = lose_order_res_arr.indexOf(max_match_odds);
						var name = lose_name_arr[index];

						if(match_resule.res_lose[name][0]>0){
							max_com_obj.push('0');
						}
						if(match_resule.res_lose[name][1]>0){
							max_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.res_lose[name][2]>0){
							max_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.res_lose[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[6] , match_resule.half_full_arr[7] , match_resule.half_full_arr[8]];
							var index2 = 6+win_half_full_arr.indexOf(match_resule.res_lose[name][3]);
							max_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.res_lose[name][4]>0){
							if(match_resule.handicap>0){
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) > (match_resule.handicap)){
									max_com_obj.push('10000');
								}
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) == (match_resule.handicap)){
									max_com_obj.push('10001');
								}
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) < (match_resule.handicap)){
									max_com_obj.push('10003');
								}
							}else{
								max_com_obj.push('10000');
							}
						}
						break;
					default:break;
				}

				//最小赔率组合,data-value组合
				//[胜负平，比分，总进球，半全场，让球]
				var min_com_obj=[];
				switch(match_min_resule_arr.indexOf(min_match_odds)){
					case 0:
						var index = win_order_res_min_arr.indexOf(min_match_odds);
						var name = win_name_arr[index];

						if(match_resule.min_res_win[name][0]>0){
							min_com_obj.push('3');
						}
						if(match_resule.min_res_win[name][1]>0){
							min_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.min_res_win[name][2]>0){
							min_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.min_res_win[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[0] , match_resule.half_full_arr[1] , match_resule.half_full_arr[2]];
							var index2 = win_half_full_arr.indexOf(match_resule.min_res_win[name][3]);
							min_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.min_res_win[name][4]>0){
							if(match_resule.handicap>0){
								min_com_obj.push('10003');
							}else{
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) > (-match_resule.handicap)){
									min_com_obj.push('10003');
								}
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) == (-match_resule.handicap)){
									min_com_obj.push('10001');
								}
								if((Number(name.charAt(name.length-2))-Number(name.charAt(name.length-1))) < (-match_resule.handicap)){
									min_com_obj.push('10000');
								}
							}
						}
						break;
					case 1:
						var index = equal_order_res_arr.indexOf(min_match_odds);
						var name = equal_name_arr[index];

						if(match_resule.min_res_equal[name][0]>0){
							min_com_obj.push('1');
						}
						if(match_resule.min_res_equal[name][1]>0){
							min_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.min_res_equal[name][2]>0){
							min_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.min_res_equal[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[3] , match_resule.half_full_arr[4] , match_resule.half_full_arr[5]];
							var index2 = 3+win_half_full_arr.indexOf(match_resule.min_res_equal[name][3]);
							min_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.min_res_equal[name][4]>0){
							if(match_resule.handicap>0){
								min_com_obj.push('10003');
							}else{
								min_com_obj.push('10000');
							}
						}
						break;
					case 2:
						var index = lose_order_res_arr.indexOf(min_match_odds);
						var name = lose_name_arr[index];

						if(match_resule.min_res_lose[name][0]>0){
							min_com_obj.push('0');
						}
						if(match_resule.min_res_lose[name][1]>0){
							min_com_obj.push(name.substr(name.length-2 , 2));
						}
						if(match_resule.min_res_lose[name][2]>0){
							min_com_obj.push('10'+(Number(name.charAt(name.length-2))+Number(name.charAt(name.length-1))));
						}
						if(match_resule.min_res_lose[name][3]>0){
							var win_half_full_arr = [match_resule.half_full_arr[6] , match_resule.half_full_arr[7] , match_resule.half_full_arr[8]];
							var index2 = 6+win_half_full_arr.indexOf(match_resule.min_res_lose[name][3]);
							min_com_obj.push(half_full_name_arr[index2]);
						}
						if(match_resule.min_res_lose[name][4]>0){
							if(match_resule.handicap>0){
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) > (match_resule.handicap)){
									min_com_obj.push('10000');
								}
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) == (match_resule.handicap)){
									min_com_obj.push('10001');
								}
								if((Number(name.charAt(name.length-1))-Number(name.charAt(name.length-2))) < (match_resule.handicap)){
									min_com_obj.push('10003');
								}
							}else{
								min_com_obj.push('10000');
							}
						}
						break;
					default:break;
				}

				//将获取到的max_com_obj最大组合放入对应的比赛id对象中
				me.match_max_resule[match_resule.matchid] = max_com_obj;

				//将获取到的min_com_obj最大组合放入对应的比赛id对象中
				me.match_min_resule[match_resule.matchid] = min_com_obj;
			}
			// console.log(me.match_max_odd);
			// console.log(me.match_min_odd);
			// console.log(me.match_max_resule);
			// console.log(me.match_min_resule);
		},
		/*解析奖金明细列表-比赛信息*/
		analyseMatchDetail:function(){
			var me = this;

			//设置 每次比赛的‘设胆’情况
			var matches_dan={};
			var match_inner = $('#J-order-list-cont');
			for(var v in me.matches){
				matches_dan[v] = match_inner.find('.o-match-'+v).find('.dan').hasClass('dan-active');
			}

			window.match_detail = {
				'all_matches':all_matches,
				'select_matches':me.matches,
				'matches_dan':matches_dan
			};

			//下注方式，倍数，注数，金额信息
			var bet_info={
				'way':[],
				'multiple':0,
				'num':0,
				'amount':'',
				'match_num':0,
			};
			var bet_way = $(".list-free").css('display')=='none' ? $(".list-group input[class='ct-select'][checked]") : $(".list-free input[class='ct-select'][checked]");
			bet_way.each(function () {
				var str = this.value.replace('_','串');
                bet_info['way'].push(str);
            });

            bet_info['multiple']=me.getMultiple();//倍数
            bet_info['num']=me.getCount();//注数
            bet_info['amount']=host.util.formatMoney(me.getCount() * me.getMultiple() * 2);
            bet_info['match_num']=me.getOrdersLen();

            window.bet_info = bet_info;
		},
		/*明细页面初始化*/
		initPrizeDetailPage:function(){
			var me = this;
			if(window.opener){
				//初始化 奖金明细页面的投注比赛信息
				me.initPrizeDetailMatch();
				//初始化 奖金明细页面的奖金明细
				me.initPrizeDetailTable();
				//初始化 奖金明细页面的奖金明细列表
				// me.getPrizeDetailTableList();
			}
		},
		/*初始化 奖金明细页面的投注比赛信息*/
		initPrizeDetailMatch:function(){
			var me = this;

			if(window.opener.match_detail){
				var all_matches = window.opener.match_detail.all_matches;
				var match_info = window.opener.match_detail.select_matches;
				var matches_dan =  window.opener.match_detail.matches_dan;
				
				//创建所选中的比赛对象
				var select_match=[];
				
				for(var v1 in match_info){
					var match_obj = {
						match_id:{},
						match_num:'',
						match_name:'',
						match_time:'',
						match_team1:'',
						match_team2:'',
						match_play:{},
						match_dan:''
					};

					//match_paly数组中是否存在该玩法，不存在则存入数组
					for(var v2 in match_info[v1]){
						match_obj.match_play[v2] = match_info[v1][v2];

						//仅设置一次-周X00X
						if(match_obj.match_num==''){
							match_obj.match_num = match_info[v1][v2][0].time;
						}
					}

					for(var v in all_matches){
						if(v1 == v){
							match_obj.match_id = v;
							match_obj.match_name = all_matches[v].league_name;
							match_obj.match_time = all_matches[v].match_time;
							match_obj.match_team1 = all_matches[v].home_team;
							match_obj.match_team2 = all_matches[v].away_team;
						}
					}

					for(var v in matches_dan){
						if(v1 == v){
							match_obj.match_dan = matches_dan[v]?'√':'x';
						}
					}

					//放入数组中
					select_match.push(match_obj);
				}

				// 创建行
				var htmlOuter = [];
				for(var i=0 ; i<select_match.length ; i++){
					for(var v in select_match[i].match_play){
						var tempStr = host.util.template($('#J-przie-match-row').html() , select_match[i]);
						htmlOuter.push(tempStr);
					}
				}
				$('#J-przie-match-body').append(htmlOuter.join(''));
				
				//创建跨行的行数据
				for(var i=0 ; i<select_match.length ; i++){
					var htmlOuter = [];
					var tempStr = host.util.template($('#J-prize-match-info-row').html() , select_match[i]);
					var row_num = 0;
					for(var v in select_match[i].match_play){
						row_num++;
					}
					tempStr = tempStr.replace(/<#=row_num#>/g, [row_num]);

					htmlOuter.push(tempStr);
					$('.przie-match-'+select_match[i].match_id).first().append(htmlOuter.join(''));
				}

				//创建单行数据
				for(var i=0 ; i<select_match.length ; i++){
					var index = 0;
					for(var v in select_match[i].match_play){
						var htmlOuter = [];
						var tempStr = host.util.template($('#J-prize-match-play-row').html() , select_match[i].match_play);

						// console.log(select_match[i].match_play[v]);
						var playStr = '';
						switch(v){
							case 'win': playStr="胜平负";break;
							case 'handicapWin': playStr="让球胜平负("+select_match[i].match_play[v][0].handicap+")";break;
							case 'haFu': playStr="半全场";break;
							case 'correctScore': playStr="比分";break;
							case 'totalGoals': playStr="总进球";break;
							default:break;
						}
						tempStr = tempStr.replace(/<#=play#>/g, [playStr]);

						htmlOuter.push(tempStr);

						$('.przie-match-'+select_match[i].match_id+':eq('+(index++)+')').append(htmlOuter.join(''));

						// 投注具体内容
						for(var v1 in select_match[i].match_play[v]){
							var bet_content = "<span class='item-bet-detail'>"+select_match[i].match_play[v][v1].name+"["+select_match[i].match_play[v][v1].odds+"]"+"</span>"
							$('.przie-match-'+select_match[i].match_id+':eq('+(index-1)+')').find('.col-order-table-content').append(bet_content);
						}
					}
				}


				//创建跨行的行数据-胆
				for(var i=0 ; i<select_match.length ; i++){
					var htmlOuter = [];
					var tempStr = host.util.template($('#J-prize-match-dan-row').html() , select_match[i]);
					var row_num = 0;
					for(var v in select_match[i].match_play){
						row_num++;
					}

					tempStr = tempStr.replace(/<#=row_num#>/g, [row_num]);
					tempStr = tempStr.replace(/<#=match_dan#>/g, [select_match[i].match_dan]);

					htmlOuter.push(tempStr);
					$('.przie-match-'+select_match[i].match_id).first().append(htmlOuter.join(''));
				}


				var bet_info = window.opener.bet_info;

				var str='';
				for(var i in bet_info['way']){
					if(i==0){
						str +=bet_info['way'][i];
					}else{
						str +=','+bet_info['way'][i];
					}
				}
				$('#J-detail-type').html(str);
				$('#J-detail-multiple').html(bet_info['multiple']);
				$('#J-detail-betnum').html(bet_info['num']);
				$('#J-detail-amount').html(bet_info['amount']);
			}
			
		},
		/*初始化 奖金明细页面的奖金明细*/
		initPrizeDetailTable:function(){
			var me = this;

			if(window.opener.detail_obj && window.opener.bet_info){
				var way_index = 0;
				var wayInfo = window.opener.detail_obj.wayInfo;
				var match_num = window.opener.bet_info['match_num'];

				var right_num_arr=[];
				var type_name_arr=[];
		        //创建玩法方式：2串1，3串1...
		        for (var v in wayInfo) {
		            var way_name = v.replace('_', '串');
		            way_index++;

		            type_name_arr.push(way_name);
		            right_num_arr.push(way_name.charAt(0));
		        }
		        //设置‘中奖注数’列 所跨列数
		        $('.prize-bet-num').attr({
		        	colspan: way_index
		        });

		        //所有比赛全部正确，即最大奖金额 组成数组
		        var min_bet_list = window.opener.detail_obj.min_bet_list;
				var max_bet_list = window.opener.detail_obj.max_bet_list;
				// 每场比赛的最大奖金赔率
				var match_max_odd = window.opener.detail_obj.match_max_odd;
				// 每场比赛的最小奖金赔率
				var match_min_odd = window.opener.detail_obj.match_min_odd;

				// console.log(max_bet_list);
				// console.log(right_num_arr);
				// console.log(match_max_odd);
				// console.log(match_min_odd);

				//排序整理
		        type_name_arr.sort();
		        right_num_arr.sort();

		        //过关方式名称数组
		        me.type_name_arr = type_name_arr;
		        for(var i in type_name_arr){
		        	$('.multiple_th').before('<th>' + type_name_arr[i] + '</th>');
		        }

		        //明细列表所拼接成的数据
				var przie_detail_data_arr = [];
				
				for(var i=match_num;i>=2;i--){
					var przie_detail_data_obj = {
						'right_num':'',
						'bet_num_arr':[],
						'bet_num_min_arr':[],
						'multiple':0,
						'prize_min':0,
						'prize_max':0,
						'prize_max_list':[],
						'prize_min_list':[]
					};

					var obj = me.getBetNumByRightMatchNum(i,right_num_arr,match_min_odd,match_max_odd,min_bet_list,max_bet_list);

					if(!(obj['prize_max_list'].length==0 && obj['prize_min_list']==0)){
						przie_detail_data_obj['right_num'] = i;
						przie_detail_data_obj['bet_num_arr'] = obj['bet_num_arr'];
						przie_detail_data_obj['bet_num_min_arr'] = obj['bet_num_min_arr'];
						przie_detail_data_obj['multiple'] = window.opener.bet_info['multiple'];
						przie_detail_data_obj['prize_min'] = obj['min_value'];
						przie_detail_data_obj['prize_max'] = obj['max_value'];
						przie_detail_data_obj['prize_max_list'] = obj['prize_max_list'];
						przie_detail_data_obj['prize_min_list'] = obj['prize_min_list'];

						przie_detail_data_arr.push(przie_detail_data_obj);
					}
				}

				// console.log(przie_detail_data_arr);
				//明细列表所拼接成的数据
				me.przie_detail_data_arr = przie_detail_data_arr;



		        //创建命中数量列
		        var htmlOuter=[];
		        for(var i=0 ; i<me.przie_detail_data_arr.length ; i++){
		        	var tempStr = host.util.template($('#J-przie-detail-row').html() , {right_num:me.przie_detail_data_arr[i]['right_num']});
					htmlOuter.push(tempStr);
		        }
		        $('#J-prize-detail-body').append(htmlOuter.join(''));

		        //中奖数量
		        for(var i=0 ; i<przie_detail_data_arr.length ; i++){
		        	var htmlOuter2=[];
		        	for(var j in type_name_arr){
		        		// console.log(type_name_arr[j].charAt(0));
	    				var tempStr2 = host.util.template($('#J-przie-detail-type-col').html() , {bet_num:przie_detail_data_arr[i]['bet_num_arr'][j]});
						htmlOuter2.push(tempStr2);
		        	}
		        	$('.prize-detail-'+przie_detail_data_arr[i]['right_num']).append(htmlOuter2.join(''));
		        }

		        //倍数 & 明细
		        for(var i=0 ; i<przie_detail_data_arr.length ; i++){
		        	var htmlOuter3=[];
	    			var tempStr3 = host.util.template($('#J-przie-detail-multiple-col').html() , 
				        			{
				        				multiple:przie_detail_data_arr[i]['multiple'], 
				        				prize_min:przie_detail_data_arr[i]['prize_min'], 
				        				prize_max:przie_detail_data_arr[i]['prize_max'],
				        				right_num:przie_detail_data_arr[i]['right_num']
				        			});
					htmlOuter3.push(tempStr3);
		        	$('.prize-detail-'+przie_detail_data_arr[i]['right_num']).append(htmlOuter3.join(''));
		        }
			}
			
		},
		//根据 命中比赛数量 计算出中奖注数
		//1.right_match_num:命中比赛数量
		//2.right_num_arr:命中比赛数量数组
		//3.match_min_odd：每场比赛的最小赔率
		//4.match_max_odd：每场比赛的最大赔率（获取对应命中比赛数量的比赛，组成新的数组，在max_bet_list查找含有该数组中比赛的注单，计算出注数）（注：只计算最大中奖额度的中奖数量）
		//5.min_bet_list:最小中奖额度 所组成的比赛数组
		//6.max_bet_list:最大中奖额度 所组成的比赛数组
		getBetNumByRightMatchNum:function(right_match_num , right_num_arr , match_min_odd , match_max_odd , min_bet_list , max_bet_list){
			var me = this;

			//2-1 , 3-1 ...比赛数量数组
			var bet_match_num = [];
			var bet_match_num_min = [];

			//初始化bet_match_num
			for(var i=0;i<right_num_arr.length;i++){
				bet_match_num.push(0);
				bet_match_num_min.push(0);
			}

			//根据命中场次，过滤每场比赛最大赔率数组
			var max_match_num = [];
			var min_match_num = [];

			var match_max_odd_arr = [];
			var match_min_odd_arr = [];
			//赔率数组
			var max_odds_arr=[];
			var min_odds_arr=[];

			//最大奖金值
			var max_value = 0;
			var min_value = 0;

			//将对象重新放入数组中
			//赔率最大对象
			for(var i in match_max_odd){
				var obj = {};
				obj[i] = match_max_odd[i];
				match_max_odd_arr.push(obj);

				max_odds_arr.push(match_max_odd[i]);
			}
			//赔率最小对象
			for(var i in match_min_odd){
				var obj = {};
				obj[i] = match_min_odd[i];
				match_min_odd_arr.push(obj);

				min_odds_arr.push(match_min_odd[i]);
			}

			//新的最大中奖额度 组成的 比赛数组
			var new_max_bet_list = [];
			//新的最小中奖额度 组成的 比赛数组
			var new_min_bet_list = [];

			//最大赔率计算
			if(right_match_num == match_max_odd_arr.length){
				for(var i=0;i<max_bet_list.length;i++){
					max_match_num.push(max_bet_list[i].length);
					new_max_bet_list.push(max_bet_list[i]);

					var odd_value = 1;
        			for(var j in max_bet_list[i]){
        				odd_value *= max_bet_list[i][j].odds;
        			}
             		//奖乘机进行累加，获得最大奖金最大值
    				max_value += Number(odd_value);
				}
			}else{
				//临时用于排序的数组
				var sort_arr = [];
				for(var i in max_odds_arr){
					sort_arr.push(max_odds_arr[i]);
				}
				//排序
				for(var i=0;i<sort_arr.length-1;i++){
					for(var j=i+1;j<sort_arr.length;j++){
						if(sort_arr[i]>sort_arr[j]){
							var temp = 0;
							temp = sort_arr[i];
							sort_arr[i] = sort_arr[j];
							sort_arr[j] = temp;
						}
					}
				}
				//截取right_match_num最大赔率
				var new_sort_arr=sort_arr.slice(-right_match_num);
				//根据命中场数，获取最大的比赛id号
				var new_match_id = [];

				for(var i in new_sort_arr){
					new_match_id.push('');
				}

				for(var i in new_sort_arr){
					for(var v in match_max_odd){
						if(new_sort_arr[i] == match_max_odd[v] && new_match_id.indexOf(v)==-1){
							new_match_id[i] = v;
						}
					}
				}
				
				for(var i in max_bet_list){
					var isValue = false;
                	for(var j in max_bet_list[i]){
                		var matchid = max_bet_list[i][j].matchid;
                		
                		if(new_match_id.indexOf(matchid)!=-1){
                			isValue = true;
                		}else{
                			isValue = false;
                			break;
                		}
                	}

                	// 如果每场比赛都符合，计算本注单中的赔率乘积
                	if(isValue){
                		new_max_bet_list.push(max_bet_list[i]);

            			var odd_value = 1;
            			for(var j in max_bet_list[i]){
            				odd_value *= max_bet_list[i][j].odds;
            			}
                 		//奖乘机进行累加，获得最大奖金最大值
        				max_value += Number(odd_value);
                	}
				}
				for(var i=0;i<new_max_bet_list.length;i++){
					max_match_num.push(new_max_bet_list[i].length);
				}
			}

			//最小赔率计算
			if(right_match_num == match_min_odd_arr.length){
				for(var i=0;i<min_bet_list.length;i++){
					min_match_num.push(min_bet_list[i].length);
					new_min_bet_list.push(min_bet_list[i]);

					var odd_value = 1;
        			for(var j in min_bet_list[i]){
        				odd_value *= min_bet_list[i][j].odds;
        			}
             		//奖乘机进行累加，获得最大奖金最大值
    				min_value += Number(odd_value);
				}
			}else{
				//临时用于排序的数组
				var sort_arr = [];
				for(var i in min_odds_arr){
					sort_arr.push(min_odds_arr[i]);
				}
				//排序
				for(var i=0;i<sort_arr.length-1;i++){
					for(var j=i+1;j<sort_arr.length;j++){
						if(sort_arr[i]>sort_arr[j]){
							var temp = 0;
							temp = sort_arr[i];
							sort_arr[i] = sort_arr[j];
							sort_arr[j] = temp;
						}
					}
				}
				//截取right_match_num最大赔率
				var new_sort_arr=sort_arr.slice(0,right_match_num);
				//根据命中场数，获取最大的比赛id号
				var new_match_id = [];

				for(var i in new_sort_arr){
					new_match_id.push('');
				}

				for(var i in new_sort_arr){
					for(var v in match_min_odd){
						if(new_sort_arr[i] == match_min_odd[v] && new_match_id.indexOf(v)==-1){
							new_match_id[i] = v;
						}
					}
				}
				
				for(var i in min_bet_list){
					var isValue = false;
                	for(var j in min_bet_list[i]){
                		var matchid = min_bet_list[i][j].matchid;
                		
                		if(new_match_id.indexOf(matchid)!=-1){
                			isValue = true;
                		}else{
                			isValue = false;
                			break;
                		}
                	}

                	// 如果每场比赛都符合，计算本注单中的赔率乘积
                	if(isValue){
                		new_min_bet_list.push(min_bet_list[i]);

            			var odd_value = 1;
            			for(var j in min_bet_list[i]){
            				odd_value *= min_bet_list[i][j].odds;
            			}
                 		//奖乘机进行累加，获得最大奖金最大值
        				min_value += Number(odd_value);
                	}
				}

				for(var i=0;i<new_min_bet_list.length;i++){
					min_match_num.push(new_min_bet_list[i].length);
				}
			}

			// console.log(max_match_num);
			// console.log(new_min_bet_list);
			// console.log(right_match_num);
			for(var i=0;i<max_match_num.length;i++){
				var index = right_num_arr.indexOf(max_match_num[i].toString());
				if(index != -1){
					bet_match_num[index] += 1;
				}
			}
			
			for(var i=0;i<min_match_num.length;i++){
				var index = right_num_arr.indexOf(min_match_num[i].toString());
				if(index != -1){
					bet_match_num_min[index] += 1;
				}
			}

			var obj = {
				'bet_num_arr':bet_match_num,
				'bet_num_min_arr':bet_match_num_min,
				'min_value':(min_value*2*window.opener.bet_info['multiple']).toFixed(2),
				'max_value':(max_value*2*window.opener.bet_info['multiple']).toFixed(2),
				'prize_max_list':new_max_bet_list,
				'prize_min_list':new_min_bet_list
			}
			return obj;
		},
		//初始化 奖金明细页面的奖金明细列表
		getPrizeDetailTableList:function(right_num , type){
			var me = this;

			$('#J-prize-detail-list').html('');

			// console.log(me.type_name_arr);
			// console.log(me.przie_detail_data_arr);
			// console.log("================================");
			var right_num = right_num;

			//遍历2串1 3串1 ...
			for(var v1 in me.type_name_arr){

				var htmlOuter = [];
				var htmlOuter2 = [];

				for(var v2 in me.przie_detail_data_arr){
					//命中比赛数量判断
					if(right_num == me.przie_detail_data_arr[v2]['right_num'] && me.przie_detail_data_arr[v2]['bet_num_arr'][v1]>0){

						//每场比赛最大赔率数组
						var obj = [];
						if(type == 'max'){
							obj = me.przie_detail_data_arr[v2]['prize_max_list'];
						}else{
							obj = me.przie_detail_data_arr[v2]['prize_min_list'];
						}

						for(var i in obj){
							if(me.type_name_arr[v1].charAt(0) == obj[i].length){
								var tempStr = host.util.template($('#J-prize-detail-list-tr').html() , {'type_char':me.type_name_arr[v1].charAt(0)});
								htmlOuter.push(tempStr);
							}
						}
						//创建行
						$('#J-prize-detail-list').append(htmlOuter.join(''));

						//创建跨行
						var tempStr2 = null;
						if(type == 'max'){
							tempStr2 = host.util.template($('#J-prize-detail-list-row').html() ,
														{
															'type':me.type_name_arr[v1],
															'bet_num':me.przie_detail_data_arr[v2]['bet_num_arr'][v1],
														});
						}else{
							tempStr2 = host.util.template($('#J-prize-detail-list-row').html() ,
														{
															'type':me.type_name_arr[v1],
															'bet_num':me.przie_detail_data_arr[v2]['bet_num_min_arr'][v1],
														});
						}
						htmlOuter2.push(tempStr2);

						$('.prize-detail-type-'+me.type_name_arr[v1].charAt(0)).eq(0).append(htmlOuter2.join(''));


						//创建明细列表内容
						var details_arr = [];
						for(var i in obj){
							var detail_str = '';
							var total = 1;
							if(me.type_name_arr[v1].charAt(0) == obj[i].length){
								for(var j in obj[i]){
									if(j==0){
										detail_str += ("["+obj[i][j].time+"]&nbsp;&nbsp;") + obj[i][j].odds;
									}else{
										detail_str += "&nbsp;&nbsp;x&nbsp;&nbsp;" + ("["+obj[i][j].time+"]&nbsp;&nbsp;") + obj[i][j].odds;
									}

									total *= obj[i][j].odds;
								}

								detail_str += "&nbsp;&nbsp;x&nbsp;&nbsp;" + 
											window.opener.bet_info['multiple'] + "倍" + 
											"&nbsp;&nbsp;x&nbsp;&nbsp;2元" + "&nbsp;&nbsp;=&nbsp;&nbsp;" + 
											(total*window.opener.bet_info['multiple']*2).toFixed(2) + "元";

								details_arr.push(detail_str);
							}
						}


						//写入模板
						for(var i in details_arr){
							var htmlOuter3 = [];

							var tempStr3 = host.util.template($('#J-prize-detail-list-content').html() , {'detail_content':details_arr[i]});
							htmlOuter3.push(tempStr3);

							$('.prize-detail-type-'+me.type_name_arr[v1].charAt(0)).eq(i).append(htmlOuter3.join(''));
						}
						
					}
				}

    		}
		}


	};

		
	

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
	host[name] = Main;
	
})(bomao, "SportsGame", bomao.Event);







//百家乐类,
(function(host, TableGame, undefined) {
    var defConfig = {

    };



    var pros = {
        init: function(cfg) {
            var me = this;
            me._areas = {};
            me._methods = {};
        },
        getDeskTopDom: function() {
            return this._deskTop || (this._deskTop = $('#J-desktop'));
        },
        addArea: function(opt) {
            var me = this,
                area = new host.TableGame.Area(opt),
                chips = new host.TableGame.Chips();

            chips.addEvent('delLastChip_after', function(e, chip) {
                area.fireEvent('cancelLastChip', chip);
            });
            area.chips = chips;
            me._areas[opt['name_en']] = area;

        },
        initDeskTop: function(areasConfig) {
            var me = this,
                html = [],
                it;
            $.each(areasConfig, function(i) {
                it = this;
                html.push('<div data-action="addchip" data-name="' + it['name_en'] + '" style="width:' + it['width'] + 'px;height:' + it['height'] + 'px;left:' + it['left'] + 'px;top:' + it['top'] + 'px;background-position:' + it['bgPosition'][0] + 'px ' + it['bgPosition'][1] + 'px" class="area area-' + it['name_en'] + '"></div>');
                me.addArea(it);
            });
            $(html.join('')).appendTo(me.getDeskTopDom());
        },
        editSubmitData: function(data) {
            var balls = data['balls'];
            data['balls'] = encrypt(JSON.stringify(balls));
            data['is_encoded'] = 1;
            return data;
        },
        getSubmitData: function() {
            var me = this,
                areas = me.getAreas(),
                _area,
                _chips,
                _money = 0,
                i = 0,
                len = areas.length,
                amount = 0,
                num = 1,
                way = "",
                type = "",
                wayId = 1,
                ball = "",
                ball = 0,
                multiple = 1,
                prize_group = 0,
                result = {},
                methods = me.getGameMethods(),
                wayIdAndBall={},
                method = {};

            me._methods = me.getGameMethods();
            result['gameId'] = me.getConfig('gameId');
            result['isTrace'] = 0;
            result['traceWinStop'] = 0;
            result['traceStopValue'] = 0;
            result['balls'] = [];
            $.each(areas, function() {
                var _area = this,
                    _chips = _area.getChipsCase(),
                    _money = 0,
                    wayId = 0;

                // 根据areaName获得这个area的wayId以及ball
                if (_chips.length > 0) {
                    $.each(_chips, function() {
                        _money += this.getMoney();
                    });

                    // 倍数，总额/2分单价
                    multiple = _money * 10 * 10 / 2;

                    // way = _area.getName().split("-")[0];
                    // digit = _area.getName().split("-")[1];
                    
                    wayIdAndBall = me.getWayIdAndBall(_area);

                    wayId = wayIdAndBall.wayId;
                    ball = wayIdAndBall.ball;

                    // 每个区域对应的玩法、数字、单价、注数、模式、倍数
                    result['balls'].push({
                        // 玩法ID
                        'wayId': wayId,
                        // 玩法英文名
                        'type': type,
                        // 数字
                        'ball': ball,
                        // 注数
                        'num': num,
                        // 单价：2分
                        'onePrice': 2,
                        // 模式：分模式
                        'moneyunit': 0.01,
                        // 倍数：1，默认为1
                        'multiple': multiple,
                        // 奖金组
                        'prize_group': prize_group
                    });
                    // 元转换成分
                    amount += _money * 10 * 10;

                }
            });

            //投注期数格式修改为键值对
            result['orders'] = {};
            //获得当前期号，将期号作为键
            result['orders'][me.getCurrNumber()] = 1;
            //总金额
            result['amount'] = amount;

            result['_token'] = me.getConfig('_token');

            var betInfoR = {
                    "balls": result['balls'],
                    'isFinish': true,
                    'issue': me.getCurrNumber()
                },
                betInfo = $.extend({}, betInfoR);
            me.setLastBetInfo(betInfo);
            return result;

        },
        submit: function() {
            var me = this,
                data = me.getSubmitData(),
                url = me.getConfig('submitUrl');
            // data['gameid'] = me.getConfig('gameId');
            // data['_token'] = me.getConfig('_token');
            $.ajax({
                url: url,
                dataType: 'JSON',
                method: 'POST',
                data: me.editSubmitData(data),
                beforeSend: function() {
                    me.fireEvent('submit_before', data);
                },
                success: function(data) {
                    if (Number(data['isSuccess']) == 1) {
                        me.fireEvent('success_after', data);
                    } else {
                        alert(data['Msg']);
                    }
                }
            });

        },

        //将树状数据整理成两级缓存数据
        getGameMethods: function() {
            var me = this,
                nodeCache = {},
                methodCache = {},
                data = me.getConfig("gameMethods"),
                node1,
                node2,
                node3;

            $.each(data, function() {
                node1 = this;
                node1['fullname_en'] = [node1['name_en']];
                node1['fullname_cn'] = [node1['name_cn']];
                nodeCache['' + node1['id']] = node1;
                if (node1['children']) {
                    $.each(node1['children'], function() {
                        node2 = this;
                        node2['fullname_en'] = node1['fullname_en'].concat(node2['name_en']);
                        node2['fullname_cn'] = node1['fullname_cn'].concat(node2['name_cn']);
                        nodeCache['' + node2['id']] = node2;
                        if (node2['children']) {
                            $.each(node2['children'], function() {
                                node3 = this;
                                node3['fullname_en'] = node2['fullname_en'].concat(node3['name_en']);
                                node3['fullname_cn'] = node2['fullname_cn'].concat(node3['name_cn']);
                                methodCache['' + node3['id']] = node3;
                            });
                        }
                    });
                }
            });
            return methodCache;
        },

        // 根据areaName获得area的wayId。
        getWayIdAndBall: function(area) {
            var me = this,
                areaName = area.getName(),
                wayId = 0,
                ball = 0;
                switch (areaName) {
                    case 'da':
                        methodName = 'paishuda';
                        ball = 3;
                        break;
                    case 'xiao':
                        methodName = 'paishuxiao';
                        ball = 4;
                        break;
                    case 'zhuang':
                        methodName = 'xianzhuangdaxiao';
                        ball = 1;
                        break;
                    case 'xian':
                        methodName = 'xianzhuangdaxiao';
                        ball = 0;
                        break;
                    case 'he':
                        methodName = 'xianzhuanghe';
                        ball = 2;
                        break;
                    case 'xiandui':
                        methodName = 'duizi';
                        ball = 5;
                        break;
                    case 'zhuangdui':
                        methodName = 'duizi';
                        ball = 6;
                        break;
                    case 'xianlongbao':
                        methodName = 'xianzhuanglongbao';
                        ball = 8;
                        break;
                    case 'zhuanglongbao':
                        methodName = 'xianzhuanglongbao';
                        ball = 9;
                        break;
                    case 'super':
                        methodName = 'super6';
                        ball = 7
                        break;
                };

                $.each(me._methods,function(){
                    var method = this;
                        if(method.name_en==methodName){
                            wayId = method.id;
                        }
                });
            return {
                wayId: wayId,
                ball: ball
            }
        }
    };




    var Main = host.Class(pros, TableGame);
    Main.defConfig = defConfig;
    TableGame.Bjl = Main;

})(bomao, bomao.TableGame);

(function(host, Event, $, undefined) {
    var defConfig = {

    };
    var pros = {
        init: function(cfg) {
            var me = this,
                containerName = cfg.containerName,
                value = parseInt(cfg.value);
            me.dom = '<div class="poker-container" name="' + containerName + '"><div class="card"><div class="poker poker-' + value + '"></div><div class="poker back"></div></div></div>';
            $(me.dom).insertBefore($(cfg.sibling));
        }
    };

    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BjlPoker = Main;
})(bomao, bomao.Event, jQuery);

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

//闲、庄、和 0、1、2
//闲对 5
//庄对 6

//庄闲和：0-闲、1-庄、2-和
//闲对：0-未出现，1-出现了
//庄对：0-未出现，2-出现了


(function(host, Event, $, undefined) {
    var defConfig = {
        mainContainer: '.betTrand',
    };
    var pros = {

        init: function(cfg) {
            var me = this;
            me.paneIndex = 1;
            me.currentRow = 0;
            me.currentColumn = 0;
            me.rows = 6;
            me.columns = 30;
            me.cfg = cfg;
            me.initContainer(me.cfg.mainContainer,me.paneIndex,me.rows,me.columns);
        },

        initTrend:function(items){
            var me = this;
            $.each(items, function() {
                me.addItem(this);
            });
        },

        // 初始化容器
        initContainer: function(mainContainer, paneIndex, rows, columns) {
            var me = this,
                pane = "<div class='pane' index='" + paneIndex + "'>";
            for (var i = 0; i < columns; i++) {
                var column = "<div class='column'>";
                for (var j = 0; j < rows; j++) {
                    column += "<div class='item item-blank'></div>";
                }
                column += "</div>";
                pane += column;
            }
            pane += "</div>"
            $(mainContainer).append(pane);
        },

        addItem: function(item) {
            var me = this,
                columnIndex = me.currentColumn + 1,
                rowIndex = me.currentRow + 1,
                zxh = item.zhuangxianhe,

                xd = item.xiandui,
                
                zd = item.zhuangdui,
                mainContainer = me.cfg.mainContainer,
                CLS = 'item';
            switch (zxh) {
                case 0:
                    CLS += '-xian';
                    break;
                case 1:
                    CLS += '-zhuang';
                    break;
                case 2:
                    CLS += '-he';
                    break;
            };
            zd == 1 ? CLS += '-zhuangdui' : "";
            xd == 1 ? CLS += '-xiandui' : "";

            $(mainContainer).find('.pane').last().find(".column:nth-child(" + columnIndex + ")").find(".item:nth-child(" + rowIndex + ")").removeClass("item-blank").addClass(CLS);
            if (me.currentRow >= me.rows - 1) {
                me.currentRow = 0;

                if (me.currentColumn >= me.columns - 1) {
                    me.currentColumn = 0;
                    me.paneIndex ++;
                    $(".pane").hide();
                    me.initContainer(".betTrand",me.paneIndex, me.rows, me.columns);
                } else {
                    me.currentColumn++;
                }
            } else {
                me.currentRow++;
            }
        },
    };



    var Main = host.Class(pros, Event);
    Main.defConfig = defConfig;
    host.TableGame.BjlHistory = Main;



})(bomao, bomao.Event, jQuery);
