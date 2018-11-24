(function(host, Event, undefined){
	var defConfig = {
		name:'resultRecord',
		container:'',
		UIContainer:'.record-panel'
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-allday"></div>').appendTo($(me.UIContainer));
			me.container.html(html_all);
	// console.log("xxx");
		}
	};

	var html_all='';
	html_all+= '<div class="r-close close-order"></div>'+
		'    <div class="r-main">'+
		'        <div class="main-top">历史记录（日汇总）</div>'+
		'<ul class="main-mid">'+
		'    <li class="mid-1">日期：</li>'+
		'    <li class="mid-2">'+
		'        <input type="text" readonly id="r-allday"><i></i>'+
		'    </li>'+
		'    <li class="mid-3">至</li>'+
		'    <li class="mid-4">'+
		'        <input type="text" readonly id="r-alldayto"/>'+
		'    </li>'+
		'    <li class="mid-5" id="today">今日</li>'+
		'    <li class="mid-6" id="week">本周</li>'+
		'    <li class="mid-7" id="3day">近三日</li>'+
		'    <li class="mid-8" id="hmonth">近半月</li>'+
		'    <li class="six">'+
		'    </li>'+
		'</ul>'+
		'        <div class="main-bot">'+
		'            <div class="bot-l"><div class="top">'+
		'                <ul class="title">'+
		'                    <li class="num-1">投注日期</li>'+
		'                    <li class="num-2">有效投注</li>'+
		'                    <li class="num-3">派奖金额</li>'+
		'                    <li class="num-4">盈亏金额</li>'+
		'                    <li class="num-5">操作</li>'+
		'                </ul></div><div class="bottom"> </div>'+
		'            </div>'+
		'        </div>'+
		'</div>';

	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;

})(bomao, bomao.Event);