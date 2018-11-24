
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






