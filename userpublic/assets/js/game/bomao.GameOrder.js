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