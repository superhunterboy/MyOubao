
//软键盘，虚拟键盘
(function(host, name, Event, $, undefined){
	var util = host.util,
	defConfig = {
		//实例化时追加的最外层样式名
		cls:'',
		target:'body',
		inputTag:'',
		effectShow:function(){
			this.dom.show();
		},
		effectHide:function(){
			this.dom.hide();
		},
		change:function (sort) {
	        sort=sort.sort(function(){
	            return Math.random()-0.5
	        });
	        return sort;
	    },
		zIndex:1001,
		//是否使用fixed模式
		isFixed:false,
		//顺序排列
		'isQueue':false
	},
	doc = $(document),
	instance;


	var pros = {
		init:function(cfg){
			var me = this,
			position = cfg.isFixed ? 'fixed' : 'absolute';
			me.isQueue = cfg.isQueue;
			me.inputTag = cfg.inputTag;

			me.effectShow = cfg.effectShow;
			me.effectHide = cfg.effectHide;
		},
		show:function(x, y, target){
			var me = this;
			me.controlTag = $(target);
			targetPos = (target == undefined ? $(me.defConfig.target) : $(target)).offset();

			if($("#globe-keyboard")){
				$("#globe-keyboard").remove();
			}

			updateKey(me.isQueue);
			me.dom = $(html_all.join('')).appendTo('body');

			var inputTxt = me.inputTag;
			//选中的INPUT元素，取消键盘输入
			inputTxt.bind('keydown',function(event){event.preventDefault;},false);

			me.dom.css({'left':targetPos.left + x,'top':targetPos.top + y});
			me.effectShow();


			shift = false,
			capslock = false;

			me.dom.on('click', 'li', function(){
				var character = $(this).html();
				
				// Shift keys
				if ($(this).hasClass('right-shift')) {
					$(this).toggleClass('shift-active');

					$('.letter').toggleClass('uppercase');
					$('.symbol span').toggle();
					
					shift = true;
					return false;
				}
				
				// Caps lock
				if ($(this).hasClass('capslock')) {
					if($('.right-shift').hasClass('shift-active')){
						$('.right-shift').removeClass('shift-active');
						$('.symbol span').toggle();
						$('.letter').toggleClass('uppercase');
					}

					$(this).toggleClass('caps-active');

					$('.letter').toggleClass('uppercase');
					capslock = (capslock==true) ? false : true;
					shift = false;
					return false;
				}

				// Delete
				if ($(this).hasClass('delete')) {
					var html = inputTxt.val();
					
					inputTxt.val(html.substr(0, html.length - 1));
					return false;
				}
				
				// Special characters
				if ($(this).hasClass('symbol')) character = $('span:visible', $(this)).text();
				if ($(this).hasClass('space')) character = ' ';
				
				// Uppercase letter
				if ($(this).hasClass('uppercase')) character = character.toUpperCase();

				//清空
				if ($(this).hasClass('cancel')){
					return false;
				}

				//确定
				if ($(this).hasClass('submit')){
					return false;
				}
				
				// Remove shift once a key is clicked.
				if (shift === true) {
					$('.symbol span').toggle();
					$('.right-shift').removeClass('shift-active');
					$('.letter').toggleClass('uppercase');

					if (capslock === true && shift == false) $('.letter').toggleClass('uppercase');
					
					shift = false;
				}

				inputTxt.val(inputTxt.val() + character);

			}).mouseup(function(event) {
				inputTxt.focus();
			});

			me.dom.on('click', '.cancel', function(){
				inputTxt.val('');
			});

			me.dom.on('click', '.submit', function(){
				me.hide();
				$(me.controlTag).removeClass('key-active');
			});
		},
		hide:function(){
			var me = this;
			me.effectHide();

			var inputTxt = me.inputTag;
			//解绑键盘
			inputTxt.unbind('keydown');

			if($("#globe-keyboard")){
				$("#globe-keyboard").remove();
			}
		}
	};

	var numbersArr = ['1','2','3','4','5','6','7','8','9','0'];
	var letterArr1 = ['q','w','e','r','t','y','u','i','o','p'];
	var letterArr2 = ['a','s','d','f','g','h','j','k','l'];
	var letterArr3 = ['z','x','c','v','b','n','m'];

	var html_all = [];

	/*键盘按键更新顺序*/
	function updateKey(flag){
		html_all = [];

		if(!flag){
			numbersArr = defConfig.change(numbersArr);
			letterArr1 = defConfig.change(letterArr1);
			letterArr2 = defConfig.change(letterArr2);
			letterArr3 = defConfig.change(letterArr3);
		}

		html_all.push('<ul id="globe-keyboard">');
			html_all.push('<li class="symbol"><span class="off">`</span><span class="on">~</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[0]+'</span><span class="on">!</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[1]+'</span><span class="on">@</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[2]+'</span><span class="on">#</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[3]+'</span><span class="on">$</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[4]+'</span><span class="on">%</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[5]+'</span><span class="on">^</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[6]+'</span><span class="on">&amp;</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[7]+'</span><span class="on">*</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[8]+'</span><span class="on">(</span></li>');
			html_all.push('<li class="symbol"><span class="off" class="num">'+numbersArr[9]+'</span><span class="on">)</span></li>');
			html_all.push('<li class="symbol"><span class="off">-</span><span class="on">_</span></li>');
			html_all.push('<li class="symbol"><span class="off">=</span><span class="on">+</span></li>');

			html_all.push('<li class="letter row-2">'+letterArr1[0]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[1]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[2]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[3]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[4]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[5]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[6]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[7]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[8]+'</li>');
			html_all.push('<li class="letter">'+letterArr1[9]+'</li>');
			html_all.push('<li class="symbol"><span class="off">[</span><span class="on">{</span></li>');
			html_all.push('<li class="symbol"><span class="off">]</span><span class="on">}</span></li>');
			html_all.push('<li class="symbol lastitem"><span class="off">\\</span><span class="on">|</span></li>');

			html_all.push('<li class="letter row-3">'+letterArr2[0]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[1]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[2]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[3]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[4]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[5]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[6]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[7]+'</li>');
			html_all.push('<li class="letter">'+letterArr2[8]+'</li>');
			html_all.push('<li class="symbol"><span class="off">;</span><span class="on">:</span></li>');
			html_all.push("<li class='symbol'><span class='off'>'</span><span class='on'>&quot;</span></li>");
			html_all.push('<li class="capslock">caps lock</li>')

			html_all.push('<li class="letter row-4">'+letterArr3[0]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[1]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[2]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[3]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[4]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[5]+'</li>');
			html_all.push('<li class="letter">'+letterArr3[6]+'</li>');
			html_all.push('<li class="symbol"><span class="off">,</span><span class="on">&lt;</span></li>');
			html_all.push('<li class="symbol"><span class="off">.</span><span class="on">&gt;</span></li>');
			html_all.push('<li class="symbol"><span class="off">/</span><span class="on">?</span></li>');
			html_all.push('<li class="right-shift lastitem">shift</li>');
			html_all.push('<li class="delete lastitem">delete</li>');

			html_all.push('<li class="space lastitem">&nbsp;</li>');
			html_all.push('<li class="cancel lastitem">清空</li>');
			html_all.push('<li class="submit lastitem">确定</li>');
		html_all.push('</ul>');
	};
	
	var Main = host.Class(pros, Event);
		Main.defConfig = defConfig;

	//可生成多个实例
	host[name] = Main;
	//也可以重复使用实例
	host[name].getInstance = function(){
		return instance || (instance = new Main(defConfig));
	};

})(bomao, "Keyboard", bomao.Event, jQuery);