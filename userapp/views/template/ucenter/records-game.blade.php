@extends('l.home')

@section('title')
            游戏记录
@parent
@stop

@section('scripts')
@parent

    {{ script('easing.1.3')}}
    {{ script('mousewheel')}}
    {{ script('datePicker')}}
    {{ script('tip')}}
@stop


@section ('main')
       <div class="nav-bg">
            <div class="title-normal">
                游戏记录
            </div>
        </div>

        <div class="content">
            <div class="area-search">
                <p class="row">
                    游戏时间：<input id="J-date-start" class="input w-3" type="text" value="2014-06-10  00:00:00" /> 至 <input id="J-date-end" class="input w-3" type="text" value="2014-06-11  00:00:00" />
                    &nbsp;&nbsp;
                    <select id="J-select-issue" style="display:none;">
                            <option value="1">注单编号</option>
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


            <table width="100%" class="table" id="J-table">
                <thead>
                    <tr>
                        <th>游戏</th>
                        <th>注单编号</th>
                        <th>投注内容</th>
                        <th>投注金额</th>
                        <th>奖金</th>
                        <th>状态</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="records-game-detail.php">D140523034VFBCBIIJAB</a></td>
                        <td><a class="view-detail" href="#">详细号码</a><textarea class="data-textarea" style="display:none;">112，252，444，157，125，148，158，118，444，555，487，489，156</textarea></td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-wait">未开奖</span></td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-notwin">未中奖</span></td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-winning">已派奖</span></td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td><a class="view-detail" href="#">详细号码</a><textarea class="data-textarea" style="display:none;">112，252，444，157，125，148，158，118，444，555，487，489，156</textarea></td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-wait">未开奖</span></td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-notwin">未中奖</span></td>
                    </tr>
                    <tr class="last">
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-winning">已派奖</span></td>
                    </tr>
                    <tr class="last">
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-winning">已派奖</span></td>
                    </tr>
                    <tr>
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-notwin">未中奖</span></td>
                    </tr>
                    <tr class="last">
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-winning">已派奖</span></td>
                    </tr>
                    <tr class="last">
                        <td>重庆时时彩</td>
                        <td><a href="#">D140523034VFBCBIIJAB</a></td>
                        <td>123,333,444</td>
                        <td>30.0000</td>
                        <td>0.0000</td>
                        <td><span class="status-winning">已派奖</span></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>小结</td>
                        <td></td>
                        <td></td>
                        <td>1,800.0000</td>
                        <td>1,800.0000</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>




             @include('w.pages')




            <div class="alert alert-error alert-noresult">
                <i></i>
                <div class="txt">
                    <h4>没有符合条件的记录，请更改查询条件。</h4>
                </div>
            </div>



        </div>


@stop


@section('end')
<script>
(function($){
    var table = $('#J-table'),
        details = table.find('.view-detail'),
        tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-page-records'}),
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



    details.hover(function(e){
        var el = $(this),
            text = el.parent().find('.data-textarea').val();
        tip.setText(text);
        tip.show(-90, tip.getDom().height() * -1 - 22, el);

        e.preventDefault();
    },function(){
        tip.hide();
    });


    /**
    setTimeout(function(){
        $(".choose-list-cont").jscroll({Btn:{btn:false}});
    }, 0);
    **/


})(jQuery);
</script>

@parent
@stop