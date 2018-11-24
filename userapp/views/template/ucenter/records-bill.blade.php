@extends('l.home')

@section('title')
            账变记录
@parent
@stop

@section('scripts')
@parent
    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
    {{ script('datePicker')}}
@stop

@section ('main')
<div class="nav-bg nav-bg-tab">
            <div class="title-normal">
                资金明细
            </div>
            <ul class="tab-title">
                <li class="current"><a href="records-bill.php"><span>账变记录</span></a></li>
                <li><a href="records-recharge.php"><span>我的充值</span></a></li>
                <li><a href="records-withdrawal.php"><span>我的提现</span></a></li>
            </ul>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    游戏时间：<input id="J-date-start" class="input w-3" type="text" value="2014-06-10  00:00:00" /> 至 <input id="J-date-end" class="input w-3" type="text" value="2014-06-11  00:00:00" />
                    &nbsp;&nbsp;
                    <select id="J-select-issue" style="display:none;">
                        <option value="" selected="selected">账变编号</option>
                        <option value="1">注单编号</option>
                        <option value="2">追号编号</option>
                        <option value="3">奖期编号</option>
                    </select>
                    <input class="input w-3" type="text" value="" />
                </p>
                <p class="row">
                    游戏名称：<select id="J-select-game-type" style="display:none;">
                            <option value="0" selected="selected">所有游戏</option>
                            <option value="1">重庆时时彩</option>
                            <option value="2">江西时时彩</option>
                            <option value="3">黑龙江时时彩</option>
                            <option value="4">新疆时时彩</option>
                            <option value="5">上海时时乐</option>
                            <option value="6">乐利时时彩</option>
                            <option value="7">天津时时彩</option>
                            <option value="8">吉利分分彩</option>
                            <option value="9">顺利秒秒彩</option>
                        </select>
                    &nbsp;&nbsp;
                    玩法群：
                        <select id="J-select-method-group" style="display:none;">
                            <option value="0" selected="selected">所有玩法群</option>
                        </select>
                    &nbsp;&nbsp;
                    玩法：
                        <select id="J-select-method" style="display:none;">
                            <option value="0" selected="selected">所有玩法</option>
                        </select>

                </p>
                <p class="row">
                    游戏模式：<select id="J-select-game-mode" style="display:none;">
                            <option selected="selected" value="0">所有</option>
                            <option value="1">元</option>
                            <option value="2">角</option>
                        </select>
                    &nbsp;&nbsp;
                    账变类型：
                        <select id="J-select-bill-type" style="display:none;">
                              <option selected="selected" value="0">所有类型</option>
                              <option value="1">加入游戏</option>
                              <option value="2">销售返点</option>
                              <option value="3">奖金派送</option>
                              <option value="4">追号扣款</option>
                              <option value="5">当期追号返款</option>
                              <option value="6">撤单返款</option>
                              <option value="7">撤单手续费</option>
                              <option value="8">撤销返点</option>
                              <option value="9">撤销派奖</option>
                              <option value="10">频道小额转出</option>
                              <option value="11">特殊金额整理</option>
                        </select>





                </p>
                <p class="row">
                    游戏用户：<input class="input w-3" type="text" value="" />
                    &nbsp;&nbsp;
                    <input class="btn" type="button" value=" 搜 索 " />
                </p>
            </div>

            <table width="100%" class="table">
                <thead>
                    <tr>
                        <th>账变编号</th>
                        <th>时间</th>
                        <th>账变类型</th>
                        <th>游戏</th>
                        <th>玩法</th>
                        <th>模式</th>
                        <th>变动价格</th>
                        <th>余额</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><a href="records-game-detail.php" title="D140574583747475858">D1405...</a></td>
                        <td>
                            2014-05-30
                            <br />
                            13:32:03
                        </td>
                        <td>销售返点</td>
                        <td>重庆时时彩</td>
                        <td>后三直选</td>
                        <td>元</td>
                        <td><span class="c-red">+ 108.00</span></td>
                        <td>29,307.05</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>D1405...</td>
                        <td>
                            2014-05-30
                            <br />
                            13:32:03
                        </td>
                        <td>销售返点</td>
                        <td>重庆时时彩</td>
                        <td>后三直选</td>
                        <td>元</td>
                        <td><span class="c-green">- 10.00</span></td>
                        <td>29,307.05</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td><a href="#">D1405...</a></td>
                        <td>
                            2014-05-30
                            <br />
                            13:32:03
                        </td>
                        <td>销售返点</td>
                        <td>重庆时时彩</td>
                        <td>后三直选</td>
                        <td>元</td>
                        <td><span class="c-red">+ 108.00</span></td>
                        <td>29,307.05</td>
                        <td>-</td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td>本页资金变动</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><span class="c-red">+ 98.00</span></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        @include('w.pages')
        </div>
@stop


@section('end')
<script>
(function($){
    var table = $('#J-table'),
        selectGameType = new bomao.Select({realDom:'#J-select-game-type',cls:'w-3'}),
        selectMethodGroup = new bomao.Select({realDom:'#J-select-method-group',cls:'w-3'}),
        selectMethod = new bomao.Select({realDom:'#J-select-method',cls:'w-3'}),
        selectIssue = new bomao.Select({realDom:'#J-select-issue',cls:'w-2'}),
        loadMethodgroup,
        loadMethod;


    new bomao.Select({realDom:'#J-select-game-mode',cls:'w-1'});
    new bomao.Select({realDom:'#J-select-bill-type',cls:'w-2'});


    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });


    selectGameType.addEvent('change', function(e, value, text){
        var id = $.trim(value);
        if(id == '' || id == '0'){
            if(id == '0'){
                selectMethodGroup.reBuildSelect([{'value':'0', 'text':'所有玩法群', 'checked':true}]);
            }
            return;
        }
        loadMethodgroup(id, function(data){
            var list = [];
            $.each(data, function(i){
                list[i] = {value:data[i]['id'], text:data[i]['name'], checked: data[i]['isdefault'] ? true : false};
            });
            selectMethodGroup.reBuildSelect(list);
        });
    });
    loadMethodgroup = function(gameid, callback){
        var id = gameid;
        $.ajax({
            url:'../data/methodgroup.php?id=' + id,
            timeout:30000,
            dataType:'json',
            beforeSend:function(){

            },
            success:function(data){
                if(Number(data['isSuccess']) == 1){
                    callback(data['data']);
                }else{
                    alert(data['msg']);
                }
            },
            error:function(){
                alert('网络请求失败，请稍后重试');
            }
        });
    };


    selectMethodGroup.addEvent('change', function(e, value, text){
        var id = $.trim(value);
        if(id == '' || id == '0'){
            if(id == '0'){
                selectMethod.reBuildSelect([{'value':'0', 'text':'所有玩法', 'checked':true}]);
            }
            return;
        }
        loadMethod(id, function(data){
            var list = [];
            $.each(data, function(i){
                list[i] = {value:data[i]['id'], text:data[i]['name'], checked: data[i]['isdefault'] ? true : false};
            });
            selectMethod.reBuildSelect(list);
        });
    });
    loadMethod = function(groupid, callback){
        var id = groupid;
        $.ajax({
            url:'../data/method.php?id=' + id,
            timeout:30000,
            dataType:'json',
            beforeSend:function(){

            },
            success:function(data){
                if(Number(data['isSuccess']) == 1){
                    callback(data['data']);
                }else{
                    alert(data['msg']);
                }
            },
            error:function(){
                alert('网络请求失败，请稍后重试');
            }
        });
    };





})(jQuery);
</script>
@stop