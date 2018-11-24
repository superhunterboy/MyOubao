@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')



<div class="col-xs-12 J-tab-chart">
    <div class="col-xs-12 J-tab-chart">
        方案ID：{{$oBet->id }}
        方案编号：{{$oBet->serial_number }}
        奖金:<span class="num" id="J-order-amount">{{ number_format($oBet->prize, 2) }}</span>元
    </div>
    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
            <tr>
                <th width="100">场次</th>
                <th width="100">赛事</th>
                <th width="150">比赛时间</th>
                <th width="300">主队VS客队</th>
                <th width="70">比分</th>
                <th width="150">玩法</th>
                <th>投注内容</th>
                <th width="50">彩果</th>
                <th width="50">胆</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $data)
            <?php $iRowSpan = count($data->method); ?>
            <tr data-match="{{$data->id}}">
                <td rowspan="{{ $iRowSpan }}">{{ $data->day }}{{ $data->match_no }}</td>
                <td rowspan="{{ $iRowSpan }}">{{ $data->league->short_name }}</td>
                <td rowspan="{{ $iRowSpan }}">{{ $data->match_time }}</td>
                <td rowspan="{{ $iRowSpan }}" class="cell-team">
                    <span class="team team-a">{{ $data->home_team->short_name }}</span>
                    <span class="ico-match-ball"></span> 
                    <span class="team team-b">{{ $data->away_team->short_name }}</span>
                </td>
                <td rowspan="{{ $iRowSpan }}">{{ $data->score }}</td>
                <?php $i = 0; ?>
                @foreach ($data->method as $method)
                @if ($i++ == 0)
                @if ($method->identifier == 'handicapWin')
                <td>{{ $method->name }}({{ $data->handicap > 0 ? '+'.$data->handicap : $data->handicap }})</td>
                @else
                <td>{{ $method->name }}</td>
                @endif
                <td>
                    <div class="col-order-table-content">
                        @foreach ($method->codeList as $oCode)
                            <span class="item-bet-detail @if($data->isFinished() && $oCode->code == $method->result) item-bet-detail-win@endif">{{ $oCode->name }}[{{ $oCode->odds }}]</span>
                        @endforeach
                    </div>
                </td>
                <td><span class="c-yellow">{{ $method->resultTitle }}</span></td>
                @endif
                @endforeach
                <td rowspan="{{ $iRowSpan }}">{{ $data->is_danma ? '√' : '' }}</td>
            </tr>
            @if ($iRowSpan > 1)
            <?php $i = 0; ?>
            @foreach ($data->method as $method)
            @if ($i++ > 0)
            <tr data-match="{{$data->id}}">
                @if ($method->identifier == 'handicapWin')
                <td>{{ $method->name }}({{ $data->handicap > 0 ? '+' . $data->handicap : $data->handicap }})</td>
                @else
                <td>{{ $method->name }}</td>
                @endif
                <td>
                    <div class="col-order-table-content">
                        @foreach ($method->codeList as $oCode)
                            <span class="item-bet-detail @if($data->isFinished() && $oCode->code == $method->result) item-bet-detail-win@endif">{{ $oCode->name }}[{{ $oCode->odds }}]</span>
                        @endforeach
                    </div>
                </td>
                <td><span class="c-yellow">{{ $method->resultTitle }}</span></td>
            </tr>
            @endif
            @endforeach
            @endif
            @endforeach
        </tbody>
    </table>
    
    <div class="col-xs-12 J-tab-chart">
        过关方式: <span class="type">@foreach($aWayList as $oWay){{$oWay->name}}@endforeach</span>
        倍数<span class="num" id="J-order-multiple">{{ $oBet->multiple }}</span>倍
        注数<span class="num" id="J-order-betnum">{{ $oBet->total }}</span>注
        金额:<span class="num" id="J-order-amount">{{ number_format($oBet->amount, 2) }}</span>元
    </div>



    <table class="table table-striped table-hover table-bordered text-center">
        <thead>
        <tr>
            <th class="text-center">方案详细</th>
            <th class="text-center">过关方式</th>
            <th class="text-center">倍数</th>
            <th class="text-center">金额</th>
            <th class="text-center">彩果</th>
            <th class="text-center">奖金</th>
        </tr>
        </thead>
        @foreach($aBetDetailList as $data)
            <tr>
                <td>{{ $data->formula }}</td>
                <td>{{ $aDetailWayList[$data->way_id]->name }}</td>
                <td>{{ $data->multiple }}</td>
                <td><span class="c-yellow">{{ $data->amount }}</span></td>
                <td>{{ $data->formatted_status }}</td>
                <td><span class="c-yellow">{{ $data->prize }}</span></td>
            </tr>
        @endforeach


    </table>
    {{ pagination($aBetDetailList->appends(Input::except('page')), 'p.slider-3') }}






</div>
@stop

@section('end')
    @parent
    <script>
        function modal(href)
        {
            $('#real-delete').attr('action', href);
            $('#myModal').modal();
        }
        {{--$.ajax({--}}
            {{--url: "{{ route($resource.'.bet-detail', $id )}}",--}}
            {{--type: 'GET',--}}
            {{--success: function(data,status){--}}
                {{--$("#detail").html('loading');--}}
                {{--if(status == 'success')--}}
                    {{--$("#detail").html(data);--}}
            {{--}--}}
        {{--});--}}
//        jQuery(document).ready(function($) {
//            var buttons = $('button');
//
//            buttons.on('click', function () {
//                $(this).toggleClass('enable');
//                var hasClass = false;
//                var str = '';
//                for(var i = 0; i < buttons.length; i++){
//                    var button = $(buttons[i]);
//                    hasClass = button.hasClass('enable');
//                    var matchId = button.attr('match-id');
//                    var betContent = button.attr('bet-content');
//                    if(button.hasClass('enable')){
//                        str += '['+matchId + '=' + '"'+betContent+'"]';
//                    }
//                }
//
//                $("#detail > table > tbody > tr").hide('slow');
//                $('tr' + str).show('slow');
//
//
//            });
//        });
    </script>
@stop