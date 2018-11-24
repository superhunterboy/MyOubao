@extends('l.home')

@section('title')
            追号记录
@parent
@stop

@section('scripts')
@parent
    {{ script('datePicker')}}
    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
@stop

@section ('main')
<div class="nav-bg">
            <div class="title-normal">
                追号记录
            </div>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    游戏时间：<input id="J-date-start" class="input w-3" type="text" value="2014-06-10  00:00:00" /> 至 <input id="J-date-end" class="input w-3" type="text" value="2014-06-11  00:00:00" />
                    &nbsp;&nbsp;
                    <select id="J-select-issue" style="display:none;">
                            <option value="1">追号编号</option>
                            <option value="2">奖期编号</option>
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
                    游戏用户：<input class="input w-3" type="text" value="" />
                    &nbsp;&nbsp;
                    <input class="btn" type="button" value=" 搜 索 " />
                </p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>游戏</th>
                        <th>追号编号</th>
                        <th>追号期数</th>
                        <th>追号金额</th>
                        <th>完成金额</th>
                        <th>取消金额</th>
                        <th>中奖后终止</th>
                        <th>追号状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="records-trace-detail.php">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>是</td>
                        <td>已完成</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>否</td>
                        <td>进行中</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>是</td>
                        <td>已完成</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>否</td>
                        <td>进行中</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>是</td>
                        <td>已完成</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>否</td>
                        <td>进行中</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>是</td>
                        <td>已完成</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>否</td>
                        <td>进行中</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>是</td>
                        <td>已完成</td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>4/10</td>
                        <td>30.00</td>
                        <td>0.00</td>
                        <td>30.00</td>
                        <td>否</td>
                        <td>进行中</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>小结</td>
                        <td></td>
                        <td></td>
                        <td>1,800.00</td>
                        <td>1,800.00</td>
                        <td>1,800.00</td>
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
@parent
@stop