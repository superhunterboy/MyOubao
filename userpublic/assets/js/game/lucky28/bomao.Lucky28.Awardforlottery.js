(function(host, Event, undefined){
	var defConfig = {
		name:'Awardforlottery',
		container:'',
		UIContainer:'.record-panel',
		initData:null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-kjjg"></div>').appendTo($(me.UIContainer));
			me.initData = cfg.initData;
			// console.log(me.initData);
			me.nameArr = [];
			me.nameId = [];
			// me.container.html(html_all);
			me.initNameArray();
			me.updateGameNameList();
			// console.log(me.nameArr);
		},
		initNameArray:function () {
			var me=this;
			// console.log(me.initData);
			$.each(me.initData , function(i , data){
				// console.log(data);
				me.nameArr.push(data.gameName_cn);
				me.nameId.push(data.gameId);
			});
		},
		updateGameNameList:function(){
			var me =this;
			$.each(me.nameArr,function (i) {
				optionStr+='<option name="lottery_id='+me.nameId[i]+'">'+me.nameArr[i]+'</option>';
			});
			me.container.html(html_part_1+optionStr+html_part_2);

		}

	};

	var optionStr='';

	var html_part_1 =
		'<div class="r-close close-order"></div>'+
		'    <div class="r-main">'+
		'        <div class="main-top">开奖记录</div>'+
		'        <ul class="main-mid">'+
		'            <li>'+
		'                <h5>彩票：</h5>'+
		'                <select id="kjjg-select" class="all-cp">';

	var html_part_2 =
		'                </select>'+
		'            </li>'+
		'            <li class="one">'+
		'                <h5>日期：</h5>'+
		'                <input type="text" readonly id=\'r-kjjg\' value=""/>' +
		'					<i></i>'+
		'            </li>'+
		'            <li>'+
		'                <h5>奖期：</h5>'+
		'                <input name="r-jiangqi" type="text" class="r-kjjq"/>'+
		'            </li>'+
		'            <!--<li>今日</li>-->'+
		'            <!--<li>本周</li>-->'+
		'            <!--<li>近三日</li>-->'+
		'            <!--<li>近半月</li>-->'+
		'            <li class="six"></li>'+
		'        </ul>'+
		'        <div class="main-bot">'+
		'            <div class="bot-l">' +
						'<div class="top">'+
			'                <ul class="title">'+
			'                    <li>期号</li>'+
			'                    <li>开奖时间</li>'+
			'                    <li>结果</li>'+
			'                    <li>和</li>'+
			'                    <li>大小</li>'+
			'                    <li>单双</li>'+
			'                    <li>二极</li>'+

			'                </ul>' +
						'</div>' +
		'				<div class="bottom"><div class="bottom-a"></div>' +
		'				</div>'+
		'            </div>'+
		'        </div>'+
		'</div>';



	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);