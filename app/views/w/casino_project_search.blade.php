{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;', 'id'=>'project_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td style="width:840px">
            <table>
                <tr>

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


                </tr>

                <tr>


                    <td colspan="2" class="j-none" @if(!isset($aSearchFields['user_search_type'])or$aSearchFields['user_search_type']==2)style="display: none;"@endif>
                        <!-- <div class="text-right" style="width:80px; float:left">游戏用户：</div> -->
                        <div class="form-group">
                            <input style="float:left;width:100px;" class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                            <label style="float:left; margin-left:10px;">
                                <input name="un_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['un_include_children']==1)checked @endif>含下级
                            </label>
                        </div>
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
            $('#project_search_form').attr('action', '/projects/download');
//            $('#_action').val('/projects/download');
            $('#project_search_form').submit();
        });
        $('#submitForm').click(function (event) {
            $('#project_search_form').attr('action', '/projects');
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