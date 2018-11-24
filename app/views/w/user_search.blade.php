{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;', 'id'=>'user_search_form')) }}
<table style="width:100%"><tr>
        <td >
            @if(isset($aSearchFields['parent_id']) && $aSearchFields['parent_id']!='')
            <input  type="hidden" name="parent_id" value="{{@$aSearchFields['parent_id']}}">
            @endif
            <table>
                <tr>
                    <td class="text-right">用户名称：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                    </td>
                    <td class="text-right">昵称：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="nickname" value="{{@$aSearchFields['nickname']}}">
                    </td>
                    <td class="text-right">用户组：</td>
                    <td>
                        <select name="user_group" style="width:100%" class="form-control select-sm"><option value>不限</option>
                            @foreach($aUserTypes as $id =>$name) <option value="{{$id}}" @if(isset($aSearchFields['user_group']) && @$aSearchFields['user_group']===$id.'')selected='selected' @endif>{{__('_user.'.$name)}}</option> @endforeach</select>
                    </td>
                    <td class="text-right">可用余额 ：</td>
                    <td >
                        <input class="form-control input-sm" type="text" style="width:80px;" name="amount[]" value="{{@$aSearchFields['amount'][0]}}"> 至
                        <input class="form-control input-sm" type="text" style="width:80px;" name="amount[]" value="{{@$aSearchFields['amount'][1]}}">(元)
                    </td>
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
                    <td  class="text-right">注册时间：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at_from"  value="{{@$aSearchFields['created_at_from']}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">至：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at_to" value="{{@$aSearchFields['created_at_to']}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td  class="text-right">每页条数：</td>
                    <td>
                        <select name="pagesize" style="width:100%" class="form-control select-sm">
                            <option value="15"  {{ @$aSearchFields['pagesize'] == 15 ?  'selected' : '' }}>15</option>
                            <option value="30"  {{ @$aSearchFields['pagesize'] == 30 ?  'selected' : '' }}>30</option>
                            <option value="50"  {{ @$aSearchFields['pagesize'] == 50 ?  'selected' : '' }}>50</option>
                            <option value="100" {{ @$aSearchFields['pagesize'] == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </td>
                    <td class="text-right">&nbsp;</td>
                    <td>&nbsp;</td>
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
            $('#user_search_form').attr('action','/users/download');
            $('#user_search_form').submit();
        });
        $('#submitForm').click(function(event) {
            $('#user_search_form').attr('action','/users');
            $('#user_search_form').submit();
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
    });
</script>

@stop