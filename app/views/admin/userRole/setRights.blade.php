@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ __('Bind Rights') }}
@stop


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
        @if (!$readonly)
        <div class="control-group">
            <div class="controls">
                <a href="" class="btn btn-default">{{ __('Reset') }}</a>
                <button type="submit" class="btn btn-success">{{ __('Submit') }}</button>
            </div>
        </div>
        <hr>
        <div class="control-group">
            <div class="controls">
                <div class="row" style="margin:0px">
                    <label class="checkbox  pull-left  " style="margin:0px;">
                        <input type="checkbox" style="display:block;" name="checkAll" />{{ __('Select All') }}/{{ __('Cancel All') }}
                    </label>
                </div>
            </div>
        </div>
        <hr>
        @endif
        <div id="checkboxListContainer" >
        </div>
        <!-- Form actions -->
        <hr>
        @if (!$readonly)
        <div class="control-group">
            <div class="controls">
                <div class="row" style="margin:0px">
                    <label class="checkbox  pull-left " style="margin:0px;">
                        <input type="checkbox" style="display:block;" name="checkAll" />{{ __('Select All') }}/{{ __('Cancel All') }}
                    </label>
                </div>
            </div>
        </div>
        <hr>
        <div class="control-group">
            <div class="controls">
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
            $item = ['id' => $value['id'], 'level' => $value['level'], 'parent_id' => $value['parent_id'], 'forefather_ids' => $value['forefather_ids'], 'title' => $value['title'], 'controller' => $value['controller'], 'action' => $value['action'], 'desc' => __('_function.' . ($value['title'] ? $value['title'] : $value['controller'] . ' ' . $value['action']))];
            if ($readonly) {
                if (in_array($value['id'], $checked)) array_push($list, $item);
            } else {
                array_push($list, $item);
            }
        }
        $list = json_encode($list);
        $checked = json_encode($checked);
        // pr((int)$readonly);
        // pr('------------');
        // pr($checked);
        // exit;
    ?>
    <?php print("<script language=\"javascript\">var rights = $list; var checked = $checked; var readonly = $readonly; </script>\n"); ?>
    <script>
        // var rightData = [];
        // var iterateRightsTree = function (data) {

        // };

        jQuery(document).ready(function($) {
            debugger;
            var container = $('#checkboxListContainer');

            $(rights).each(function(index, el) {
                // debugger;
                var html = [];
                var id = this.id,
                    pid = this.parent_id,
                    fid = this.forefather_ids,
                    fPid = fid ? fid.split(',')[1] : 0,
                    desc = this.desc,
                    level = +this.level;
                if (level == 1 || level == 2) {
                    html.push(
                        '<div class="panel panel-default" id="#Container'+id+'">'
                        +   '<div class="panel-heading">'
                        +    '<div class="row" style="margin:0px">'
                        +    '<label class="checkbox  pull-left" style="margin:0px;" for="functionality_' + id + '">'
                        +       (readonly ? '' : '<input type="checkbox" style="display:block;" class="funCheckbox" name="functionality_id[]" id="functionality_' + id + '" forefather="' + fid + '" value="' + id + '" ' + ($.inArray(id.toString(), checked) < 0 ? '' : 'checked') + ' />')
                        +       desc
                        +    '</label>'
                        +    '<a class="glyphicon glyphicon-eject pull-right" data-toggle="collapse" data-parent="#Container'+id+'" href="#checkboxList_'+id+'"></a>'
                        +    (readonly ? '' : '<label class="checkbox pull-right" style="margin:0px; margin-right:20px;">')
                        +       (readonly ? '' : '<input type="checkbox" style="display:block;" class="checkSet" name="checkSet" id="checkSet_' + id + '" value="' + id + '" />')
                        +        (readonly ? '' : 'Select Set / Cancel Set')
                        +   (readonly ? '' : '</label>')
                        +   '</div>'
                        +   '</div>'
                        +   '<div class="panel-body  collapse in list_' + id + '" id="checkboxList_'+id+'">'
                        +   '</div>'
                        +'</div>'
                    );
                    if (level == 1)
                        container.append(html.join(''));
                    else
                        container.find('.list_' + pid).append(html.join(''));
                } else {
                    html.push(
                            '<div class="col-md-3">'
                        +    '<label class="checkbox" for="functionality_' + id + '">'
                        +       (readonly ? '' : '<input type="checkbox" style="display:block;" class="funCheckbox"  name="functionality_id[]" id="functionality_' + id + '" forefather="' + fid + '" value="' + id + '" ' + ($.inArray(id.toString(), checked) < 0 ? '' : 'checked') + ' />')
                        +       desc
                        +    '</label>'
                        +   '</div>'
                    );
                    if (level > 3)
                        container.find('.list_' + fPid).append(html.join(''));
                    else
                        container.find('.list_' + pid).append(html.join(''));
                }

            });
            if (! readonly) {
                $('input[name=checkAll]').click(function(event) {
                    var checkedStatus = this.checked;
                    container.find(':checkbox').each(function(index, el) {
                        // if (checkedStatus) $(this).attr('checked', checkedStatus);
                        // else $(this).removeAttr('checked');
                        this.checked = checkedStatus;
                    });
                });
                $('.checkSet').click(function(event) {
                    var checkedStatus = this.checked,
                        panelId = $(this).val();
                    // if (checkedStatus) $('#functionality_' + panelId).attr('checked', checkedStatus);
                    // else $('#functionality_' + panelId).removeAttr('checked');
                    document.getElementById('functionality_' + panelId).checked = checkedStatus;
                    $('#checkboxList_' + panelId).find(':checkbox').each(function(index, el) {
                        // if (checkedStatus) $(this).attr('checked', checkedStatus);
                        // else $(this).removeAttr('checked');
                        this.checked = checkedStatus;
                    });
                });
                $('.funCheckbox').click(function(event) {
                    // if (!$(this).attr('forefather')) return false;
                    var checkedStatus = this.checked;
                    if (checkedStatus) {
                        var forefatherIds = ($(this).attr('forefather')).split(',');
                        for (var i = 0, l = forefatherIds.length; i < l; i++) {
                            // $('#functionality_' + forefatherIds[i]).attr('checked', checkedStatus);
                            document.getElementById('functionality_' + forefatherIds[i]).checked = checkedStatus;
                        }
                    }
                });

                // 初始化全选和组内全选的勾选状态
                var checkedCount = $('.funCheckbox:checked').length;
                var allCount = $('.funCheckbox').length;
                if (checkedCount == allCount) $('input[name=checkAll]').attr('checked', true);
                $('.panel-body').each(function(index, el) {
                    // var allSetChecked = true;
                    var id = $(this).attr('id').split('_')[1];
                    // $(this).find('.funCheckbox').each(function(index, el) {
                    //     var checkedStatus = this.checked;
                    //     if (!checkedStatus) {
                    //         allSetChecked = false;
                    //     }
                    // });
                    var setCheckedCount = $(this).find('.funCheckbox:checked').length;
                    var setCount = $(this).find('.funCheckbox').length;
                    if (setCheckedCount == setCount)
                        document.getElementById('checkSet_' + id).checked = true;
                        // $('#checkSet_' + id).attr('checked', true);
                    // if (allSetChecked) $('#checkSet_' + id).attr('checked', allSetChecked);
                });
            }
        });
    </script>
@stop
