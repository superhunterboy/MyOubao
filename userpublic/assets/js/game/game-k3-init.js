(function(host, name, Event, undefined){
	var gameConfigData = global_game_config_k3,
		defConfig = {
		},
		nodeCache = {},
		methodCache = {},
		instance;


	//将树状数据整理成两级缓存数据
	(function(){
		var data = gameConfigData['gameMethods'],
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
	})();


	var pros = {
		init:function(){
		},
		getConfig:function(key){
			if(key){
				return gameConfigData[key];
			}
			return gameConfigData;
		},
		getGameId:function(){
			return gameConfigData['gameId'];
		},
		//获取游戏英文名称
		getGameNameEn:function(){
			return gameConfigData['gameName_en'];
		},
		//获取游戏中文名称
		getGameNameCn:function(){
			return gameConfigData['gameName_cn'];
		},
		//获取最大追号期数
		getTraceMaxTimes:function(){
			return Number(gameConfigData['traceMaxTimes']);
		},
		//获取当前期开奖时间
		getCurrentLastTime:function(){
			return gameConfigData['currentNumberTime'];
		},
		//获取当前时间
		getCurrentTime:function(){
			return gameConfigData['currentTime'];
		},
		//获取当前期期号
		getCurrentGameNumber:function(){
			return gameConfigData['currentNumber'];
		},
		//获取上期期号
		getLastGameNumber:function(){
			return gameConfigData['lastNumber'];
		},
		//获得上期开奖球
		getLotteryBalls:function(){
			return gameConfigData['lotteryBalls'] || '00000';
		},
		//获取期号列表
		getGameNumbers:function(){
			return gameConfigData['gameNumbers'];
		},
		//id : methodid
		//unit : money unit (1 | 0.1)
		getLimitByMethodId:function(id, unit){
			var unit = unit || 1,maxnum = Number(this.getMethodById(id)['max_multiple']);
			return maxnum / unit;
		},
		//注单提交地址
		getSubmitUrl:function(){
			return gameConfigData['submitUrl'];
		},
		//更新开奖、配置等最新信息的地址
		getUpdateUrl:function(){
			return gameConfigData['loaddataUrl'];
		},
		//文件上传地址
		getUploadPath:function(){
			return gameConfigData['uploadPath'];
		},
		getPollBetInfoUrl:function(){
			return gameConfigData['pollBetInfoUrl'];
		},
		getPollUserAccountUrl:function(){
			return gameConfigData['pollUserAccountUrl'];
		},
		getProjectViewBaseUrl:function(){
			return gameConfigData['projectViewBaseUrl'];
		},
		getTraceViewBaseUrl:function(){
			return gameConfigData['traceViewBaseUrl'];
		},
		//js存放目录
		getJsPath:function(){
			return gameConfigData['jsPath'];
		},
		getJsSuffix:function(){
			return gameConfigData['jsSuffix'];
		},
		//默认游戏玩法
		getDefaultMethodId:function(){
			return gameConfigData['defaultMethodId'];
		},
		//获取当前用户名
		getUserName:function(){
			return gameConfigData['username'];
		},

		getDelayTime:function(){
			return 5;
			return Number(gameConfigData['delayTime']);
		},
		getLastGameBallsUrl:function(){
			return '/';
			return gameConfigData['lastGameBallsUrl'];
		},

		//获取所有玩法
		getMethods:function(){
			return gameConfigData['gameMethods'];
		},
		//获取某个玩法
		getMethodById:function(id){
			return methodCache['' + id];
		},
		//获取玩法节点
		getMethodNodeById:function(id){
			return nodeCache['' + id];
		},
		//获取玩法英文名称
		getMethodNameById:function(id){
			var method = this.getMethodById(id);
			return method ? method['name_en'] : '';
		},
		//获取玩法中文名称
		getMethodCnNameById:function(id){
			var method = this.getMethodById(id);
			return method ? method['name_cn'] : '';
		},
		//获取完整的英文名称 wuxing.zhixuan.fushi
		getMethodFullNameById:function(id){
			var method = this.getMethodById(id);
			return method ? method['fullname_en'] : '';
		},
		//获取完整的玩法名称
		getMethodCnFullNameById:function(id){
			var method = this.getMethodById(id);
			return method ? method['fullname_cn'] : '';
		},
		//获取某玩法的单注单价
		getOnePriceById:function(id){
			return Number(this.getMethodById(id)['price']);
		},
		//获取用户行为记录
		getUserBehavior:function(key){
			if(!gameConfigData['coefficient']){
				return;
			}
			if(key){
				return gameConfigData['coefficient'][key];
			}else{
				return gameConfigData['coefficient'];
			}
		},
		getToken:function(){
			return gameConfigData['_token'];
		},
		//用户角色
		getUserRole:function(){
			return gameConfigData['is_agent'] == 1 ? 'agent' : 'user';
		},
		//更新配置，进行深度拷贝
		updateConfig:function(cfg){
			$.extend(true, gameConfigData, cfg);
		},

		//获取页面中输出的最新开奖列表
		//因为不同游戏的开奖球格式有可能不同，因此放在游戏模板中处理
		getHistoryBallsList:function(){
			var me = this,
				arr = $.trim($('#J-textarea-historys-balls-data').text()).split(','),
				i = 0,
				len = arr.length,
				j = 0,
				len2,
				temp,
				balls,
				data = [];

			for(i = 0; i < len; i++){
				temp = arr[i].split('=');
				balls = [];
				for(j = 0,len2 = temp[1].length; j < len2; j++){
					balls[j] = Number(temp[1][j]);
				}
				data[i] = {'number':$.trim(temp[0]), 'balls':balls};
			}
			//data.pop();
			return data;
		}


	};

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;
	Main.getInstance = function(cfg){
		return instance || (instance = new Main(cfg));
	};

	host.Games.K3[name] = Main;

})(bomao, "Config", bomao.Event);



(function(){

	var init = function(config){
			//游戏公共访问对象
		var Games = bomao.Games;
			//游戏实例
			bomao.Games.K3.getInstance({'jsNameSpace': 'bomao.Games.K3.'});
			//游戏玩法切换
			bomao.GameTypes.getInstance();
			//统计实例
			bomao.GameStatistics.getInstance();
			//号码篮实例
			bomao.GameOrder.getInstance();
			//追号实例
			bomao.GameTrace.getInstance();
			//提交
			bomao.GameSubmit.getInstance();
			//消息类
			bomao.Games.K3.Message.getInstance();

			//数字翻牌效果
			//number 当前要设置的数字
			//lastNumebr 上一次的数字
			var flipCard = function(dom, number){
				var numDoms = dom.getElementsByTagName('span'),CLS = 'min-left-anim';
				numDoms[1].innerHTML = number;
				numDoms[3].innerHTML = number;
				dom.className = dom.className.replace(CLS, '') + ' ' + CLS;
				setTimeout(function(){
					numDoms[5].innerHTML = number;
					numDoms[7].innerHTML = number;
					dom.className = dom.className.replace(CLS, '').replace('  ', '');
				}, 800);
			};

			//开将球翻转效果
			var flipBalls = function(lastBalls){
				var doms = $('#J-lottery-balls-lasttime').find('em'),
					time = document.all ? 50 : 100,
					sttime = document.all ? 250 : 1000;

				doms.each(function(i){
					var el = $(this);
					el.text(lastBalls[i]);
					setTimeout(function(){
						el.addClass('flip').text(lastBalls[i]).removeClass().addClass('dice dice-' + lastBalls[i]);
						setTimeout(function(){
							el.removeClass('flip');
						}, sttime);
					}, i * time);
				});
			};



			//更新界面显示内容
			var checkUserTimeout = function(data){
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
				return true;
			};
			//更新开奖列表
			var updateHistorys = function(number, balls){
                var cfg = Games.getCurrentGame().getGameConfig().getInstance(),
                    method = Games.getCurrentGame().getCurrentGameMethod(),
                    historyBalls = cfg.getHistoryBallsList(),
                    result = [],
                    i = 0,
                    len = historyBalls.length;

				//检测奖期号是否已经存在列表中
				for(i = 0; i < len; i++){
					if(number == historyBalls[i]['number']){
						return;
					}
				}

				//将新开奖号和期号加入开奖列表缓存
				historyBalls.pop();
				result.push(number + '=' + balls);
				$.each(historyBalls, function(){
					result.push(this['number'] + '=' + this['balls'].join(''));
				});
				result = result.join(',');
				$('#J-textarea-historys-balls-data').text(result);
				method.miniTrend_updateTrend();
			};
			var oldTimeNumber = [];
			var updateView = function(){
				var cfg = Games.getCurrentGame().getGameConfig().getInstance(),
					time = cfg.getCurrentLastTime(),
					timeNow = cfg.getCurrentTime(),
					surplusTime = time - timeNow,
					vartime = surplusTime,
					timer = null,
					fn,
					currentNumber = '' + cfg.getCurrentGameNumber(),
					lastNumber = '' + cfg.getLastGameNumber(),
					lastBalls = ('' + cfg.getLotteryBalls()).split(''),
					timeDoms = $('#J-deadline-panel').children('em'),
					h = 0,
					m = 0,
					s = 0,
					timeStrArr = [],
					indexArr = [],
					timeStr = '',
					buttonTimeDom = $('#J-button-btn-time'),
					message = Games.getCurrentGameMessage(),
                    //用本地时间差来解决由于js阻塞导致的倒计时延迟的问题
                    jsStartTime = (new Date()).getTime();

				fn = function(){
					var i = 0,len,pageHiddenInput,pageLastBalls = '';
					if(vartime < 0){
						if(timer && timer.stop){
							timer.stop();
						}
						Games.getCurrentGame().getServerDynamicConfig(function(){
							var newCurrentNumber = '' + cfg.getCurrentGameNumber(),
								newLastNumber = '' + cfg.getLastGameNumber(),
								newLastBalls = '' + cfg.getLotteryBalls(),
								timer,
								sNum = 2;

                            //同步倒计时时间
                            //surplusTime = cfg.getCurrentLastTime() - cfg.getCurrentTime();

							//当当前期期号不同时,提示用户期号变化
							if(currentNumber != newCurrentNumber){
								message.showTip('<div class="tipdom-cont">当前已进入第<div class="row" style="color:#F60;font-size:18px;">'+ newCurrentNumber +' 期</div><div class="row">请留意期号变化 (<span id="J-gamenumber-change-s-num">3</span>)</div></div>');
								timer = setInterval(function(){
									$('#J-gamenumber-change-s-num').text(sNum);
									sNum -= 1;
									if(sNum < 0){
										clearInterval(timer);
										message.hideTip();
									}
								}, 1 * 1000);
								if(message && message.tipdom){
									message.tipdom.find('.tipdom-cont').mouseover(function(){
										clearInterval(timer);
										message.hideTip();
									});
								}
								//清空追号数据
								Games.getCurrentGameTrace().autoDeleteTrace();
							}
							//当有新的奖期开出号码时,才更新历史开奖号列表
							if(lastNumber != newLastNumber){
								updateHistorys(newLastNumber, newLastBalls);
							}
						});
						//此处的return防止当剩余时间为 -1 时,界面显示不正确
						return;
					}
					indexArr = [];
					h = Math.floor(vartime/3600);
					m = Math.floor(vartime%3600/60);
					s = vartime%60;

					h = h < 10 ? '0' + h : '' + h;
					m = m < 10 ? '0' + m : '' + m;
					s = s < 10 ? '0' + s : '' + s;
					timeStr = '' + h + m + s;
					timeStrArr = timeStr.split('');
					

					for(i = 0,len = timeStrArr.length; i < len; i++){
						if(timeStrArr[i] != oldTimeNumber[i]){
							indexArr.push(i);
						}
					}
					oldTimeNumber = timeStr.split('');
					for(i = 0, len = indexArr.length; i < len; i++){
						if(document.all){
							timeDoms[indexArr[i]].innerHTML = oldTimeNumber[indexArr[i]];
						}else{
							flipCard(timeDoms[indexArr[i]], oldTimeNumber[indexArr[i]]);
						}
					}

					buttonTimeDom.text(oldTimeNumber[0] + oldTimeNumber[1] + ':' + oldTimeNumber[2] + oldTimeNumber[3] + ':' + oldTimeNumber[4] + oldTimeNumber[5]);

					vartime =  surplusTime - Math.ceil(((new Date()).getTime() - jsStartTime)/1000);

				};

				timer = new bomao.Timer({time:1000, fn:fn});

				$('#J-header-currentNumber').html(currentNumber);
				$('#J-header-newnum').text(cfg.getLastGameNumber());


				var ballsHtml = [];
				$.each(lastBalls, function(){
					ballsHtml.push('<em class="dice dice-'+ this +'"></em>');
				});
                $('#J-lottery-balls-lasttime').html(ballsHtml.join(''));

				//在页面中缓存最新的开奖球
				pageHiddenInput = document.getElementById('J-input-hidden-lastballs');
				pageLastBalls = pageHiddenInput.value;
				if(pageLastBalls != lastBalls.join(',')){
					//翻转动画
					flipBalls(lastBalls);
					pageHiddenInput.value = lastBalls.join(',');
				}





			};


            //当最新的配置信息和新的开奖号码出现后，进行界面更新
            Games.getCurrentGame().addEvent('changeDynamicConfig', function(e, cfg){
            	$('#J-global-token-value').val(cfg['_token']);
                updateView();
            });

			//初次手动更新一次界面
			updateView();



			//玩法菜单区域的高亮处理
			Games.getCurrentGameTypes().addEvent('beforeChange', function(e, id){
				var panel = $('#J-panel-gameTypes'),dom = panel.find('[data-id="'+ id +'"]'),
					li,
					name_cn = Games.getCurrentGame().getGameConfig().getInstance().getMethodCnNameById(id),
					cls = 'current';
				if(dom.size() > 0){
					panel.find('dd').removeClass(cls);
					dom.addClass(cls);
					li = dom.parents('li');
					li.parent().children().removeClass(cls).find('.title span').text('');
					dom.parents('li').addClass(cls);
					li.find('.title span').text('['+ name_cn.substr(0,4) +']').attr('title', name_cn);
				}
			});


			//选球区域的玩法名称显示
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(e, id) {
				var cfg = Games.getCurrentGame().getGameConfig().getInstance(),
					fullname_cn = cfg.getMethodCnFullNameById(id),
					method = Games.getCurrentGame().getCurrentGameMethod(),
					dom = method.container.find('.number-select-link'),
					title = '<span class="method-play-title-name"></span>',
					textDom = dom.find('.method-play-title-name'),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					userPrize = Number(cfg.getConfig('user_prize_group')),
					prize,
					prizes = [],
					unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue());

				if(textDom.size() < 1){
					textDom = $(title);
					textDom.prependTo(dom);
				}
				textDom.text(fullname_cn[0] + ' [ ' + fullname_cn[2] + ' ]');


				//切换玩法后，将倍数归为 1 倍
				Games.getCurrentGameStatistics().getMultipleDom().setValue(1);


				//玩法切换后，修改右侧开奖历史容器顶部高度
				//$('#J-list-historys').show().css('marginTop', $('#J-gametypes-panel-cont').height() * -1 + 10);


				//龙虎玩法启用多级奖金提示
				if(method.getName().indexOf('longhu.') == 0){
					//龙虎
					prizes.push((2 / 0.45 * userPrize / 2000).toFixed(2));
					//和
					prizes.push((2 / 0.1 * userPrize / 2000).toFixed(2));
					$('#J-method-prize').html(prizes.join(' - '));
				}else{
					//更新玩法单注奖金
					prize = Number(methodCfg['prize']) * unit;
					prize = bomao.util.formatMoney(prize);
					prize = prize.split('.');
					prize[1] = '<i>' + prize[1] + '</i>';
					prize = prize.join('.');
					$('#J-method-prize').html(prize);
				}


				//更新玩法说明
				$('#J-panel-method-tip-text').html(methodCfg['bonus_note'] + '<span class="money-text">单注奖金：<i>' + bomao.util.formatMoney(Number(methodCfg['prize'])) + '</i></span>');


				//切换玩法后，生成小走势图
				method.miniTrend_updateTrend();
			});

			//切换菜单模式后，调整右侧历史开奖容器顶部高度
			Games.getCurrentGameTypes().addEvent('afterSwitchMenuStatus', function(e, status){
				if(status == 1){
					//$('#J-list-historys').css('marginTop', 45);
				}
			});



			var loadingMethodTimer;
			//切换玩法(远程加载)时显示锁屏loading
			Games.getCurrentGame().addEvent('beforeSetup', function(e){
				loadingMethodTimer = setTimeout(function(){
					$('#J-mask-page-inner').show();
				}, 1000);
			});
			Games.getCurrentGame().addEvent('afterSetup', function(e){
				clearTimeout(loadingMethodTimer);
				$('#J-mask-page-inner').hide();
			});




			/**
			//玩法规则，中奖说明的tips提示
			var tipRule = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-showrule'});
			$('#J-balls-main-panel').on('mouseover', '.pick-rule, .win-info', function(){
				var el = $(this),
					id = Games.getCurrentGame().getCurrentGameMethod().getId(),
					unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue()),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					prize = Number(methodCfg['prize']) * unit,
					text = [];
				text.push('<div class="row-title">');
					text.push(methodCfg['fullname_cn'].join('-'));
					text.push('<span class="prize">(单注奖金:'+ bomao.util.formatMoney(prize) +')</span>');
				text.push('</div>');
				text.push('<div>'+ methodCfg['bet_note'] +'</div>');
				text.push('<div class="row-line"></div>');
				text.push('<div>'+ methodCfg['bonus_note'] +'</div>');
				tipRule.setText(text.join(''));
				tipRule.show(tipRule.getDom().width()/2 * -1 + el.width()/2, tipRule.getDom().height() * -1 - 30, el);
			});
			$('#J-balls-main-panel').on('mouseleave', '.pick-rule, .win-info', function(){
				tipRule.hide();
			});






			//平铺型菜单玩法规则提示
			var TipRulePanel = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-showrule'});
			$('#J-gametyes-menu-panel').on('mouseover', '.types-item', function(){
				var el = $(this),
					id = Number(el.attr('data-id')),
					unit = Number(Games.getCurrentGameStatistics().getMoneyUnitDom().getValue()),
					methodCfg = Games.getCurrentGame().getGameConfig().getInstance().getMethodById(id),
					prize = Number(methodCfg['prize']) * unit,
					text = [];
				text.push('<div class="row-title">');
					text.push(methodCfg['fullname_cn'].join('-'));
					text.push('<span class="prize">(单注奖金:'+ bomao.util.formatMoney(prize) +')</span>');
				text.push('</div>');
				text.push('<div>'+ methodCfg['bet_note'] +'</div>');
				text.push('<div class="row-line"></div>');
				text.push('<div>'+ methodCfg['bonus_note'] +'</div>');
				TipRulePanel.setText(text.join(''));
				TipRulePanel.show(TipRulePanel.getDom().width()/2 * -1 + el.width()/2, TipRulePanel.getDom().height() * -1 - 30, el);
			});
			$('#J-gametyes-menu-panel').on('mouseleave', '.types-item', function(){
				TipRulePanel.hide();
			});
			**/
		








			//用户行为记录 ==================================
			var userBehavior = new bomao.Behavior();
			(function(){
				var cfg = Games.getCurrentGame().getGameConfig().getInstance(),
					gameid = cfg.getGameId(),
					gameBehavior = cfg.getUserBehavior(),
					historyMethodid,
					keyOpt = {};

				keyOpt['gameid'] = gameid;
				userBehavior.getToken = function(){
					return cfg.getToken();
				};
				//设置存储key
				userBehavior.setKey($.param(keyOpt));
				//自动保存
				userBehavior.autoSave(10 * 1000);


				//玩法历史行为 =========
				if(gameBehavior && gameBehavior['methodid']){
					//用户历史玩法
					historyMethodid = Number(gameBehavior['methodid']);
				}else{
					//后台默认玩法
					historyMethodid = Games.getCurrentGame().getGameConfig().getInstance().getDefaultMethodId();
				}
				//加载初始玩法
				Games.getCurrentGameTypes().addEvent('endShow', function() {
					this.changeMode(historyMethodid);
					userBehavior.setData({'methodid':historyMethodid});
				});
				//改变声音开关;
				if(gameBehavior && gameBehavior['voice']){
                                                                            bomao.voice.voiceOff=gameBehavior['voice'];
                                                                        }else{
                                                                            bomao.voice.voiceOff=0;
                                                                        }
				//圆角模式历史行为 =======
				if(gameBehavior && gameBehavior['moneyunit']){
					Games.getCurrentGameStatistics().moneyUnitDom.setValue(Number(gameBehavior['moneyunit']));
					userBehavior.setData({'moneyunit':Number(gameBehavior['moneyunit'])});
				}else{
					userBehavior.setData({'moneyunit':1});
				}

				//追号历史行为 =========
				if(gameBehavior && (typeof gameBehavior['tracetab'] != 'undefined')){
					Games.getCurrentGameTrace().TraceTab.controlTo(Number(gameBehavior['tracetab']));
					userBehavior.setData({'tracetab':Number(gameBehavior['tracetab'])});
				}else{
					userBehavior.setData({'tracetab':0});
				}


			})();
			//切换玩法后记录行为
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(e, id){
				userBehavior.setData({methodid:id});
			});
			//用户手动切换元角模式记录行为
			Games.getCurrentGameStatistics().moneyUnitDom.addEvent('afterSwitch', function(e, i){
				var unit = Number(this.triggers.eq(i).attr('data-value'));
				userBehavior.setData({moneyunit:unit});
			});
			//切换追号tab后记录行为
			Games.getCurrentGameTrace().TraceTab.addEvent('afterSwitch', function(e, i){
				userBehavior.setData({tracetab:i});
			});
			//用户行为记录 ==================================













			//将选球数据添加到号码篮
			$('#J-add-order').click(function(){
				var result = Games.getCurrentGameStatistics().getResultData();
				if(!result['mid']){
					return;
				}
				Games.getCurrentGameOrder().add(result);
			});

			//快速下单
			$('#J-add-fastorder').click(function(){
				var result = Games.getCurrentGameStatistics().getResultData();
				if(result['type']){
					Games.getCurrentGameOrder().fastOrder(result);
				}
			});

			//根据选球内容更新添加按钮的状态样式
			Games.getCurrentGameStatistics().addEvent('afterUpdate', function(e, num, money){
				var button = $('#J-add-order'),fastbutton = $('#J-add-fastorder'),allinButton = $('#J-button-bet-allin'),
					balance = Number($.trim($('#J-balls-statistics-balance').text().replace(/,/g, '')));
				if(num > 0){
					button.removeClass('disable');
					fastbutton.removeClass('disable');
					if(balance > 0){
						allinButton.removeClass('btn-bet-allin-disable');
					}
				}else{
					button.addClass('disable');
					fastbutton.addClass('disable');
					allinButton.addClass('btn-bet-allin-disable');
				}
			});



			//all in 功能
			$('#J-button-bet-allin').click(function(e){
				var el = $(this),
					unit = 0.01,
					balance = Number($.trim($('#J-balls-statistics-balance').text().replace(/,/g, ''))),
					statcis = Games.getCurrentGameStatistics(),
					data = statcis.getResultData(),
					orderPrice = data['onePrice'] * data['num'] * unit,
					multiple;

				e.preventDefault();
				if(el.hasClass('btn-bet-allin-disable')){
					return;
				}
				multiple = Math.floor(balance/orderPrice);
				if(multiple > 0){
					statcis.getMoneyUnitDom().setValue(unit);
					statcis.getMultipleDom().setValue(multiple);
				}
			});
			//all in 按钮提示
			var allinButtonTip = new bomao.Tip({cls:'j-ui-tip-l'});
			$('#J-button-bet-allin').mouseover(function(){
				var el = $(this);
				if(el.hasClass('btn-bet-allin-disable')){
					allinButtonTip.setText('根据可用余额自动计算您所选号码的最大可投倍数(不超过最高奖金限额）');
					allinButtonTip.show(el.width() + 10, -5, el);
				}
			});
			$('#J-button-bet-allin').mouseleave(function(){
				allinButtonTip.hide();
			});



			/**
			//号码蓝模拟滚动条(该滚动条初始化使用autoReinitialise: true参数也可以达到自动调整的效果，但是是用的定时器检测)
			var gameOrderScroll = $('#J-panel-order-list-cont'),gameOrderScrollAPI;
				gameOrderScroll.jScrollPane();
			gameOrderScrollAPI = gameOrderScroll.data('jsp');
			**/
			//注单提交按钮的禁用和启用
			//当投注内容发生改变时，重新绘制滚动条
			//数字改变闪烁动画
			Games.getCurrentGameOrder().addEvent('afterChangeLotterysNum', function(e, lotteryNum){
				var me = this,subButton = $('#J-submit-order');
				if(lotteryNum > 0){
					subButton.removeClass('btn-bet-disable');
				}else{
					subButton.addClass('btn-bet-disable');
				}
				//gameOrderScrollAPI.reinitialise();
				me.totalLotterysNumDom.addClass('blink');
				me.totalAmountDom.addClass('blink');
				setTimeout(function(){
					me.totalLotterysNumDom.removeClass('blink');
					me.totalAmountDom.removeClass('blink');
				}, 600);
			});


			//清空号码篮
			$('#J-button-clearall').click(function(e){
				e.preventDefault();
				Games.getCurrentGameOrder().reSet().cancelSelectOrder();
				Games.getCurrentGame().getCurrentGameMethod().reSet();
			});


			//单式上传的删除、去重、清除功能
			$('body').on('click', '.remove-error', function(){
				Games.getCurrentGame().getCurrentGameMethod().removeOrderError();
			}).on('click', '.remove-same', function(){
				Games.getCurrentGame().getCurrentGameMethod().removeOrderSame();
			}).on('click', '.remove-all', function(){
				Games.getCurrentGame().getCurrentGameMethod().removeOrderAll();
			});


			//投注按钮操作
			$('body').on('click', '#J-submit-order', function(e){
				if($(this).hasClass('btn-bet-disable')){
					return false;
				}
				Games.getCurrentGameSubmit().submitData();

			});


			//追号区域的显示
			$('#J-trace-switch').click(function(){
				Games.getCurrentGameTrace().show();
			});


			//submit loading
			Games.getCurrentGameSubmit().addEvent('beforeSend', function(e, msg){
				var panel = msg.win.dom.find('.pop-control'),
				comfirmBtn = panel.find('a.confirm'),
				cancelBtn = panel.find('a.cancel');
				comfirmBtn.addClass('btn-disabled');
				comfirmBtn.text('提交中...');
				msg.win.hideCancelButton();

			});
			Games.getCurrentGameSubmit().addEvent('afterSubmit', function(e, msg){
				var panel = msg.win.dom.find('.pop-control'),
				comfirmBtn = panel.find('a.confirm'),
				cancelBtn = panel.find('a.cancel');
				comfirmBtn.removeClass('btn-disabled');
				comfirmBtn.text('确 认');
			});




			//临时隐藏底部dom
			$('.global-footer-cont').hide();
			//临时隐藏回到顶部按钮
			$('#J-global-gototop').remove();





			//初始化投注记录列表tab
			var historysTab = new bomao.Tab({
				par:'#J-list-history-panel',
				triggers:'.title li',
				panels:'.content',
				eventType:'click'
			});






			//侧边消息提示开始
			var sideTip = bomao.SideTip.getInstance();
			//侧边消息提示结束





			//读取投注，追号消息开始 ========================================
			(function(){
				var userRole = Games.getCurrentGame().getGameConfig().getInstance().getUserRole(),
					betsCache = {},
					projectViewBaseUrl = Games.getCurrentGame().getGameConfig().getInstance().getProjectViewBaseUrl(),
					tracesCache = {},
					traceViewBaseUrl = Games.getCurrentGame().getGameConfig().getInstance().getTraceViewBaseUrl(),
					betsTbody = $('#J-tbody-historys-bets'),
					tracesTbody = $('#J-tbody-historys-traces'),
					betsRowTpl = '<tr class="<#=rowclass#>"><td><#=gamename#></td><td><#=method#></td><td><#=number#></td><td><#=prizeballs#></td><td><#=balls#></td><td><#=money#></td><td><#=prize#></td><td><#=commission#></td><td><#=status#></td><td><a target="_blank" href="'+projectViewBaseUrl+'/<#=id#>">详情</a></td></tr>',
					tracesRowTpl = '<tr class="<#=rowclass#>"><td><#=gamename#></td><td><#=method#></td><td><#=startnumber#></td><td><#=progress#></td><td><#=amount#></td><td><#=prize#></td><td><#=iswinstop#></td><td><#=status#></td><td><a target="_blank" href="'+traceViewBaseUrl+'/<#=id#>">详情</a></td></tr>';
				//投注列表部分
				var updateBets = function(data){
					var row,
						strlist = [],
						msgObj,
						i = 0,
						len = data.length;

					for(i = len - 1; i >= 0; i--){
						row = data[i];
						if(betsCache[row['id']]){
							if(betsCache[row['id']]['statuscode'] != row['statuscode']){
								$(formatRowBets(row)).replaceAll(betsTbody.find('.row-data-' + row['id']));
								if(row['statuscode'] == 3){

									strlist.push('<div class="row">');
										strlist.push('恭喜您的');
										strlist.push(' ' + row['gamename'] + ' ');
										strlist.push('(' + row['method'] + ')');
										strlist.push('已中奖');
										strlist.push('<span class="num">');
										strlist.push(bomao.util.formatMoney(Number(row['prize'])));
										strlist.push('</span>');
										strlist.push('元');
									strlist.push('</div>');
									if(bomao.voice.voiceOff==='1'&&i===0){
                                        bomao.voice.zhongjiang();
									}
								}
							}
						}else{
							$(formatRowBets(row)).prependTo(betsTbody);
						}
						betsCache[row['id']] = row;
					}
					if(strlist.length > 0){
						sideTip.setTitle('中奖通知');
						sideTip.setContent(strlist.join(''));
						sideTip.show();
					}
				};
				var formatRowBets = function(row){
					var tpl = betsRowTpl,
						cls = [];

					if(row['balls'].length > 10){
						row['balls'] = row['balls'].substr(0, 10) + '...';
					}
					row['prizeballs'] = !!!row['prizeballs'] ? '' : row['prizeballs'];

					cls.push('row-data-' + row['id']);
					cls.push('row-status-' + row['statuscode']);
					tpl = tpl.replace(/<#=prizeballs#>/g, '<span class="cls-prizeballs">' + row['prizeballs'] + '</span>');
					tpl = tpl.replace(/<#=gamename#>/g, '<span class="cls-gamename">' + row['gamename'] + '</span>');
					tpl = tpl.replace(/<#=number#>/g, '<span class="cls-number">' + row['number'] + '</span>');
					tpl = tpl.replace(/<#=method#>/g, '<span class="cls-method">' + row['method'] + '</span>');
					tpl = tpl.replace(/<#=money#>/g, '<span class="cls-money">' + row['money'] + '</span>');
					tpl = tpl.replace(/<#=prize#>/g, '<span class="cls-prize">' + bomao.util.formatMoney(row['prize']) + '</span>');
					tpl = tpl.replace(/<#=balls#>/g, '<span class="cls-balls">' + row['balls'] + '</span>');
					tpl = tpl.replace(/<#=commission#>/g, '<span class="cls-commission">' + row['commission'] + '</span>');
					tpl = tpl.replace(/<#=status#>/g, '<span class="cls-status">' + row['status'] + '</span>');
					tpl = tpl.replace(/<#=id#>/g, row['id']);
					tpl = tpl.replace(/<#=rowclass#>/g, cls.join(' '));
					return tpl;
				};

				//追号列表部分
				var updateTraces = function(data){
					var has = tracesCache,
						row,
						cacheRow,
						strlist = [],
						msgObj,
						i = 0,
						len = data.length;

					for(i = len - 1; i >= 0; i--){
						row = data[i],cacheRow = has[row['id']];
						if(cacheRow){
							if((cacheRow['statuscode'] != row['statuscode']) || (cacheRow['progress'] != cacheRow['progress'])){
								$(formatRowTraces(row)).replaceAll(tracesTbody.find('.row-data-' + row['id']));
							}
						}else{
							$(formatRowTraces(row)).prependTo(tracesTbody);
							has[row['id']] = row;
						}
					}

				};
				var formatRowTraces = function(row){
					var traceViewBaseUrl = Games.getCurrentGame().getGameConfig().getInstance().getTraceViewBaseUrl();
					var tpl = tracesRowTpl,cls = [];
					cls.push('row-data-' + row['id']);
					tpl = tpl.replace(/<#=gamename#>/g, '<span class="cls-gamename">' + row['gamename'] + '</span>');
					tpl = tpl.replace(/<#=method#>/g, '<span class="cls-method">' + row['method'] + '</span>');
					tpl = tpl.replace(/<#=startnumber#>/g, '<span class="cls-startnumber">' + row['startnumber'] + '</span>');
					tpl = tpl.replace(/<#=progress#>/g, '<span class="cls-progress">' + row['progress'] + '</span>');
					tpl = tpl.replace(/<#=amount#>/g, '<span class="cls-amount">' + bomao.util.formatMoney(row['amount']) + '</span>');
					tpl = tpl.replace(/<#=prize#>/g, '<span class="cls-balls">' + row['prize'] + '</span>');
					tpl = tpl.replace(/<#=iswinstop#>/g, '<span class="cls-iswinstop">' + row['iswinstop'] + '</span>');
					tpl = tpl.replace(/<#=status#>/g, '<span class="cls-status">' + row['status'] + '</span>');
					tpl = tpl.replace(/<#=rowclass#>/g, cls.join(' '));
					tpl = tpl.replace(/<#=id#>/g, row['id']);
					return tpl;
				};

				//消息监听部分
				var MSG = new bomao.Alive({
						url: Games.getCurrentGame().getGameConfig().getInstance().getPollBetInfoUrl(),
						cache:false,
						dataType:'json',
						method:'get',
						looptime:5 * 1000
				});
				MSG.addEvent('afterSuccess', function(e, data){
					var me = this,cfg = me.defConfig;
						if(!checkUserTimeout(data)){
							return;
						}
						if(Number(data['isSuccess']) == 1){
							var results = data['data'];
							$.each(results, function(){
								switch(this['type']){
									case 'bets':
										updateBets(this['data']);
									break;
									case 'traces':
										updateTraces(this['data']);
									break;
									default:
									break;
								}
							});
						}
				});
				MSG.addEvent('afterError', function(e, xhr, type){

				});

				//通过tab切换更改消息请求参数
				historysTab.addEvent('afterSwitch', function(e, i){
					var params = [];
					if(i == 0){
						params.push({'type':'bets'});
					}
					if(i == 1){
						params.push({'type':'traces'});
					}
					MSG.getParams = function(){
						return {'params':params};
					};
				});
				MSG.getParams = function(){
					var params = [{'type':'bets'}, {'type':'traces'}];
					return {'params':params};
				};
				/**
				if(userRole != 'agent'){
					MSG.start();
				}
				**/
				MSG.start();
			})();
			//读取投注，追号消息结束 ========================================




			//读取账户金额开始 ========================================
			var accountCache = {'recharge':{}, 'withdrawals':{}};
			(function(){
				var userRole = Games.getCurrentGame().getGameConfig().getInstance().getUserRole(),
					balanceDoms = $('#J-balls-statistics-balance, #J-user-amount-num, #J-top-user-balance'),balanceCache = 0;
				var updateBalance = function(balance){
					if(balance != balanceCache){
						balanceDoms.text(bomao.util.formatMoney(balance));
						balanceCache = balance;
					}
				};
                var updateRecharge = function(data){
                    var has = accountCache['recharge'],
                        lastId = '' + $.cookie('user-recharge-id'),
                        id = '' + data['id'],
                        num = Number(data['amount']);

                    if(has[id]){
                        return;
                    }
                    if(!!lastId && lastId == id){
                        return;
                    }
                    $.cookie('user-recharge-id', id);
                    sideTip.setTitle('充值到账提醒');
                    sideTip.setContent('<div class="row">您有一笔金额为 <span class="num">' + bomao.util.formatMoney(num) + '</span> 元的充值已到账。</div>');
                    sideTip.show();
                    has[id] = data;
                };
                var updateWithdrawals = function(data){
                    var has = accountCache['withdrawals'],
                        lastId = '' + $.cookie('user-withdrawals-id'),
                        id = '' + data['id'],
                        num = Number(data['amount']);
                    if(has[id]){
                        return;
                    }
                    if(!!lastId && lastId == id){
                        return;
                    }
                    $.cookie('user-withdrawals-id', id);
                    sideTip.setTitle('提现转账提醒');
                    sideTip.setContent('<div class="row">您有一笔金额为 <span class="num">' + bomao.util.formatMoney(num) + '</span> 元的提现已处理完毕，请注意查收。</div>');
                    sideTip.show();
                    has[id] = data;
                };
				//消息监听部分
				var MSG = new bomao.Alive({
						url: Games.getCurrentGame().getGameConfig().getInstance().getPollUserAccountUrl(),
						cache:false,
						dataType:'json',
						method:'get',
						looptime:10 * 1000
				});
				MSG.getParams = function(){
					return {'params':[{'type':'account'}]};
				};
				MSG.addEvent('afterSuccess', function(e, data){
					var me = this,cfg = me.defConfig;
						if(!checkUserTimeout(data)){
							return;
						}
						//updateBalance(2000);
						if(Number(data['isSuccess']) == 1){
							var results = data['data'],list,it;
							$.each(results, function(){
								switch(this['type']){
									case 'account':
										list = this['data'];
										$.each(list, function(){
											it = this;
											switch(it['type']){
												//更新余额
												case 'balance':
													//it['data'] = 34745.12;
													updateBalance(Number(it['data']));
												break;
												//充值到账
												case 'recharge':
													updateRecharge(it['data']);
												break;
												//提现消息
												case 'withdrawals':
													updateWithdrawals(it['data']);
												break;
												default:
												break;
											}
										});
									break;
									default:
									break;
								}
							});
						}
				});
				/**
				if(userRole != 'agent'){
					MSG.start();
				}
				**/
				MSG.start();
			})();
			//读取账户金额结束 ========================================




			//读取最新开奖号码
			(function(){
				var MSG = new bomao.Alive({
						url: Games.getCurrentGame().getGameConfig().getInstance().getUpdateUrl(),
						cache:false,
						dataType:'json',
						method:'get',
						looptime:10 * 1000
				});
				MSG.addEvent('afterSuccess', function(e, data){
					//data['data']['lastNumber'] = '150228038';
					//data['data']['lotteryBalls'] = '97755';
					var cfg = Games.getCurrentGame().getGameConfig().getInstance(),
						lastNumber = '' + cfg.getLastGameNumber(),
						lastBalls = ('' + cfg.getLotteryBalls()).split(''),
						newLastNumber = data['data']['lastNumber'],
						newLotteryBalls = data['data']['lotteryBalls'].split('');

						$('#J-global-token-value').val(data['data']['_token']);

					if(lastNumber != newLastNumber){
						updateHistorys(newLastNumber, newLotteryBalls.join(''));
						var lastBallsDomStr = [];
						$.each(newLotteryBalls, function(i){
							lastBallsDomStr[i] = '<em class="dice dice-'+ this +'">' + this + '</em>';
						});
						$('#J-header-newnum').text(newLastNumber);
						//翻转开奖球效果
						flipBalls(newLotteryBalls);
						document.getElementById('J-input-hidden-lastballs').value = newLotteryBalls.join(',');
						cfg.updateConfig(data['data']);

                        //開獎的提示聲音
                        if(bomao.voice.voiceOff==='1'){
                            bomao.voice.kaijiang();
                        }
                        //新开奖球出现，高亮动画提示有新号开出
						//J-minitrend-trendtable-50
						var methodid = Games.getCurrentGame().getCurrentGameMethod().getId(),
							tr = $('#J-minitrend-trendtable-' + methodid).find('.first');
						tr.addClass('first-blink');
						setTimeout(function(){
							tr.removeClass('first-blink');
						}, 1000);
					}
				});
				MSG.getParams = function(){
					return {};
				};
				MSG.start();
			})();



	};


	$(function(){
		init();
	});


})();