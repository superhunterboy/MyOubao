

            @if (count($datas) > 0)
                <table class="table table-group">
                    <tr>
                        <th rowspan="2">赛事编号</th>
                        <th rowspan="2">赛事类型</th>
                        <th rowspan="2">比赛时间</th>
                        <th rowspan="2">主队</th>
                        <th rowspan="2">让球</th>
                        <th rowspan="2">客队</th>
                        <th rowspan="2">(半场)全场</th>
                        <th colspan="2">胜平负</th>
                        <th colspan="2">让球胜平负</th>
                        <th colspan="2">总进球</th>
                        <th colspan="2">半全场</th>
                    </tr>
                    <tr>
                        <th>彩果</th>
                        <th>奖金</th>
                        <th>彩果</th>
                        <th>奖金</th>
                        <th>彩果</th>
                        <th>奖金</th>
                        <th>彩果</th>
                        <th>奖金</th>
                    </tr>
                    @foreach($datas as $data)
                    <tr>
                        <td>{{{ $data->day }}}{{{ $data->match_no }}}</td>
                        <td>{{{ $data->league_name }}}</td>
                        <td>{{{ $data->match_time }}}</td>
                        <td><span class="c-blue">{{{ $data->home_name }}}</span></td>
                        <td>
                            @if ($data->handicap > 0)
                            <span class="f-red">+{{{ $data->handicap }}}</span>
                            @elseif($data->handicap < 0)
                            <span class="f-green">{{{ $data->handicap }}}</span>
                            @else
                            0
                            @endif
                        </td>
                        <td><span class="c-blue">{{{ $data->away_id }}}</span></td>
                        <td>
                            <span class="c-red">
                                @if ($data->score)
                                ({{{ $data->half_score }}}) {{{ $data->score }}}
                                @else
                                -
                                @endif
                            </span>
                        </td>
                        <td>{{{ $data->result['win']->name }}}</td>
                        <td><span class="c-red">{{{ $data->result['win']->odds }}}</span></td>
                        <td>{{{ $data->result['handicapWin']->name }}}</td>
                        <td><span class="c-red">{{{ $data->result['handicapWin']->odds }}}</span></td>
                        <td>{{{ $data->result['totalGoals']->name }}}</td>
                        <td><span class="c-red">{{{ $data->result['totalGoals']->odds }}}</span></td>
                        <td>{{{ $data->result['haFu']->name }}}</td>
                        <td><span class="c-red">{{{ $data->result['haFu']->odds }}}</span></td>
                    </tr>
                    @endforeach
                </table>

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}

            @else
            
            @endif
