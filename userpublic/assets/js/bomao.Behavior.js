

//用户行为辅助类
(function(host, name, Event, $, undefined){
	var defConfig = {
		key:'',
		getUrl:'/get-cache',
		saveUrl:'/set-cache'
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.key = cfg.key;
			me.token = cfg.token;
			me.saveUrl = cfg.saveUrl;
			me.getUrl = cfg.getUrl;
			me.timer = null;
			me.data = {};
			me.oldData = {};
		},
		autoSave:function(time){
			var me = this,data = me.data;
			me.timer = setInterval(function(){
				me.saveDataToServer();
			}, time);
		},
		setToken:function(token){
			this.token = token;
		},
		getToken:function(){
			return this.token;
		},
		cancelAutoSave:function(){
			var me = this;
			clearInterval(me.timer);
		},
		getKey:function(){
			return this.key;
		},
		setKey:function(key){
			this.key = key;
		},
		getGetUrl:function(){
			return this.getUrl;
		},
		getSaveUrl:function(){
			return this.saveUrl;
		},
		getData:function(prokey){
			var me = this;
			if(prokey){
				return me.data[prokey];
			}
			return this.data;
		},
		setData:function(obj){
			var me = this,data = me.data,
				p;
			for(p in obj){
				if(obj.hasOwnProperty(p)){
					data[p] = obj[p];
				}
			}
		},
		deleteData:function(prokey){
			var me = this;
			if(me.data.hasOwnProperty(prokey)){
				delete me.data[prokey];
			}
		},
		getDataFromServer:function(callback){
			
		},
		addVoice:function () {
			this.data.voice= bomao.voice.voiceOff;
		},
		saveDataToServer:function(callback){
			var me = this,data = me.data,key = me.key;
			var p,proNum = 0,token = me.getToken();
			//添加voice开关到data属性里面
			this.addVoice();
			for(p in data){
				if(data.hasOwnProperty(p)){
					proNum += 1;
				}
			}
			if(proNum < 1){
				return;
			}
			$.ajax({
				url:me.getSaveUrl(),
				dataType:'json',
				method:'POST',
				data:{key:key, _token:token, data:data},
				success:function(data){
					if(Number(data['isSuccess']) == 1){
						if(callback){
							callback.call(me, data);
						}
					}
				}
			});
		}
	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
	host[name] = Main;


})(bomao, "Behavior", bomao.Event, jQuery);








