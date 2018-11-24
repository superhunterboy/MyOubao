@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $resourceName . __('Management') }}
@stop
@section('container')

@include('w.breadcrumb')
@include('w.notification')
<div class="row">
    <div class="col-xs-4">
        <div class="h1" style="line-height: 32px;">{{ __('Generate ') . $resourceName }}</div>
    </div>
    <div class="col-xs-8">
        <div class="pull-right">
            <a href="{{ route($resource.'.index') }}" class="btn btn-sm btn-default">
                &laquo; {{ __('Return') . $resourceName . __('List') }}
            </a>
        </div>
    </div>
</div>
<hr>
<div class="col-md-12 clearfix" style=" margin-bottom:20px;">



    <form name="rightsSettingForm" method="post" action="{{ route($resource.'.set-ways') }}" autocomplete="off">
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
            @foreach($lottery_ways as $lotterys)
            <div class="panel panel-default" id="#Container {{$lotterys['id']}}">
                <div class="panel-heading">
                    <div class="row" style="margin:0px">
                        <label class="checkbox  pull-left" style="margin:0px;" for="functionality_ {{$lotterys['id']}}">
                            <input type="checkbox" style="display:block;" class="funCheckbox" name="lottery_ids[]" id="functionality_{{$lotterys['id']}}"  value="{{$lotterys['id']}}" />
                            {{$lotterys['name']}}
                        </label>
                        <a class="glyphicon glyphicon-eject pull-right" data-toggle="collapse" data-parent="#Container_{{$lotterys['id']}}" href="#checkboxList_{{$lotterys['id']}}"></a>
                        <label class="checkbox pull-right" style="margin:0px; margin-right:20px;">
                            <input type="checkbox" style="display:block;" class="checkSet" name="checkSet"  id="checkSet_{{$lotterys['id']}}" value="{{$lotterys['id']}}" />
                            Select Set / Cancel Set
                        </label>
                    </div>
                </div>
                <div class="panel-body  collapse in list_{{$lotterys['id']}}" id="checkboxList_{{$lotterys['id']}}">

                    @foreach($lotterys['ways'] as $ways)
                    <div class="col-md-3">
                        <label class="checkbox" for="functionality_{{$lotterys['id']}}">
                            <input type="checkbox" style="display:block;" class="funCheckbox"  forefather="{{$lotterys['id']}}" name="lottery_ids[{{$lotterys['id']}}][]" id="functionality_" 
                                   @if(isset($aChecked[$lotterys['id']][$ways['id']]))
                                    checked="checked" 
                                   @endif
                                   value="{{$ways['id']}}"/>
                            {{$ways['name']}}
                        </label>
                    </div>
                    @endforeach
                </div>

            </div>
            @endforeach
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
    $readonly = (int) $readonly;
//        foreach ($lottery_ways as $key => $value) {
//            $item = ['id' => $value['id'], 'level' => $value['level'], 'parent_id' => $value['parent_id'], 'forefather_ids' => $value['forefather_ids'], 'title' => $value['title'], 'controller' => $value['controller'], 'action' => $value['action'], 'desc' => __('_function.' . ($value['title'] ? $value['title'] : $value['controller'] . ' ' . $value['action']))];
//            if ($readonly) {
//                if (in_array($value['id'], $checked)) array_push($list, $item);
//            } else {
//                array_push($list, $item);
//            }
//        }
    $checked = [];
    $list = json_encode($lottery_ways);
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

        jQuery(document).ready(function ($) {
            debugger;
            var container = $('#checkboxListContainer');

          
                $('input[name=checkAll]').click(function (event) {
                    var checkedStatus = this.checked;
                    container.find(':checkbox').each(function (index, el) {
                        // if (checkedStatus) $(this).attr('checked', checkedStatus);
                        // else $(this).removeAttr('checked');
                        this.checked = checkedStatus;
                    });
                });
                $('.checkSet').click(function (event) {
                    var checkedStatus = this.checked,
                            panelId = $(this).val();
                    // if (checkedStatus) $('#functionality_' + panelId).attr('checked', checkedStatus);
                    // else $('#functionality_' + panelId).removeAttr('checked');
                    document.getElementById('functionality_' + panelId).checked = checkedStatus;
                    $('#checkboxList_' + panelId).find(':checkbox').each(function (index, el) {
                        // if (checkedStatus) $(this).attr('checked', checkedStatus);
                        // else $(this).removeAttr('checked');
                        this.checked = checkedStatus;
                    });
                });
                $('.funCheckbox').click(function (event) {
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
                if (checkedCount == allCount)
                    $('input[name=checkAll]').attr('checked', true);
                $('.panel-body').each(function (index, el) {
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
        });
    </script>
    @stop