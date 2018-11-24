@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Bind Rights') }}
@stop

<?php
$sSelectedAllText = __('_basic.select all') . ' / ' . __('_basic.cancel all');
?>
@section('container')
    @include('w.breadcrumb')
    @include('w.notification')

    <div class="row">
      <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('Bind Rights For Role ' . $sRoleName) }} </div>
      </div>
      <div class="col-xs-8">
        <div class="pull-right">
            <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                {{ __('Return') . $resourceName . __('List') }}
            </a>
        </div>
      </div>
    </div>
    <hr>

    <form name="rightsSettingForm" method="post" action="{{ route($resource.'.set-rights', $role_id) }}" autocomplete="off"> 
        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
        
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="t-title">
                    @if (!$readonly)
                    <input type="checkbox" name="checkAll" />
                    @endif
                    所有权限
                </div>
                <div class="t-body" id ="t-box">

                </div>

            </div>
        </div>

        @if (!$readonly)
        <div class="col-xs-6">
            <div class="panel panel-default">
                <div class="t-title">
                    已选权限
                </div>
                <div class="t-body" id ="f-box">

                </div>

            </div>
        </div>
        <div class="co-xs-12">
            <div class="controls text-center">
                <a href="" class="btn btn-default">{{ __('Reset') }}</a>
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
        @endif
    </form>

@stop

@section('end')
    @parent
    <?php
        $list = [];
        $readonly = (int)$readonly;
        foreach ($datas as $key => $value) {
            $sTitleKey = '_function.' . strtolower($value['title'] ? $value['title'] : $value['controller'] . ' ' . $value['action']);
            $item      = ['id' => $value[ 'id' ],'level' => $value[ 'level' ],'parent_id' => $value[ 'parent_id' ],'forefather_ids' => $value[ 'forefather_ids' ],'title' => $value[ 'title' ],'controller' => $value[ 'controller' ],'action' => $value[ 'action' ],'desc' => __($sTitleKey,[],3)];
    if ($readonly) {
                if (in_array($value['id'], $checked)) array_push($list, $item);
            } else {
                array_push($list, $item);
            }
        }
        $list = json_encode($list);
        $checked = json_encode($checked);
    ?>
    <?php print("<script language=\"javascript\">var rights = $list; var checked = $checked; var readonly = $readonly; </script>\n"); ?>
    <script>

        jQuery(document).ready(function($) {

            var container = $('#t-box');

            $(rights).each(function(index, el) {
                // debugger;
                var html  = [],
                    id    = this.id,
                    pid   = this.parent_id,
                    fid   = this.forefather_ids,
                    fPid  = fid ? fid.split(',')[1] : 0,
                    desc  = this.desc,
                    level = +this.level;
                if (level == 1 || level == 2) {


                    html.push(
                        '<div class="t-box" id="#Container'+id+'">'
                        +   '<div class="t-t">'
                        +       (readonly ? '' : '<input type="checkbox" name="functionality_id[]" class="checkSet" liVal="'+level+'" lId ="'+id+'" flId ="'+fid+'" id="functionality_' + id + '" forefather="' + fid + '" value="' + id + '" nameVal="'+desc+'" ' + ($.inArray(id.toString(), checked) < 0 ? '' : 'checked') + ' /> ')
                        +        desc
                        +    '<a class="glyphicon glyphicon-tasks pull-right" data-toggle="collapse" data-parent="#Container'+id+'" href="#checkboxList_'+id+'"></a>'
                        +    '</div>'
                        +   '<div class="t-s collapse '+ (readonly ? 'in' : '') + ' list_'+id + '" id="checkboxList_'+id+'" >'

                        +   '</div>'
                        +'</div>'


                    );

                    if (level == 1)
                        container.append(html.join(''));
                    else
                        container.find('.list_' + pid).append(html.join(''));
                } else {
                    html.push(

                        '<span class="t-label label-warning">'
                        +  (readonly ? '' : '<input type="checkbox" class="funCheckbox" liVal="3" lId ="'+fPid+'" name="functionality_id[]" id="functionality_' + id + '" forefather="' + fid + '" value="' + id + '"  nameVal="'+desc+'" ' + ($.inArray(id.toString(), checked) < 0 ? '' : 'checked') + ' /> ')
                        +        desc
                        +'</span>'
                    );
                    if (level > 3)
                        container.find('.list_' + fPid).append(html.join(''));
                    else
                        container.find('.list_' + pid).append(html.join(''));
                }

            });
            if (! readonly) {
                var checkFun = function(){
                    $('#f-box').html('');
                    container.find(':checkbox').each(function(index, val) {
                        var i=[];
                        var cl=$(this).attr('liVal'),na = $(this).attr('nameVal'),lId=$(this).attr('lId'),flId =$(this).attr('flId');
                        
                            if( this.checked && cl == '1'){
                                i.push(
                                     '<span class="t-t1">'+ na+'</span><div class="listU listU_'+lId+'"></div>'
                                )
                                $('#f-box').append(i.join(''));
                            }else if(this.checked & cl == '2'){
                                i.push(
                                   '<span class="t-t2">'+ na +'</span><div class="listU listU_'+lId+'"></div>'
                                )
                                $('#f-box').find('.listU_'+flId ).append(i.join(''));
                            }else if(this.checked & cl == '3'){
                                i.push(
                                   '<span class="t-t3 ">'+ na+'</span>'
                                )
                                $('#f-box').find('.listU_'+lId ).append(i.join(''));
                            
                            }
                            
                                
                    });
                    
                };



                $('input[name=checkAll]').click(function(event) {
                    var checkedStatus = this.checked;
                    container.find(':checkbox').each(function(index, el) {
                        this.checked = checkedStatus;
                    });
                    checkFun();
                });
                $('.checkSet').click(function(event) {
                    var checkedStatus = this.checked,
                        panelId = $(this).val(),
                        forefatherId =$(this).attr('forefather');
                    document.getElementById('functionality_' + panelId).checked = checkedStatus;
                    if(forefatherId){
                        document.getElementById('functionality_' + forefatherId).checked = checkedStatus;
                    }
                    $('#checkboxList_' + panelId).find(':checkbox').each(function(index, el) {
                        this.checked = checkedStatus;
                    });
                    checkFun();
                });
                $('.funCheckbox').click(function(event) {
                    var checkedStatus = this.checked;
                    if (checkedStatus) {
                        var forefatherIds = ($(this).attr('forefather')).split(',');
                        for (var i = 0, l = forefatherIds.length; i < l; i++) {
                            // $('#functionality_' + forefatherIds[i]).attr('checked', checkedStatus);
                            document.getElementById('functionality_' + forefatherIds[i]).checked = checkedStatus;
                        }
                    }
                    checkFun();
                });

                // 初始化全选和组内全选的勾选状态
                var checkedCount = $('.funCheckbox:checked').length;
                var allCount = $('.funCheckbox').length;
                if (checkedCount == allCount) $('input[name=checkAll]').attr('checked', true);
                $('.panel-body').each(function(index, el) {
                    var id = $(this).attr('id').split('_')[1];
                    var setCheckedCount = $(this).find('.funCheckbox:checked').length;
                    var setCount = $(this).find('.funCheckbox').length;
                    if (setCheckedCount == setCount)
                        document.getElementById('checkSet_' + id).checked = true;
                });
                checkFun();
            }
         });
    </script>
@stop
