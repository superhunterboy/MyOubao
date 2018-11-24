{{ Form::open(array('method' => 'get', 'class' => 'form-inline', 'style' => 'background: #F8F8F8;margin-bottom: 5px;text-align: left;padding: 5px;margin-top: -20px;','id'=>'transaction_search_form')) }}
<input id="download_flag" name="download_flag"  value="" type="hidden" />
<table style="width:100%"><tr>
        <td style="width:180px;">
            <label for="controller" class="text-right">{{__('_transaction.transaction_type')}}：</label>
            <div class="form-group">
                <select name="type_id[]" size="7" multiple="multiple" class="form-control select-sm" style=" height: auto; width:120px;-webkit-appearance:inherit;">
                    <option value  @if(!isset($aSearchFields['type_id']) ||empty($aSearchFields['type_id'][0]))selected='selected' @endif>{{__('_transaction.all_transaction_types')}}..</option>
                    @foreach ($aTransactionTypes as $id=>$name)
                    <option value="{{$id}}" @if(@in_array($id,$aSearchFields['type_id']))selected='selected' @endif>{{$name}}</option>
                    @endforeach
                </select>
            </div>
        </td>
        <td style="width:980px">
            <table>
                <tr>
                    <td style="width:80px" class="text-right">{{__('_transaction.serial_number_short')}}：</td>
                    <td style="width:140px"><input class="form-control input-sm" type="text" name="serial_number" value="{{@$aSearchFields['serial_number']}}"></td>
                    <td style="width:80px" class="text-right">{{__('_transaction.project_no')}}：</td>
                    <td style="width:140px"><input class="form-control input-sm" type="text" name="project_no" value="{{@$aSearchFields['project_no']}}"></td>
                    <td style="width:80px" class="text-right">{{__('_transaction.ip')}}：</td>
                    <td style="width:140px"><input class="form-control input-sm" type="text" name="ip" value="{{@$aSearchFields['ip']}}"></td>
                    <td style="width:80px" class="text-right">{{__('_transaction.admin_user_id')}}：</td>
                    <td style="width:140px"><select name="admin_user_id" style="width:100%" class="form-control select-sm"><option value>{{__('_transaction.all')}}</option>@foreach($aAdminUsers as $id => $name)<option value="{{$id}}" @if(@$aSearchFields['admin_user_id']==$id)selected='selected' @endif>{{$name}}</option>@endforeach</select></td>
                </tr>
                <tr>
                    <td class="text-right">{{__('_transaction.created_at')}}：</td>
                    <td  >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at[]" value="{{@$aSearchFields['created_at'][0]}}" >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">{{__('_transaction.to')}}：</td>
                    <td >
                        <div class="input-group date form_date" style="width:100%" data-date="" data-date-format="yyyy-mm-dd hh:ii">
                            <input class="form-control" size="16" type="text" name="created_at[]" value="{{@$aSearchFields['created_at'][1]}}"  >
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                        </div>
                    </td>
                    <td class="text-right">{{__('_transaction.amount')}}：</td>
                    <td >
                        <input class="form-control input-sm" type="text" style="width:40px;" name="amount[]" value="{{@$aSearchFields['amount'][0]}}"> {{__('_transaction.to')}}
                        <input class="form-control input-sm" type="text" style="width:40px;" name="amount[]" value="{{@$aSearchFields['amount'][1]}}">
                    </td>
                </tr>
                <tr>
                    <td class="text-right">{{__('_transaction.user-search')}}：</td>
                    <td>
                        <select name="user_search_type" style="width:100%" class="form-control select-sm j-select">
                            <option value="1"@if(@$aSearchFields['user_search_type']==1)selected='selected' @endif>{{__('_transaction.manual-input')}}</option>
                            <option value="2"@if(@$aSearchFields['user_search_type']==2)selected='selected' @endif>{{__('_transaction.topagent-list')}}</option>
                        </select>
                    </td>
                    <td colspan="2" class="j-none"@if(isset($aSearchFields['user_search_type'])&&$aSearchFields['user_search_type']==2)style="display: none;"@endif>
                        <div class="text-right" style="width:80px; float:left">{{__('_transaction.username')}}：</div>
                        <div class="form-group">
                            <input style="float:left;width:80px;" class="form-control input-sm" type="text" name="username" value="{{@$aSearchFields['username']}}">

                            <label style="float:left; margin-left:10px;" >
                                <input name="un_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['un_include_children']==1)checked @endif>{{__('_transaction.children')}}
                            </label>
                        </div>
                    </td>
                    <td colspan="2" class="j-none" @if(!isset($aSearchFields['user_search_type'])or$aSearchFields['user_search_type']==1)style="display: none;"@endif>
                        <div class="text-right" style="width:80px; float:left">{{__('_transaction.topagent')}}：</div>
                        <div class="form-group">
                            <select name="root_agent" style="float:left;width:100px;" class="form-control select-sm">@foreach($aRootAgent as $id =>$name) <option value="{{$name}}"@if(@$aSearchFields['root_agent']==$name)selected='selected' @endif>{{$name}}</option> @endforeach</select>
                            <label style="float:left; margin-left:10px;">
                                <input name="ra_include_children" type="checkbox" name="sel" value="1"@if(@$aSearchFields['ra_include_children']==1)checked @endif>{{__('_transaction.children')}}
                            </label>
                            <label style="float:left; margin-left:10px;">
                                <input name="ra_exclude_self"  type="checkbox" name="sel" value="1"@if(@$aSearchFields['ra_exclude_self']==1)checked @endif>{{__('_transaction.exclude-topagent')}}
                            </label>
                        </div>
                    </td>
                    <td class="text-right">测试用户：</td>
                    <td>
                        <select name="is_tester" style="width:100%" class="form-control select-sm">
                            <option value>不限</option>
                            <option value="1" {{ @$aSearchFields['is_tester'] === '1' ? 'selected' : '' }}>是</option>
                            <option value="0" {{ @$aSearchFields['is_tester'] === '0' ? 'selected' : '' }}>否</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="text-right">{{__('_transaction.game-transaction')}}：</td>
                    <td>
                        <select name="lottery_id" style="width:100%" class="form-control select-sm"> <option value>{{__('_transaction.all')}}</option> <option value='NULL' @if(@$aSearchFields['lottery_id']=='NULL')selected='selected' @endif>{{__('_transaction.not-game-transaction')}}</option>@foreach($aLotteries as $id =>$name) <option value="{{$id}}"@if(@$aSearchFields['lottery_id']==$id)selected='selected' @endif>{{$name.__('_model.transaction')}}</option> @endforeach</select>
                    </td>
                    <td class="text-right">{{__('_transaction.coefficient')}}：</td>
                    <td >
                        <select name="coefficient" style="width:100%" class="form-control select-sm"><option value>{{__('_transaction.all')}}</option>@foreach($aCoefficients as $id=>$value)<option value="{{$id}}" @if(@$aSearchFields['coefficient']==$id)selected='selected' @endif>{{$value}}</option>@endforeach</select>
                    </td>
                    <td  class="text-right">{{__('_transaction.pagesize')}}：</td>
                    <td><select name="pagesize" style="width:100%" class="form-control select-sm"><option value="15" @if(@$aSearchFields['pagesize']==15)selected='selected' @endif>15</option><option value="30"@if(@$aSearchFields['pagesize']==30)selected='selected' @endif>30</option><option value="50"@if(@$aSearchFields['pagesize']==50)selected='selected' @endif>50</option><option value="100"@if(@$aSearchFields['pagesize']==100)selected='selected' @endif>100</option></select></td>

                </tr>
            </table>
        </td>
        <td class="text-left">
            <a class="btn btn-default" style="margin:2px;" id="submit_transaction">{{__('_transaction.search')}}</a>
            <a class="btn btn-success" style="margin:2px;" id="download_transaction">{{__('_transaction.download')}}</a>
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
                $('.j-none').hide().eq('0').show();
            } else {
                $('.j-none').hide().eq('1').show();
            }
        });

        $('#download_transaction').click(function () {
            $('#transaction_search_form').attr('action','/transactions/download');
            $('#transaction_search_form').submit();
        });

        $('#submit_transaction').click(function () {
            $('#transaction_search_form').attr('action','/transactions');
            $('#transaction_search_form').submit();
        });
        
    });
</script>

@stop