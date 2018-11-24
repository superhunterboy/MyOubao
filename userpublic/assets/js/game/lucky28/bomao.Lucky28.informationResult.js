(function(host, Event, undefined){
	var defConfig = {
		name:'informationresult',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.parentPrize = null;
			
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			//游戏名称
			switch(me.parentPrize.parentGame.name){
				case "重庆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" CQ");break;
				case "黑龙江":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" HLJ");break;
				case "新疆":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" XJ");break;
				case "天津":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" TJ");break;
				case "北京":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" BJ");break;
				case "上海":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" SH");break;
				case "博猫":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Bomao");break;
				case "韩国":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Korea");break;
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Slovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};

			me.container.find('.current-ball').get(0).addEventListener("animationend",function(){
				me.container.find('.current-ball').removeClass('bounceInLow').removeClass('bounceInLowTwo').removeClass('bounceInLowThree');
				me.container.find('.current-ball').removeClass('show-model').addClass('hide-model');
				switch(me.animate_time){
					case 4: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						break;
					case 3: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						break;
					case 2: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-3').removeClass('hide-model').addClass('show-model');
						break;
					default:break;
				}
			});
			me.container.find('.current-ball').get(0).addEventListener("webkitAnimationEnd",function(){
				me.container.find('.current-ball').removeClass('bounceInLow').removeClass('bounceInLowTwo').removeClass('bounceInLowThree');
				me.container.find('.current-ball').removeClass('show-model').addClass('hide-model');
				switch(me.animate_time){
					case 4: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						break;
					case 3: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						break;
					case 2: 
						me.container.find('.num-1').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-2').removeClass('hide-model').addClass('show-model');
						me.container.find('.num-3').removeClass('hide-model').addClass('show-model');
						break;
					default:break;
				}
			});
		},
		//开奖动画
		playAnimation:function(data){
			var me = this;
			me.animate_time = 5;

			me.container.find('.result_box').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.result_lab').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.result_money').children().removeClass('show-model').addClass('hide-model');
			me.container.find('.change_tips').removeClass('show-model').addClass('hide-model');

			var timer = setInterval(function(){
				switch(me.animate_time){
					case 5:
						me.container.find('.lottering-box').removeClass('hide-model').addClass('show-model');
						me.container.find('.current-ball').html(data.num_1);
						me.container.find('.current-total').html(data.num_1);

						me.container.find('.current-ball').addClass('bounceInLow');

						me.updateBetArea(Number(data.num_1));
						break;
					case 4:
						me.container.find('.current-ball').removeClass('hide-model').addClass('show-model');

						me.container.find('.current-ball').html(data.num_2);
						me.container.find('.current-total').html((Number(data.num_1)+Number(data.num_2)));

						me.container.find('.current-ball').addClass('bounceInLowTwo');

						me.updateBetArea((Number(data.num_1)+Number(data.num_2)));
						break;
					case 3:
						me.container.find('.current-ball').removeClass('hide-model').addClass('show-model');

						me.container.find('.current-ball').html(data.num_3);
						me.container.find('.current-total').html((Number(data.num_1)+Number(data.num_2)+Number(data.num_3)));

						me.container.find('.current-ball').addClass('bounceInLowThree');

						me.updateBetArea((Number(data.num_1)+Number(data.num_2)+Number(data.num_3)));
						break;
					case 2:
						me.container.find('.result_box').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_lab').children().removeClass('hide-model').addClass('show-model');

						me.container.find('.lottering-box').removeClass('show-model').addClass('hide-model');
						me.container.find('.current-ball').html('');
						me.container.find('.current-total').html('');
						break;
					case 1:
						me.container.find('.result_box').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_lab').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.result_money').children().removeClass('hide-model').addClass('show-model');
						me.container.find('.change_tips').removeClass('hide-model').addClass('show-model');
						me.container.find('.lottering-box').hide();
						break;
					default:break;
				}

				me.animate_time--;

				if(me.animate_time<=0){
					clearInterval(timer);

					//更新走势图数据、渲染
					me.parentPrize.parentGame.mini_history.updataSourceData(me.parentPrize.resultNumberData);

					me.parentPrize.updataStatus(4);

					setTimeout(function(){

						me.parentPrize.parentGame.autoSwitchPrize(0);
						me.container.find('.change_tips').removeClass('show-model').addClass('hide-model');
						
					} , 5000);
				}
			},1200);
		},
		//更新下注区域效果
		updateBetArea:function(currentTotalNum){
			var me = this;
			//大小、单双
			me.parentPrize.play_zuhe.updateZuheBetArea(Number(currentTotalNum));
			//和值
			me.parentPrize.play_hezhi.updateHezhiBetArea(Number(currentTotalNum));

		},
		//显示开奖结果
		updateResult:function(data){
			var me = this;

			me.container.find('.num-1').html(data.num_1);
			me.container.find('.num-2').html(data.num_2);
			me.container.find('.num-3').html(data.num_3);
			me.container.find('.num-total').html(data.num_total);

			var m_val_1 = 0;//大小结算
			var m_val_2 = 0;//单双结算
			var m_val_3 = 0;//串关结算
			var m_val_4 = 0;//两级结算
			var m_val_5 = 0;//和值

			//大小
			if(data.num_total>13){
				me.container.find('.res-lab-1').html('大');
				m_val_1 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-daxiao').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[0]) - Number(me.parentPrize.play_zuhe.betAmountData[1]);
			}else{
				me.container.find('.res-lab-1').html('小');
				m_val_1 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-daxiao').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[1]) - Number(me.parentPrize.play_zuhe.betAmountData[0]);
			}
			//单双
			if(data.num_total%2==1){
				me.container.find('.res-lab-2').html('、单');
				m_val_2 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-danshuang').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[2]) - Number(me.parentPrize.play_zuhe.betAmountData[3]);
			}else{
				me.container.find('.res-lab-2').html('、双');
				m_val_2 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-danshuang').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[3]) - Number(me.parentPrize.play_zuhe.betAmountData[2]);
			}
			//极大
			if(data.num_total>=22 && data.num_total<=27){
				me.container.find('.res-lab-3').html('、极大');
				m_val_4 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-liangji').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[4]) - Number(me.parentPrize.play_zuhe.betAmountData[5]);
			}else if(data.num_total>=0 && data.num_total<=5){
			//极小
				me.container.find('.res-lab-3').html('、极小');
				m_val_4 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-liangji').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[5]) - Number(me.parentPrize.play_zuhe.betAmountData[4]);
			}else{
			//非极值
				me.container.find('.res-lab-3').html('');
				m_val_4 = -Number(me.parentPrize.play_zuhe.betAmountData[5]) - Number(me.parentPrize.play_zuhe.betAmountData[4]);
			}
			
			//和值
			me.container.find('.lab-total').html(data.num_total);

			//大小结算
			me.container.find('.money-value-1').html((m_val_1>0?'+':'')+m_val_1.toFixed(2));
			if(m_val_1==0){
				me.container.find('.money-value-1').parent().parent().hide();
			}else{
				me.container.find('.money-value-1').parent().parent().show();
				if(m_val_1>0){
					me.container.find('.money-value-1').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-1').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//单双结算
			me.container.find('.money-value-2').html((m_val_2>0?'+':'')+m_val_2.toFixed(2));
			if(m_val_2==0){
				me.container.find('.money-value-2').parent().parent().hide();
			}else{
				me.container.find('.money-value-2').parent().parent().show();
				if(m_val_2>0){
					me.container.find('.money-value-2').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-2').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//两级结算
			me.container.find('.money-value-4').html((m_val_4>0?'+':'')+m_val_4.toFixed(2));
			if(m_val_4==0){
				me.container.find('.money-value-4').parent().parent().hide();
			}else{
				me.container.find('.money-value-4').parent().parent().show();
				if(m_val_4>0){
					me.container.find('.money-value-4').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-4').parent().find('.money-value').addClass('money-value-win');
				}
			}
			//大单、大双、小单、小双
			if(data.num_total>13 && data.num_total%2==1){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-11').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total>13 && data.num_total%2==0){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-10').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total<14 && data.num_total%2==1){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-01').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[8])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[9]);
			}
			if(data.num_total<14 && data.num_total%2==0){
				m_val_3 = (Number(me.parentPrize.container.find('.daxiaodans-bsde-chuanguan-00').html())-1) * Number(me.parentPrize.play_zuhe.betAmountData[9])-
					Number(me.parentPrize.play_zuhe.betAmountData[6])-
					Number(me.parentPrize.play_zuhe.betAmountData[7])-
					Number(me.parentPrize.play_zuhe.betAmountData[8]);
			}
			//串关结算
			me.container.find('.money-value-3').html((m_val_3>0?'+':'')+m_val_3.toFixed(2));
			if(m_val_3==0){
				me.container.find('.money-value-3').parent().parent().hide();
			}else{
				me.container.find('.money-value-3').parent().parent().show();
				if(m_val_3>0){
					me.container.find('.money-value-3').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-3').parent().find('.money-value').addClass('money-value-win');
				}
			}

			//和值结算			
			for(var i in me.parentPrize.play_hezhi.betAmountData){
				var cls = '.tip-'+i;
				if(i == Number(data.num_total)){
					m_val_5 = Number(m_val_5)+(Number(me.parentPrize.container.find(cls).html())-1)*Number(me.parentPrize.play_hezhi.betAmountData[i]);
					if(me.parentPrize.status == 4){
						me.updateBetArea(i);
					}
					
				}else{
					m_val_5 = Number(m_val_5) - Number(me.parentPrize.play_hezhi.betAmountData[i]);
				}
			}
			
			if(m_val_5==0){
				me.container.find('.money-value-5').parent().parent().hide();
			}else{
				me.container.find('.money-value-5').parent().parent().show();
				if(m_val_5>0){
					me.container.find('.money-value-5').parent().find('.money-lab').addClass('money-lab-win');
					me.container.find('.money-value-5').parent().find('.money-value').addClass('money-value-win');
				}
			}

			me.container.find('.money-value-5').html((m_val_5>0?'+':'')+m_val_5.toFixed(2));
		}
	};

	var html_all = [];
	html_all.push('<div class="result_box">');
		html_all.push('<ul class="result_num">');
			html_all.push('<li><span class="number num-1"></span></li>');
			html_all.push('<li><span class="sign">+</span></li>');
			html_all.push('<li><span class="number num-2"></span></li>');
			html_all.push('<li><span class="sign">+</span></li>');
			html_all.push('<li><span class="number num-3"></span></li>');
			html_all.push('<li><span class="sign">=</span></li>');
			html_all.push('<li><span class="num-total"></span></li>');
		html_all.push('</ul>');

		html_all.push('<ul class="result_lab">');
			html_all.push('<li><span class="lab res-lab-1"></span></li>');
			html_all.push('<li><span class="lab res-lab-2"></span></li>');
			html_all.push('<li><span class="lab res-lab-3"></span></li>');
			html_all.push('<li><span class="blank"></span></li>');
			html_all.push('<li><span class="lab">和</span></li>');
			html_all.push('<li><span>:</span></li>');
			html_all.push('<li><span class="lab lab-total"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');
	
	html_all.push('<div class="result_money">');
		html_all.push('<ul>');
			html_all.push('<li><span class="money"><span class="money-lab">大小</span><span class="money-value money-value-1"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">单双</span><span class="money-value money-value-2"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">串关</span><span class="money-value money-value-3"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">两级</span><span class="money-value money-value-4"></span></li>');
			html_all.push('<li><span class="money"><span class="money-lab">和</span><span class="money-value money-value-5"></span></li>');
		html_all.push('</ul>');
	html_all.push('</div>');

	html_all.push('<div class="lottering-box hide-model">');
		html_all.push('<span class="current-ball"></span>');
		html_all.push('<span class="current-total"></span>');
	html_all.push('</div>');
	
	
	html_all.push('<span class="change_tips hide-model">5秒后自动切换</span>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;

})(bomao, bomao.Event);