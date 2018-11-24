

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










