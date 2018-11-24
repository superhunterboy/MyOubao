@extends('l.base-v4')


@section('title')
体育竞彩
@parent
@stop

@section ('styles')
@parent
{{ style('sports-base')}}
@stop

@section('scripts')
    {{ script('base-all') }}
    {{ script('game-all') }}
@stop

@section('start')
@parent
@include('w.header')
@stop

@section('end')
<script type="text/javascript">
(function($){
    var panel = $('#J-sports-float-help'),
        relative = $('.sports-header-container'),
        dis = 500,
        offset,
        top;
    if(relative.size() == 0){
        return;
    }
    offset = relative.offset();

    top = offset.top + 15;
    panel.css({
        left: offset.left + relative.width() + 20,
        top: top + dis,
        opacity: 0
    });
    panel.show();
    panel.animate({
        top: top,
        opacity: 1
    }, 600, 'easeInOutBack');
})(jQuery);
</script>
@stop