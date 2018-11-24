@extends('l.sports')


@section ('container')
@include('jc.header')





<div class="layout-main">
    <div class="container">
        <div class="inner match-result">
            <div class="line-list-top"></div>

            <div class="list-tab">
                <ul class="list clearfix">
                    <li @if (!isset($sMethodKey))class="active"@endif><a href="{{ route('jc.result', [$oJcLottery->identifier, null, 'bet_date' => $dBetDate]) }}">全部</a></li>
                    @foreach($aMethodList as $oMethod)
                    <li @if (isset($sMethodKey) && $sMethodKey == $oMethod->identifier)class="active"@endif><a href="{{{ route('jc.result', [$oJcLottery->identifier, $oMethod->identifier, 'bet_date' => $dBetDate]) }}}">{{{ $oMethod->name }}}</a></li>
                    @endforeach
                </ul>
            </div>



            <div class="bet-confirm">
                <div class="search">
                        日期选择：
                        <input class="input input-date" name="bet_date" id="J-date-start" value="{{{ $dBetDate }}}" data-url="{{{ route('jc.result', [$oJcLottery->identifier, $sMethodKey]) }}}" />
                        &nbsp;&nbsp;
                        <span class="tip">注：表内奖金，表示固定奖金</span>
                </div>

                @if (isset($sMethodKey))
                @include('jc.result_child')
                @else
                @include('jc.result_all')
                @endif

            </div>


        </div>
    </div>
</div>
@include('w.footer')
@stop




@section('end')
@parent
<script>
(function($, host){
    $('#J-date-start').focus(function(){
        var dateStart = new host.DatePicker({input:'#J-date-start', startYear:2013});
        dateStart.show();
        dateStart.addEvent('afterSetValue', function(e){
            var dom = $('#J-date-start'),
                date = dom.val(),
                url = dom.attr('data-url');
            location.href = url + '?bet_date=' + date;
        });
    });



        

})(jQuery, bomao);
</script>
@stop





















