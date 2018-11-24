{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;', 'id'=>'project_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td style="width:840px">
            <table>
                <tr>
                    <td  class="text-right">年份：</td>
                    <td>
                        <select name="year" style="width:100%" class="form-control select-sm">
                            <option value="" @if(!isset($i))selected='selected' @endif></option>
                            @for($i=2015;$i<=date('Y');$i++)
                                <option value="{{$i}}" @if(@$aSearchFields['year']==$i)selected='selected' @endif>{{$i}}</option>
                            @endfor
                        </select>
                    </td>
                    <td  class="text-right">季度：</td>
                    <td>
                        <select name="quarter" style="width:100%" class="form-control select-sm">
                            <option value="" @if(@$aSearchFields['quarter']=='')selected='selected' @endif></option>
                            <option value="Q1" @if(@$aSearchFields['quarter']=='Q1')selected='selected' @endif>第一季度</option>
                            <option value="Q2" @if(@$aSearchFields['quarter']=='Q2')selected='selected' @endif>第二季度</option>
                            <option value="Q3" @if(@$aSearchFields['quarter']=='Q3')selected='selected' @endif>第三季度</option>
                            <option value="Q4" @if(@$aSearchFields['quarter']=='Q4')selected='selected' @endif>第四季度</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:80px" class="text-right">注单编号：</td>
                    <td  style="width:180px">
                        <input class="form-control input-sm" type="text" name="serial_number" value="{{@$aSearchFields['serial_number']}}">
                    </td>
                    <td style="width:80px" class="text-right">游戏时间：</td>
                    <td  style="width:180px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at[]" value="{{@$aSearchFields['created_at'][0]}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td style="width:80px" class="text-right">至：</td>
                    <td  style="width:180px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at[]" value="{{@$aSearchFields['created_at'][1]}}"  >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td style="width:80px" class="text-right">测试用户：</td>
                    <td  style="width:180px">
                        <select name="is_tester" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>

                </tr>

                <tr>
                    <td class="text-right">用户搜索：</td>
                    <td>
                        <select name="user_search_type" style="width:100%" class="form-control select-sm j-select">
                            <option value="2" {{ @$aSearchFields['user_search_type'] == 2 ? 'selected' : '' }} >总代列表</option>
                            <option value="1" {{ @$aSearchFields['user_search_type'] == 1 ? 'selected' : '' }} >手工输入用户名</option>
                        </select>
                    </td>
                    <td colspan="2" class="j-none" @if(isset($aSearchFields['user_search_type'])&&$aSearchFields['user_search_type']==1)style="display: none;"@endif>
                        <!-- <div class="text-right" style="width:80px; float:left">总代：</div> -->
                        <div class="form-group">
                            <select name="root_agent" style="float:left;width:100px;" class="form-control select-sm">
                                <option value>所有总代</option>
                                @foreach($aRootAgent as $id => $name)
                                <option value="{{ $name }}" {{ @$aSearchFields['root_agent']==$name ? 'selected' : '' }} >{{ $name }}</option>
                                @endforeach
                            </select>
                            <label style="float:left; margin-left:10px;" >
                                <input name="ra_include_children" type="checkbox" name="sel" value="1" checked="checked" disabled="true">含下级
                            </label>
                        </div>
                    </td>
                    <td colspan="2" class="j-none" @if(!isset($aSearchFields['user_search_type'])or$aSearchFields['user_search_type']==2)style="display: none;"@endif>
                        <!-- <div class="text-right" style="width:80px; float:left">游戏用户：</div> -->
                        <div class="form-group">
                            <input style="float:left;width:100px;" class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                            <label style="float:left; margin-left:10px;">
                                <input name="un_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['un_include_children']==1)checked @endif>含下级
                            </label>
                        </div>
                    </td>
                    <td class="text-right">元角模式：</td>
                    <td >
                        <select name="coefficient" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach($aCoefficients as $id => $value)
                            <option value="{{ $id }}" {{ @$aSearchFields['coefficient'] == $id ? 'selected' : '' }} >{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width:80px" class="text-right">状态：</td>
                    <td  style="width:180px">
                        <?php
                        $aStatusDescs = [];
                        foreach ($aStatusDesc as $key => $value) {
                            $aStatusDescs[$key] = __('_project.' . strtolower(Str::slug($value)));
                        }
                        ?>
                        <select name="status" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($aStatusDescs as $key => $value)
                            <option value="{{ $key }}" {{ @$aSearchFields['status'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="text-right">游戏名称：</td>
                    <td>
                        <select id="lottery_id" name="lottery_id" style="width:100%" class="form-control select-sm">
                            <option value>所有游戏</option>
                            @foreach($aLotteries as $id =>$name)
                            <option value="{{ $id }}" {{ @$aSearchFields['lottery_id'] == $id ? 'selected' : '' }} >{{ $name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="text-right">游戏玩法：</td>
                    <td>
                        <select id="way_id" name="way_id" style="width:100%" class="form-control select-sm">
                            <option value>所有玩法</option>
                            @if(isset($aLotteryWays))
                            @foreach($aLotteryWays as $val)
                            <option value="{{ $val['id'] }}" {{ $val['id'] == $aSearchFields['way_id'] ? 'selected' : '' }} >{{ $val['name'] }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                    </td>
                    <td class="text-right">游戏奖期：</td>
                    <td>
                        <select id="issue" name="issue" style="width:100%" class="form-control select-sm">
                            <option value>所有奖期</option>
                            @if(isset($aIssues))
                            @foreach($aIssues as $val)
                            <option value="{{ $val['name'] }}" {{ $val['name'] == $aSearchFields['issue'] ? 'selected' : '' }} >{{ $val['name'] }}</option>
                            @endforeach
                            @endif
                        </select>
                    </td>
                    <td  class="text-right">来源：</td>
                    <td>
                        <select name="bet_source" style="width:100%" class="form-control select-sm">
                            <option value>所有来源</option>
                            @if(isset($aBetSources))
                                @foreach($aBetSources as $betSource)
                                    <option value="{{ $betSource['name'] }}" {{ @$aSearchFields['bet_source'] == $betSource['name'] ? 'selected' : '' }} >{{ $betSource['name'] }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </td>
                </tr>
                <tr>
                    <td  class="text-right">每页条数：</td>
                    <td>
                        <select name="pagesize" style="width:100%" class="form-control select-sm">
                            <option value="15"  {{ @$aSearchFields['pagesize'] == 15 ?  'selected' : '' }}>15</option>
                            <option value="30"  {{ @$aSearchFields['pagesize'] == 30 ?  'selected' : '' }}>30</option>
                            <option value="50"  {{ @$aSearchFields['pagesize'] == 50 ?  'selected' : '' }}>50</option>
                            <option value="100" {{ @$aSearchFields['pagesize'] == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </td>
                    <td colspan="2">
                        
                        <label style="float:left; margin-left:10px;" >
                         <input name="is_bet_number" type="checkbox" name="sel" value="1" {{@$aSearchFields['is_bet_number']==1?'checked="checked"':''}} ">显示投注号码
                            </label>
                    </td>
                </tr>

            </table>
        </td>


        <td class="text-left">
            <input class="btn btn-default" style="margin:2px;" type="submit" id="submitForm" value="搜索"/>
            <a class="btn btn-success" style="margin:2px;"  id="download">下载数据报表</a>
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
        $('#download').click(function () {
//            $('#download_flag').val('download');
            $('#project_search_form').attr('action', '/project-histories/download');
//            $('#_action').val('/projects/download');
            $('#project_search_form').submit();
        });
        $('#submitForm').click(function (event) {
            $('#project_search_form').attr('action', '/project-histories');
            $('#project_search_form').submit();
        });
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
        function resetSelectForm(selectId, title) {
            var selectDom = $("#" + selectId);
            selectDom.html("<option value>" + title + "</option>");
        }
        function setDatatoSelectForm(selectId, title, data) {
            var selectDom = $("#" + selectId);
            resetSelectForm(selectId, title);
            var optstr = "";
            $(data).each(function () {
                if (selectId == 'way_id') {
                    optstr += "<option value='" + this.id + "'>" + this.name + "</option>";
                } else if (selectId == 'issue') {
                    optstr += "<option value='" + this.name + "'>" + this.name + "</option>";
                }
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
            } else {
                resetSelectForm('way_id', '所有玩法');
                resetSelectForm('issue', '所有奖期');
            }
        });
    });
</script>

@stop