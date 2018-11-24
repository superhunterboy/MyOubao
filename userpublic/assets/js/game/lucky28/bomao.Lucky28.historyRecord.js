(function(host, Event, undefined){
	var defConfig = {
		name:'historyRecord',
		container:'',
		UIContainer:'.record-panel',
		initData:null
	};

	var pros = {
		init:function(cfg){
			var me = this;
			me.UIContainer = cfg.UIContainer;
			me.container = $('<div class="r-history"></div>').appendTo($(me.UIContainer));
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
			me.container.html(html_part_1+'<option name="all">所有游戏</option>'+optionStr+html_part_2);

		}

	};

	var optionStr='';

	var html_part_1 = '<div class="r-close close-order"></div>'+
		'    <div class="r-a">'+
		'        <div class="a-1">'+
		'            <h5>历史明细</h5>'+
		'            <span id="r-return"></span>'+
		'        </div>'+
		'        <ul class="a-2">'+
		'            <li class="r-cp">彩票：</li>'+
		'            <li>'+
		'                <select id="all-cp" class="all-cp">';

	var html_part_2 = '                </select>'+
		'            </li>'+
		'            <li>'+
		'                状态:'+
		'            </li>'+
		'            <li>'+
		'               <label class="check" for="r-all"><input type="checkbox" id="r-all" name="series_id=20"/></label>'+
		'                全选'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status0"><input type="checkbox" id="status0" name="status_0=0"/></label>'+
		'                有效投注(未开奖)'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status1"><input type="checkbox" id="status1" name="status_1=1"/></label>'+
		'                已撤单'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status2"><input type="checkbox" id="status2" name="status_2=2"/></label>'+
		'                未中奖'+
		'            </li>'+
		'            <li>'+
		'                <label class="check" for="status3"><input type="checkbox" id="status3" name="status_3=3"/></label>'+
		'                已中奖'+
		'            </li>'+
		'            <li class="last" id="r-search"></li>'+
		'        </ul>'+
		'        <div class="a-3">'+
		'           <ul class="a3-1">'+
		'               <li class="num-1">投注时间</li>'+
		'               <li class="num-3">游戏</li>'+
		'               <li class="num-4">编号</li>'+
		'               <li class="num-5">奖期No.</li>'+
		'               <li class="num-6">投注内容</li>'+
		'               <li class="num-7">投注额</li>'+
		'               <li class="num-8">中奖金额</li>'+
		'               <li class="num-9">状态</li>'+
		'               <li class="num-10">操作</li>'+
		'           </ul>'+
		'            <div class="a3-2">'+
		'                <div class="l">'+
		'                </div>'+
		'            </div>'+
		'        </div>'+
		'</div>';



	var Main = host.Class(pros, Event);
	Main.defConfig = defConfig;

	host.Lucky28[defConfig.name] = Main;
})(bomao, bomao.Event);