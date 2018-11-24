(function(host, Danshi, undefined) {
	var defConfig = {
			name: 'renxuandanshi.renxuandanshi.renxuanba',
			//玩法提示
			tips: '',
			//选号实例
			exampleTip: ''
		},
		gameCaseName = 'L115',
		Games = host.Games,
		//游戏类
		gameCase = host.Games[gameCaseName].getInstance();


	//定义方法
	var pros = {
		init: function(cfg) {
			var me = this;
			//建立编辑器DOM
			//防止绑定事件失败加入定时器
			setTimeout(function() {
				me.initFrame();
			}, 25);
		},
		rebuildData: function() {
			var me = this;
			me.balls = [
				[-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1]
			];
		},
        //检测结果重复
        checkResult: function(data, array) {
            //检查重复
            for (var i = array.length - 1; i >= 0; i--) {
                if (array[i].sort().join('') == data.sort().join('')) {
                    return false;
                }
            };
            return true;
        },
		//检测单注号码是否通过
		checkSingleNum: function(lotteryNum) {
			var me = this,
				isPass = true,
				lotteryNum = lotteryNum.sort(),
				len = lotteryNum.length,
				i = 0;
			if(len != 8){
				return isPass = false;
			}
			
			for(i = 0;i < len;i++){
				if(lotteryNum[i] == lotteryNum[i+1]){
					return isPass = false;
				}
			}

			$.each(lotteryNum, function() {
				if (!me.defConfig.checkNum.test(this)  || Number(this) < 1 || Number(this) > 11) {
					return isPass = false;
				}
			});
			return isPass;
		}
	};

	//继承Danshi
	var Main = host.Class(pros, Danshi);
	Main.defConfig = defConfig;
	//将实例挂在游戏管理器上
	gameCase.setLoadedHas(defConfig.name, new Main());



})(bomao, bomao.Games.L115.Danshi);