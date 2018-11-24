@extends('l.admin', ['active' => $resource])
@section('title')
@parent
{{ $sPageTitle }}
@stop

@section('container')
    @include('w.breadcrumb')
    @include('w.notification')
    <div class="col-xs-12 J-tab-chart">
        <table class="table table-striped table-hover table-bordered text-center">
            <thead>
            <tr>
                <th class="text-center">场次</th>
                <th class="text-center">开赛时间</th>
                <th class="text-center">主队VS客队</th>
                <th class="text-center">投注方案</th>
                <th class="text-center">胆码</th>
            </tr>
            </thead>
            @if(is_array($aMatches) && isset($aMatches['matches']))
    @foreach($aMatches['matches'] as $match_id => $aMatche)
        <tr>
        <td>{{substr($match_id,8)}}</td>
                <td>{{$aMatche['match_time']}}</td>
                <td>{{$aMatche['match_teams']}}</td>
            <td >
                <table class="table table-striped table-hover table-bordered text-center">
                    <tr>
                        <th class="text-center">玩法</th>
                        <th class="text-center">选项</th>
                        <th class="text-center">彩果</th>
                    </tr>
        @foreach($aMatche['methods'] as $key => $value)
            @foreach($value as $k => $v)
            <tr>

                <td>{{$key}}</td>
                <td>
                    <button type="button" class="btn btn-primary" data-toggle="button" aria-pressed="true" autocomplete="off" match-id="{{substr($match_id,8)}}" bet-id="{{$aMatches['bet_id']}}" bet-content="{{$v['bet_content']}}">
                        {{$k}}[{{$v['odds']}}]
                    </button>
                </td>
                <td>{{__('jc/_manjcbet.' . strtolower(\Str::slug($aStatus[$v['status']])))}}</td>
            </tr>
            @endforeach
        @endforeach
                </table>
            </td>
            <td>{{isset($aMatche['is_danma']) ? yes_no($aMatche['is_danma'])  : yes_no(0)}}</td>
        </tr>
    @endforeach
                @endif
            <tr>
                <td colspan="5">
                    {{--共{{isset($aMatches['total_matches']) ? $aMatches['total_matches'] : 0 }}场--}}
                    过关方式：{{isset($aMatches['way'])?$aMatches['way']:''}}<br>
                    倍数：{{$aMatches['multiple']}} 投入：{{$aMatches['amount']}} 奖金：{{$aMatches['prize']}}
                </td>
            </tr>

        </table>
    </div>
    <div class="col-xs-12 J-tab-chart" id="detail">

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
        $.ajax({
            url: "{{ route('jc-bets.bet-detail', $id )}}",
            type: 'GET',
            success: function(data,status){
                $("#detail").html('loading');
                if(status == 'success')
                    $("#detail").html(data);
            }
        });
        jQuery(document).ready(function($) {
            var buttons = $('button');

            buttons.on('click', function () {
                $(this).toggleClass('enable');
                var hasClass = false;
                var str = '';
                for(var i = 0; i < buttons.length; i++){
                    var button = $(buttons[i]);
                    hasClass = button.hasClass('enable');
                    var matchId = button.attr('match-id');
                    var betContent = button.attr('bet-content');
                    if(button.hasClass('enable')){
                        str += '['+matchId + '=' + '"'+betContent+'"]';
                    }
                }

                $("#detail > table > tbody > tr").hide('slow');
                $('tr' + str).show('slow');


            });
        });
    </script>
@stop
