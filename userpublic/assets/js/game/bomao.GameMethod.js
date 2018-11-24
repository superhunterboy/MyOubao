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