

(function(host, name, Event, undefined){
	var defConfig = {
		//主面板dom
		panel:'#J-panel-gameTypes'
	},
	//渲染实例
	instance,
	//游戏实例
	Games = host.Games;
	
	//渲染方法
	var pros = {
		init:function(cfg){
			var me = this;
			//缓存方法
			Games.setCurrentGameTypes(me);
			me.container = $(cfg.panel);
			//玩法数据
			me.data = Games.getCurrentGame().getGameConfig().getInstance().getMethods();
			me.buildDom();
			me.initEvent();


			//联动玩法面板的切换
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(obj, id){
				me.changePanel(id);
			});

		},
		buildDom:function(){
			var me = this,it,it2,it3,strArr = [],strArrAll = [],modeArr = [];
			$.each(me.data, function(){
				strArr = [];
				it = this;
				strArr.push('<li class="gametypes-menu-'+ it['name_en'] +'">');
					strArr.push('<div class="title">');
						strArr.push(it['name_cn']);
						modeArr[0] = it['name_en'];
						strArr.push('<span></span>');
					strArr.push('</div>');
					strArr.push('<div class="content clearfix">');
						strArr.push('<div class="sj"></div>');
					
					$.each(it['children'], function(){
						it2 = this;
						modeArr[1] = it2['name_en'];
						strArr.push('<dl>');
							strArr.push('<dd class="types-node types-node-'+ it2['name_en'] +'">'+ it2['name_cn'] +'</dd>');
							$.each(it2['children'], function(){
								it3 = this;
								modeArr[2] = it3['name_en'];
								strArr.push('<dd class="types-item" data-id="'+ it3['id'] +'">'+ it3['name_cn'] +'</dd>');
							});
						strArr.push('</dl>');
					});
					strArr.push('</div>');
				strArr.push('</li>');
				
				strArrAll.push(strArr.join(''));
			});
			me.getContainerDom().html(strArrAll.join(''));



			//构建平板面板菜单
			me.buildPanelMenu();


			
			setTimeout(function(){
				me.fireEvent('endShow');
			}, 20);
			
		},
		buildPanelMenu:function(){
			var me = this,it,it2,it3,strArr = [],strArrAll = [],modeArr = [],
				panelDom = me.getPanelDom();
			$.each(me.data, function(){
				strArr = [];
				it = this;
				strArr.push('<li class="gametypes-sort gametypes-menu-'+ it['name_en'] +'">');
					/**
					strArr.push('<div class="title">');
						strArr.push(it['name_cn']);
						modeArr[0] = it['name_en'];
						strArr.push('<span></span>');
					strArr.push('</div>');
					**/
					strArr.push('<div class="content clearfix">');
					
					$.each(it['children'], function(){
						it2 = this;
						modeArr[1] = it2['name_en'];
						strArr.push('<dl>');
							strArr.push('<dd class="types-node types-node-'+ it2['name_en'] +'">'+ it2['name_cn'] +'</dd>');
							$.each(it2['children'], function(){
								it3 = this;
								modeArr[2] = it3['name_en'];
								strArr.push('<dd class="types-item" data-id="'+ it3['id'] +'">'+ it3['name_cn'] +'</dd>');
							});
						strArr.push('</dl>');
					});
					strArr.push('</div>');
				strArr.push('</li>');
				
				strArrAll.push(strArr.join(''));
			});
			panelDom.html(strArrAll.join(''));
		},
		initEvent:function(){
			var me = this;
			me.container.on('click', '.types-item', function(){
				var el = $(this),id = el.attr('data-id');
				if(id){
					me.changeMode(id, el);
				}
			});


			me.getPanelDom().on('click', '.types-item', function() {
				var el = $(this),id = el.attr('data-id');
				if(id){
					me.changeMode(id, el);
				}
			});
			Games.getCurrentGame().addEvent('afterSwitchGameMethod', function(obj, id){
				//只有当开启全展模式的时候事件才有效
				if(!me.getContainerDom().parent().hasClass('play-select-status-b')){
						return;
				}
				//显示玩法高亮
				var el = me.getPanelDom().find('dd[data-id=' + id + ']'),CLS = 'types-item-current',CLS2 = 'gametypes-sort-current';
				me.getPanelDom().find('.types-item').removeClass(CLS);
				el.addClass(CLS);
				//显示大面板
				me.getPanelDom().children().removeClass(CLS2);
				el.parents('.gametypes-sort').addClass(CLS2);

				Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').hide();
			});

			//大玩法群切换
			me.getContainerDom().find('li > .title').on('click', function() {
				var el = $(this),
					parent = el.parent(),
					lis = parent.parent().children(),
					index = lis.index(parent.get(0)),
					panel = me.getPanelDom().find('.gametypes-sort').eq(index),
					dom = panel.find('.types-item').eq(0),
					id = dom.attr('data-id');
					//只有当开启全展模式的时候事件才有效
					if(me.getContainerDom().parent().hasClass('play-select-status-b')){
						me.changeMode(id, dom);
					}
			});

			me.initMenuStatusEvent();
			

		},
		//菜单模式切换
		initMenuStatusEvent:function(){
			var me = this;
			$('#J-menu-status-control').click(function(e) {
				var el = $(this),CLS = 'menu-control-current';
				e.preventDefault();

				//切换到鼠标划过模式
				if(el.hasClass(CLS)){
					me.fireEvent('beforeSwitchMenuStatus', 1);
					el.removeClass(CLS);
					me.getContainerDom().parent().removeClass('play-select-status-b');
					me.getPanelDom().find('.gametypes-sort-current').removeClass('gametypes-sort-current');
					Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').show();
					me.fireEvent('afterSwitchMenuStatus', 1);
				}else{
				//切换到平板模式
					me.fireEvent('beforeSwitchMenuStatus', 0);
					el.addClass(CLS);
					me.getContainerDom().parent().addClass('play-select-status-b');
					me.changeMode(Games.getCurrentGame().getCurrentGameMethod().getId());
					Games.getCurrentGame().getCurrentGameMethod().container.find('.number-select-link').hide();
					me.fireEvent('afterSwitchMenuStatus', 0);
				}
			});
		},
		//获取外部容器DOM
		getContainerDom: function(){
			return this.container;
		},
		getPanelDom:function(){
			return this.panelDom || (this.panelDom = $('#J-gametyes-menu-panel'));
		},
		//切换事件
		changeMode: function(mode, el){
			var me = this,
				container = me.getContainerDom();
			
			//执行自定义事件
			me.fireEvent('beforeChange', mode);
			try{
				if(mode == Games.getCurrentGame().getCurrentGameMethod().getGameMethodName()){
					return;
				}
			}catch(e){
			}
			//执行切换
			Games.getCurrentGame().switchGameMethod(mode);
		},
		//切换
		changePanel:function() {
			
		}
	};
	
	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;
	
})(bomao, "GameTypes", bomao.Event);










