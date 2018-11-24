//前三直选复式玩法实现类
(function(host, GameMethod, undefined) {
	var defConfig = {
			name: 'sanma.zhixuan.fushi',
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
				data = me.getBallData();
			if(y == 0){
				return;
			}
			me.fireEvent('beforeSetBallData', x, y, value);
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
			$.each(original, function(i){
				tempArr[i]  = [];
				$.each(original[i], function(j){
					tempArr[i][j] = original[i][j] < 10 ? '0' + original[i][j] : '' + original[i][j];
				});
			});

			len = tempArr.length;
			for (i =0; i < len; i++) {
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
				ball = me.getBallData(),
				i = 0,
				len = ball[0].length,
				firstNum = 0,
				secondNum = 0,
				thirdNum = 0;

			for (; i < len; i++) {
				if (ball[0][i] > 0) {
					firstNum++;
				}
				if (ball[1][i] > 0) {
					secondNum++;
				}
				if (ball[2][i] > 0) {
					thirdNum++;
				}
			}
			//二重号大于1 && 单号大于3
			if (firstNum >= 1 && secondNum >= 1 && thirdNum >= 1) {
				return me.isBallsComplete = true;
			}
			return me.isBallsComplete = false;
		},
		//过滤结果集合中的双重号和豹子号
		filterErrorData: function(arr, len, num, saveArray) {
			var me = this,
				saveArray = saveArray || [],
				num = num || 0,
				len = len || arr.length;

			if (num == len) {
				return saveArray;
			} else {
				if (arr[num][0] != arr[num][1] && arr[num][0] != arr[num][2] && arr[num][1] != arr[num][2]) {
					saveArray.push(arr[num]);
				}
				num++;
				return me.filterErrorData(arr, len, num, saveArray);
			}
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
				result = me.filterErrorData(me.combination(result));
				//组合结果
				return result;
			} else {
				return [];
			}
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
	html_row.push('<div class="ball-title"><strong><#=title#>位</strong></div>');
	html_row.push('<ul class="ball-content">');
	$.each([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], function(i) {
		if(i == 0){
			html_row.push('<li style="display:none;"><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + this + '</a></li>');
		}else{
			html_row.push('<li><a class="ball-number" data-param="action=ball&value=' + this + '&row=<#=row#>" href="javascript:void(0);">' + (i < 10 ? '0' + this : this) + '</a></li>');
		}
		
	});
	html_row.push('</ul>');
	html_row.push('<div class="ball-control">');
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
	$.each(['一', '二', '三'], function(i) {
		html_all.push(rowStr.replace(/<#=title#>/g, this).replace(/<#=row#>/g, i));
	});
	html_all.push(html_bottom.join(''));



	//继承GameMethod
	var Main = host.Class(pros, GameMethod);
	Main.defConfig = defConfig;
	//将实例挂在游戏管理器实例上
	gameCase.setLoadedHas(defConfig.name, new Main());

})(bomao, bomao.GameMethod);