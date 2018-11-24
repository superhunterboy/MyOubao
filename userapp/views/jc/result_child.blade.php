



            @if (count($datas) > 0)
                <table class="table table-group">
                    <tr>
                        <th>赛事编号</th>
                        <th>赛事类型</th>
                        <th>比赛时间</th>
                        <th>主队</th>
                        <th>让球</th>
                        <th>客队</th>
                        <th>(半场)全场</th>
                        <th>彩果</th>
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
                        <td>{{{ $data->result[$sMethodKey]->name }}}</td>
                    </tr>
                    @endforeach
                </table>

                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
            @else
            
            @endif