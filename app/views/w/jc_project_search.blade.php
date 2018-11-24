{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;', 'id'=>'project_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td style="width:840px">
            <table>
                <tr>
                    <td style="width:80px" class="text-right">注单ID：</td>
                    <td  style="width:180px">
                        <input class="form-control input-sm" type="text" name="id" value="{{@$aSearchFields['id']}}">
                    </td>
                    <td style="width:80px" class="text-right">方案ID：</td>
                    <td  style="width:180px">
                        <input class="form-control input-sm" type="text" name="bet_id" value="{{@$aSearchFields['bet_id']}}">
                    </td>
                    <td style="width:80px" class="text-right">合买ID：</td>
                    <td  style="width:180px">
                        <input class="form-control input-sm" type="text" name="group_id" value="{{@$aSearchFields['group_id']}}">
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
                </tr>
                <tr>
                    <td style="width:80px" class="text-right">类型：</td>
                    <td  style="width:180px">
                        <?php
                        foreach ($validType as $key => $value) {
                            $validType[$key] = __('_manjcproject.' . strtolower($value));
                        }
                        ?>
                        <select name="type" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($validType as $key => $value)
                                <option value="{{ $key }}" {{ @$aSearchFields['type'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width:80px" class="text-right">购买类型：</td>
                    <td  style="width:180px">
                        <?php
                        foreach ($validBuyType as $key => $value) {
                            $validBuyType[$key] = __('_manjcproject.' . strtolower($value));
                        }
                        ?>
                        <select name="buy_type" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($validBuyType as $key => $value)
                                <option value="{{ $key }}" {{ @$aSearchFields['buy_type'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width:80px" class="text-right">玩法群：</td>
                    <td  style="width:180px">
                        <?php
                        foreach ($validMethodGroups as $key => $value) {
                            $validMethodGroups[$key] = $value;
                        }
                        ?>
                        <select name="method_group_id" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($validMethodGroups as $key => $value)
                                <option value="{{ $key }}" {{ @$aSearchFields['method_group_id'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width:80px" class="text-right">状态：</td>
                    <td  style="width:180px">
                        <?php
                        foreach ($validStatus as $key => $value) {
                            $validStatus[$key] = __('_manjcproject.' . strtolower($value));
                        }
                        ?>
                        <select name="status" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($validStatus as $key => $value)
                                <option value="{{ $key }}" {{ @$aSearchFields['status'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td style="width:80px" class="text-right">派奖状态：</td>
                    <td  style="width:180px">
                        <?php
                        foreach ($validPrizeStatus as $key => $value) {
                            $validPrizeStatus[$key] = __('_manjcproject.' . strtolower($value));
                        }
                        ?>
                        <select name="prize_status" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            @foreach ($validPrizeStatus as $key => $value)
                                <option value="{{ $key }}" {{ @$aSearchFields['prize_status'] === (string)$key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
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
            $('#project_search_form').attr('action', '/jc-projects/download');
//            $('#_action').val('/projects/download');
            $('#project_search_form').submit();
        });
        $('#submitForm').click(function (event) {
            $('#project_search_form').attr('action', '/jc-projects');
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