//前三直选复式玩法实现类
(function(host, GameMethod, undefined) {
	var defConfig = {
			name: 'renxuandantuo.renxuandantuo.renxuansan',
			//玩法提示
			tips: '',
			//选号实例
			exampleTip: '',
			//限制选求重复次数
			randomBetsNum: 500
		},
		gameCaseName = 'L115',
		//游戏类
		gameCase = host.Games[gameCaseName].getInstance();


	//定义方法
	var pros = {
		init: function(cfg) {
			var me = this;
			//
			me.lastSelectBallIndex = -1;

			//默认加载执行30期遗漏号码
			////me.getHotCold(me.getGameMethodName(), 'currentFre', 'lost');
			//初始化冷热号事件
			//me.initHotColdEvent();
		},
		//复位选球数据
		rebuildData: function() {
			var me = this;
			me.balls = [
				[-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1],
				[-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1]
			];
		},
		buildUI: function() {
			var me = this;
			me.container.html(html_all.join(''));
		},
		//设置选球数据
		//x y value   x y 为选球数据二维数组的坐标 value 为-1 或1
		setBallData: function(x, y, value) {
			var me = this,
				cfgNum = 3,
				data = me.getBallData(),
				xLen = 0;
			if(y == 0){
				return;
			}
			me.fireEvent('beforeSetBallData', x, y, value);



			$.each(data[0], function() {
				if (this > 0) {
					xLen++;
				}
			});
			if (x == 0) {
				if (value == 1) {
					if (xLen > (cfgNum - 2)) {
						me.setBallData(0, me.lastSelectBallIndex, -1);
					}
					me.setBallData(1, y, -1);
					me.lastSelectBallIndex = y;
				}
			}
			if (x == 1) {
				me.setBallData(0, y, -1);
			}



			if (x >= 0 && x < data.length && y >= 0) {
				data[x][y] = value;
			}
			me.fireEvent('afterSetBallData', x, y, value);
		},
		makePostParameter: function(original) {
			var me = this,
				result = [],
				len,
				tempArr = [],
				i = 0;
			$.each(original, function(i) {
				tempArr[i] = [];
				$.each(original[i], function(j) {
					tempArr[i][j] = original[i][j] < 10 ? '0' + original[i][j] : '' + original[i][j];
				});
			});

			len = tempArr.length;
			for (i = 0; i < len; i++) {
				result = result.concat(tempArr[i].join(' '));
			}
			return result.join('|');
		},
		formatViewBalls: function(original) {
			return this.makePostParameter(original);
		},
		//检测选球是否完整，是否能形成有效的投注
		//并设置 isBallsComplete
		checkBallIsComplete: function() {
			var me = this,
				cfgNum = 3,
				ball = me.getBallData(),
				i = 0,
				len = ball[0].length,
				num = 0,
				oNum = 0;

			for (; i < len; i++) {
				if (ball[0][i] > 0) {
					oNum++;
				}
				if (ball[1][i] > 0) {
					num++;
				}
			}
			if (oNum >= 1 && num >= 1 && (oNum + num) >= cfgNum) {
				return me.isBallsComplete = true;
			}

			return me.isBallsComplete = false;
		},
		//获取组合结果
		getLottery: function() {
			var me = this,
				cfgNum = 3,
				ball = me.getBallData(),
				i = 0,
				danMaLen = me.countBallsNumInLine(0),
				len = ball[1].length,
				result = [];
			arr = [],
			danMaArr = [],
			nr = new Array();

			//校验当前的面板
			//获取选中数字
			if (me.checkBallIsComplete()) {
				for (i = 0; i < len; i++) {
					if (ball[1][i] > 0) {
						arr.push(i);
					}
				}

				//存储单号组合
				result = me.combine(arr, cfgNum - danMaLen);
				for (var i = 0, current; i < ball[0].length; i++) {
					if (ball[0][i] == 1) {
						danMaArr.push(i);
					}
				};

				//加上单号各种组合
				for (var s = 0; s < result.length; s++) {

					nr.push(result[s].concat(danMaArr));
				}

				return nr;
			}
			return [];
		},


		//批量选球事件
		exeEvent_batchSetBall: function(param, target) {
			// debugger;
			var me = this,
				ballsData = me.balls,
				x = Number(param['row']),
				bound = param['bound'],
				row = ballsData[x],
				dan = ballsData[0],
				i = 0,
				len = row.length,
				makearr = [],
				start = (typeof param['start'] == 'undefined') ? 0 : Number(param['start']);
			halfLen = Math.ceil((len - start) / 2 + start),
			dom = $(target),
			i = start;

			//清空该行选球
			for (; i < len; i++) {
				//删除第一组球数组
				var ballBan = me.balls[0];
				me.balls[0] = '';
				me.setBallData(x, i, -1);
				me.balls[0] =ballBan;
			}


			switch (bound) {
				case 'all':
					for (i = start; i < len; i++) {
						if(dan[i] !='1'){
						me.setBallData(x, i, (dan[i] == '1'?-1:1));
						}
					}
					break;
				case 'big':
					for (i = halfLen; i < len; i++) {
						if(dan[i] !='1'){
						me.setBallData(x, i, (dan[i] == '1'?-1:1));

						}

					}
					break;
				case 'small':
					for (i = start; i < halfLen; i++) {
						if(dan[i] !='1'){
						me.setBallData(x, i, (dan[i] == '1'?-1:1));

						}
					}
					break;
				case 'odd':
					for (i = start; i < len; i++) {
						if ((i + 1) % 2 != 1) {
							if(dan[i] !='1'){
								me.setBallData(x, i, (dan[i] == '1'?-1:1));
							}
						}
					}
					break;
				case 'even':
					for (i = start; i < len; i++) {
						if ((i + 1) % 2 == 1) {
							if(dan[i] !='1'){
							me.setBallData(x, i, (dan[i] == '1'?-1:1));

							}
						}
					}
					break;
				case 'none':
					for (i = start; i < len; i++) {
						me.setBallData(x, i, -1);
					}
					break;
				default:
					break;
			}

			dom.addClass('current');
			me.updateData();
		}
	};



	//html模板
	var html_head = [];
	//头部
	html_head.push('<div class="number-select-title balls-type-title clearfix"><div class="number-select-link"><a href="#" class="pick-rule">选号规则</a><a href="#" class="win-info">中奖说明</a></div></div>');
	html_head.push('<div class="number-select-content">');
	html_head.push('<ul class="ball-section">');
	//每行
	var html_row = [];
	html_row.push('<li>');
	html_row.push('<div class="ball-title"><strong><#=title#></strong></div>');
	html_row.push('<ul class="ball-content">');
	$.each([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], function(i) {
		if (i == 0) {
			html_row.push('<li style="display:none;"><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + this + '</a></li>');
		} else {
			html_row.push('<li><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + (i < 10 ? '0' + this : this) + '</a></li>');
		}
	});
	html_row.push('</ul>');
	html_row.push('<div class="ball-control"  <#=styleStr#>>');
	html_row.push('<a href="javascript:void(0);" class="circle"></a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=all" class="all">全</a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=big" class="big">大</a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=small" class="small">小</a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=odd" class="odd">奇</a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=even" class="even">偶</a>');
	html_row.push('<a href="javascript:void(0);" data-param="action=batchSetBall&amp;row=<#=row#>&amp;bound=none" class="none">清</a>');
	html_row.push('</div>');
	html_row.push('</li>');

	var html_bottom = [];
	html_bottom.push('</ul>');
	html_bottom.push('</div>');
	//拼接所有
	var html_all = [],
		rowStr = html_row.join('');
	html_all.push(html_head.join(''));
	$.each(['胆码', '拖码'], function(i) {
		if (i == 0) {
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i).replace(/<#=styleStr#>/g, 'style="display:none;"'));
		} else {
			html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i).replace(/<#=styleStr#>/g, ''));
		}

	});
	html_all.push(html_bottom.join(''));



	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
	Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	gameCase.setLoadedHas(defConfig.name, new Main());

})(bomao, bomao.GameMethod);