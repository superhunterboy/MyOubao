(function(host, name, Event, undefined){
	var defConfig = {
		name:'DataService',
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.gameServerPathHash = {};

		},
		//获取账号余额
		getUserAccount:function(callback){
			var me = this;
			var url = "/users/user-account-info"+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
		},
		//新增奖期数据
		getGameDataByNumber:function(gameId , callback){
			var me = this;
			var url = '';
			var id = gameId;
			var url = "/bets/load-data/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data['data']);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
			
		},
		//开奖数据
		getPrizeIssueByPrizeID:function(gameId,callback){
			var me = this;
			var url = "/bets/wnnumber-history/"+gameId+"/1"+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			 });
		},
		//所有开奖结果数据
		getAllIssueByGameID:function(gameId , callback){
			var me = this;
			var url = "/bets/wnnumber-history/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me,data);
					}
				},
				error: function(data){
					console.log(data);
				}
			});
		},
		//提交订单
		sumbitOrder:function(gameId,orderData,callback){
			var me = this;
			var url = "/bets/bet/"+gameId;
			var message = new bomao.GameMessage();
			$.ajax({
				type: "post",
				url: url,
				data: orderData,
				dataType: "json",
				beforeSend:function(){
					message.showTip('提交中...');
				},
				success: function(data){
					if(Number(data.isSuccess) != 1){
						message.show(data);
					}
					
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				complete: function(data){
					message.hideTip();
				},
				error:function(data){
					console.log(data);
				}
			});
		},
		//获取订单
		getOrders:function(gameId,callback){
			var me = this;
			var url = "/bets/bet-info/"+gameId+'?time='+new Date();
			$.ajax({
				type: "get",
				url: url,
				dataType: "json",
				success: function(data){
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				error:function(data){
					console.log(data);
				}
			});
		},
		//取消订单
		cancelOrder:function(orderIdArr , issue , lottery_id , token , callback){
			var me=this;
			var url = '/projects/drop-multi-projects';
			var message = new bomao.GameMessage();
			$.ajax({
				type:'post',
				url:url,
				data:{lottery_id:lottery_id , issue:issue , _token : token,project_ids : orderIdArr},
				dataType: "json",
				beforeSend:function(){
					message.showTip('撤单中...');
				},
				success: function(data){
					message.show(data);
					if($.isFunction(callback)){
						callback.call(me , data);
					}
				},
				complete: function(data){
					message.hideTip();
				},
				error:function(data){
					console.log(data);
				}
			});
		}
	};

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;

	host.Lucky28[name] = Main;

})(bomao,"DataService", bomao.Event);