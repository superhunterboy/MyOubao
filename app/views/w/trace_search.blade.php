{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;')) }}
<table style="width:100%"><tr>
        <td>
            <table>
                <tr>
                    <td style="width:80px" class="text-right">追号编号：</td>
                    <td  style="width:150px">
                        <input class="form-control input-sm" type="text" name="serial_number" value="{{@$aSearchFields['serial_number']}}">
                    </td>
                    <td style="width:80px" class="text-right">游戏时间：</td>
                    <td  style="width:150px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="bought_at[]" value="{{@$aSearchFields['bought_at'][0]}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td style="width:80px" class="text-right">至：</td>
                    <td  style="width:150px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="bought_at[]" value="{{@$aSearchFields['bought_at'][1]}}"  >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td style="width:80px"   class="text-right">每页条数：</td>
                    <td style="width:150px"><select name="pagesize" style="width:100%" class="form-control select-sm"><option value="15" @if(@$aSearchFields['pagesize']==15)selected='selected' @endif>15</option><option value="30"@if(@$aSearchFields['pagesize']==30)selected='selected' @endif>30</option><option value="50"@if(@$aSearchFields['pagesize']==50)selected='selected' @endif>50</option><option value="100"@if(@$aSearchFields['pagesize']==100)selected='selected' @endif>100</option></select></td>
        

                </tr>

                <tr>
                    <td class="text-right">用户搜索：</td>
                    <td>
                        <select name="user_search_type" style="width:100%" class="form-control select-sm j-select">
                            <option value="2"@if(@$aSearchFields['user_search_type']==2)selected='selected' @endif>总代列表</option>
                            <option value="1"@if(@$aSearchFields['user_search_type']==1)selected='selected' @endif>手工输入</option>
                        </select>
                    </td>
                    <td colspan="2" class="j-none"@if(isset($aSearchFields['user_search_type'])&&$aSearchFields['user_search_type']==1)style="display: none;"@endif>
                        <div class="text-right" style="width:80px; float:left">总代：</div>
                        <div class="form-group">
                            <select name="root_agent" style="float:left;width:80px;" class="form-control select-sm"><option value>所有总代</option>@foreach($aRootAgent as $id =>$name) <option value="{{$name}}"@if(@$aSearchFields['root_agent']==$name)selected='selected' @endif>{{$name}}</option> @endforeach</select>
                            <label style="float:left; margin-left:10px;" >
                                <input name="ra_include_children" type="checkbox" name="sel" value="1" checked="checked" disabled="true">含下级
                            </label>
                        </div>
                    </td>
                    <td colspan="2" class="j-none" @if(!isset($aSearchFields['user_search_type'])or$aSearchFields['user_search_type']==2)style="display: none;"@endif>
                        <div class="text-right" style="width:80px; float:left">游戏用户：</div>
                        <div class="form-group">
                            <input style="float:left;width:80px;" class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                            <label style="float:left; margin-left:10px;">
                                <input name="un_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['un_include_children']==1)checked @endif>含下级
                            </label>
                        </div>
                    </td>
                    <td class="text-right">元角模式：</td>
                    <td >
                        <select name="coefficient" style="width:100%" class="form-control select-sm"><option value>不限</option>@foreach($aCoefficients as $id=>$value)<option value="{{$id}}" @if(@$aSearchFields['coefficient']==$id)selected='selected' @endif>{{$value}}</option>@endforeach</select>
                    </td>
                    <td  class="text-right">追号状态：</td>
                    <td><select name="status" style="width:100%" class="form-control select-sm"><option value>所有追号</option>@if(isset($aStatuses)) @foreach($aStatuses as $key => $val)<option value="{{$key}}"@if(@strlen($aSearchFields['status'])>0&&@$aSearchFields['status']==$key)selected='selected' @endif>{{$val}}</option> @endforeach @endif</select></td>
               



                </tr>

                <tr>
                    <td class="text-right">游戏名称：</td>
                    <td> 
                        <select id="lottery_id" name="lottery_id" style="width:100%" class="form-control select-sm"> <option value>所有游戏</option>@foreach($aLotteries as $id =>$name) <option value="{{$id}}"@if(@$aSearchFields['lottery_id']==$id)selected='selected' @endif>{{$name}}</option> @endforeach </select>
                    </td>
                    <td class="text-right">游戏玩法：</td>
                    <td>
                        <select id="way_id" name="way_id" style="width:100%" class="form-control select-sm"> <option value>所有玩法</option>@if(isset($aLotteryWays)) @foreach($aLotteryWays as $val)<option value="{{$val['id']}}"@if($val['id']==$aSearchFields['way_id'])selected="selected"@endif>{{$val['name']}}</option> @endforeach @endif</select>
                    </td>
                    <td class="text-right">游戏奖期：</td>
                    <td><select id="issue" name="start_issue" style="width:100%" class="form-control select-sm"><option value>所有奖期</option>@if(isset($aIssues)) @foreach($aIssues as $val)<option value="{{$val['name']}}"@if($val['name']==$aSearchFields['start_issue'])selected="selected"@endif>{{$val['name']}}</option> @endforeach @endif</select></td>
                  </tr>

            </table>
        </td>
        <td class="text-left">
            <input class="btn btn-default" style="margin:2px;" type="submit" value="搜索"/>
            <!--<a class="btn btn-success" style="margin:2px;" />下载数据报表</a>-->
        </td>
    </tr></table>
<?php
echo Form::hidden('is_search');
echo Form::close();
?>

@section('end')
{{ script('datetimepicker') }}
{{ script('datetimepicker-zh-CN')}}
@parent
<script type="text/javascript">
    $(function () {
        //时间控件
        $('.form_date').datetimepicker({
            language: 'zh-CN',
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            minView: 0,
            forceParse: 0,
            showMeridian: 1,
            pickerPosition: 'bottom-left'
        });
        
        //切换
        $('.j-select').change(function () {
            if ($(this).val() == 1) {
                $('.j-none').hide().eq('1').show();
            } else {
                $('.j-none').hide().eq('0').show();
            }
        });
        function resetSelectForm(selectId,title){
            var selectDom = $("#"+selectId);
            selectDom.html("<option value>"+title+"</option>");
        }
        function setDatatoSelectForm(selectId, title, data) {
            var selectDom = $("#"+selectId);
            resetSelectForm(selectId,title);
            var optstr = "";
            $(data).each(function(){
                optstr +="<option value='"+this.id+"'>"+this.name+"</option>";
            });
            selectDom.append(optstr);
        }

        $('#lottery_id').change(function () {
            var lottery_id = $("#lottery_id").val();
            if (lottery_id > 0) {
                $.ajax({
                    url: '/projects/?action=ajax&lottery_id=' + lottery_id,
                    type: 'GET',
                }).done(function (data) {
                    jsonObj = eval("(" + data + ")");
                    lotteryWays = jsonObj.lottery_ways;
                    setDatatoSelectForm('way_id', '所有玩法', lotteryWays);
                    issues = jsonObj.issues;
                    setDatatoSelectForm('issue', '所有奖期', issues);
                }).fail(function (data) {
                    alert('Getl Data Failed!', 'Tip');
                });
            }else{
                resetSelectForm('way_id', '所有玩法');
                resetSelectForm('issue', '所有奖期');
            }
        });
    });
</script>

@stop