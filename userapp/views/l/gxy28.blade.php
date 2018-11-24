@extends('l.base')


@section ('styles')

@parent
{{ style('animate') }}
{{ style('lucky28')}}
{{ style('daterangepicker')}}
{{ style('r-bootstrap')}}

@stop


@section ('container')
    @include('w.header')



    
    @section ('centerGame')
    @show
    


    @include('w.footer')



    <style type="text/css">
    .panel-test {
    	position: fixed;
    	left: 50px;
    	top: 150px;
    	background: #CCC;
		width: 2450px;
    	height: 300px;
    	overflow: hidden;
    	z-index: 10000;
    	display: none;
    }
    .panel-test .inner {
    	height: 100%;
    	width: 10000px;
    	position: relative;
    }
    .panel-test .it {
    	left: 0;
    	top: 0;
    	width: 49px;
    	height: 49px;
    	border: 1px solid #999;
    	border-left: none;
    	border-top: none;
    	text-align: center;
    	line-height: 48px;
    	position: absolute;
    	color: #CCC;
    }
    .panel-test .it i {
    	background: red;
    	display:inline-block;
    	width: 100%;
    	height: 100%;
    	border-radius: 100%;
    	color: #FFF;
    	font-style: normal;
    	font-size: 14px;
    }
    .panel-test .it i.even {
    	background: blue;
    }
    </style>

    <div class="panel-test">
    	<div class="inner" id="J-panel-test"></div>
    </div>

@stop




@section('end')
@parent
<script type="text/javascript">
(function($){
	var panelDom = $('#J-panel-test');
	var Trend = function(){
		var me = this;
		
		me.rownum = 6;
		me.width = 50;
		me.height = 50;

		me.build();
	};
	Trend.prototype = {
		add:function(num){
			var me = this,
				v = me.getValueFromNum(num),
				last = me.lastItem,
				nextx = last.x,
				nexty = last.y + 1;

			if(nextx > 48){
				me.build();
				return;
			}

			if(me.isfirst){
				nextx = 0;
				nexty = 0;
				me.isfirst = false;
			}else{
				//与上次相同
				if(v == last['v']){
					//需要拐弯
					if(me.posHash[nextx + ',' + nexty] || nexty == me.rownum){
						nextx += 1;
						nexty -= 1;
						me.datas[nextx][nexty]['turnx'] = last['turnx'] + 1;
					}else{
						//受前一列影响,需要拐弯
						if(last['turnx'] > 0){
							nextx += 1;
							nexty -= 1;
							me.datas[nextx][nexty]['turnx'] = last['turnx'] + 1;
						}
					}
				}else{
					nextx = nextx - last['turnx'] + 1;
					nexty = 0;
				}
			}
			me.posHash[nextx + ',' + nexty] = true;
			me.lastItem = me.datas[nextx][nexty];
			me.datas[nextx][nexty]['v'] = v;
			me.getPanel().find('.it-'+nextx+'-'+nexty).html(v);
		},
		//由号码解析出显示值
		getValueFromNum:function(num){
			return num%2 ==  0 ? '<i class="even">闲</i>' : '<i class="odd">庄</i>';
		},
		getPanel:function(){
			return panelDom;
		},
		build:function(){
			var me = this,
				i = 0,
				cellnum = 50,
				j = 0;

			me.clear();

			me.isfirst = true;
			me.doms = [];
			me.datas = [];
			var htmls = [];
			for(i = 0; i < cellnum; i++){
				me.datas[i] = [];
				for(j = 0; j < me.rownum; j++){
					me.datas[i][j] = {x:i, y:j, v:'', turnx:0};
					htmls.push('<div class="it it-'+i+ '-'+ j +'" style="left:'+ (i * me.width) +'px;top:'+ (j * me.height) +'px;">'+(i+','+j)+'</div>');
				}
			}
			me.lastItem = me.datas[0][0];
			me.posHash = {};
			$(htmls.join('')).appendTo(me.getPanel());
		},
		clear:function(){
			var me = this;
			me.getPanel().html('');
		},
	};


	var trend = new Trend(),
		timer,
		times = 0;
	return;
	timer = setInterval(function(){
		trend.add(bomao.util.getRandom(0, 27));

		if(times > 500){
			clearInterval(timer);
		}
		times++;
	}, 250);


})(jQuery);
</script>


	{{ script('game-all') }}
@stop


