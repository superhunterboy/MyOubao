{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;', 'id'=>'account_snapshot_search_form')) }}
<table style="width:100%"><tr>
        <td >
            <table>
                <tr>
                    <td class="text-right">搜索总代：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                    </td>


                    <td  class="text-right">查看时间：</td>
                    <td >
                        <div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-format="yyyy-mm-dd" data-link-field="created_at_from" style="width:215px"><input class="form-control" size="16" type="text" name="created_at_from"  value="{{@$aSearchFields['created_at_from']}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>

                    </td>
                    {{--<td class="text-right">结束时间：</td>--}}
                    {{--<td >--}}
                        {{--<div class="input-group date form_date" data-date="" data-date-format="yyyy-mm-dd" data-link-format="yyyy-mm-dd" data-link-field="created_at_to" style="width:215px"><input class="form-control" size="16" type="text" name="created_at_to"  value="{{@$aSearchFields['created_at_to']}}" >--}}
                            {{--<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div>--}}
                    {{--</td>--}}

                    <td style="width:80px" class="text-right">测试用户：</td>
                    <td>
                        <select name="is_tester" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <td class="text-right">最小余额 ：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="balance_at_from" value="{{@$aSearchFields['balance_at_from']}}">
                    </td>

                    <td class="text-right">最大余额 ：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="balance_at_to" value="{{@$aSearchFields['balance_at_to']}}">
                    </td>

                    <td class="text-right">是否冻结账户：</td>
                    <td>
                        <select name="blocked" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['blocked'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['blocked'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>
                </tr>

            </table>
        </td>
        <td class="text-left">
            <input class="btn btn-default" style="margin:2px;" type="submit" id="submitForm" value="搜索"/>
            <a class="btn btn-success" style="margin:2px;"  id="download">下载数据报表</a>
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
                $('#account_snapshot_search_form').attr('action','/account-snapshots/download');
                $('#account_snapshot_search_form').submit();
            });
            $('#submitForm').click(function(event) {
                $('#account_snapshot_search_form').attr('action','/account-snapshots');
                $('#account_snapshot_search_form').submit();
            });
            //时间控件
            $('.form_date').datetimepicker({
                language: 'zh-CN',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0,
                showMeridian: 1,
                pickerPosition: 'bottom-left'
            });
        });
    </script>

@stop