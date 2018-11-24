





<dl class="row row-header">
    <dd class="col-no">场次</dd>
    <!-- <dd class="col-weather">天气</dd> -->
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
    <dd class="col-bet">
        <div class="line line-title">进球数</div>
        <div class="line clearfix">
            <span class="cell">0</span>
            <span class="cell">1</span>
            <span class="cell">2</span>
            <span class="cell">3</span>
            <span class="cell">4</span>
            <span class="cell">5</span>
            <span class="cell">6</span>
            <span class="cell cell-last">7+</span>
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
        <!-- <dd class="col-weather">晴</dd> -->
        <dd class="col-cup">
            {{ $aLeagueList[$data->league_id]->short_name }}
        </dd>
        <dd class="col-time">
                <span class="time-type time-type-end" detail=" 截止时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->bet_time)) }}<br/>开赛时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->match_time)) }}">{{ date('H:i', strtotime($data->bet_time)) }}</span>
                <span style="display: none" class="time-type time-type-match" detail=" 截止时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->bet_time)) }}<br/>开赛时间:&nbsp;{{ date('Y-m-d H:i', strtotime($data->match_time)) }}">{{ date('H:i', strtotime($data->match_time)) }}</span>
        </dd>
        <dd class="col-team">
            @if($data->is_single)
                <span class="flag-issingle"></span>
                <span class="flag-issingle-text">单</span>
            @endif
            <span class="c-blue">{{ $aTeamList[$data->home_id]->short_name }}</span>&nbsp;
             <img class="ico-match-home-time" src="{{ $aTeamList[$data->home_id]->icon_url }}">&nbsp;
            <span class="ico-match-ball">@if(!empty($data->score)) <span class="ico-match-ball-his"> {{ $data->score }} </span> @else VS @endif</span>&nbsp;
             <img class="ico-match-visiting-time" src="{{ $aTeamList[$data->away_id]->icon_url }}">&nbsp;
            <span class="c-yellow-t">{{ $aTeamList[$data->away_id]->short_name }}</span>
        </dd>
        <dd class="col-bet">

	        @foreach ($data->method as $method)
	            @if($method->identifier != 'win' && $method->identifier != 'handicapWin' && ($oFilterMethodGroupKey == 'hunhe' || $oFilterMethodGroupKey == $method->identifier))
	                <div class="method-{{$method->identifier}} clearfix type-row">
	                    <div class="bets">
                                        @if($data->is_cancelled)
                                                <span>彩果：<span class="result-item">取消</span></span>
                                        @else
	                    @foreach ($method->codeList as $oOdds)
                                          
                                            @if(!$data->is_selling)
                                                <span class="item history-item">
                                                @if($oOdds->code==$method->getResult($data))
                                                    <span class="result-item">{{ $oOdds->name }}</span>
                                                @else
                                                    <span>--</span>
                                                @endif
                                                </span>
                                            @else
                                              <span data-type="{{$method->identifier}}" data-value="{{ $oOdds->code }}" data-param="action=addOrder&matchid={{$data->match_id}}&type={{$method->identifier}}&value={{ $oOdds->code }}&team1={{ $aTeamList[$data->home_id]->short_name}}&team2={{ $aTeamList[$data->away_id]->short_name }}&time={{ $data->day.$data->match_no }}&name={{ $oOdds->name }}&index={{$index++}}&odds={{ $oOdds->odds }}" class="item">
                                                    <b>{{ $oOdds->name }}</b>
                                                    <b class="odds">{{ $oOdds->odds }}</b>
                                              </span>
                                            @endif
	                  
	                    @if (in_array($oOdds->code, ['99','90','09']))
	                    <div class="brline"></div>
	                    @endif
	                    @endforeach
                                        @endif
	                    </div>
	                </div>
	            @endif
	        @endforeach

        </dd>
        <dd class="col-statics">
            <a rel="nofollow" target="_blank" href="http://info.sporttery.cn/football/info/fb_match_info.php?m={{ $data->original_id }}">析</a>
        </dd>
    </dl>
    <dl class="row-detail">
        <div class="inner">


        </div>
    </dl>
    </div>
    @endforeach
@endforeach
@else
    <div class="match-data-noresult">暂无可投注赛事</div>
@endif




