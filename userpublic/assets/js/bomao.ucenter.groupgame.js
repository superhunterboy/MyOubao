

(function($){
	//奖金组套餐缓存
	var groupCacheData = {},
	//自定义奖金组彩种缓存
		customSortCacheData = {},
	//自定义奖金组游戏缓存
		customGameCacheData = {};
	

	//开户类型切换
	var switchHandles = $('#J-user-type-switch-panel').find('a');
	switchHandles.click(function(e){
		var index = switchHandles.index(this),userTypeId = $.trim($(this).attr('data-userTypeId'));
		e.preventDefault();
		switchHandles.removeClass('current');
		switchHandles.eq(index).addClass('current');
		$('#J-input-userType').val(userTypeId);
		loadGroupDataByUserTypeId(userTypeId);
	});

	
	
	
	//固定奖金组和自定义奖金组切换
	var tab = new bomao.Tab({par:'#J-panel-cont', triggers:'.tab-title > li', panels:'.tab-panels > li', eventType:'click'});
	tab.addEvent('afterSwitch', function(e, index){
		$('#J-input-group-type').val(index + 1);
	});

	
	//选择某个奖金组套餐
	$('#J-panel-group').on('click', '.button-selectGroup', function(){
		var el = $(this),groupid = $.trim(el.attr('data-groupid'));
		$('#J-input-groupid').val(groupid);
		$('#J-panel-group').find('li').removeClass('current');
		$('#J-panel-group').find('input[type="button"]').val('选 择')
		el.parent().addClass('current');
		el.val('已选择');
		
	});
	
	
	//游戏彩种选择
	$('#J-group-gametype-panel').on('click', '.item-game', function(e){
		var el = $(this),
			type = $.trim(el.attr('data-itemtype')),
			id = $.trim(el.attr('data-id'));
		selectGameConfig(type, id);
		$('#J-group-gametype-panel').find('li').removeClass('current');
		el.parent().addClass('current');
		e.preventDefault();
	});
	//选择某一个游戏或者彩系进行设置
	var selectGameConfig = function(type, id){
		var data,typeDom = $('#J-input-custom-type'),idDom = $('#J-input-custom-id'),sliderCfg,feedback = [];
		//选择的是彩种
		if(type == 'all'){
			data = getGroupSortConfig(id);
			typeDom.val('1');
		}else{
		//选择的是游戏
			data = getGroupGameConfig(id);
			typeDom.val('2');
		}
		idDom.val(data['id']);
		
		sliderCfg = data['info']
		//生成拖动slider参数
		slider.reSet({
			'minBound':sliderCfg['min'], 
			'maxBound':sliderCfg['max'],
			'step':sliderCfg['step'],
			'value':sliderCfg['bonus']
		});
		
		feedback = getFeedback(sliderCfg['proxyBonus'], sliderCfg['bonus'], sliderCfg['minMethodBonus'], sliderCfg['maxMethodBonus']);
		$('#J-custom-feedback-value').text(feedback[0] + '% - ' + feedback[1] + '%');
		
	};
	//根据参数计算返点率
	var getFeedback = function(proxyBonus, playerBonus, minMethod, maxMethod){
		var arr = [];
		arr.push(((proxyBonus - playerBonus)/maxMethod).toFixed(2));
		arr.push(((proxyBonus - playerBonus)/minMethod).toFixed(2));
		return arr;
	};
	
	//================================================================
	//获取奖金组信息(远程)
	var loadGroupDataByUserTypeId = function(userTypeId){
		$.ajax({
			url:$.trim($('#J-loadGroupData-url').val()) + '?usertype=' + userTypeId,
			cache:false,
			dataType:'json',
			success:function(data){
				if(Number(data['isSuccess']) == 1){
					var defaultGroup = data['data']['defaultGroup'],
						tpl = $('#J-template-group').html(),
						strArr = [];
					
					//奖金组套餐处理
					$.each(defaultGroup, function(i){
						//存入缓存
						groupCacheData[''+this['id']] = this;
						this['feedback'] = this['feedback'] * 100;
						strArr[i] = bomao.util.template(tpl, this);
					});
					$('#J-panel-group').html(strArr.join(''));
					
					
					//自定义奖金组处理
					var gameTypes = data['data']['gameTypes'],
						typesTpl = $('#J-template-gametype').html(),
						gameTpl = $('#J-template-gamesitem').html(),
						gamesArr = [],
						gamesStrArr = [],
						typesStrArr = [];
					
					$.each(gameTypes, function(i){
						customSortCacheData[''+this['id']] = this;
						gamesArr = this['games'];
						gamesStrArr = ['<li class="item-all"><a class="item-game" href="#" data-id="'+ this['id'] +'" data-itemType="all"><span class="name">全部'+ this['name'] +'</span></a></li>'],
						$.each(gamesArr, function(i){
							gamesStrArr.push(bomao.util.template(gameTpl, this));
							customGameCacheData[''+this['id']] = this;
						});
						typesStrArr[i] =  bomao.util.template(typesTpl, {'listloop':gamesStrArr.join('')});
					});
					$('#J-group-gametype-panel').html(typesStrArr.join(''));
					
					
					//初始化一个自定义奖金组
					//默认选择彩系
					if(data['data']['defaultGameType'] == 1){
						selectGameConfig('all', data['data']['defaultGameId']);
						$('#J-group-gametype-panel').find('a[data-id="'+ data['data']['defaultGameId'] +'"]').filter('[data-itemType="all"]').parent().addClass('current');
					}else{
					//默认选择游戏
						selectGameConfig('', data['data']['defaultGameId']);
						$('#J-group-gametype-panel').find('a[data-id="'+ data['data']['defaultGameId'] +'"]').filter('[data-itemType!="all"]').parent().addClass('current');
					}
					
					
				}else{
					alert('加载奖金组信息失败');
				}
			},
			eoror:function(xhr, type, ex){
			}
		});
	};
	//初始加载
	var initLoadData = function(){
		var currentDom = $('#J-user-type-switch-panel').find('.current'),
			userTypeId = $.trim(currentDom.attr('data-userTypeId'));
		loadGroupDataByUserTypeId(userTypeId);
	};
	//获取某个奖金组套餐信息(本地)
	//return {bonus:,feedback:}
	var getGroupDetailById = function(id){
		var id = $.trim(''+id);
		//选择奖金组
		if(typeof id != 'undefined'){
			return groupCacheData[id];
		}else{
		//自定义奖金组
			return {bonus:$.trim($('#J-input-custom-bonus-value').val()), feedback:$.trim($('#J-custom-feedback-value').text())};
		}
	};
	
	
	//获取自定义奖金组的[彩种]配置参数
	var getGroupSortConfig = function(id){
		return customSortCacheData[id];
	};
	//获取自定义奖金组的[游戏]配置参数
	var getGroupGameConfig = function(id){
		return customGameCacheData[id];
	};
	
	
	
	//自定义奖金组设置组件
	var slider = new bomao.SliderBar({
				'minDom':'#J-slider-minDom', 
				'maxDom':'#J-slider-maxDom', 
				'contDom':'#J-slider-cont',
				'handleDom':'#J-slider-handle', 
				'innerDom':'#J-slider-innerbg',
				'minNumDom':'#J-slider-num-min',
				'maxNumDom':'#J-slider-num-max',
				'isUpOnly':true
	});
	slider.addEvent('change', function(){
		var me = this,feedback,data,typeDom = $('#J-input-custom-type'),idDom = $('#J-input-custom-id'),sliderCfg,feedback = [];
		$('#J-input-custom-bonus-value').val(me.getValue());
		if($.trim(typeDom.val()) == '1'){
			data = getGroupSortConfig($.trim(idDom.val()));
		}else{
			data = getGroupGameConfig($.trim(idDom.val()));
		}
		if(!data){
			return;
		}
		feedback = getFeedback(data['info']['proxyBonus'], me.getValue(), data['info']['minMethodBonus'], data['info']['maxMethodBonus']);
		$('#J-custom-feedback-value').text(feedback[0] + '% - ' + feedback[1] + '%');
	});
	
	
	$('#J-input-custom-bonus-value').blur(function(){
		var v = $.trim(this.value),mul = 1,step = 50;
		v = v.replace(/[^\d]/g, '');
		v = Number(v);
		mul = Math.ceil(v/step);
		v = mul * step;
		this.value = v;
		slider.setValue(v);
	}).keyup(function(){
		this.value = this.value.replace(/[^\d]/g, '');
	});
	
	
	

	
	
	initLoadData();
})(jQuery);






