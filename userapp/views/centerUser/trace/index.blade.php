@extends('l.home')

@section('title')
            追号记录
@parent
@stop
<style type="text/css">
    .content .row {
        padding: 0 0 10px 0;
        margin: 10px 0 0 0;
        float: left;
    }
    .content .row-nav {
        /*border-bottom: 1px solid #EEE;*/
        /*padding: 0 35px;*/
        /*margin-bottom: 20px;*/
    }
    .content .row {
        /*margin-bottom: 10px;*/
    }
    .content .row:last-child {border-bottom: none;}
    .content .row .text-title {
        width: 35%;
        float: left;
        text-align: right;
        padding-right: 20px;
        padding-top: 5px;
    }
    .content .row-set-prize .input {
        padding: 5px 10px;
        font-size: 16px;
    }
    .content .row .field li {
    float: left;
    }
    .field-type-switch li {
        font-size: 12px;
    }
    .field-type-switch li a {display: inline-block;padding:10px 20px;color: #999;}
    .field-type-switch li.current {
        color: #333;
        background: #EEE;
    }
    .field-type-switch li.current a {
        color: #333;
    }
    .page-content .row-nav ul{
        width: 176px;
        height: 38px;
        margin-top: 10px;
        margin-left: 20px;
        border-radius: 4px;
        background-color: #31CEAC;
        padding: 5px 10px;
        font-size: 13px;
    }
</style>


@section ('main')

        <div class="nav-inner nav-bg-tab">
            <div class="title-normal">
                追号记录
            </div>

            
            @include('w.uc-menu-game')


        </div>

        <div class="content">
            <div class="row row-nav clearfix">
                    @if($projectType == 'lottery')
                    <ul class="field field-type-switch">
                     <li @if($current_tab == 'projects')class="current"@endif><a href="{{ route('projects.index') }}"><span>游戏记录</span></a></li>
                     <li @if($current_tab == 'traces')class="current"@endif><a href="{{ route('traces.index') }}"><span>追号记录</span></a></li>
                    </ul>
                    @endif
            </div>

            @include('centerUser.trace._search')
            @include('centerUser.trace._list')
            {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
        </div>
@stop

@section('end')
@parent
<script>
(function($){
    var table       = $('#J-table'),
        details     = table.find('.view-detail'),
        tip         = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-page-records'}),
        selectIssue = new bomao.Select({realDom:'#J-select-issue',cls:'w-2'}),
        loadMethodgroup,
        loadMethod;

    $('#J-date-start').focus(function(){
        (new bomao.DatePicker({input:'#J-date-start',isShowTime:true, startYear:2013})).show();
    });
    $('#J-date-end').focus(function(){
        (new bomao.DatePicker({input:'#J-date-end',isShowTime:true, startYear:2013})).show();
    });

    details.hover(function(e){
        var el = $(this),
            text = el.parent().find('.data-textarea').val();
        tip.setText(text);
        tip.show(-90, tip.getDom().height() * -1 - 22, el);

        e.preventDefault();
    },function(){
        tip.hide();
    });

})(jQuery);
</script>
@stop