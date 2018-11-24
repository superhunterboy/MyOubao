<div class="panel panel-default">
    <div class=" panel-body">
        {{ Form::open(array('method' => 'get', 'class' => 'form-inline','id'=>'team_profit_search_form')) }}
        <input id="download_flag" name="download_flag"  value="" type="hidden" />
        <table style="width:100%"><tr>

                <td style="width:980px">
                    <table>
                        <tr>
                            <td class="text-right">日期：</td>
                            <td  >
                                <input class="form-control input-xs boot-time" type="text" name="date_from" value="{{@$aSearchFields['date_from']}}" >

                            </td>
                            <td class="text-right">{{__('_transaction.to')}}：</td>
                            <td >
                                <input class="form-control input-xs boot-time" type="text" name="date_to" value="{{@$aSearchFields['date_to']}}"  >
                            </td>
                            <td style="width:80px" class="text-right">用户类型：</td>
                            <td >
                                <select name="user_type" style="width:100%" class="form-control input-xs"><option value>不限</option>
                                    <option value="1" @if(isset($aSearchFields['user_type']) && @$aSearchFields['user_type']==='1')selected='selected' @endif>代理</option>  \
                                    <option value="2" @if(isset($aSearchFields['user_type']) && @$aSearchFields['user_type']==='2')selected='selected' @endif>总代</option> </select>
                            </td>
                            <td>
                                <div class="text-right" style="width:80px; float:left;padding-top:3px;">用户名：</div>
                                <div class="form-group">
                                    <input style="float:left;width:100px;" class="form-control input-xs" type="text" name="username" value="{{@$aSearchFields['username']}}">
                                    <label style="float:left; margin-left:10px;" >
                                        <input name="un_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['un_include_children']==1)checked @endif>{{__('_transaction.children')}}
                                    </label>
                                </div>
                            </td>
                            <td style="width:80px" class="text-right">测试用户：</td>
                            <td  style="width:80px">
                                <select name="is_tester" style="width:100%" class="form-control input-xs">
                                    <option value>不限</option>
                                    <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                                    <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="text-right">
                    <input class="btn btn-success" type="submit" id="submitForm" value="{{__('_transaction.search')}}"/>
                </td>
            </tr></table>
        <?php
        echo Form::hidden('is_search');
        echo Form::close();
        ?>
    </div></div>
@section('end')
@parent
<script type="text/javascript">
    $(function () {


        //切换
        $('.j-select').change(function () {
            if ($(this).val() == 1) {
                $('.j-none').hide().eq('0').show();
            } else {
                $('.j-none').hide().eq('1').show();
            }
        });

        $('#submitForm').click(function () {
            $('#team_profit_search_form').attr('action', '/team-profits');
            $('#team_profit_search_form').submit();
        });

    });
</script>

@stop