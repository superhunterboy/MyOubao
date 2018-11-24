@extends('l.home')

@section('title')
    游戏记录
    @parent
@stop

@section('styles')
    <style type="text/css">
        .table th a+a {margin-left: 0;}
        .table th a {text-decoration: none;}
        div.select-table-fileter {border:none;background: none;}
        div.select-table-fileter .choose-input {text-align: center;}
        div.select-table-fileter .choose-list-cont a {text-align: center;}
    </style>
    @parent
@stop



@section ('main')
    <div class="nav-inner nav-bg-tab">
        <div class="title-normal">
            游戏记录
        </div>


        @include('w.uc-menu-game')


    </div>

    <div class="content">

        @include('centerUser.bet.casino_search')
        @include('centerUser.bet.casino_search_list')


        {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
    </div>
@stop


@section('end')
    @parent
    <script>
        (function($){
            var table = $('#J-table'),
                    details = table.find('.view-detail'),
                    tip = new bomao.Tip({cls:'j-ui-tip-b j-ui-tip-page-records'}),
            // selectGameType = new bomao.Select({realDom:'#J-select-game-type',cls:'w-3'}),
            // selectMethodGroup = new bomao.Select({realDom:'#J-select-method-group',cls:'w-3'}),
            // selectMethod = new bomao.Select({realDom:'#J-select-method',cls:'w-3'}),
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


            var tableFilterSelect = new bomao.Select({
                cls:'w-2 select-table-fileter',
                realDom:$('#J-select-table-fileter')
            });
            tableFilterSelect.addEvent('change', function(e, v, text){
                var urltpl = $('#J-select-table-fileter').attr('data-urltpl').replace(/<#=status#>/g, v);
                location.href = urltpl;
            });


            /**
             setTimeout(function(){
        $(".choose-list-cont").jscroll({Btn:{btn:false}});
    }, 0);
             **/


        })(jQuery);
    </script>
@stop