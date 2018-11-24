(function(host, Event, undefined){
	var defConfig = {
		name:'informationtimer',
		container:'',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.leftTime = '';
			me.parentPrize = null;
		},
		//复位数据
		rebuildData:function(){
			var me = this;
		},
		//建立UI模型
		buildUI: function(){
			var me = this;
			me.container.html(html_all.join(''));
			me.container.find('.prize_number').html("No."+me.getParentPrize().prize_id);
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
				case "斯洛伐克":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+"S lovakia");break;
				case "土耳其":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Turkey");break;
				case "加拿大":me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name+" Canada");break;
				default:me.container.find('.prize_city_lab').html(me.parentPrize.parentGame.name);break;
			};
		},
		getParentPrize:function(){
			var me = this;
			return me.parentPrize;
		},
		setParentPrzie:function(prize){
			var me = this;
			me.parentPrize = prize;
		},
		setLeftTime:function(leftTime){
			var me = this;
			me.leftTime = leftTime;

			me.old_time = new Date();
			
			if(leftTime<=me.parentPrize.cycleTime){
				me.timer = setInterval(function(){
					me.updateTimer();
				} , 500);

				me.updateTimer();
			}else{
				me.parentPrize.parentGame.showDeadLine('---');
			}
		},
		//更新倒计时
		updateTimer:function(){
			var me = this;
			
			// var new_time = new Date();
			
			var dis_time = ($('#new-service-time').val()-me.old_time.getTime())>0?$('#new-service-time').val()-me.old_time.getTime():0;
			//计算时间差 
			var time_distance = parseInt((me.leftTime*1000-dis_time)/1000);

			if(me.leftTime > 0){
				//计算、显示秒数
				var end_time = '';

				if(time_distance <= 0){
					end_time = "000";
				}else{
					switch(String(time_distance).length){
						case 2 : end_time = "0"+time_distance;break;
						case 1 : end_time = "00"+time_distance;break;
						default : end_time = time_distance;break;
					}
				}

				me.parentPrize.parentGame.showDeadLine(end_time);
			}
			
			
			me.container.find(".deadseconds").html(me.parentPrize.entertainedTime);
			if(parseInt(time_distance)>=(Number(me.parentPrize.entertainedTime)+31)){
				me.container.find(".prize_tips").removeClass().addClass('prize_tips').addClass('prize_tips_hide');
			}else{
				me.container.find(".prize_tips").removeClass().addClass('prize_tips').addClass('prize_tips_show');
				//开奖时间少于40s，进行封盘动作
				if(parseInt(time_distance)<=Number(me.parentPrize.entertainedTime)){
					//状态0 -> 状态1
					if(me.getParentPrize().status == 0){
						me.getParentPrize().status = 1;
						me.getParentPrize().fireEvent("change_prize_status" , me.getParentPrize());
						me.getParentPrize().container.find('bet-panel-mask').show();
					}
					//状态1 -> 状态2
					if(me.getParentPrize().status == 1 && parseInt(time_distance)<=0){
						me.getParentPrize().status = 2;
						me.getParentPrize().fireEvent("change_prize_status" , me.getParentPrize());
					}
					
				}
			}

			if(time_distance>=0){
				// 时
				var int_hour = Math.floor(time_distance/3600) 
				time_distance -= int_hour * 3600; 
				// 分
				var int_minute = Math.floor(time_distance/60) 
				time_distance -= int_minute * 60; 
				// 秒 
				var int_second = Math.floor(time_distance) 
				// 时分秒为单数时、前面加零 
				/*
				if(int_hour < 10){ 
					int_hour = "0" + int_hour; 
				} 
				*/
				if(int_minute < 10){ 
					int_minute = "0" + int_minute; 
				} 
				if(int_second < 10){
					int_second = "0" + int_second; 
				} 
				
				// 显示时间
				me.container.find(".time_h").html(int_hour);
				me.container.find(".time_m").html(int_minute);
				me.container.find(".time_s").html(int_second);

			}else{
				clearInterval(me.timer);

				// 显示时间 
				me.container.find(".time_h").html("00");
				me.container.find(".time_m").html("00");
				me.container.find(".time_s").html("00");
			}
		},
	};

	var html_all = [];
	html_all.push('<ul class="timer_ul">');
		html_all.push('<li><span class="prize_label">即将开奖</span></li>');
		html_all.push('<li><span class="prize_number">No.</span></li>');
		html_all.push('<li>');
			html_all.push('<div class="prize_timer">');
				html_all.push('<span class="time_txt time_h" style="display: none"></span>');
		               	html_all.push(' <span class="time_txt" style="display: none">:</span>');
		                	html_all.push('<span class="time_txt time_m"></span>');
		                	html_all.push('<span class="time_txt">:</span>');
		                	html_all.push('<span class="time_txt time_s"></span>');
			html_all.push('</div>');
		html_all.push('</li>');
		html_all.push('<li><span class="prize_tips">最后<span class="deadseconds"></span>秒截止受注</span></li>');
	html_all.push('</ul>');
	html_all.push('<div class="prize_city"><span class="prize_city_lab"></span></div>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28.list.informationlist[defConfig.name] = Main;
})(bomao, bomao.Event);