@extends('l.home')

@section('title') 
    配额管理
@stop


@section ('styles')
@parent
    {{ style('proxy-global') }}
    {{ style('proxy') }}
<style type="text/css">
.table th {

    padding: 10px;
    font-size: 14px;
    border-right: 1px solid #94C4AD;
    color: #262732;
    background-color: #DAEBE9;
    /*background-image: linear-gradient(-180deg, #F2F2F2 19%, #E8E8E8 92%);*/
}
.area-search {
    background: #FFF;
    border: 1px solid #CCC;
    border-bottom: none;
    color: #333;
}
.area-search .filter-cont {
    background:none;
    margin-bottom: 10px;
    padding: 0;
    border: none;
}
.area-search .filter-list li a {
    display: block;
    padding: 5px 20px;
    background-color: #FFF;
    border: 1px solid #D0D0D0;
    margin-right: 4px;
    border-radius: 5px;
    background-image: linear-gradient(-180deg, #F9F9F9 19%, #CCCCCC 92%);
}
</style>
@stop



@section ('container')

    @include('w.header')


    <div class="banner">
        <img src="/assets/images/proxy/banner.jpg" width="100%" />
    </div>




    <div class="page-content">
        <div class="g_main clearfix">
            @include('w.manage-menu')

            <div class="nav-inner clearfix">
                @include('w.uc-menu-proxy')
            </div>


        
            <div class="page-content-inner"> 


                <table width="100%" class="table">
                    <thead>
                        <tr>
                            <th>我的高点配额</th> 
                             @foreach($quotas as $key=>$val)
                             <th>
                                 {{$key}}
                             </th>
                             @endforeach
                        </tr>
                    </thead>
                    <tbody>
                     <tr>
                        <td>已用</td> 
                         @foreach($quotas as $key=>$val)
                         <td>
                             {{$val['used_num']}}
                         </td>
                         @endforeach
                    </tr>
                     <tr>
                        <td>未用</td> 
                         @foreach($quotas as $key=>$val)
                         <td>
                             {{$val['limit_num']-$val['used_num']}}
                         </td>
                         @endforeach
                    </tr>
                     <tr>
                        <td>总量</td> 
                         @foreach($quotas as $key=>$val)
                         <td>
                             {{$val['limit_num']}}
                         </td>
                         @endforeach
                    </tr>
                    <tbody>
                </table>




                <br />
                <br />
                <div class="area-search">
                <form action="{{route('my-overlimit-quotas.index')}}" method="get">
                    <div class="filter-cont clearfix">
                        <ul class="filter-list clearfix">
                            <li 
                                @if($sCurrentTab == 'all')
                                class="current"
                                @endif
                                ><a href="?">全部</a></li>
                            @foreach($quotas as $quota)
                            <li
                                 @if($sCurrentTab == $quota['prize_group'])
                                 class="current"
                                 @endif
                                ><a href="{{route('my-overlimit-quotas.index')}}?prize_group={{$quota['prize_group']}}">{{$quota['prize_group']}}</a>
                            </li>
                            @endforeach
                        </ul>
                        <div style="padding:5px 0 0 20px;float:right;">
                            用户名：<input name="username" style="padding:4px 5px;" type="text" value="" class="input w-2" /> <input style="padding:0 20px;" type="submit" class="btn" value="搜索" />
                        </div>
                    </div>
                </form>
                </div>

                <table width="100%" class="table table-highpoint">
                    <thead>
                        <tr>
                            <th rowspan="2">下级代理</th>
                            <th rowspan="2">永久奖金组</th>
                            <th rowspan="2">临时奖金组</th>
                            <th colspan="{{count($quotas)}}" style="border-bottom:0;">
                                管理下级配额
                            </th>
                        </tr>
                        <tr>
                            @foreach($quotas as $quota)
                                <th> {{$quota['prize_group']}}</th>
                            @endforeach
                            
                        </tr>
                    </thead>
                    <tbody id="J-table-tbody">
                        @foreach ($aSubUsers as $user_id => $value)
                        <tr>
                            <td><a href="{{route('user-user-prize-sets.set-prize-set',$user_id)}}">{{$value['info']['username']}}</a></td>
                            <td>
                                @if($value['info']['forever_prize_group'])
                                    {{$value['info']['forever_prize_group']}}
                                @endif
                            </td>
                            <td>@if($value['info']['prize_group']!=$value['info']['forever_prize_group'])
                                    {{$value['info']['prize_group']}}
                                @else
                                    -
                                @endif
                            </td>

                            
                            @foreach($quotas as $quota)
                            <td>
                            @if($value['quotas'][$quota['prize_group']]['editable'])
                                <div class="point-cont" data-userid="{{$user_id}}" data-group="{{$quota['prize_group']}}">
                                    <span class="num">
                                        {{$value['quotas'][$quota['prize_group']]['value']}}
                                    </span>
                                    <span class="edit">编辑</span>
                                </div>
                            @else
                                <div>
                                    <span class="num">
                                        {{$value['quotas'][$quota['prize_group']]['value']}}
                                    </span>
                                </div>
                            @endif


                            </td>    
                            @endforeach
                        </tr>
                        @endforeach
                       
                    </tbody>
                </table>



                
            </div>
        </div>
    </div>
<script type="text/template" id="J-tpl-edit">
<div class="proxy-wd-point-cont">
    <form id="J-form-wd" action="{{route('my-overlimit-quotas.save')}}" method="post">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
    <input name="prize_group" type="hidden" value="<#=prize_group#>" />
    <input name="user_id" type="hidden" value="<#=user_id#>" />
    <input name="username" type="hidden" value="<#=username#>" />
    <table width="100%" cellpadding="10" class="table-point-window">
        <tr>
            <td align="right" width="150">用户：</td>
            <td><#=username#></td>
            <td rowspan="4">
                <div style="text-align:left;padding-left:10px;">
                    调整理由:
                    <br />
                    <textarea maxlength="100" name="note" class="textarea" placeholder="100字以内"></textarea>
                </div>
            </td>
        </tr>
        <tr>
            <td align="right" width="150">当前奖金组：</td>
            <td><#=prize_group#></td>
        </tr>
        <tr>
            <td align="right">拥有配额：</td>
            <td>
                <span class="num"><#=point_have#></span>
                &nbsp;&nbsp;
                <select id="J-select-add-num" name="plus_num" <#=sel_add_dis#>>
                    <#=select_addlist#>
                </select>
                <span style="font-size:12px;">可用: <#=mymax#></span>
            </td>
        </tr>
        <!--
        <tr>
            <td align="right">增加：</td>
            <td>
                <input name="plus_num" data-mymax="<#=mymax#>" id="J-input-wd-num-add" type="text" value="0" class="input input-num-add" />
                &nbsp;( 可用:<#=mymax#> )
            </td>
        </tr>
        -->
        <tr>
            <td align="right">未使用配额：</td>
            <td>
                <span class="num"><#=point_notuse#></span>
                &nbsp;&nbsp;
                <select id="J-select-back-num" name="subtract_num" <#=sel_back_dis#>>
                    <#=select_backlist#>
                </select>
            </td>
        </tr>
        <!--
        <tr>
            <td align="right">回收：</td>
            <td>
                <input name="subtract_num" data-usermax="<#=point_notuse#>" id="J-input-wd-num-reduce" type="text" value="0" class="input input-num-reduce" />
            </td>
        </tr>
        <tr>
            <td valign="top" align="right">调整理由：</td>
            <td>
                <textarea name="note" class="textarea"></textarea>
            </td>
        </tr>
        -->
    </table>
    </form>
        
    <div class="history-cont">
        <table width="100%">
        <thead>
            <tr>
                <th>历史变化</th>
                <th>新增</th>
                <th>减少</th>
                <th>时间</th>
                <th width="300">理由</th>
            </tr>
        </thead>
        <tbody>
            <#=list#>
        </tbody>
        </table>
    </div>
</div>
</script>
<script type="text/template" id="J-tpl-edit-list">
            <tr>
                <td><#=change#></td>
                <td><#=add#></td>
                <td><#=reduce#></td>
                <td><#=time#></td>
                <td><span class="small"><#=reason#></span><input value="<#=reason#>" type="hidden" /></td>
            </tr>
</script>

    @include('w.footer')
@stop



@section('end')
@parent
<script>
(function($){
    var wd = bomao.Message.getInstance({cls:'high-point-wd'}),mask = bomao.Mask.getInstance(),
        points = $('#J-table-tbody .point-cont'),tpl = $('#J-tpl-edit').html(),listtpl = $('#J-tpl-edit-list').html();
    points.click(function(){
        var el = $(this),uid = Number(el.attr('data-userid')),group = Number(el.attr('data-group'));
        $.ajax({
            url:'/my-overlimit-quotas/get-quota-and-history?user_id='+uid+'&prize_group='+group,
            success:function(data){
                //console.log(data);
                if(Number(data['isSuccess']) == 1){
                var uinfo = data['data']['userQuota'];
                var idata = {
                    'username':uinfo['username'],
                    'prize_group':uinfo['prize_group'],
                    'point_have':uinfo['limit_num'],
                    'point_notuse':uinfo['limit_num'] - uinfo['used_num'],
                    'mymax':uinfo['parent_limit_num'] - uinfo['parent_used_num'],
                    'user_id':uinfo['user_id'],
                    'note':uinfo['note']
                },
                datalist = [],
                listStr = [];

                //下拉框
                var addList = ['<option value="0">增加</option>'],i = 0,len;
                for(i = 0,len = idata['mymax']; i < len; i++){
                    addList.push('<option value="'+ (i+1) +'">'+(i+1)+'</option>');
                }
                idata['sel_add_dis'] = '';
                if(addList.length < 2){
                    //idata['sel_add_dis'] = ' disabled="disabled"';
                }
                idata['select_addlist'] = addList.join('');
                var backList = ['<option value="0">回收</option>'];
                for(i = 0,len = idata['point_notuse']; i < len; i++){
                    backList.push('<option value="'+ (i+1) +'">'+(i+1)+'</option>');
                }
                idata['sel_back_dis'] = '';
                if(backList.length < 2){
                    //idata['sel_back_dis'] = ' disabled="disabled"';
                }
                idata['select_backlist'] = backList.join('');



                $.each(data['data']['history'], function(){
                    datalist.push({'change':this['prize_group'], 'add':this['plus_num'], 'reduce':this['subtract_num'], 'time':this['created_at'], 'reason':this['note']});
                });

                $.each(datalist, function(i){
                    listStr[i] = bomao.util.template(listtpl, this);
                });
                tpl = tpl.replace(/<#=list#>/g, listStr.join(''));
                var template = bomao.util.template(tpl, idata);

                mask.show();
                wd.show({
                    'title':'调整下级配额',
                    'isShowMask':true,
                    'confirmIsShow':true,
                    'confirmText':' 保 存 ',
                    'tpl':template,
                    confirmFun:function(){
                        $('#J-form-wd').submit();
                    }
                });


                }else{
                    alert(data['msg']);
                }
            }
        });
    });

    /**
    var tip = new bomao.Tip({cls:'j-ui-tip-b'});
    tip.dom.css({zIndex:900});
    $(document).on('mouseover', '.small', function(){
        var el = $(this);
        tip.setText(el.parent().find('input').val());
        tip.show(tip.dom.width()/2 - el.width()/2, el.height()*-1 - 19, this);
    });
    $(document).on('mouseout', '.small', function(){
        tip.hide();
    });
    **/



    $(document).on('keyup', '.input-num-reduce,.input-num-add', function(){
        var el = $(this),v = this.value.replace(/[^\d]/g, ''),limit;
        if(v == ''){
            v = 0;
        }
        v = Number(v);
        if(el.hasClass('input-num-add')){
            limit = Number(el.attr('data-mymax'));
            v = v >= limit ? limit : v;
        }else if(el.hasClass('input-num-reduce')){
            limit = Number(el.attr('data-usermax'));
            v = v >= limit ? limit : v;
        }
        this.value = Number(v);
    });


})(jQuery);
</script>
@stop


