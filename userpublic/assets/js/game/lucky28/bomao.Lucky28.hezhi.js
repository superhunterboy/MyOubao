(function(host, Event, undefined){
	var defConfig = {
		name:'hezhi',
		parentPrize:null,
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = cfg.parentPrize;
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
			//已下注金额数组
			me.betAmountData = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
			me.current_cell_data = null;
			me.last_random = -1;

			me.addEvent('after_setSelect', function(e, data){
				me.updateSelect(data);
			});

			me.addEvent('afterReSet', function(){
				me.updateSelect(me.squareData);
			});
		},
		//复位数据
		rebuildData:function(){
			var me = this;
			//0-27
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
		},
		//建立UI模型
		buildUI:function(){
			var me = this;
			me.container.html(html_all.join(''));

			me.updateRealyBetButtonDate();
		},
		//恢复初始样式
		reSet:function(){
			var me = this;
			me.last_random = -1;

			for(var i in me.squareData){
				me.squareData[i] = -1;
			}

			me.fireEvent('afterReSet');
		},
		//判断是否处于活跃状态
		isActivity:function(){
			var me = this;

			for(var i in me.squareData){
				if(me.squareData[i] == 0){
					return true;
					break;
				}
			}

			return false;
		},
		//设置选择项
		completeSelect:function(index){
			var me = this;
			i=0,
			len = me.squareData.length;

			var cur_play_id = me.parentPrize.pladIdArray[4];
			var cur_play_type = 'hezhi.hezhi.hezhi';

			for(;i<len;i++){
				if(i==index){
					me.squareData[index] = (me.squareData[index] == 0?-1:0);

					if(me.squareData[index] == 0){
						me.current_cell_data = {
							'city':me.parentPrize.parentGame.name,
							'prize_id':me.parentPrize.prize_id,
							'play_id':cur_play_id,
							'play_type':cur_play_type,
							'bet_style': '和',
							'ball':me.container.find('li').eq(index).find('.bet-num').html(),
							'odds':me.container.find('li').eq(index).find('.hezhi-odds-tip').children().eq(1).html(),
							'index':index,
							'extra':(me.parentPrize.limite_extra[index]>=me.parentPrize.parentGame.bet_max_amount?me.parentPrize.parentGame.bet_max_amount:me.parentPrize.limite_extra[index])
						}
					}
				}else{
					me.squareData[i] = -1;
				}
			}
			
			me.fireEvent('after_setSelect', me.squareData);
		},
		//更新选中项
		updateSelect:function(data){
			var me = this;
			me.container.find('.bet-num').removeClass('hezhi-li-style-select');
			for(var i=0;i<data.length;i++){
				if(data[i]==0){
					me.container.find('.bet-num').eq(i).removeClass('hezhi-li-style-select-bet').addClass('hezhi-li-style-select');
				}

				if(data[i]==-1 && me.betAmountData[i]!=0){
					me.container.find('.bet-num').eq(i).addClass('hezhi-li-style-select-bet');
				}
			}
		},
		//修改下注金额数组
		updateBetButtonArray:function(index , value){
			var me = this;
			me.betAmountData[index] = Number(me.betAmountData[index])+Number(value);

			me.updateRealyBetButtonDate();
		},
		//更新已下注金额数组
		updateRealyBetButtonDate:function(){
			var me = this;
			for(var i=0;i<me.betAmountData.length;i++){
				if(me.betAmountData[i] > 0){
					me.container.find('.bet-num').eq(i).addClass('hezhi-li-style-select-bet');

					me.container.find('.hezhi-bet-money').eq(i).html("￥"+me.betAmountData[i]+"&nbsp;x&nbsp;");
				}else{
					me.container.find('.bet-num').eq(i).removeClass('hezhi-li-style-select-bet');

					me.container.find('.hezhi-bet-money').eq(i).html("&nbsp;x&nbsp;");
				}
			}
		},
		//开奖时 更新和值下注区域背景
		updateHezhiBetArea:function(curTotalNum){
			var me = this;
			var clsStr = '.bet-num-'+curTotalNum;

			me.container.find('.bet-num').removeClass('hezhi-active-num');
			me.container.find('.bet-num').removeClass('locked-button').addClass('locked-button');

			me.container.find(clsStr).removeClass('locked-button').addClass('hezhi-active-num');
		}

		
	};

	//html模板
	var html_all = [];
	html_all.push('<ul>');
		$.each([0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27],function(){
			html_all.push('<li param='+this+'>');
				html_all.push('<a class="bet-num bet-num-'+this+'">'+this+'</a>');
				html_all.push('<span class="hezhi-odds-tip">');
					html_all.push('<span class="hezhi-bet-money"></span>');
					html_all.push('<span class="tip-'+this+'"></span>');
				html_all.push('</span>');
			html_all.push('</li>');
		});
	html_all.push('</ul>');

	html_all.push('<ul class="hezhi-random-box">');
		html_all.push('<li><span class="odds-explain"><span class="odds-help-logo">?</span>&nbsp;赔率说明</span></li>');
		html_all.push('<li><span class="random-box random-submit">机选</span></li>');
		// html_all.push('<li><span class="random-box random-cancel">取消</span></li>');
	html_all.push('</ul>');
	
	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.playlist[defConfig.name] = Main;
})(bomao, bomao.Event);