@extends('l.admin', array('active' => $resource))

@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    @include('w._function_title')

    @include('agent.BonusRule.detailForm')

@stop

@section('end')
     {{ script('bootstrap-switch') }}
    @parent

    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }


        (function($){
            $('#J-button-addrow').click(function(){
                $($('#J-addrow-tpl').html()).appendTo($('#J-tbody'));
            });

        })(jQuery);



    </script>
@stop
