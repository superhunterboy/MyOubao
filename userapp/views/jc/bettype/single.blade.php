





<dl class="row row-header">
    <dd class="col-no">场次</dd>
    <dd class="col-weather">天气</dd>
    <dd class="col-cup">
        <span class="text">赛事</span>
        <i class="fa fa-sort-desc"></i>
        <span class="match-filter-panel">
            <div class="filter-all">
                <a href="#" class="ct-select-all">全选</a>
                <a href="#" class="ct-select-none">全清</a>
            </div>
            <ul>
                 @foreach($aLeagueList as $iLeagueId => $oLeague)
                    <li><a data-id="{{$iLeagueId}}" class="ct-select-league selected" href="#" data-text="{{ $oLeague->short_name }}"><i class="fa fa-check"></i> {{ $oLeague->short_name }}</a></li>
                @endforeach
            </ul>
        </span>

    </dd>
    <dd class="col-time">
            <span class="text">截止时间</span> 
            <i class="fa fa-sort-desc"></i>
            <span class="match-filter-panel">
                <ul>
                    <li><a class="ct-select-timetype active" href="#" data-type="end" data-text="截止时间"><i class="fa fa-check"></i> 截止时间</a></li>
                    <li><a class="ct-select-timetype" href="#" data-type="match" data-text="开赛时间"><i class="fa fa-check"></i> 开赛时间</a></li>
                </ul>
            </span>
    </dd>
    <dd class="col-team">主队 - 客队</dd>
    <dd class="col-handicap">
        <span class="text">全部</span>
        <i class="fa fa-sort-desc"></i>
        <span class="match-filter-panel">
            <ul>
                <li><a class="ct-select-handicap active" href="#" data-type="all" data-text="全部"><i class="fa fa-check"></i> 全部</a></li>
                <li><a class="ct-select-handicap" href="#" data-type="yes" data-text="让球"><i class="fa fa-check"></i> 让球</a></li>
                <li><a class="ct-select-handicap" href="#" data-type="no" data-text="不让球"><i class="fa fa-check"></i> 不让球</a></li>
            </ul>
        </span>
    </dd>
    <dd class="col-bet">
        <div class="line line-title">投注</div>
        <div class="line clearfix">
            <span class="cell">主胜</span>
            <span class="cell">平</span>
            <span class="cell cell-last">主负</span>
            <span class="cell cell-more">更多</span>
        </div>
    </dd>
    <dd class="col-statics">分析</dd>
</dl>





@if (count($datas) > 0)
@foreach ($datas as $date => $list)

    <dl class="row row-date clearfix">
        {{ $date }}　{{ getWeekDay($date) }}12:00 -- {{ getWeekDay(date('Y-m-d',strtotime($date) + 86400)) }}12:00　{{ count($list) }} 场比赛可投注
        <a class="ct" href="#" data-date="{{ $date }}">收起</a>
    </dl>
    @foreach ($list as $data)
    <?php
    $index = 0;
    ?>
    <div class="r row-list-{{$data->match_id}} row-list-league-{{$data->league_id}} row-list-date-{{$data->bet_date}} @if(!$data->is_selling) isStop @if(!isset($dBetDate)) current-date-isStop @endif @endif">
    <dl class="row row-list">
        <dd class="col-no">{{ $data->match_no }}</dd>
        <dd class="col-weather">
            @if ($data->weather_pic)
            <img src="/assets/images/sports/weather_icons/{{ $data->weather_pic }}" detail="{{ $data->weather }}&nbsp;&nbsp;{{ $data->temperature }}℃">
            @else
            未知
            @endif
        </dd>
        <dd class="col-cup">
            {{ $aLeagueList[$data->league_id]->short_name }}
        </dd>
        <dd class="col-time">
                <span class="time-type time-type-end" detail=" 截止时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->bet_time)) }}<br/>开赛时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->match_time)) }}">{{ date('H:i', strtotime($data->bet_time)) }}</span>
                <span style="display: none" class="time-type time-type-match" detail=" 截止时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->bet_time)) }}<br/>开赛时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->match_time)) }}">{{ date('H:i', strtotime($data->match_time)) }}</span>
        </dd>
        <dd class="col-team">
            @if($data->is_single && $sTabKey != 'single')
                <span class="flag-issingle"></span>
                <span class="flag-issingle-text">单</span>
            @endif
            <span class="c-blue">{{ $aTeamList[$data->home_id]->short_name }}</span>&nbsp;
            <img class="ico-match-home-time" src="{{ $aTeamList[$data->home_id]->icon_url }}">&nbsp;
            <span class="ico-match-ball">@if(!empty($data->score)) <span class="ico-match-ball-his"> {{ $data->score }} </span>  @else VS @endif</span>&nbsp;
            <img class="ico-match-visiting-time" src="{{ $aTeamList[$data->away_id]->icon_url }}">&nbsp;
            <span class="c-yellow-t">{{ $aTeamList[$data->away_id]->short_name }}</span>
        </dd>
        <dd class="col-handicap">
            <span class="item item-num">0</span>
            <span class="item item-handicap-num">
                @if($data->handicap > 0)
                    <span class="c-red">+{{ $data->handicap }}</span>
                @elseif($data->handicap < 0)
                    <span class="c-green">{{ $data->handicap }}</span>
                @else
                    {{ $data->handicap }}
                @endif
            </span>
        </dd>
        <dd class="col-bet">
            <span data-matchid="{{$data->match_id}}" class="item item-more" data-desc="其他投注" data-asc="收起">其他投注 <i class="fa fa-sort-desc"></i></span>
            @if ($oFilterMethodGroupKey == 'hunhe' || $oFilterMethodGroupKey == 'win-handicapWin')
            @foreach ($data->method as $method)
                @if($method->identifier == 'win' || $method->identifier == 'handicapWin')
                    <div class="method method-{{$method->identifier}} clearfix">
                        @if(count($method->codeList) > 0)
                        @if($data->is_cancelled)
                            <span class="item item-empty">取消</span>
                        @else
                            @foreach ($method->codeList as $k => $oOdds)
                                @if(!$data->is_selling)
                                    <span class="item history-item">
                                        <span @if($oOdds->code==$method->getResult($data)) style='color:red;' @endif>{{ $oOdds->name }}</span>
                                    </span>
                                @else
                                    <span data-type="{{$method->identifier}}" data-value="{{ $oOdds->code }}" data-param="action=addOrder&matchid={{$data->match_id}}&type={{$method->identifier}}&value={{ $oOdds->code }}&team1={{ $aTeamList[$data->home_id]->short_name }}&team2={{ $aTeamList[$data->away_id]->short_name }}&time={{ $data->day.$data->match_no }}&name={{ $oOdds->full_name }}&index={{$index++}}&odds={{ $oOdds->odds }}&handicap={{ $data->handicap }}" class="item">{{ $oOdds->odds }}
                                    <!-- <span class="item-trends trends-up">↑</span> -->
                                    <!-- <span class="item-trends trends-down">↓</span> -->
                                    </span>
                                @endif
                            @endforeach
                            @endif
                        @else
                            <span class="item item-empty">未开售</span>
                        @endif

                    </div>
                @endif
            @endforeach
            @endif
        </dd>
        <dd class="col-statics">
            <a rel="nofollow" target="_blank" href="http://info.sporttery.cn/football/info/fb_match_info.php?m={{ $data->original_id }}">析</a>
        </dd>
    </dl>
    <dl class="row-detail">
        <div class="inner">

        @foreach ($data->method as $method)
            @if($method->identifier != 'win' && $method->identifier != 'handicapWin' && ($oFilterMethodGroupKey == 'hunhe' || $oFilterMethodGroupKey == $method->identifier))
                @if($data->is_cancelled)
                <div class="method-{{$method->identifier}} clearfix type-row-cancel">
                @else
                <div class="method-{{$method->identifier}} clearfix type-row">
                @endif
                    <span class="title">
                        {{$method->name}}
                    </span>

                    <div class="bets">
                    @if($data->is_cancelled)
                            <span class="cancel">取消</span>
                    @else
                    @foreach ($method->codeList as $oOdds)
                    @if(!$data->is_selling)
                        <span class="item history-item">
                                <span @if($oOdds->code==$method->getResult($data)) class="result-item" @endif>{{ $oOdds->name }}</span>
                        </span>
                    @else
                        <span data-type="{{$method->identifier}}" data-value="{{ $oOdds->code }}" data-param="action=addOrder&matchid={{$data->match_id}}&type={{$method->identifier}}&value={{ $oOdds->code }}&team1={{ $aTeamList[$data->home_id]->short_name }}&team2={{ $aTeamList[$data->away_id]->short_name }}&time={{ $data->day.$data->match_no }}&name={{ $oOdds->name }}&index={{$index++}}&odds={{ $oOdds->odds }}" class="item">
                            <b>{{ $oOdds->name }}</b>
                            <b class="odds">{{ $oOdds->odds }}</b>
                        </span>
                    @endif
                    @if (in_array($oOdds->code, ['99', '90', '09']))
                    <div class="brline"></div>
                    @endif
                    @endforeach
                    @endif
                    </div>
                </div>
            @endif
        @endforeach

        </div>
    </dl>
    </div>
    @endforeach
@endforeach
@else
    <div class="match-data-noresult">暂无可投注赛事</div>
@endif







