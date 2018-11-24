{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;','id'=>'deposit_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td style="width:980px">
            <table>
                <tr>
                    <td style="width:80px" class="text-right">充值用户：</td>
                    <td  style="width:180px">
                        <input class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">
                    </td>
                    <td style="width:100px" class="text-right">平台充值时间：</td>
                    <td  style="width:160px">
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
                    <td style="width:80px" class="text-right">附言：</td>
                    <td><input class="form-control" size="16" type="text" name="note" value="{{@$aSearchFields['note']}}" ></td>

                </tr>
                <tr>
                    <td class="text-right">发起银行：</td>
                    <td>
                        <select name="bank_id" style="width:100%" class="form-control select-sm j-select">
                            <option value>所有银行</option>
                            @foreach($aBanks as $id =>$name) <option value="{{$id}}"@if(@$aSearchFields['bank_id']==$id)selected='selected' @endif>{{$name}}</option> @endforeach
                        </select>
                    </td>
                    <td style="width:100px" class="text-right">银行到账时间：</td>
                    <td  style="width:160px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="pay_time[]" value="{{@$aSearchFields['pay_time'][0]}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td style="width:80px" class="text-right">至：</td>
                    <td  style="width:180px">
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="pay_time[]" value="{{@$aSearchFields['pay_time'][1]}}"  >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">充值方式：</td>
                    <td>
                        <select  name="deposit_mode" style="width:100%" class="form-control select-sm"><option value>所有方式</option>@foreach($aDepositMode as $id =>$name) <option value="{{$id}}"@if(@strlen($aSearchFields['deposit_mode'])>0&&@$aSearchFields['deposit_mode']==$id)selected='selected' @endif>{{__('_deposit.'.$name)}}</option> @endforeach</select>
                    </td>
                </tr>

                <tr>
                    <td class="text-right">处理状态：</td>
                    <td>
                        <select  name="status" style="width:100%" class="form-control select-sm"><option value>所有状态</option>@foreach($validStatuses as $id =>$name) <option value="{{$id}}"@if(@strlen($aSearchFields['status'])>0&&@$aSearchFields['status']==$id)selected='selected' @endif>{{__('_deposit.'.$name)}}</option> @endforeach</select>
                    </td>
                    <td class="text-right">MC订单号：</td>
                    <td>
                        <input class="form-control input-sm" type="text" name="mownecum_order_num" value="{{@$aSearchFields['mownecum_order_num']}}">
                    </td>
                    <td  class="text-right">每页条数：</td>
                    <td><select name="pagesize" style="width:100%" class="form-control select-sm"><option value="15" @if(@$aSearchFields['pagesize']==15)selected='selected' @endif>15</option><option value="30"@if(@$aSearchFields['pagesize']==30)selected='selected' @endif>30</option><option value="50"@if(@$aSearchFields['pagesize']==50)selected='selected' @endif>50</option><option value="100"@if(@$aSearchFields['pagesize']==100)selected='selected' @endif>100</option></select></td>
                    <td class="text-right">测试账户：</td>
                    <td>
                        <select name="is_tester" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">平台订单号：</td>
                    <td>
                        <input class="form-control input-sm" type="text" name="company_order_num" value="{{@$aSearchFields['company_order_num']}}">
                    </td>
                </tr>


            </table>
        </td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="text-left">
            <a class="btn btn-default" style="margin:2px;" id='submit_deposit'>搜索</a>
            <a class="btn btn-success" style="margin:2px;"  id="download_deposit">下载数据报表</a>
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

        $('#download_deposit').click(function () {
            $('#deposit_search_form').attr('action','/deposits/download');
            $('#deposit_search_form').submit();
        });

        $('#submit_deposit').click(function () {
            $('#deposit_search_form').attr('action','/deposit-histories');
            $('#deposit_search_form').submit();
        });
    });
</script>

@stop