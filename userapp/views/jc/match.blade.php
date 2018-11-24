
<table class="table">
    <thead>
        <tr>
            <th width="100">场次</th>
            <th width="100">赛事</th>
            <th width="150">比赛时间</th>
            <th width="300">主队VS客队</th>
            @if ($oBet->isEnd)
            <th width="70">比分</th>
            @endif
            <th width="150">玩法</th>
            <th>投注内容</th>
            @if ($oBet->isEnd)
            <th width="50">彩果</th>
            @endif
            <th width="50">胆</th>
        </tr>
    </thead>
    <tbody>
        @if (isset($oGroupBuy) && empty($bDisplayBet))
        <tr>
            <td colspan="10">
                <div class="noresult-tip">
                    该方案已设置为{{{ $oGroupBuy->formatted_show_type }}}
                </div>
            </td>
        </tr>
        @else
        @foreach ($datas as $data)
        <?php $iRowSpan = count($data->method); ?>
        <tr data-match="{{{ $data->id }}}">
            <td rowspan="{{{ $iRowSpan }}}">{{{ $data->day }}}{{{ $data->match_no }}}</td>
            <td rowspan="{{{ $iRowSpan }}}">{{{ $data->league->short_name }}}</td>
            <td rowspan="{{{ $iRowSpan }}}">{{{ $data->match_time }}}</td>
            <td rowspan="{{{ $iRowSpan }}}" class="cell-team">
                <span class="team team-a">{{{ $data->home_team->short_name }}}</span>
                <img class="ico-match-home-time" src="{{{ $data->home_team->icon_url }}}">
                &nbsp;
                <span class="ico-match-ball">VS</span>
                &nbsp;
                <img class="ico-match-visiting-time" src="{{{ $data->away_team->icon_url }}}">
                <span class="team team-b">{{{ $data->away_team->short_name }}}</span>
            </td>
            @if ($oBet->isEnd)
            <td rowspan="{{{ $iRowSpan }}}">{{{ $data->score }}}</td>
            @endif
            <?php $i = 0; ?>
            @foreach ($data->method as $method)
            @if ($i++ == 0)
            @if ($method->identifier == 'handicapWin')
            <td>{{{ $method->name }}}({{{ $data->handicap > 0 ? '+'.$data->handicap : $data->handicap }}})</td>
            @else
            <td>{{{ $method->name }}}</td>
            @endif
            <td>
                <div class="col-order-table-content">
                    @foreach ($method->codeList as $oCode)
                        <span class="item-bet-detail @if($data->isFinished() && $oCode->code == $method->result) item-bet-detail-win@endif">{{{ $oCode->name }}}[{{{ $oCode->odds }}}]</span>
                    @endforeach
                </div>
            </td>
            @if ($oBet->isEnd)
            <td><span class="c-yellow">{{{ $method->resultTitle }}}</span></td>
            @endif
            @endif
            @endforeach
            <td rowspan="{{{ $iRowSpan }}}">{{{ $data->is_danma ? '√' : '' }}}</td>
        </tr>
        @if ($iRowSpan > 1)
        <?php $i = 0; ?>
        @foreach ($data->method as $method)
        @if ($i++ > 0)
        <tr data-match="{{{ $data->id }}}">
            @if ($method->identifier == 'handicapWin')
            <td>{{{ $method->name }}}({{{ $data->handicap > 0 ? '+' . $data->handicap : $data->handicap }}})</td>
            @else
            <td>{{{ $method->name }}}</td>
            @endif
            <td>
                <div class="col-order-table-content">
                    @foreach ($method->codeList as $oCode)
                        <span class="item-bet-detail @if($data->isFinished() && $oCode->code == $method->result) item-bet-detail-win@endif">{{{ $oCode->name }}}[{{{ $oCode->odds }}}]</span>
                    @endforeach
                </div>
            </td>
            @if ($oBet->isEnd)
            <td><span class="c-yellow">{{{ $method->resultTitle }}}</span></td>
            @endif
        </tr>
        @endif
        @endforeach
        @endif
        @endforeach
        @endif
    </tbody>
</table>

<div class="bet-statics">
    过关方式: @foreach($aWays as $oWay)<b class="type c-red">{{{ $oWay->name }}}</b>@endforeach
    倍数<span class="num" id="J-order-multiple">{{{ $oBet->multiple }}}</span>倍
    注数<span class="num" id="J-order-betnum">{{{ $oBet->total }}}</span>注
    金额:<span class="num" id="J-order-amount">{{{ number_format($oBet->amount, 2) }}}</span>元
    @if (empty($oBet->id))
    &nbsp;&nbsp;&nbsp;&nbsp;
    可用余额: <span id="J-text-user-money">{{{ number_format($oAccount->available, 2) }}}</span> 元
    @endif
</div>



@section('end')
@parent
<script type="text/javascript">

(function($, host){
    $('.bet-confirm .table td').hover(function(){
        var el = $(this),par = el.parent(),id = par.attr('data-match');
        par.parent().find('[data-match="'+id+'"]').addClass('hover');
    },function(){
        var el = $(this),par = el.parent(),id = par.attr('data-match');
        par.parent().find('[data-match="'+id+'"]').removeClass('hover');
    });
})(jQuery, bomao);


</script>
@stop
