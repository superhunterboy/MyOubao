{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;','id'=>'withdraw_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td >
            <table>
                <tr>
                    <td style="width:80px" class="text-right">处理状态：</td>
                    <td  style="width:160px">
                        <select name="status" style="width:100%" class="form-control select-sm j-select">
                            <option value>全部</option>
                            @foreach($validStatuses as $id =>$name) <option value="{{$id}}"@if(@strlen($aSearchFields['status'])>0&&@$aSearchFields['status']==$id)selected='selected' @endif>{{$name}}</option> @endforeach
                        </select>
                    </td>
                    <td style="width:110px" class="text-right">处理人员：</td>
                    <td  style="width:160px">
                        <input class="form-control input-sm" type="text" name="auditor" value="{{@$aSearchFields['auditor']}}">
                    </td>
                    <td style="width:80px" class="text-right">网络地址：</td>
                    <td  style="width:160px">
                        <input class="form-control input-sm" type="text" name="serial_number" value="{{@$aSearchFields['serial_number']}}">
                    </td>
                    <td style="width:80px" class="text-right">用户身份：</td>
                    <td style="width:160px">
                        <select id="way_id" name="role_id" style="width:100%" class="form-control select-sm"> <option value>全部</option>@if(isset($aUserIds)) @foreach($aUserIds as $key => $val)<option value="{{$key}}"@if($key==@$aSearchFields['role_id'])selected="selected"@endif>{{$val}}</option> @endforeach @endif</select>
                    </td>
                </tr>

                <tr>
                    <td  class="text-right">发起时间：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="request_time[]"  value="{{@$aSearchFields['request_time'][0]}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">至：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="request_time[]" value="{{@$aSearchFields['request_time'][1]}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">提现用户：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                    </td>
                    <td class="text-right">提现银行：</td>
                    <td>
                        <select id="way_id" name="bank_id" style="width:100%" class="form-control select-sm"><option value>所有银行</option>
                            @foreach($aBanks as $id =>$name) <option value="{{$id}}"@if(@$aSearchFields['bank_id']==$id)selected='selected' @endif>{{$name}}</option> @endforeach</select>
                    </td>
                </tr>

                <tr>
                    <td  class="text-right">处理时间：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="verified_time[]"  value="{{@$aSearchFields['verified_time'][0]}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">至：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="verified_time[]"   value="{{@$aSearchFields['verified_time'][1]}}">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">总代：</td>
                    <td >
                        <input class="form-control input-sm" type="text" name="top_agent" value="{{@$aSearchFields['top_agent']}}">
                    </td>
                    <td class="text-right">测试账户：</td>
                    <td>
                        <select name="is_tester" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>
                    <!-- <td style="width:80px" class="text-right">测试账户：</td>
                    <td  style="width:180px">
                        <select name="is_tester" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{-- @$aSearchFields['is_tester'] === 1 ? 'selected' : '' --}}>是</option>
                            <option value="0" {{-- @$aSearchFields['is_tester'] === 0 ? 'selected' : '' --}}>否</option>
                        </select>
                    </td> -->
                </tr>
                <tr>
                    <td class="text-right">MC订单号：</td>
                    <td>
                        <input class="form-control input-sm" type="text" name="mownecum_order_num" value="{{@$aSearchFields['mownecum_order_num']}}">
                    </td>
                    <td class="text-right">提现渠道：</td>
                    <td>
                      
                        <select name="is_sdpay" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="0" {{ @$aSearchFields['is_sdpay'] === '0' ? 'selected' : '' }}>dashpay</option>
                            <option value="1" {{ @$aSearchFields['is_sdpay'] === '1' ? 'selected' : '' }}>sdpay</option>
                            <option value="2" {{ @$aSearchFields['is_sdpay'] === '2' ? 'selected' : '' }}>tonghuika</option>
                        </select>
                    </td>
                    <td  class="text-right">每页条数：</td>
                    <td>
                        <select name="pagesize" style="width:100%" class="form-control select-sm">
                            <option value="50" @if(@$aSearchFields['pagesize']==50)selected='selected' @endif>50</option>
                            <option value="100"@if(@$aSearchFields['pagesize']==100)selected='selected' @endif>100</option>
                            <option value="200"@if(@$aSearchFields['pagesize']==200)selected='selected' @endif>200</option>
                            <option value="500"@if(@$aSearchFields['pagesize']==500)selected='selected' @endif>500</option>
                        </select>
                    </td>
                </tr>

            </table>
        </td>
        <td class="text-left">
            <a class="btn btn-default" style="margin:2px;" id='submit_withdraw'>搜索</a>
            <a class="btn btn-success" style="margin:2px;"  id='download_withdraw'>下载数据报表</a>
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
    });
    $('#download_withdraw').click(function () {
        $('#withdraw_search_form').attr('action', '/withdrawals/download');
        $('#withdraw_search_form').submit();
    });
    $('#submit_withdraw').click(function (event) {
        // $('#withdraw_search_form').attr('action', '/withdrawals');
        $('#withdraw_search_form').submit();
    });
</script>

@stop