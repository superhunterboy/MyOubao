(function(host, Event, undefined){
	var defConfig = {
		name:'zuhe',
		parentPrize:null,
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = cfg.parentPrize;
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
			//已下注金额数组
			me.betAmountData = [0,0,0,0,0,0,0,0,0,0];
			me.current_cell_data = null;

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
			// 大：1， 小：0，
			// 单：1， 双：0，
			// 极大：1， 极小：0，
			// 大单：11， 大双：10，小单：01， 小双：00 
			//["286", "284", "285", "286", "282"]

			//大、小、单、双、极大、极小、大单、大双、小单、小双
			me.squareData = [-1,-1,-1,-1,-1,-1,-1,-1,-1,-1];
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));

			me.updateRealyBetButtonDate();
		},
		//恢复初始样式
		reSet:function(){
			var me = this;

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

			var cur_play_id = '';
			var cur_play_type = '';
			var cur_ball = '';
			switch(index){
				case '0' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[0];
					cur_play_type = "daxiaodans.bsde.daxiao";
					break;
				case '1' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[0];
					cur_play_type = "daxiaodans.bsde.daxiao";
					break;
				case '2' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[1];
					cur_play_type = "daxiaodans.bsde.danshuang";
					break;
				case '3' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[1];
					cur_play_type = "daxiaodans.bsde.danshuang";
					break;
				case '4' :
					cur_ball = '1';
					cur_play_id = me.parentPrize.pladIdArray[2];
					cur_play_type = "daxiaodans.bsde.liangji";
					break;
				case '5' : 
					cur_ball = '0';
					cur_play_id = me.parentPrize.pladIdArray[2];
					cur_play_type = "daxiaodans.bsde.liangji";
					break;
				case '6' :
					cur_ball = '11';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '7' : 
					cur_ball = '10';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '8' :
					cur_ball = '01';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				case '9' : 
					cur_ball = '00';
					cur_play_id = me.parentPrize.pladIdArray[3];
					cur_play_type = "daxiaodans.bsde.chuanguan";
					break;
				default : break;
			}

			for(;i<len;i++){
				if(i==index){
					me.squareData[index] = (me.squareData[index] == 0?-1:0);

					if(me.squareData[index] == 0){
						me.current_cell_data = {
							'city':me.parentPrize.parentGame.name,
							'prize_id':me.parentPrize.prize_id,
							'play_id':cur_play_id,
							'play_type':cur_play_type,
							'ball':cur_ball,
							'bet_style':me.container.find('.li-style').eq(index).find('.bet-name-label-normal').html() ,
							'odds':me.container.find('.li-style').eq(index).find('.odds').html(),
							'index':index,
							'extra':me.parentPrize.parentGame.bet_max_amount
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
			me.container.find('.li-style').removeClass('li-style-select');
			for(var i=0;i<data.length;i++){
				if(data[i]==0){
					me.container.find('.li-style').eq(i).removeClass('li-style-select-bet').addClass('li-style-select');
				}

				if(data[i]==-1 && me.betAmountData[i]!=0){
					me.container.find('.li-style').eq(i).addClass('li-style-select-bet');
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
					me.container.find('.li-style').eq(i).addClass('li-style-select-bet');

					if(i<2){
						me.container.find('.li-style').eq(i).children().eq(0).addClass('bet-name-labe-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).addClass('bet-odds-labe-inbet');
					}else{
						me.container.find('.li-style').eq(i).children().eq(0).addClass('bet-name-label-normal-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).addClass('bet-odds-label-normal-inbet');
					}

					me.container.find('.li-style').eq(i).children().eq(2).children().eq(1).html(me.betAmountData[i]);
				}else{
					me.container.find('.li-style').eq(i).removeClass('li-style-select-bet');

					if(i<2){
						me.container.find('.li-style').eq(i).children().eq(0).removeClass('bet-name-labe-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).removeClass('bet-odds-labe-inbet');
					}else{
						me.container.find('.li-style').eq(i).children().eq(0).removeClass('bet-name-label-normal-inbet');
						me.container.find('.li-style').eq(i).children().eq(1).removeClass('bet-odds-label-normal-inbet');
					}
				}
			}
		},
		//开奖时 更新组合下注区域背景
		updateZuheBetArea:function(curTotalNum){
			var me = this;

			me.container.find('.li-style').removeClass('zuhe-active-num');
			me.container.find('.li-style').removeClass('locked-button').addClass('locked-button');
				
			if(curTotalNum>13 && curTotalNum%2==1){
				me.container.find('.li-style').eq(0).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(2).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(6).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>13 && curTotalNum%2==0){
				me.container.find('.li-style').eq(0).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(3).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(7).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum<14 && curTotalNum%2==1){
				me.container.find('.li-style').eq(1).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(2).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(8).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum<14 && curTotalNum%2==0){
				me.container.find('.li-style').eq(1).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(3).removeClass('locked-button').addClass('zuhe-active-num');

				me.container.find('.li-style').eq(9).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>=22 && curTotalNum<=27){
				me.container.find('.li-style').eq(4).removeClass('locked-button').addClass('zuhe-active-num');
			};

			if(curTotalNum>=0 && curTotalNum<=5){
				me.container.find('.li-style').eq(5).removeClass('locked-button').addClass('zuhe-active-num');
			};
		}
	};

	//html模板
	var html_all = [];
	html_all.push('<ul>');
		html_all.push('<li class="li-style li-style-1" param="0">');
			html_all.push('<span class="bet-name-label-normal bet-name-label">大</span>');
			html_all.push('<span class="bet-odds-label">x');
				html_all.push('<span class="odds daxiaodans-bsde-daxiao"></span>');
			html_all.push('</span>');
                                	html_all.push('<span class="bet-value-label-normal bet-value-label">');
                                		html_all.push('<span class="bet-chip-img"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
                                	html_all.push('</span>');
                       	html_all.push('</li>');
                       	html_all.push('<li class="li-style li-style-2" param="1">');
			html_all.push('<span class="bet-name-label-normal bet-name-label">小</span>');
			html_all.push('<span class="bet-odds-label">x');
				html_all.push('<span class="odds daxiaodans-bsde-daxiao"></span>');
			html_all.push('</span>');
                    		html_all.push('<span class="bet-value-label-normal bet-value-label">');
                                		html_all.push('<span class="bet-chip-img"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
                                	html_all.push('</span>');
                       	html_all.push('</li>');
		html_all.push('<li class="li-style" param="2">');
			html_all.push('<span class="bet-name-label-normal">单</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-danshuang"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="3">');
			html_all.push('<span class="bet-name-label-normal">双</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-danshuang"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="4">');
			html_all.push('<span class="bet-name-label-normal">极大</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-liangji"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style li-style-3" param="5">');
			html_all.push('<span class="bet-name-label-normal">极小</span>');
			html_all.push('<span class="bet-odds-label-normal">x');
				html_all.push('<span class="odds daxiaodans-bsde-liangji"></span>');
			html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="6">');
			html_all.push('<span class="bet-name-label-normal">大单</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-11"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="7">');
			html_all.push('<span class="bet-name-label-normal">大双</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-10"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style" param="8">');
			html_all.push('<span class="bet-name-label-normal">小单</span>');
                            	html_all.push('<span class="bet-odds-label-normal">x');
                            		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-01"></span>');
                            	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
		html_all.push('<li class="li-style li-style-3" param="9">');
			html_all.push('<span class="bet-name-label-normal">小双</span>');
                                	html_all.push('<span class="bet-odds-label-normal">x');
                                		html_all.push('<span class="odds daxiaodans-bsde-chuanguan-00"></span>');
                                	html_all.push('</span>');
			html_all.push('<span class="bet-value-label-normal">');
				html_all.push('<span class="bet-chip-img-normal"></span>');
                                		html_all.push('<span class="bet-money-lab"></span>');
			html_all.push('</span>');
		html_all.push('</li>');
	html_all.push('</ul>');
	
	
	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.playlist[defConfig.name] = Main;
})(bomao, bomao.Event);