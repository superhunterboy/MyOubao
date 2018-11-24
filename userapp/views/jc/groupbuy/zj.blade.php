
    <div class="inner">
        <div class="title">
            <div class="l-left">
                {{{ $oUser->display_nickname }}}
                &nbsp;
                @include('jc.groupbuy.star', ['oUserGrowth' => $oUserExtra])
            </div>
            <div class="l-middle">
                中奖总金额：<span class="c-yellow">{{{ number_format($oUserExtra->won_prize, 4) }}}</span> 元
                &nbsp;&nbsp;
                <i class="fa fa-info-circle J-groupbuy-info-tip" style="cursor: pointer;font-size:16px;"></i>
            </div>
            <div class="l-right">
                <p>发单成功率：{{{ $oUserExtra->success_percent }}}%</p>
                <p>中奖次数：{{{ $oUserExtra->won_count }}}次</p>
                <p>发单次数：{{{ $oUserExtra->bet_count }}}次</p>
            </div>
        </div>
        <div class="cont">
            
            <div class="list-tab">
                <ul class="list clearfix">
                    <li @if(!isset($sMethodGroupKey))class="active"@endif><a class="ct-update-data" href="{{{ route('jc.zj', [$sLotteryKey, $oUser->id]) }}}">全部方案</a></li>
                    @foreach($aMethodGroup as $oMethodGroup)
                    <li @if($sMethodGroupKey == $oMethodGroup->identifier)class="active"@endif><a class="ct-update-data" href="{{{ route('jc.zj', [$sLotteryKey, $oUser->id, $oMethodGroup->identifier]) }}}">{{{ $oMethodGroup->name }}}</a></li>
                    @endforeach
                </ul>
            </div>

            <table class="rank-table" width="100%">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>期号</th>
                        <th>玩法</th>
                        <th>类型</th>
                        <th>方案金额</th>
                        <th>中奖金额</th>
                        <th>盈利金额</th>
                        <th>回报率</th>
                        <th>方案状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $index = $datas->getFrom(); ?>
                    @foreach($datas as $data)
                    <tr class="tr-status-{{{ $data->userGroupBuy ? $data->userGroupBuy->status : $data->status }}}">
                        <td>{{{ $index++ }}}</td>
                        <td>{{ date('Ymd', strtotime($data->created_at)) }}</td>
                        <td>
                            @if (isset($aMethodGroup[$data->method_group_id]))
                            {{{ $aMethodGroup[$data->method_group_id]->name }}}
                            @endif
                        </td>
                        <td>{{{ $data->formatted_type }}}</td>
                        <td>{{ number_format($data->amount, 2) }}</td>
                        <td>{{ number_format($data->prize, 4) }}</td>
                        <td>{{ number_format($data->gains, 4) }}</td>
                        <td>{{{ $data->return_percent }}}%</td>
                        <td>
                            {{{ $data->userGroupBuy ? $data->userGroupBuy->formatted_status : $data->formatted_status }}}
                        </td>
                        <td class="cell-ct">
                            @if ($data->group_id > 0)
                            <a class="ct-link" href="{{{ route('jc.follow', $data->group_id) }}}" target="_blank">查看</a>
                            @elseif(Session::get('user_id') == $data->user_id)
                            <a class="ct-link" href="{{{ route('jc.bet_view', $data->id) }}}" target="_blank">查看</a>
                            @else
                            &nbsp;&nbsp;
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


            <div class="b">
                {{ pagination($datas->appends(Input::except('page')), 'w.pages') }}
            </div>


        </div>
    </div>

<script type="text/javascript">
    (function(){
        var TIP = bomao.Tip.getInstance();

        $('.l-middle').on('mouseover', '.J-groupbuy-info-tip', function(){
            TIP.setText('中奖总金额包括自购方案所中奖金和发起合买所中奖金，参与合买所中奖金不计算在内');
            TIP.dom.css('zIndex', 10000);
            TIP.show(25, TIP.dom.height()/2*-1, this);
        });
        $('.l-middle').on('mouseout', '.J-groupbuy-info-tip', function(){
            TIP.hide();
        });
    })();

</script>
