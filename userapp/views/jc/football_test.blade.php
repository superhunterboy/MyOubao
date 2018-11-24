<table border="1">
    @if (count($datas))
    <form action="/jc/confirm" method="post" onsubmit="return formsubmit()">
        <tr>
            <td colspan="10">
            @foreach ($aWayList as $way)
                <input class="way" type="checkbox" value="{{ $way->identifier }}" /> {{ $way->name }}
            @endforeach
            
                <br>
                <input name="_token" type="hidden" value="{{ csrf_token() }}" />
                <input name="gameId" type="hidden" value="9001" />
                <input name="gameData" type="hidden" value="" />
                <input name="gameExtra" type="hidden" value="" />
                倍数：<input name="betTimes" type="text" value="2" /><br>
                元角分：<input name="coefficent" type="text" value="1.00" /><br>
                <input type="submit" />
                <input type="submit" value="合买" onclick="this.form.action+='?is_group_buy=1'" />
            </td>
        </tr>
        <tr>
            <td colspan="10">
        </tr>
        @foreach ($datas as $data)
        <tr>
            <td>{{ $data->match_no }}</td>
            <td> {{ $aLeagueList[$data->league_id] }} </td>
            <td>{{ $data->match_time }}</td>
            <td>{{ $aTeamList[$data->home_id] }} vs {{ $aTeamList[$data->away_id] }}</td>
            <td>
            @foreach ($aMethodList as $method)
                <div @if (!$data->method[$method->id]->is_enable) style="background:#ccc" @endif>
                {{ $method->name }}
                @if ($data->method[$method->id]->is_single)
                (单关)
                @endif
                @if (!$data->method[$method->id]->is_enable)
                (暂未开售)
                @endif
                <br>
                @foreach (explode(',',$method->valid_nums) as $code)
                <input class="method" match_id="{{ $data->match_id }}" type="checkbox" value="{{ $code }}" /> {{ $code }}
                @endforeach
                <br>
                </div>
            @endforeach
            </td>
            <td>胆码<input class="danma" type="checkbox" match_id="{{ $data->match_id }}" /></td>
        </tr>
        @endforeach
    @else
        <tr><td colspan="10">没有符合条件的记录，请更改查询条件</td></tr>
    @endif
    </form>
</table>

@section('scripts')
    {{ script('jquery-1.9.1') }}
@show

<script>
    function formsubmit(){
        var gameData = [];
        var gameExtra = [];
        var gameMethod = {};
        var danmaData = {};
        $('.way:checked').each(function(){
            gameExtra.push($(this).val())
        });
        $('.method:checked').each(function(){
            var match_id = $(this).attr('match_id');
            gameMethod[match_id] = gameMethod[match_id] || [];
            gameMethod[match_id].push($(this).val());
        });
        $('.danma:checked').each(function(){
            var match_id = $(this).attr('match_id');
            danmaData[match_id] = danmaData[match_id] || [];
            danmaData[match_id] = $(this).val();
        });
        for( match_id in gameMethod){
            var danma = danmaData[match_id] ? 1 : 0;
            var str = match_id + ':' + gameMethod[match_id].join('.') + ':' + danma;
            gameData.push(str)
        }
        $('input[name=gameData]').val(gameData.join('+'));
        $('input[name=gameExtra]').val(gameExtra.join(','));
        console.log( $('input[name=gameData]').val())
        console.log( $('input[name=gameExtra]').val())
    }
</script>