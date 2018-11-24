
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






