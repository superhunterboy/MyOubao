
//侧边提示面板
;(function(host, name, Event, undefined){
	var defConfig = {
		//面板元素dom
		dom:'#J-panel-sidetip',
		//页面主体内容参照元素，面板将以该元素为参照定位
		target:'#J-page-container-main',
		//标题元素
		titleDom:'.sidetip-title-text',
		//主体内容元素
		contentDom:'.sidetip-content',
		//关闭面板元素
		closeDom:'.sidetip-close',
		//是否自动关闭
		autoHideTime:8 * 1000
	},
	instance,
	Games = host.Games;


	var pros = {
		//初始化
		init: function(cfg){
			var me = this;
			me.dom = $(cfg.dom);
			me.targetDom = $(cfg.target);
			me.initEvent();
		},
		initEvent:function(){
			var me = this,cfg = me.defConfig;
			me.dom.on('click', cfg.closeDom, function(e){
				e.preventDefault();
				me.hide(this);
			});
			$(window).resize(function(){
				me.reSetPostion();
			});
		},
		reSetPostion:function(){
			var me = this,dom = me.dom,target = me.targetDom,wd = $(window),
				wd_width = wd.width(),
				left = target.offset().left + target.width() + 20,
				right = wd_width - left - dom.width();
				right = right < 10 ? 10 : right;
			me.dom.css('right', right);
		},
		setTitle:function(html){
			var me = this,cfg = me.defConfig;
			me.dom.find(cfg.titleDom).html(html);
		},
		setContent:function(html){
			var me = this,cfg = me.defConfig;
			$(html).appendTo(me.dom.find(cfg.contentDom));
		},
		show:function(callback){
			var me = this,fn,cfg = me.defConfig;
			clearTimeout(me.autoTimer);
			if(cfg.autoHideTime > 0){
				if(callback){
					fn = function(){
						callback.call(me);
						me.autoHide();
					};
				}else{
					fn = function(){
						me.autoHide();
					};
				}
			}
			me.reSetPostion();
			me.fireEvent('beforeShow', arguments);
			me.dom.slideDown(fn);
			me.fireEvent('afterSHow', arguments);
		},
		hide:function(callback){
			var me = this,cfg = me.defConfig;
			me.fireEvent('beforeHide', arguments);
			me.dom.find(cfg.contentDom).html('');
			me.dom.slideUp(callback);
			me.fireEvent('afterHide', arguments);
		},
		autoHide:function(){
			var me = this,cfg = me.defConfig;
			clearTimeout(me.autoTimer);
			me.autoTimer = setTimeout(function(){
				me.dom.stop();
				me.hide();
			}, cfg.autoHideTime);
		},
		css:function(styles){
			this.dom.css(styles);
		}
	}

	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;
		Main.getInstance = function(cfg){
			return instance || (instance = new Main(cfg));
		};
	host[name] = Main;

})(bomao, "SideTip",  bomao.Event);










