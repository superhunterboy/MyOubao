(function(host, Event, undefined){

	var defConfig = {
		name:'clock',
		//父类容器
		UIContainer:'',
		//自身游戏容器
		container : '',
		cityName:''
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = $(cfg.UIContainer);
			me.cityName = cfg.cityName
			me.container = $('<div class="clock-box"></div>').appendTo(me.UIContainer);
			me.container.html(html_all.join(''));
		},
		updateCityName:function(){
			var me = this;
			me.container.find('.clock-city-name').html(me.cityName);
		},
		updataLeftTime:function(time_distance){
			var me = this;
			
			if(time_distance>=0){
				// 分
				var int_minute = Math.floor(time_distance/60) 
				time_distance -= int_minute * 60; 
				// 秒 
				var int_second = Math.floor(time_distance) 
				// 时分秒为单数时、前面加零 
	
				if(int_minute < 10){ 
					int_minute = "0" + int_minute; 
				} 
				if(int_second < 10){
					int_second = "0" + int_second;
				} 
				// 显示时间
				me.container.find(".time_m_1").html(String(int_minute).substring(0,1));
				me.container.find(".time_m_2").html(String(int_minute).substring(1,2));
				me.container.find(".time_s_1").html(String(int_second).substring(0,1));
				me.container.find(".time_s_2").html(String(int_second).substring(1,2));

			}else{
				if(time_distance == '---'){
					// 显示时间 
					me.container.find(".time_m_1").html("-");
					me.container.find(".time_m_2").html("-");
					me.container.find(".time_s_1").html("-");
					me.container.find(".time_s_2").html("-");
				}else{
					// 显示时间 
					me.container.find(".time_m_1").html("0");
					me.container.find(".time_m_2").html("0");
					me.container.find(".time_s_1").html("0");
					me.container.find(".time_s_2").html("0");
				}
			
			}
		}
	};

	//html模板
	var html_all = [];

	html_all.push('<span class="clock-city-name"></span>');
	html_all.push('<span class="clock-city-time">');
		html_all.push('<span class="clock-time-txt time_m_1"></span>');
		html_all.push('<span class="clock-time-txt time_m_2"></span>');
		html_all.push('<span class="clock-time-txt time_s_1"></span>');
		html_all.push('<span class="clock-time-txt time_s_2"></span>');
	html_all.push('</span>');

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);