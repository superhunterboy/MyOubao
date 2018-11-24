

                <table class="table table-group table-records">
                    <thead>
                    <tr>
                        <th>序号</th>
                        <th>用户名</th>
                        <th>认购金额</th>
                        <th>比例</th>
                        <th>加入时间</th>
                        <th>奖金</th>
                        <th>购买类型</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php $index = $aProjects->getFrom(); ?>
                        @foreach($aProjects as $oProject)
                        <tr class="status-{{{ $oProject->status }}}">
                            <td>{{{ $index++ }}}</td>
                            <td>{{{ $oProject->display_nickname }}}</td>
                            <td>{{ number_format($oProject->amount, 2) }}</td>
                            <td>{{{ $oProject->buy_percent }}}</td>
                            <td>{{{ $oProject->created_at }}}</td>
                            <td><span class="c-yellow">{{{ $oProject->prize > 0 ? number_format($oProject->prize, 4) : '' }}}</span></td>
                            <td>{{{ $oProject->formatted_buy_type }}}</td>
                            <td>
                                @if ($oProject->checkDrop())
                                <a class="cancel-button J-groupbuy-cancel" href="{{{ route('jc.drop_detail', $oProject->id) }}}">撤单</a>
                                @elseif(in_array($oProject->status, [\JcModel\JcUserProject::STATUS_DROPED]))
                                {{{ $oProject->formatted_status }}}
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ pagination($aProjects->appends(Input::except('page')), 'w.pages') }}

                